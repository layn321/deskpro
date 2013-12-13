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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Searcher\TicketSearch;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

/**
 * Perform searches or get results from filters.
 */
class TicketSearchController extends AbstractController
{
	public function searchAction()
	{
		$search_map = array(
			'agent_id'        => TicketSearch::TERM_AGENT,
			'agent_team_id'   => TicketSearch::TERM_AGENT_TEAM,
			'category_id'     => TicketSearch::TERM_CATEGORY,
			'department_id'   => TicketSearch::TERM_DEPARTMENT,
			'label'           => TicketSearch::TERM_LABEL,
			'language_id'     => TicketSearch::TERM_LANGUAGE,
			'organization_id' => TicketSearch::TERM_ORGANIZATION,
			'participant'     => TicketSearch::TERM_PARTICIPANT,
			'person_id'       => TicketSearch::TERM_PERSON,
			'priority_id'     => TicketSearch::TERM_PRIORITY,
			'product_id'      => TicketSearch::TERM_PRODUCT,
			'status'          => TicketSearch::TERM_STATUS,
			'subject'         => TicketSearch::TERM_SUBJECT,
			'urgency'         => TicketSearch::TERM_URGENCY,
			'workflow_id'     => TicketSearch::TERM_WORKFLOW,
			'sla_id'          => TicketSearch::TERM_SLA,
			'sla_status'      => TicketSearch::TERM_SLA_STATUS,
			'sla_completed'   => TicketSearch::TERM_SLA_COMPLETED,
		);

		$date_search_map = array(
			'date_created'          => TicketSearch::TERM_DATE_CREATED,
			'date_resolved'         => TicketSearch::TERM_DATE_RESOLVED,
			'date_archived'         => TicketSearch::TERM_DATE_CLOSED,
			'date_status'           => TicketSearch::TERM_DATE_STATUS,
			'date_last_agent_reply' => TicketSearch::TERM_DATE_LAST_AGENT_REPLY,
			'date_last_user_reply'  => TicketSearch::TERM_DATE_LAST_USER_REPLY,
			'date_last_reply'       => TicketSearch::TERM_DATE_LAST_REPLY,
		);

		$terms = array();

		foreach ($search_map AS $input => $search_key) {
			$value = $this->in->getCleanValueArray($input, 'raw', 'discard');
			if ((is_string($value) && strlen($value) > 0) || (!is_string($value) && $value)) {
				$terms[] = array('type' => $search_key, 'op' => 'contains', 'options' => $value);
			}
		}

		$proc_date_input = function($date_input) {
			$date = null;
			if (Numbers::isTimestamp($date_input)) {
				try {
					$date = new \DateTime("@$date_input");
				} catch (\Exception $e) {
					$date = null;
				}
			}

			if (!$date) {
				try {
					$date = \DateTime::createFromFormat(\DateTime::ISO8601, $date_input);
				} catch (\Exception $e) {
					$date = null;
				}
			}

			if (!$date) {
				try {
					$date = \DateTime::createFromFormat('Y-m-d H:i:s', $date_input, new \DateTimeZone('UTC'));
				} catch (\Exception $e) {
					$date = null;
				}
			}

			if (!$date) {
				try {
					$date = \DateTime::createFromFormat('Y-m-d', $date_input, new \DateTimeZone('UTC'));
					$date->setTime(0,0,0);
				} catch (\Exception $e) {
					$date = null;
				}
			}

			return $date;
		};

		foreach ($date_search_map as $input => $search_key) {
			$raw = $this->in->getString($input);
			if (!$raw) {
				continue;
			}

			$date1 = null;
			$date2 = null;

			if (strpos($raw, '/') !== false) {
				$op = 'between';
				list ($date1_input, $date2_input) = explode('/', $raw, 2);

				$date1 = $proc_date_input($date1_input);
				$date2 = $proc_date_input($date2_input);

				if (!$date1 || !$date2) {
					return $this->createApiErrorResponse('invalid_term', "$input includes a bad date range: $raw. Expected format: date1/date2 where the dates are unix timestamps or ISO 8601");
				}

				$options = array('date1' => $date1, 'date2' => $date2);

			} else {
				$op_sym = $raw[0];
				$raw = substr($raw, 1);

				if ($op_sym == '<' || $op_sym == '<=') {
					$op = 'lt';
				} else if ($op_sym == '>' || $op_sym == '>=') {
					$op = 'gt';
				} else {
					return $this->createApiErrorResponse('invalid_term', "$input includes a bad operator: $op_sym. Expected '<' or '>'");
				}

				$date1 = $proc_date_input($raw);
				if (!$date1) {
					return $this->createApiErrorResponse('invalid_term', "$input includes a bad date: $raw. Expected format is a unix timestamp or ISO 8601");
				}

				$options = array('date1' => $date1);
			}

			$terms[] = array('type' => $search_key, 'op' => $op, 'options' => $options);
		}

		foreach ($this->container->getSystemService('ticket_fields_manager')->getFields() as $field) {
			if ($this->in->checkIsset("field." . $field->getId())) {
				$in_val = $this->in->getString('field.'.$field->getId());
				if ($in_val) {
					$terms[] = array('type' => 'ticket_field[' . $field->getId() . ']', 'op' => 'is', 'options' => array('value' => $in_val));
				}
			}
		}

		if ($this->in->getString('query')) {
			$terms[] = array('type' => 'text', 'op' => 'is', 'options' => array('query' => $this->in->getString('query')));
		}

		if ($this->in->checkIsset('order')) {
			$order_by = $this->in->getString('order');
		} else {
			$order_by = $this->person->getPref('agent.ui.ticket-basic-order-by.general');
			if (!$order_by) {
				$order_by = 'ticket.date_created:desc';
			}
		}

		$extra = array();
		if ($order_by !== null) {
			$extra['order_by'] = $order_by;
		}

		$result_cache = $this->getApiSearchResult('ticket', $terms, $extra, $this->in->getUint('cache_id'), new \Application\DeskPRO\Searcher\TicketSearch());

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$helper = \Application\AgentBundle\Controller\Helper\TicketResults::newFromResultCache($this, $result_cache);

		return $this->createApiResponse(array(
			'page' => $page,
			'per_page' => $per_page,
			'total' => $helper->getCount(),
			'cache_id' => $result_cache->id,
			'tickets' => $this->getApiData($helper->getTicketsForPage($page, $per_page))
		));
	}

