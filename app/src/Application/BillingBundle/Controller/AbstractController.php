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
 * @subpackage BillingBundle
 */

namespace Application\BillingBundle\Controller;

use Application\DeskPRO\App;

abstract class AbstractController extends \Application\DeskPRO\Controller\AbstractController
{
	/**
	 * The currently logged in person.
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public $person;

	protected function init()
	{
		parent::init();

		$this->person = $this->session->getPerson();

		if (!$this->person->id) {
			$cas = new \Application\AgentBundle\Controller\Helper\CarryAdminSession($this);
			$cas->process();
		}
	}


	/**
	 * Force a login
	 */
	public function preAction($action, $arguments = null)
	{
		if (!$this->person['id']) {
			if ($this->request->isXmlHttpRequest()) {
				$data = array(
					'error' => 'session_expired',
					'redirect_login' => $this->generateUrl('billing_login')
				);

				return $this->createJsonResponse($data, 403);

			} else {
				if ($this->isPostRequest()) {
					$return = $this->get('router')->generate('billing');
				} else {
					$return = $this->request->getRequestUri();
				}


				$redirect_url = $this->get('router')->generate('billing_login', array('return' => $return));
				return $this->redirect($redirect_url);
			}
		}

		if (!$this->_userHasPermissions()) {
			return $this->renderStandardPermissionError('You do not have permission to use the billing interface.');
		}

		$this->person->loadHelper('Agent');
		$this->person->loadHelper('AgentTeam');
		$this->person->loadHelper('AgentPermissions');
		$this->person->loadHelper('PermissionsManager');
		$this->person->loadHelper('HelpMessages');
	}

	protected function _userHasPermissions()
	{
		if ($this->person->is_agent && $this->person->can_billing) {
			return true;
		}

		return false;
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
		$billing_agents = array_filter($this->container->getAgentData()->getAgents(), function($a) {
			if ($a->can_admin || $a->can_billing) {
				return true;
			}
			return false;
		});

		$tpl = 'BillingBundle:Main:error-permission.html.twig';

		$vars = array_merge($vars, array(
			'error_message'  => $error_message,
			'error_title'    => $error_title,
			'billing_agents' => $billing_agents,
		));

		$res = $this->render($tpl, $vars);

		$res->setStatusCode($code);

		return $res;
	}
}
