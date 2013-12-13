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

namespace Application\DeskPRO\WorkerProcess\Runner;

use Application\DeskPRO\Entity;
use Application\DeskPRO\Log\Logger;

/**
 * This runner continuously runs in a loop, used by the CheckableInterface items
 */
class CheckParallel extends ExecParallel
{
	protected $job_workers = array();

	protected $_should_stop = false;

	protected function init()
	{
		pcntl_signal(SIGTERM, array($this, '_handleTerm'));
		pcntl_signal(SIGINT, array($this, '_handleTerm'));
	}

	/**
	 * Called when we get term/int signals, which means we want to quit.
	 * So when that happens we should stop running jobs.
	 */
	public function _handleTerm()
	{
		$this->_should_stop = true;
	}

	public function runChunk(array $worker_jobs)
	{
		while (true) {

			if ($this->_should_stop) return;

			$usleep = 200000;

			foreach ($worker_jobs as $worker_job) {

				if ($this->_should_stop) return;

				$job = $this->getJob($job_worker);
				if (!($job instanceof \Application\DeskPRO\WorkerProcess\Job\CheckableInterface)) {
					continue;
				}
				if ($job->isReady()) {
					$this->runJob($job);
				}

				$usleep = max($usleep, $job->getReadyCheckDelay());
			}

			usleep($usleep);
		}
	}
}
