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
use Application\DeskPRO\Entity;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\People\PersonContextInterface;

use Orb\Log\Logger;

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Arrays;

abstract class SearcherAbstract implements PersonContextInterface
{
	const OP_IS          = 'is';
	const OP_NOT         = 'not';
	const OP_LT          = 'lt';
	const OP_GT          = 'gt';
	const OP_LTE         = 'lte';
	const OP_GTE         = 'gte';
	const OP_BETWEEN     = 'between';
	const OP_CONTAINS    = 'contains';
	const OP_NOTCONTAINS = 'notcontains';
	const OP_NOOP        = null;

	const ORDER_ASC      = 'ASC';
	const ORDER_DESC     = 'DESC';

	/**
	 * The person context
	 * @var Entity\Person
	 */
	protected $person = array();

	/**
	 * Array of terms we've set.
	 * term_type=>array(op_type, choice)
	 *
	 * @var array
	 */
	protected $terms = array();

	/**
	 * Array of terms we've set.
	 * term_type=>array(op_type, choice)
	 *
	 * @var array
	 */
	protected $terms_any = array();

	/**
	 * array(type, direction) of ordering
	 * @var array
	 */
	protected $order_by = array();

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;


	/**
	 * Get a logger instance
	 */
	public function getLogger()
	{
		if (!$this->logger) {
			$this->logger = new Logger();

			$search_name = strtolower(\Orb\Util\Util::getBaseClassname($this));
			if (dp_get_config('debug.enable_' . $search_name . '_log')) {
				$wr = new \Orb\Log\Writer\Stream(dp_get_log_dir() . '/' . $search_name . '.log');
				$this->logger->addWriter($wr);
			}
		}

		return $this->logger;
	}


	/**
	 * @param Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * Set the person context to fetch permissions etc for
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return void
	 */
	public function setPersonContext(Person $person)
	{
		$this->person = $person;
	}

	/**
	 * @depreciated Use setPersonContext
	 */
	public function setPerson($person)
	{
		$this->setPersonContext($person);
	}


	/**
	 * @return \Application\DeskPRO\Entity\Person|Entity\Person
	 */
	public function getPersonContext()
	{
		if ($this->person) {
			return $this->person;
		} else {
			return App::getCurrentPerson();
		}
	}



	/**
	 * Set an array of terms at once.
	 *
	 * @param array $terms
	 */
	public function setTerms(array $terms)
	{
		$this->terms = array_merge($this->terms, $terms);
	}


	/**
	 * Set an array of terms at once.
	 *
	 * @param array $terms
	 */
	public function setTermsAny(array $terms)
	{
		$this->terms_any = array_merge($this->terms_any, $terms);
	}



	/**
	 * Get the current terms
	 *
	 * @return array
	 */
	public function getTerms()
	{
		return $this->terms;
	}


	/**
	 * @param $term
	 * @param bool $all Find all or just the first?
	 * @return mixed
	 */
	public function findTerm($term, $all = false)
	{
		$ret = array();

		foreach ($this->terms as $info) {
			if ($info[0] == $term) {
				if ($all) {
					$ret[] = $info;
				} else {
					return $info;
				}
			}
		}

		if (!$all) {
			return null;
		}

		return $ret;
	}


	/**
	 * @param $term
	 * @param bool $all Find all or just the first?
	 * @return mixed
	 */
	public function findAnyTerm($term, $all = false)
	{
		$ret = array();

		foreach ($this->terms_any as $info) {
			if ($info[0] == $term) {
				if ($all) {
					$ret[] = $info;
				} else {
					return $info;
				}
			}
		}

		if (!$all) {
			return null;
		}

		return $ret;
	}


	/**
	 * Get the current terms
	 *
	 * @return array
	 */
	public function getTermsAny()
	{
		return $this->terms_any;
	}


	/**
	 * Get an array of fields the searcher has set. (so just the 'type' codes, not op/choices).
	 *
	 * @return array
	 */
	public function getTermFields()
	{
		$terms = $this->getTerms();
		$fields = array();
		foreach ($terms as $info) {
			$fields[] = $info[0];
		}

		return $fields;
	}



