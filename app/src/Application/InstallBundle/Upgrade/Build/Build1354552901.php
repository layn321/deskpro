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

namespace Application\InstallBundle\Upgrade\Build;

class Build1354552901 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add task reminder notifications");
		$this->execMutateSql("
			INSERT IGNORE INTO people_prefs
				(person_id, name, value_str, value_array)
			SELECT person_id, 'agent_notif.task_due.email', '1', 'N;'
			FROM people_prefs
			WHERE name = 'agent_notif.task_assign_self.email'
		");
		$this->execMutateSql("
			INSERT IGNORE INTO people_prefs
				(person_id, name, value_str, value_array)
			SELECT person_id, 'agent_notif.task_due.alert', '1', 'N;'
			FROM people_prefs
			WHERE name = 'agent_notif.task_assign_self.alert'
		");
		$this->execMutateSql("
			INSERT INTO `worker_jobs` (`id`, `worker_group`, `title`, `description`, `job_class`, `data`, `run_interval`, `last_run_date`, `last_start_date`)
			VALUES ('task_reminders', 'task_reminders', 'Task Reminders', 'Sends task reminder notifications', 'Application\\\\DeskPRO\\\\WorkerProcess\\\\Job\\\\TaskReminders', X'613A303A7B7D', '3600', NULL, NULL)
		");
	}
}