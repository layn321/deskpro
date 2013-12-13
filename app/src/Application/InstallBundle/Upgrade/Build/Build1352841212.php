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

class Build1352841212 extends AbstractBuild
{
	public function run()
	{
		$this->out("Support applying SLAs to tickets");
		$this->execMutateSql("CREATE TABLE slas (id INT AUTO_INCREMENT NOT NULL, warning_trigger_id INT DEFAULT NULL, fail_trigger_id INT DEFAULT NULL, apply_priority_id INT DEFAULT NULL, apply_trigger_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, sla_type VARCHAR(50) NOT NULL, active_time VARCHAR(50) NOT NULL, work_start INT DEFAULT NULL, work_end INT DEFAULT NULL, work_days LONGBLOB DEFAULT NULL COMMENT '(DC2Type:array)', work_timezone VARCHAR(50) DEFAULT NULL, work_holidays LONGBLOB DEFAULT NULL COMMENT '(DC2Type:array)', apply_all TINYINT(1) NOT NULL, allow_agent_manual TINYINT(1) NOT NULL, INDEX IDX_ACE9984A91D0B882 (warning_trigger_id), INDEX IDX_ACE9984A55EA90D4 (fail_trigger_id), INDEX IDX_ACE9984A13CC0145 (apply_priority_id), INDEX IDX_ACE9984AED1A7B28 (apply_trigger_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("CREATE TABLE sla_people (sla_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_14ABD6A37A2CC8C4 (sla_id), INDEX IDX_14ABD6A3217BBB47 (person_id), PRIMARY KEY(sla_id, person_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("CREATE TABLE sla_organizations (sla_id INT NOT NULL, organization_id INT NOT NULL, INDEX IDX_A7F081987A2CC8C4 (sla_id), INDEX IDX_A7F0819832C8A3DE (organization_id), PRIMARY KEY(sla_id, organization_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("CREATE TABLE ticket_slas (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, sla_id INT DEFAULT NULL, sla_status VARCHAR(20) NOT NULL, warn_date DATETIME DEFAULT NULL, fail_date DATETIME DEFAULT NULL, is_completed TINYINT(1) NOT NULL, INDEX IDX_9E328D72700047D2 (ticket_id), INDEX IDX_9E328D727A2CC8C4 (sla_id), INDEX status_completed_warn_date_idx (sla_status, is_completed, warn_date), INDEX status_completed_fail_date_idx (sla_status, is_completed, fail_date), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE tickets ADD worst_sla_status VARCHAR(20) DEFAULT NULL, ADD waiting_times LONGBLOB DEFAULT NULL COMMENT '(DC2Type:array)'");
		$this->execMutateSql("ALTER TABLE slas ADD CONSTRAINT FK_ACE9984A91D0B882 FOREIGN KEY (warning_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE slas ADD CONSTRAINT FK_ACE9984A55EA90D4 FOREIGN KEY (fail_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE slas ADD CONSTRAINT FK_ACE9984A13CC0145 FOREIGN KEY (apply_priority_id) REFERENCES ticket_priorities (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE slas ADD CONSTRAINT FK_ACE9984AED1A7B28 FOREIGN KEY (apply_trigger_id) REFERENCES ticket_triggers (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE sla_people ADD CONSTRAINT FK_14ABD6A37A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE sla_people ADD CONSTRAINT FK_14ABD6A3217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE sla_organizations ADD CONSTRAINT FK_A7F081987A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE sla_organizations ADD CONSTRAINT FK_A7F0819832C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE ticket_slas ADD CONSTRAINT FK_9E328D72700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE ticket_slas ADD CONSTRAINT FK_9E328D727A2CC8C4 FOREIGN KEY (sla_id) REFERENCES slas (id) ON DELETE CASCADE");
		$this->execMutateSql("
			INSERT INTO `worker_jobs` (`id`, `worker_group`, `title`, `description`, `job_class`, `data`, `run_interval`, `last_run_date`, `last_start_date`)
			VALUES ('ticket_slas', 'ticket_slas', 'Ticket SLAs', 'Updates ticket SLA status', 'Application\\\\DeskPRO\\\\WorkerProcess\\\\Job\\\\TicketSlas', X'613A303A7B7D', '60', NULL, NULL)
		");
	}
}