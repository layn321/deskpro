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

class Build1358274352 extends AbstractBuild
{
	public function run()
	{
		$this->out("Track organization Twitter associations explicitly");
		$this->execMutateSql("CREATE TABLE organizations_twitter_users (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, twitter_user_id BIGINT DEFAULT NULL, screen_name VARCHAR(50) NOT NULL, is_verified TINYINT(1) NOT NULL, oauth_token VARCHAR(4000) DEFAULT NULL, oauth_token_secret VARCHAR(4000) DEFAULT NULL, INDEX IDX_268948132C8A3DE (organization_id), INDEX IDX_26894816B1F2707 (twitter_user_id), INDEX screen_name_idx (screen_name), UNIQUE INDEX unique_key_idx (organization_id, screen_name), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("ALTER TABLE organizations_twitter_users ADD CONSTRAINT FK_268948132C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE organizations_twitter_users ADD CONSTRAINT FK_26894816B1F2707 FOREIGN KEY (twitter_user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("
			INSERT IGNORE INTO organizations_twitter_users
				(organization_id, screen_name)
			SELECT organization_id, field_1
			FROM organizations_contact_data
			WHERE contact_type = 'twitter'
				AND field_1 <> ''
		");
	}
}