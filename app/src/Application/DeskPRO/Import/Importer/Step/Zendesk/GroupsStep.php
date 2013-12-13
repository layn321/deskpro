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

class GroupsStep extends AbstractZendeskStep
{
	public $on_rerun = false;

	public static function getTitle()
	{
		return 'Import Groups';
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);

		#----------------------------------------
		# Preload group membership data
		#----------------------------------------

		$raw = $this->zd->sendGetAll('group_memberships', 'group_memberships', array('per_page' => 100));
		$group_members = array();

		$check_uids = array();
		foreach ($raw as $r) {
			$check_uids[] = $r['user_id'];
		}

		if ($check_uids) {
			foreach ($raw as $r) {
				$user_id =  $this->getMappedNewId('zd_user_id', $r['user_id']);

				// Invalid user for whatever reason
				if (!$user_id) {
					continue;
				}

				if (!isset($group_members[$r['group_id']])) {
					$group_members[$r['group_id']] = array();
				}

				$group_members[$r['group_id']][] = $user_id;
			}
		}

		#----------------------------------------
		# Import the actual data
		#----------------------------------------

		$groups = $this->zd->sendGetAll('groups', 'groups', array('per_page' => 100));

		$this->db->exec("DELETE FROM agent_teams");
		$this->db->exec("DELETE FROM departments");

		$this->db->beginTransaction();
		try {
			foreach ($groups as $g) {
				$this->processGroup(
					$g,
					isset($group_members[$g['id']]) ? $group_members[$g['id']] : array()
				);
			}
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}

	public function processGroup($group_info, $group_members)
	{
		#------------------------------
		# Insert the group
		#------------------------------

		$insert_team = array();
		$insert_team['name'] = $group_info['name'];

		$this->db->insert('agent_teams', $insert_team);
		$group_id = $this->db->lastInsertId();
		$this->saveMappedId('zd_group_id', $group_info['id'], $group_id);


		#------------------------------
		# Insert the group as a dep as well
		#------------------------------

		$this->db->insert('departments', array('title' => $group_info['name'], 'is_tickets_enabled' => 1));
		$dep_id = $this->db->lastInsertId();
		$this->saveMappedId('zd_groupdep_id', $group_info['id'], $dep_id);

		#------------------------------
		# Insert members/perms
		#------------------------------

		$insert_bulk = array();
		$insert_bulk_perms = array();

		foreach ($group_members as $uid) {
			$insert_bulk[] = array(
				'team_id'   => $group_id,
				'person_id' => $uid
			);

			$insert_bulk_perms[] = array(
				'department_id' => $dep_id,
				'person_id'     => $uid,
				'app'           => 'tickets',
				'name'          => 'full',
				'value'         => '1',
			);
		}

		if ($insert_bulk) {
			$this->db->batchInsert('agent_team_members', $insert_bulk, true);
		}
		if ($insert_bulk_perms) {
			$this->db->batchInsert('department_permissions', $insert_bulk_perms, true);
		}
	}
}
