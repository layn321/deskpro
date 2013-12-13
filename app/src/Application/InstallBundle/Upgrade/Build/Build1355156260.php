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

class Build1355156260 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add Twitter module");
		$this->execMutateSql("CREATE TABLE twitter_accounts (id INT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, oauth_token VARCHAR(4000) NOT NULL, oauth_token_secret VARCHAR(4000) NOT NULL, UNIQUE INDEX UNIQ_D4051D30A76ED395 (user_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_accounts_person (account_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_BB12235C9B6B5FBA (account_id), INDEX IDX_BB12235C217BBB47 (person_id), PRIMARY KEY(account_id, person_id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_accounts_followers (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, user_id BIGINT NOT NULL, INDEX IDX_EB8452969B6B5FBA (account_id), INDEX IDX_EB845296A76ED395 (user_id), UNIQUE INDEX account_user_idx (account_id, user_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_accounts_friends (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, user_id BIGINT NOT NULL, INDEX IDX_FADA774D9B6B5FBA (account_id), INDEX IDX_FADA774DA76ED395 (user_id), UNIQUE INDEX account_user_idx (account_id, user_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_accounts_searches (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, term VARCHAR(255) NOT NULL, INDEX IDX_5CC0E8CF9B6B5FBA (account_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_accounts_statuses (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, status_id BIGINT DEFAULT NULL, agent_id INT DEFAULT NULL, agent_team_id INT DEFAULT NULL, retweeted_id INT DEFAULT NULL, in_reply_to_id INT DEFAULT NULL, date_created DATETIME NOT NULL, status_type VARCHAR(25) DEFAULT NULL, is_archived TINYINT(1) NOT NULL, is_favorited TINYINT(1) NOT NULL, INDEX IDX_7728CEC79B6B5FBA (account_id), INDEX IDX_7728CEC76BF700BD (status_id), INDEX IDX_7728CEC73414710B (agent_id), INDEX IDX_7728CEC7FB3FBA04 (agent_team_id), INDEX IDX_7728CEC754E76E81 (retweeted_id), INDEX IDX_7728CEC7DD92DAB8 (in_reply_to_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_accounts_statuses_notes (id INT AUTO_INCREMENT NOT NULL, account_status_id INT NOT NULL, person_id INT DEFAULT NULL, text VARCHAR(4000) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_E5D3CBA2498DD8E6 (account_status_id), INDEX IDX_E5D3CBA2217BBB47 (person_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_statuses (id BIGINT NOT NULL, user_id BIGINT DEFAULT NULL, in_reply_to_status_id BIGINT DEFAULT NULL, retweet_id BIGINT DEFAULT NULL, in_reply_to_user_id BIGINT DEFAULT NULL, recipient_id BIGINT DEFAULT NULL, text VARCHAR(4000) NOT NULL, is_truncated TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, geo_latitude NUMERIC(10, 5) DEFAULT NULL, geo_longitude NUMERIC(10, 5) DEFAULT NULL, source VARCHAR(4000) DEFAULT NULL, INDEX IDX_553D9D8DA76ED395 (user_id), INDEX IDX_553D9D8D6B347969 (in_reply_to_status_id), INDEX IDX_553D9D8D72A1C5CA (retweet_id), INDEX IDX_553D9D8DD2347268 (in_reply_to_user_id), INDEX IDX_553D9D8DE92F8F78 (recipient_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_statuses_long (id INT AUTO_INCREMENT NOT NULL, status_id BIGINT NOT NULL, text VARCHAR(4000) NOT NULL, is_public TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, date_read DATETIME DEFAULT NULL, INDEX IDX_8B914BFB6BF700BD (status_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_statuses_mentions (id INT AUTO_INCREMENT NOT NULL, status_id BIGINT NOT NULL, user_id BIGINT NOT NULL, starts INT NOT NULL, ends INT NOT NULL, INDEX IDX_66912DD16BF700BD (status_id), INDEX IDX_66912DD1A76ED395 (user_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_statuses_tags (id INT AUTO_INCREMENT NOT NULL, status_id BIGINT NOT NULL, hash VARCHAR(255) NOT NULL, starts INT NOT NULL, ends INT NOT NULL, INDEX IDX_DFBA76B56BF700BD (status_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_statuses_urls (id INT AUTO_INCREMENT NOT NULL, status_id BIGINT NOT NULL, url VARCHAR(255) NOT NULL, starts INT NOT NULL, ends INT NOT NULL, INDEX IDX_9A92D5326BF700BD (status_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_stream (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, date_created DATETIME NOT NULL, event VARCHAR(50) NOT NULL, data LONGTEXT NOT NULL COMMENT '(DC2Type:object)', INDEX IDX_8D6AB9A89B6B5FBA (account_id), PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("CREATE TABLE twitter_users (id BIGINT NOT NULL, name VARCHAR(40) NOT NULL, screen_name VARCHAR(20) NOT NULL, profile_image_url VARCHAR(200) NOT NULL, language VARCHAR(3) NOT NULL, is_protected TINYINT(1) NOT NULL, is_verified TINYINT(1) NOT NULL, location VARCHAR(255) DEFAULT NULL, description VARCHAR(500) DEFAULT NULL, is_geo_enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB");
		$this->execMutateSql("ALTER TABLE twitter_accounts ADD CONSTRAINT FK_D4051D30A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_person ADD CONSTRAINT FK_BB12235C9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_person ADD CONSTRAINT FK_BB12235C217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_followers ADD CONSTRAINT FK_EB8452969B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_followers ADD CONSTRAINT FK_EB845296A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_friends ADD CONSTRAINT FK_FADA774D9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_friends ADD CONSTRAINT FK_FADA774DA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_searches ADD CONSTRAINT FK_5CC0E8CF9B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC79B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC76BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC73414710B FOREIGN KEY (agent_id) REFERENCES people (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC7FB3FBA04 FOREIGN KEY (agent_team_id) REFERENCES agent_teams (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC754E76E81 FOREIGN KEY (retweeted_id) REFERENCES twitter_accounts_statuses (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE twitter_accounts_statuses ADD CONSTRAINT FK_7728CEC7DD92DAB8 FOREIGN KEY (in_reply_to_id) REFERENCES twitter_accounts_statuses (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE twitter_accounts_statuses_notes ADD CONSTRAINT FK_E5D3CBA2498DD8E6 FOREIGN KEY (account_status_id) REFERENCES twitter_accounts_statuses (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_statuses_notes ADD CONSTRAINT FK_E5D3CBA2217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8DA76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id)");
		$this->execMutateSql("ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8D6B347969 FOREIGN KEY (in_reply_to_status_id) REFERENCES twitter_statuses (id)");
		$this->execMutateSql("ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8D72A1C5CA FOREIGN KEY (retweet_id) REFERENCES twitter_statuses (id)");
		$this->execMutateSql("ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8DD2347268 FOREIGN KEY (in_reply_to_user_id) REFERENCES twitter_users (id)");
		$this->execMutateSql("ALTER TABLE twitter_statuses ADD CONSTRAINT FK_553D9D8DE92F8F78 FOREIGN KEY (recipient_id) REFERENCES twitter_users (id)");
		$this->execMutateSql("ALTER TABLE twitter_statuses_long ADD CONSTRAINT FK_8B914BFB6BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_statuses_mentions ADD CONSTRAINT FK_66912DD16BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_statuses_mentions ADD CONSTRAINT FK_66912DD1A76ED395 FOREIGN KEY (user_id) REFERENCES twitter_users (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_statuses_tags ADD CONSTRAINT FK_DFBA76B56BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_statuses_urls ADD CONSTRAINT FK_9A92D5326BF700BD FOREIGN KEY (status_id) REFERENCES twitter_statuses (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_stream ADD CONSTRAINT FK_8D6AB9A89B6B5FBA FOREIGN KEY (account_id) REFERENCES twitter_accounts (id) ON DELETE CASCADE");
		$this->execMutateSql("
			INSERT IGNORE INTO `worker_jobs` (`id`, `worker_group`, `title`, `description`, `job_class`, `data`, `run_interval`, `last_run_date`, `last_start_date`)
			VALUES ('twitter_stream', 'twitter_stream', 'Twitter Stream', 'Imports tweets from the Twitter stream', 'Application\\\\DeskPRO\\\\WorkerProcess\\\\Job\\\\TwitterStream', X'613A303A7B7D', '10', NULL, NULL)
		");
	}
}