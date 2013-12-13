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

class Build1349176493 extends AbstractBuild
{
	public function run()
	{
		$this->out("Allow plugins to define ticket trigger actions");
		$this->execMutateSql("CREATE TABLE ticket_trigger_plugin_actions (id INT AUTO_INCREMENT NOT NULL, plugin_id VARCHAR(255) DEFAULT NULL, event_type VARCHAR(50) NOT NULL, setup_class VARCHAR(255) NOT NULL, action_class VARCHAR(255) NOT NULL, INDEX IDX_1D905890EC942BCF (plugin_id), UNIQUE INDEX event_type_idx (event_type), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE ticket_trigger_plugin_actions ADD CONSTRAINT FK_1D905890EC942BCF FOREIGN KEY (plugin_id) REFERENCES plugins (id) ON DELETE CASCADE");
	}
}