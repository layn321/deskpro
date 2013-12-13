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
 * @subpackage Dpql
 */

namespace Application\DeskPRO\Dpql;

/**
 * This represents a SELECT query that will be passed to MySQL. It is used to
 * create a query in a non-linear fashion.
 */
class SqlSelect
{
	/**
	 * List of fields/expressions in the SELECT clause. Joined by commas.
	 *
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * Name of table for the FROM clause. This must be a table name
	 * rather than a full expression.
	 *
	 * @var string
	 */
	protected $_table;

	/**
	 * List of joins to add. Each join must be keyed by a unique identifier
	 * to prevent adding duplicates.
	 *
	 * @var array
	 */
	protected $_joins = array();

	/**
	 * List of conditions for the WHERE clause. These will be joined by ANDs.
	 *
	 * @var array
	 */
	protected $_conditions = array();

	/**
	 * List of expressions/fields for the GROUP BY clause. Joined by commas.
	 *
	 * @var array
	 */
	protected $_groupBy = array();

	/**
	 * List of expressions/fields for the ORDER BY clause. Joined by commas.
	 *
	 * @var array
	 */
	protected $_orderBy = array();

	/**
	 * The amount of rows to fetch. If null or 0, rows will not be limited.
	 *
	 * @var integer|null
	 */
	protected $_limitAmount = null;

	/**
	 * The number of rows to skip before returning results. If null or 0,
	 * rows will not be limited.
	 *
	 * @var integer|null
	 */
	protected $_limitOffset = null;

	/**
	 * @param string $table
	 */
	public function setTable($table)
	{
		$this->_table = $table;
	}

	/**
	 * @return string
	 */
	public function getTable()
	{
		return $this->_table;
	}

	/**
	 * Adds a field to the select list. This returns a 1-based index
	 * identifying the position of the column being selected. This is
	 * 1-based to correspond with how MySQL returns results when returning
	 * number-based keys.
	 *
	 * @param string $string
	 *
	 * @return integer
	 */
	public function addSelectField($string)
	{
		$this->_fields[] = $string;
		$this->_lastFieldAdded = true;

		end($this->_fields);
		return key($this->_fields) + 1;
	}

	/**
	 * @return array
	 */
	public function getSelectFields()
	{
		return $this->_fields;
	}

	/**
	 * Gets the specified select field.
	 *
	 * @param integer $id The 1-based ID of the field to look up
	 *
	 * @return string|false False if the field cannot be found
	 */
	public function getSelectField($id)
	{
		return isset($this->_fields[$id - 1]) ? $this->_fields[$id - 1] : false;
	}

	/**
	 * @param string $name Unique identifier for the join
	 * @param string $string
	 *
	 * @return bool True if the join was added, false if the join was there already
	 */
	public function addJoin($name, $string)
	{
		if (isset($this->_joins[$name])) {
			return false;
		}

		$this->_joins[$name] = $string;
		return true;
	}

	/**
	 * Sets all joins
	 *
	 * @param array $joins
	 */
	public function setJoins(array $joins)
	{
		$this->_joins = $joins;
	}

	/**
	 * @return array
	 */
	public function getJoins()
	{
		return $this->_joins;
	}

	/**
	 * Sets all conditions
	 *
	 * @param array $conditions
	 */
	public function setConditions(array $conditions)
	{
		$this->_conditions = $conditions;
	}

	/**
	 * @param string $condition
	 */
	public function addCondition($condition)
	{
		$this->_conditions[] = $condition;
	}

	/**
	 * @return array
	 */
	public function getConditions()
	{
		return $this->_conditions;
	}

	/**
	 * @param string $string
	 */
	public function addGroupBy($string)
	{
		$this->_groupBy[] = $string;
	}

	/**
	 * @return array
	 */
	public function getGroupBy()
	{
		return $this->_groupBy;
	}

	/**
	 * @param string $string
	 */
	public function addOrderBy($string)
	{
		$this->_orderBy[] = $string;
	}

	/**
	 * @return array
	 */
	public function getOrderBy()
	{
		return $this->_orderBy;
	}

	/**
	 * Sets the limit clause.
	 *
	 * @param integer $amount
	 * @param integer|null $offset
	 */
	public function setLimit($amount, $offset = null)
	{
		$this->_limitAmount = $amount;
		if ($offset !== null) {
			$this->_limitOffset = $offset;
		}
	}

	/**
	 * @return int|null
	 */
	public function getLimitAmount()
	{
		return $this->_limitAmount;
	}

	/**
	 * @return int|null
	 */
	public function getLimitOffset()
	{
		return $this->_limitOffset;
	}

	/**
	 * Gets the results as runnable SQL (SELECT statement).
	 *
	 * @return string
	 */
	public function toSql()
	{
		if ($this->_limitAmount) {
			$limit = $this->_limitAmount . ($this->_limitOffset ? " OFFSET " . $this->_limitOffset : '');
		} else if ($this->_limitOffset) {
			$limit = '999999 OFFSET ' . $this->_limitOffset;
		} else {
			$limit = false;
		}

		return 'SELECT ' . implode(', ', $this->_fields)
			. "\nFROM `$this->_table`"
			. ($this->_joins ? "\n" . implode("\n", $this->_joins) : '')
			. ($this->_conditions ? "\nWHERE " . implode(' AND ', $this->_conditions) : '')
			. ($this->_groupBy ? "\nGROUP BY " . implode(', ', $this->_groupBy) : '')
			. ($this->_orderBy ? "\nORDER BY " . implode(', ', $this->_orderBy) : '')
			. ($limit ? "\nLIMIT $limit" : '');
	}

	/**
	 * Converts the object to a string (SQL SELECT statement).
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toSql();
	}

	/**
	 * Quotes the value as a literal string in SQL.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public function quoteForSql($value)
	{
		return \Application\DeskPRO\App::getDb()->quote($value);
	}
}