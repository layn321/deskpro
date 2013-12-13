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
use Application\DeskPRO\Searcher\TicketSearch;

use Orb\Util\Arrays;
use Orb\Util\Strings;
use Orb\Util\Util;
use Orb\Validator\StringEmail;

class GroupingCounter
{
	const LAST_TIME_MARKER   = 1893456000;
	const MODE_AGENT         = 'agent';
	const MODE_AGENT_TEAM    = 'agent_team';
	const MODE_PARTICIPANT   = 'participant';
	const MODE_UNASSIGNED    = 'unassigned';
	const MODE_ALL           = 'all';
	const MODE_SPECIFY       = 'specify';

	protected $grouping1 = 'department';
	protected $grouping2 = null;

	protected $mode = 'unassigned';
	protected $tickets = array();
	protected $this_person = null;

	protected $terms = array();

	protected $grouping_summary = '';

	public function getGroupingSummary()
	{
		return $this->grouping_summary;
	}

	/**
	 * Get an array of counts suitable for looping in a template etc
	 *
	 * @return array
	 */
	public function getDisplayArray()
	{

		#------------------------------
		# Connect counts to titles
		#------------------------------

		$display_elements = $this->getDisplayElementsArray();
		$titles1 = $display_elements['titles1'];
		$titles2 = $display_elements['titles2'];
		$counts  = $display_elements['counts'];

		Arrays::unshiftAssoc($titles1, -1, 'TOTAL');
		if ($titles2) {
			Arrays::unshiftAssoc($titles2, -1, 'TOTAL');
		}

		$items = array();

		$group1_has = array();
		$group2_has = array();

		foreach ($titles1 as $field1 => $field1_title) {

			if (!isset($counts[$field1])) continue;

			$countinfo = $counts[$field1];

			$group1_has[] = $field1;

			$row = array();
			$row['id'] = $field1;
			$row['title'] = $field1_title;
			$row['total'] = $countinfo['total'];

			if (!empty($countinfo['sub'])) {

				$row['sub'] = array();
				foreach ($titles2 as $field2 => $field2_title) {

					if (!isset($countinfo['sub'][$field2])) continue;
					$countinfo2 = $countinfo['sub'][$field2];

					$group2_has[] = $field2;

					$row2 = array();
					$row2['id'] = $field2;
					$row2['title'] = $field2_title;
					$row2['total'] = $countinfo2['total'];

					$row['sub'][$field2] = $row2;
				}
			}

			$items[$field1] = $row;
		}

		$group1_has = array_unique($group1_has);
		$group2_has = array_unique($group2_has);

		#------------------------------
		# Now fetch hierarchy which might be used
		#------------------------------

		$group1_structure = array();
		$group2_structure = array();

		$group1_structure = $this->getFieldStructure($this->grouping1, $titles1, $display_elements['ids1']);

		if ($this->grouping2) {
			$group2_structure = $this->getFieldStructure($this->grouping2, $titles2, $display_elements['ids2']);
		}

		return array(
			'items' => $items,
			'counts' => $counts,
			'titles1' => $titles1,
			'titles2' => $titles2,
			'group1_structure' => $group1_structure,
			'group2_structure' => $group2_structure,
		);
	}

	/**
	 * Sort a display array so that the biggest counts are first
	 *
	 * @param array $display_array
	 */
	public function sortDisplayArray(array &$display_array)
	{
		uasort($display_array, array($this, '_sortDisplayArrayCallback'));
	}

	public function _sortDisplayArrayCallback($a, $b)
	{
		if ($a['total'] == $b['total']) {
			return 0;
		}

		return ($a['total'] < $b['total']) ? -1 : 1;
	}

