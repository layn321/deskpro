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

class Build1354098581 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add default triggers for assignment by email");

		$this->execMutateSql("
			INSERT INTO `ticket_triggers` (`id`, `title`, `event_trigger`, `event_trigger_options`, `is_enabled`, `terms`, `terms_any`, `actions`, `sys_name`, `run_order`, `date_created`)
			VALUES
				(NULL, '', 'new.email.agent', 'a:0:{}', 1, 'a:0:{}', 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:5:\"agent\";s:7:\"options\";a:1:{s:5:\"agent\";s:1:\"0\";}}}', 'agent_email_fwd_assignment', 120, '2012-11-28 10:26:06')
		");

		$this->execMutateSql("
			INSERT INTO `ticket_triggers` (`id`, `title`, `event_trigger`, `event_trigger_options`, `is_enabled`, `terms`, `terms_any`, `actions`, `sys_name`, `run_order`, `date_created`)
			VALUES
				(NULL, '', 'update.agent', 'a:0:{}', 1, 'a:2:{i:0;a:3:{s:4:\"type\";s:12:\"is_via_email\";s:2:\"op\";s:2:\"is\";s:7:\"options\";a:1:{s:2:\"do\";s:1:\"1\";}}i:1;a:3:{s:4:\"type\";s:5:\"agent\";s:2:\"op\";s:2:\"is\";s:7:\"options\";a:1:{s:5:\"agent\";s:1:\"0\";}}}', 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:5:\"agent\";s:7:\"options\";a:1:{s:5:\"agent\";s:2:\"-1\";}}}', 'agent_email_reply_assignment', 30, '2012-11-28 10:44:17')
		");
	}
}