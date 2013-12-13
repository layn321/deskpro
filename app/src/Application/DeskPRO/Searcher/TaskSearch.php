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
*/

namespace Application\DeskPRO\Searcher;

use Application\DeskPRO\App;

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Arrays;

use Application\DeskPRO\Entity;

class TaskSearch extends SearcherAbstract
{
	const TERM_ID                     = 'id';
	const TERM_PERSON_ID              = 'person_id';
	const TERM_ASSIGNED_AGENT_ID      = 'assigned_agent_id';
	const TERM_ASSIGNED_AGENT_TEAM_ID = 'assigned_agent_team_id';
	const TERM_IS_COMPLETED           = 'is_completed';
	const TERM_TITLE                  = 'title';
	const TERM_VISIBILITY             = 'visibility';
	const TERM_DATE_CREATED           = 'date_created';
	const TERM_DATE_COMPLETED         = 'date_completed';
	const TERM_DATE_DUE               = 'date_due';


	/**
	 * Run the search and return an array of matching ID's.
	 *
	 * @return array
	 */
	public function getMatches()
	{
		$db = App::getDbRead();

		$tasks_ids = $db->fetchAllCol($this->getSql());

		return $tasks_ids;
	}



	/**
	 * Get the SQL query that'll fetch the results
	 * @return string
	 */
	public function getSql()
	{
		$sql = "SELECT tasks.id FROM tasks ";

		$parts = $this->getSqlParts();
		$order_by = $this->getOrderByPart();


		#------------------------------
		# Add joins
		#------------------------------

		foreach ($parts['joins'] as $j) {
			if (is_array($j)) {
				$sql .= $j[1] . " ";
			} else {
				$sql .= "LEFT JOIN $j ON $j.task_id = tasks.id ";
			}
		}

		if (is_array($order_by)) {
			list ($order_join, $order_by) = $order_by;

			$sql .= " $order_join ";
		}

		#------------------------------
		# Add wheres
		#------------------------------

		if ($this->person && $this->person->is_agent) {
			$person_id = App::getDbRead()->quote($this->person->id);

			$this->person->loadHelper('Agent');
			if ($this->person->Agent->getTeamIds()) {
				$where = "((tasks.person_id = $person_id OR tasks.assigned_agent_id = $person_id OR tasks.assigned_agent_team_id IN (" . implode(',', $this->person->Agent->getTeamIds()) . ")) OR tasks.visibility = 1)";
			} else {
				$where = "((tasks.person_id = $person_id OR tasks.assigned_agent_id = $person_id) OR tasks.visibility = 1)";
			}
		} else {
			$where = '1';
		}

		if ($parts['wheres']) {
			$where .= ' AND ' . implode(" AND ", $parts['wheres']);
		}

		$sql .= "WHERE $where";

		$sql .= " GROUP BY tasks.id ";
		$sql .= $order_by;
		$sql .= " LIMIT 1000";

		return $sql;
	}



	/**
	 * Get the ORDER BY clause based on order info set.
	 *
	 * @return string
	 */
	public function getOrderByPart()
	{
		// Set a default if none
		if (!$this->order_by) {
			$this->order_by = array('tasks.id', 'DESC');
		}

		list($type, $dir) = $this->order_by;

		$dir = strtoupper($dir);
		if ($dir != self::ORDER_ASC AND $dir != self::ORDER_DESC) {
			$dir = self::ORDER_DESC;
		}

		$order_by = '';

		switch ($type) {
			case 'tasks.id':
				$order_by = "ORDER BY tasks.id $dir";
				break;

			case 'tasks.title':
				$order_by = "ORDER BY tasks.title $dir";
				break;

			case 'tasks.person':
				$order_by = "ORDER BY tasks.person_id $dir";
				break;

			case 'tasks.assigned_agent':
				$order_by = "ORDER BY tasks.assigned_agent_id $dir";
				break;

			case 'tasks.assigned_agent_team':
				$order_by = "ORDER BY tasks.assigned_agent_team_id $dir";
				break;

			case 'tasks.is_completed':
				$order_by = "ORDER BY tasks.is_completed $dir";
				break;

			case 'tasks.visibility':
				$order_by = "ORDER BY tasks.visibility $dir";
				break;

			case 'tasks.date_created':
				$order_by = "ORDER BY tasks.date_created $dir";
				break;

			case 'tasks.date_completed':
				$order_by = "ORDER BY tasks.date_completed $dir";
				break;

			case 'tasks.date_due':
				$order_by = "ORDER BY tasks.date_due $dir";
				break;
		}

		return $order_by;
	}



