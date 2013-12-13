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
 * Entity for an SLA record
 *
 */
class Sla extends \Application\DeskPRO\Domain\DomainObject
{
	const TYPE_FIRST_RESPONSE = 'first_response';
	const TYPE_RESOLUTION = 'resolution';
	const TYPE_WAITING_TIME = 'waiting_time';

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
	protected $title;

	/**
	 * Type of SLA - first_response, resolution, waiting_time
	 *
	 * @var string
	 */
	protected $sla_type;

	/**
	 * Whether active all the time (all) or during work hours only (work_hours)
	 * or use the default ticket-wide settings (default)
	 *
	 * @var string
	 */
	protected $active_time = 'default';

	/**
	 * When the work day starts. This is stored as the number of seconds after 00:00:00.
	 *
	 * @var integer
	 */
	protected $work_start;

	/**
	 * When the work day ends. This is stored as the number of seconds after 00:00:00.
	 *
	 * @var integer
	 */
	protected $work_end;

	/**
	 * Array of work days, stored with keys corresponding to day numbers. Values are true.
	 * 0 = Sunday, 6 = Saturday (same as PHP, easy to convert to MySQL which is 1 = Sunday, 7 = Saturday)
	 *
	 * @var array
	 */
	protected $work_days = array();

	/**
	 * Timezone for work hours/days to be considered in
	 *
	 * @var string
	 */
	protected $work_timezone;

	/**
	 * List of work holidays
	 *
	 * @var array
	 */
	protected $work_holidays = array();

	/**
	 * Controls how the SLA is applied to tickets
	 *
	 * @var string
	 */
	protected $apply_type = 'all';

	/**
	 * @var TicketTrigger
	 */
	protected $warning_trigger;

	/**
	 * @var TicketTrigger
	 */
	protected $fail_trigger;

	/**
	 * @var TicketPriority
	 */
	protected $apply_priority;

	/**
	 * @var TicketTrigger
	 */
	protected $apply_trigger;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $people;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $organizations;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $ticket_slas;

