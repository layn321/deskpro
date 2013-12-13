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

class FeedbackSearch extends SearcherAbstract
{
	const TERM_ID              = 'id';
	const TERM_STATUS          = 'status';
	const TERM_HIDDEN_STATUS   = 'hidden_status';
	const TERM_CATEGORY        = 'category';
	const TERM_CATEGORY_SPECIFIC = 'category_specific';
	const TERM_STATUS_CATEGORY = 'status_category';
	const TERM_NUM_RATINGS       = 'num_ratings';
	const TERM_DATE_CREATED    = 'date_created';
	const TERM_LABEL           = 'label';
	const TERM_QUERY           = 'query';

	const ORDER_ID    = 'id';
	const ORDER_DATE  = 'id';
	const ORDER_NUM_RATINGS = 'num_ratings';

	protected $include_hidden = false;
	protected $visitor;

	public function setVisitor($visitor)
	{
		$this->visitor = $visitor;
	}

	/**
	 * Run the search and return an array of matching ID's.
	 *
	 * @param int $limit
	 * @return array
	 */
	public function getMatches(array $limit = null)
	{
		$db = App::getDbRead();

		$feedback_ids = $db->fetchAllCol($this->getSql($limit));

		return $feedback_ids;
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

		return App::getEntityRepository('DeskPRO:Feedback')->getByResultIds($ids);
	}


	/**
	 * @return string
	 */
	public function getPermWhere()
	{
		if (!$this->person) {
			return '';
		}

		if (!$this->person->hasPerm('feedback.use')) {
			return '0';
		}

		$dis_ids = $this->person->PermissionsManager->FeedbackCategories->getDisallowedCategories();
		if (!$dis_ids) {
			return '';
		}

		$dis_ids = implode(',', $dis_ids);

		return '(feedback.category_id NOT IN(' . $dis_ids . '))';
	}