	protected function buildSelectTerm($grouping, $field)
	{
		if ($this->isTimeField($grouping)) {
			return $this->makeTimeFieldSelect($grouping, $field);
		} elseif ($grouping == 'language') {
			$default = App::getEntityRepository('DeskPRO:Language')->getDefault();
			return "COALESCE(tickets.language_id, {$default['id']}) AS $field";
		} elseif ($f = $this->getCustomDefField($grouping)) {
			if ($f->isChoiceType()) {
				return "COALESCE(custom_data_ticket_$field.field_id, 0) AS $field";
			} else {
				return "COALESCE(custom_data_ticket_$field.input, 0) AS $field";
			}
		} else {
			try {
				$group_fieldname = \Application\DeskPRO\Searcher\TicketSearch::getTableField($grouping);
			} catch (\InvalidArgumentException $e) {
				$group_fieldname = \Application\DeskPRO\Searcher\TicketSearch::getTableField('department');
			}
			return "COALESCE(tickets.{$group_fieldname}, 0) AS $field";
		}
	}


	/**
	 * Get the raw counts
	 *
	 * @return array
	 */
	public function getCounts()
	{
		$group_by = 'GROUP BY field1';

		$select_fields[] = $this->buildSelectTerm($this->grouping1, 'field1');

		if ($this->grouping2) {
			$select_fields[] = $this->buildSelectTerm($this->grouping2, 'field2');
			$group_by .= ', field2';
		}
		$select_fields[] = 'COUNT(*) AS total';

		$join = '';
		if ($f = $this->getCustomDefField($this->grouping1)) {
			if ($f->isChoiceType()) {
				$join = "LEFT JOIN custom_data_ticket AS custom_data_ticket_field1 ON (custom_data_ticket_field1.ticket_id = tickets.id AND custom_data_ticket_field1.root_field_id = {$f->getId()})";
			} else {
				$join = "LEFT JOIN custom_data_ticket AS custom_data_ticket_field1 ON (custom_data_ticket_field1.ticket_id = tickets.id AND custom_data_ticket_field1.field_id = {$f->getId()})";
			}
		}
		if ($this->grouping2 && ($f = $this->getCustomDefField($this->grouping2))) {
			if ($f->isChoiceType()) {
				$join = "LEFT JOIN custom_data_ticket AS custom_data_ticket_field2 ON (custom_data_ticket_field2.ticket_id = tickets.id AND custom_data_ticket_field2.root_field_id = {$f->getId()})";
			} else {
				$join = "LEFT JOIN custom_data_ticket AS custom_data_ticket_field2 ON (custom_data_ticket_field2.ticket_id = tickets.id AND custom_data_ticket_field2.field_id = {$f->getId()})";
			}
		}

		// Doing a search on a sys-type filter at the same time

		if ($this->mode != self::MODE_SPECIFY) {
			$wheres = array('tickets.status = \'open\'');

			// Standard agent perms
			$agent = App::getEntityRepository('DeskPRO:Person')->find($this->this_person);
			$agent->loadHelper('AgentPermissions');
			$agent->loadHelper('AgentTeam');

			// perms only matter if person has permissions applied at all
			if ($agent->getDisallowedDepartments()) {

				$where_perm = array();
				$where_perm[] = "tickets.agent = {$agent['id']}";

				if ($agent->getAgentTeamIds()) {
					$where_perm[] = "tickets.agent_team IN (" . implode(',', $agent->getAgentTeamIds()) . ")";
				}

				$where_perm[] = "tickets.department IN (" . implode(',', $agent->getAllowedDepartments()) . ")";
				$where_perm[] = "part_check.person = {$agent['id']}";

				$where_perm = implode(' OR ', $where_perm);

				$where[] = "($where_perm)";
			}

			switch ($this->mode) {
				case self::MODE_AGENT:
					$wheres[] = 'tickets.agent = ' . $agent['id'];
					break;

				case self::MODE_AGENT_TEAM:
					$wheres[] = "tickets.agent_team IN (" . implode(',', $agent->getAgentTeamIds()) . ")";
					break;

				case self::MODE_PARTICIPANT:
					$wheres[] = "tickets.agent_team IN (" . implode(',', $agent->getAgentTeamIds()) . ")";
					break;

				case self::MODE_ALL:
					break;

				case self::MODE_UNASSIGNED:
					$wheres[] = 'tickets.agent IS NULL';
					break;
			}

			$sql = "
				SELECT " . implode(', ', $select_fields) . "
				FROM tickets
				$join
				LEFT JOIN tickets_participants ON (tickets_participants.ticket = tickets.id)
				LEFT JOIN tickets_participants AS part_check ON (part_check.ticket = tickets.id)
				WHERE " . implode(' AND ', $wheres) . "
				$group_by WITH ROLLUP
			";

		// We have ticket IDs already (mode = specify)
		} else {

			if (!$this->tickets) {
				return array();
			}

			$wheres[] = "tickets.id IN (" . implode(',', $this->tickets) . ")";
			$sql = "
				SELECT " . implode(', ', $select_fields) . "
				FROM tickets
				$join
				WHERE " . implode(' AND ', $wheres) . "
				$group_by WITH ROLLUP
			";
		}

		$db = App::getDb();

		$counts = $db->fetchAll($sql);

		return $counts;
	}


