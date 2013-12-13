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

class Build1340805438 extends AbstractBuild
{
	public function run()
	{
		$this->out("Inserting default REGISTERED usergroup");

		// Shift usergroups IDs up by one, we need usergroup ID 2 to be registered
		$this->container->getDb()->exec("SET FOREIGN_KEY_CHECKS = 0");
		$this->execMutateSql("UPDATE usergroups SET id = id + 1 WHERE id >= 2 ORDER BY id DESC");
		$this->execMutateSql("UPDATE article_category2usergroup SET usergroup_id = usergroup_id + 1 WHERE usergroup_id >= 2 ORDER BY usergroup_id DESC");
		$this->execMutateSql("UPDATE download_category2usergroup SET usergroup_id = usergroup_id + 1 WHERE usergroup_id >= 2 ORDER BY usergroup_id DESC");
		$this->execMutateSql("UPDATE feedback_category2usergroup SET usergroup_id = usergroup_id + 1 WHERE usergroup_id >= 2 ORDER BY usergroup_id DESC");
		$this->execMutateSql("UPDATE news_category2usergroup SET usergroup_id = usergroup_id + 1 WHERE usergroup_id >= 2 ORDER BY usergroup_id DESC");
		$this->execMutateSql("UPDATE organization2usergroups SET usergroup_id = usergroup_id + 1 WHERE usergroup_id >= 2 ORDER BY usergroup_id DESC");
		$this->execMutateSql("UPDATE person2usergroups SET usergroup_id = usergroup_id + 1 WHERE usergroup_id >= 2 ORDER BY usergroup_id DESC");
		$this->execMutateSql("UPDATE department_permissions SET usergroup_id = usergroup_id + 1 WHERE usergroup_id >= 2 ORDER BY usergroup_id DESC");
		$this->execMutateSql("UPDATE permissions SET usergroup_id = usergroup_id + 1 WHERE usergroup_id >= 2 ORDER BY usergroup_id DESC");

		// Clear permissions cache, the keys will be invalid now
		$this->execMutateSql("TRUNCATE TABLE permissions_cache");

		// If this is a dp3 import, then move the registered group into place
		$is_dp3_import = $this->container->getDb()->fetchColumn("SELECT value FROM settings WHERE name = 'core.deskpro3importer'");
		$dp3_reg_ug = 0;
		if ($is_dp3_import) {
			// We only know based off the title ...
			$dp3_reg_ug = $this->container->getDb()->fetchColumn("SELECT id FROM usergroups WHERE title = 'Registered'");
		}

		if ($dp3_reg_ug) {

			$this->execMutateSql("UPDATE usergroups SET id = 2 WHERE id = $dp3_reg_ug");
			$this->execMutateSql("UPDATE article_category2usergroup SET usergroup_id = 2 WHERE usergroup_id = $dp3_reg_ug");
			$this->execMutateSql("UPDATE download_category2usergroup SET usergroup_id = 2 WHERE usergroup_id = $dp3_reg_ug");
			$this->execMutateSql("UPDATE feedback_category2usergroup SET usergroup_id = 2 WHERE usergroup_id = $dp3_reg_ug");
			$this->execMutateSql("UPDATE news_category2usergroup SET usergroup_id = 2 WHERE usergroup_id = $dp3_reg_ug");
			$this->execMutateSql("UPDATE organization2usergroups SET usergroup_id = 2 WHERE usergroup_id = $dp3_reg_ug");
			$this->execMutateSql("UPDATE department_permissions SET usergroup_id = 2 WHERE usergroup_id = $dp3_reg_ug");
			$this->execMutateSql("UPDATE permissions SET usergroup_id = 2 WHERE usergroup_id = $dp3_reg_ug");

			// Remove everyone from the usergroup, they're added implicitly so we dont need to store it
			$this->execMutateSql("DELETE FROM person2usergroups WHERE usergroup_id = $dp3_reg_ug");

			// Update the record to have the proper sys_name
			$this->execMutateSql("UPDATE usergroups SET sys_name = 'registered' WHERE id = 2");

		// Otherwise we'll create it new
		} else {
			$this->execMutateSql("
				INSERT INTO `usergroups` (`id`, `title`, `note`, `is_agent_group`, `sys_name`)
				VALUES	(2, 'Registered', 'Permissions applied to all registered users.', 0, 'registered')
			");
		}

		$this->container->getDb()->exec("SET FOREIGN_KEY_CHECKS = 1");
	}
}