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
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Person;

class TicketsOpenedHour extends AbstractTableOverviewStat implements PersonContextInterface
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

	/**
	 * @var string
	 */
	protected $date_group;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	public function __construct($date_group, \DateTime $date_start, \DateTime $date_end)
	{
		$this->date_group = $date_group;
		$this->date_start = $date_start;
		$this->date_end   = $date_end;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}


	/**
	 * @return string[]
	 */
	public function getTitles()
	{
		switch ($this->date_group) {
			case 'hour':
				$titles = array_combine(range(1, 23), range(1,23));
				$titles['0'] = 0;

				foreach ($titles as &$x) {
					if ($x == 0) {
						$x = '12am';
					} elseif ($x == 12) {
						$x = '12pm';
					} elseif ($x < 12) {
						$x = $x . 'am';
					} else {
						$x = ($x-12) . 'pm';
					}
				}

				break;

			case 'weekday':
				// Sunday is start of week in MySQL, hence weird indexes
				$titles = array(
					0 => 'Monday',
					1 => 'Tuesday',
					2 => 'Wednesday',
					3 => 'Thursday',
					4 => 'Friday',
					5 => 'Saturday',
					6 => 'Sunday',
				);
				break;

			case 'day':
				$days = \Orb\Util\Dates::daysInMonth($this->date_start->format('n'), $this->date_start->format('Y'));
				$titles = array();
				foreach (range(1,$days) as $d) {
					$titles[$d] = $d . \Orb\Util\Numbers::ordinalSuffix($d);
				}

				break;

			case 'month':
				$titles = array(
					1  => 'Jan',
					2  => 'Feb',
					3  => 'Mar',
					4  => 'Apr',
					5  => 'May',
					6  => 'Jun',
					7  => 'Jul',
					8  => 'Aug',
					9  => 'Sep',
					10 => 'Oct',
					11 => 'Nov',
					12 => 'Dec',
				);
				break;

			default:
				throw new \InvalidArgumentException("Unknown date group: {$this->date_group}");
		}
		return $titles;
	}


	/**
	 * @return int[]
	 */
	public function getValues()
	{
		if ($this->values !== null) {
			return $this->values;
		}

		// Convert input datetime which has timezone data, into UTC for db range
		$date1 = \Orb\Util\Dates::convertToUtcDateTime($this->date_start);
		$date2 = \Orb\Util\Dates::convertToUtcDateTime($this->date_end);

		$d1 = $date1->format('Y-m-d H:i:s');
		$d2 = $date2->format('Y-m-d H:i:s');

		// Get offset of original date from UTC, we need for mysql
		$offset = 0;
		if ($this->person_context) {
			$offset = $this->person_context->getTimezoneOffsetSeconds();
		}

		switch ($this->date_group) {
			case 'hour':
				$date_group = "HOUR(DATE_ADD(tickets.date_created, INTERVAL $offset SECOND))";
				break;

			case 'weekday':
				$date_group = "WEEKDAY(DATE_ADD(tickets.date_created, INTERVAL $offset SECOND))";
				break;

			case 'day':
				$date_group = "DAYOFMONTH(DATE_ADD(tickets.date_created, INTERVAL $offset SECOND))";
				break;

			case 'month':
				$date_group = "MONTH(DATE_ADD(tickets.date_created, INTERVAL $offset SECOND))";
				break;

			default:
				throw new \InvalidArgumentException("Unknown date group: {$this->date_group}");
		}

		$sql = "
			SELECT $date_group AS date_group, COUNT(*)
			FROM tickets
			WHERE tickets.status != 'hidden' AND tickets.date_created BETWEEN '$d1' AND '$d2'
			GROUP BY date_group
		";

		$this->logger->logDebug("[TicketsOpenedHour] $sql");
		$this->logger->startTimer('TicketsOpenedHour');
		$this->values = App::getDb()->fetchAllKeyValue($sql);
		$this->logger->logToatlTime('TicketsOpenedHour');

		return $this->values;
	}
}