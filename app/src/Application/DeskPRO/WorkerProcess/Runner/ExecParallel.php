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
 * This runner forks and runs several jobs in parallel using exec().
 */
class ExecParallel extends Standard
{
	protected $_max_childs = 4;
	protected $_cmd_pattern;

	protected $_running_children = array();

	public function __construct($cmd_pattern, $max_children = 4)
	{
		$this->_cmd_pattern = $cmd_pattern;
		$this->_max_childs = $max_children;

		$this->init();
	}

	protected function init()
	{
		// empty hook method
	}


	public function runJobs($jobs)
	{
		if (!count($jobs)) {
			return;
		}

		if (!is_array($jobs)) {
			$got_jobs = $jobs;
			$jobs = array();

			foreach ($got_jobs as $j) $jobs[] = $j;
			unset($got_jobs);
		}

		// Get chunks that each child will work on
		$chunks = array_chunk($jobs, ceil(count($jobs) / $this->_max_childs));

		// Fork for each chunk
		foreach ($chunks as $chunk) {
			$this->_forkChunk($chunk);
		}

		// Wait for each child to finish
		do {
			if ($pid = pcntl_wait($status)) {
				$job_id = array_search($pid, $this->_running_children);
				unset($this->_running_children[$job_id]);
			}
			usleep(500000);
		} while ($this->_running_children);
	}

	protected function _forkChunk(array $jobs)
	{
		$pid = pcntl_fork();
		if ($pid === -1) {
			throw new \Exception('Could not fork');
		} elseif ($pid) {
			$this->_running_children[] = $pid;
		} else {
			$this->runChunk($jobs);
			exit(0);
		}
	}

	public function runChunk(array $jobs)
	{
		// If we got here, we're the child and can process the jobs
		foreach ($jobs as $job) {
			$this->runJob($job);
		}
	}

	public function runJob(Entity\WorkerJob $job)
	{
		passthru($this->getCmdForJob($job));
	}

	protected function getCmdForJob(Entity\WorkerJob $job)
	{
		$cmd = $this->_cmd_pattern;
		$cmd = str_replace('%job%', $job['id'], $cmd);

		return $cmd;
	}
}
