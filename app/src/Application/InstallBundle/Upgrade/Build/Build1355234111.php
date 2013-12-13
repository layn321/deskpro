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

class Build1355234111 extends AbstractBuild
{
	public function run()
	{
		$this->out("Twitter search support");
		$this->execMutateSql("CREATE TABLE twitter_accounts_searches_statuses (account_status_id INT NOT NULL, search_id INT NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_E52AFE3B498DD8E6 (account_status_id), INDEX IDX_E52AFE3B650760A9 (search_id), INDEX search_date_idx (search_id, date_created), PRIMARY KEY(account_status_id, search_id)) ENGINE = InnoDB");
		$this->execMutateSql("ALTER TABLE twitter_accounts_searches_statuses ADD CONSTRAINT FK_E52AFE3B498DD8E6 FOREIGN KEY (account_status_id) REFERENCES twitter_accounts_statuses (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_searches_statuses ADD CONSTRAINT FK_E52AFE3B650760A9 FOREIGN KEY (search_id) REFERENCES twitter_accounts_searches (id) ON DELETE CASCADE");
		$this->execMutateSql("ALTER TABLE twitter_accounts_searches ADD date_updated DATETIME DEFAULT NULL, ADD max_id BIGINT DEFAULT NULL, ADD min_id BIGINT DEFAULT NULL");
		$this->execMutateSql("CREATE INDEX account_type_archived_idx ON twitter_accounts_statuses (account_id, status_type, is_archived)");
	}
}