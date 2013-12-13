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
 */

namespace Application\DeskPRO\People;

use Application\DeskPRO\App;

class PermissionUtil
{
	/**
	 * This checks that permissions on usergroups are correct. For example,
	 * if a department is removed, make sure that at least one other department
	 * exists for a 'can use tickets' permission to remain enabled.
	 */
	public static function getBadUsergroupPermissions()
	{
		// Fetch usergroups that are set to allow chat/tickets
		$app_usergroups = App::getDb()->fetchAllGrouped("
			SELECT
				IF(name = 'tickets.use', 'tickets', 'chat') AS appname,
				usergroup_id
			FROM permissions
			WHERE usergroup_id IS NOT NULL AND value = 1 AND name IN ('tickets.use', 'chat.use')
		", array(), 'appname', null, 'usergroup_id');

		// Fetch the departments that each usergroup can use
		$app_usergroup_dep_raw = App::getDb()->fetchAll("
			SELECT
				IF(app = 'tickets', 'tickets', 'chat') AS appname,
				usergroup_id,
				department_id
			FROM department_permissions
			WHERE value = 1
		");

		//$app_usergroup_dep[tickets][usergroup_id]=array(department_ids);
		$app_usergroup_deps = array();
		foreach ($app_usergroup_dep_raw as $row) {
			if (!isset($app_usergroup_deps[$row['appname']])) {
				$app_usergroup_deps[$row['appname']] = array();
			}
			if (!isset($app_usergroup_deps[$row['appname']][$row['usergroup_id']])) {
				$app_usergroup_deps[$row['appname']][$row['usergroup_id']] = array();
			}

			$app_usergroup_deps[$row['appname']][$row['usergroup_id']][] = $row['department_id'];
		}

		// $corrections[usergroup_id] = array(permission names that need to be removed)
		$corrections = array();

		foreach ($app_usergroups as $appname => $usergroup_ids) {
			foreach ($usergroup_ids as $ugid) {
				if (empty($app_usergroup_deps[$appname][$ugid])) {
					if (!isset($corrections[$ugid])) {
						$corrections[$ugid] = array();
					}

					$corrections[$ugid][] = $appname == 'tickets' ? 'tickets.use' : 'chat.use';
				}
			}
		}

		return $corrections;
	}


	/**
	 * Fetches bad permissions and removes them. This makes changes to the database.
	 */
	public static function cleanPermissions()
	{
		$corrections = self::getBadUsergroupPermissions();

		if ($corrections) {
			foreach ($corrections as $usergroup_id => $bad_perms) {
				App::getDb()->executeUpdate("
					DELETE FROM permissions
					WHERE usergroup_id = ? AND name IN (" . App::getDb()->quoteIn($bad_perms) . ")
				", array($usergroup_id));
			}
		}

		App::getDb()->exec("DELETE FROM permissions_cache");
	}
}