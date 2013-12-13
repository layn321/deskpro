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
 * Handler for defining how DPQL results should be formatted, including
 * how grouping is done and how the columns should be ordered/displayed.
 */
class ResultHandler
{
	/**
	 * List of columns that should be selected.
	 *
	 * Array needs these keys:
	 *  - title: Title of the column
	 *  - resultId: Column number from the SQL results used to display
	 *  - renderer: Null for default behavior, or a closure to control how to render the column
	 *
	 * Grouping arrays need these keys as well:
	 *  - groupResultId: Column number from SQL results that's used to group
	 *
	 * @var array[int]
	 */
	protected $_columns = array();

	/**
	 * List of columns that will be grouped on in the X direction.
	 * That is, this will be listed as headers from left to right.
	 * The use of X grouping causes the results to be a matrix table.
	 *
	 * See columns for array structure.
	 *
	 * @var array[int]
	 */
	protected $_groupXColumns = array();

	/**
	 * List of columns that will be grouped on in the Y direction.
	 * That is, this displays like MySQL's native GROUP BY.
	 *
	 * See columns for array structure.
	 *
	 * @var array[int]
	 */
	protected $_groupYColumns = array();

	/**
	 * @var array
	 */
	protected $_groupStackColumns = array();

	/**
	 * List of columns that will be used to split the results into
	 * separate tables.
	 *
	 * See columns for array structure, but without title.
	 *
	 * @var array[int]
	 */
	protected $_splitColumns = array();

	/**
	 * List of select column IDs that should be totaled
	 *
	 * @var array
	 */
	protected $_totalColumns = array();

	/**
	 * Adds a column that will be selected/output into the results.
	 *
	 * @param string $title
	 * @param integer $resultId
	 * @param \Closure|null $renderer
	 */
	public function addSelectColumn($title, $resultId, $renderer = null)
	{
		$this->_columns[] = array(
			'title' => $title,
			'resultId' => $resultId,
			'renderer' => $renderer
		);
	}

	/**
	 * Gets the columns that will be selected/output.
	 *
	 * @return array
	 */
	public function getSelectColumns()
	{
		return $this->_columns;
	}

	/**
	 * Adds a column that will be grouped in the Y direction (normal
	 * MySQL group by). Note that these columns should be selected/displayed
	 * before the normal select columns.
	 *
	 * @param string $title
	 * @param integer $groupResultId The ID of the column in the results that holds the grouping field value
	 * @param integer $resultId
	 * @param \Closure|null $renderer
	 */
	public function addGroupYColumn($title, $groupResultId, $resultId, $renderer = null)
	{
		$this->_groupYColumns[] = array(
			'title' => $title,
			'groupResultId' => $groupResultId,
			'resultId' => $resultId,
			'renderer' => $renderer
		);
	}

	/**
	 * Gets columns that will be used for Y grouping.
	 *
	 * @return array
	 */
	public function getGroupYColumns()
	{
		return $this->_groupYColumns;
	}

	/**
	 * Adds a column that will be grouped in the X direction to create
	 * a matrix table.
	 *
	 * @param string $title
	 * @param integer $groupResultId The ID of the column in the results that holds the grouping field value
	 * @param integer $resultId
	 * @param \Closure|null $renderer
	 */
	public function addGroupXColumn($title, $groupResultId, $resultId, $renderer = null)
	{
		$this->_groupXColumns[] = array(
			'title' => $title,
			'groupResultId' => $groupResultId,
			'resultId' => $resultId,
			'renderer' => $renderer
		);
	}

	/**
	 * Gets columns to be grouped in the X direction to create a matrix table.
	 *
	 * @return array
	 */
	public function getGroupXColumns()
	{
		return $this->_groupXColumns;
	}

	public function addGroupStackColumn($groupId, $printId)
	{
		$this->_groupStackColumns[] = array(
			'groupId' => $groupId,
			'printId' => $printId
		);
	}

	public function getGroupStackColumns()
	{
		return $this->_groupStackColumns;
	}

	/**
	 * Adds a column that will be used for splitting into separate tables.
	 *
	 * @param integer $resultId
	 * @param \Closure|null $renderer
	 */
	public function addSplitColumn($resultId, $renderer = null)
	{
		$this->_splitColumns[] = array(
			'resultId' => $resultId,
			'renderer' => $renderer
		);
	}

	/**
	 * Gets columns that are to be used to split results into tables.
	 *
	 * @return array
	 */
	public function getSplitColumns()
	{
		return $this->_splitColumns;
	}

	/**
	 * @param integer $id
	 */
	public function addTotalColumn($id)
	{
		$this->_totalColumns[$id] = $id;
	}

	/**
	 * @return array
	 */
	public function getTotalColumns()
	{
		return $this->_totalColumns;
	}
}