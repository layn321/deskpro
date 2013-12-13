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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller\Helper;

use Application\DeskPRO\App;

class CarryAdminSession
{
	protected $controller;
	protected $cookie_name;

	public function __construct($controller, $cookie_name = 'dpsid-admin')
	{
		$this->controller = $controller;
		$this->cookie_name = $cookie_name;
	}

	public function process()
	{
		if (!$this->controller->person->id) {
			$admin_session_code = !empty($_COOKIE[$this->cookie_name]) ? $_COOKIE[$this->cookie_name] : false;
			$admin_session = null;
			if ($admin_session_code) {
				$admin_session = App::getEntityRepository('DeskPRO:Session')->getSessionFromCode($admin_session_code);
				if (!$admin_session || !$admin_session->person || !$admin_session->person->is_agent) {
					$admin_session = null;
				}

				if ($admin_session) {
					$this->controller->session->set('auth_person_id', $admin_session->person->id);
					\Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie('dp-guest-cache')->send();
					$this->controller->session->set('dp_interface', DP_INTERFACE);

					// Set their status to available by default
					$this->controller->session->set('active_status', 'available');
					$this->controller->session->set('is_chat_available', 1);

					$this->controller->session->save();

					$this->controller->person = $admin_session->person;
					App::setCurrentPerson($admin_session->person);
				}
			}
		}
	}
}
