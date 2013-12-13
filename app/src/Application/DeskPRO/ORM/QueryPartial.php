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
 * @category ORM
 */

namespace Application\DeskPRO\ORM;

class QueryPartial
{
	protected $order_by = null;
	protected $order_dir = 'ASC';
	protected $first_result = null;
	protected $max_results = null;

	public function __construct() {}

	public function setOrderBy($order_by, $order_dir)
	{
		$this->order_by = $order_by;
		$this->order_dir = $order_dir;
		return $this;
	}

	public function setFirstResult($first_result)
	{
		$this->first_result = $first_result;
		return $this;
	}

	public function setMaxResults($max_results)
	{
		$this->max_results = $max_results;
		return $this;
	}

	public function getOrderBy()
	{
		return array($this->order_by, $this->order_dir);
	}

	public function getFirstResult()
	{
		return $this->first_result;
	}

	public function getMaxResults()
	{
		return $this->max_results;
	}

	public function applyToQuery(\Doctrine\ORM\Query $query)
	{
		if ($this->max_results) {
			$query->setMaxResults($this->max_results);
		}

		if ($this->first_result) {
			$query->setFirstResult($this->first_result);
		}
	}

	public function applyToQueryBuilder(\Doctrine\ORM\QueryBuilder $qb)
	{
		if ($this->max_results) {
			$qb->setMaxResults($this->max_results);
		}

		if ($this->first_result) {
			$qb->setFirstResult($this->first_result);
		}

		if ($this->order_by) {
			$qb->orderBy($this->order_by, $this->order_dir);
		}
	}
}
