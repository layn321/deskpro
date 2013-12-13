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

namespace Application\DeskPRO\Search\SearcherResult;

use Application\DeskPRO\App;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Search adapter
 */
class ResultSet implements \Countable, \IteratorAggregate
{
	/**
	 * @var int
	 */
	protected $total = 0;

	/**
	 * Array of results
	 * @var Application\DeskPRO\Search\SearcherResult\ResultInterface[]
	 */
	protected $results = array();

	public function __construct($total, array $results)
	{
		$this->total = $total;
		$this->results = $results;
	}

	/**
	 * The total number of matched objects.
	 *
	 * @var int
	 */
	public function totalCount()
	{
		return $this->total;
	}


	/**
	 * How many results in this object? Note: NOT the same as total
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->results);
	}


	/**
	 * Get a result by index, or null if the index doesnt exist
	 *
	 * @return \Application\DeskPRO\Search\SearcherResult\ResultInterface
	 */
	public function getResult($i)
	{
		return isset($this->results[$i]) ? $this->results[$i] : null;
	}


	/**
	 * Get results
	 *
	 * @var \Application\DeskPRO\Search\SearcherResult\ResultInterface[]
	 */
	public function getResults()
	{
		return $this->results;
	}


	/**
	 * Get iterator
	 *
	 * @return \ArrayObject
	 */
	public function getIterator()
	{
		return new \ArrayObject($this->results);
	}


	/**
	 * If the result was cached, this is the cacheid we might use again.
	 * Null means no cacheid.
	 *
	 * @return mixed
	 */
	public function getCacheId()
	{
		return null;
	}
}
