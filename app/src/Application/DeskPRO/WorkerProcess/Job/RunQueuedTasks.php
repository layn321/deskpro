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

use Application\DeskPRO\App;
use Application\DeskPRO\Log\Logger;
use Application\DeskPRO\Entity\Article;

/**
 * Runs queued tasks if there are any
 */
class RunQueuedTasks extends AbstractJob
{
	const DEFAULT_INTERVAL = 60; // 60 seconds

	public function run()
	{
		$max_run = 25;
		$start_time = microtime(true);
		$task = false;

		$em = App::getOrm();
		$logger = $this->getLogger();

		while (($remaining_time = $max_run - (microtime(true) - $start_time)) > 1) {
			if (!$task) {
				/** @var $task \Application\DeskPRO\Entity\TaskQueue */
				$task = App::getEntityRepository('DeskPRO:TaskQueue')->getRunnableTask();
			}

			if (!$task) {
				break;
			}

			$logger->logInfo("Running task #$task->id: $task->runner_class");

			try {
				$result = $task->runTask($remaining_time, $logger);
			} catch (\Exception $e) {
				$result = false;
				$logger->logWarn("Task #$task->id ($task->runner_class) errored: " . $e->getMessage());
			}

			$em->persist($task);
			$em->flush();

			if ($result === \Application\DeskPRO\TaskQueueJob\AbstractJob::TASK_COMPLETED) {
				// finished task, move on
				$logger->logInfo("Task #$task->id ($task->runner_class) completed successfully.");
				$task = false;
			} elseif ($result === \Application\DeskPRO\TaskQueueJob\AbstractJob::TASK_CONTINUING) {
				// still running task, so keep $task in case we have more time
				$logger->logInfo("Task #$task->id ($task->runner_class) to be continued.");
			} else {
				// task errored, logged above already
				$task = false;
			}
		}
	}
}
