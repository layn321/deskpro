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

class Build1349349294 extends AbstractBuild
{
	public function run()
	{
		$this->out("Recreate log_items table");
		$this->execMutateSql("DROP TABLE IF EXISTS log_items");
		$this->execMutateSql("
			CREATE TABLE `log_items` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`log_name` varchar(50) NOT NULL,
				`session_name` varchar(100) DEFAULT NULL,
				`flag` varchar(50) DEFAULT NULL,
				`priority` int(11) NOT NULL,
				`priority_name` varchar(25) NOT NULL,
				`message` longtext NOT NULL,
				`data` longblob COMMENT '(DC2Type:array)',
				`date_created` datetime NOT NULL,
				PRIMARY KEY (`id`),
				KEY `log_name_idx` (`log_name`,`session_name`),
				KEY `flag_idx` (`flag`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");

		$this->out("Delete old trigger about auto-closing");
		$this->execMutateSql("DELETE FROM ticket_triggers WHERE sys_name = 'auto_close.close_resolved'");
	}
}