	/**
	 * Set the ordering
	 *
	 * @param string $type
	 * @param string $direction
	 */
	public function setOrderBy($type, $direction = self::ORDER_DESC)
	{
		$this->order_by = array($type, $direction);
	}



	/**
	 * Get the current order by
	 *
	 * @return array
	 */
	public function getOrderBy()
	{
		return $this->order_by;
	}



	/**
	 * Set orderBy using a 'code' which is "type:direction" such as "ticket.id:asc".
	 *
	 * @param string $order_by_code
     * @param string $separator
	 */
	public function setOrderByCode($order_by_code, $separator = ':')
	{
		if ($order_by_code == 'newest') {
			$order_by_code = 'date_created:desc';
		}
		if ($order_by_code == 'most-voted') {
			$order_by_code = 'num_ratings:desc';
		}

		if (strpos($order_by_code, $separator) === false) {
			$order_by_code .= $separator . self::ORDER_DESC;
		}

		list($type, $direction) = explode($separator, $order_by_code);

		$this->setOrderBy($type, $direction);
	}



	/**
	 * Add a new term.
	 *
	 * @param  $term
	 * @param  $op
	 * @param  $data
	 */
	public function addTerm($term, $op, $data)
	{
		$this->terms[] = array($term, $op, $data);
	}



	/**
	 * Add a new term.
	 *
	 * @param  $term
	 * @param  $op
	 * @param  $data
	 */
	public function addAnyTerm($term, $op, $data)
	{
		$this->terms_any[] = array($term, $op, $data);
	}



	/**
	 * Run the search and return an array of matching ID's.
	 *
	 * @param int $limit
	 * @return array
	 */
	abstract public function getMatches();



