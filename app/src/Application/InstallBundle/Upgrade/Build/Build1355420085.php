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

class Build1355420085 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add long Twitter message support");
		$this->execMutateSql("ALTER TABLE twitter_statuses_long ADD for_user_id BIGINT DEFAULT NULL");
		$this->execMutateSql("ALTER TABLE twitter_statuses_long CHANGE status_id status_id BIGINT DEFAULT NULL");
		$this->execMutateSql("ALTER TABLE twitter_statuses_long ADD CONSTRAINT FK_8B914BFB9B5BB4B8 FOREIGN KEY (for_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("CREATE INDEX IDX_8B914BFB9B5BB4B8 ON twitter_statuses_long (for_user_id)");
	}
}