	/**
	 * Check if a field is a time field
	 *
	 * @param $field
	 * @return bool
	 */
	public function isTimeField($field)
	{
		return in_array($field, array(
			TicketSearch::TERM_USER_WAITING,
			TicketSearch::TERM_TOTAL_USER_WAITING,
			TicketSearch::TERM_DATE_CREATED,
		));
	}


	/**
	 * @param string $field
	 * @return \Application\DeskPRO\Entity\CustomDefTicket|null
	 */
	public function getCustomDefField($field)
	{
		if ($fid = Strings::extractRegexMatch('#^ticket_field_(\d+)$#', $field)) {
			return App::getSystemService('TicketFieldsManager')->getFieldFromId($fid);
		}
		return null;
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
		$times = array_keys($this->getTimeTitles());
		$fieldname = \Application\DeskPRO\Searcher\TicketSearch::getTableField($field);
		$times = array_reverse($times);
		$last_t = self::LAST_TIME_MARKER;

		$now = time();

		$sql = "CASE ";

		$parts = array();
		foreach ($times as $t) {
			if ($field == TicketSearch::TERM_TOTAL_USER_WAITING) {
				// total time is stored in seconds, so we're not doing a date compare
				$date = $t;
				$parts[] = " WHEN (tickets.$fieldname + ($now - COALESCE(UNIX_TIMESTAMP(date_user_waiting)))) >= $date THEN $t ";
			} else {
				// Get a real time so we dont have mysql doing calculations,
				// and we dont need to do a subquery etc
				$date = date('Y-m-d H:i:s', $now - $t);

				if ($t == 300) {
					$now = date('Y-m-d H:i:s');
					$parts[] = " WHEN tickets.$fieldname BETWEEN '$date' AND '$now' THEN $t ";
				}

				$parts[] = " WHEN tickets.$fieldname <= '$date' THEN $last_t ";
			}

			$last_t = $t;
		}

		$sql .= implode('', $parts) . " ELSE ".self::LAST_TIME_MARKER." END AS $select_name";

		return $sql;
	}


	/**
	 * Get information about strucutred counts and titles.
	 *
	 * @return array
	 */
	public function getDisplayElementsArray()
	{
		$counts = $this->getCounts();

		#------------------------------
		# Get titles for each grouping, and sort into a keyed structure
		#------------------------------

		$ids1 = array();
		$ids2 = array();

		// $counts_structure becomes:
		// array(field1 => array(total => xxx, sub => array(someid => 123, someid2 => 123 ...) )

		$counts_structured = array();
		foreach ($counts as $count) {

			// Store ID's
			if ($count['field1'] !== null) {
				$ids1[] = $count['field1'];
			}

			if ($this->grouping2 AND $count['field2'] !== null) {
				$ids2[] = $count['field2'];
			}

			#------------------------------
			# Into structure
			#------------------------------

			// Set ROLLUP's (totals) to -1
			if ($count['field1'] === null) $count['field1'] = -1;
			if ($this->grouping2 AND $count['field2'] === null) $count['field2'] = -1;

			// Init array keys
			if (!isset($counts_structured[$count['field1']])) {
				$counts_structured[$count['field1']] = array('total' => $count['total']);
				if ($this->grouping2) {
					$counts_structured[$count['field1']]['sub'] = array();
				}
			}

			// Save numbers
			if ($this->grouping2) {
				if ($count['field2'] == -1) {
					$counts_structured[$count['field1']]['total'] = $count['total'];
				} else {
					$counts_structured[$count['field1']]['sub'][$count['field2']] = $count['total'];
				}
			}
		}

		$ids1 = array_unique($ids1);

		if ($this->grouping2) {
			$ids2 = array_unique($ids2);
		}

		$titles1 = $this->getFieldTitles($this->grouping1, $ids1);
		$titles2 = null;
		if ($this->grouping2) {
			$titles2 = $this->getFieldTitles($this->grouping2, $ids2);
		}

		return array(
			'titles1' => $titles1,
			'titles2' => $titles2,
			'ids1'    => $ids1,
			'ids2'    => $ids2,
			'counts'  => $counts_structured
		);
	}



