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
use Application\DeskPRO\Entity\TicketTrigger;

use Application\DeskPRO\EntityRepository\AgentTeam;
use Application\DeskPRO\EntityRepository\TicketLog;
use Application\DeskPRO\Tickets\TicketActions\AgentAction;
use Application\DeskPRO\Tickets\TicketActions\AgentTeamAction;
use Application\DeskPRO\Tickets\TicketActions\CategoryAction;
use Application\DeskPRO\Tickets\TicketActions\DepartmentAction;
use Application\DeskPRO\Tickets\TicketActions\PriorityAction;
use Application\DeskPRO\Tickets\TicketActions\ProductAction;
use Application\DeskPRO\Tickets\TicketActions\StatusAction;
use Application\DeskPRO\Tickets\TicketActions\WorkflowAction;
use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Tickets\TicketActions\ActionsCollection;

use Orb\Util\Arrays;

class TriggerExecutor
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $listener;

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket;

	/**
	 * @var array
	 */
	protected $event_types = array();

	protected $all_triggers = array();
	protected $is_performing = false;
	protected $is_cancelled = false;

	public function __construct(TicketChangeTracker $tracker)
	{
		$this->tracker = $tracker;
		$this->ticket = $tracker->getTicket();
	}

	/**
	 * @return \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	public function getChangeTracker()
	{
		return $this->tracker;
	}

	/**
	 * @return array
	 */
	public function getEventTypes()
	{
		return $this->event_types;
	}

	public function runPre()
	{
		if (!$this->tracker->isExtraSet('ticket_created')) {
			return;
		}

		if ($this->is_performing) return;
		$this->is_performing = true;

		$this->tracker->logMessage("[TriggerExecutor] pre");

		$ticket_created_trigger = null;
		$ticket_created_trigger = new \Application\DeskPRO\Entity\TicketTrigger();
		$ticket_created_trigger->terms = array();
		$ticket_created_trigger->actions = array(
			array('type' => 'new_ticket', 'options' => array('mode' => 'pre'))
		);

		$event = TicketTrigger::getNewTicketEventName($this->ticket->creation_system);
		$all_triggers = App::getEntityRepository('DeskPRO:TicketTrigger')->getTriggersForEvents(array($event));
		$all_triggers[] = $ticket_created_trigger;

		$factory = new \Application\DeskPRO\Tickets\TicketActions\ActionsFactory();
		$factory->addGlobalOption('tracker', $this->tracker);
		$factory->addGlobalOption('ticket', $this->tracker->getTicket());

		$actions_collection = new ActionsCollection();
		$stop_actions = false;

		foreach ($all_triggers as $trigger) {
			if ($trigger->getId() && $stop_actions) {
				continue;
			}

			if ($trigger->isTriggerMatch($this->tracker->getTicket(), $this->tracker)) {
				$this->tracker->logMessage("[TriggerExecutor] Executing trigger {$trigger->id} {$trigger->event_trigger} " . print_r($trigger->terms,true) . " " . print_r($trigger->actions, true));

				foreach ($trigger['actions'] as $action_info) {
					$action = $factory->createFromInfo($action_info);

					if ($action instanceof \Application\DeskPRO\Tickets\TicketActions\ExecutionContextAware) {
						$action->setExecutionContext('trigger');
					}

					// Custom triggers only specify modifiers so
					// this pre action can be modified to force email validation
					// Actual actions will be run next with the usual run()

					if ($action &&
							(!$trigger->id
								|| $action instanceof \Application\DeskPRO\Tickets\TicketActions\CollectionModifierInterface
								|| $action instanceof \Application\DeskPRO\Tickets\TicketActions\ForceEmailValidationAction
							)
					) {
						$actions_collection->add($action);
						$this->tracker->recordExtraMulti('trigger', $trigger);
					}
				}
			}

			if ($actions_collection->hasModifierType('StopActions')) {
				$stop_actions = true;
			}
		}

		$person = App::getCurrentPerson();
		if (!$person) {
			$person = $this->tracker->getTicket()->person;
		}
		$actions_collection->apply(null, $this->tracker->getTicket(), $person);

		if ($actions_collection->isBroken()) {
			$this->is_cancelled = true;
		}

		$this->is_performing = false;
	}

	public function run()
	{
		if ($this->is_performing || $this->is_cancelled) return;

		$this->tracker->logMessage('[TriggerExecutor] run');
		$time = microtime(true);

		$this->is_performing = true;

		$status_change  = $this->tracker->getChangedProperty('status');
		$hstatus_change = $this->tracker->getChangedProperty('hidden_status');

		// If we've just validated, then we'll send off a fake
		// ticket_created event for the TriggerExecutor
		if (!$this->tracker->isExtraSet('ticket_created') && $this->ticket->status_code == 'awaiting_agent' && ($status_change['old'] == 'hidden' && $hstatus_change['old'] == 'validating')) {
			$this->tracker->logMessage('[TriggerExecutor] ticket_created true');
			$this->tracker->recordExtra('ticket_created', true);
		}

		// Mark that is a validating ticket created, created triggers
		// will be ignored in the TriggerExecutor
		if ($this->ticket->status_code == 'hidden.validating') {
			$this->tracker->logMessage('[TriggerExecutor] ticket_created_validating true');
			$this->tracker->recordExtra('ticket_created_validating', true);
		}

		#------------------------------
		# Handle built-in events
		#------------------------------

		$is_newticket = false;
		$is_propchange = false;
		$is_newreply = false;
		$performer = null;

		if ($this->tracker->isExtraSet('ticket_created')) {
			$is_newticket = true;
		} else {
			$is_propchange = true;

			if ($this->tracker->isPropertyChanged('messages')) {
				$is_newreply = true;
			}
		}

		if (DP_INTERFACE == 'agent') {
			$performer = 'agent';
		} elseif (DP_INTERFACE == 'user') {
			$performer = 'user';
		} elseif (DP_INTERFACE == 'api') {
			$performer = 'api';
		} else {
			if ($is_newticket) {
				if (strpos($this->ticket->creation_system, 'agent') !== false) {
					$performer = 'agent';
				} elseif (strpos($this->ticket->creation_system, 'api') !== false) {
					$performer = 'api';
				} elseif (strpos($this->ticket->creation_system, 'gateway') !== false) {
					if ($this->tracker->hasNewAgentReply() && $this->tracker->getNewAgentReply()->person->id != $this->ticket->person->id) {
						$performer = 'agent';
					} else {
						$performer = 'user';
					}
				} else {
					$performer = 'user';
				}
			} elseif ($this->tracker->isExtraSet('is_agent_reply')) {
				$performer = 'agent';
			} elseif (isset($GLOBALS['DP_ESCALATION_RUNNING']) && $GLOBALS['DP_ESCALATION_RUNNING']) {
				$performer = null;
			} else {
				$performer = 'user';
			}
		}

		if ($is_newticket) {
			$this->event_types = array(TicketTrigger::getNewTicketEventName($this->ticket->creation_system));
		} else {
			if ($performer == 'agent') {
				$this->event_types = array('update.agent');
			} elseif ($performer == 'api') {
				$this->event_types = array('update.api');
			} elseif (isset($GLOBALS['DP_ESCALATION_RUNNING']) && $GLOBALS['DP_ESCALATION_RUNNING']) {
				$this->event_types = array('update.escalation');
			} else {
				$this->event_types = array('update.user');
			}
		}

		$primary_event = Arrays::getFirstItem($this->event_types);

		if ($this->tracker->isExtraSet('is_fwd_reply')) {
			$this->event_types[] = 'new.email.agent';
			$primary_event = 'new.email.agent';
		}

		$this->tracker->logMessage('[TriggerExecutor] Events: ' . implode(', ', $this->event_types));
		$this->tracker->logMessage('[TriggerExecutor] Primary Event: ' . $primary_event);

		#-------------------------
		# Primary ticket log
		#-------------------------

		if ($primary_event) {
			$ticket_log = new \Application\DeskPRO\Entity\TicketLog();

			if ((DP_INTERFACE == 'agent' || DP_INTERFACE == 'user') && (App::getCurrentPerson() && App::getCurrentPerson()->id)) {
				$ticket_log['person'] = App::getCurrentPerson();
			} elseif ($this->tracker->hasNewReply()) {
				$ticket_log['person'] = $this->tracker->getNewReply()->person;
			} else if ($this->tracker->getExtra('person_performer')) {
				$ticket_log['person'] = $this->tracker->getExtra('person_performer');
			}

			$ticket_log['ticket'] = $this->ticket;
			$ticket_log['action_type'] = 'action_starter';
			$ticket_log['details'] = array(
				'event'     => $primary_event,
				'interface' => DP_INTERFACE,
				'has_reply' => $this->tracker->hasNewReply(),
				'has_agent_reply' => $this->tracker->hasNewAgentReply(),
				'has_user_reply'  => $this->tracker->hasNewUserReply(),
			);

			$this->tracker->recordExtra('primary_ticket_log', $ticket_log);

			if ($this->ticket->inserted_log_row_batch) {
				foreach ($this->ticket->inserted_log_row_batch as $l) {
					$l['parent'] = $ticket_log;
				}
			}
		}


		#-------------------------
		# Triggers
		#-------------------------

		$all_triggers = App::getEntityRepository('DeskPRO:TicketTrigger')->getTriggersForEvents($this->event_types);

		// Note that the "built in" triggers below for notifications,
		// its important that they're array_unshift'ed onto the BEGINNING
		// of the $all_triggers array
		// This is because they can be modified like any other trigger,
		// so we dont want them added at the end after modifiers
		// are already run. For example: Template overrides, disabling notifications,
		// adding more users to notifications, etc.

		#------------------------------
		# Notify the user of course
		#------------------------------

		$is_bounce = false;
		if ($this->getChangeTracker()->isExtraSet('is_bounce_message')) {
			$is_bounce = true;
		}

		$ticket_created_trigger = null;
		if (!$is_bounce) {
			if ($this->tracker->isExtraSet('ticket_created')) {
				$trigger = new \Application\DeskPRO\Entity\TicketTrigger();
				$trigger->terms = array();
				$trigger->actions = array(
					array('type' => 'new_ticket', 'options' => array('mode' => 'run'))
				);

				$ticket_created_trigger = $trigger;

			// We need the is_user_reply check to make sure the reply wasnt made from the user interface
			// i.e., an agent logged in to user interface replying
			} elseif ($this->tracker->hasNewAgentReply() && !$this->tracker->isExtraSet('is_user_reply')) {
				$trigger = new \Application\DeskPRO\Entity\TicketTrigger();
				$trigger->terms = array();
				$trigger->actions = array(
					array('type' => 'user_notification_new_reply_agent', 'options' => array())
				);

				$all_triggers[] = $trigger;
			} elseif ($this->tracker->hasNewUserReply() || $this->tracker->isExtraSet('is_user_reply')) {
				$trigger = new \Application\DeskPRO\Entity\TicketTrigger();
				$trigger->terms = array();
				$trigger->actions = array(
					array('type' => 'user_notification_new_reply_user', 'options' => array())
				);

				$all_triggers[] = $trigger;

				if (DP_INTERFACE == 'user') {
					$message = $this->tracker->getNewUserReply();

					$trigger = new \Application\DeskPRO\Entity\TicketTrigger();
					$trigger->terms = array();
					$trigger->actions = array(
						array('type' => 'user_notification_new_reply_user_other', 'options' => array(
							'skip_person_id' => $message->getPersonId()
						))
					);

					$all_triggers[] = $trigger;
				}
			}
		}

		if (in_array('new.web.agent.portal', $this->event_types)) {
			// Enable newticket notification
			// The checkbox option on the form toggles the extra status suppress_user_notify to turn it off
			$trigger = new \Application\DeskPRO\Entity\TicketTrigger();
				$trigger->terms = array();
				$trigger->actions = array(
					array('type' => 'enable_new_ticket_confirmation', 'options' => array('enabled' => 1))
				);

				$all_triggers[] = $trigger;
		}

		#------------------------------
		# Handle vacation mode agent
		#------------------------------

		// If the assigned agent is on vacation and the status is now awaiting_agent,
		// the ticket must be unasssigned

		if (($this->tracker->getTicket()->status == 'awaiting_agent' || $this->tracker->getTicket()->status == 'awaiting_user') && $this->tracker->getTicket()->agent && $this->tracker->getTicket()->agent->is_deleted) {
			$trigger = new \Application\DeskPRO\Entity\TicketTrigger();
			$trigger->terms = array();
			$trigger->actions = array(
				array('type' => 'agent', 'options' => array('agent' => 0))
			);

			array_unshift($all_triggers, $trigger);
		}

		#------------------------------
		# Execute triggers
		#------------------------------

		if ($ticket_created_trigger) {
			$all_triggers[] = $ticket_created_trigger;
		}

		$factory = new \Application\DeskPRO\Tickets\TicketActions\ActionsFactory();
		$factory->addGlobalOption('tracker', $this->tracker);
		$factory->addGlobalOption('ticket', $this->tracker->getTicket());

		$actions_collection = new ActionsCollection();
		$set_modifiers = array();

		$stop_actions = false;

		$override_actions = $this->tracker->getExtra('reply_actions_override');

		foreach ($all_triggers as $trigger) {

			// Doing a check on ID because some system triggers like notifications are always run,
			// and they are created above so arent actual records
			if ($trigger->getId() && $stop_actions) {
				continue;
			}

			$trigger_time = microtime(true);
			$this->tracker->logMessage("[TriggerExecutor] Testing trigger match {$trigger->id} {$trigger->event_trigger} " . print_r($trigger->terms,true) . " " . print_r($trigger->terms_any,true) . print_r($trigger->actions, true));
			if ($trigger->isTriggerMatch($this->tracker->getTicket(), $this->tracker)) {

				$this->tracker->logMessage(sprintf('[TriggerExecutor] -- Match', microtime(true)-$trigger_time));
				$this->tracker->recordExtraMulti('trigger', $trigger);

				foreach ($trigger['actions'] as $action_info) {
					$action = $factory->createFromInfo($action_info);

					if ($action instanceof \Application\DeskPRO\Tickets\TicketActions\ExecutionContextAware) {
						$action->setExecutionContext('trigger');
					}

					if ($override_actions) {
						if ($action instanceof AgentAction && isset($override_actions['assign_agent'])) {
							continue;
						}
						if ($action instanceof AgentTeamAction && isset($override_actions['assign_agent_team'])) {
							continue;
						}
						if ($action instanceof DepartmentAction && isset($override_actions['department'])) {
							continue;
						}
						if ($action instanceof ProductAction && isset($override_actions['product'])) {
							continue;
						}
						if ($action instanceof CategoryAction && isset($override_actions['category'])) {
							continue;
						}
						if ($action instanceof WorkflowAction && isset($override_actions['workflow'])) {
							continue;
						}
						if ($action instanceof PriorityAction && isset($override_actions['priority'])) {
							continue;
						}
					}

					// Saving a copy of the modifiers set so we can apply them to the secondary
					// collection for agent notifications
					if ($action instanceof \Application\DeskPRO\Tickets\TicketActions\CollectionModifierInterface) {
						$set_modifiers[] = array('modifier' => $action, 'trigger' => $trigger);
					}

					if ($action) {
						$actions_collection->add($action, array('trigger' => $trigger));
					}
				}
			} else {
				$this->tracker->logMessage(sprintf('[TriggerExecutor] -- No match', microtime(true)-$trigger_time));
			}

			$this->tracker->logMessage(sprintf('[TriggerExecutor] -- Done trigger in %.4f seconds', microtime(true)-$trigger_time));

			if ($actions_collection->hasModifierType('StopActions')) {
				$this->tracker->logMessage(sprintf('[TriggerExecutor] -- Got StopActions signal', microtime(true)-$trigger_time));
				$stop_actions = true;
			}
		}

		// Some actions need to use variables that could potentially be assigned by previous actions.
		// Eg., reply can be written by "Assigned Agent" which might not be set until after a trigger action runs
		// So we say ReplyAction is run after Agent so it can use that variable
		$actions_collection->sortActions(array(
			'prepend'                                   => 0,
			'default'                                   => 1,
			'ReplyAction'                               => 1000,
			'NewTicketAction'                           => 1000,
			'AgentNotificationAction'                   => 1000,
			'SendAgentEmail'                            => 1000,
			'SendUserEmail'                             => 1000,
			'SendTicketEmail'                           => 1000,
			'UserNotificationNewReplyAgentAction'       => 1000,
			'UserNotificationNewReplyUserAction'        => 1000,
			'UserNotificationNewReplyParticipantAction' => 1000,
			'UserNotificationNewReplyUserOtherAction'   => 1000,
		));

		#------------------------------
		# Flood checks / autoreply checks
		#------------------------------

		$flood_check = true;
		$is_email = false;
		if ($this->tracker->isNewTicket()) {
			if (strpos($this->ticket->creation_system, 'gateway') !== false) {
				$is_email = true;
			}
		} else if ($reply = $this->tracker->getNewReply()) {
			if (strpos($reply ->creation_system, 'gateway') !== false) {
				$is_email = true;
			}
		}

		if (
			($this->tracker->isNewTicket() && $this->tracker->getTicket()->person->disable_autoresponses)
			|| ($this->tracker->getNewUserReply() && $this->tracker->getNewUserReply()->person->disable_autoresponses)
		) {
			$flood_check = false;
		}

		if ($is_email && $flood_check && !App::getSetting('core.disable_gateway_floodcheck')) {
			$is_autoreply = false;

			if ($is_newticket) {
				$timesnip = date('Y-m-d H:i:s', time() - App::getSetting('core_email.antiflood_newtickets_time'));
				$new_ticket_count = App::getDb()->fetchColumn("
					SELECT COUNT(*)
					FROM tickets
					WHERE person_id = ? AND date_created > ?
				", array($this->tracker->getTicket()->person->id, $timesnip));

				// If ticket is over antiflood, require validation
				if ($new_ticket_count > App::getSetting('core_email.antiflood_newtickets')) {
					$this->tracker->logMessage('Anti-flood delete');
					$actions_collection->add($factory->create('force_email_validation', array()));

				// Lower threshold for turning off notificaiton
				} elseif ($new_ticket_count >= App::getSetting('core_email.antiflood_newtickets_warn')) {

					$this->tracker->logMessage('Anti-flood adding trigger: disable_user_notifications');
					$actions_collection->add($factory->create('disable_user_notifications', array()));

					// If it is exactly the count, then send the warning email
					if ($new_ticket_count == App::getSetting('core_email.antiflood_newtickets_warn')) {
						$this->tracker->logMessage('Anti-flood adding trigger: warn_newticket_flood');
						$actions_collection->add($factory->create('warn_newticket_flood', array()));
					}
				}

				// Always disable user notificatiosn if message advertises itself as autoreply
				if ($this->ticket->email_reader && $this->ticket->email_reader->isFromRobot()) {
					$is_autoreply = true;
				}

			} elseif (in_array('new_reply', $this->event_types) && $this->tracker->hasNewUserReply()) {

				$timesnip = date('Y-m-d H:i:s', time() - App::getSetting('core_email.antiflood_newtickets_time'));
				$new_ticket_count = App::getDb()->fetchColumn("
					SELECT COUNT(*)
					FROM tickets_messages
					WHERE person_id = ? AND date_created > ?
				", array($this->tracker->getTicket()->person->id, $timesnip));

				// Dont send notifications to anyone now
				if ($new_ticket_count > App::getSetting('core_email.antiflood_newreplies')) {
					$this->tracker->logMessage('Anti-flood adding trigger: disable_user_notifications');
					$actions_collection->add($factory->create('disable_notifications', array()));

				// Lower threshold for turning off user notificaiton to prevent loops
				} elseif ($new_ticket_count >= App::getSetting('core_email.antiflood_newreplies_warn')) {

					$this->tracker->logMessage('Anti-flood adding trigger: disable_user_notifications');
					$actions_collection->add($factory->create('disable_user_notifications', array()));

					// If it is exactly the count, then send the warning email
					if ($new_ticket_count == App::getSetting('core_email.antiflood_newreplies_warn')) {
						$this->tracker->logMessage('Anti-flood adding trigger: warn_newticket_flood');
						$actions_collection->add($factory->create('warn_newticket_flood', array()));
					}
				}

				$messages = $this->tracker->getChangedProperty('messages');
				foreach ($messages as $m) {
					$m = $m['new'];
					if ($m->email_reader && $m->email_reader->isFromRobot()) {
						$is_autoreply = true;
					}
				}
			}

			if ($is_autoreply) {
				$this->tracker->logMessage('Is Auto-Reply, adding trigger: disable_user_notifications');
				$actions_collection->add($factory->create('disable_user_notifications', array()));

				$change_info = array(
					'type'    => 'free',
					'message' => 'User breached flood limit, notifications for this ticket were suppressed',
				);
				$this->tracker->recordMultiPropertyChanged('log_actions', null, $change_info);
			}
		}

		#------------------------------
		# Execute triggers
		#------------------------------

		$person = App::getCurrentPerson();
		if (!$person) {
			$person = $this->tracker->getTicket()->person;
		}

		$trigger_apply_time = microtime(true);
		$this->tracker->logMessage(sprintf('[TriggerExecutor] Applying %d actions', $actions_collection->countActions()));
		$actions_collection->apply($this->tracker, $this->ticket, $person, $this->tracker->getLog());

		App::getOrm()->persist($this->ticket);
		App::getOrm()->flush();

		$this->tracker->logMessage(sprintf('[TriggerExecutor] -- Done in %.4f sections', microtime(true)-$trigger_apply_time));

		$this->tracker->logMessage(sprintf('[TriggerExecutor] Done all work in %.4f seconds', microtime(true)-$time));

		#------------------------------
		# Execute default notification triggers
		#------------------------------

		if (!$is_bounce) {
			$all_triggers = array();

			$trigger = new \Application\DeskPRO\Entity\TicketTrigger();
			$trigger->terms = array();
			$trigger->actions = array(
				array('type' => 'agent_alert_notification', 'options' => array())
			);

			array_unshift($all_triggers, $trigger);

			$trigger = new \Application\DeskPRO\Entity\TicketTrigger();
			$trigger->terms = array();
			$trigger->actions = array(
				array('type' => 'agent_notification', 'options' => array())
			);

			array_unshift($all_triggers, $trigger);

			$actions_collection = new ActionsCollection();

			foreach ($all_triggers as $trigger) {
				$trigger_time = microtime(true);
				$this->tracker->logMessage("[TriggerExecutor] Testing trigger match {$trigger->id} {$trigger->event_trigger} " . print_r($trigger->terms,true) . " " . print_r($trigger->actions, true));
				if ($trigger->isTriggerMatch($this->tracker->getTicket(), $this->tracker)) {

					$this->tracker->logMessage(sprintf('[TriggerExecutor] -- Match', microtime(true)-$trigger_time));

					foreach ($trigger['actions'] as $action_info) {
						$action = $factory->createFromInfo($action_info);
						if ($action) {
							$actions_collection->add($action, array('trigger' => $trigger));
							$this->tracker->recordExtraMulti('trigger', $trigger);
						}
					}
				} else {
					$this->tracker->logMessage(sprintf('[TriggerExecutor] -- No match', microtime(true)-$trigger_time));
				}

				$this->tracker->logMessage(sprintf('[TriggerExecutor] -- Done trigger in %.4f seconds', microtime(true)-$trigger_time));
			}

			foreach ($set_modifiers as $mod) {
				$trigger = $mod['trigger'];
				$this->tracker->logMessage("[TriggerExecutor] Applying {$trigger->id} {$trigger->event_trigger} to agent notify collections");
				$actions_collection->add($mod['modifier'], array('trigger' => $mod['trigger']));
			}

			$actions_collection->apply($this->tracker, $this->tracker->getTicket(), $person, $this->tracker->getLog());
		}

		$this->is_performing = false;
	}
}
