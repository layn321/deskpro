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

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\TicketFilter;
use Symfony\Component\DependencyInjection\ContainerAware;

class Filters
{
	/**
	 * Find all filters a person can use.
	 *
	 * @param mixed $person Person or person ID
	 * @return array Collection of TicketFilter entities
	 */
	public function getFiltersForPerson($person)
	{
		return App::getOrm()
			->getRepository('DeskPRO:TicketFilter')
			->getFiltersForPerson($person);
	}


	public function getGroupedFiltersForPerson($person)
	{
		$all_filters = App::getApi('tickets.filters')->getFiltersForPerson($person);

		$order = $person->getPref('agent.ui.ticket-filters-order');
		if ($order) {
			$filters_unordered = $all_filters;
			$all_filters = array();

			foreach ($order as $id) {
				if (isset($filters_unordered[$id])) {
					$all_filters[$id] = $filters_unordered[$id];
					unset($filters_unordered[$id]);
				}
			}

			if (count($filters_unordered)) {
				foreach ($filters_unordered as $id => $q) {
					$all_filters[$id] = $q;
				}
			}
		}

		// Order them into sys/other
		$sys_filters = array();
		$sys_filters_hold = array();
		$custom_filters = array();
		$archive_filters = array();


		$unset_ids = array();

		foreach ($all_filters as $id => $filter) {
			if ($filter['sys_name']) {
				if (strpos($filter['sys_name'], '_w_hold')) {
					$sys_filters_hold[$filter['sys_name']] = $filter;
				} elseif (strpos($filter['sys_name'], 'archive_') === 0) {
					$archive_filters[$filter['sys_name']] = $filter;

					// Archive filters are special and dont act exactly like normal filters, so unset them from the 'all' array
					$unset_ids[] = $id;
				} else {
					$sys_filters[$filter['sys_name']] = $filter;
				}
			} else {
				$custom_filters[$id] = $filter;
			}
		}

		foreach ($unset_ids as $uid) {
			unset($all_filters[$uid]);
		}

		// Force order of sys
		$sys_filters_unordered = $sys_filters;
		$sys_filters = array();
		foreach (array('agent', 'participant', 'agent_team', 'unassigned', 'all') as $id) {
			if (isset($sys_filters_unordered[$id])) {
				$sys_filters[$id] = $sys_filters_unordered[$id];
				unset($sys_filters_unordered[$id]);
			}
		}

		$sys_filters_unordered = $sys_filters_hold;
		$sys_filters_hold = array();
		foreach (array('agent', 'participant', 'agent_team', 'unassigned', 'all') as $id) {
			$id .= '_w_hold';
			if (isset($sys_filters_unordered[$id])) {
				$sys_filters_hold[$id] = $sys_filters_unordered[$id];
				unset($sys_filters_unordered[$id]);
			}
		}

		if (count($sys_filters_unordered)) {
			foreach ($sys_filters_unordered as $id => $q) {
				$sys_filters[$id] = $q;
			}
		}

		if (!$person->getHasTeams()) {
			unset($sys_filters['agent_team']);
			unset($sys_filters_unordered['agent_team_w_hold']);
			unset($sys_filters_hold['agent_team_w_hold']);
		}

		return array(
			'all_filters' => $all_filters,
			'sys_filters' => $sys_filters,
			'sys_filters_hold' => $sys_filters_hold,
			'archive_filters' => $archive_filters,
			'custom_filters' => $custom_filters,
		);
	}


	/**
	 * Get a ticket filter from an ID
	 * @param int $ticket_filter_id
	 * @return TicketFilter
	 */
	public function getFilterFromId($ticket_filter_id)
	{
		return App::getOrm()
			->getRepository('DeskPRO:TicketFilter')
			->find($ticket_filter_id);
	}


	/**
	 * Get the number of results in a filter.
	 *
	 * @param TicketFilter $ticket_filter
	 * @return int
	 */
	public function getCountForFilter($ticket_filter)
	{
		$ticket_filter = App::getOrm()->getRepository('DeskPRO:TicketFilter')->getTicketFilterFromVar($ticket_filter);

		return $ticket_filter->getResultsCount();
	}


	/**
	 * Get the counts for each filter a person can see.
	 *
	 * @param mixed $person Person or person ID
	 * @return array
	 */
	public function getAllCountsSystemFilters($person)
	{
		$coll = App::getOrm()
			->getRepository('DeskPRO:TicketFilter')
			->getSystemFilters($person);

		return $this->getAllCountsForFiltersCollection($coll, $person);
	}


	/**
	 * Get the counts for each custom filter a person can see.
	 *
	 * @param mixed $person Person or person ID
	 * @return array
	 */
	public function getAllCountsCustomFilters($person)
	{
		$coll = App::getOrm()
			->getRepository('DeskPRO:TicketFilter')
			->getCustomFiltersForPerson($person);

		return $this->getAllCountsForFiltersCollection($coll);
	}


