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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

class TasksStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Calendar Tasks';
	}

	public function countPages()
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM calendar_task");
		if (!$count) {
			return 1;
		}

		return ceil($count / 500);
	}

	public function run($page = 1)
	{
		$start = ($page - 1) * 500;
		$batch = $this->getOldDb()->fetchAll("SELECT * FROM calendar_task ORDER BY id ASC LIMIT $start, 500");

		$this->getDb()->beginTransaction();
		try {
			foreach ($batch as $t) {
				if ($t['repeattype']) {
					$this->processRepeatingTask($t);
				} else {
					$this->processTask($t);
				}
			}
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	/**
	 * @param array $task_info
	 */
	public function processTask($task_info)
	{
		$by_agent_id = $this->getMappedNewId('tech', $task_info['techmaker']);
		if (!$by_agent_id) {
			return;
		}

		$insert_task_tpl = array();
		$insert_task_tpl['person_id']          = $by_agent_id;
		$insert_task_tpl['title']              = $task_info['title'] . " " . strip_tags($task_info['description']);
		$insert_task_tpl['date_created']       = date('Y-m-d H:i:s', $task_info['startstamp']);

		$assignments = $this->getOldDb()->fetchAll("
			SELECT * FROM calendar_task_tech
			WHERE taskid = ?
			ORDER BY id ASC
		", array($task_info['id']));

		foreach ($assignments as $as) {
			$assigned_agent_id = $this->getMappedNewId('tech', $as['techid']);
			if (!$assigned_agent_id) {
				continue;
			}

			$insert_task = $insert_task_tpl;
			$insert_task['assigned_agent_id'] = $assigned_agent_id;
			if ($as['completed']) {
				$insert_task['is_completed']    = 1;
				$insert_task['date_completed']  = date('Y-m-d H:i:s', $task_info['startstamp'] + 1);
			}

			$this->getDb()->insert('tasks', $insert_task);
		}
	}

	/**
	 * @param array $task_info
	 */
	public function processRepeatingTask($task_info)
	{
		$by_agent_id = $this->getMappedNewId('tech', $task_info['techmaker']);
		if (!$by_agent_id) {
			return;
		}

		$iterations = $this->getOldDb()->fetchAll("
			SELECT * FROM calendar_task_iteration
			WHERE taskid = ?
			ORDER BY timestamp ASC
		", array($task_info['id']));

		foreach ($iterations as $it) {

			$assigned_agent_id = $this->getMappedNewId('tech', $it['task_techid']);
			if (!$assigned_agent_id) {
				continue;
			}

			$insert_task = array();
			$insert_task['person_id']          = $by_agent_id;
			$insert_task['assigned_agent_id']  = $assigned_agent_id;
			$insert_task['is_completed']       = 1;
			$insert_task['title']              = $task_info['title'] . " " . strip_tags($task_info['description']);
			$insert_task['date_created']       = date('Y-m-d H:i:s', $task_info['startstamp']);
			$insert_task['date_completed']     = date('Y-m-d H:i:s', $it['timestamp']);

			$this->getDb()->insert('tasks', $insert_task);
		}
	}
}
