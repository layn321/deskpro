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
*/

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\HttpFoundation\UserAgentRequirementCheck;

abstract class AbstractController extends \Application\DeskPRO\Controller\AbstractController
{
	/**
	 * The currently logged in person.
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public $person;

	/**
	 * @var \Application\AdminBundle\SetupGuide
	 */
	protected $setup_guide;

	protected function init()
	{
		parent::init();

		$this->person = $this->session->getPerson();
		$this->person->loadHelper('HelpMessages');

		$last_run = $this->container->getSetting('core.last_cron_start');
		if (!$last_run) $last_run = 0;

		$time_since_run = time() - $last_run;
		$is_problem = false;
		if ($time_since_run > 301 && !defined('DPC_IS_CLOUD')) {
			$is_problem = true;
		}

		$this->get('templating.globals')->setVariable('cron_last_run', $last_run);
		$this->get('templating.globals')->setVariable('cron_time_since_last_run', $time_since_run);
		$this->get('templating.globals')->setVariable('cron_is_problem', $is_problem);
		$this->get('templating.globals')->setVariable('helpdesk_is_offline', file_exists(dp_get_data_dir().'/helpdesk-offline.trigger') || $this->container->getSetting('core.helpdesk_disabled'));
	}

	/**
	 * Check if the global request token check is required for the request
	 */
	public function requireRequestToken($action, $arguments = null)
	{
		// Pre install we dont have a secret yet
		// So dont require the request token on POSTs
		// while we fill out setup form
		if (!App::getSetting('core.setup_initial')) {
			return false;
		}

		if ($this->request->getMethod() == 'POST') {
			return true;
		}

		return false;
	}

	/**
	 * Force a login
	 */
	public function preAction($action, $arguments = null)
	{
		if (!$this->person['id']) {
			if ($this->isPostRequest()) {
				$return = $this->get('router')->generate('admin');
			} else {
				$return = $this->request->getRequestUri();
			}

			if ($this->request->isXmlHttpRequest()) {
				$data = array(
					'error' => 'session_expired',
					'redirect_login' => $this->generateUrl('admin_login')
				);

				return $this->createJsonResponse($data, 403);
			}

			return $this->redirect($this->get('router')->generate('admin_login', array('return' => $return)));
		}

		if (!$this->_userHasPermissions()) {
			return $this->renderStandardPermissionError('You do not have permission to use the admin interface.');
		}

		$setup_guide = new \Application\AdminBundle\SetupGuide($this->container, $this);
		$this->setup_guide = $setup_guide;

		if ($setup_guide->hasDoneInitialSetup() && $this->requireRequestToken($action, $arguments) && !$this->checkRequestToken('request_token', '_rt')) {
			if ($this->request->isXmlHttpRequest()) {
				$data = array(
					'error' => 'invalid_request_token',
					'redirect_login' => $this->generateUrl('agent_login')
				);

				return $this->createJsonResponse($data, 403);
			} else {
				return $this->renderStandardPermissionError('The form you are trying to submit has expired. Please go back and try again.');
			}
		}

		if ($setup_guide->hasDoneInitialSetup() && !$this->request->isXmlHttpRequest() && !UserAgentRequirementCheck::passAgentInterface()) {
			return $this->redirect($this->request->getUriForPath('/agent/browser-requirements'));
		}

		$this->container->get('templating.globals')->setVariable('setup_guide', $setup_guide);
		return $setup_guide->preActionHelper($action, $arguments);
	}

	protected function _userHasPermissions()
	{
		if ($this->person->is_agent && $this->person->can_admin) {
			return true;
		}

		return false;
	}

	/**
	 * Render a standard error message.
	 *
	 * @param string $error_message
	 * @param string $error_title
	 * @return Response
	 */
	public function renderStandardError($error_message = '', $error_title = '', $code = 200, array $vars = array())
	{
		$tpl_standard = 'AdminBundle:Main:error-standard.html.twig';
		$tpl_specific = "AdminBundle:Main:error-{$code}.html.twig";

		$tpl = $tpl_standard;
		if (App::getTemplating()->exists($tpl_specific)) {
			$tpl = $tpl_specific;
		}

		$vars = array_merge($vars, array(
			'error_message' => $error_message,
			'error_title'   => $error_title
		));

		$res = $this->render($tpl, $vars);

		$res->setStatusCode($code);

		return $res;
	}

	/**
	 * Render a standard permission error message.
	 *
	 * @param string $error_message
	 * @param string $error_title
	 * @return Response
	 */
	public function renderStandardPermissionError($error_message = '', $error_title = '', $code = 200, array $vars = array())
	{
		$tpl = 'AdminBundle:Main:error-permission.html.twig';

		$vars = array_merge($vars, array(
			'error_message' => $error_message,
			'error_title'   => $error_title
		));

		$res = $this->render($tpl, $vars);

		$res->setStatusCode($code);

		return $res;
	}

	/**
	 * Standard error displayed when we're given an invalid security token.
	 *
	 * @return Response
	 */
	public function renderStandardTokenError()
	{
		return $this->renderStandardError('The page you are trying to access has expired. Go back, refresh, and try again.');
	}


	/**
	 * After changes that affect the agent UI, send a signal to
	 * any online agents to relaod.
	 */
	public function sendAgentReloadSignal()
	{
		$cm = new \Application\DeskPRO\Entity\ClientMessage();
		$cm->fromArray(array(
			'channel' => 'agent.ui.reload',
			'data' => array(
				'type'        => 'admin',
				'person_id'   => $this->person->getId(),
				'person_name' => $this->person->getDisplayName()
			)
		));

		$this->em->persist($cm);
		$this->em->flush();
	}

	/**
	 * Checks a request token in a form
	 *
	 * @param string $name
	 * @param string $field_name
	 * @return bool
	 */
	public function checkRequestToken($name = '', $field_name = '_dp_security_token')
	{
		if (!$this->setup_guide->hasDoneInitialSetup()) {
			return true;
		}

		return parent::checkRequestToken($name, $field_name);
	}
}
