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
 * Represents the results from a DPQL query (which may span multiple MySQL queries).
 */
class Results
{
	/**
	 * List of result sets. Each element is another array with 2 elements:
	 *  - 0: results set (multiple rows, with each row 0-base keyed)
	 *  - 1: split results row (0-based keyed array) or null for non-split results
	 *
	 * @var array
	 */
	protected $_results = array();

	/**
	 * Sets the results to a single result set
	 *
	 * @param array $results
	 */
	public function setResults(array $results)
	{
		$this->_results = array(0 => array($results, null));
	}

	/**
	 * Adds a split result set
	 *
	 * @param array $results
	 * @param array $split Row of data for the split header
	 */
	public function addSplitResults(array $results, array $split)
	{
		$this->_results[] = array($results, $split);
	}

	/**
	 * @return bool
	 */
	public function hasSplitResults()
	{
		$total = count($this->_results);

		if ($total > 1) return true;
		if ($total < 1) return false;

		return ($this->_results[0][1] !== null);
	}

	/**
	 * Gets all split result sets
	 *
	 * @return array
	 */
	public function getSplitResults()
	{
		return $this->_results;
	}

	/**
	 * Gets the single result set (errors if multiple result sets).
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function getResults()
	{
		if (!$this->_results) {
			return array();
		}

		if ($this->hasSplitResults()) {
			throw new \Exception("Has split results but trying to get base results");
		}

		return $this->_results[0][0];
	}
}