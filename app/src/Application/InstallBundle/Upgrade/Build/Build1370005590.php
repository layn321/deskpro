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

class Build1370005590 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add tickets_messages_translated table");
		$this->execMutateSql("CREATE TABLE tickets_messages_translated (id INT AUTO_INCREMENT NOT NULL, ticket_id INT DEFAULT NULL, message_id INT DEFAULT NULL, date_created DATETIME NOT NULL, from_lang_code VARCHAR(80) NOT NULL, lang_code VARCHAR(80) NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_EDCD3BB3700047D2 (ticket_id), INDEX IDX_EDCD3BB3537A1329 (message_id), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE tickets_messages_translated ADD CONSTRAINT FK_EDCD3BB3700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE tickets_messages_translated ADD CONSTRAINT FK_EDCD3BB3537A1329 FOREIGN KEY (message_id) REFERENCES tickets_messages (id) ON DELETE CASCADE");

		$this->out("Add tickets_messages.message_translated_id and tickets_messages.lang_code");
		$this->execMutateSql("ALTER TABLE tickets_messages ADD message_translated_id INT DEFAULT NULL, ADD lang_code VARCHAR(80) DEFAULT NULL, ADD geo_country VARCHAR(10) DEFAULT NULL, ADD CONSTRAINT FK_3A9962E2251FB291 FOREIGN KEY (message_translated_id) REFERENCES tickets_messages_translated (id) ON DELETE SET NULL, ADD INDEX IDX_3A9962E2251FB291 (message_translated_id)");
	}
}