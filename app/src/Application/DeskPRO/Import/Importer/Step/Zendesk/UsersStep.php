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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Zendesk;

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Import\Importer\Step\Zendesk\User\ImportUser;

class UsersStep extends AbstractZendeskStep
{
	public $on_rerun = false;

	const PERPAGE = 100;

	/**
	 * @var array
	 */
	public $custom_field_info = array();

	/**
	 * @var array
	 */
	public $checked_org_ids = array();

	public static function getTitle()
	{
		return 'Import Users';
	}

	public function countPages()
	{
		$count = $this->db->fetchColumn("
			SELECT data
			FROM import_datastore
			WHERE typename = 'zd_users_cache_total'
		");

		$this->logMessage(sprintf("%d records in %d pages", $count, ceil($count / self::PERPAGE)));

		return ceil($count / self::PERPAGE);
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		$users = $this->getBatch($page);

		$this->db->beginTransaction();
		try {
			foreach ($users as $u) {
				$this->processUser($u);
			}
			$this->importer->flushSaveMappedIdBuffer();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}


	/**
	 * Process a single user
	 * @param $user_id
	 */
	public function processUser($user_info)
	{
		$import_user = new ImportUser();
		$import_user->importer = $this->importer;
		$import_user->import($user_info);
	}


	/**
	 * @param $page
	 * @return array
	 */
	public function getBatch($page)
	{
		$cached = $this->db->fetchColumn("
			SELECT data
			FROM import_datastore
			WHERE typename = 'zd_users_cache.p{$page}'
		");

		$res = null;
		if ($cached) {
			$res = @unserialize($cached);
		}

		if (!$res) {
			$res = $this->zd->sendGet('users', array('per_page' => self::PERPAGE, 'page' => $page));
		}

		$batch = $res->get('users');

		return $batch;
	}
}
