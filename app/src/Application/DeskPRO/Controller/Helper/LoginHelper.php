<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * DeskPRO
 *
 * @package DeskPRO
 * @category Controller
 */

namespace Application\DeskPRO\Controller\Helper;

use Application\DeskPRO\App;
use Application\DeskPRO\Controller\AbstractController;
use Application\DeskPRO\Auth\LoginProcessor;

class LoginHelper
{
	/**
	 * @var \Application\DeskPRO\Controller\AbstractController
	 */
	protected $controller;

	/**
	 * @var string
	 */
	protected $tpl_prefix;

	/**
	 * @var string
	 */
	protected $route_prefix;

	public function __construct(AbstractController $controller, $tpl_prefix, $route_prefix)
	{
		$this->controller   = $controller;
		$this->tpl_prefix   = $tpl_prefix;
		$this->route_prefix = $route_prefix;
	}

	############################################################################
	# /login
	############################################################################

	public function execIndexAction(array $vars = array())
	{
		$usersources = $this->em->createQuery('
			SELECT us
			FROM DeskPRO:Usersource us
			INDEX BY us.id
			WHERE us.is_enabled = ?1
		')->setParameter(1, true)->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

		$return = $this->controller->in->getStringFromGet('return');
		if ($return AND $return[0] != '/') {
			// Always be a path on the current domain,
			// or else it might be a trick to go to some other domain etc
			$return = '';
		}

		$vars = $vars + array(
			'usersources'      => $usersources,
			'usersource_forms' => $this->_getUsersourceLoginForms($usersources),
			'return'           => $return
		);

		return $this->controller->render($this->tpl_prefix . ':index.html.twig', $vars);
	}

	protected function _getUsersourceLoginForms($usersources)
	{
		$forms = array();

		foreach ($usersources as $usersource) {
			$parts = explode('\\', $usersource['handler_class']);
			$tpl_name = $this->tpl_prefix . 'login-form-.html.twig' . strtolower(array_pop($parts));

			$forms[] = array(
				'usersource' => $usersource,
				'html' => $this->tpl->render($tpl_name, array('usersource' => $usersource))
			);
		}

		return $forms;
	}



	############################################################################
	# /logout
	############################################################################

	public function execLogoutAction()
	{
		// When an agent actually logs out, we should be clearing the state
		$person = $this->controller->session->getPerson();
		if ($person['is_agent']) {
			App::getDb()->executeUpdate("
				DELETE FROM people_prefs
				WHERE person_id = ? AND name = ?
			", array($person['id'], 'agent.ui.state'));
		}

		$this->controller->session->replace(array());
		$this->controller->session->save();

		return $this->controller->redirectRoute($this->route_prefix . '_login');
	}


	############################################################################
	# /login/authenticate
	############################################################################

	public function execAuthenticateAction($usersource_id)
	{
		if ($usersource_id) {
			return $this->_processUsersourceLogin($usersource_id);
		} else {
			return $this->_processLocalLogin();
		}
	}

	protected function _processLocalLogin()
	{
		$adapter = new \Application\DeskPRO\Auth\Adapter\Local(App::getOrm());
		$adapter->setCredentials($this->controller->in->getString('email'), $this->controller->in->getString('password'));
		$result = $adapter->authenticate();

		if (!$result->isValid()) {
			return $this->_redirectLoginFailed();
		}

		$identity = $result->getIdentity();

		$person = $identity['person'];
		$person->setLastLoginAt();
		$this->em->persist($person);
		$this->em->flush();

		$this->controller->session->set('auth_person_id', $identity->getIdentity());

		if ($person['is_agent']) {

			// Set their status to available by default
			$this->controller->session->set('dp_active_status', 'available');

			$data = array(
				'agent_id'   => $person['id'],
				'agent_name' => $person['display_name'],
				'agent_short_name' => $person->getDisplayContactShort(4),
				'picture_url' => $person->getPictureUrl(10)
			);

			// Announce if its an agent
			$cm = new \Application\DeskPRO\Entity\ClientMessage();
			$cm->fromArray(array(
				'channel' => 'agent.new-agent-online',
				'data' => $data,
				'created_by_client' => $this->controller->session->getEntityId(),
			));

			$this->em->persist($cm);
			$this->em->flush();
		}

		\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();

		return $this->_redirectLoginSuccess();
	}

	protected function _processUsersourceLogin($usersource_id)
	{
		$return = $this->controller->in->getString('return');

		$usersource = $this->em->find('DeskPRO:Usersource', $usersource_id);

		$adapter = $this->_initUserSourceAdapter($usersource);

		#------------------------------
		# Callback types require us to redirect
		#------------------------------

		if ($adapter instanceof \Orb\Auth\Adapter\CallbackInterface) {
			$result = $adapter->authenticate();

			// We expect a redirect to be rquired
			if ($result->isRedirectRequired()) {
				return $this->controller->redirect($result->getRedirectUrl());

			// Otherwise its an error
			} else {
				return $this->_redirectLoginFailed();
			}

		#------------------------------
		# Other types should return a result right away
		#------------------------------

		} else {
			$result = $adapter->authenticate();

			// Valid
			if ($result->isValid()) {

				$login_processor = new LoginProcessor($usersource, $result->getIdentity());
				$person = $login_processor->getPerson();

				$this->controller->session->set('auth_person_id', $person['id']);
				\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();
				return $this->_redirectLoginSuccess();

			// Error, go back to login
			} else {
				return $this->_redirectLoginFailed();
			}
		}
	}

	protected function _redirectLoginSuccess()
	{
		$return = $this->controller->in->getString('return');
		if ($return) {
			return $this->controller->redirect($return);
		} else {
			return $this->controller->redirectRoute($this->route_prefix);
		}
	}

	protected function _redirectLoginFailed()
	{
		$return = $this->controller->in->getString('return');
		return $this->controller->redirectRoute($this->route_prefix . '_login', array('return' => $return));
	}

	############################################################################
	# /login/authenticate-callback/:usersource_id
	############################################################################

	public function execAuthenticateCallbackAction($usersource_id)
	{
		$usersource = $this->em->find('DeskPRO:Usersource', $usersource_id);

		$adapter = $this->_initUserSourceAdapter($usersource);

		// It must be a callback type to be here, so if not redirect back to login
		if (!($adapter instanceof \Orb\Auth\Adapter\CallbackInterface)) {
			return $this->controller->redirect($this->controller->get('router')->generate($this->route_prefix + '_login', array()));
		}

		$adapter->setCallbackContext($_REQUEST);

		$result = $adapter->authenticate();

		// Valid
		if ($result->isValid()) {

			$login_processor = new LoginProcessor($usersource, $result->getIdentity());
			$person = $login_processor->getPerson();

			$this->controller->session->set('auth_person_id', $person['id']);
			\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();
			return $this->controller->redirect($this->controller->get('router')->generate($this->route_prefix, array()));

		// Error, go back to login
		} else {
			return $this->controller->redirect($this->controller->get('router')->generate($this->route_prefix . '_login', array()));
		}
	}


	############################################################################

	protected function _initUserSourceAdapter($usersource)
	{
		$adapter = $usersource->getHandler()->getAuthAdapter();

		if ($adapter instanceof \Orb\Auth\Adapter\FormLoginInterface) {
			$adapter->setFormData($_POST);
		}

		if ($adapter instanceof \Orb\Auth\Adapter\CallbackInterface) {
			$adapter->setCallbackUrl($this->generateUrl($this->route_prefix . '_login_callback', array('usersource_id' => $usersource['id']), true));
		}

		if ($adapter instanceof \Orb\Auth\Adapter\SessionStateInterface) {
			$auth_state = new \Orb\Auth\StateHandler\ArrayAccessWrapper($this->session);
			$auth_state->setClearStateMethod('clear');

			$adapter->setStateHandler($auth_state);
		}

		return $adapter;
	}
}