	/**
	 * Get's a hierarchy array of titles for use in a template.
	 *
	 * @param $field
	 * @param array $titles
	 * @param array $ids
	 * @return array
	 */
	public function getFieldStructure($field, array $titles, array $ids)
	{
		switch ($field) {
			case TicketSearch::TERM_DEPARTMENT:
				$group_structure = App::getDataService('Department')->getInHierarchy();
				break;

			case TicketSearch::TERM_CATEGORY:
				$group_structure = App::getDataService('TicketCategory')->getInHierarchy();
				$group_structure['0'] = array('id' => 0, 'title' => App::getTranslator()->phrase('agent.general.none'));
				break;

			case TicketSearch::TERM_PRODUCT:
				$group_structure = App::getDataService('Product')->getInHierarchy();
				$group_structure['0'] = array('id' => 0, 'title' => App::getTranslator()->phrase('agent.general.none'));
				break;

			default:
				$group_structure = array();
				foreach ($titles as $id => $t) {
					$group_structure[$id] = array('id' => $id, 'title' => $t);
				}

				// Make note of unknown items (should never happen, but better to include than not!)
				foreach ($ids as $id) {
					if (!isset($group_structure[$id])) {
						$group_structure[$id] = array('id' => $id, 'title' => "Unknown $id");
					}
				}


				// But remove the -1 rollups
				unset($group_structure[-1]);
				break;
		}

		return $group_structure;
	}



