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
 * @category Entities
 */

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\App;

/**
 * Entity for an ticket SLA record
 *
 */
class TicketSla extends \Application\DeskPRO\Domain\DomainObject
{
	const STATUS_OK = 'ok';
	const STATUS_WARNING = 'warning';
	const STATUS_FAIL = 'fail';

	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $sla_status = 'ok';

	/**
	 * @var \DateTime|null
	 */
	protected $warn_date;

	/**
	 * @var \DateTime|null
	 */
	protected $fail_date;

	/**
	 * @var bool
	 */
	protected $is_completed = false;

	/**
	 * @var bool
	 */
	protected $is_completed_set = false;

	/**
	 * @var null|integer
	 */
	protected $completed_time_taken = null;

	/**
	 * @var Ticket
	 */
	protected $ticket;

	/**
	 * @var Sla
	 */
	protected $sla;

	protected $_original_status = null;
	protected $_original_is_completed = null;

	protected $_ticket_log = null;

	public function evaluateSlaDates($call_triggers = true)
	{
		if ($this->is_completed) {
			return;
		}

		$time = $this->sla->getSlaTestTime($this->ticket);

		if ($this->sla_status == self::STATUS_OK && $this->warn_date && $this->warn_date->getTimestamp() < $time) {
			$this->setSlaStatus(self::STATUS_WARNING, $call_triggers);
		}

		if (in_array($this->sla_status, array(self::STATUS_OK, self::STATUS_WARNING)) && $this->fail_date && $this->fail_date->getTimestamp() < $time) {
			$this->setSlaStatus(self::STATUS_FAIL, $call_triggers);
		}
	}

	public function setSlaStatus($status, $call_triggers = true)
	{
		if ($this->_original_status === null) {
			$this->_original_status = $this->sla_status;
		}

		$current_status = $this->sla_status;

		$this->setModelField('sla_status', $status);

		if ($this->id) {
			// this works around an issue where the ticket can't be saved
			$this->ticket->addPropertyChangedListener(App::getOrm()->getUnitOfWork());
		}

		$this->ticket->updateWorstSlaStatus();

		if ($call_triggers) {
			if ($current_status == self::STATUS_OK && in_array($status, array(self::STATUS_WARNING, self::STATUS_FAIL))) {
				$this->_runTrigger($this->sla->warning_trigger, 'warning', $this->warn_date);
			}

			if (in_array($current_status, array(self::STATUS_OK, self::STATUS_WARNING)) && $status == self::STATUS_FAIL) {
				$this->_runTrigger($this->sla->fail_trigger, 'fail', $this->fail_date);
			}
		}

		if ($this->id) {
			// if we call this when inserting, end up with a double insert
			App::getOrm()->persist($this->ticket);
		}
	}

	public function setIsCompleted($value, $date = null)
	{
		$value = (bool)$value;

		if ($this->_original_is_completed === null) {
			$this->_original_is_completed = $this->is_completed;
		}

		$this->setModelField('is_completed', $value);
		if ($this->is_completed) {
			if ($this->sla_status == self::STATUS_OK) {
				$this->setModelField('warn_date', null);
				$this->setModelField('fail_date', null);
			} else if ($this->sla_status == self::STATUS_WARNING) {
				$this->setModelField('fail_date', null);
			}

			if ($date === null) {
				$date = time();
			}
			if ($date) {
				$this->setModelField('completed_time_taken',
					$this->sla->calculateSlaTimeUntil($date, $this->ticket)
				);
			} else {
				$this->setModelField('completed_time_taken', null);
			}
		} else {
			$this->setModelField('completed_time_taken', null);
		}
	}

	/**
	 * Same as setIsCompleted but the completed status is set forever (unless its overriden with a trigger etc).
	 * Usually when status changes, the SLA is re-calculated.
	 *
	 * @param $value
	 * @param null $date
	 */
	public function setIsCompletedSet($value, $date = null)
	{
		$this->setIsCompleted($value, $date);
		if ($value) {
			$this->setModelField('is_completed_set', true);
		} else {
			$this->setModelField('is_completed_set', false);
		}
	}

