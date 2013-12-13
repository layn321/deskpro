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

class Build1352459603 extends AbstractBuild
{
	public function run()
	{
		$this->out("Give new agent_people.disable permission to people with agent_people.delete");

		#-------------------------
		# Permission groups
		#-------------------------

		$ug_ids = $this->container->getDb()->fetchAllCol("
			SELECT usergroup_id
			FROM permissions
			WHERE name = 'agent_people.delete' AND usergroup_id IS NOT NULL
		");
		$insert = array();
		foreach ($ug_ids as $id) {
			$insert[] = array('usergroup_id' => $id, 'name' => 'agent_people.disable', 'value' => 1);
		}
		if ($insert) {
			$this->container->getDb()->batchInsert('permissions', $insert);
		}

		#-------------------------
		# Permission overrides
		#-------------------------

		$person_ids = $this->container->getDb()->fetchAllCol("
			SELECT person_id
			FROM permissions
			WHERE name = 'agent_people.delete' AND person_id IS NOT NULL
		");
		$insert = array();
		foreach ($person_ids as $id) {
			$insert[] = array('person_id' => $id, 'name' => 'agent_people.disable', 'value' => 1);
		}
		if ($insert) {
			$this->container->getDb()->batchInsert('permissions', $insert);
		}

		#-------------------------
		# Clear caache
		#-------------------------

		if ($ug_ids || $person_ids) {
			$this->container->getDb()->executeUpdate("TRUNCATE TABLE permissions_cache");
		}
	}
}