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

class ContentSearcher extends AbstractSearcher
{
	public function search($query)
	{
		$index = $this->manager->getIndex('content');

		$query = new \Elastica_Query_QueryString($query);
		$query_out = new \Elastica_Query();
		$query_out->setQuery($query);
		$query_out->addHighlight(array(
			'tags_schema' => 'styled',
			'fields' => array(
				'content' => array(),
			)
		));

		$filter = $this->getPermissionFilter();
		if ($filter) {
			$query_out->setFilter($filter);
		}

		$documents = $index->search($query)->getResults();
		$results = $this->documentsToResults($documents);

		return $results;
	}

	public function omnisearch($query_text)
	{
		$index = $this->manager->getIndex('content');

		$query = new \Elastica_Query();
		$query->setParam('query', array(
			'fuzzy_like_this' => array(
				'like_text' => $query_text,
				'prefix_length' => 3,
			)
		));

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

		$index = $this->manager->getIndex('content');

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


	/**
	 * Gets the terms that apply permissions
	 *
	 * @return \Elastica_Query_Bool
	 */
	public function getPermissionFilter()
	{
		$filter = new \Elastica_Filter_Bool();

		$no_perm_types = array();

		#------------------------------
		# Articles
		#------------------------------

		if (!$this->person->getPermissionsManager()->ArticleCategories->hasRestrictions()) {
			$no_perm_types[] = 'article';
		} else {
			$term = new \Elastica_Filter_Bool();

			$cat_perms = $this->person->getPermissionsManager()->ArticleCategories->getSmallestSet();
			$type = $cat_perms['type'] == 'allowed' ? 'addMust' : 'addMustNot';

			$term->$type(array('term' => array('category_ids' => $cat_perms['ids'])));
			$filter->addShould($term);
		}

		#------------------------------
		# Downloads
		#------------------------------

		if (!$this->person->getPermissionsManager()->DownloadCategories->hasRestrictions()) {
			$no_perm_types[] = 'download';
		} else {
			$term = new \Elastica_Filter_Bool();

			$cat_perms = $this->person->getPermissionsManager()->DownloadCategories->getSmallestSet();
			$type = $cat_perms['type'] == 'allowed' ? 'addMust' : 'addMustNot';

			$term->$type(array('term' => array('category_id' => $cat_perms['ids'])));
			$filter->addShould($term);
		}

		#------------------------------
		# Feedback
		#------------------------------

		if (!$this->person->getPermissionsManager()->FeedbackCategories->hasRestrictions()) {
			$no_perm_types[] = 'feedback';
		} else {
			$term = new \Elastica_Filter_Bool();

			$cat_perms = $this->person->getPermissionsManager()->FeedbackCategories->getSmallestSet();
			$type = $cat_perms['type'] == 'allowed' ? 'addMust' : 'addMustNot';

			$term->$type(array('term' => array('category_id' => $cat_perms['ids'])));
			$filter->addShould($term);
		}

		#------------------------------
		# News
		#------------------------------

		if (!$this->person->getPermissionsManager()->NewsCategories->hasRestrictions()) {
			$no_perm_types[] = 'feedback';
		} else {
			$term = new \Elastica_Filter_Bool();

			$cat_perms = $this->person->getPermissionsManager()->NewsCategories->getSmallestSet();
			$type = $cat_perms['type'] == 'allowed' ? 'addMust' : 'addMustNot';

			$term->$type(array('term' => array('category_id' => $cat_perms['ids'])));
			$filter->addShould($term);
		}

		#------------------------------
		# No perm types
		#------------------------------

		// If all four have no perms, then we dont need this filter at all
		if (count($no_perm_types) == 4) {
			return null;
		}

		// Otherwise we need another term that just says the content type
		// is one of the ones there are no permissions for
		if ($no_perm_types) {
			$term = new \Elastica_Filter_Terms();
			$term->addShould(array('type' => $no_perm_types));
			$filter->addShould($term);
		}

		return $filter;
	}

	public function similarArticleToTicket($ticket)
	{
		$index = $this->manager->getIndex('content');

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
}
