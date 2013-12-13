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
 * @category Search
 */

namespace Application\DeskPRO\Search\Searcher\Elastic;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\People\PersonContextInterface;

use Application\DeskPRO\Search\Adapter\ElasticAdapter;
use Application\DeskPRO\Search\Searcher\ContentSearcherInterface;

use Application\DeskPRO\Search\SearcherResult\Elastic\ResultSet;
use Application\DeskPRO\Search\SearcherResult\Elastic\Result;

/**
 * The content searcher searches: articles, downloads, feedback, news
 */
class ContentSearcher implements ContentSearcherInterface, PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Search\Adapter\ElasticAdapter
	 */
	protected $adapter;

	public function __construct(ElasticAdapter $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function setPersonContext(Person $person)
	{
		$this->person = $person;
	}


	public function query($query_text, $per_page = 25, $page = 1, array $limit_types = null)
	{
		$index = $this->adapter->getIndex('content');

		$query = new \Elastica_Query_QueryString($query_text);
		$query_out = new \Elastica_Query();
		$query_out->setQuery($query);

		$filter = $this->getPermissionFilter();
		if ($filter) {
			$query_out->setFilter($filter);
		}

		$query_out->setHighlight(array(
			'tags_schema' => 'styled',
			'fields' => array(
				'_all' => array()
			)
		));

		$from = ($page - 1) * $per_page;
		$query_out->setFrom($from);
		$query_out->setLimit($per_page);

		$e_result_set = $index->search($query_out);
		$result_set = ResultSet::newFromElasticResultSet($e_result_set);

		return $result_set;
	}

	public function labelled(array $labels)
	{
		$index = $this->adapter->getIndex('content');

		$query = new \Elastica_Query_Bool();
		foreach ($labels as $l) {
			$query->addMust(array('term' => array('labels' => $l)));
		}

		$query_out = \Elastica_Query::create($query);

		$filter = $this->getPermissionFilter();
		if ($filter) {
			$query_out->setFilter($filter);
		}

		$e_result_set = $index->search($query_out);
		$result_set = ResultSet::newFromElasticResultSet($e_result_set);

		return $result_set;
	}


	/**
	 * Find content similar to $content.
	 *
	 * @param string $content
	 * @param array $in_types Types you want to search in, or null for all
	 * @return \Application\DeskPRO\Search\SearcherResult\ResultSet
	 */
	public function similarContent($content, array $in_types = null)
	{
		$index = $this->adapter->getIndex('content');

		if ($in_types) {
			$query = new \Elastica_Query();
			$query->setParam('query', array(
				'fuzzy_like_this' => array(
					'like_text' => $content,
					'prefix_length' => 3,
				),
			));
		} else {
			$query = new \Elastica_Query();
			$query->setParam('query', array(
				'fuzzy_like_this' => array(
					'like_text' => $content,
					'prefix_length' => 3,
				)
			));
		}

		$filter = $this->getPermissionFilter();
		if ($filter) {
			$query->setFilter($filter);
		}

		$e_result_set = $index->search($query);
		$result_set = ResultSet::newFromElasticResultSet($e_result_set);

		return $result_set;
	}

	public function omnisearch($query_text)
	{
		$index = $this->adapter->getIndex('content');

		$query = new \Elastica_Query_QueryString($query_text);
		$query_out = new \Elastica_Query();
		$query_out->setQuery($query);

		$e_result_set = $index->search($query_out);
		$result_set = ResultSet::newFromElasticResultSet($e_result_set);

		return $result_set;
	}


	/**
	 * Gets the filter that applies permissions to results
	 *
	 * @return \Elastica_Filter_Bool
	 */
	public function getPermissionFilter()
	{
		return null;
		if (!$this->person OR $this->person['is_agent']) {
			return null;
		}

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
}
