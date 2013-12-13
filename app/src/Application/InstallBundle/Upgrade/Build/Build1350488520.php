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

class Build1350488520 extends AbstractBuild
{
	protected $deps = array();
	protected $id_map = array();

	public function run()
	{
		$this->out("Copying out chat departments that are also ticket departments");

		$this->deps = $this->container->getDb()->fetchAllKeyed("
			SELECT * FROM departments
			ORDER BY id ASC
		", array(), 'id');

		$proc_deps = $this->container->getDb()->fetchAllCol("
			SELECT id FROM departments
			WHERE is_tickets_enabled = 1 AND is_chat_enabled = 1
			ORDER BY id ASC
		");

		foreach ($proc_deps as $dep_id) {
			$this->container->getDb()->beginTransaction();
			try {
				$dep = $this->deps[$dep_id];
				$this->handleDep($dep);
				$this->container->getDb()->commit();
			} catch (\Exception $e) {
				$this->container->getDb()->rollback();
				throw $e;
			}
		}

		// Now update those deps so they arent chat anymore
		$this->container->getDb()->executeUpdate("
			UPDATE departments
			SET is_chat_enabled = 0
			WHERE is_tickets_enabled = 1 AND is_chat_enabled = 1
		");
	}

	protected function handleDep(array $dep)
	{
		$new_dep = $dep;
		unset($new_dep['id']);
		unset($new_dep['is_tickets_enabled']);

		if ($new_dep['parent_id']) {
			if (!isset($this->id_map[$new_dep['parent_id']])) {
				$parent_dep = $this->deps[$new_dep['parent_id']];
				$this->handleDep($parent_dep);
			}

			$new_dep['parent_id'] = $this->id_map[$new_dep['parent_id']];
		}

		// Insert new department
		$this->container->getDb()->insert('departments', $new_dep);
		$new_dep['id'] = $this->container->getDb()->lastInsertId();

		$this->id_map[$dep['id']] = $new_dep['id'];

		// Copy permissions
		$perms = $this->container->getDb()->fetchAll("
			SELECT usergroup_id, person_id
			FROM department_permissions
			WHERE app = 'chat' AND department_id = ?
		", array($dep['id']));

		foreach ($perms as $p) {
			$this->container->getDb()->insert('department_permissions', array(
				'department_id' => $new_dep['id'],
				'usergroup_id'  => $p['usergroup_id'],
				'person_id'     => $p['person_id'],
				'app'           => 'chat',
			));
		}

		// Update chats
		$this->container->getDb()->executeQuery("
			UPDATE chat_conversations
			SET department_id = ?
			WHERE department_id = ?
		", array($new_dep['id'], $dep['id']));
	}
}