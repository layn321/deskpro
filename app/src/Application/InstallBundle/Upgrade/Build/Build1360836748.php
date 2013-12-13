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

class Build1360836748 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add task_reminder_logs table");
		$this->execMutateSql("CREATE TABLE task_reminder_logs (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT NULL, person_id INT DEFAULT NULL, date_sent DATE DEFAULT NULL, INDEX IDX_A264D7248DB60186 (task_id), INDEX IDX_A264D724217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("ALTER TABLE task_reminder_logs ADD CONSTRAINT FK_A264D7248DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE task_reminder_logs ADD CONSTRAINT FK_A264D724217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");

		$this->out("Change tasks.date_due to datetime");
		$this->execMutateSql("ALTER TABLE tasks CHANGE date_due date_due DATETIME DEFAULT NULL");

		$this->out("Change tasks.date_due time to end of the day");
		$this->execMutateSql("UPDATE tasks SET date_due = CONCAT(DATE(date_due), ' 23:59:59') WHERE date_due IS NOT NULL");
	}
}