	/**
	 * Get counts for each filter in a collection.
	 *
	 * @param array $ticket_filters
	 * @return array
	 */
	public function getAllCountsForFiltersCollection($ticket_filters, Person $person_context = null)
	{
		$counts = array();

		$prefs = array();
		if ($person_context) {
			$prefs = App::getDb()->fetchAllKeyValue("
				SELECT name, value_str
				FROM people_prefs
				WHERE name LIKE 'ticket_counts.' AND person_id = ?
			", array($person_context->id));
		}

		foreach ($ticket_filters as $ticket_filter) {

			$count = 0;

			switch ($ticket_filter['sys_name']) {
				case 'archive_closed':
					$count = isset($prefs['ticket_counts.archive_closed']) ? $prefs['ticket_counts.archive_closed'] : App::getSetting('core_tablecounts.tickets.archive_closed');
					break;

				case 'archive_validating':
					$count = isset($prefs['ticket_counts.archive_validating']) ? $prefs['ticket_counts.archive_validating'] : App::getSetting('core_tablecounts.tickets.archive_validating');
					break;

				case 'archive_spam':
					$count = isset($prefs['ticket_counts.archive_spam']) ? $prefs['ticket_counts.archive_spam'] : App::getSetting('core_tablecounts.tickets.archive_spam');
					break;

				case 'archive_deleted':
					$count = isset($prefs['ticket_counts.archive_deleted']) ? $prefs['ticket_counts.archive_deleted'] : App::getSetting('core_tablecounts.tickets.archive_deleted');
					break;
			}

			if (!$count || $count < 10000) {
				$searcher = $ticket_filter->getSearcher();
				$searcher->setPerson($person_context ?: App::getCurrentPerson());
				$count = $searcher->getCount(null);
			}

			$counts[$ticket_filter['id']] = $count;
		}

		return $counts;
	}


	/**
	 * Get an array of IDs for each filter in a collection
	 *
	 * @param $ticket_filters
	 * @return array
	 */
	public function getAllIdsForFiltersCollection($ticket_filters, Person $person_context = null)
	{
		$all_ids = array();

		foreach ($ticket_filters as $ticket_filter) {
			if ($ticket_filter->isArchiveTableFilter()) {
				continue;
			}

			$all_ids[$ticket_filter['id']] = $ticket_filter->getResults($person_context);
		}

		return $all_ids;
	}


	/**
	 * Get an array of IDs for each filter in a collection
	 *
	 * @param $ticket_filters
	 * @return array
	 */
	public function getAllHoldIdsForFiltersCollection($ticket_filters)
	{
		$all_ids = array();

		foreach ($ticket_filters as $ticket_filter) {
			$searcher = $ticket_filter->getSearcher(array('type' => 'is_hold', 'op' => 'is', 'options' => array('is_hold' => 1)));
			$all_ids[$ticket_filter['id']] = $searcher->getResults();
		}

		return $all_ids;
	}


	/**
	 * @param $ticket_filter
	 * @return
	 */
	public function getIdsFromFilter($ticket_filter)
	{
		$ticket_filter = App::getOrm()->getRepository('DeskPRO:TicketFilter')->getTicketFilterFromVar($ticket_filter);

		$result_ids = $ticket_filter->getResults();

		return $result_ids;
	}


	/**
	 * Get ticket results from a filter
	 *
	 * @param TicketFilter $ticket_filter
	 * @param int $page
	 * @param int $per_page
	 * @return array
	 */
	public function getTicketsFromFilter($ticket_filter, $page = 1, $per_page = 25)
	{
		$ticket_filter = App::getOrm()->getRepository('DeskPRO:TicketFilter')->getTicketFilterFromVar($ticket_filter);

		$result_ids = $ticket_filter->getResults();

		if ($per_page) {
			$result_ids = array_chunk($result_ids, $per_page);
		} else {
			$result_ids = array($result_ids);
		}

		// index is 0-based
		$page--;

		if (!isset($result_ids[$page])) {
			return array();
		}

		$page_ids = $result_ids[$page];

		return App::getOrm()
			->getRepository('DeskPRO:Ticket')
			->getTicketsFromIds($page_ids);
	}


	/**
	 * Get flagged tickets
	 *
	 * @param string $flag
	 * @param Person $person
	 * @param int $page
	 * @param int $per_page
	 * @return array
	 */
	public function getTicketsFromFlagged($flag, $person, $page = 1, $per_page = 25)
	{
		$result_ids = App::getDb()->fetchAllCol("
			SELECT ticket_id
			FROM tickets_flagged
			WHERE person_id = ? AND color = ?
		", array($person['id'], $flag));

		if ($per_page) {
			$result_ids = array_chunk($result_ids, $per_page);
		} else {
			$result_ids = array($result_ids);
		}

		// index is 0-based
		$page = min(0, --$page);

		if (!isset($result_ids[$page])) {
			return array();
		}

		$page_ids = $result_ids[$page];

		return App::getOrm()
			->getRepository('DeskPRO:Ticket')
			->getTicketsFromIds($page_ids);
	}


	/**
	 * Get the counts for each flag a person has.
	 *
	 * @param mixed $person Person or person ID
	 * @return array
	 */
	public function getAllCountsForPersonFlagged($person)
	{
		$counts = App::getDb()->fetchAllKeyValue("
			SELECT color, COUNT(color)
			FROM tickets_flagged
			WHERE person_id = ?
			GROUP BY color
		", array($person['id']));

		return $counts;
	}
}
