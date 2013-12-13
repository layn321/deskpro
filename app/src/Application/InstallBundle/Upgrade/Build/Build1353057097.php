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

class Build1353057097 extends AbstractBuild
{
	public function run()
	{
		$this->out("Ensure one department exists and correct bad departments");
		$count = $this->container->getDb()->count('departments', array('is_tickets_enabled' => 1));

		if (!$count) {
			// Insert default department
			$this->container->getDb()->insert('departments', array(
				'title' => 'Default',
				'is_tickets_enabled' => 1
			));

			$default_department = $this->container->getDb()->lastInsertId();

			// Give all agents access
			$agent_ids = $this->container->getDb()->fetchAllCol("
				SELECT id
				FROM people
				WHERE is_agent = 1
			");

			$batch = array();
			foreach ($agent_ids as $aid) {
				$batch[] = array(
					'department_id' => $default_department,
					'person_id'     => $aid,
					'app'           => 'tickets',
					'name'          => 'full',
					'value'         => 1,
				);
			}

			$this->container->getDb()->batchInsert('department_permissions', $batch);

			// Give 'everyone' access too
			$this->container->getDb()->insert('department_permissions', array(
				'department_id' => $default_department,
				'usergroup_id'  => 1,
				'app'           => 'tickets',
				'name'          => 'full',
				'value'         => 1,
			));
		} else {
			$default_department = $this->container->getEm()->getRepository('DeskPRO:Department')->getDefaultDepartment('ticket');
			$default_department = $default_department->id;
		}

		// Correct bad tickets
		$chat_deps = $this->container->getDb()->fetchAllCol("
			SELECT id
			FROM departments
			WHERE is_chat_enabled = 1
		");

		if ($chat_deps) {
			$this->container->getDb()->executeUpdate("
				UPDATE tickets
				SET department_id = ?
				WHERE department_id IN (" . implode(',', $chat_deps) . ") OR department_id IS NULL
			", array($default_department));
			$this->container->getDb()->executeUpdate("
				UPDATE tickets_search_active
				SET department_id = ?
				WHERE department_id IN (" . implode(',', $chat_deps) . ") OR department_id IS NULL
			", array($default_department));
		}

		// Clean permissions too
		\Application\DeskPRO\People\PermissionUtil::cleanPermissions();
	}
}