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

class Build1361546888 extends AbstractBuild
{
	public function run()
	{
		$this->out("Unset visitor_ids");

		// Erase all existing visitor connections
		$tables = array(
			'article_comments',
			'chat_blocks',
			'chat_conversations',
			'download_comments',
			'feedback_comments',
			'news_comments',
			'pretickets_content',
			'ratings',
			'searchlog',
			'sessions',
			'tickets_messages',
		);

		foreach ($tables as $t) {
			$this->execMutateSql("UPDATE `$t` SET visitor_id = NULL");
		}

		$this->out("Recreate visitor table");
		$this->execMutateSql("SET FOREIGN_KEY_CHECKS = 0");
		$this->execMutateSql("DROP TABLE IF EXISTS `visitors`");
		$this->execMutateSql("DROP TABLE IF EXISTS `visitor_tracks`");
		$this->execMutateSql("CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, initial_track_id INT DEFAULT NULL, last_track_id INT DEFAULT NULL, auth VARCHAR(15) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, chat_invite LONGBLOB DEFAULT NULL COMMENT '(DC2Type:array)', page_count INT NOT NULL, date_created DATETIME NOT NULL, date_last DATETIME NOT NULL, INDEX IDX_7B74A43F217BBB47 (person_id), INDEX IDX_7B74A43F866B65F3 (initial_track_id), INDEX IDX_7B74A43F26B379DD (last_track_id), INDEX date_last_idx (date_last), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("CREATE TABLE visitor_tracks (id INT AUTO_INCREMENT NOT NULL, visitor_id INT DEFAULT NULL, is_new_visit TINYINT(1) NOT NULL, page_title VARCHAR(255) NOT NULL, page_url VARCHAR(255) NOT NULL, ref_page_url VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(255) NOT NULL, user_browser VARCHAR(255) NOT NULL, user_os VARCHAR(255) NOT NULL, ip_address VARCHAR(80) NOT NULL, geo_continent VARCHAR(2) DEFAULT NULL, geo_country VARCHAR(2) DEFAULT NULL, geo_region VARCHAR(2) DEFAULT NULL, geo_city VARCHAR(2) DEFAULT NULL, geo_long NUMERIC(16, 8) DEFAULT NULL, geo_lat NUMERIC(16, 8) DEFAULT NULL, data LONGBLOB DEFAULT NULL COMMENT '(DC2Type:array)', date_created DATETIME NOT NULL, INDEX IDX_E002459270BEE6D (visitor_id), INDEX idx1 (date_created, is_new_visit), PRIMARY KEY(id)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
		$this->execMutateSql("ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43F217BBB47 FOREIGN KEY (person_id) REFERENCES people (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7B74A43F866B65F3 FOREIGN KEY (initial_track_id) REFERENCES visitor_tracks (id) ON DELETE SET NULL, ADD CONSTRAINT FK_7B74A43F26B379DD FOREIGN KEY (last_track_id) REFERENCES visitor_tracks (id) ON DELETE SET NULL");
		$this->execMutateSql("ALTER TABLE visitor_tracks ADD CONSTRAINT FK_E002459270BEE6D FOREIGN KEY (visitor_id) REFERENCES visitors (id) ON DELETE CASCADE");
		$this->execMutateSql("SET FOREIGN_KEY_CHECKS = 1");
		$this->execMutateSql("ALTER TABLE sessions DROP page_count");
	}
}