	public function getNextTriggerDate()
	{
		$times = array();

		if ($this->sla_status == self::STATUS_OK && $this->warn_date && $this->warn_date->getTimestamp() > time()) {
			$times[] = $this->warn_date->getTimestamp();
		}

		if (in_array($this->sla_status, array(self::STATUS_OK, self::STATUS_WARNING)) && $this->fail_date && $this->fail_date->getTimestamp() > time()) {
			$times[] = $this->fail_date->getTimestamp();
		}

		if (!$times) {
			return null;
		}

		return new \DateTime('@' . min($times));
	}

	public function getOriginalStatus()
	{
		return $this->_original_status === null ? $this->sla_status : $this->_original_status;
	}

	public function getOriginalIsCompleted()
	{
		return $this->_original_is_completed === null ? $this->is_completed : $this->_original_is_completed;
	}

	public function calculateSlaDates($call_triggers = true)
	{
		if (!$this->is_completed) {
			$this->setModelField('warn_date', $this->sla->calculateWarnDate($this->ticket));
			$this->setModelField('fail_date', $this->sla->calculateFailDate($this->ticket));
			$this->evaluateSlaDates($call_triggers);

			$completed_ts = $this->sla->calculateCompleted($this->ticket);
			if ($completed_ts) {
				$this->setIsCompleted(true, $completed_ts);
			} else {
				$this->setIsCompleted(false);
			}
		}
	}

	public function changedLoggable()
	{
		return ($this->sla_status != $this->getOriginalStatus() || $this->is_completed != $this->getOriginalIsCompleted());
	}

	protected function _runTrigger(TicketTrigger $trigger = null, $status, \DateTime $date = null)
	{
		if (!$trigger) {
			return;
		}

		$tracker = $this->ticket->getTicketLogger();
		if ($tracker) {
			$tracker->recordExtraMulti('trigger', $trigger);
			$tracker->recordExtra('sla', $this->sla);
			$tracker->recordExtra('sla_status', $status);
		}

		if ($this->ticket->id) {
			$trigger_log = array(
				'ticket_id'     => $this->ticket->id,
				'trigger_id'    => $trigger->id,
				'date_ran'      => date('Y-m-d H:i:s'),
				'date_criteria' => ($date ? $date->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'))
			);
			App::getDb()->insert('ticket_trigger_logs', $trigger_log);
		}

		$factory = new \Application\DeskPRO\Tickets\TicketActions\ActionsFactory();
		if ($tracker) {
			$factory->addGlobalOption('tracker', $tracker);
		}
		$factory->addGlobalOption('ticket', $this->ticket);

		$actions_collection = new \Application\DeskPRO\Tickets\TicketActions\ActionsCollection();
		foreach ($trigger->actions as $action_info) {
			$action = $factory->createFromInfo($action_info);
			if ($action) {
				if ($action instanceof \Application\DeskPRO\Tickets\TicketActions\ExecutionContextAware) {
					$action->setExecutionContext('trigger');
				}

				$actions_collection->add($action, array(
					'trigger' => $trigger,
					'sla' => $this->sla,
					'sla_status' => $status,
					'original_status' => $this->getOriginalStatus()
				));
			}
		}

		try {
			$actions_collection->apply($this->ticket->getTicketLogger(), $this->ticket, null);
		} catch (\Exception $e) {
			// Log the error but continue with execution
			$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
			\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);
		}
	}

	public function _sendClientMessages($removed = false)
	{
		$person_id = 0;
		try {
			if (App::has('session') && App::get('session')->getEntity()->person) {
				$person_id = App::get('session')->getEntity()->person->getId();
			}
		} catch (\Exception $e) {}

		App::getDb()->insert('client_messages', array(
			'channel' => 'agent.ticket-sla-updated',
			'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
			'date_created' => date('Y-m-d H:i:s'),
			'data' => serialize(array(
				'ticket_id'      => $this->ticket->getId(),
				'ticket_agent_id' => $this->ticket->agent ? $this->ticket->agent->id : null,
				'ticket_agent_team_id' => $this->ticket->agent_team ? $this->ticket->agent_team->id : null,
				'sla_id'         => $this->sla->id,
				'sla_status'     => $this->sla_status,
				'original_status' => $this->getOriginalStatus(),
				'warn_date'      => $this->warn_date ? $this->warn_date->format('c') : null,
				'fail_date'      => $this->fail_date ? $this->fail_date->format('c') : null,
				'is_completed'   => $this->is_completed,
				'original_is_completed' => $this->getOriginalIsCompleted(),
				'removed'        => $removed,
				'via_person'     => $person_id
			)),
			'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
		));
	}

