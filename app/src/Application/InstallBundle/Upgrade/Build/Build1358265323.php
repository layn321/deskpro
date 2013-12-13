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

class Build1358265323 extends AbstractBuild
{
	public function run()
	{
		$this->out("Support tracking Twitter user followers/friends");
		$this->execMutateSql("CREATE TABLE twitter_users_followers (id INT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, follower_user_id BIGINT NOT NULL, display_order INT NOT NULL, INDEX IDX_F37AF1BEA76ED395 (user_id), INDEX IDX_F37AF1BE70FC2906 (follower_user_id), UNIQUE INDEX user_follower_idx (user_id, follower_user_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_users_friends (id INT AUTO_INCREMENT NOT NULL, user_id BIGINT NOT NULL, friend_user_id BIGINT NOT NULL, display_order INT NOT NULL, INDEX IDX_77C2EDABA76ED395 (user_id), INDEX IDX_77C2EDAB93D1119E (friend_user_id), UNIQUE INDEX user_friend_idx (user_id, friend_user_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("ALTER TABLE twitter_users_followers ADD CONSTRAINT FK_F37AF1BEA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_users_followers ADD CONSTRAINT FK_F37AF1BE70FC2906 FOREIGN KEY (follower_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_users_friends ADD CONSTRAINT FK_77C2EDABA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_users_friends ADD CONSTRAINT FK_77C2EDAB93D1119E FOREIGN KEY (friend_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_users ADD followers_count INT NOT NULL, ADD friends_count INT NOT NULL, ADD last_follow_update DATETIME DEFAULT NULL");
	}
}