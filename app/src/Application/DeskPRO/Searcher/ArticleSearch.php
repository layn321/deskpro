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

class ArticleSearch extends SearcherAbstract
{
	const TERM_ID                  = 'id';
	const TERM_STATUS              = 'status';
	const TERM_HIDDEN_STATUS       = 'hidden_status';
	const TERM_CATEGORY            = 'category';
	const TERM_CATEGORY_SPECIFIC   = 'category_specific';
	const TERM_DATE_CREATED        = 'date_created';
	const TERM_VIEW_COUNT          = 'view_count';
	const TERM_POPULAR             = 'popular';
	const TERM_NEW                 = 'new';
	const TERM_LABEL               = 'label';
	const TERM_AGENT_LIST          = 'agent_list';
	const TERM_PENDING_TRANSLATE   = 'pending_translate';
	const TERM_QUERY               = 'query';

	const ORDER_ID    = 'id';
	const ORDER_DATE  = 'id';
	const ORDER_VIEWS = 'view_count';

	/**
	 * From getSqlParts()
	 * @var array
	 */
	protected $sql_parts = null;

	/**
	 * Summary of terms in phrases
	 * @var array
	 */
	protected $summary = array();

	/**
	 * Run the search and return an array of matching ID's.
	 *
	 * @param array $limit
	 * @return array
	 */
	public function getMatches(array $limit = null)
	{
		$db = App::getDbRead();

		$article_ids = $db->fetchAllCol($this->getSql($limit));

		return $article_ids;
	}


	/**
	 * Get actual model objects for matches
	 *
	 * @param array $limit
	 * @return array
	 */
	public function getMatchingObjects(array $limit = null)
	{
		$ids = $this->getMatches($limit);

		if (!$ids) return array();

		return App::getEntityRepository('DeskPRO:Article')->getByResultIds($ids);
	}


	/**
	 * @return string
	 */
	public function getPermWhere()
	{
		if (!$this->person) {
			return '';
		}

		if (!$this->person->hasPerm('articles.use')) {
			return '0';
		}

		$dis_ids = $this->person->PermissionsManager->ArticleCategories->getDisallowedCategories();
		if (!$dis_ids) {
			return '';
		}

		$dis_ids = implode(',', $dis_ids);

		return '(catperm.category_id NOT IN(' . $dis_ids . '))';
	}


	/**
	 * Get the total number of matches
	 *
	 * @return int
	 */
	public function getCount()
	{
		$sql = "SELECT COUNT(DISTINCT id) FROM articles ";
		$parts = $this->getSqlParts();
		$order_by = $this->getOrderByPart();

		#------------------------------
		# Add joins
		#------------------------------

		foreach ($parts['joins'] as $j) {
			if (is_array($j)) {
				$sql .= $j[1] . " ";
			} else {
				$sql .= "LEFT JOIN $j ON $j.article_id = articles.id ";
			}
		}

		if (is_array($order_by)) {
			list ($order_join, $order_by) = $order_by;

			$sql .= " $order_join ";
		}

		#------------------------------
		# Add wheres
		#------------------------------

		$sql .= "WHERE ";
		if (!$this->findTerm(self::TERM_AGENT_LIST)) {
			$where_perm = $this->getPermWhere();
			if ($where_perm) {
				$sql .= $where_perm . ' AND ';
			}
		}
		if ($parts['wheres']) {
			$sql .= implode(" AND ", $parts['wheres']);
		} else {
			$sql .= '1';
		}

		$count = App::getDbRead()->fetchColumn($sql);

		return $count;
	}


	/**
	 * Get the summary of crtiera
	 *
	 * @return array
	 */
	public function getSummary()
	{
		$this->getSqlParts();

		$summary = $this->summary;

		return $summary;
	}


	/**
	 * Get the SQL query that'll fetch the results
	 *
	 * @return string
	 */
	public function getSql(array $limit = null)
	{
		$sql = "SELECT articles.id FROM articles ";

		$parts = $this->getSqlParts();
		$order_by = $this->getOrderByPart();


		#------------------------------
		# Add joins
		#------------------------------

		foreach ($parts['joins'] as $j) {
			if (is_array($j)) {
				$sql .= $j[1] . " ";
			} else {
				$sql .= "LEFT JOIN $j ON $j.article_id = articles.id ";
			}
		}

		if (is_array($order_by)) {
			list ($order_join, $order_by) = $order_by;

			$sql .= " $order_join ";
		}

		#------------------------------
		# Add wheres
		#------------------------------

		$sql .= "WHERE ";

		if (!$this->findTerm(self::TERM_AGENT_LIST)) {
			$where_perm = $this->getPermWhere();
			if ($where_perm) {
				$sql .= $where_perm . ' AND ';
			}
		}

		if ($parts['wheres']) {
			$sql .= implode(" AND ", $parts['wheres']);
		} else {
			$sql .= '1';
		}

		$sql .= " GROUP BY articles.id ";
		$sql .= $order_by;

		if ($limit) {
			$sql .= " LIMIT {$limit['offset']},{$limit['max']}";
		} else {
			$sql .= " LIMIT 1000";
		}

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
			$this->order_by = array('id', 'DESC');
		}

