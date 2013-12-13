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

class Build1351089837 extends AbstractBuild
{
	public function run()
	{
		$this->out("Insert new default triggers to set 'From' name");
		$this->execMutateSql("
			INSERT INTO `ticket_triggers` (`id`, `title`, `event_trigger`, `is_enabled`, `terms`, `actions`, `sys_name`, `run_order`, `date_created`, `event_trigger_options`, `terms_any`)
			VALUES
				(NULL, '', 'update.agent', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:1:\"0\";}}}', NULL, 0, '2012-10-24 14:10:26', 'a:0:{}', 'a:0:{}'),
				(NULL, '', 'update.user', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:1:\"0\";}}}', NULL, 0, '2012-10-24 14:12:46', 'a:0:{}', 'a:0:{}'),
				(NULL, '', 'new.email.user', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:5:\"agent\";}}}', NULL, 0, '2012-10-24 14:14:05', 'a:0:{}', 'a:0:{}'),
				(NULL, '', 'new.web.user', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:5:\"agent\";}}}', NULL, 0, '2012-10-24 14:14:05', 'a:0:{}', 'a:0:{}'),
				(NULL, '', 'new.email.agent', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:1:\"0\";}}}', NULL, 0, '2012-10-24 14:14:05', 'a:0:{}', 'a:0:{}'),
				(NULL, '', 'new.web.agent.portal', 1, 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:21:\"set_initial_from_name\";s:7:\"options\";a:2:{s:9:\"from_name\";s:18:\"{{performer.name}}\";s:7:\"to_whom\";s:1:\"0\";}}}', NULL, 0, '2012-10-24 14:14:05', 'a:0:{}', 'a:0:{}');
		");
	}
}