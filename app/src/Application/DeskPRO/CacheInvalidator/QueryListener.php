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

namespace Application\DeskPRO\CacheInvalidator;

use Orb\Util\Strings;
use Application\DeskPRO\App;

class QueryListener
{
	protected $is_executing = false;

	protected $updates = array();

	public function __construct()
	{
		\DpShutdown::add(array($this, 'sendUpdatesQuiet'));
	}

	public function sendUpdatesQuiet()
	{
		try {
			$this->sendUpdates();
		} catch (\Exception $e) {}
	}

	public function sendUpdates()
	{
		if (!$this->updates) {
			return;
		}

		$updates = array_flip($this->updates);
		$this->updates = array();

		$this->is_executing = true;

		if (isset($updates['publish_structure_cache'])) {
			App::getContainer()->getSystemService('publish_structure_cache')->flush();
		}

		if (isset($updates['department_permissions'])) {
			\Application\DeskPRO\People\PermissionUtil::cleanPermissions();
		}

		if (isset($updates['permissions'])) {
			App::getDb()->exec("DELETE FROM permissions_cache");
		}

		$this->is_executing = false;
	}

	public function handleQuery($sql, array $params)
	{
		if ($this->is_executing) return;
		$this->is_executing = true;

		$query_id = $this->getQueryIdent($sql, $params);
		if (!$query_id) {
			$this->is_executing = false;
			return;
		}

		switch ($query_id) {
			case 'update_departments':
			case 'insert_departments':
			case 'delete_departments':
			case 'update_department_permissions':
			case 'insert_department_permissions':
			case 'delete_department_permissions':
				$this->updates[] = 'department_permissions';
			case 'update_usergroups':
			case 'insert_usergroups':
			case 'delete_usergroups':
			case 'update_permissions':
			case 'insert_permissions':
			case 'delete_permissions':
				$this->updates[] = 'permissions';
				break;

			case 'update_article_categories':
			case 'insert_article_categories':
			case 'delete_article_categories':
			case 'update_downloads_categories':
			case 'insert_downloads_categories':
			case 'delete_downloads_categories':
			case 'update_feedback_categories':
			case 'insert_feedback_categories':
			case 'delete_feedback_categories':
			case 'update_news_categories':
			case 'insert_news_categories':
			case 'delete_news_categories':
				$this->updates[] = 'publish_structure_cache';
				$this->updates[] = 'permissions';
				break;

			case 'update_article_to_categories':
			case 'delete_article_to_categories':
				$this->updates[] = 'publish_structure_cache';
				$this->updates[] = 'permissions';
				break;

			case 'delete_articles':
			case 'delete_feedback':
			case 'delete_news':
			case 'delete_downloads':
			case 'insert_articles':
			case 'insert_news':
			case 'insert_downloads':
			case 'insert_feedback':
				$this->updates[] = 'publish_structure_cache';
				break;

			case 'update_articles':
			case 'update_news':
			case 'update_downloads':
			case 'update_feedback':
				if (strpos($sql, 'category_id') !== false) {
					$this->updates[] = 'publish_structure_cache';
				} elseif (strpos($sql, 'status')) {
					$this->updates[] = 'publish_structure_cache';
				}
				break;
		}

		$this->is_executing = false;
	}


	/**
	 * Get the ID of a query that we can try to match against caches.
	 *
	 * @param $sql
	 * @param array $params
	 * @return string
	 */
	public function getQueryIdent($sql, array $params)
	{
		if (preg_match('#/*QUERY_NAME\((.*?)\)*/#', $sql, $m)) {
			$query_name = $m[1];
		} else {
			if (preg_match('#^\s*SELECT#i', $sql)) {
				return null; // Ignore selects
			} else if (preg_match('#^\s*UPDATE#i', $sql)) {
				$query_type = 'UPDATE';
			} else if (preg_match('#^\s*INSERT#i', $sql)) {
				$query_type = 'INSERT';
			} else if (preg_match('#^\s*DELETE#i', $sql)) {
				$query_type = 'DELETE';
			} else {
				return null; // unknown
			}

			if (preg_match('#\s*(FROM|INSERT\s+INTO|UPDATE|DELETE\s+FROM)\s+(.*?)\s+#i', $sql, $m)) {
				$query_table = $m[2];
			} else {
				return null; // unknown table
			}

			$query_name = $query_type . '_' . $query_table;
		}

		return strtolower($query_name);
	}
}