	public function _preInsert()
	{
		$this->calculateSlaDates();
	}

	public function _postInsert()
	{
		if (!$this->ticket || $this->ticket->isLoggingDisabled()) {
			return;
		}

		$this->_sendClientMessages();

		$person = App::getCurrentPerson();
		if ($person && $person->id && !$person->is_agent) {
			// only agents are the ones to manually apply an SLA
			$person = null;
		}
		$action = new \Application\DeskPRO\Tickets\TicketChangeInspector\LogActions\TicketSlaAdded($this);

		$ticket_log = new TicketLog();
		$ticket_log['person'] = ($person && $person->id) ? $person : null;
		$ticket_log['ticket'] = $this->ticket;
		$ticket_log['action_type'] = $action->getLogName();
		$ticket_log['details'] = $action->getLogDetails();

		if ($ticket_log['details']) {
			if (!$this->ticket->inserted_log_row_batch) {
				$this->ticket->inserted_log_row_batch = array();
			}
			$this->ticket->inserted_log_row_batch[] = $ticket_log;
		}
	}

	public function _postUpdate()
	{
		if (!$this->ticket || $this->ticket->isLoggingDisabled()) {
			return;
		}

		$this->_sendClientMessages();

		$action = new \Application\DeskPRO\Tickets\TicketChangeInspector\LogActions\TicketSlaUpdated($this);

		$ticket_log = new TicketLog();
		$ticket_log['person'] = null; // SLA updates are always done by the system
		$ticket_log['ticket'] = $this->ticket;
		$ticket_log['action_type'] = $action->getLogName();
		$ticket_log['details'] = $action->getLogDetails();

		if ($ticket_log['details']) {
			if (!$this->ticket->inserted_log_row_batch) {
				$this->ticket->inserted_log_row_batch = array();
			}
			$this->ticket->inserted_log_row_batch[] = $ticket_log;
		}
	}

	public function _postRemove()
	{
		if (!$this->ticket || $this->ticket->isLoggingDisabled()) {
			return;
		}

		$this->_sendClientMessages(true);

		$person = App::getCurrentPerson();
		$action = new \Application\DeskPRO\Tickets\TicketChangeInspector\LogActions\TicketSlaRemoved($this);

		$ticket_log = new TicketLog();
		$ticket_log['person'] = ($person && $person->id) ? $person : null;
		$ticket_log['ticket'] = $this->ticket;
		$ticket_log['action_type'] = $action->getLogName();
		$ticket_log['details'] = $action->getLogDetails();

		if ($ticket_log['details']) {
			$orm = App::getOrm();
			if (method_exists($orm, 'delayedInsert')) {
				App::getOrm()->delayedInsert($ticket_log);
			}
		}
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketSla';
		$metadata->setPrimaryTable(array(
			'name' => 'ticket_slas',
			'indexes' => array(
				'status_completed_warn_date_idx' => array('columns' => array('sla_status', 'is_completed', 'warn_date')),
				'status_completed_fail_date_idx' => array('columns' => array('sla_status', 'is_completed', 'fail_date')),
			),
		));
		$metadata->addLifecycleCallback('_preInsert', 'prePersist');
		$metadata->addLifecycleCallback('_postInsert', 'postPersist');
		$metadata->addLifecycleCallback('_postUpdate', 'postUpdate');
		$metadata->addLifecycleCallback('_postRemove', 'postRemove');
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'sla_status', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'sla_status', ));
		$metadata->mapField(array( 'fieldName' => 'warn_date', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'warn_date', ));
		$metadata->mapField(array( 'fieldName' => 'fail_date', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'fail_date', ));
		$metadata->mapField(array( 'fieldName' => 'is_completed', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_completed', ));
		$metadata->mapField(array( 'fieldName' => 'is_completed_set', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_completed_set', ));
		$metadata->mapField(array( 'fieldName' => 'completed_time_taken', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'completed_time_taken', ));

		$metadata->mapManyToOne(array( 'fieldName' => 'ticket', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Ticket', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'ticket_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'sla', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Sla', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'sla_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true  ));

		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
