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

namespace Application\DeskPRO\Log;

use Application\DeskPRO\App;

class DbErrorLoggerQueue
{
	protected $waiting = array();

	private function __construct() { }

	/**
	 * Get the single instance of the queue
	 *
	 * @return DbErrorLoggerQueue
	 */
	public static function getInstance()
	{
		static $inst;
		if (!$inst) {
			$inst = new self();
		}

		return $inst;
	}


	/**
	 * Inits the queue once
	 */
	public static function initQueue()
	{
		static $has_init;
		if (!$has_init) {
			App::getDb()->getEventManager()->addEventListener(array(
				'onPostCommit', 'onPostRollback'
			), self::getInstance());
		}
	}


	/**
	 * Adds a queued log
	 *
	 * @param $logger
	 * @param $item
	 */
	public function add($logger, $item)
	{
		$this->waiting[] = array($logger, $item);
	}


	/**
	 * Flushes all waiting logs to be written
	 */
	public function flush()
	{
		if (!$this->waiting) {
			return;
		}

		foreach ($this->waiting as $info) {
			$logger = $info[0];
			$item = $info[1];

			$logger->logItem($item);
		}

		$this->waiting = array();
	}


	/**
	 * @see Application\DeskPRO\DBAL\Connection::commit
	 */
	public function onPostCommit()
	{
		try {
			$this->flush();
		} catch (\Exception $e){}
	}


	/**
	 * @see Application\DeskPRO\DBAL\Connection::rollback
	 */
	public function onPostRollback()
	{
		try {
			$this->flush();
		} catch (\Exception $e){}
	}
}