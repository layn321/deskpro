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

class Build1357828655 extends AbstractBuild
{
	public function run()
	{
		$this->out("Enable new Twitter notifications");

		$this->execMutateSql("
			INSERT IGNORE INTO people_prefs
				(person_id, name, value_str, value_array)
			SELECT person_id, 'agent_notif.tweet_assign_self.alert', '1', 'N;'
			FROM people_prefs
			WHERE name = 'agent_notif.task_assign_self.alert'
		");
		$this->execMutateSql("
			INSERT IGNORE INTO people_prefs
				(person_id, name, value_str, value_array)
			SELECT person_id, 'agent_notif.tweet_assign_self.email', '1', 'N;'
			FROM people_prefs
			WHERE name = 'agent_notif.task_assign_self.email'
		");

		$this->execMutateSql("
			INSERT IGNORE INTO people_prefs
				(person_id, name, value_str, value_array)
			SELECT person_id, 'agent_notif.tweet_assign_team.alert', '1', 'N;'
			FROM people_prefs
			WHERE name = 'agent_notif.task_assign_team.alert'
		");
		$this->execMutateSql("
			INSERT IGNORE INTO people_prefs
				(person_id, name, value_str, value_array)
			SELECT person_id, 'agent_notif.tweet_assign_team.email', '1', 'N;'
			FROM people_prefs
			WHERE name = 'agent_notif.task_assign_team.email'
		");

		$this->execMutateSql("
			INSERT IGNORE INTO people_prefs
				(person_id, name, value_str, value_array)
			SELECT person_id, 'agent_notif.tweet_reply.alert', '1', 'N;'
			FROM people_prefs
			WHERE name = 'agent_notif.task_due.alert'
		");
		$this->execMutateSql("
			INSERT IGNORE INTO people_prefs
				(person_id, name, value_str, value_array)
			SELECT person_id, 'agent_notif.tweet_reply.email', '1', 'N;'
			FROM people_prefs
			WHERE name = 'agent_notif.task_due.email'
		");
	}
}