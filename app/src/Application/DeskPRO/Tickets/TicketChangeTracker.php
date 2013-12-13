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
 * @category Tickets
 */

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\LabelTicket;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketParticipant;
use Application\DeskPRO\Entity\TicketTrigger;
use Application\DeskPRO\Domain\ChangeTracker;

use Doctrine\Common\Collections\ArrayCollection;
use Orb\Util\Arrays;

/**
 * The ticket change tracker listens for changes to a ticket, and then runs inspections once the changes
 * are committed. The changes can trigger other events and other changes, such as inserting change logs,
 * sending email notifications or applying triggers.
 */
class TicketChangeTracker extends ChangeTracker
{
	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket;

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $original_ticket = null;

	/**
	 * @var bool
	 */
	protected $is_new_ticket = false;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeInspector\Log
	 */
	protected $log_inspector;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeInspector\TriggerExecutor
	 */
	protected $exec_inspector;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeInspector\ListUpdater
	 */
	protected $list_updater;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeInspector\SearchUpdater
	 */
	protected $search_updater;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeInspector\DetectFilterMatches
	 */
	protected $filter_detector;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeInspector\NotifyListBuilder
	 */
	protected $notify_list_builder;

	/**
	 * @var bool
	 */
	protected $has_non_ignored = false;

	/**
	 * @var bool
	 */
	protected $running = false;

	/**
	 * @var \Application\DeskPRO\Entity\TicketTrigger|null
	 */
	protected $applying_trigger = null;

	/**
	 * @var \Application\DeskPRO\Entity\Sla|null
	 */
	protected $applying_sla = null;

	/**
	 * @var string|null
	 */
	protected $applying_sla_status = null;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $log;

	/**
	 * @var float
	 */
	protected $start_time = 0;

	/**
	 * @var \Orb\Log\Writer\ArrayWriter
	 */
	protected $arr_writer;

	/**
	 * @var array
	 */
	protected $trigger_changed_fields = array();

	/**
	 * Fields that shouldnt trigger the full logger and filter inspections
	 * THey are still recoreded and may still be used as criteria, but they are always
	 * accompanied by a real trigger such as a status change etc. So by themselves
	 * they dont trigger inspections.
	 *
	 * (Really the only one that needs to be here is date_locked since the others are never
	 * actually set in code.)
	 *
	 * @var array
	 */
	public static $ignored_fields = array(
		'id', 'ref', 'auth', 'attachments', 'access_codes', 'email_gateway', 'ticket_hash',
		'date_created', 'date_resolved', 'date_closed', 'date_closed', 'date_first_agent_assign',
		'date_first_agent_reply', 'date_last_agent_reply', 'date_last_user_reply',
		'date_agent_waiting', 'date_user_waiting', 'total_user_waiting', 'total_to_first_reply',
		'locked_by_agent', 'date_locked', 'has_attachments',
	);

	public function __construct(Ticket $ticket)
	{
		$this->entity     = $ticket;
		$this->ticket     = $ticket;
		$this->arr_writer = new \Orb\Log\Writer\ArrayWriter();
		$this->arr_writer->_x_ticket_tracker = true;

		if (defined('DP_INTERFACE') && DP_INTERFACE == 'cli') {
			$this->person_context = null;
		} else {
			$this->person_context = App::getCurrentPerson();
		}

		if (!$ticket['id']) {
			$this->is_new_ticket = true;
		}

		if (DP_INTERFACE == 'api') {
			$this->recordExtra('api_key', \Application\ApiBundle\StaticLoader\RequestKey::getApiKeyFromRequest());
		}
	}


