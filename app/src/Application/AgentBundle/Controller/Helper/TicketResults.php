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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller\Helper;

use Application\DeskPRO\Searcher\TicketSearch;
use Application\DeskPRO\Entity\TicketFilter;
use Application\DeskPRO\Entity\ResultCache;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity;
use Application\DeskPRO\App;
use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Handles ticket searches
 */
class TicketResults
{
	/**
	 * @var Application\AgentBundle\Controller\AbstractController
	 */
	protected $controller;

	/**
	 * @var array
	 */
	protected $ticket_ids = array();

	/**
	 * @var array
	 */
	protected $grouped_ticket_ids = null;

	/**
	 * @var string
	 */
	protected $group_field = null;

	/**
	 * @var array
	 */
	protected $order_by = null;

	/**
	 * @var array
	 */
	protected $group_display_info = null;

	/**
	 * @var string
	 */
	protected $grouping_summary;


	/**
	 * @return Application\AgentBundle\Controller\Helper\TicketResults
	 */
	public static function newFromFilter($controller, TicketFilter $filter)
	{
		$helper = new self($controller);
		$helper->setTicketIds($filter->getResults($controller->getPerson()));

		if ($controller->in->getString('group_by')) {
			$helper->setGroupField($controller->in->getString('group_by'));
		} elseif ($filter['group_by']) {
			$helper->setGroupField($filter['group_by']);
		}

		// Or if the user has their own
		$group_by = $controller->getPerson()->getPref('agent.ui.ticket-filter-group-by.' . $filter['id']);
		if ($group_by) {
			$helper->setGroupField($group_by);
		}

		$helper->setGroupOrderBy($filter->getSearcher()->getOrderBy());

		return $helper;
	}

	/**
	 * @return \Application\AgentBundle\Controller\Helper\TicketResults
	 */
	public static function newFromResultCache($controller, ResultCache $result_cache)
	{
		$helper = new self($controller);
		$helper->setTicketIds($result_cache['results']);

		if (!empty($result_cache['criteria']['order_by'])) {
			$helper->setGroupOrderBy($result_cache['criteria']['order_by']);
		}
		if (!empty($result_cache['criteria']['group_by'])) {
			$helper->setGroupField($result_cache['criteria']['group_by']);
		}

		$extra = $result_cache['extra'];
		if (!empty($extra['group_by'])) {
			$helper->setGroupField($extra['group_by']);
		}

		return $helper;
	}



	public function __construct($controller)
	{
		$this->controller = $controller;
	}



	/**
	 * Set ticket IDs for the search results
	 * @param array $ticket_ids
	 */
	public function setTicketIds(array $ticket_ids)
	{
		$this->ticket_ids = $ticket_ids;
	}



	/**
	 * Get ticket IDs
	 *
	 * @return array
	 */
	public function getTicketIds()
	{
		return $this->ticket_ids;
	}


	/**
	 * Get total number of matches
	 *
	 * @return int
	 */
	public function getCount()
	{
		return count($this->getTicketIds());
	}



	/**
	 * Get ticket IDs that match the current group
	 *
	 * @return array
	 */
	public function getGroupTicketIds($field_id)
	{
		if ($this->grouped_ticket_ids !== null) return $this->grouped_ticket_ids;
		if ($this->group_field === null) return array();

		$searcher = new TicketSearch();
		$searcher->setPerson($this->controller->getPerson());
		$searcher->addTerm(TicketSearch::TERM_ID, TicketSearch::OP_IS, $this->getTicketIds());

		$term = \Application\DeskPRO\Tickets\GroupingCounter::getSearchTerm($this->group_field, $field_id);

		$searcher->addTerm($term['type'], $term['op'], $term['options']);

		if ($this->order_by) {
			$searcher->setOrderByCode($this->order_by);
		}

		$this->grouped_ticket_ids = $searcher->getMatches();

		return $this->grouped_ticket_ids;
	}



	/**
	 * Get tickets for a particular page
	 *
	 * @return array
	 */
	public function getTicketsForPage($page, $per_page = 50)
	{
		return $this->_getPageFromTicketIds($this->getTicketIds(), $page, $per_page);
	}



	/**
	 * Get grouped tickets for a particular page
	 *
	 * @return array
	 */
	public function getGroupedTicketsForPage($field_id, $page, $per_page = 50)
	{
		return $this->_getPageFromTicketIds($this->getGroupTicketIds($field_id), $page, $per_page);
	}




	protected function _getPageFromTicketIds(array $ticket_ids, $page, $per_page)
	{
		$page_ticket_ids = Arrays::getPageChunk($ticket_ids, $page, $per_page);
		$tickets_raw = App::getEntityRepository('DeskPRO:Ticket')->getTicketsResultsFromIds($page_ticket_ids);

		// - We'll get a page of results, but that actual page isn't going to be
		// sorted the way we want, because MySQL was just sent a list of ID's.
		// - So we'll re-create the array here according to the order they're supposed to be in.
		$tickets = array();
		foreach ($ticket_ids as $tid) {
			if (isset($tickets_raw[$tid])) {
				$tickets[$tid] = $tickets_raw[$tid];
			}
		}

		return $tickets;
	}




	/**
	 * Set the grouping field
	 *
	 * @param string $field
	 */
	public function setGroupField($field)
	{
		$this->group_field = $field;
	}


	/**
	 * Get the grouping field
	 *
	 * @return null|string
	 */
	public function getGroupField()
	{
		return $this->group_field;
	}



	/**
	 * Set the order by that will be used for sub-grouping. Tickets area
	 * already sorted, so this is only used for fetching grouped results.
	 *
	 * @param arary $order_by
	 */
	public function setGroupOrderBy($order_by)
	{
		$this->order_by = $order_by;
	}



	/**
	 * Get counts and titles for the grouping options
	 *
	 * @return array
	 */
	public function getGroupDisplayInfo()
	{
		if ($this->group_display_info !== null) return $this->group_display_info;
		if ($this->group_field === null) return null;

		$grouper = new \Application\DeskPRO\Tickets\GroupingCounter();
		$grouper->setGrouping($this->group_field);
		$grouper->setMode('specify', $this->getTicketIds());

		$this->group_display_info = $grouper->getDisplayArray();
		//$grouper->sortDisplayArray($this->group_display_info);

		$this->grouping_summary = $grouper->getGroupingSummary();

		return $this->group_display_info;
	}


	/**
	 * Get the grouping field phrase
	 *
	 * @return string
	 */
	public function getGroupingSummary()
	{
		if ($this->grouping_summary) {
			return $this->grouping_summary;
		}
		return '';
	}


	/**
	 * Do we have enough info to run grouping? aka if we havea group_field set
	 * @return bool
	 */
	public function isGroupable()
	{
		if ($this->group_field) {
			return true;
		}

		return false;
	}
}
