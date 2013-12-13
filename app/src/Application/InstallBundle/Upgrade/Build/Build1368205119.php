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

class Build1368205119 extends AbstractBuild
{
	public function run()
	{
		$this->out("Create sendmail_logs");
		$this->execMutateSql("CREATE TABLE sendmail_logs (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, ticket_message_id INT DEFAULT NULL, person_id INT DEFAULT NULL, code VARCHAR(30) NOT NULL, to_address VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, from_address VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, date_process DATETIME DEFAULT NULL, date_deliver DATETIME DEFAULT NULL, reason_deliver LONGTEXT DEFAULT NULL, date_open DATETIME DEFAULT NULL, date_click DATETIME DEFAULT NULL, clicked_urls LONGTEXT DEFAULT NULL, count_open INT NOT NULL, count_click INT NOT NULL, date_defer DATETIME DEFAULT NULL, reason_defer LONGTEXT DEFAULT NULL, date_bounce DATETIME DEFAULT NULL, bounce_code VARCHAR(10) DEFAULT NULL, bounce_type VARCHAR(10) DEFAULT NULL, reason_bounce LONGTEXT DEFAULT NULL, date_drop DATETIME DEFAULT NULL, reason_drop LONGTEXT DEFAULT NULL, date_spam DATETIME DEFAULT NULL, INDEX IDX_D9E8157F700047D2 (ticket_id), INDEX IDX_D9E8157FC5E9817D (ticket_message_id), INDEX IDX_D9E8157F217BBB47 (person_id), UNIQUE INDEX code (code, to_address), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE sendmail_logs ADD CONSTRAINT FK_D9E8157F700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE sendmail_logs ADD CONSTRAINT FK_D9E8157FC5E9817D FOREIGN KEY (ticket_message_id) REFERENCES tickets_messages (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE sendmail_logs ADD CONSTRAINT FK_D9E8157F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL");
	}
}