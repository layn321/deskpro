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

class Build1352301179 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add email_uids table");
		$this->execMutateSql("CREATE TABLE email_uids (id VARCHAR(100) NOT NULL, gateway_id INT DEFAULT NULL, date_created DATETIME NOT NULL, INDEX IDX_6D08D1BD577F8E00 (gateway_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("ALTER TABLE email_uids ADD CONSTRAINT FK_6D08D1BD577F8E00 FOREIGN KEY (gateway_id) REFERENCES email_gateways (id) ON DELETE CASCADE");

		$this->out("Add email_gateways.keep_read");
		$this->execMutateSql("ALTER TABLE email_gateways ADD keep_read TINYINT(1) NOT NULL");

		$this->out("Add email_sources.uid");
		$this->execMutateSql("ALTER TABLE email_sources ADD uid VARCHAR(100) DEFAULT NULL");
	}
}