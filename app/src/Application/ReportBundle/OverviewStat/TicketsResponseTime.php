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
 * @subpackage
 */

namespace Application\ReportBundle\OverviewStat;

use Application\DeskPRO\App;

class TicketsResponseTime extends AbstractSubgroupedTableOverviewStat
{
	/**
	 * @var \DateTime
	 */
	protected $date_start;

	/**
	 * @var \DateTime
	 */
	protected $date_end;

	/**
	 * @var int[]
	 */
	protected $values = null;

	/**
	 * @var array
	 */
	protected $titles = null;

	public function __construct(GroupingField $grouping_field = null, \DateTime $date_start, \DateTime $date_end)
	{
		$this->grouping_field = $grouping_field;
		$this->date_start     = \Orb\Util\Dates::convertToUtcDateTime($date_start);
		$this->date_end       = \Orb\Util\Dates::convertToUtcDateTime($date_end);
	}


	/**
	 * @return string[]
	 */
	public function getTitles()
	{
		$largest = 0;
		foreach ($this->getValues() as $time => $x) {
			if ($time > $largest) {
				$largest = $time;
			}
		}

		$titles = array();

		foreach (TimeTitles::$time_phrases as $time => $phrase) {
			if ($time > $largest) {
				break;
			}

			$titles[$time] = $phrase;
		}

		return $titles;
	}


	/**
	 * @return string[]
	 */
	public function getSubgroupTitles()
	{
		if (!$this->grouping_field) {
			return null;
		}

		$collect = array();
		foreach ($this->getValues() as $time_group => $sub_groups) {
			foreach ($sub_groups as $group_id => $count) {
				$collect[$group_id] = $group_id;
			}
		}

		return $this->grouping_field->getTitles($collect);
	}


	/**
	 * @return int[]
	 */
	public function getValues()
	{
		if ($this->values !== null) {
			return $this->values;
		}

		$d1 = $this->date_start->format('Y-m-d H:i:s');
		$d2 = $this->date_end->format('Y-m-d H:i:s');

		$field = TimeTitles::makeTimeFieldSelect('tickets.total_to_first_reply');

		if ($this->grouping_field) {
			$group_field = $this->grouping_field->getFieldInfo();
			$sql = "
				SELECT {$group_field['select']}, $field, COUNT(*)
				FROM tickets
				{$group_field['join']}
				WHERE tickets.status != 'hidden' AND tickets.date_created BETWEEN '$d1' AND '$d2' AND tickets.total_to_first_reply != 0 {$group_field['where']}
				GROUP BY {$group_field['group_by']}, time_group
				ORDER BY time_group ASC
			";

			$this->logger->logDebug("[TicketsResponseTime (Grouped)] $sql");
			$this->logger->startTimer('TicketsResponseTime');
			$q = App::getDb()->executeQuery($sql);
			$this->logger->logToatlTime('TicketsResponseTime');

			$this->logger->startTimer('TicketsResponseTime.collecting');

			$this->values = array();
			while ($row = $q->fetch(\PDO::FETCH_NUM)) {
				$group_id = $row[0];
				$time_group = $row[1];
				$count = $row[2];
				if (!isset($this->values[$time_group])) {
					$this->values[$time_group] = array();
				}

				if (!isset($this->values[$time_group][$group_id])) {
					$this->values[$time_group][$group_id] = 0;
				}

				$this->values[$time_group][$group_id] += $count;
			}

			$this->logger->logToatlTime('TicketsResponseTime.collecting');
		} else {
			$sql = "
				SELECT $field, COUNT(*)
				FROM tickets
				WHERE tickets.status != 'hidden' AND tickets.date_created BETWEEN '$d1' AND '$d2' AND tickets.total_to_first_reply != 0
				GROUP BY time_group
			";

			$this->logger->logDebug("[TicketsResponseTime] $sql");
			$this->logger->startTimer('TicketsResponseTime');
			$this->values = App::getDb()->fetchAllKeyValue($sql);
			$this->logger->logToatlTime('TicketsResponseTime');
		}

		return $this->values;
	}
}