	/**
	 * Get a string of id=>title for a particular field, given IDs.
	 * Sometimes $ids is not needed (ie departments can all be fetched),
	 * other times it's important (ie dont want every company name in the entire db).
	 *
	 * @param string $field
	 * @param array $ids
	 * @return array
	 */
	public function getFieldTitles($field, array $ids)
	{
        $tr = App::getTranslator();
		$titles = null;

		switch ($field) {
			case TicketSearch::TERM_DEPARTMENT:
				$this->grouping_summary = $tr->phrase('agent.general.department');
				$titles = App::getDataService('Department')->getNames();
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.none'));
				break;

			case TicketSearch::TERM_AGENT:
				$this->grouping_summary = $tr->phrase('agent.general.agent');
				$titles = App::getDataService('Person')->getAgentNames();
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.unassigned'));
				break;

			case TicketSearch::TERM_AGENT_TEAM:
				$this->grouping_summary = $tr->phrase('agent.general.agent_team');
				$titles = App::getDataService('AgentTeam')->getTeamNames();
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.unassigned'));
				break;

			case TicketSearch::TERM_PERSON:
				$this->grouping_summary = 'Person';
				$titles = array();

				if ($ids) {
					$ids_str = implode(',', $ids);
					$all = App::getDb()->fetchAll("
						SELECT id, name, first_name, last_name FROM people WHERE id IN ($ids_str)
					");

					foreach ($all as $r) {
						if ($r['first_name'] AND $r['last_name']) {
							$name = $r['first_name'] . ' ' . $r['last_name'];
						} elseif ($r['name']) {
							$name = $r['name'];
						} elseif ($r['last_name']) {
							$name = $r['last_name'];
						} elseif ($r['first_name']) {
							$name = $r['first_name'];
						} else {
							$name = 'Person #'.$r['id'];
						}

						$titles[$r['id']] = $name;
					}
				}
				break;

			case TicketSearch::TERM_URGENCY:
				$this->grouping_summary = $tr->phrase('agent.general.urgency');
				$x = range(1, 10);
				$titles = array_combine($x, $x);
				break;

			case TicketSearch::TERM_CATEGORY:
				$this->grouping_summary = $tr->phrase('agent.general.category');
				$titles = App::getDataService('TicketCategory')->getNames();
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.none'));
				break;

			case TicketSearch::TERM_PRIORITY:
				$this->grouping_summary = $tr->phrase('agent.general.priority');
				$titles = App::getDataService('TicketPriority')->getNames();
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.none'));
				break;

			case TicketSearch::TERM_PRODUCT:
				$this->grouping_summary = $tr->phrase('agent.general.product');
				$titles = App::getDataService('Product')->getNames();
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.none'));
				break;

			case TicketSearch::TERM_WORKFLOW:
				$this->grouping_summary = $tr->phrase('agent.general.workflow');
				$titles = App::getDataService('TicketWorkflow')->getNames();
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.none'));
				break;

			case TicketSearch::TERM_ORGANIZATION:
				$this->grouping_summary = $tr->phrase('agent.general.organization');
				$titles = App::getDataService('Organization')->getOrganizationNames($ids);
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.none'));
				break;

			case TicketSearch::TERM_LANGUAGE:
				$this->grouping_summary = $tr->phrase('agent.general.language');
				$titles = App::getDataService('Language')->getTitles();
				Arrays::unshiftAssoc($titles, 0, App::getTranslator()->phrase('agent.general.none'));
				break;

			case TicketSearch::TERM_USER_WAITING:
				$this->grouping_summary = $tr->phrase('agent.general.time_waiting');
				$titles = $this->getTimeTitles();
				break;

			case TicketSearch::TERM_TOTAL_USER_WAITING:
				$this->grouping_summary = $tr->phrase('agent.general.total_time_waiting');
				$titles = $this->getTimeTitles();
				break;

			case TicketSearch::TERM_DATE_CREATED:
				$this->grouping_summary = $tr->phrase('agent.general.time_since_creation');
				$titles = $this->getTimeTitles();
				break;

			default:

				if ($f = $this->getCustomDefField($field)) {
					if ($f->isChoiceType()) {
						$titles = array('0' => 'None');
						foreach (App::getSystemService('TicketFieldsManager')->getFieldChildren($f) as $subf) {
							$titles[$subf->getId()] = $subf->title;
						}
					} else {
						if ($ids) {
							$titles = array_combine($ids, $ids);
						} else {
							$titles = array();
						}

						Arrays::unshiftAssoc($titles, '0', 'None');
					}
				} else {
					$this->grouping_summary = $field;

					// Just make all titles the ids themselves by default,
					// useful for things like status which might be rendered into words after
					if ($ids) {
						$titles = array_combine($ids, $ids);
					} else {
						$titles = array();
					}
				}
				break;
		}

		return $titles;
	}

	public static function getTimeTitles()
	{
		$times = array(
			300                                => 'agent.time.group_lt_5_mins',
			900                                => 'agent.time.group_5_to_15_mins',
			1800                               => 'agent.time.group_15_to_30_mins',
			3600                               => 'agent.time.group_30_to_60_mins',
			7200                               => 'agent.time.group_1_to_2_hours',
			10800                              => 'agent.time.group_2_to_3_hours',
			14400                              => 'agent.time.group_3_to_4_hours',
			21600                              => 'agent.time.group_4_to_6_hours',
			43200                              => 'agent.time.group_6_to_12_hours',
			86400                              => 'agent.time.group_12_to_24_hours',
			172800                             => 'agent.time.group_1_to_2_days',
			259200                             => 'agent.time.group_2_to_3_days',
			345600                             => 'agent.time.group_3_to_4_days',
			432000                             => 'agent.time.group_4_to_5_days',
			518400                             => 'agent.time.group_5_to_6_days',
			604800                             => 'agent.time.group_6_to_7_days',
			1209600                            => 'agent.time.group_1_to_2_weeks',
			1814400                            => 'agent.time.group_2_to_3_weeks',
			2419200                            => 'agent.time.group_3_to_4_weeks',
			4838400                            => 'agent.time.group_1_to_2_months',
			7257600                            => 'agent.time.group_2_to_3_months',
			9676800                            => 'agent.time.group_3_to_4_months',
			12096000                           => 'agent.time.group_4_to_5_months',
			14515200                           => 'agent.time.group_5_to_6_months',
			self::LAST_TIME_MARKER             => 'agent.time.group_gt_6_months',
		);

		foreach ($times as &$phrase) {
			$phrase = App::getTranslator()->phrase($phrase);
		}

		return $times;
	}



	/**
	 * Set mode which defines which kinds of tickets we want.
	 *
	 * When $mode is MODE_YOUR or MODE_OTHERS, $opt sholud be a person ID.
	 *
	 * @param string $mode
	 * @param mixed $opt
	 */
	public function setMode($mode, $opt = null)
	{
		$this->mode = $mode;

		if ($this->mode == self::MODE_SPECIFY) {
			$this->tickets = $opt;
		} else {
			$this->this_person = $opt;
		}
	}



	/**
	 * Set the grouping fields.
	 *
	 * @param string $grouping1
	 * @param string $grouping2
	 */
	public function setGrouping($grouping1, $grouping2 = null)
	{
		$this->grouping1 = $grouping1 ? $grouping1 : 'department';
		$this->grouping2 = $grouping2 ? $grouping2 : null;
	}


	/**
	 * Transforms a grouping var and choice into a search term for TicketSearch
	 *
	 * @param $groupvar
	 * @param $groupchoice
	 * @return array
	 */
	public static function getSearchTerm($groupvar, $groupchoice)
	{
		switch ($groupvar) {
			case TicketSearch::TERM_USER_WAITING:
			case TicketSearch::TERM_DATE_CREATED:

				$times = array_keys(self::getTimeTitles());
				$key = array_search($groupchoice, $times);

				if ($key == 0) {
					$date1 = new \DateTime('-5 minutes');
					$date2 = new \DateTime('now');

					return array('type' => $groupvar, 'op' => 'between', 'options' => array('date1' => $date1, 'date2' => $date2));
				} elseif ($key == (count($times) - 1)) {
					$date = new \DateTime('@' . (time() - 14515201));
					return array('type' => $groupvar, 'op' => 'lte', 'options' => array('date1' => $date));
				} else {
					$date1 = new \DateTime('-' . $times[$key] . ' seconds');
					$date2 = new \DateTime('-' . $times[$key-1] . ' seconds');

					return array('type' => $groupvar, 'op' => 'between', 'options' => array('date1' => $date1, 'date2' => $date2));
				}

				break;

			case TicketSearch::TERM_TOTAL_USER_WAITING:

				$times = array_keys(self::getTimeTitles());
				$key = array_search($groupchoice, $times);

				if ($key == 0) {
					$term = array('type' => $groupvar, 'op' => 'lte', 'options' => 300);
				} elseif ($key == (count($times) - 1)) {
					$term = array('type' => $groupvar, 'op' => 'gte', 'options' => 14515200);
				} else {
					$term = array('type' => $groupvar, 'op' => 'lte', 'options' => array($times[$key], $times[$key+1]));
				}

				return $term;
            case TicketSearch::TERM_URGENCY:
                return array('type' => $groupvar, 'op' => 'is', 'options' => array($groupchoice));
			case 'person':
				return array('type' => 'person_id', 'op' => 'is', 'options' => array('person_id' => $groupchoice));
			default:
				$f = null;
				if ($fid = Strings::extractRegexMatch('#^ticket_field_(\d+)$#', $groupvar)) {
					$f = App::getSystemService('TicketFieldsManager')->getFieldFromId($fid);
				}
				if ($f) {
					if ($groupchoice === '0') {
						$groupchoice = 'DP_NO_SELECTION';
					}
					return array(
						'type' => "ticket_field[$fid]",
						'op' => 'is',
						'options' => array('value' => $groupchoice)
					);
				}

				return array('type' => $groupvar, 'op' => 'is', 'options' => array($groupchoice));
		}
	}
}
