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

use Application\DeskPRO\Entity\Department;

class UserChatDepartmentsStep extends AbstractDeskpro3Step
{
	/**
	 * Existing departments read in
	 * @var array
	 */
	public $departments;

	public static function getTitle()
	{
		return 'Import Chat Departments';
	}

	public function run($page = 1)
	{
		$start_time = microtime(true);

		$this->departments = $this->getDb()->fetchAllKeyValue("SELECT id, title FROM departments WHERE parent_id IS NULL");
		foreach ($this->departments as &$title) {
			$title = strtolower($title);
		}

		$chat_deps = $this->getOldDb()->fetchAll("SELECT * FROM chat_dep ORDER BY displayorder ASC");

		$this->getDb()->beginTransaction();

		try {
			foreach ($chat_deps as $chat_dep) {
				$this->processCategory($chat_dep);
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("Done all categories. Took %.3f seconds.", $end_time-$start_time));
	}

	public function processCategory(array $chat_dep)
	{
		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('chat_dep', $chat_dep['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$chat_dep['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Try to find a name match on existing departments
		# that would have been imported from tickets before
		#------------------------------

		$title_l = strtolower($chat_dep['name']);
		foreach ($this->departments as $id => $dep_title) {
			if ($dep_title == $title_l) {
				// We found a match, so just map this chat department to th existing one,
				// and enable chat app on it

				// But only if its a top-level
				if (!$this->getDb()->fetchColumn("SELECT COUNT(*) FROM departments WHERE parent_id = ? LIMIT 1", array($id))) {
					$this->saveMappedId('chat_dep', $chat_dep['id'], $id);
					$this->getDb()->update('departments', array(
						'is_chat_enabled' => 1
					), array('id' => $chat_dep['id']));
					return;
				}
			}
		}

		#------------------------------
		# Have to create the department
		#------------------------------

		$dep = new Department();
		$dep->title = $chat_dep['name'];
		$dep->display_order = '1' . $chat_dep['displayorder'];
		$dep->is_tickets_enabled = false;
		$dep->is_chat_enabled = true;

		$this->getEm()->persist($dep);
		$this->getEm()->flush();

		$this->getDb()->insert('department_permissions', array(
			'usergroup_id' => 1,
			'department_id' => $dep->id,
			'app' => 'chat',
			'name' => 'full',
			'value' => 1
		));

		$this->saveMappedId('chat_dep', $chat_dep['id'], $dep->id);
	}
}
