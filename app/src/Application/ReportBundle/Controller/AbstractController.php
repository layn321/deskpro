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

namespace Application\ReportBundle\Controller;

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
		$this->person->loadHelper('HelpMessages');

		if (!$this->person->getId()) {
			// Carry an agent session
			$cas = new \Application\AgentBundle\Controller\Helper\CarryAdminSession($this, 'dpsid-agent');
			$cas->process();

			if (!$this->person->getId()) {
				// Or an admin session
				$cas = new \Application\AgentBundle\Controller\Helper\CarryAdminSession($this);
				$cas->process();
			}
		}
	}


	/**
	 * Force a login
	 */
	public function preAction($action, $arguments = null)
	{
		if (!$this->person['id']) {
			if ($this->isPostRequest()) {
				$return = $this->get('router')->generate('report');
			} else {
				$return = $this->request->getRequestUri();
			}

			return $this->redirect($this->get('router')->generate('report_login', array('return' => $return)));
		}

		if (!$this->_userHasPermissions()) {
			return $this->renderStandardPermissionError('You do not have permission to use the reports interface.');
		}
	}

	protected function _userHasPermissions()
	{
		if ($this->person->is_agent && $this->person->can_reports) {
			return true;
		}

		return false;
	}

	protected function rememberLastPage($url = null)
	{
		if (!$url) {
			$url = $this->request->getRequestUri();
		}
		$this->session->set('report_last_page', $url);
	}

	public function renderStandardPermissionError($error_message = '', $error_title = '', $code = 200, array $vars = array())
	{
		$tpl = 'ReportBundle:Main:error-permission.html.twig';

		$vars = array_merge($vars, array(
			'error_message' => $error_message,
			'error_title'   => $error_title
		));

		$res = $this->render($tpl, $vars);

		$res->setStatusCode($code);

		return $res;
	}
}