	/**
	 * Build a "where" part given two dates.
	 *
	 * $choice should be an array, first value date1 and second is date2.
	 * If not datetime objects, they're considered to mean "seconds in the past" and will be calculated.
	 *
	 * @param  $field
	 * @param  $op
	 * @param \DateTime|null $date1
	 * @param \DateTime|null $date2
	 * @return string
	 */
	protected function _dateMatch($field, $op, $choice)
	{
		$where = '';

		if (!is_array($choice)) {
			$choice = array($choice);
		}

		$timezone_context = new \DateTimeZone('UTC');
		if ($this->person) {
			$timezone_context = $this->person->getDateTimezone();
		}

		$date1 = null;
		if (!empty($choice['date1'])) {
			$date1 = $choice['date1'];
		} else if (!empty($choice['date1_relative']) AND !empty($choice['date1_relative_type'])) {
			$date1 = date_create("-" . (int)$choice['date1_relative'] . " {$choice['date1_relative_type']}", $timezone_context);
		} else if (!empty($choice[0])) {
			$date1 = $choice[0];
		}

		$date2 = null;
		if (!empty($choice['date2'])) {
			$date2 = $choice['date2'];
		} else if (!empty($choice['date2_relative']) AND !empty($choice['date2_relative_type'])) {
			$date2 = date_create("-" . (int)$choice['date2_relative'] . " {$choice['date2_relative_type']}", $timezone_context);
		} else if (!empty($choice[1])) {
			$date2 = $choice[1];
		}

		if ($date1 AND !($date1 instanceof \DateTime)) {
			$date1 = new \DateTime("@{$date1}");
			$date1->setTimezone($timezone_context);
		}
		if ($date2 AND !($date2 instanceof \DateTime)) {
			$date2 = new \DateTime("@{$date2}");
			$date2->setTimezone($timezone_context);
		}

		// There should always be at least one date
		if ($date1 === null AND $date2 === null) {
			return '0';
		}

		if ($date1) $date1 = \Orb\Util\Dates::convertToUtcDateTime($date1);
		if ($date2) $date2 = \Orb\Util\Dates::convertToUtcDateTime($date2);

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		// Between with only one date is invalid, so
		// we'll decide which op we really want to do
		if ($op == self::OP_BETWEEN && ($date1 === null or $date2 === null)) {
			if ($date1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if ($op == self::OP_BETWEEN) {
			if ($date1 > $date2) {
				$tmp = $date2;
				$date2 = $date1;
				$date1 = $tmp;
			}
			$where = "$field BETWEEN '" . $date1->format('Y-m-d H:i:s') . "' AND '" . $date2->format('Y-m-d H:i:s') . "'";
		} elseif ($op == self::OP_GTE) {
			$date = Util::coalesce($date1, $date2);
			$where = "$field >= '" . $date->format('Y-m-d H:i:s') . "'";
		} else {
			$date = Util::coalesce($date1, $date2);
			$where = "$field <= '" . $date->format('Y-m-d H:i:s') . "'";
		}

		return $where;
	}



	/**
	 * Does a string match. If op is of the 'contains' type, then a LIKE is performed.
	 *
	 * @param  $field
	 * @param  $op
	 * @param  $choice
	 * @return string
	 */
	protected function _stringMatch($field, $op, $choice, $suffix_only = false, $force_like = false)
	{
		$where = '';

		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		$db = App::getDbRead();
		if (!$force_like AND ($op == self::OP_IS OR $op == self::OP_NOT)) {
			$choices_in = (array)$choice;
			array_walk($choices_in, function(&$v, $k) use ($db) {
				$v = $db->quote($v);
			});

			$choices_in = "(" . implode(',', $choices_in) . ")";

			if ($op == self::OP_IS) {
				$where = "$field IN $choices_in";
			} elseif ($op == self::OP_NOT) {
				$where = "$field NOT IN $choices_in";
			}

		} else {
			$choices_in = (array)$choice;
			array_walk($choices_in, function(&$v, $k) use ($db, $suffix_only) {
				if ($suffix_only) {
					$v = $db->quote($v . '%');
				} else {
					$v = $db->quote('%' . $v . '%');
				}
			});

			if ($op == self::OP_CONTAINS) {
				$where = "($field LIKE " . implode(" OR $field LIKE ", $choices_in) . ")";
			} else {
				$where = "($field NOT LIKE " . implode(" AND $field NOT LIKE ", $choices_in) . ")";
			}
		}

		return $where;
	}


	/**
	 * Does a string search.
	 *
	 * @param $field
	 * @param $op
	 * @param $string
	 * @param string $type
	 */
	protected function _stringSearch($field, $op, $string, $type = 'or')
	{
		$string = Strings::utf8_strtolower($string);
		$db = App::getDbRead();

		if ($op == self::OP_NOT || $op == self::OP_NOTCONTAINS) {
			$op_like = 'NOT LIKE';
		} else {
			$op_like = 'LIKE';
		}

		if ($type == 'or' || $type == 'and') {
			$words = explode(' ', $string);
			$words = Arrays::func($words, 'trim');
			$words = Arrays::removeFalsey($words);
			$words = array_unique($words);

			if (!$words) {
				return '1';
			}

			$where = array();
			foreach ($words as $w) {
				$where[] = "($field $op_like " . $db->quote('%' . str_replace(array('%', '_'), array('\\%', '\\_'), $w) . '%') . ")";
			}

			if ($type == 'or') {
				$where = '(' . implode(' OR ', $where) . ')';
			} else {
				$where = '(' . implode(' AND ', $where) . ')';
			}

			return $where;
		} else {
			if (!$string) {
				return '1';
			}

			return "($field $op_like " . $db->quote('%' . str_replace(array('%', '_'), array('%%', '__'), $string) . '%') . ")";
		}
	}


	/**
	 * @param $field
	 * @param $op
	 * @param $string
	 */
	protected function _fulltextSearch($field, $op, $string)
	{
		$not = '';
		if ($op == self::OP_NOT || $op == self::OP_NOTCONTAINS) {
			$not = 'NOT';
		}

		$string_q = App::getDbRead()->quote($string, \PDO::PARAM_STR);
		return "( $not MATCH ($field) AGAINST ($string_q IN BOOLEAN MODE) )";
	}



	/**
	 * Get a summary string for a term
	 *
	 * @param  $field
	 * @param  $op
	 * @param  $choice
	 * @return string
	 */
	protected function _rangeSummary($field, $op, $choice)
	{
		$summary = '';

		$choice = (array)$choice;
		$choice = array_values($choice);

		$range1 = !empty($choice[0]) ? $choice[0] : null;
		$range2 = !empty($choice[1]) ? $choice[1] : null;

		// There should always be at least one
		if ($range1 === null AND $range2 === null) {
			return '';
		}

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		if ($op == self::OP_BETWEEN && ($range1 === null or $range2 === null)) {
			if ($range1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if ($op == self::OP_IS) {
			$summary = App::getTranslator()->phrase('agent.general.x_is_y', array(
				'field' => $field,
				'value1' => $range1,
				'value2' => $range2
			));
		} elseif ($op == self::OP_BETWEEN) {
			$summary = App::getTranslator()->phrase('agent.general.x_is_between_y_and_z', array(
				'field' => $field,
				'value1' => $range1,
				'value2' => $range2
			));
		} elseif ($op == self::OP_GTE) {
			$summary = App::getTranslator()->phrase('agent.general.x_is_greater_than_y', array(
				'field' => $field,
				'value' => $range1,
			));
		} else {
			$summary = App::getTranslator()->phrase('agent.general.x_is_less_than_y', array(
				'field' => $field,
				'value' => $range1,
			));
		}

		return $summary;
	}


	/**
	 * Get summary of the range summary
	 *
	 * @param $field
	 * @param $op
	 * @param $choice
	 * @return string
	 */
	public function _dateRangeSummary($field, $op, $choice)
	{
		$summary = '';

		$choice = (array)$choice;

		$date1 = null;
		$date1_relative = null;
		if (!empty($choice['date1'])) {
			$date1 = $choice['date1'];
		} else if (!empty($choice['date1_relative']) AND !empty($choice['date1_relative_type'])) {
			$date1_relative = (int)$choice['date1_relative'] . " {$choice['date1_relative_type']} ago";
		} else if (!empty($choice[0])) {
			$date1 = $choice[0];
		}

		$date2 = null;
		$date2_relative = null;
		if (!empty($choice['date2'])) {
			$date2 = $choice['date2'];
		} else if (!empty($choice['date2_relative']) AND !empty($choice['date2_relative_type'])) {
			$date2_relative = (int)$choice['date2_relative'] . " {$choice['date2_relative_type']} ago";
		} else if (!empty($choice[1])) {
			$date2 = $choice[1];
		}

		if (!($date1 instanceof \DateTime)) {
			$date1 = new \DateTime("@" . intval($date1));
		}
		if (!($date2 instanceof \DateTime)) {
			$date2 = new \DateTime("@" . intval($date2));
		}

		// There should always be at least one date
		if ($date1 === null AND $date2 === null) {
			return '';
		}

		if ($date1 === null AND $date2 !== null) {
			$date1 = $date2;
			$date1_relative = $date2_relative;
			$date2 = null;
			$date2_relative = null;
		}

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		if ($op == self::OP_BETWEEN && ($date1 === null or $date2 === null)) {
			if ($date1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if ($op == self::OP_BETWEEN) {
			if ($date1 > $date2) {
				$tmp = $date2;
				$date2 = $date1;
				$date1 = $tmp;

				$tmp = $date2_relative;
				$date2_relative = $date1_relative;
				$date1_relative = $tmp;
			}

			$summary = App::getTranslator()->phrase('agent.general.x_is_between_y_and_z', array(
				'field' => $field,
				'value1' => $date1_relative ? $date1_relative : $date1->format('M j, Y'),
				'value2' => $date2_relative ? $date2_relative : $date2->format('M j, Y')
			));
		} elseif ($op == self::OP_GTE) {
			$summary = App::getTranslator()->phrase('agent.general.x_after_y', array(
				'field' => $field,
				'value' => $date1_relative ? $date1_relative : $date1->format('M j, Y'),
			));
		} else {
			$summary = App::getTranslator()->phrase('agent.general.x_before_y', array(
				'field' => $field,
				'value' => $date1_relative ? $date1_relative : $date1->format('M j, Y'),
			));
		}

		return $summary;
	}


	/**
	 * Build a where part for a range field (date/integer).
	 *
	 * @param  $field
	 * @param  $op
	 * @param  $choice
	 * @return string
	 */
	protected function _rangeMatch($field, $op, $choice)
	{
		$where = '1';

		$choice = (array)$choice;
		$range1 = !empty($choice[0]) ? $choice[0] : null;
		$range2 = !empty($choice[1]) ? $choice[1] : null;

		// There should always be at least one date
		if ($range1 === null AND $range2 === null) {
			return '1';
		}

		$range1 = (int)$range1;
		$range2 = (int)$range2;

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		if ($range1 && $range2) {
			$op = self::OP_BETWEEN;
		}

		// Between with only one date is invalid, so
		// we'll decide which op we really want to do
		if ($op == self::OP_BETWEEN && ($range1 === null or $range2 === null)) {
			if ($range1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if ($op == self::OP_IS) {
			$where = "$field = $range1";
		} elseif ($op == self::OP_BETWEEN) {
			$where = "$field BETWEEN $range1 AND $range2";
		} elseif ($op == self::OP_GTE) {
			$range1 = Util::coalesce($range1, $range2);
			$where = "$field >= $range1";
		} else {
			$range1 = Util::coalesce($range1, $range2);
			$where = "$field <= $range1";
		}

		return $where;
	}



	/**
	 * Build a "where" part on simple fields given a choice.
	 *
	 * $is_id treats 0-values as NULL.
	 *
	 * @param  $field
	 * @param  $op
	 * @param  $choice
	 * @return string
	 */
	protected function _choiceMatch($field, $op, $choice, $is_id = false)
	{
		$db = App::getDbRead();
		$where = '';

		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		// Normalize op
		if (is_array($choice)) {
			if ($op == self::OP_IS) $op = self::OP_CONTAINS;
			if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;
		} else {
			if ($op == self::OP_CONTAINS) $op = self::OP_IS;
			if ($op == self::OP_NOTCONTAINS) $op = self::OP_NOT;
		}

		if (is_array($choice)) {

			$choices_in = $choice;
			array_walk($choices_in, function(&$v, $k) use ($db) {
				$v = $db->quote($v);
			});

			if (empty($choices_in)) {
				$choices_in = array(0);
			}

			$choices_in = "(" . implode(',', $choices_in) . ")";

			if ($op == self::OP_CONTAINS) {
				$where = "$field IN $choices_in";
			} elseif ($op == self::OP_NOTCONTAINS) {
				$where = "$field NOT IN $choices_in";
			}

		} else {
			if ($is_id AND ($choice === 0 OR $choice === '0')) {
				$choice = 'NULL';
				$op = ($op == self::OP_IS) ? "IS" : "IS NOT";
			}
			else {
				$choice = $db->quote($choice);
				$op = ($op == self::OP_IS) ? "=" : "!=";
			}

			$where = "$field $op $choice";
		}

		return $where;
	}



	/**
	 * @param  $field
	 * @param  $op
	 * @param  $choice
	 * @param bool $is_id
	 * @return string
	 */
	protected function _choiceSummary($field, $op, $choice, $title_callback = null, $always_choice = false)
	{
		$summary = '';

		if ($choice === null || $choice === '') {
			return '';
		}

		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		// Normalize op
		if (is_array($choice)) {
			if ($op == self::OP_IS) $op = self::OP_CONTAINS;
			if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;
		} else {
			if ($op == self::OP_CONTAINS) $op = self::OP_IS;
			if ($op == self::OP_NOTCONTAINS) $op = self::OP_NOT;
		}

		if ($always_choice) {
			if ($op == self::OP_IS) $op = self::OP_CONTAINS;
			if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;
		}

		if ($title_callback) {
			$title = call_user_func($title_callback, $choice, $field);
			if ($choice == '0' || (is_array($choice) && in_array('0', $choice))) {
				Arrays::unshiftAssoc($title, '0', 'None');
			}
		} else {
			$title = $choice;
		}

		if (is_array($title)) {
			$last = array_pop($title);
			$title = implode(', ', (array)$title);
			if ($title) {
				$title .= ' or ';
			}
			$title .= $last;
		}

		switch ($op) {
			case self::OP_IS:
				$summary = App::getTranslator()->phrase('agent.general.x_is_y', array('field' => $field, 'value' => $title));
				break;
			case self::OP_NOT:
				$summary = App::getTranslator()->phrase('agent.general.x_is_not_y', array('field' => $field, 'value' => $title));
				break;
			case self::OP_CONTAINS:
				$summary = App::getTranslator()->phrase('agent.general.x_is_y', array('field' => $field, 'value' => $title));
				break;
			case self::OP_NOTCONTAINS:
				$summary = App::getTranslator()->phrase('agent.general.x_is_not_y', array('field' => $field, 'value' => $title));
				break;
		}

		return $summary;
	}


	protected function _normalizeOpAndChoice(&$op, &$choice)
	{
		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		// Normalize op
		if (is_array($choice)) {
			if ($op == self::OP_IS) $op = self::OP_CONTAINS;
			if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;
		} else {
			if ($op == self::OP_CONTAINS) $op = self::OP_IS;
			if ($op == self::OP_NOTCONTAINS) $op = self::OP_NOT;
		}
	}

	public function normalizeWaitingTime($choice)
	{
		if (is_array($choice)) {
			$choice = Arrays::removeFalsey($choice);
			if (!$choice) {
				return 0;
			}
			if (isset($choice['date1']) || isset($choice['date2'])) {
				return $choice;
			} elseif (empty($choice['waiting_time']) || empty($choice['waiting_time_unit'])) {
				return 0;
			}
		}

		if (!$choice) {
			return 0;
		}

		return $choice;
	}


	protected function _normalizeAgentChoice($choice)
	{
		 if (!is_array($choice)) {
			 $choice = array($choice);
		 }

		$agent_ids = array();
		$not_id = null;
		$unassigned = false;

		foreach ($choice as $c) {
			$c = (int)$c;
			if ($c === 0) {
				$unassigned = true;
			} elseif ($c == -1) {
				if ($this->getPersonContext()) {
					$agent_ids[] = $this->getPersonContext()->getId();
				} else {
					$agent_ids[] = -1;
				}
			} elseif ($c == -2) {
				if ($this->getPersonContext()) {
					$not_id = $this->getPersonContext()->getId();
				} else {
					$not_id = -1;
				}
			} else {
				$agent_ids[] = $c;
			}
		}

		return array(
			'agent_ids' => $agent_ids,
			'not_id' => $not_id,
			'unassigned' => $unassigned
		);
	}

	protected function _normalizeAgentTeamChoice($choice)
	{
		$choice = (array)$choice;

		$team_ids = array();
		$not_ids = null;
		$no_team = false;

		if ($this->getPersonContext()) {
			$agent = $this->getPersonContext();
			$agent->loadHelper('AgentTeam');
		} else {
			$agent = null;
		}

		foreach ($choice as $c) {
			$c = (int)$c;
			if ($c === 0) {
				$no_team = true;
			} elseif ($c == -1) {
				if ($agent) {
					$team_ids = array_merge($team_ids, Arrays::removeFalsey($agent->getAgentTeamIds()));
				}
				$team_ids[] = -1;
			} elseif ($c == -2) {
				if ($agent) {
					$not_ids = array_merge($team_ids, Arrays::removeFalsey($agent->getAgentTeamIds()));
				}
				$not_ids[] = -1;
			} else {
				$team_ids[] = $c;
			}
		}

		return array(
			'team_ids' => $team_ids,
			'not_ids' => $not_ids,
			'no_team' => $no_team
		);
	}


	###################################################################
	# test methods test terms statically against some value
	###################################################################

	protected function _testDateMatch($value, $op, $choice)
	{
		$choice = (array)$choice;

		$date1 = null;
		if (isset($choice['date1'])) {
			$date1 = $choice['date1'];
		} else if (isset($choice[0])) {
			$date1 = $choice[0];
		}

		$date2 = null;
		if (isset($choice['date2'])) {
			$date2 = $choice['date2'];
		} else if (isset($choice[1])) {
			$date2 = $choice[1];
		}

		if ($date1 AND !($date1 instanceof \DateTime)) {
			$date1 = new \DateTime("-{$date1} seconds");
		}
		if ($date2 AND !($date2 instanceof \DateTime)) {
			$date2 = new \DateTime("-{$date2} seconds");
		}

		// There should always be at least one date
		if ($date1 === null AND $date2 === null) {
			return false;
		}

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		// Between with only one date is invalid, so
		// we'll decide which op we really want to do
		if ($op == self::OP_BETWEEN && ($date1 === null or $date2 === null)) {
			if ($date1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if ($date1) $date1 = $date1->getTimestamp(); else $date1 = 0;
		if ($date2) $date2 = $date2->getTimestamp(); else $date2 = 0;

		if ($value instanceof \DateTime) {
			$value = $value->getTimestamp();
		} elseif (is_string($value) AND !ctype_digit($value)) {
			$value = strtotime($value);
		}

		if ($op == self::OP_BETWEEN) {
			return ($value >= $date1 AND $value <= $date2);
		} elseif ($op == self::OP_GTE) {
			$date = $date1 ? $date1 : $date2;
			return ($value >= $date);
		} else {
			$date = $date1 ? $date1 : $date2;
			return ($value <= $date);
		}
	}

	protected function _testStringMatch($value, $op, $choice, $suffix_only = false, $force_like = false)
	{
		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		if (!$force_like AND ($op == self::OP_IS OR $op == self::OP_NOT)) {
			$choices_in = (array)$choice;

			$found = false;
			foreach ($choices_in as $c) {
				if (strpos($value, $c) === 0) {
					$found = true;
					break;
				}
			}

			if ($op == self::OP_IS) {
				return $found;
			} else {
				return (!$found);
			}

		} else {

			$choices_in = (array)$choice;

			$found = false;
			foreach ($choices_in as $c) {
				if ($suffix_only) {
					if (\Orb\Util\Strings::endsWith($c, $value)) {
						$found = true;
						break;
					}
				} else {
					if (strpos($value, $c) !== false) {
						$found = true;
						break;
					}
				}
			}

			if ($op == self::OP_CONTAINS) {
				return $found;
			} else {
				return (!$found);
			}
		}
	}

	protected function _testRangeMatch($value, $op, $choice)
	{
		$choice = (array)$choice;
		$range1 = !empty($choice[0]) ? $choice[0] : null;
		$range2 = !empty($choice[1]) ? $choice[1] : null;

		// There should always be at least one date
		if ($range1 === null AND $range2 === null) {
			return false;
		}

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		// Between with only one date is invalid, so
		// we'll decide which op we really want to do
		if ($op == self::OP_BETWEEN && ($range1 === null or $range2 === null)) {
			if ($range1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if ($op == self::OP_BETWEEN) {
			return ($value >= $range1 AND $value <= $range2);
		} elseif ($op == self::OP_GTE) {
			$range1 = Util::coalesce($range1, $range2);
			return ($value >= $range1);
		} else {
			$range1 = Util::coalesce($range1, $range2);
			return ($value <= $range1);
		}
	}

	protected function _testChoiceMatch($value, $op, $choice, $is_id = false)
	{
		if (!$choice) {
			return false; // no choices, always fails!
		}

		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		// Normalize op
		if (is_array($choice)) {
			if ($op == self::OP_IS) $op = self::OP_CONTAINS;
			if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;
		} else {
			if ($op == self::OP_CONTAINS) $op = self::OP_IS;
			if ($op == self::OP_NOTCONTAINS) $op = self::OP_NOT;
		}

		if (is_array($choice)) {

			$found = in_array($value, (array)$choice);

			if ($op == self::OP_CONTAINS) {
				return $found;
			} elseif ($op == self::OP_NOTCONTAINS) {
				return !$found;
			}

		} else {

			$found = in_array($value, (array)$choice);

			if ($op == self::OP_IS) {
				return $found;
			} elseif ($op == self::OP_NOT) {
				return !$found;
			}
		}
	}


	/**
	 * Get the oppositve op
	 *
	 * @param string $op
	 * @return string
	 */
	public function invertOp($op)
	{
		switch ($op) {
			case self::OP_GT:          return self::OP_LT;
			case self::OP_GTE:         return self::OP_LTE;
			case self::OP_LT:          return self::OP_GT;
			case self::OP_LTE:         return self::OP_GTE;
			case self::OP_NOT:         return self::OP_IS;
			case self::OP_IS:          return self::OP_NOT;
			case self::OP_NOTCONTAINS: return self::OP_CONTAINS;
			case self::OP_CONTAINS:    return self::OP_NOTCONTAINS;
		}

		return null;
	}
}
