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

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;

/**
 * Performs groupings for specific tickets only.
 */
class SimpleGroupingCounter extends GroupingCounter
{
	protected $_ticket_ids = array();

	public function __construct(array $ticket_ids, $group_by)
	{
		$this->_ticket_ids = $ticket_ids;
		$this->setGrouping($group_by);
	}

	public function getCounts()
	{
		$group_by = 'GROUP BY field1';

		$select_fields[] = 'tickets.' . $this->grouping1 . ' AS field1';
		if ($this->grouping2) {
			$select_fields[] = 'tickets.' . $this->grouping2 . ' AS field2';
			$group_by .= ', field2';
		}
		$select_fields[] = 'COUNT(*) AS total';

		$params = array();
		$wheres = array(
			'tickets.id IN (' . implode(',', $this->_ticket_ids) . ')'
		);

		$sql = "
			SELECT " . implode(', ', $select_fields) . "
			FROM tickets
			WHERE " . implode(' AND ', $wheres) . "
			$group_by WITH ROLLUP
		";

		$db = App::getDb();

		$counts = $db->fetchAll($sql, $params);

		return $counts;
	}
}
