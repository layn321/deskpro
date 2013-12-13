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

namespace Application\DeskPRO\Tickets\TicketChangeInspector;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketFilter;
use Application\DeskPRO\Entity\ClientMessage;

use Application\DeskPRO\Tickets\TicketChangeTracker;

use Orb\Log\Logger;

/**
 * This passes a ticket change through filters to determine which are affected by the change,
 * and for whom it affects.
 *
 * This is done by:
 * 1) Recreating the original ticket
 * 2) Fetching all filters in the system and all agents
 * 3) For each agent (the "scope") we execute the PHP-based filter check to see
 * if it matched the original, and then again to see if it matches the new.
 * Using this information we can determine if a ticket has entered or left a certain list,
 * and for who.
 *
 * Note that we have to run through all agents all the time. These filter detections are
 * used to both send real-time ClientMessage updates to sync UI's and send browser alerts,
 * but also for email notifications where agents of course wont be online.
 *
 * Ways we can optimize this process in the future:
 * - Pre-cache agents/permissions to prevent loading full collection of agents
 * - Pre-cache filter criteria to prevent loading full collection of filters
 */
class DetectFilterMatches
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var array
	 */
	protected $filter_changes = null;

	public function __construct(TicketChangeTracker $tracker)
	{
		$this->tracker = $tracker;
	}

	protected function logMessage($message)
	{
		$this->tracker->logMessage('[DetectFilterMatches] ' . $message);
	}

	/**
	 * Read the changelog to fetch an array of actual changed fields that we
	 * can compare against that of the affected fields of a filter searcher.
	 *
	 * @return array
	 */
	public function getChangedFields()
	{
		$changed_fields = array();
		foreach ($this->tracker->getAllChangedProperties() as $prop => $info) {
			switch ($prop) {
				case 'agent':
					$changed_fields[] = 'ticket.agent_id';
					break;

				case 'agent_team':
					$changed_fields[] = 'ticket.agent_team_id';
					break;

				case 'category':
					$changed_fields[] = 'ticket.category_id';
					break;

				case 'department':
					$changed_fields[] = 'ticket.department_id';
					break;

				case 'priority':
					$changed_fields[] = 'ticket.priority_id';
					break;

				case 'product':
					$changed_fields[] = 'ticket.product_id';
					break;

				case 'workflow':
					$changed_fields[] = 'ticket.workflow_id';
					break;

				case 'status':
					$changed_fields[] = 'ticket.status';
					break;

				case 'is_hold':
					$changed_fields[] = 'ticket.is_hold';
					break;

				case 'hidden_status':
					$changed_fields[] = 'ticket.hidden_status';
					break;

				case 'participants':
					$changed_fields[] = 'ticket.participants';
					break;

				case 'labels':
				case 'label_added':
				case 'label_removed':
					$changed_fields[] = 'ticket.labels';
					break;
			}
		}

		$this->logMessage("Raw Changed: " . implode(', ', array_keys($this->tracker->getAllChangedProperties())));

		$this->logMessage("Changed fields: " . implode(', ', $changed_fields));

		return $changed_fields;
	}


	/**
	 * Filters only matter if they contain a term that has changed.
	 * So investigate the ticket changes, and then get an array of filters
	 * that do indeed need to be checked.
	 *
	 * @return array
	 */
	public function getApplicableFilters()
	{
		$filters = App::getEntityRepository('DeskPRO:TicketFilter')->getAll();
		$filters_apply = array();

		$this->logMessage("Filters to check: " . count($filters));

		$changed_fields = $this->getChangedFields();
		$new_messages = $this->tracker->getChangedProperty('messages') ? true : false;

		$hidden_changed = false;
		if (in_array('ticket.hidden_status', $changed_fields)) {
			$hidden_changed = true;
		}

		foreach ($filters as $filter) {
			if ($new_messages || $hidden_changed || $filter->getSearcher()->hasAnyAffectedFields($changed_fields)) {
				$filters_apply[] = $filter;
			}
		}

		$this->logMessage("Passed filters: " . count($filters_apply));

		return $filters_apply;
	}


	/**
	 * Fetch an array of filters that were affected by a change.
	 *
	 * array(filterid=>array(add=>array(1,2), del=>array(3,4)))
	 *
	 * @return array
	 */
	public function getFilterMatches()
	{
		if ($this->filter_changes !== null) return $this->filter_changes;

		$filters     = $this->getApplicableFilters();
		$all_agents  = App::getEntityRepository('DeskPRO:Person')->getAgents();
		$team2agents = App::getEntityRepository('DeskPRO:AgentTeam')->getTeamToAgentsMap();

		$old_dep_id = null;
		$new_dep_id = null;
		if ($dep_change = $this->tracker->getChangedProperty('department')) {
			$old_dep_id = $dep_change['old'];
			if ($old_dep_id) {
				$old_dep_id = $old_dep_id->getId();
			}
			$new_dep_id = $dep_change['new'];
			if ($new_dep_id) {
				$new_dep_id = $new_dep_id->getId();
			}
		}

		$orig_ticket = $this->tracker->getOriginalTicket();
		$new_ticket  = $this->tracker->getTicket();

		$changed = array();

		$scope_counts = 0;
		$time = microtime(true);

		foreach ($filters as $filter) {

			// If the filter is the recycle bin and we are nuking the ticket,
			// then dont show it entering the list
			if ($filter->sys_name == 'archive_deleted' && $this->tracker->isExtraSet('is_physical_delete')) {
				continue;
			}

			$changed[$filter->id] = array(
				'add' => array(),
				'del' => array(),
				'orig_match' => array(),
				'new_match' => array(),
				'filter' => $filter
			);

			$this->logMessage("Filter {$filter['id']} {$filter['title']}");

			$agent_scopes = null;

			if ($filter->is_global) {
				$agent_scopes = $all_agents;
			} else if ($filter->agent_team) {
				if (isset($team2agents[$filter->agent_team->id])) {
					$agent_scopes = array();
					foreach ($team2agents[$filter->agent_team->id] as $id) {
						if (isset($all_agents[$id])) $agent_scopes[] = $all_agents[$id];
					}
				}
			} elseif ($filter->person) {
				$agent_scopes = array($filter->person);
			}

			$affected_agent_ids = array();
			if ($agent_scopes) {
				foreach ($agent_scopes as $a) {
					$affected_agent_ids[] = $a->getId();
				}
			}
			$this->logMessage("-- Affected agents: " . implode(', ', $affected_agent_ids));

			if (!$agent_scopes) {
				continue;
			}

			foreach ($agent_scopes as $agent) {

				$agent->loadHelper('Agent');
				$agent->loadHelper('AgentTeam');
				$agent->loadHelper('AgentPermissions');
				$agent->loadHelper('PermissionsManager');

				$reset_status = false;
				if ($filter->sys_name) {
					// System filters are special in that we ignore status/hold
					// for notifications
					$searcher = $filter->getSearcher(array(
						array('type' => 'status', 'op' => 'ignore'),
						array('type' => 'hidden_status', 'op' => 'ignore'),
						array('type' => 'is_hold', 'op' => 'ignore')
					));

					// Reset because we have to re-run to get proper result for add/del lists
					$reset_status = true;
				} else {
					$searcher = $filter->getSearcher();
				}
				$searcher->setPersonContext($agent);

				$orig_match_failterm = null;
				$new_match_failterm = null;

				if ($dep_change) {
					if (!$this->tracker->isNewTicket() && !$agent->AgentPermissions->isDepartmentAllowed($old_dep_id)) {
						$orig_match = false;
						$orig_match_failterm = 'ticket.department_id';
					}

					if (!$agent->AgentPermissions->isDepartmentAllowed($new_dep_id)) {
						$new_match = false;
						$new_match_failterm = 'ticket.department_id';
					}
				}

				if ($orig_match_failterm === null) {
					if ($this->tracker->isNewTicket()) {
						// there is no such thing as an original match with a new ticket
						$orig_match = false;
					} else {
						$orig_match = $searcher->doesTicketMatch($orig_ticket, 'orig_match', $orig_match_failterm);
					}
				}

				if ($new_match_failterm === null) {
					$new_match  = $searcher->doesTicketMatch($new_ticket, null, $new_match_failterm);
				}

				if ($orig_match && $agent->PermissionsManager->TicketChecker->canView($orig_ticket)) {
					$changed[$filter->id]['orig_match'][] = $agent;
				}
				if ($new_match && $agent->PermissionsManager->TicketChecker->canView($new_ticket)) {
					$changed[$filter->id]['new_match'][] = $agent;
				}

				if ($reset_status) {
					$searcher = $filter->getSearcher();
					$searcher->setPersonContext($agent);

					if ($this->tracker->isNewTicket()) {
						$orig_match = false;
					} else {
						$orig_match = $searcher->doesTicketMatch($orig_ticket, 'orig_match', $orig_match_failterm);
					}
					$new_match  = $searcher->doesTicketMatch($new_ticket, null, $new_match_failterm);
				}

				if (!$orig_match) {
					$this->logMessage("-- Orig failed term: $orig_match_failterm");
				}
				if (!$new_match) {
					$this->logMessage("-- New failed term: $new_match_failterm");
				}

				if (!$orig_match AND !$new_match) {
					$this->logMessage("-- Agent Scope {$agent->id}: Nothing changed (both no-match)");
				} else if ($orig_match AND $new_match) {
					$this->logMessage("-- Agent Scope {$agent->id}:  Nothing changed (both match)");
				} else if ($orig_match AND !$new_match) {
					$this->logMessage("-- Agent Scope {$agent->id}: Removed from list");
					$changed[$filter->id]['del'][] = $agent;
				} else if (!$orig_match AND $new_match) {
					$this->logMessage("-- Agent Scope {$agent->id}: Added to list");
					$changed[$filter->id]['add'][] = $agent;
				}

				$scope_counts++;
			}
		}

		$this->filter_changes = array();
		foreach ($changed as $fid => $changes) {
			if ($changes['orig_match'] || $changes['new_match']) {
				$this->filter_changes[$fid] = $changes;
			}
		}

		$total_time = microtime(true) - $time;

		$this->logMessage('Found ' . count($this->filter_changes) . ' matches');
		$this->logMessage("Full check done in iterations: " . $scope_counts . "  in time " . sprintf('%.5f', $total_time) . " seconds");
		$this->logMessage(\Orb\Util\Util::debugVar($this->filter_changes));

		return $this->filter_changes;
	}
}