	/**
	 * @return \Application\DeskPRO\Entity\Person|null
	 */
	public function getPersonPerformer()
	{
		if ($this->isExtraSet('sla')) {
			return null;
		}

		if ($this->person_context && $this->person_context->getId()) {
			return $this->person_context;
		}

		$reply = $this->getNewAgentReply();
		if (!$reply) {
			$reply = $this->getNewUserReply();
		}

		if ($this->isExtraSet('person_performer')) {
			return $this->getExtra('person_performer');
		}

		if (!$reply) {
			return null;
		}

		return $reply->person;
	}


	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLog()
	{
		if ($this->log) return $this->log;

		if (App::getConfig('debug.ticket_change_logger')) {
			$logger = new \Orb\Log\Logger();
			$writer = new \Orb\Log\Writer\Stream(App::getContainer()->getLogDir() . '/ticket-change-tracker.log');
			$logger->addWriter($writer);

			if (DP_INTERFACE == 'cli') {
				$writer = new \Orb\Log\Writer\Output();
				$logger->addWriter($writer);
			}

			$this->log = $logger;
		} else {
			$logger = new \Orb\Log\Logger();
			$this->log = $logger;
		}

		$this->log->addWriter($this->arr_writer);

		return $this->log;
	}

	protected function _cleanOldWriters()
	{
		if ($this->log) {
			foreach ($this->log->getWriterChain()->getWriters() as $wr) {
				if ($wr instanceof \Orb\Log\Writer\ArrayWriter && isset($wr->_x_ticket_tracker)) {
					$this->log->removeWriter($wr);
				}
			}
		}
	}


	public function setLogger(\Orb\Log\Logger $logger)
	{
		$this->log = $logger;
		$this->_cleanOldWriters();

		$this->log->addWriter($this->arr_writer);
	}

	/**
	 * If a trigger is being applied, then this sets it so ticket log and future actions
	 * know that a trigger is causing the changes.
	 *
	 * @param \Application\DeskPRO\Entity\TicketTrigger|null $trigger
	 */
	public function setApplyingTrigger(TicketTrigger $trigger = null)
	{
		$this->applying_trigger = $trigger;
	}

	/**
	 * Get the applying trigger if there is one
	 */
	public function getApplyingTrigger()
	{
		return $this->applying_trigger;
	}

	/**
	 * If a SLA trigger is being applied, then this sets it so ticket log and future actions
	 * know that the SLA is causing the changes.
	 *
	 * @param \Application\DeskPRO\Entity\Sla|null $sla
	 */
	public function setApplyingSla(\Application\DeskPRO\Entity\Sla $sla = null, $sla_status = null)
	{
		$this->applying_sla = $sla;
		$this->applying_sla_status = $sla_status;
	}

	/**
	 * Get the applying SLA if there is one
	 *
	 * @return \Application\DeskPRO\Entity\Sla|null
	 */
	public function getApplyingSla()
	{
		return $this->applying_sla;
	}

	/**
	 * Get the applying SLA status if there is one
	 *
	 * @return string|null
	 */
	public function getApplyingSlaStatus()
	{
		return $this->applying_sla_status;
	}


	/**
	 * Send a message to the ticket change tracker log.
	 * To enable the log, enable the `debug.ticket_change_logger` option in config.
	 *
	 * @param string $message
	 */
	public function logMessage($message)
	{
		$this->getLog();
		if ($this->log) {
			$this->log->log($message, \Orb\Log\Logger::DEBUG);
		}
	}


