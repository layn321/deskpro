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

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Tickets\TicketChangeInspector\DetectFilterMatches;

use Orb\Log\Logger;

/**
 * Builds a list of people who should be notified,
 * and how they are to be notified based on preferences.
 *
 * Actual notifications are sent via triggers where the list can
 * be mutated etc.
 */
class NotifyListBuilder
{
	/**
	 * @var TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeInspector\DetectFilterMatches
	 */
	protected $filter_detector;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var array
	 */
	protected $notify_list = null;

	public function __construct(TicketChangeTracker $tracker, DetectFilterMatches $filter_detector)
	{
		$this->tracker = $tracker;
		$this->filter_detector = $filter_detector;
		$this->em = App::getOrm();
	}

	/**
	 * This gets a raw notification list based off of subscriptions.
	 *
	 * The resulting array will look like this:
	 *
	 * <code>
	 * array(
	 *     agent_id => array(
	 *			filter_id => array(
	 *              agent => Agent,
	 *              filter => TicketFilter,
	 *              is_new => true/false,
	 *              is_update => true/false,
	 *              types => array(email, alert)
	 *          )
	 *     )
	 * );
	 * </code>
	 *
	 * Note that this means that each ticket might have multiple alerts for an agent
	 * for different fitlers. If you implement this, be sure to eg dont sent multiple
	 * emails.
	 *
	 * @return array
	 */
	public function getNotifyList($notify_type = null)
	{
		if ($this->notify_list !== null) {
			return $this->notify_list;
		}

		$filter_changes = $this->filter_detector->getFilterMatches();
		$ticket = $this->tracker->getTicket();

		$status_change  = $this->tracker->getChangedProperty('status');
		$hstatus_change = $this->tracker->getChangedProperty('hidden_status');

		$assign_change         = $this->tracker->getChangedProperty('agent');
		$assign_team_change    = $this->tracker->getChangedProperty('agent_team');
		$assign_follow_change  = $this->tracker->getChangedProperty('participants');

		$notify_new         = false;
		$notify_agent_reply = false;
		$notify_agent_note  = false;
		$notify_user_reply  = false;

		if ($this->tracker->isExtraSet('ticket_created') || ($ticket->status_code == 'awaiting_agent' && $status_change['old'] == 'hidden.validating')) {
			$this->tracker->logMessage("[NotifyListBuilder] notify_new");
			$notify_new = true;
		}
		$messages = $this->tracker->getChangedProperty('messages');
		if ($messages) {
			$message = array_shift($messages);
			$message = $message['new'];
			if ($message->is_agent_note) {
				$this->tracker->logMessage("[NotifyListBuilder] notify_agent_note");
				$notify_agent_note = true;
			} elseif ($message->person->is_agent) {
				$this->tracker->logMessage("[NotifyListBuilder] notify_agent_reply");
				$notify_agent_reply = true;
			} else {
				$this->tracker->logMessage("[NotifyListBuilder] notify_user_reply");
				$notify_user_reply = true;
			}
		}

		// Fetch the filter subscriptions for the agents we've found and the filters affected
		$agent_ids = array();
		$filter_ids = array();
		foreach ($filter_changes as $change_info) {
			$filter_ids[] = $change_info['filter']->id;

			foreach ($change_info['orig_match'] as $agent) {
				$agent_ids[] = $agent->id;
			}
			foreach ($change_info['new_match'] as $agent) {
				$agent_ids[] = $agent->id;
			}
		}

		// We dont want to notify ourselves (that is, the one that performed whatever action prompted the changeset)
		$person_context = App::getCurrentPerson();
		if ($person_context && $person_context->id && !$this->tracker->isExtraSet('is_user_reply') && !$person_context->getPref("agent_notify_override.all.$notify_type")) {
			$agent_ids = array_filter($agent_ids, function($aid) use ($person_context) {
				if ($aid == $person_context->id) {
					return false;
				}
				return true;
			});
		}

		$this->tracker->logMessage("[NotifyListBuilder] " . count($agent_ids) . " agents and " . count($filter_ids) . " filters");

		$agent_subs = $this->em->getRepository('DeskPRO:TicketFilterSubscription')->getForAgents($agent_ids, $filter_ids);
		$this->tracker->logMessage(\Orb\Util\Util::debugVar($agent_subs));

		// Build a list of who should be notified and how
		$this->notify_list = array();

		foreach ($filter_changes as $change_info) {
			$filter = $change_info['filter'];

			$new_match_agents = array();

			// Notify about tickets entering a list
			// AKA a ticket changed such that it was added into a new list it wasnt before
			foreach ($change_info['new_match'] as $agent) {

				$new_match_agents[$agent->getId()] = $agent->getId();

				if (!isset($agent_subs[$agent->id][$filter->id])) continue;
				$sub = $agent_subs[$agent->id][$filter->id];

				$types = array();

				// New ticket entering a list
				// - If its new, then we check subs for everyone
				// - Other notify types, we have to ignore 'all' for entering a list
				if ($notify_new || $filter->sys_name != 'all') {
					if ($notify_new) {
						if ($sub->email_created) {
							$types[] = 'email';
						}
						if ($sub->alert_created) {
							$types[] = 'alert';
						}
					} else {
						if (
							$sub->email_property_change
							|| (!$filter->sys_name && $sub->email_new)
							|| (($filter->sys_name == 'agent' || $filter->sys_name == 'unassigned') && $assign_change && $sub->email_new)
							|| ($filter->sys_name == 'agent_team' && $assign_team_change && $sub->email_new)
							|| ($filter->sys_name == 'participant' && $assign_follow_change && $sub->email_new)
						) {
							$types[] = 'email';
						}
						if (
							$sub->alert_property_change
							|| (!$filter->sys_name && $sub->alert_new)
							|| (($filter->sys_name == 'agent' || $filter->sys_name == 'unassigned') && $assign_change && $sub->alert_new)
							|| ($filter->sys_name == 'agent_team' && $assign_team_change && $sub->alert_new)
							|| ($filter->sys_name == 'participant' && $assign_follow_change && $sub->alert_new)
						) {
							$types[] = 'alert';
						}
					}
				}

				if ($types) {
					$this->addToNotifyList($agent, $filter, 'new', $types);
				}
			}

			// Notify about changes done to a ticket in a subscribed list
			// AKA a ticket changed but we want to notify subscribers in whatever filter it was in last
			foreach ($change_info['orig_match'] as $agent) {
				if (!isset($agent_subs[$agent->id][$filter->id])) continue;

				$sub = $agent_subs[$agent->id][$filter->id];

				$types = array();
				if ($sub->email_property_change) {
					$types[] = 'email';
				} else {
					if ($notify_agent_note && $sub->email_agent_note) {
						$types[] = 'email';
					}
					if ($notify_agent_reply && $sub->email_agent_activity) {
						$types[] = 'email';
					}
					if ($notify_user_reply && $sub->email_user_activity) {
						$types[] = 'email';
					}
				}
				if ($sub->alert_property_change) {
					$types[] = 'alert';
				} else {
					if ($notify_agent_note && $sub->alert_agent_note) {
						$types[] = 'alert';
					}
					if ($notify_agent_reply && $sub->alert_agent_activity) {
						$types[] = 'alert';
					}
					if ($notify_user_reply && $sub->alert_user_activity) {
						$types[] = 'alert';
					}
				}

				// If orig matched but its not a new match,
				// then we know it's left this list
				if (!isset($new_match_agents[$agent->getId()])) {
					if (
						(!$filter->sys_name && $sub->email_leave)
						|| (($filter->sys_name == 'agent' || $filter->sys_name == 'unassigned') && $assign_change && $sub->email_leave)
						|| ($filter->sys_name == 'agent_team' && $assign_team_change && $sub->email_leave)
						|| ($filter->sys_name == 'participant' && $assign_follow_change && $sub->email_leave)
					) {
						$types[] = 'email';
					}
					if (
						(!$filter->sys_name && $sub->alert_leave)
						|| (($filter->sys_name == 'agent' || $filter->sys_name == 'unassigned') && $assign_change && $sub->alert_leave)
						|| ($filter->sys_name == 'agent_team' && $assign_team_change && $sub->alert_leave)
						|| ($filter->sys_name == 'participant' && $assign_follow_change && $sub->alert_leave)
					) {
						$types[] = 'email';
					}
				}

				if ($types) {
					$this->addToNotifyList($agent, $filter, 'update', $types);
				}
			}
		}

		$this->tracker->logMessage("[NotifyListBuilder] " . count($this->notify_list) . " agents with notifications");
		$this->tracker->logMessage("[NotifyListBuilder] " . \Orb\Util\Util::debugVar($this->notify_list));

		return $this->notify_list;
	}

	protected function addToNotifyList($agent, $filter, $changetype, array $notify_types)
	{
		if (!isset($this->notify_list[$agent->id])) {
			$this->notify_list[$agent->id] = array();
		}
		if (!isset($this->notify_list[$agent->id][$filter->id])) {
			$this->notify_list[$agent->id][$filter->id] = array(
				'agent'   => $agent,
				'filter'  => $filter,
				'is_new'     => false,
				'is_update'  => false,
				'types'   => array()
			);
		}

		$this->notify_list[$agent->id][$filter->id]["is_$changetype"] = true;
		$this->notify_list[$agent->id][$filter->id]['types'] = array_merge($this->notify_list[$agent->id][$filter->id]['types'], $notify_types);
		$this->notify_list[$agent->id][$filter->id]['types'] = array_unique($this->notify_list[$agent->id][$filter->id]['types']);
	}
}
