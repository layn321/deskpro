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

namespace Application\DeskPRO\Chat\UserChat;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Searcher\ChatConversationSearch;
use Orb\Util\Arrays;

class GroupingCounter
{
	const LAST_TIME_MARKER   = 1893456000;

	protected $group_by;
	protected $searcher;
	protected $groups = array(
		'none' => '',
		'department' => 'department_id',
		'agent' => 'agent_id',
		'date_created' => 'date_created',
		'total_to_ended' => 'total_to_ended'
	);

	public function __construct($group_by)
	{
		$this->group_by = $this->groups[$group_by];
	}

	public function getCounts(ChatConversationSearch $searcher)
	{
		if(empty($this->group_by)) {
			return array();
		}

		$searcher->setGroupBy($this->group_by);
		$db = App::getDb();

		switch($this->group_by) {
			case 'agent_id':
				$searcher->addJoin('people ON agent_id = people.id');
				$searcher->setColumns('agent_id AS id, COALESCE(people.name, "Unassigned") AS title, COUNT(*) AS count');
				$searcher->setOrderBy('people.name');
				break;
			case 'department_id':
				$searcher->addJoin('departments ON department_id = departments.id');
				$searcher->setColumns('department_id AS id, COUNT(*) AS count');
				$searcher->setOrderBy('departments.title');
				$counts = $db->fetchAll($searcher->getSql());
				$counts_department = array();

				foreach($counts as $count)
					$counts_department[$count['id']] = $count;

				$departments = App::getDataService('Department')->getInHierarchy();

				foreach($departments as $i => $department) {
					if(!isset($counts_department[$department['id']])) {
						$departments[$i]['count'] = 0;
					}
					else {
						$departments[$i]['count'] = $counts_department[$department['id']]['count'];
					}

					foreach($department['children'] as $h => $child) {
						if(!isset($counts_department[$child['id']])) {
							unset($departments[$i]['children'][$h]);
							continue;
						}

						$child['count'] = $counts_department[$child['id']]['count'];
						$departments[$i]['count'] += $child['count'];
						$departments[$i]['children'][$h] = $child;
					}

					if(!$departments[$i]['count'])
						unset($departments[$i]);
				}

				return $departments;
			case 'date_created':
				$searcher->setGroupBy('MONTH(date_created), YEAR(date_created)');
				$searcher->setColumns('DATE_FORMAT(date_created, "%c-%Y") AS id, DATE_FORMAT(date_created,"%M %Y") AS title, COUNT(*) AS count');
				$searcher->setOrderBy('chat_conversations.date_created');
				break;

			case 'total_to_ended':
				$searcher->setColumns($this->makeTimeFieldSelect('total_to_ended', 'grouping_var') . ',  COUNT(*) AS count');
				$searcher->setGroupBy('grouping_var');
				$searcher->setOrderBy('grouping_var', 'ASC');
				break;
		}

		$counts = $db->fetchAll($searcher->getSql());

		if ($this->group_by == 'total_to_ended') {
			$titles = \Application\DeskPRO\Tickets\GroupingCounter::getTimeTitles();
			foreach ($counts as &$c) {
				$c['id'] = $c['grouping_var'];
				$c['title'] = $titles[$c['grouping_var']];
			}
		}

		return $counts;
	}

	/**
	 * Generates some nasty SQL to get MySQL to group on the right date range value.
	 *
	 * @param $field
	 * @param $select_name
	 * @return string
	 */
	public function makeTimeFieldSelect($field, $select_name)
	{
		$times = array_keys(\Application\DeskPRO\Tickets\GroupingCounter::getTimeTitles());

		$sql = "CASE ";

		$parts = array();
		foreach ($times as $k => $t) {
			$parts[] = " WHEN chat_conversations.total_to_ended < $t THEN $t ";
		}

		$sql .= implode('', $parts) . " ELSE ".self::LAST_TIME_MARKER." END AS $select_name";

		return $sql;
	}
}