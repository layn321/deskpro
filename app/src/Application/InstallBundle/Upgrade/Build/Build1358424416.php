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

class Build1358424416 extends AbstractBuild
{
	public function run()
	{
		$this->out("Support using API tokens to access the API");
		$this->execMutateSql("DROP TABLE api_auth_codes");
		$this->execMutateSql("DROP TABLE api_auth_tokens");
		$this->execMutateSql("CREATE TABLE api_token (person_id INT NOT NULL, token VARCHAR(25) NOT NULL, date_expires DATETIME DEFAULT NULL, PRIMARY KEY(person_id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE api_token_rate_limit (person_id INT NOT NULL, hits INT DEFAULT NULL, created_stamp INT DEFAULT NULL, reset_stamp INT DEFAULT NULL, PRIMARY KEY(person_id)) ENGINE = InnoDB");
		$this->execMutateSql("ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE api_token_rate_limit ADD CONSTRAINT FK_458445A9217BBB47 FOREIGN KEY (person_id) REFERENCES api_token (person_id) ON DELETE CASCADE");
	}
}