		list($type, $dir) = $this->order_by;

		$dir = strtoupper($dir);
		if ($dir != self::ORDER_ASC AND $dir != self::ORDER_DESC) {
			$dir = self::ORDER_DESC;
		}

		$order_by = '';

		switch ($type) {
			case 'id':
			case 'date':
				$order_by = "ORDER BY articles.date_published $dir";
				break;

			case 'view_count':
				$order_by = "ORDER BY articles.view_count $dir";
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
		if ($this->sql_parts !== null) return $this->sql_parts;

		$db = App::getDbRead();
		$tr = App::getTranslator();

		$wheres = array();
		$joins = array(
			array('article_to_categories_perm', "LEFT JOIN article_to_categories AS catperm ON (catperm.article_id = articles.id)")
		);

		foreach ($this->terms as $info) {
			$join_id = Util::requestUniqueId();
			$join_name = "j_$join_id";

			list($term, $op, $choice) = $info;
			$term_id = null;

			switch ($term) {
                case self::TERM_ID:
					$wheres[] = $this->_rangeMatch("articles.id", $op, $choice, true);
					$this->summary[] = $this->_rangeSummary($tr->phrase('agent.general.id'), $op, $choice);
					break;

				case self::TERM_HIDDEN_STATUS:
					$wheres[] = $this->_stringMatch('articles.hidden_status', $op, $choice);
					break;

				case self::TERM_STATUS:

					$choice = (array)$choice;
					$choice = array_pop($choice);

					// Normal vis status
					if (strpos($choice, '.') === false){
						$status = $choice;
						$hidden_status = '';

					// Formatted: hidden.hidden_status
					} else {
						list ($status, $hidden_status) = explode('.', $choice, 2);
					}

					if ($hidden_status) {
						$wheres[] = $this->_stringMatch('articles.hidden_status', $op, $hidden_status);
					} else {
						$wheres[] = $this->_stringMatch('articles.status', $op, $status);
					}

                    $phrase_vars = array('field' => 'Status', 'value' => ($hidden_status ? $hidden_status : $status));

					if ($op == self::OP_NOT OR $op == self::OP_NOTCONTAINS) {
                        $this->summary[] = $tr->phrase('agent.general.x_is_not_y', $phrase_vars);
					}
                    else {
    					$this->summary[] = $tr->phrase('agent.general.x_is_y', $phrase_vars);
                    }

					break;

				case self::TERM_QUERY:

					$j1 = $join_name . '_t';
					$j2 = $join_name . '_c';

					$joins[] = array(
						'object_lang',
						"LEFT JOIN object_lang AS $j1 ON ($j1.ref_type = 'article' AND $j1.ref_id = articles.id AND $j1.prop_name = 'title')"
					);

					$joins[] = array(
						'object_lang',
						"LEFT JOIN object_lang AS $j2 ON ($j2.ref_type = 'article' AND $j2.ref_id = articles.id AND $j2.prop_name = 'content')"
					);

					$string = $choice['query'];
					$type = !empty($choice['type']) ? $choice['type'] : 'phrase';

					if (!$string) {
						break;
					}

					$w = array();
					$w[] = '(' . $this->_stringSearch("articles.title", $op, $string, $type) . ')';
					$w[] = '(' . $this->_stringSearch("articles.content", $op, $string, $type) . ')';
					$w[] = '(' . $this->_stringSearch("$j1.value", $op, $string, $type) . ')';
					$w[] = '(' . $this->_stringSearch("$j2.value", $op, $string, $type) . ')';

					$wheres[] = implode(' OR ' , $w);
					break;

				case self::TERM_CATEGORY:
				case self::TERM_CATEGORY_SPECIFIC:
					$base_ids = (array)((is_array($choice) && isset($choice['category'])) ? $choice['category'] : $choice);
					$ids = array();

					if ($term == self::TERM_CATEGORY_SPECIFIC) {
						$ids = $base_ids;
					} else {
						foreach ($base_ids as $id) {
							$ids = array_merge($ids, App::getEntityRepository('DeskPRO:ArticleCategory')->getIdsInTree($id, true));
						}
					}

					$ids = array_unique($ids);

					$joins[] = array(
						'article_to_categories',
						"LEFT JOIN article_to_categories AS $join_name ON ($join_name.article_id = articles.id)"
					);

					$wheres[] = $this->_choiceMatch("$join_name.category_id", $op, $ids);

					$this->summary[] = $this->_choiceSummary('Category', $op, $choice, function($choice) {
						$titles = App::getEntityRepository('DeskPRO:ArticleCategory')->getNames((array)$choice);
						return $titles;
					});

					break;

				case self::TERM_VIEW_COUNT:
					$wheres[] = $this->_rangeMatch('articles.view_count', $op, $choice);
					break;

				case self::TERM_POPULAR:
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}
					if ($choice) {
						$wheres[] = $this->_rangeMatch('articles.view_count', 'gte', App::getSetting('core_kb.popular_views'));
					}
					break;

				case self::TERM_NEW:
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}
					if ($choice) {
						$date = new \DateTime(App::getSetting('core_kb.new_time'));
						$wheres[] = $this->_dateMatch('articles.date_created', 'gte', array('date1' => $date));
					}
					break;

				case self::TERM_DATE_CREATED:
					$wheres[] = $this->_dateMatch('articles.date_created', $op, $choice);
					$this->summary[] = $this->_dateRangeSummary('Date created', $op, $choice);
					break;

				case self::TERM_AGENT_LIST:
					$wheres[] = "(articles.status IN ('published', 'archived') OR articles.hidden_status IN('unpublished'))";
					break;

				case self::TERM_PENDING_TRANSLATE:

					$w = array();

					$langs = App::getContainer()->getLanguageData()->getAll();

					if (isset($choice['language_id']) && $choice['language_id'] && isset($langs[$choice['language_id']])) {
						$langs = array($langs[$choice['language_id']]);
					}

					foreach ($langs as $lang) {

						$lang_id   = $lang->getId();
						$join_id   = Util::requestUniqueId();
						$join_name = "j_$join_id";

						$joins[] = array(
							'object_lang',
							"LEFT JOIN object_lang AS $join_name ON ($join_name.ref_type = 'articles' AND $join_name.ref_id = articles.id AND $join_name.language_id = $lang_id)"
						);

						$w[] = "(articles.language_id != $lang_id AND $join_name.id IS NULL)";
					}

					$wheres[] = implode(' OR ', $w);

					break;

				case self::TERM_LABEL:
					$this->_normalizeOpAndChoice($op, $choice);

					$choices_in = array();
					if (is_array($choice)) {
						foreach ((array)$choice as $c) {
							$choices_in[] = $db->quote($c);
						}
						$choices_in = implode(',', $choices_in);
					}

					$this->summary[] = $this->_choiceSummary($tr->phrase('agent.general.label'), $op, $choice);

					switch ($op) {
						case self::OP_IS:
							$joins[] = array(
								'labels_articles',
								"LEFT JOIN labels_articles AS $join_name ON ($join_name.article_id = articles.id)"
							);
							$wheres[] = "$join_name.label = " . $db->quote($choice);
							break;
						case self::OP_NOT:
							$joins[] = array(
								'labels_articles',
								"LEFT JOIN labels_articles AS $join_name ON ($join_name.article_id = articles.id AND $join_name.label = '.$db->quote($choice).')"
							);
							$wheres[] = "$join_name.person_id IS NULL";
							break;
						case self::OP_CONTAINS:
							$joins[] = array(
								'labels_articles',
								"LEFT JOIN labels_articles AS $join_name ON ($join_name.article_id = articles.id)"
							);
							$wheres[] = "$join_name.label IN ($choices_in)";
							break;

						case self::OP_NOTCONTAINS:
							$joins[] = array(
								'labels_articles',
								"LEFT JOIN labels_articles AS $join_name ON ($join_name.article_id = articles.id AND $join_name.label IN ($choices_in)"
							);
							$wheres[] = "$join_name.person_id IS NULL";
							break;
					}
					break;// end labels
			}
		}

		$wheres = Arrays::removeEmptyString($wheres);

		$this->sql_parts = array(
			'joins' => $joins,

			'wheres' => $wheres
		);

		return $this->sql_parts;
	}
}
