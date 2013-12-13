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

class Build1358330844 extends AbstractBuild
{
	public function run()
	{
		$this->out("Adjust Twitter user storage for people/organizations");
		$this->execMutateSql("ALTER TABLE organizations_twitter_users DROP FOREIGN KEY FK_26894816B1F2707");
		$this->execMutateSql("DROP INDEX IDX_26894816B1F2707 ON organizations_twitter_users");
		$this->execMutateSql("ALTER TABLE organizations_twitter_users CHANGE twitter_user_id twitter_user_id BIGINT NOT NULL");
		$this->execMutateSql("ALTER TABLE people_twitter_users DROP FOREIGN KEY FK_E13A49D06B1F2707");
		$this->execMutateSql("DROP INDEX IDX_E13A49D06B1F2707 ON people_twitter_users");
		$this->execMutateSql("ALTER TABLE people_twitter_users CHANGE twitter_user_id twitter_user_id BIGINT NOT NULL");
	}
}