	/**
	 * Get the total number of matches
	 *
	 * @return int
	 */
	public function getCount()
	{
		$sql = "SELECT COUNT(*) FROM feedback ";
		$parts = $this->getSqlParts();
		$order_by = $this->getOrderByPart();

		#------------------------------
		# Add joins
		#------------------------------

		foreach ($parts['joins'] as $j) {
			if (is_array($j)) {
				$sql .= $j[1] . " ";
			} else {
				$sql .= "LEFT JOIN $j ON $j.feedback_id = feedback.id ";
			}
		}

		if (is_array($order_by)) {
			list ($order_join, $real_order_by) = $order_by;

			$sql .= " $order_join ";
			$order_by = $real_order_by;
		}

		#------------------------------
		# Add wheres
		#------------------------------

		if ($this->include_hidden) {
			$sql .= "WHERE (feedback.hidden_status IS NULL OR feedback.hidden_status NOT IN ('temp')) AND ";
		} else {
			$sql .= "WHERE (feedback.hidden_status IS NULL OR feedback.hidden_status NOT IN ('temp', 'deleted')) AND ";
		}
		$where_perm = $this->getPermWhere();
		if ($where_perm) {
			$sql .= $where_perm . ' AND ';
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
	 * Get the SQL query that'll fetch the results
	 *
	 * @return string
	 */
	public function getSql(array $limit = null)
	{
		$sql = "SELECT feedback.id FROM feedback ";

		$parts = $this->getSqlParts();
		$order_by = $this->getOrderByPart();


		#------------------------------
		# Add joins
		#------------------------------

		foreach ($parts['joins'] as $j) {
			if (is_array($j)) {
				$sql .= $j[1] . " ";
			} else {
				$sql .= "LEFT JOIN $j ON $j.feedback_id = feedback.id ";
			}
		}

		if (is_array($order_by)) {
			list ($order_join, $real_order_by) = $order_by;

			$sql .= " $order_join ";
			$order_by = $real_order_by;
		}

		#------------------------------
		# Add wheres
		#------------------------------

		if ($this->include_hidden) {
			$sql .= "WHERE (feedback.hidden_status IS NULL OR feedback.hidden_status NOT IN ('temp')) AND ";
		} else {
			$sql .= "WHERE (feedback.hidden_status IS NULL OR feedback.hidden_status NOT IN ('temp', 'deleted')) AND ";
		}
		$where_perm = $this->getPermWhere();
		if ($where_perm) {
			$sql .= $where_perm . ' AND ';
		}
		if ($parts['wheres']) {
			$sql .= implode(" AND ", $parts['wheres']);
		} else {
			$sql .= '1';
		}

		$sql .= " GROUP BY feedback.id ";
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
			case 'date_created':
				$order_by = "ORDER BY feedback.date_published $dir";
				break;

			case 'i-voted':
			case 'i_voted':
				if (!$this->person && !$this->visitor) {
					$this->order_by = array('id', 'DESC');
					return $this->getOrderBy();
				}

				if ($this->person->id) {
					$join = "LEFT JOIN ratings ON (ratings.object_id = feedback.id AND ratings.object_type = 'feedback' AND ratings.person_id = {$this->person->id})";
				} elseif ($this->visitor) {
					$join = "LEFT JOIN ratings ON (ratings.object_id = feedback.id AND ratings.object_type = 'feedback' AND ratings.visitor_id = {$this->visitor->id})";
				} else {
					$order_by = "ORDER BY feedback.date_published $dir";
					return $order_by;
				}

				$order_by = array(
					$join,
					"ORDER BY ratings.date_created DESC, feedback.id DESC"
				);
				break;

			case 'popular':
				$order_by = "ORDER BY (POW(total_rating+1,2)/DATEDIFF(NOW(),date_created)) DESC, date_created DESC";
				break;

			case 'most-voted':
			case 'num_ratings':
				$order_by = "ORDER BY feedback.num_ratings $dir";
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
		$db = App::getDbRead();

		$wheres = array();
		$joins = array();

		foreach ($this->terms as $info) {
			$join_id = Util::requestUniqueId();
			$join_name = "j_$join_id";

			list($term, $op, $choice) = $info;
			$term_id = null;

			switch ($term) {
                case self::TERM_ID:
					$wheres[] = $this->_rangeMatch("feedback.id", $op, $choice, true);
					break;

				case self::TERM_HIDDEN_STATUS:
					if ($op == 'not') {
						$wheres[] = '(feedback.hidden_status IS NULL OR ' . $this->_stringMatch('feedback.hidden_status', $op, $choice) . ')';
					} else {
						$wheres[] = $this->_stringMatch('feedback.hidden_status', $op, $choice);
					}
					break;

				case self::TERM_STATUS:

					$cats = array();
					$types = array();
					$hidden_types = array();

					foreach ((array)$choice as $c) {
						if (strpos($c, '.') !== false) {
							list ($hidden, $c) = explode('.', $c, 2);
						} else {
							$hidden = false;
						}
						if ($hidden === 'hidden') {
							$hidden_types[] = $c;
						} else if (ctype_digit($c)) {
							$cats[] = $c;
						} else {
							$types[] = $c;
							if ($c == 'hidden') {
								$this->include_hidden = true;
							}
						}
					}

					// Visible is a special type name
					if (($k = array_search('visible', $types)) !== false) {
						unset($types[$k]);
						$types = array_merge($types, array('new', 'active', 'closed'));
						$types = array_unique($types);
					}

					$part_where = array();
					if ($cats) {
						$part_where[] = $this->_choiceMatch('feedback.status_category_id', $op, $cats);
					}
					if ($types) {
						$part_where[] = $this->_stringMatch('feedback.status', $op, $types);
					}
					if ($hidden_types) {
						$part_where[] = "(feedback.status = 'hidden' AND " . $this->_stringMatch('feedback.hidden_status', $op, $types) . ')';
					}

					if ($hidden_types) {
						$this->include_hidden = true;
					}

					$part_where = "(" . implode(' OR ', $part_where) . ")";

					$wheres[] = $part_where;

					break;

				case self::TERM_QUERY:

					$string = $choice['query'];
					$type = !empty($choice['type']) ? $choice['type'] : 'phrase';

					if (!$string) {
						break;
					}

					$w = array();
					$w[] = '(' . $this->_stringSearch("feedback.title", $op, $string, $type) . ')';
					$w[] = '(' . $this->_stringSearch("feedback.content", $op, $string, $type) . ')';

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
							$ids = array_merge($ids, App::getEntityRepository('DeskPRO:FeedbackCategory')->getIdsInTree($id, true));
						}
					}

					$ids = array_unique($ids);

					$wheres[] = $this->_choiceMatch('feedback.category_id', $op, $ids);

					$this->summary[] = $this->_choiceSummary('Category', $op, $choice, function($choice) {
						$titles = App::getEntityRepository('DeskPRO:FeedbackCategory')->getNames((array)$choice);
						return $titles;
					});
					break;

				case self::TERM_STATUS_CATEGORY:
					$ids = (array)$choice;
					$ids = array_unique($ids);

					$wheres[] = $this->_choiceMatch('feedback.status_category_id', $op, $ids);

					$this->summary[] = $this->_choiceSummary('Status Category', $op, $choice, function($choice) {
						$titles = App::getEntityRepository('DeskPRO:FeedbackStatusCategory')->getNames((array)$choice);
						return $titles;
					});
					break;

				case self::TERM_NUM_RATINGS:
					$wheres[] = $this->_rangeMatch('feedback.num_ratings', $op, $choice);
					break;

				case self::TERM_DATE_CREATED:
					$wheres[] = $this->_dateMatch('idaes.date_created', $op, $choice);
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

					switch ($op) {
						case self::OP_IS:
							$joins[] = array(
								'labels_feedback',
								"LEFT JOIN labels_feedback AS $join_name ON ($join_name.feedback_id = feedback.id)"
							);
							$wheres[] = "$join_name.label = " . $db->quote($choice);
							break;
						case self::OP_NOT:
							$joins[] = array(
								'labels_feedback',
								"LEFT JOIN labels_feedback AS $join_name ON ($join_name.feedback_id = feedback.id AND $join_name.label = '.$db->quote($choice).')"
							);
							$wheres[] = "$join_name.person_id IS NULL";
							break;
						case self::OP_CONTAINS:
							$joins[] = array(
								'labels_feedback',
								"LEFT JOIN labels_feedback AS $join_name ON ($join_name.feedback_id = feedback.id)"
							);
							$wheres[] = "$join_name.label IN ($choices_in)";
							break;

						case self::OP_NOTCONTAINS:
							$joins[] = array(
								'labels_feedback',
								"LEFT JOIN labels_feedback AS $join_name ON ($join_name.feedback_id = feedback.id AND $join_name.label IN ($choices_in)"
							);
							$wheres[] = "$join_name.person_id IS NULL";
							break;
					}
					break;// end labels
			}
		}

		$joins = array_unique($joins);

		return array(
			'joins' => $joins,
			'wheres' => $wheres
		);
	}
}
