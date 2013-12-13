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

class Build1346358439 extends AbstractBuild
{
	public function run()
	{
		$this->out("Fix tickets in invalid parent departments");
		$parent_ids = $this->container->getDb()->fetchAllCol("
			SELECT DISTINCT(parent_id)
			FROM departments
			WHERE parent_id IS NOT NULL
		");

		foreach ($parent_ids as $pid) {
			$cid = $this->container->getDb()->fetchColumn("
				SELECT id
				FROM departments
				WHERE parent_id = $pid
				ORDER BY display_order ASC
				LIMIT 1
			");

			if ($cid) {
				$this->container->getDb()->executeUpdate('UPDATE tickets SET department_id = ? WHERE department_id = ?', array($cid, $pid));
				$this->container->getDb()->executeUpdate('UPDATE tickets_search_active SET department_id = ? WHERE department_id = ?', array($cid, $pid));
				$this->container->getDb()->executeUpdate('UPDATE tickets_search_message SET department_id = ? WHERE department_id = ?', array($cid, $pid));
				$this->container->getDb()->executeUpdate('UPDATE tickets_search_message_active SET department_id = ? WHERE department_id = ?', array($cid, $pid));
				$this->container->getDb()->executeUpdate('UPDATE chat_conversations SET department_id = ? WHERE department_id = ?', array($cid, $pid));
			}
		}
	}
}