	/**
	 * Get the SQL parts we need in the query.
	 *
	 * @return array
	 */
	public function getSqlParts()
	{
		$org_table = 'tasks';

		$db = App::getDbRead();
		$tr = App::getTranslator();

		$wheres = array();
		$joins = array();

		foreach ($this->terms as $info) {
			$join_id = Util::requestUniqueId();
			$join_name = "j_$join_id";

			list($term, $op, $choice) = $info;

			$term_id = null;

			$m = null;
			if (preg_match('#^(.*?)\[(.*?)\]$#', $term, $m)) {
				$term = $m[1];
				$term_id = $m[2];
			}

			switch ($term) {
				case self::TERM_ID:
					$wheres[] = $this->_rangeMatch("$org_table.id", $op, $choice, true);
					$this->summary[] = $this->_rangeSummary($tr->phrase('agent.general.id'), $op, $choice);
					break;

				case self::TERM_TITLE:
					$wheres[] = $this->_stringMatch("tasks.title", $op, $choice);
					break;

				case self::TERM_PERSON_ID:
					$wheres[] = $this->_rangeMatch("tasks.person_id", $op, $choice, true);
					$this->summary[] = $this->_rangeSummary($tr->phrase('agent.general.person_id'), $op, $choice);
					break;

				case self::TERM_ASSIGNED_AGENT_ID:
					$wheres[] = $this->_rangeMatch("tasks.assigned_agent_id", $op, $choice, true);
					$this->summary[] = $this->_rangeSummary($tr->phrase('agent.general.assigned_agent_id'), $op, $choice);
					break;

				case self::TERM_ASSIGNED_AGENT_TEAM_ID:
					$wheres[] = $this->_rangeMatch("tasks.assigned_agent_team_id", $op, $choice, true);
					$this->summary[] = $this->_rangeSummary($tr->phrase('agent.general.assigned_agent_id'), $op, $choice);
					break;

				case self::TERM_DATE_CREATED:
					$this->summary[] = $this->_dateRangeSummary($tr->phrase('agent.general.date_created'), $op, $choice);
					$wheres[] = $this->_dateMatch("tasks.date_created", $op, $choice);
					break;

				case self::TERM_DATE_COMPLETED:
					$this->summary[] = $this->_dateRangeSummary($tr->phrase('agent.general.date_completed'), $op, $choice);
					$wheres[] = $this->_dateMatch("tasks.date_completed", $op, $choice);
					break;

				case self::TERM_DATE_DUE:
					$this->summary[] = $this->_dateRangeSummary($tr->phrase('agent.general.date_due'), $op, $choice);
					$wheres[] = $this->_dateMatch("tasks.date_due", $op, $choice);
					break;

				case self::TERM_IS_COMPLETED:
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}

					if ($choice) {
						$choice = 1;
					} else {
						$choice = 0;
					}

					$wheres[] = $this->_choiceMatch("tasks.is_completed", $op, $choice, false);
					break;

				case self::TERM_VISIBILITY:
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}

					if ($choice) {
						$choice = 1;
					} else {
						$choice = 0;
					}

					$wheres[] = $this->_choiceMatch("tasks.visibility", $op, $choice, false);
					break;
			}
		}

		$joins = array_unique($joins);

		return array(
			'joins' => $joins,
			'wheres' => $wheres
		);
	}


}
