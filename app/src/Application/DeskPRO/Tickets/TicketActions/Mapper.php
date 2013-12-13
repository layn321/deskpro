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
 * @subpackage Tickets
 */

namespace Application\DeskPRO\Tickets\TicketActions;

use Orb\Util\Strings;

/**
 * Maps a standard action name to an action class
 */
class Mapper
{
	protected $map = array();

	public function addMapName($name, $class)
	{
		$this->map[$name] = $class;
	}

	public function mapName($name)
	{
		// We have a specific name
		if (isset(self::$map[$name])) {
			$class = self::$map[$name];

		// We'll try to generate it
		} else {

			// Example:
			// agent_team
			// agent-team
			// AgentTeam
			// ActionTeamAction

			$class = str_replace('_', '-', $name);
			$class = ucfirst(Strings::dashToCamelCase($class));

			$action_class = $class . 'Action';
			$modifier_class = $class . 'Modifier';
			if (is_class($action_class)) {
				return $action_class;
			} elseif (is_class($modifier_class)) {
				return $modifier_class;
			}
		}

		return null;
	}
}
