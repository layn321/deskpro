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
*/

namespace Application\DeskPRO\Command;

use Orb\Util\Strings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Log\Logger;

class WorkerJobCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected $set_verbose = false;
	protected $ignore_interval = false;
	protected $output;
	protected $cron_id;

	protected function configure()
	{
		$this->setName('dp:worker-job')
			->addOption('job', 'j', InputOption::VALUE_REQUIRED, 'Run only specific job')
			->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Run only a specific group of jobs')
			->addOption('ignore-interval', 'f', InputOption::VALUE_NONE, 'Always run job(s) even if the job interval has not ellapsed since last run')
			->addOption('daemon', null, InputOption::VALUE_NONE, 'Runs forever. Only "checkable" jobs supported. php-exec option is required.')
			->addOption('php-exec', 'p', InputOption::VALUE_REQUIRED, 'Runs jobs as child processes using this path to PHP.')
			->addOption('options', 'o', InputOption::VALUE_REQUIRED, 'Specify a JSON-encoded array of options to pass to worker jobs')
			->addOption('info', null, InputOption::VALUE_NONE, 'Don\'t execute anything, just list info about scheduled tasks');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$time_cron_start = microtime(true);

		@ini_set('track_errors', true);

		$GLOBALS['DP_PREF_MAX_EXEC_TIME'] = 1800;
		@set_time_limit($GLOBALS['DP_PREF_MAX_EXEC_TIME']);

		$is_verbose = $output->getVerbosity() == OutputInterface::VERBOSITY_VERBOSE;

		if ($is_verbose && defined('DP_START_TIME')) {
			$output->writeln(sprintf("(Time to enter execute: %.4f)", $time_cron_start-DP_START_TIME));
		}

		if ($input->getOption('info')) {

			$jobs = App::getOrm()->createQuery("
				SELECT j
				FROM DeskPRO:WorkerJob j
				ORDER BY j.last_run_date ASC
			")->execute();

			$last_run = App::getSetting('core.last_cron_run');
			if (!$last_run) $last_run = 0;

			$time_since_run = time() - $last_run;
			$is_problem = false;
			if ($time_since_run > 301) {
				$is_problem = true;
			}

			if (!$last_run) {
				$output->writeln("Last run time: NEVER");
			} else {
				$output->writeln(sprintf("Last run time: %s (%s)", date('Y-m-d H:i:s', $last_run), \Orb\Util\Dates::secsToReadable(time()-$last_run, 5)));

				if ($is_problem) {
					$output->writeln("");
					$output->writeln("<info>Tasks have not completed successfully in a while which could indicate a problem. Try running this command with --verbose -f to force all jobs to run with output.</info>");
				}
			}

			$output->writeln("");

			$format = "%-30s  %-4s  %-16s  %-16s";
			$output->writeln(sprintf($format, "Job", "Int.", "Last Run", "Next Run"));
			$output->writeln(sprintf($format, str_repeat('=', 30), str_repeat('=', 4), str_repeat('=', 16), str_repeat('=', 16)));

			foreach ($jobs as $j) {
				$output->writeln(sprintf(
					"%-30s  %-4s  %-16s  %-16s",
					$j->id,
					$j->getIntervalReadable(),
					$j->last_run_date ? \Orb\Util\Dates::dateToAgo($j->last_run_date, 3, 'short') : 'Never',
					$j->next_run_date ? \Orb\Util\Dates::dateToAgo($j->next_run_date, 3, 'short') : 'NA'
				));
			}

			$output->writeln("");

			return 0;
		}

		#------------------------------
		# Clean up installer error detection
		#------------------------------

		if (file_exists(dp_get_log_dir().'/cron-boot-errors.log')) {
			@unlink(dp_get_log_dir().'/cron-boot-errors.log');
		}

		App::getDb()->delete('install_data', array('build' => 1, 'name' => 'cron_run_errors'));

		#------------------------------
		# Report fatal errors from logs
		#------------------------------

		if (!defined('DPC_IS_CLOUD')) {
			$date_cut = time() - 259200;
			$date_cut_min = time() - 172800;
			foreach (array('server-phperr-web.log', 'cli-phperr.log') as $logfile) {
				$logpath = dp_get_log_dir() . '/' . $logfile;
				if (!file_exists($logpath)) {
					continue;
				}

				$mtime = @filemtime($logpath);
				if (!$mtime || $mtime < $date_cut_min) {
					continue;
				}

				// One per day
				$check = (int)App::getDb()->fetchColumn("SELECT value FROM settings WHERE name = ?", array('core.cron_logreport.' . $logfile));
				if ($check && $check > $date_cut) {
					continue;
				}

				App::getDb()->replace('settings', array('name' => 'core.cron_logreport.' . $logfile, 'value' => time()));

				if ($is_verbose) {
					$output->writeln("Submitting $logfile log");
				}

				$log = file_get_contents($logpath);
				if (filesize($logpath) > 307200) {
					$log = substr($log, -307200);
				}

				// With this reporting we are trying to get notified of fatal errors that couldnt be
				// handled. If we handled the erorr properly, then the path would have been truncated.
				// So an easy way to check is by checking for the full file path and then getting the timestamp
				// of that log line.
				$last_pos = strrpos($log, DP_ROOT);
				if ($last_pos === false) {
					continue;
				}

				// Now try to find the line timestamp
				$last_timestamp = null;
				$x = 0;
				while ($x++ < 100) {
					$line_start = strrpos($log, "\n[", max($last_pos-1000, 0));
					$last_timestamp = Strings::extractRegexMatch('#\[((.*?)-(.*?)-(.*?) (.*?))\]#', substr($log, $line_start, 100));

					if ($last_timestamp) {
						$last_timestamp = @strtotime($last_timestamp);
						if ($last_timestamp) {
							break;
						}
					}
				}

				if (!$last_timestamp) {
					continue;
				}

				// And make sure the timestamp is after our last submission
				if ($last_timestamp > $date_cut_min) {
					continue;
				}

				$errinfo = array(
					'type'            => 'error',
					'session_name'    => '',
					'die'             => false,
					'pri'             => 'ERR',
					'trace'           => $log,
					'summary'         => 'PHP error log ('.$logfile.')',
					'errstr'          => 'PHP error log ('.$logfile.')',
					'errname'         => 'E_ERROR',
					'errno'           => 1,
					'errfile'         => $logfile,
					'errline'         => 1,
					'display'         => false,
					'build'           => defined('DP_BUILD_TIME') ? DP_BUILD_TIME : 0,
					'process_log'     => '',
					'context_data'    => '',
					'error_time'      => microtime(true),
					'time_to_error'   => 1
				);

				try {
					\Application\DeskPRO\Service\ErrorReporter::reportPhpError($errinfo);
				} catch (\Exception $e) {}
			}
		}

		#------------------------------
		# Run
		#------------------------------

		$time_start = microtime(true);
		if (!defined('DP_DISABLE_DBCRONLOG')) {
			App::getDb()->insert('log_items', array(
				'log_name' => 'worker_job.cron_runner',
				'session_name' => 'cron_runner.' . $time_start,
				'flag' => 'cron_start',
				'priority' => 6,
				'priority_name' => 'INFO',
				'message' => 'Cron runner started',
				'date_created' => date('Y-m-d H:i:s')
			));
		}
		App::getDb()->replace('settings', array('name' => 'core.last_cron_start', 'value' => time()));

		$cron_id = 'dp-cron';
		if ($input->getOption('job')) {
			$cron_id .= '-' . $input->getOption('job');
		} elseif ($input->getOption('group')) {
			$cron_id .= '-g-' . $input->getOption('group');
		}

		$GLOBALS['DP_CRON_ID'] = $cron_id;

		if (!$input->getOption('ignore-interval')) {
			$check = App::getDb()->fetchColumn("SELECT value FROM settings WHERE name = ?", array('core.croncheck.' . $cron_id));
			if ($check) {
				$date = (int)$check;
				$date_cut = time() - 900;
				$diff = \Orb\Util\Dates::secsToReadable(time() - $date_cut, 5);

				if ($date_cut < $date) {
					if ($input->getOption('verbose')) { $output->writeln("$cron_id is still active. Running for {$diff} (since " . date('Y-m-d H:i:s', $date) . ")"); }
					App::getDb()->insert('log_items', array(
						'log_name' => 'worker_job.cron_runner',
						'session_name' => 'cron_runner.' . $time_start,
						'flag' => 'cron_abort',
						'priority' => 6,
						'priority_name' => 'INFO',
						'message' => 'Cron runner aborted (still running)',
						'date_created' => date('Y-m-d H:i:s')
					));
					return 0;
				} else {

					App::getDb()->insert('log_items', array(
						'log_name' => 'worker_job.cron_runner',
						'session_name' => 'cron_runner.' . $time_start,
						'flag' => 'cron_resume',
						'priority' => 3,
						'priority_name' => 'ERR',
						'message' => "WARNING: Cron ($cron_id) has been active for {$diff}. Assuming crashed process, resuming.",
						'date_created' => date('Y-m-d H:i:s')
					));

					$title = "WARNING: Cron ($cron_id) has been active for {$diff}. Assuming crashed process, resuming.";

					$text = "Cron ($cron_id) has been marked as active for {$diff} (since " . date('Y-m-d H:i:s', $date) . ").\n\n"
							. "This is most likely caused by a fatal error that prevented the runner from resetting the timer.\n\n"
							. "Cron will now resume, but this is a problem you should investigate. Refer to the error log files and contact support@deskpro.com."
							. "\n\n"
							. "More information about this error can be found here: https://support.deskpro.com/kb/articles/170\n";

					$output->writeln($title);
					$output->writeln($text);

					$e = new Exception\CronRunningException($title);
					$e_info = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
					$e_info['email']             = true;
					$e_info['email_subject']     = $title;
					$e_info['email_body']        = $text;
					$e_info['email_throttle_id'] = 'email_error_cron_timeout';
					$e_info['attach_logs']       = true;
					\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($e_info);
				}
			}
		}

		App::getDb()->replace('settings', array(
			'name'  => 'core.croncheck.' . $cron_id,
			'value' => time()
		));

		\DpShutdown::add(function() {
			// Already done (clean shutdown)
			if (!isset($GLOBALS['DP_CRON_ID'])) {
				return;
			}

			try {
				$last_error = error_get_last();
				if (!$last_error && isset($GLOBALS['DP_LAST_ERROR'])) {
					$last_error = $GLOBALS['DP_LAST_ERROR'];
				} elseif (!$last_error && isset($php_errormsg) && $php_errormsg) {
					$last_error = array($php_errormsg);
				}

				if ($last_error) {
					$e = new \Exception("Cron did not shut down cleanly. Last error: " . implode("\n", $last_error));
					$e_info = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
					\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($e_info);
				} else {
					$e = new \Exception("Cron did not shut down cleanly");
					$e_info = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
					\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($e_info);
				}

				App::getDb()->delete('settings', array('name' => 'core.croncheck.' . $GLOBALS['DP_CRON_ID']));
			} catch (\Exception $e) {}
		});

		$step = (int)App::getSetting('core.setup_initial');

		// Only run crom if we've passed initial setup
		// This command will just execute nothing and set the last run time
		// so the system knows its been set up
		if ($step) {
			$ret = $this->doExecute($input, $output);
		} else {
			$ret = 0;
		}

		App::getDb()->delete('settings', array('name' => 'core.croncheck.' . $cron_id));
		App::getDb()->replace('settings', array('name' => 'core.last_cron_run', 'value' => time()));

		$done_time = microtime(true);
		if (!defined('DP_DISABLE_DBCRONLOG')) {
			App::getDb()->insert('log_items', array(
				'log_name' => 'worker_job.cron_runner',
				'session_name' => 'cron_runner.' . $time_start,
				'flag' => 'cron_end',
				'priority' => 6,
				'priority_name' => 'INFO',
				'message' => sprintf('Cron runner done. Took %.4f seconds.', $done_time-$time_start),
				'date_created' => date('Y-m-d H:i:s')
			));
		}

		unset($GLOBALS['DP_CRON_ID']);

		if ($is_verbose) {
			$output->writeln(sprintf("(Time until execute end: %.4f)", microtime(true)-$time_cron_start));
		}

		return $ret;
	}

	protected function doExecute(InputInterface $input, OutputInterface $output)
	{
		$options = null;
		if ($input->getOption('options')) {
			$options = json_decode($input->getOption('options'), true);
			if (!is_array($options)) {
				$output->writeln("<error>The options array is malformed</error>");
				return 1;
			}
		}
		if (!$options) {
			$options = array();
		}

		$verbose = $input->getOption('verbose');

		if (App::getSetting('core.helpdesk_disabled')) {
			if ($verbose) {
				$output->writeln("<info>Helpdesk is currently disabled.</info>");
			}

			return 0;
		}


		$ignore_interval = false;
		if ($input->getOption('ignore-interval')) {
			$ignore_interval = true;
		}

		if ($input->getOption('php-exec')) {
			$cmd = $input->getOption('php-exec') . ' "' . DP_ROOT . '/bin/console-dev" dp:worker-job '.($verbose ? '-v ' : '').'-f -j %job%';

			if ($input->getOption('daemon')) {
				$runner = new \Application\DeskPRO\WorkerProcess\Runner\CheckParallel($cmd);
			} else {
				$runner = new \Application\DeskPRO\WorkerProcess\Runner\ExecParallel($cmd);
			}
		} else {
			$runner = new \Application\DeskPRO\WorkerProcess\Runner\Standard();
		}

		$runner->setJobOptions($options);

		$runner->setPostJobCallback(function($runner, $worker_job, $logger) {
			$t = microtime(true) - DP_START_TIME;
			if ($t > 600) {
				$runner->haltJobLoop();
				$logger->log(sprintf("haltJobLoop after {$worker_job['id']} :: Time running: %.3fs", $t), Logger::WARN, array('flag' => 'halt_job_loop'));
			}

			// Reset the cron timer so we dont try and restart while we still run
			if (isset($GLOBALS['DP_CRON_ID']) && $GLOBALS['DP_CRON_ID']) {
				App::getDb()->replace('settings', array(
					'name'  => 'core.croncheck.' . $GLOBALS['DP_CRON_ID'],
					'value' => time()
				));
			}
		});

		if ($verbose) {
			$GLOBALS['DP_OUTPUT'] = $output;
			$runner->setVerbose();
		}

		// A specific job
		if ($input->getOption('job')) {

			$job = App::getEntityRepository('DeskPRO:WorkerJob')->findOneById($input->getOption('job'));
			if (!$job) {
				$output->writeln('<error>No such job exists</error>');
				return 1;
			}

			if (!$ignore_interval AND !$job->isReady()) {
				if ($verbose) {
					$output->writeln('Job does not need to run');
				}
				return 0;
			}

			$runner->runJobs(array($job));

		// A group of jobs
		} elseif ($input->getOption('group')) {
			$group_jobs = App::getOrm()->createQuery("
				SELECT j
				FROM DeskPRO:WorkerJob j
				WHERE j.worker_group = ?1
			")->setParameter(1, $input->getOption('group'))->execute();

			if (!count($group_jobs)) {
				$output->writeln('<warn>No jobs in that worker group</warn>');
				return -1;
			}

			if (!$ignore_interval) {
				$jobs = array();
				foreach ($group_jobs as $job) {
					if ($job->isReady()) {
						$jobs[] = $job;
					}
				}
			} else {
				$jobs = $group_jobs;
			}

			if (!count($jobs)) {
				if ($verbose) {
					$output->writeln('No jobs need to run');
				}
				return 0;
			}

			$runner->runJobs($jobs);

		// All jobs
		} else {
			$group_jobs = App::getEntityRepository('DeskPRO:WorkerJob')->findAll();

			if (!$ignore_interval) {
				$jobs = array();
				foreach ($group_jobs as $job) {
					if ($job->isReady()) {
						$jobs[] = $job;
					}
				}
			} else {
				$jobs = $group_jobs;
			}

			if (!count($jobs)) {
				if ($verbose) {
					$output->writeln('No jobs need to run');
				}
				return 0;
			}

			$runner->runJobs($jobs);
		}
	}
}
