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

class Build1371739498 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add agent_alerts table");
		$this->execMutateSql("CREATE TABLE agent_alerts (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, typename VARCHAR(255) NOT NULL, data LONGBLOB NOT NULL COMMENT '(DC2Type:array)', date_created DATETIME DEFAULT NULL, is_dismissed TINYINT(1) NOT NULL, INDEX IDX_A99D974D217BBB47 (person_id), INDEX date_created_idx (date_created), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE agent_alerts ADD CONSTRAINT FK_A99D974D217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");
	}
}