	/**
	 * Get a map of filters.
	 */
	public function getFiltersAction()
	{
		$filters = $this->_getFiltersApi()->getFiltersForPerson($this->person);
		$data = array('filters' => $this->getApiData($filters));

		if ($this->in->getBool('with_counts')) {
			$all_counts = App::getApi('tickets.filters')->getAllCountsForFiltersCollection($filters);
			$all_counts = Arrays::castToType($all_counts, 'int', 'int');

			$data['counts'] = $all_counts;
		}

		return $this->createApiResponse($data);
	}



	/**
	 * Execute a filter and return results.
	 *
	 * @param int $filter_id
	 */
	public function getFilterAction($filter_id)
	{
		$page = $this->in->getUint('page');
		if (!$page) $page = 1;

		$per_page = Numbers::bound($this->in->getUint('per_page') ?: 25, 1, 250);

		$filter = $this->_getFiltersApi()->getFilterFromId($filter_id);
		$total = $filter->getResultsCount();

		$tickets = $this->_getFiltersApi()->getTicketsFromFilter($filter_id, $page, $per_page);

		return $this->createApiResponse(array(
			'page'     => $page,
			'per_page' => $per_page,
			'total'    => $total,
			'tickets'  => $this->getApiData($tickets),
			'filter'   => $filter->toApiData(true)
		));
	}

	/**
	 * Get array of filters and counts
	 */
	public function getFilterCountsAction()
	{
		$all_counts = App::getApi('tickets.filters')->getAllCountsCustomFilters($this->person);
		$all_counts = Arrays::castToType($all_counts, 'int', 'int');

		return $this->createApiResponse(array(
			'filter_counts' => $all_counts
		));
	}

	/**
	 * @return \Application\DeskPRO\Tickets\Filters
	 */
	protected function _getFiltersApi()
	{
		return App::getApi('tickets.filters');
	}

}
