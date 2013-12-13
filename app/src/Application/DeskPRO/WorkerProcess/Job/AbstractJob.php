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
 * @subpackage WorkerProcess
 */

namespace Application\DeskPRO\WorkerProcess\Job;

use Application\DeskPRO\Log\Logger;

/**
 * A job completes some specific processing task.
 */
abstract class AbstractJob
{
	const DEFAULT_INTERVAL = 3600;

	/**
	 * @var \Orb\Util\OptionsArray
	 */
	protected $options;

	/**
	 * @var \Application\DeskPRO\Log\Logger
	 */
	protected $logger;

	final public function __construct(Logger $logger, array $options = null)
	{
		$this->options = new \Orb\Util\OptionsArray($options);
		$this->logger = $logger;
		$this->init();
	}


	protected function init() { }


	/**
	 * Run the task
	 */
	abstract public function run();


	/**
	 * Log a status message. These should include information about how many records
	 * processed etc.
	 *
	 * @param string $message
	 * @param array $details
	 */
	public function logStatus($message, array $details = array())
	{
		$details['flag'] = 'status';
		$this->logger->log($message, Logger::INFO, $details);
	}


	/**
	 * Get the logger for this job
	 *
	 * @return \Application\DeskPRO\Log\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}
}