	/**
	 * Creates a new team.
	 */
	public function __construct()
	{
		$this->people = new \Doctrine\Common\Collections\ArrayCollection();
		$this->organizations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->ticket_slas = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function getWorkStartHour()
	{
		return floor($this->work_start / 3600);
	}

	public function getWorkStartMinute()
	{
		return floor(($this->work_start % 3600) / 60);
	}

	public function getWorkEndHour()
	{
		return floor($this->work_end / 3600);
	}

	public function getWorkEndMinute()
	{
		return floor(($this->work_end % 3600) / 60);
	}

	public function setWorkDays(array $days, $raw = false)
	{
		$old = $this->work_days;

		if ($raw) {
			$this->work_days = $days;
		} else {
			$days = array_unique($days);
			sort($days);

			$this->work_days = array_fill_keys($days, true);
		}
		$this->_onPropertyChanged('work_days', $old, $this->work_days);
	}

	public function resetHolidays()
	{
		$this->setModelField('work_holidays', array());
	}

	public function removeHolidayKey($key)
	{
		$old = $this->work_holidays;
		unset($this->work_holidays[$key]);
		$this->_onPropertyChanged('work_holidays', $old, $this->work_holidays);
	}

	public function addHoliday($name, $day, $month, $year = null)
	{
		$old = $this->work_holidays;

		if (!$year) {
			$year = null;
		} else {
			$year = intval($year);
		}

		foreach ($this->work_holidays AS $k => $existing) {
			if ($existing['day'] == $day && $existing['month'] == $month && $existing['year'] === $year) {
				return $k;
			}
		}

		$this->work_holidays[] = array(
			'name' => $name,
			'day' => intval($day),
			'month' => intval($month),
			'year' => $year
		);

		$this->_onPropertyChanged('work_holidays', $old, $this->work_holidays);

		return count($this->work_holidays) - 1;
	}

	public function getHolidaysSorted()
	{
		$holidays = $this->work_holidays;
		uasort($holidays, function($a, $b) {
			if ($a['month'] < $b['month']) {
				return -1;
			}
			if ($a['month'] > $b['month']) {
				return 1;
			}
			if ($a['day'] < $b['day']) {
				return -1;
			}
			if ($a['day'] > $b['day']) {
				return 1;
			}

			return 0; // same month and day
		});

		return $holidays;
	}

	public function getWarningTimeText()
	{
		return $this->_getTriggerTimeText($this->warning_trigger);
	}

	public function getFailTimeText()
	{
		return $this->_getTriggerTimeText($this->fail_trigger);
	}

	protected function _getTriggerTimeText($trigger) {
		if (!$trigger) {
			return '';
		}

		$length = $trigger->getOptionTime();
		$scale = $trigger->getOptionScale();

		$translator = App::getTranslator();

		switch ($scale) {
			case 'minutes': return $translator->phrase('admin.general.time_x_minute', array('count' => $length));
			case 'hours': return $translator->phrase('admin.general.time_x_hour', array('count' => $length));
			case 'days': return $translator->phrase('admin.general.time_x_day', array('count' => $length));
			case 'weeks': return $translator->phrase('admin.general.time_x_week', array('count' => $length));
			case 'months': return $translator->phrase('admin.general.time_x_month', array('count' => $length));
			default: return '';
		}
	}

	public function setPeople($people)
	{
		$have_ids = array();
		foreach ($people AS $person) {
			$this->addPerson($person);
			$have_ids[] = $person->id;
		}

		foreach ($this->people AS $k => $person) {
			if (!in_array($person->id, $have_ids)) {
				$this->people->remove($k);
			}
		}
	}

	public function removePerson(Person $person)
	{
		$this->people->removeElement($person);
	}

	public function addPerson(Person $person)
	{
		if (!$this->people->contains($person)) {
			$this->people->add($person);
		}
	}

	public function setOrganizations($organizations)
	{
		$have_ids = array();
		foreach ($organizations AS $organization) {
			$this->addOrganization($organization);
			$have_ids[] = $organization->id;
		}

		foreach ($this->organizations AS $k => $organization) {
			if (!in_array($organization->id, $have_ids)) {
				$this->organizations->remove($k);
			}
		}
	}

	public function removeOrganization(Organization $organization)
	{
		$this->organizations->removeElement($organization);
	}

	public function addOrganization(Organization $organization)
	{
		if (!$this->organizations->contains($organization)) {
			$this->organizations->add($organization);
		}
	}

	public function appliesToPerson(Person $person)
	{
		return App::getEntityRepository('DeskPRO:Sla')->doesSlaApplyToPerson($this, $person);
	}

	public function appliesToOrganization(Organization $organization)
	{
		return App::getEntityRepository('DeskPRO:Sla')->doesSlaApplyToOrganization($this, $organization);
	}

	public function calculateWarnDate(Ticket $ticket)
	{
		if (!$this->warning_trigger) {
			return null;
		}

		$hours_set = $this->getWorkHoursSet();
		return $this->_calculateTriggerDate($this->warning_trigger->getOptionSeconds($hours_set), $ticket);
	}

	public function calculateFailDate(Ticket $ticket)
	{
		if (!$this->warning_trigger) {
			return null;
		}

		if (!$this->fail_trigger) {
			return null;
		}

		$hours_set = $this->getWorkHoursSet();
		return $this->_calculateTriggerDate($this->fail_trigger->getOptionSeconds($hours_set), $ticket);
	}

	protected $_work_hours_set;

	/**
	 * @return \Orb\Util\WorkHoursSet
	 */
	public function getWorkHoursSet()
	{
		if (!$this->_work_hours_set) {
			if ($this->active_time == 'default') {
				$work_hours = unserialize(App::getSetting('core_tickets.work_hours'));
				$this->_work_hours_set = new \Orb\Util\WorkHoursSet(
					$work_hours['active_time'], $work_hours['start_hour'] * 3600 + $work_hours['start_minute'] * 60,
					$work_hours['end_hour'] * 3600 + $work_hours['end_minute'] * 60,
					$work_hours['days'], $work_hours['timezone'], $work_hours['holidays']
				);
			} else {
				$this->_work_hours_set = new \Orb\Util\WorkHoursSet(
					$this->active_time, $this->work_start, $this->work_end,
					$this->work_days, $this->work_timezone, $this->work_holidays
				);
			}
		}

		return $this->_work_hours_set;
	}

	public function calculateSlaTimeUntil($end_ts, Ticket $ticket)
	{
		if ($this->sla_type == self::TYPE_WAITING_TIME) {
			$time = 0;
			$work_hours_set = $this->getWorkHoursSet();
			foreach ($ticket->waiting_times AS $waiting) {
				if ($waiting['type'] == 'user' && $waiting['start'] < $end_ts) {
					$time += $work_hours_set->getWorkTimeBetween($waiting['start'], min($end_ts, $waiting['end']));
				}
			}

			return $time;
		} else {
			return $this->getWorkHoursSet()->getWorkTimeBetween($ticket->date_created, $end_ts);
		}
	}

	protected function _calculateTriggerDate($delay, Ticket $ticket)
	{
		if ($this->sla_type == self::TYPE_FIRST_RESPONSE || $this->sla_type == self::TYPE_RESOLUTION) {
			return $this->getWorkHoursSet()->calculateWorkHoursDelay($ticket->date_created, $delay);
		}

		if ($this->sla_type == self::TYPE_WAITING_TIME) {
			if ($ticket->status != 'awaiting_agent') {
				// can't know when it will expire
				return null;
			}

			$work_hours_set = $this->getWorkHoursSet();

			if ($work_hours_set->getActiveTime() == \Orb\Util\WorkHoursSet::ACTIVE_24X7) {
				$wait_time = $ticket->total_user_waiting;
				if ($ticket->date_user_waiting) {
					$wait_time += time() - $ticket->date_user_waiting->getTimestamp();
				}

				return new \DateTime('+' . ($delay - $wait_time) . ' seconds', new \DateTimeZone('UTC'));
			} else {
				$work_day_length = $this->work_end - $this->work_start;
				if ($work_day_length <= 0) {
					return null;
				}

				$wait_time = 0;
				if ($ticket->waiting_times) {
					foreach ($ticket->waiting_times AS $waiting) {
						if ($waiting['type'] == 'user') {
							$wait_time += $work_hours_set->getWorkTimeBetween($waiting['start'], $waiting['end']);
						}
					}
				}

				if ($ticket->date_user_waiting && $ticket->status == 'awaiting_agent') {
					// ticket is waiting but we don't have an end so add that
					$wait_time += $work_hours_set->getWorkTimeBetween($ticket->date_user_waiting);
				}

				return $work_hours_set->calculateWorkHoursDelay(new \DateTime(), $delay - $wait_time);
			}
		}

		return null;
	}

	public function calculateCompleted(Ticket $ticket)
	{
		$dates = array();

		if ($ticket->status == 'resolved') {
			if ($ticket->date_resolved) {
				$dates[] = $ticket->date_resolved->getTimestamp();
			} else {
				$dates[] = time();
			}
		}

		if ($ticket->status == 'hidden' && ($ticket->hidden_status == 'spam' || $ticket->hidden_status == 'deleted')) {
			$dates[] = time();
		}

		if ($ticket->date_closed) {
			$dates[] = $ticket->date_closed->getTimestamp();
		}

		if ($this->sla_type == self::TYPE_FIRST_RESPONSE && $ticket->date_last_agent_reply) {
			if ($ticket->date_last_agent_reply->getTimestamp() > $ticket->date_created->getTimestamp()) {
				// don't auto resolve sla on ticket creation, even if created by an agent
				$dates[] = $ticket->date_first_agent_reply->getTimestamp();
			}
		}

		if ($this->sla_type == self::TYPE_FIRST_RESPONSE && $ticket->date_status && $ticket->date_status->getTimestamp() > $ticket->date_created->getTimestamp() && $ticket->status != 'awaiting_agent') {
			$dates[] = $ticket->date_status->getTimestamp();
		}

		if ($dates) {
			return min($dates);
		}

		return null;
	}

	public function getSlaTestTime(Ticket $ticket)
	{
		$times = array(time());

		if ($this->sla_type == self::TYPE_FIRST_RESPONSE && $ticket->date_last_agent_reply) {
			if ($ticket->date_last_agent_reply->getTimestamp() > $ticket->date_created->getTimestamp()) {
				// don't auto resolve sla on ticket creation, even if created by an agent
				$times[] = $ticket->date_first_agent_reply->getTimestamp();
			}
		}

		if ($ticket->date_closed) {
			$times[] = $ticket->date_closed->getTimestamp();
		}

		if ($ticket->status == 'resolved' && $ticket->date_resolved) {
			$times[] = $ticket->date_resolved->getTimestamp();
		}

		return min($times);
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Sla';
		$metadata->setPrimaryTable(array(
			'name' => 'slas'
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'sla_type', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'sla_type', ));
		$metadata->mapField(array( 'fieldName' => 'active_time', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'active_time', ));
		$metadata->mapField(array( 'fieldName' => 'work_start', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'work_start', ));
		$metadata->mapField(array( 'fieldName' => 'work_end', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'work_end', ));
		$metadata->mapField(array( 'fieldName' => 'work_days', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'work_days', ));
		$metadata->mapField(array( 'fieldName' => 'work_timezone', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'work_timezone', ));
		$metadata->mapField(array( 'fieldName' => 'work_holidays', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'work_holidays', ));
		$metadata->mapField(array( 'fieldName' => 'apply_type', 'type' => 'string', 'length' => 25, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'apply_type', ));

		$metadata->mapManyToOne(array( 'fieldName' => 'warning_trigger', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketTrigger', 'cascade' => array('remove'), 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'warning_trigger_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'fail_trigger', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketTrigger', 'cascade' => array('remove'), 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'fail_trigger_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'apply_priority', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketPriority', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'apply_priority_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'apply_trigger', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketTrigger', 'cascade' => array('remove'), 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'apply_trigger_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));

		$metadata->mapOneToMany(array( 'fieldName' => 'ticket_slas', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketSla', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'sla', 'orphanRemoval' => true ));

		$metadata->mapManyToMany(array( 'fieldName' => 'people', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'joinTable' => array( 'name' => 'sla_people', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'sla_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), 'orderBy' => array( 'name' => 'ASC', ), ));
		$metadata->mapManyToMany(array( 'fieldName' => 'organizations', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Organization', 'joinTable' => array( 'name' => 'sla_organizations', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'sla_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'organization_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), 'orderBy' => array( 'name' => 'ASC', ), ));

		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
