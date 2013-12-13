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

use Application\DeskPRO\App;

/**
 * This runner goes through all the cron jobs and executes them in their own process
 */
class ChildProcess extends AbstractRunner
{
	protected $is_verbose = false;
	public function setVerbose()
	{
		$this->is_verbose = true;
	}

	public function _initLogger(Logger $logger, Entity\WorkerJob $worker_job)
	{
		if ($this->is_verbose) {
			$out_writer = new \Orb\Log\Writer\Stream('php://stdout');
			$logger->addWriter($out_writer);
		}
	}

		/**
	 *
	 * @param Entity\WorkerJob $worker_job
	 */
	public function runJob(Entity\WorkerJob $worker_job)
	{
		$cmd = App::getConfig('php_path') . ' cmd.php dp:worker-job -j='.$worker_job->id;

		$process = new \Symfony\Component\Process\Process($cmd, DP_ROOT . '/../');
		$process->run();
	}
}