	/**
	 * Checks if this ticket is new, or should be TREATED as new.
	 * A ticket should be treated as new when it comes out of validation.
	 *
	 * @return bool
	 */
	public function isNewTicket()
	{
		$status_change  = $this->getChangedProperty('status');
		$hstatus_change = $this->getChangedProperty('hidden_status');

		if ($this->isExtraSet('ticket_created') || ($this->ticket->status_code == 'awaiting_agent' && $status_change['old'] == 'hidden.validating')) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a new agent reply was added
	 *
	 * @return bool
	 */
	public function hasNewAgentReply()
	{
		$messages = $this->getChangedProperty('messages');
		if ($messages) {
			$message = array_shift($messages);
			$message = $message['new'];
			if ($message && $message->person->is_agent && !$this->isExtraSet('is_user_reply')) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the new agent reply just added
	 *
	 * @return \Application\DeskPRO\Entity\TicketMessage
	 */
	public function getNewAgentReply()
	{
		$messages = $this->getChangedProperty('messages');
		if ($messages) {
			$message = array_shift($messages);
			$message = $message['new'];
			if ($message && $message->person->is_agent && !$this->isExtraSet('is_user_reply')) {
				return $message;
			}
		}

		return null;
	}

	/**
	 * Get the new agent reply just added
	 *
	 * @return \Application\DeskPRO\Entity\TicketMessage
	 */
	public function getNewUserReply()
	{
		$messages = $this->getChangedProperty('messages');
		if ($messages) {
			$message = array_shift($messages);
			$message = $message['new'];
			if ($message && !$message->person->is_agent || $this->isExtraSet('is_user_reply')) {
				return $message;
			}
		}

		return null;
	}


	/**
	 * Check if a new user reply was added
	 *
	 * @return bool
	 */
	public function hasNewUserReply()
	{
		$messages = $this->getChangedProperty('messages');
		if ($messages) {
			$message = array_shift($messages);
			$message = $message['new'];
			if ($message && !$message->person->is_agent || $this->isExtraSet('is_user_reply')) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if theres a new reply added
	 *
	 * @return bool
	 */
	public function hasNewReply()
	{
		$messages = $this->getChangedProperty('messages');
		if ($messages) {
			$message = array_shift($messages);
			$message = $message['new'];
			if ($message) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Get the new reply added
	 *
	 * @return mixed
	 */
	public function getNewReply()
	{
		$messages = $this->getChangedProperty('messages');
		if ($messages) {
			$message = array_shift($messages);
			$message = $message['new'];
			if ($message) {
				return $message;
			}
		}

		return false;
	}


	/**
	 * Get the ticket
	 *
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public function getTicket()
	{
		return $this->ticket;
	}


	/**
	 * Get the original ticket before changes, used for comparisons usually
	 *
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public  function getOriginalTicket()
	{
		/*
		 * Manually reconstructing the original ticket based off of the changelog.
		 * Easier would have been to clone the ticket in __construct, but because
		 * this tracker is created in Doctrine's PostLoad event, object references
		 * are set up yet and we'd end up with all these relationships being 0.
		 * (Which was what happened the first time I wrote the functionality!)
		 */

		if ($this->original_ticket !== null) return $this->original_ticket;

		$this->original_ticket = clone $this->ticket;
		$this->original_ticket->id = null;
		$this->original_ticket->_setNoPersist();

		foreach ($this->getAllChangedProperties() as $prop => $info) {
			$action = null;

			$old_val = null;

			if (isset($info['old'])) $old_val = $info['old'];

			switch ($prop) {
				case 'agent':
					$this->original_ticket['agent'] = $old_val;
					break;

				case 'agent_team':
					$this->original_ticket['agent_team'] = $old_val;
					break;

				case 'person':
					if (!$this->is_new_ticket) {
						$this->original_ticket['person'] = $old_val;
					}
					break;

				case 'category':
					$this->original_ticket['category'] = $old_val;
					break;

				case 'department':
					$this->original_ticket['department'] = $old_val;
					break;

				case 'priority':
					$this->original_ticket['priority'] = $old_val;
					break;

				case 'product':
					$this->original_ticket['product'] = $old_val;
					break;

				case 'workflow':
					$this->original_ticket['workflow'] = $old_val;
					break;

				case 'subject':
					$this->original_ticket['subject'] = $old_val;
					break;

				case 'is_hold':
					$this->original_ticket['is_hold'] = $old_val;
					break;
			}
		}

		if ($this->isPropertyChanged('label_added') || $this->isPropertyChanged('label_removed')) {
			$labels = new ArrayCollection();

			if ($this->isPropertyChanged('label_removed')) {
				foreach ($this->getChangedProperty('label_removed') as $info) {
					$l = new LabelTicket();
					$l->label = $info['old'];
					$labels->add($l);
				}
			}

			$added = array();
			if ($this->isPropertyChanged('label_added')) {
				foreach ($this->getChangedProperty('label_added') as $info) {
					$added[$info['new']] = true;
				}
			}

			foreach ($this->ticket->labels as $l) {
				if (!isset($added[$l->label])) {
					$labels[] = $l;
				}
			}

			$this->original_ticket->setUntrackedModelField('labels', $labels);
		}

		$parts = new ArrayCollection();

		if ($this->ticket->part_del_ids) {
			foreach ($this->ticket->part_del_ids as $pid) {
				$p = new TicketParticipant();
				$p->setPerson(App::getOrm()->find('DeskPRO:Person', $pid));
				$parts->add($p);
			}
		}

		$added_ids = array();
		if ($this->ticket->part_add_ids) {
			$added_ids = $this->ticket->part_add_ids;
		}

		foreach ($this->ticket->participants as $p1) {
			if (!in_array($p1->getPerson()->getId(), $added_ids)) {
				$p = new TicketParticipant();
				$p->setPerson($p1->getPerson());
				$parts->add($p);
			}
		}

		$this->original_ticket->setUntrackedModelField('participants', $parts);

		if (!$this->isNewTicket()) {
			if ($this->isPropertyChanged('hidden_status')) {
				$tmp = $this->getChangedProperty('hidden_status');
				if ($tmp['old']) {
					$old_status_code = 'hidden.' . $tmp['old'];
				} else {
					$tmp = $this->getChangedProperty('status');
					$old_status_code = $tmp['old'];
				}

				$this->original_ticket->setStatus($old_status_code);
			} elseif ($this->isPropertyChanged('status')) {
				$tmp = $this->getChangedProperty('status');
				$old_status_code = $tmp['old'];

				$this->original_ticket->setStatus($old_status_code);
			}
		}

		foreach ($this->getAllChangedProperties() as $prop => $info) {
			$action = null;
			$old_val = null;

			if (isset($info['old'])) $old_val = $info['old'];

			switch ($prop) {
				case 'date_agent_waiting':
				case 'date_user_waiting':
				case 'date_status':
				case 'total_user_waiting':
				case 'total_to_first_reply':
				case 'date_closed':
				case 'date_resolved':
					$this->original_ticket[$prop] = $old_val;
					break;
			}
		}

		return $this->original_ticket;
	}

	public function getChangeData($prop, $old_val, $new_val)
	{
		$data = parent::getChangeData($prop, $old_val, $new_val);

		if ($this->applying_trigger) {
			$data['trigger_id'] = $this->applying_trigger->getId();
			if (App::getDataService('ticket_trigger')->hasEscalationId($data['trigger_id'])) {
				$data['trigger_is_escalation'] = true;
			}
		}
		if ($this->applying_sla) {
			$data['sla_id'] = $this->applying_sla->getId();
			if ($this->applying_sla_status) {
				$data['sla_status'] = $this->applying_sla_status;
			}
		}

		return $data;
	}

	public function propertyChanged($sender, $prop, $old_val, $new_val)
	{
		if (!in_array($prop, self::$ignored_fields)) {
			$this->has_non_ignored = true;
		}

		if (in_array($prop, array('messages'))) {
			$this->recordMultiPropertyChanged($prop, $old_val, $new_val);
		} else {
			$this->recordPropertyChanged($prop, $old_val, $new_val);
		}
	}

	public function recordPropertyChanged($prop, $old_val, $new_val)
	{
		if (!in_array($prop, self::$ignored_fields)) {
			$this->has_non_ignored = true;
		}

		parent::recordPropertyChanged($prop, $old_val, $new_val);

		if ($this->applying_trigger) {
			$this->trigger_changed_fields[$prop] = $prop;
		}
	}

	public function recordMultiPropertyChanged($prop, $old_val, $new_val)
	{
		$this->has_non_ignored = true;
		parent::recordMultiPropertyChanged($prop, $old_val, $new_val);

		if ($this->applying_trigger) {
			$this->trigger_changed_fields[$prop] = $prop;
		}
	}


	/**
	 * @param string $prop
	 * @return bool
	 */
	public function isTriggerChangeField($prop)
	{
		return isset($this->trigger_changed_fields[$prop]);
	}


	/**
	 * Service to fetch information about how this change affected various filters.
	 *
	 * @return \Application\DeskPRO\Tickets\TicketChangeInspector\DetectFilterMatches
	 */
	public function getFilterDetector()
	{
		if ($this->filter_detector !== null) return $this->filter_detector;

		$this->logMessage('[TicketChangeTracker] init filter_detector');

		$this->filter_detector = new TicketChangeInspector\DetectFilterMatches($this);
		return $this->filter_detector;
	}


	/**
	 * Service to generate lists of who should be notified based off of preferences and
	 * how filters were affected.
	 *
	 * @return \Application\DeskPRO\Tickets\TicketChangeInspector\NotifyListBuilder;
	 */
	public function getNotifyListBuilder()
	{
		if ($this->notify_list_builder !== null) return $this->notify_list_builder;
		$this->notify_list_builder = new TicketChangeInspector\NotifyListBuilder($this, $this->getFilterDetector());

		$this->logMessage('[TicketChangeTracker] init notify_list_builder');

		return $this->notify_list_builder;
	}


	/**
	 * @return \Application\DeskPRO\Tickets\TicketChangeInspector\Log
	 */
	public function getLogInspector()
	{
		if ($this->log_inspector !== null) return $this->log_inspector;

		$this->logMessage('[TicketChangeTracker] init log_inspector');

		$this->log_inspector = new TicketChangeInspector\Log($this);
		return $this->log_inspector;
	}


	/**
	 * @return \Application\DeskPRO\Tickets\TicketChangeInspector\TriggerExecutor
	 */
	public function getTriggerExecutorInspector()
	{
		if ($this->exec_inspector !== null) return $this->exec_inspector;

		$this->logMessage('[TicketChangeTracker] init exec_inspector');

		$this->exec_inspector = new TicketChangeInspector\TriggerExecutor($this);
		return $this->exec_inspector;
	}


	/**
	 * @return \Application\DeskPRO\Tickets\TicketChangeInspector\ListUpdater
	 */
	public  function getListUpdater()
	{
		if ($this->list_updater !== null) return $this->list_updater;

		$this->logMessage('[TicketChangeTracker] init list_updater');

		$this->list_updater = new TicketChangeInspector\ListUpdater($this, $this->getFilterDetector());
		return $this->list_updater;
	}


	/**
	 * @return \Application\DeskPRO\Tickets\TicketChangeInspector\SearchUpdater
	 */
	public function getSearchUpdater()
	{
		if ($this->search_updater !== null) return $this->search_updater;

		$this->search_updater = new TicketChangeInspector\SearchUpdater($this->getTicket());
		return $this->search_updater;
	}


	/**
	 * Notify all listeners that changes are about to be committed
	 */
	public function preDone()
	{
		if ($this->ticket->_isRemoved || $this->ticket->_no_log) {
			return;
		}
		if (!$this->ticket['id']) {
			return;
		}
		if (!$this->has_non_ignored) {
			return;
		}
		if (!$this->start_time) {
			$this->logMessage("[TicketChangeTracker] BEGIN TICKET {$this->ticket['id']}");
			$this->start_time = microtime(true);
		}
		$this->getLogInspector()->runPre();
	}


	/**
	 * @return string
	 */
	public function getLogMessagesAsString()
	{
		if (!$this->arr_writer) {
			return '';
		}

		return $this->arr_writer->getMessagesAsString();
	}


	/**
	 * Notify all listeners that changes to the ticket have been committed
	 */
	public function done()
	{
		if (!$this->start_time) {
			$this->logMessage("[TicketChangeTracker] BEGIN TICKET {$this->ticket['id']}");
			$this->start_time = microtime(true);
		}

		$hstatus = $this->getChangedProperty('hidden_status');

		if ($this->ticket->_isRemoved || $this->ticket->_no_log) {
			return;
		}
		if (!$this->ticket['id']) {
			return;
		}
		if ($this->running) {
			$this->logMessage('[TicketChangeTracker] (running)');
			return;
		}
		if (!$this->has_non_ignored && !$this->isExtraSet('always_run_tracker')) {
			$this->logMessage('[TicketChangeTracker] (trivial change set)');
			return;
		}

		$this->running = true;

		$this->ticket->unsetTicketLogger();

		// Bare delete doesnt update any triggers, it just sends CM's.
		// Its used in merging where we want to notify clients that the old ticket was removed from lists,
		// but the actual messages about changing the status to deleted etc arent wanted
		if (!$this->isExtraSet('bare_delete')) {
			$this->getTriggerExecutorInspector();
			if (!$this->isExtraSet('is_install')) {
				$this->getTriggerExecutorInspector()->runPre();
			}

			if (!$this->isExtraSet('is_install')) {
				$this->getTriggerExecutorInspector()->run();
			}

			$this->getLogInspector()->run();

			$person_activity = new \Application\DeskPRO\Tickets\TicketChangeInspector\PersonActivity($this);
			$person_activity->run();

			// Broadcast a change event
			$person_id = 0;
			try {
				if (App::has('session') && App::get('session')->getEntity()->person) {
					$person_id = App::get('session')->getEntity()->person->getId();
				}
			} catch (\Exception $e) {}

			if ($this->isExtraSet('sla')) {
				$person_id = 0;
			}

			App::getDb()->insert('client_messages', array(
				'channel' => 'agent.ticket-updated',
				'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
				'date_created' => date('Y-m-d H:i:s'),
				'data' => serialize(array(
					'ticket_id'      => $this->ticket->getId(),
					'changed_fields' => $this->getAllChangedPropertyNames(),
					'sla_ids'        => $this->ticket->getSlaIds(),
					'via_person'     => $person_id
				)),
				'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
			));
		}

		$this->getListUpdater()->run();

		$time = microtime(true);
		$this->getSearchUpdater()->run();
		$this->logMessage(sprintf("[TicketChangeTracker] Search updater took %.4f seconds", microtime(true)-$time));

		$total_time = sprintf("%.4f", microtime(true) - $this->start_time);
		$this->logMessage("[TicketChangeTracker] END TICKET {$this->ticket['id']} : Took " . $total_time . " seconds");

		$this->_cleanOldWriters();

		if (dp_get_config('enable_changetracker_log')) {
			$log = implode("\n", $this->arr_writer->getMessages());
			if ($log && $this->ticket && $this->ticket->getId() && !$this->ticket->_isRemoved && !$this->isExtraSet('bare_delete')) {
				try {
					App::getDb()->insert('ticket_changetracker_logs', array(
						'ticket_id'    => $this->ticket['id'],
						'log'          => $log,
						'date_created' => date('Y-m-d H:i:s')
					));
				} catch (\Exception $e) {
					\DeskPRO\Kernel\KernelErrorHandler::logException($e);
				}
			}
		}

		$this->running = false;

		if ($hstatus) {
			if ($hstatus['new'] == 'spam') {
				$spamdata = $this->ticket->subject . "\n";

				$msg = App::getDb()->fetchColumn("
					SELECT message
					FROM tickets_messages
					WHERE ticket_id = ?
					ORDER BY id DESC
					LIMIT 1
				", array($this->ticket->id));
				if ($msg) {
					$spamdata .= $msg;
				}

				$spamdata = strip_tags($spamdata);

				App::getDb()->replace('import_datastore', array(
					'typename' => 'dp4_ticketspam_' . $this->ticket->getId(),
					'data' => $spamdata
				));
			} elseif ($hstatus['old'] == 'spam' && $hstatus['new'] != 'spam') {
				App::getDb()->delete('import_datastore', array(
					'typename' => 'dp4_ticketspam_' . $this->ticket->getId()
				));
			}
		}
	}
}
