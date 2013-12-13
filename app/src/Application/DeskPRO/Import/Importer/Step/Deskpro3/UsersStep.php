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

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Import\Importer\Step\Deskpro3\User\ImportUser;

class UsersStep extends AbstractDeskpro3Step
{
	const PERPAGE = 1000;

	/**
	 * @var array
	 */
	public $custom_field_info = array();

	/**
	 * @var \Application\DeskPRO\CustomFields\FieldManager
	 */
	public $fieldmanager;

	/**
	 * @var \Application\DeskPRO\Entity\Usersource[]
	 */
	public $usersources;

	public static function getTitle()
	{
		return 'Import Users';
	}

	public function countPages()
	{
		$count = $this->olddb->fetchColumn("SELECT id FROM user ORDER BY id DESC LIMIT 1");
		if (!$count) {
			return 1;
		}

		return ceil($count / self::PERPAGE);
	}

	public function preRunAll()
	{
		$this->importer->removeTableIndexes('people');
		$this->importer->removeTableIndexes('custom_data_person');
		$this->importer->removeTableIndexes('person2usergroups');
		$this->importer->removeTableIndexes('people_emails');
	}

	public function postRunAll()
	{
		// - Some DP installs have bad email data which could potentially
		// result in dupe email addresses. This means when we add the unique index,
		// it'll erorr out.
		// - So here we are appending a unique string to the dupes, then
		// an agent can go in later to merge accounts if they are real
		$sql = "
			SELECT id, COUNT(*) AS count
			FROM people_emails
			GROUP BY email
			HAVING count > 1
			LIMIT 5000
		";

		while ($ids = $this->db->fetchAllCol($sql)) {
			$ids_in = implode(',', $ids);
			$this->db->executeUpdate("
				UPDATE people_emails
				SET email = CONCAT(email, '.', id, '.importer-duplicate')
				WHERE id IN ($ids_in)
			");
		}

		$this->importer->restoreTableIndexes('people');
		$this->importer->restoreTableIndexes('custom_data_person');
		$this->importer->restoreTableIndexes('person2usergroups');
		$this->importer->restoreTableIndexes('people_emails');
	}

	public function run($page = 1)
	{
		if ($page == 1) {
			$this->preRunAll();
		}

		$this->custom_field_info = $this->olddb->fetchAll("SELECT * FROM user_def");
		$this->fieldmanager = $this->getContainer()->getSystemService('person_fields_manager');
		$this->fieldmanager->getFields();

		$this->usersources = $this->getEm()->getRepository('DeskPRO:Usersource')->getAllUsersources(false);

		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		$users = $this->getBatch($page);
		$this->db->exec("SET unique_checks = 0");
		$this->db->exec("SET foreign_key_checks = 0");

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

		$this->db->exec("SET unique_checks = 1");
		$this->db->exec("SET foreign_key_checks = 1");

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));

		if ($page >= $this->countPages()) {
			$this->postRunAll();
		}
	}


	/**
	 * Process a single user
	 * @param $user_id
	 */
	public function processUser($user_batch)
	{
		$import_user = new ImportUser();
		$import_user->importer          = $this->importer;
		$import_user->custom_field_info = $this->custom_field_info;
		$import_user->fieldmanager      = $this->fieldmanager;
		$import_user->usersources       = $this->usersources;

		$import_user->importUser($user_batch);
	}


	/**
	 * @param $page
	 * @return array
	 */
	public function getBatch($page)
	{
		$start = (($page-1) * self::PERPAGE) + 1;
		$end   = $page * self::PERPAGE;

		$between_where = "BETWEEN $start AND $end";

		#------------------------------
		# Fetch ticket
		#------------------------------

		$q = $this->olddb->query("SELECT * FROM user WHERE id $between_where");
		$q->execute();

		$batch = array();

		while ($user = $q->fetch(\PDO::FETCH_ASSOC)) {
			$batch[$user['id']] = array(
				'user' => $user,
				'user_map' => array(),
				'user_deskpro' => array(),
				'user_company_id' => 0,
				'user_email' => array(),
				'usergroup_ids' => array(),
			);
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch user_map
		#------------------------------

		$dp_source_id = (int)$this->olddb->fetchColumn("SELECT id FROM user_source WHERE module = 'Dp' LIMIT 1");

		$q = $this->olddb->query("SELECT * FROM user_map WHERE localid $between_where AND sourceid = $dp_source_id");
		$q->execute();

		$remote_ids = array();
		$remote_id_map = array();
		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['localid']])) continue;
			$batch[$r['localid']]['user_map'][] = $r;
			$remote_ids[] = $r['remoteid'];
			$remote_id_map[$r['remoteid']] = $r['localid'];
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch user_deskpro
		#------------------------------

		if ($remote_ids) {

			$remote_ids = \Orb\Util\Arrays::castToType($remote_ids, 'int');
			$remote_ids = \Orb\Util\Arrays::removeFalsey($remote_ids);
			$remote_ids = implode(',', $remote_ids);

			if ($remote_ids) {
				$q = $this->olddb->query("SELECT * FROM user_deskpro WHERE id IN ($remote_ids)");
				$q->execute();

				while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
					if (!isset($remote_id_map[$r['id']])) continue;
					$localid = $remote_id_map[$r['id']];
					if (!isset($batch[$localid])) continue;
					$batch[$localid]['user_deskpro'] = $r;
				}
				$q->closeCursor();
				unset($q);
			}
		}

		unset($remote_ids, $remote_id_map);

		#------------------------------
		# Fetch user_company_id
		#------------------------------

		$q = $this->olddb->query("SELECT user, company FROM user_member_company WHERE user $between_where");
		$q->execute();

		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['user']])) continue;
			$batch[$r['user']]['user_company_id'] = $r['company'];
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch user_email
		#------------------------------

		$q = $this->olddb->query("SELECT * FROM user_email WHERE userid $between_where");
		$q->execute();

		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['userid']])) continue;
			$batch[$r['userid']]['user_email'][] = $r;
		}
		$q->closeCursor();
		unset($q);

		#------------------------------
		# Fetch usergroup_ids
		#------------------------------

		$q = $this->olddb->query("SELECT user, usergroup FROM user_member_groups WHERE user $between_where");
		$q->execute();

		while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
			if (!isset($batch[$r['user']])) continue;
			$batch[$r['user']]['usergroup_ids'][] = $r['usergroup'];
		}
		$q->closeCursor();
		unset($q);

		return $batch;
	}
}
