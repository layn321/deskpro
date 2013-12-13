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
 * @subpackage Elastica
 */

namespace Application\DeskPRO\Elastica\Searcher;

class TicketSearcher extends AbstractSearcher
{
	public function search($query)
	{
		$index = $this->manager->getIndex('ticket');

		$query = new \Elastica_Query_QueryString($query);
		$query_out = new \Elastica_Query();
		$query_out->setQuery($query);

		$filter = $this->getPermissionFilter();
		if ($filter) {
			$query_out->setFilter($filter);
		}

		$documents = $index->search($query)->getResults();
		$results = $this->documentsToResults($documents);

		return $results;
	}

	public function labelled($labels)
	{
		// Explode into an array of labels if not already
		// given an array
		if (!is_array($labels)) {
			$labels = explode(',', $labels);
			array_walk($labels, 'trim');
		}

		$index = $this->manager->getIndex('ticket');

		$query = new \Elastica_Query_Bool();
		foreach ($labels as $l) {
			$query->addMust(array('term' => array('labels' => $l)));
		}

		$query_out = \Elastica_Query::create($query);

		$filter = $this->getPermissionFilter();
		if ($filter) {
			$query_out->setFilter($filter);
		}

		$search_result = $index->search($query_out);

		$documents = $search_result->getResults();
		$results = $this->documentsToResults($documents);

		return $results;
	}

	public function similar($ticket)
	{
		$index = $this->manager->getIndex('ticket');

		$text = $ticket['subject'] . "\n" . $ticket->getFirstMessage()->getMessageText();

		$query = new \Application\DeskPRO\Elastica\Query\MoreLikeThis($text);
		$query_out = new \Elastica_Query();
		$query_out->setQuery($query);

		$filter = $this->getPermissionFilter();
		if ($filter) {
			$query_out->setFilter($filter);
		}

		$documents = $index->search($query)->getResults();
		$results = $this->documentsToResults($documents);

		return $results;
	}


	/**
	 * Gets the terms that apply permissions
	 *
	 * @return \Elastica_Query_Bool
	 */
	public function getPermissionFilter()
	{
		$filter = new \Elastica_Filter_Bool();

		$allowed_ids = $this->person->getAllowedDepartments();
		$disallowed_ids = $this->person->getDisallowedDepartments();

		// Allowed everything
		if (!$disallowed_ids) {
			return null;
		}

		$term = new \Elastica_Filter_Bool();

		if (count($allowed_ids) > count($disallowed_ids)) {
			$term->addShould(array('term' => array('department_id' => $allowed_ids)));
		} else {
			$term->addMustNot(array('term' => array('department_id' => $disallowed_ids)));
		}

		$filter->addShould($term);

		return $filter;
	}
}
