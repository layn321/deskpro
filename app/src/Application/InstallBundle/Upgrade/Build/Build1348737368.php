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

class Build1348737368 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add support for user source plugins");
		$this->execMutateSql("CREATE TABLE usersource_plugins (id INT AUTO_INCREMENT NOT NULL, plugin_id VARCHAR(255) DEFAULT NULL, unique_key VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, form_model_class VARCHAR(255) NOT NULL, form_type_class VARCHAR(255) NOT NULL, form_template VARCHAR(255) NOT NULL, adapter_class VARCHAR(255) NOT NULL, INDEX IDX_E484A367EC942BCF (plugin_id), UNIQUE INDEX unique_key_idx (unique_key), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE usersource_plugins ADD CONSTRAINT FK_E484A367EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE usersources ADD usersource_plugin_id INT DEFAULT NULL");
		$this->execMutateSql("ALTER TABLE usersources ADD CONSTRAINT FK_4E3C994CEB0D3362 FOREIGN KEY (usersource_plugin_id) REFERENCES usersource_plugins (id) ON DELETE CASCADE");
		$this->execMutateSql("CREATE INDEX IDX_4E3C994CEB0D3362 ON usersources (usersource_plugin_id)");
	}
}