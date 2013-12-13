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
use Application\DeskPRO\Entity\PageViewLog;

class KbViewsHour extends AbstractTableOverviewStat
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

	public function __construct(\DateTime $date_start, \DateTime $date_end)
	{
		$this->date_start = $date_start;
		$this->date_end   = $date_end;
	}


	/**
	 * @return string[]
	 */
	public function getTitles()
	{
		$titles = array_combine(range(1, 23), range(1,23));
		$titles['0'] = '0';

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
		$offset = $date1->getTimestamp() - $this->date_start->getTimestamp();

		$type = PageViewLog::TYPE_ARTICLE;
		$sql = "
			SELECT HOUR(DATE_SUB(page_view_log.date_created, INTERVAL $offset SECOND)) AS hour, COUNT(*)
			FROM page_view_log
			WHERE page_view_log.object_type = $type AND page_view_log.date_created BETWEEN '$d1' AND '$d2'
			GROUP BY hour
		";

		$this->logger->logDebug("[KbViewsHour] $sql");
		$this->logger->startTimer('KbViewsHour');
		$this->values = App::getDb()->fetchAllKeyValue($sql);
		$this->logger->logToatlTime('KbViewsHour');

		return $this->values;
	}
}