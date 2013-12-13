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

class Build1345043112 extends AbstractBuild
{
	public function run()
	{
		$this->out("Create report builder tables");

		$this->execMutateSql("CREATE TABLE report_builder (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, unique_key VARCHAR(50) DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, query LONGTEXT NOT NULL, is_custom TINYINT(1) NOT NULL, category VARCHAR(25) DEFAULT NULL, INDEX IDX_B6BED249727ACA70 (parent_id), UNIQUE INDEX unique_key_idx (unique_key), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE report_builder ADD CONSTRAINT FK_B6BED249727ACA70 FOREIGN KEY (parent_id) REFERENCES report_builder (id) ON DELETE SET NULL");
	}
}