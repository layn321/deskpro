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

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Log\Logger;
use Orb\Log\LogItem;

/**
 * A Runner is a thing that knows how to run jobs.
 */
abstract class AbstractRunner
{
	/**
	 * A callback function code can hook into to init loggers
	 * @var callback
	 */
	protected $_init_logger_callback = null;

	/**
	 * Ana rray of already initialized jobs
	 * @var array
	 */
	protected $_job_cache = array();

	/**
	 * @var array
	 */
	protected $job_options;

	/**
	 * @var int
	 */
	protected $job_time_limit = 900;

	/**
	 * @var callable
	 */
	protected $post_job_callback;

	/**
	 * @var bool
	 */
	protected $halt_job_loop = false;


	/**
	 * @param $callback
	 */
	public function setPostJobCallback($callback)
	{
		$this->post_job_callback = $callback;
	}


	/**
	 * Signals that the job should break, even if there are still jobs to process.
	 */
	public function haltJobLoop()
	{
		$this->halt_job_loop = true;
	}


	/**
	 * Sets the options array to pass to jobs when they are run
	 *
	 * @param array $options
	 */
	public function setJobOptions(array $options)
	{
		$this->job_options = $options;
	}


	/**
	 * Run an array of jobs
	 *
	 * @param array $jobs
	 */
	public function runJobs($jobs)
	{
		foreach ($jobs as $job) {
			App::getDb()->avoidTimeout();
			$this->runJob($job);
			if ($this->halt_job_loop) {
				break;
			}
		}
	}



	/**
	 *
	 * @param \Application\DeskPRO\Entity\WorkerJob $worker_job
	 */
	public function runJob(Entity\WorkerJob $worker_job)
	{
		App::setCurrentPerson(null);
		unset($GLOBALS['DP_CRON_LOGGER']);

		if ($this->job_time_limit) {
			@set_time_limit($this->job_time_limit);
		}

		$job = $this->getJob($worker_job);
		$logger = $job->getLogger();
		$GLOBALS['DP_CRON_LOGGER'] = $logger;

		if ($worker_job->getIsCrashed()) {
			$logger->log("ERROR: Job appears to have crashed during the last run! The last run was started at " . $worker_job->last_start_date->format('Y-m-d H:i:s'), Logger::ERR, array('flag' => 'job_crash'));
		}

		$mtime_start = microtime(true);
		$logger->log("Job {$worker_job['id']} start", Logger::INFO, array('flag' => 'job_start'));

		$worker_job['last_start_date'] = new \DateTime();
		App::getDb()->update('worker_jobs', array('last_start_date' => date('Y-m-d H:i:s')), array('id' => $worker_job->getId()));

		$run_e = null;

		try {
			$job->run();
		} catch (\Exception $e) {
			$run_e = $e;

			// Roll back any transactions that might still be open
			try {
				while (App::getDb()->isTransactionActive()) {
					$logger->log("(Rolling back open transaction)", Logger::DEBUG);
					App::getDb()->rollback();
				}
			} catch (\Exception $e) {}
		}

		$mtime_end = microtime(true);
		$mtime_total = $mtime_end - $mtime_start;
		$mtime_total = sprintf("%.5f", $mtime_total);

		if ($run_e) {
			$logger->log(sprintf("Exception: %s[%d]: %s", get_class($run_e), $run_e->getCode(), $run_e->getMessage()), Logger::ERR);
			\DeskPRO\Kernel\KernelErrorHandler::handleException($run_e);
		} else {
			$worker_job['last_run_date'] = new \DateTime();
			App::getDb()->update('worker_jobs', array('last_run_date' => date('Y-m-d H:i:s')), array('id' => $worker_job->getId()));
		}

		$logger->log("Job {$worker_job['id']} done in {$mtime_total}s", Logger::INFO, array('flag' => 'job_end'));

		if ($this->job_time_limit) {
			@set_time_limit(0);
		}

		if ($this->post_job_callback) {
			call_user_func($this->post_job_callback, $this, $worker_job, $logger);
		}

		unset($GLOBALS['DP_CRON_LOGGER']);
	}



	/**
	 * Get the job
	 *
	 * @param \Application\DeskPRO\Entity\WorkerJob $job_worker
	 * @return \Application\DeskPRO\WorkerProcess\Job\AbstractJob
	 */
	public function getJob(Entity\WorkerJob $job_worker)
	{
		if (isset($this->_job_cache[$job_worker['id']])) {
			return $this->_job_cache[$job_worker['id']];
		}

		$logger = $this->getLoggerForWorkerJob($job_worker);
		$job = $job_worker->createJobObj($logger, $this->job_options);
		$this->_job_cache[$job_worker['id']] = $job;

		return $job;
	}



	/**
	 * Get a logger for a specific job to log its status/debug messages
	 *
	 * @param \Application\DeskPRO\Entity\WorkerJob $worker_job
	 * @return Logger
	 */
	public function getLoggerForWorkerJob(Entity\WorkerJob $worker_job)
	{
		$logger_session = $worker_job['id'] . '.' . microtime(true);
		$logger = App::createNewLogger('worker_job.' . $worker_job->id, $logger_session);

		$this->_initLogger($logger, $worker_job);
		if ($this->_init_logger_callback) {
			call_user_func($this->_init_logger_callback, $logger, $worker_job);
		}

		// Copy log lines to the process log in error controller,
		// so we can send the log in error reports
		\DeskPRO\Kernel\KernelErrorHandler::clearProcessLog();
		$wr = new \Orb\Log\Writer\Callback(function($log_item) {
			$message_line = "[%datetime% %priority_name%] %message%";

			foreach ($log_item as $k => $v) {
				if ($v instanceof \DateTime) {
					$v = $v->format('Y-m-d H:i:s');
				}

				if (is_scalar($v)) {
					$message_line = str_replace("%$k%", $v, $message_line);
				}
			}

			\DeskPRO\Kernel\KernelErrorHandler::addProcessLog($message_line);
		});
		$logger->addWriter($wr);

		return $logger;
	}


	/**
	 * Init the logger with any custom stuff etc
	 */
	public function _initLogger(Logger $logger, Entity\WorkerJob $worker_job)
	{
		// Empty hook method to init a logger with custom writers or filters
	}



	/**
	 * Set a custom callback function that helps init the logger.
	 */
	public function setCustomLoggerInit($fn)
	{
		$this->_init_logger_callback = $fn;
	}
}
