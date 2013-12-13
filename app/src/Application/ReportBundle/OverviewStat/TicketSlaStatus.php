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

class TicketSlaStatus extends AbstractTableOverviewStat
{
	/**
	 * @var int[]
	 */
	protected $values = null;

	/**
	 * @var int
	 */
	protected $sla_id;

	/**
	 * @var \DateTime
	 */
	protected $date_start;

	/**
	 * @var \DateTime
	 */
	protected $date_end;

	/**
	 * @param int $sla_id
	 * @param \DateTime $date_start
	 * @param \DateTime $date_end
	 */
	public function __construct($sla_id = null, \DateTime $date_start = null, \DateTime $date_end = null)
	{
		$this->sla_id     = $sla_id ? (int)$sla_id : null;
		$this->date_start = $date_start;
		$this->date_end   = $date_end;
	}


	/**
	 * @return string[]
	 */
	public function getTitles()
	{
		return array(
			'ok'       => 'Passed',
			'warning'  => 'Warning',
			'fail'     => 'Failed',
		);
	}


	/**
	 * @return int[]
	 */
	public function getValues()
	{
		if ($this->values !== null) {
			return $this->values;
		}

		$where = array();

		if ($this->sla_id) {
			$where[] = "ticket_slas.sla_id = {$this->sla_id}";
		}
		if ($this->date_start && $this->date_end) {
			$date1 = \Orb\Util\Dates::convertToUtcDateTime($this->date_start);
			$date2 = \Orb\Util\Dates::convertToUtcDateTime($this->date_end);

			$d1 = $date1->format('Y-m-d H:i:s');
			$d2 = $date2->format('Y-m-d H:i:s');

			$where[] = "tickets.date_created BETWEEN '$d1' AND '$d2'";
		}

		if ($where) {
			$where = " WHERE " . implode(' AND ', $where);
		} else {
			$where = '';
		}

		$sql = "
			SELECT ticket_slas.sla_status, COUNT(*) AS count
			FROM ticket_slas
			INNER JOIN tickets ON (ticket_slas.ticket_id = tickets.id)
			INNER JOIN slas ON (ticket_slas.sla_id = slas.id)
			$where
			GROUP BY ticket_slas.sla_status
		";

		$this->logger->logDebug("[TicketSlaStatus] $sql");
		$this->logger->startTimer('TicketSlaStatus');
		$this->values = App::getDb()->fetchAllKeyValue($sql);
		$this->logger->logToatlTime('TicketSlaStatus');

		return $this->values;
	}
}