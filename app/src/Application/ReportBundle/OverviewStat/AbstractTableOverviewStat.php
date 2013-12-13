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

use Orb\Log\Loggable;
use Orb\Log\Logger;

abstract class AbstractTableOverviewStat implements Loggable
{
	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * Gets a id => array(info) array of titles. Titles can have children.
	 *
	 * @abstract
	 * @return mixed
	 */
	abstract function getTitles();

	/**
	 * Gets an id => xxx of counts.
	 *
	 * @abstract
	 * @return mixed
	 */
	abstract function getValues();


	/**
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}


	/**
	 * @return int
	 */
	public function getMax()
	{
		if (!$this->getValues()) {
			return 1;
		}

		$max = max($this->getValues());

		if ($max < 3) {
			$max = 3;
		}

		return $max;
	}
}