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

class OrgsStep extends AbstractZendeskStep
{
	public $on_rerun = false;

	const PERPAGE = 100;

	public static function getTitle()
	{
		return 'Import Organizations';
	}

	public function countPages()
	{
		$res = $this->zd->sendGet('organizations', array('per_page' => 1));
		$count = (int)$res->get('count');

		$this->logMessage(sprintf("%d records in %d pages", $count, ceil($count / self::PERPAGE)));

		return ceil($count / self::PERPAGE);
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		$orgs = $this->getBatch($page);
		$this->db->exec("SET unique_checks = 0");
		$this->db->exec("SET foreign_key_checks = 0");

		$this->db->beginTransaction();
		try {
			foreach ($orgs as $org) {
				$this->processOrg($org);
			}
			$this->importer->flushSaveMappedIdBuffer();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		$this->db->exec("SET unique_checks = 1");
		$this->db->exec("SET foreign_key_checks = 1");

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}


	/**
	 * Process a single user
	 * @param $user_id
	 */
	public function processOrg($org_info)
	{
		#------------------------------
		# Insert the org
		#------------------------------

		$insert_org = array();
		$insert_org['name']         = $org_info['name'];
		$insert_org['date_created'] = date('Y-m-d H:i:s', strtotime($org_info['created_at']));

		$notes = '';
		if ($org_info['details']) {
			$notes = $org_info['details'] . "\n\n\n";
		}
		if ($org_info['notes']) {
			$notes .= $org_info['notes'];
		}

		$insert_org['summary'] = trim($notes);

		$this->db->insert('organizations', $insert_org);
		$org_id = $this->db->lastInsertId();

		$this->saveMappedId('zd_org_id', $org_info['id'], $org_id);

		#------------------------------
		# Insert domains
		#------------------------------

		if ($org_info['domain_names']) {
			$insert_bulk = array();
			foreach ($org_info['domain_names'] as $domain) {
				$row = array();
				$row['organization_id'] = $org_id;
				$row['domain'] = strtolower($domain);

				$insert_bulk[] = $row;
			}

			$this->db->batchInsert('organization_email_domains', $insert_bulk, true);
		}

		#------------------------------
		# Insert labels
		#------------------------------

		if ($org_info['tags']) {
			$insert_bulk = array();
			foreach ($org_info['tags'] as $tag) {
				$row = array();
				$row['organization_id'] = $org_id;
				$row['label'] = strtolower($tag);

				$insert_bulk[] = $row;
			}

			$this->db->batchInsert('labels_organizations', $insert_bulk, true);
		}
	}


	/**
	 * @param $page
	 * @return array
	 */
	public function getBatch($page)
	{
		$res = $this->zd->sendGet('organizations', array('per_page' => self::PERPAGE, 'page' => $page));

		$batch = $res->get('organizations');

		return $batch;
	}
}
