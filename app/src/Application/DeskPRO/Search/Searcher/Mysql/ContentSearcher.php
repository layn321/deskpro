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

namespace Application\DeskPRO\Search\Searcher\Mysql;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\People\PersonContextInterface;

use Application\DeskPRO\Search\Adapter\MysqlAdapter;
use Application\DeskPRO\Search\Searcher\ContentSearcherInterface;

use Application\DeskPRO\Search\SearcherResult\ResultSet;
use Application\DeskPRO\Search\SearcherResult\Result;

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
	 * @var bool
	 */
	protected $ignore_perms = false;

	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function setPersonContext(Person $person)
	{
		$this->person = $person;

		// Agents in the agent interface dont apply user usergroup permissions
		if ($person->is_agent && defined('DP_INTERFACE') && DP_INTERFACE == 'agent') {
			$this->ignore_perms = true;
		}
	}

	protected function permFilterTypes($types)
	{
		$limit_types = array_combine($types,$types);

		if ($this->person) {
			if (!$this->person->hasPerm('articles.use')) unset($limit_types['article']);
			if (!$this->person->hasPerm('feedback.use')) unset($limit_types['feedback']);
			if (!$this->person->hasPerm('news.use')) unset($limit_types['news']);
			if (!$this->person->hasPerm('download.use')) unset($limit_types['download']);
		}

		return array_values($limit_types);
	}

	public function query($query_text, $per_page = 25, $page = 1, array $limit_types = null, $top = false)
	{
		$limit_types = \Orb\Util\Arrays::removeFalsey($limit_types);
		if (!$limit_types) {
			$limit_types = array('article', 'download', 'feedback', 'news');
		}

		$limit_types = $this->permFilterTypes($limit_types);

		if (!$limit_types) {
			return new ResultSet(0, array());
		}

		$limit_types = "'" . implode('\',\'', $limit_types) . "'";

		// Specific labels
		if (preg_match_all('#\[(.*?)\]#', $query_text, $m)) {
			foreach ($m[1] as $w) {
				$query_text .= " " . MysqlAdapter::encodeLabel(strtolower($w));
			}
		}

		$words = explode(' ', $query_text);
		foreach ($words as $w) {
			$query_text .= " " . MysqlAdapter::encodeLabel(strtolower($w));
		}

		$where = "
			content_search.object_type IN ($limit_types)
			AND MATCH (content_search.content) AGAINST (? IN BOOLEAN MODE)
		";

		if (!$this->ignore_perms) {
			$permfilter = new \Application\DeskPRO\Search\Adapter\Mysql\PermissionFilter();
			$permfilter->setPersonContext($this->person);
			$perm_join  = $permfilter->getJoin();
			$perm_where = $permfilter->getWhere();
			if (!$perm_where) {
				$perm_where = '1';
			}
		} else {
			$perm_join = '';
			$perm_where = '1';
		}

		$count_query = "
			SELECT COUNT(*)
			FROM content_search
			$perm_join
			WHERE $perm_where AND $where
		";

		$start = ($page - 1) * $per_page;
		$select_query = "
			SELECT content_search.object_type, content_search.object_id, MATCH (content_search.content) AGAINST (?) AS _rel
			FROM content_search
			$perm_join
			WHERE $perm_where AND $where
			ORDER BY _rel DESC
			LIMIT $start, $per_page
		";

		if ($top) {
			$total = null;
		} else {
			$total = App::getDbRead()->fetchColumn($count_query, array($query_text));
		}

		$results_raw  = App::getDbRead()->fetchAll($select_query, array($query_text, $query_text));
		$results      = array();

		foreach ($results_raw as $result_raw) {
			$result = Result::newFromArray(array(
				'id' => $result_raw['object_id'],
				'content_type' => $result_raw['object_type'],
			));

			$results[] = $result;
		}

		if ($total === null) {
			$total = count($results);
		}

		$result_set = new ResultSet($total, $results);

		return $result_set;
	}

	public function labelled(array $labels, $per_page = 25, $page = 1, array $limit_types = null)
	{
		$limit_types = \Orb\Util\Arrays::removeFalsey($limit_types);
		if (!$limit_types) {
			$limit_types = array('article', 'download', 'feedback', 'news');
		}

		$limit_types = $this->permFilterTypes($limit_types);

		if (!$limit_types) {
			return new ResultSet(0, array());
		}

		$limit_types = "'" . implode('\',\'', $limit_types) . "'";

		$label_where = array();

		foreach ($labels as $label) {
			$label_where[] = "+" . MysqlAdapter::encodeLabel($label);
		}

		$label_where = implode(' ', $label_where);

		$where = "
			object_type IN ($limit_types)
			AND MATCH (content) AGAINST (? IN BOOLEAN MODE)
		";

		$count_query = "
			SELECT COUNT(*)
			FROM content_search
			WHERE $where
		";

		$start = ($page - 1) * $per_page;
		$select_query = "
			SELECT object_type, object_id, MATCH (content_search.content) AGAINST (?) AS _rel
			FROM content_search
			WHERE $where
			ORDER BY _rel DESC
			LIMIT $start, $per_page
		";

		$total        = App::getDb()->fetchColumn($count_query, array($label_where));
		$results_raw  = App::getDb()->fetchAll($select_query, array($label_where,$label_where));
		$results      = array();

		foreach ($results_raw as $result_raw) {
			$result = Result::newFromArray(array(
				'id' => $result_raw['object_id'],
				'content_type' => $result_raw['object_type'],
			));

			$results[] = $result;
		}

		$result_set = new ResultSet($total, $results);

		return $result_set;
	}

	/**
	 * Find content similar to $content.
	 *
	 * @param string $content
	 * @param array $in_types Types you want to search in, or null for all
	 * @return \Application\DeskPRO\Search\SearcherResult\ResultSet
	 */
	public function similarContent($content, array $in_types = array())
	{
		throw new \Application\DeskPRO\Search\Searcher\UnsupportedOperation();
	}


	public function omnisearch($query_text, array $limit_types = null, $per_page = 25, $page = 1)
	{
		$per_page = 25; $page = 1; $top = false;

		// Fulltext matches
		$r = $this->query($query_text, 10, 1, $limit_types, true);
		if ($r->count()) {
			return $r;
		}

		// Otherwise fallback to like
		$limit_types = \Orb\Util\Arrays::removeFalsey($limit_types);
		if (!$limit_types) {
			$limit_types = array('article', 'download', 'feedback', 'news');
		}

		$limit_types = $this->permFilterTypes($limit_types);

		if (!$limit_types) {
			return new ResultSet(0, array());
		}

		$limit_type_names = $limit_types;
		$limit_types = "'" . implode('\',\'', $limit_types) . "'";

		$query_words = explode(' ', $query_text);
		if (!$query_words) {
			return $r;
		}

		$params = array();
		$likes = array();
		foreach ($query_words as $w) {
			if (strlen($w) <= 2) {
				continue;
			}

			$likes[] = "content_search.content LIKE ?";
			$params[] = '%' . str_replace(array('%', '_', '\\'), array('\\%', '\\_', '\\\\'), $w) . '%';
		}
		if ($likes) {
			$where = "
				content_search.object_type IN ($limit_types)
				AND (" . implode(' OR ', $likes) . ")
			";

			if (!$this->ignore_perms) {
				$permfilter = new \Application\DeskPRO\Search\Adapter\Mysql\PermissionFilter();
				$permfilter->setPersonContext($this->person);

				if ($limit_type_names) {
					$permfilter->setTypes($limit_type_names);
				}

				$perm_join  = $permfilter->getJoin();
				$perm_where = $permfilter->getWhere();
				if (!$perm_where) {
					$perm_where = '1';
				}
			} else {
				$perm_join = '';
				$perm_where = '1';
			}

			$count_query = "
				SELECT COUNT(*)
				FROM content_search
				$perm_join
				WHERE $perm_where AND $where
				LIMIT $per_page
			";

			$start = ($page - 1) * $per_page;
			$select_query = "
				SELECT content_search.object_type, content_search.object_id
				FROM content_search
				$perm_join
				WHERE $perm_where AND $where
				ORDER BY content_search.object_id DESC
				LIMIT $start, $per_page
			";

			$total = App::getDb()->fetchColumn($count_query, $params);

			$results_raw  = App::getDb()->fetchAll($select_query, $params);
			$results      = array();

			foreach ($results_raw as $result_raw) {
				$result = Result::newFromArray(array(
					'id' => $result_raw['object_id'],
					'content_type' => $result_raw['object_type'],
				));

				$results[] = $result;
			}
		} else {
			$total       = 0;
			$results_raw = array();
			$results     = array();
		}

		if ($total === null) {
			$total = count($results);
		}

		$result_set = new ResultSet($total, $results);

		return $result_set;
	}
}
