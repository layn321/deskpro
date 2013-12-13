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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;

class TaskQueue extends AbstractEntityRepository
{
	public function getAllTasks($newest_first = true)
	{
		return $this->getEntityManager()->createQuery("
			SELECT tq
			FROM DeskPRO:TaskQueue tq
			ORDER BY tq.date_runnable " . ($newest_first ? 'DESC' : 'ASC') . "
		")->execute();
	}

	public function getRunningTask()
	{
		return $this->getEntityManager()->createQuery("
			SELECT tq
			FROM DeskPRO:TaskQueue tq
			WHERE tq.status = 'running'
			ORDER BY tq.date_runnable
		")->setMaxResults(1)->getOneOrNullResult();
	}

	public function getNextQueuedTask()
	{
		return $this->getEntityManager()->createQuery("
			SELECT tq
			FROM DeskPRO:TaskQueue tq
			WHERE tq.status = 'queued' AND tq.date_runnable < ?0
			ORDER BY tq.date_runnable
		")->setMaxResults(1)->setParameters(array(date('Y-m-d H:i:s')))->getOneOrNullResult();
	}

	public function getRunnableTask()
	{
		$task = $this->getRunningTask();
		if ($task) {
			return $task;
		}

		$task = $this->getNextQueuedTask();
		if ($task) {
			return $task;
		}

		return null;
	}

	public function countTasksBefore(\Application\DeskPRO\Entity\TaskQueue $task)
	{
		$count = $this->getEntityManager()->getConnection()->fetchColumn("
			SELECT COUNT(*)
			FROM task_queue
			WHERE status NOT IN ('completed', 'errored')
				AND date_runnable <= ?
		", array($task->date_runnable->format('Y-m-d H:i:s')));

		return $count - 1; // -1 takes out this one
	}

	public function getTasksInGroup($group, $include_ended = false)
	{
		return $this->getEntityManager()->createQuery("
			SELECT tq
			FROM DeskPRO:TaskQueue tq
			WHERE tq.task_group = ?0
				" . (!$include_ended ? "AND tq.status NOT IN ('completed', 'errored')" : '') . "
			ORDER BY tq.date_runnable
		")->execute(array($group));
	}

	public function getPendingTasks()
	{
		return $this->getEntityManager()->createQuery("
			SELECT tq
			FROM DeskPRO:TaskQueue tq
			WHERE tq.status NOT IN ('completed', 'errored')
			ORDER BY tq.date_runnable
		")->execute();
	}

	public function enqueueTask($runner_class, array $data = array(), $task_group = null)
	{
		$task = new \Application\DeskPRO\Entity\TaskQueue();
		$task->runner_class = $runner_class;
		$task->task_data = $data;
		$task->task_group = $task_group;

		$em = $this->getEntityManager();
		$em->persist($task);
		$em->flush();

		return $task;
	}
}
