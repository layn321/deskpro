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

use Application\DeskPRO\App;
use Orb\Util\Arrays;

class AddAgentNotifyModifier implements CollectionModifierInterface
{
	protected $codes;

	public function __construct(array $codes = array())
	{
		$this->codes = $codes;
	}

	public function modifyCollection(ActionsCollection $collection)
	{
		$notify_types = array();
		$notify_types[] = 'AgentNotification';
		$notify_types[] = 'AgentAlertNotification';

		foreach ($notify_types as $type) {
			if ($collection->hasActionType($type)) {
				$collection->getActionType($type)->addAdditionalAgents($this->codes);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		$desc_agents = array();
		$desc_teams = array();

		$agent_ids = array();
		$agent_team_ids = array();

        $tr = App::getTranslator();

		foreach ($this->codes as $send_to) {
			if ($send_to == 'assigned_agent') {
				$desc_agents[] = $tr->phrase('agent.general.assigned_agent');

			} elseif ($send_to == 'assigned_agent_team') {
				$desc_teams[] = $tr->phrase('agent.general.assigned_team');

			} elseif ($send_to == 'all_agents') {
				$desc_agents = array('All agents');
				$desc_teams = array();
				break;

			} elseif (strpos($send_to, 'agent.') === 0) {
				list (, $agent_id) = explode('.', $send_to, 2);
				$agent_ids[] = $agent_id;

			} elseif (strpos($send_to, 'agent_team.') === 0) {
				list (, $agent_team_id) = explode('.', $send_to, 2);
				$agent_team_ids[] = $agent_team_id;
			}
		}

		if ($agent_ids) {
			$titles = App::getEntityRepository('DeskPRO:Person')->getAgentNames($agent_ids);
			$titles = Arrays::func($titles, 'htmlspecialchars');
			foreach ($agent_ids as $id) {
				if (!isset($titles[$id])) {
					$titles[$id] = "<error>Unknown #$id</error>";
				}
			}
			$desc_agents = array_merge($desc_agents, $titles);
		}
		if ($agent_team_ids) {
			$titles = App::getEntityRepository('DeskPRO:AgentTeam')->getTeamNames($agent_team_ids);
			$titles = Arrays::func($titles, 'htmlspecialchars');
			foreach ($agent_ids as $id) {
				if (!isset($titles[$id])) {
					$titles[$id] = "<error>Unknown #$id</error>";
				}
			}
			$desc_teams  = array_merge($desc_agents, $titles);
		}

		$parts = array();
		if ($desc_agents) {
			$parts[] = $tr->phrase('agent.tickets.agents_action', array('agents' => implode(', ', $desc_agents)));
		}
		if ($desc_teams) {
			$parts[] = $tr->phrase('agent.tickets.teams_action', array('teams' => implode(', ', $desc_teams)));
		}

		if (!$parts) {
			return '';
		}

		$parts = implode($tr->phrase('agent.general.and'), $parts);

		return $tr->phrase('agent.tickets.always_notify_people', array('parts' => $parts));
	}
}
