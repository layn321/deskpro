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

class Build1352293755 extends AbstractBuild
{
	public function run()
	{
		$this->out("Change department permissions to support multiple");
		$this->execMutateSql("ALTER TABLE department_permissions ADD name VARCHAR(50) NOT NULL, ADD value LONGTEXT DEFAULT NULL");

		// existing rows indicate viewing permissions
		$this->execMutateSql("UPDATE department_permissions SET name = 'full', value = 1");
		$this->execMutateSql("
			INSERT IGNORE INTO department_permissions
				(department_id, usergroup_id, person_id, app, name, value)
			SELECT department_id, null, person_id, app, 'assign', 1
			FROM department_permissions
			WHERE person_id IS NOT NULL AND app = 'tickets' AND name = 'full' AND value = 1
		");
	}
}