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

use Orb\Util\Arrays;

class UsersCacheStep extends AbstractZendeskStep
{
	public $on_rerun = false;

	const PERPAGE  = 100;
	const PERBATCH = 5;

	/**
	 * @var \Application\DeskPRO\CustomFields\FieldManager
	 */
	public $fieldmanager;

	public static function getTitle()
	{
		return 'Download Users';
	}

	public function countPages()
	{
		if ($this->importer->getConfig('with_preinserted_cache')) {
			return 0;
		}

		$this->db->replace('import_datastore', array(
			'typename' => 'zd_users_cache_time',
			'data' => time()
		));

		$res = $this->zd->sendGet('users', array('per_page' => 1));
		$count = (int)$res->get('count');

		$pages = ceil($count / self::PERPAGE);
		$batches = ceil($pages / 5);

		$this->db->replace('import_datastore', array(
			'typename' => 'zd_users_cache_pages',
			'data' => $pages
		));

		$this->db->replace('import_datastore', array(
			'typename' => 'zd_users_cache_total',
			'data' => $count
		));

		$this->logMessage(sprintf("%d records in %d pages fetched using %d batches of %d request", $count, $pages, $batches, self::PERBATCH));

		return $batches;
	}

	public function run($batch = 1)
	{
		if ($this->importer->getConfig('with_preinserted_cache')) {
			return;
		} else {
			if ($batch == 1) {
				$this->db->executeUpdate("DELETE FROM import_datastore WHERE typename LIKE 'zd_users_cache.%'");
			}
		}

		$reqs = array();

		for ($i = 1; $i <= self::PERBATCH; $i++) {
			$page = (($batch-1)*self::PERBATCH) + $i;

			$got = $this->db->fetchColumn("SELECT typename FROM import_datastore WHERE typename = ?", array('zd_users_cache.p'.$page));
			if ($got) {
				continue;
			}

			$reqs[$page] = array(
				'users',
				array('per_page' => self::PERPAGE, 'page' => $page)
			);
		}

		$results = $this->zd->sendGetMulti($reqs);

		$retry_pages = array();

		foreach ($results as $page => $info) {
			if ($info['exception'] || !$info['response']) {
				$retry_pages[$page] = $reqs[$page];
			} else {
				$this->db->replace('import_datastore', array(
					'typename' => 'zd_users_cache.p'.$page,
					'data' => serialize($info['response'])
				));
			}
		}

		if ($retry_pages) {
			// Sleep to get rid of rate limits
			sleep(8);
			$results = $this->zd->sendGetMulti($retry_pages);

			foreach ($results as $page => $info) {
				if ($info['exception'] || !$info['response']) {
				} else {
					$this->db->replace('import_datastore', array(
						'typename' => 'zd_users_cache.p'.$page,
						'data' => serialize($info['response'])
					));
				}
			}
		}
	}
}
