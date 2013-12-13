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

class Build1354634985 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add rate limiting support to the API");
		$default_collation = $this->getDefaultCollation();
		$this->execMutateSql("CREATE TABLE api_key_rate_limit (api_key_id INT NOT NULL, hits INT DEFAULT NULL, created_stamp INT DEFAULT NULL, reset_stamp INT DEFAULT NULL, PRIMARY KEY(api_key_id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=$default_collation");
		$this->execMutateSql("ALTER TABLE api_key_rate_limit ADD CONSTRAINT FK_BBDD0D428BE312B3 FOREIGN KEY (api_key_id) REFERENCES api_keys (id) ON DELETE CASCADE");
	}
}