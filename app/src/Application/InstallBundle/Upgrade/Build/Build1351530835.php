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

class Build1351530835 extends AbstractBuild
{
	public function run()
	{
		$this->out("Allow drafts to be created");
		$this->execMutateSql("CREATE TABLE drafts (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, content_type VARCHAR(50) NOT NULL, content_id INT NOT NULL, date_created DATETIME NOT NULL, message LONGTEXT NOT NULL, extras LONGBLOB NOT NULL COMMENT '(DC2Type:array)', INDEX IDX_EC2AE4C0217BBB47 (person_id), INDEX content_idx (content_type, content_id), INDEX date_idx (date_created), UNIQUE INDEX person_content_idx (person_id, content_type, content_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE drafts ADD CONSTRAINT FK_EC2AE4C0217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");
		$this->execMutateSql("
			INSERT INTO `worker_jobs` (`id`, `worker_group`, `title`, `description`, `job_class`, `data`, `run_interval`, `last_run_date`, `last_start_date`)
			VALUES ('cleanup_drafts', 'cleanup', 'Cleanup Drafts', 'Cleans up old drafts', 'Application\\\\DeskPRO\\\\WorkerProcess\\\\Job\\\\CleanupDrafts', X'613A303A7B7D', '3600', NULL, NULL)
		");
	}
}