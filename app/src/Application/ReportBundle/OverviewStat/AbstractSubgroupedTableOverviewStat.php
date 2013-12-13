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
 * @subpackage
 */

namespace Application\ReportBundle\OverviewStat;

abstract class AbstractSubgroupedTableOverviewStat extends AbstractTableOverviewStat
{
	/**
	 * @var GroupingField
	 */
	protected $grouping_field;

	/**
	 * @var array
	 */
	protected $group_max = null;

	/**
	 * @var array
	 */
	protected $group_total = null;

	/**
	 * @var array
	 */
	protected $group_colors = null;


	/**
	 * @return string[]
	 */
	abstract public function getSubgroupTitles();


	/**
	 * @return int[]
	 */
	public function getGroupMax()
	{
		$this->_initGroupInfo();
		return $this->group_max;
	}


	/**
	 * @return int[]
	 */
	public function getGroupTotal()
	{
		$this->_initGroupInfo();
		return $this->group_total;
	}


	/**
	 * @return int[]
	 */
	public function getGroupColors()
	{
		$this->_initGroupInfo();
		return $this->group_colors;
	}


	/**
	 * @return void
	 */
	protected function _initGroupInfo()
	{
		if (!$this->grouping_field) {
			return;
		}

		if ($this->group_max !== null) {
			return;
		}

		$group_max = array();
		$group_total = array();

		foreach ($this->getValues() as $master_group => $sub_info) {
			$group_max[$master_group] = 0;
			$group_total[$master_group] = 0;
			foreach ($sub_info as $subid => $count) {
				$group_total[$master_group] += $count;
				if ($count > $group_max[$master_group]) {
					$group_max[$master_group] = $count;
				}
			}
		}

		$group_colors = \Orb\Util\Colors::getColorsForKeys(array_keys($this->getSubgroupTitles()));

		$this->group_max     = $group_max;
		$this->group_total   = $group_total;
		$this->group_colors  = $group_colors;
	}

	/**
	 * @return int
	 */
	public function getMax()
	{
		if (!$this->getValues()) {
			return 1;
		}

		if ($this->grouping_field) {
			return max($this->getGroupTotal());
		}

		return max($this->getValues());
	}
}