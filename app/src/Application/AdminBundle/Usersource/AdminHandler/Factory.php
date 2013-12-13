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

namespace Application\AdminBundle\Usersource\AdminHandler;

use Application\DeskPRO\Entity\Usersource;

/**
 * Creates an admin handler based off of a particular usersource.
 *
 * @static
 */
class Factory
{
	private function __construct() { /* static */ }

	/**
	 * @return Application\AgentBundle\CustomField\AdminHandler\AbstractAdminHandler
	 */
	public static function createUsersource(Usersource $usersource)
	{
		$handler = null;
		switch ($usersource['handler_class']) {
			case 'Application\\DeskPRO\\Usersource\\Handler\\Twitter':
				$handler = new \Application\AgentBundle\Usersource\AdminHandler\Twitter($usersource);
				break;

			case 'Application\\DeskPRO\\Usersource\\Handler\\OpenId':
				$handler = new \Application\AgentBundle\Usersource\AdminHandler\OpenId($usersource);
				break;
		}

		if (!$handler) {
			throw new \InvalidArgumentException("Unknown AdminHandler for {$usersource['handler_class']}");
		}

		return $handler;
	}
}
