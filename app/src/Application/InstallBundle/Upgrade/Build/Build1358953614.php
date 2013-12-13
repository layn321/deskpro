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

class Build1358953614 extends AbstractBuild
{
	public function run()
	{
		$this->out("Insert new default triggers");
		$this->execMutateSql("
			INSERT INTO `ticket_triggers` (`id`, `title`, `event_trigger`, `event_trigger_options`, `is_enabled`, `terms`, `terms_any`, `actions`, `sys_name`, `run_order`, `date_created`)
			VALUES
				(NULL, '', 'new.email.user', 'N;', 1, 'a:0:{}', 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:30:\"enable_new_ticket_confirmation\";s:7:\"options\";a:1:{s:7:\"enabled\";s:1:\"1\";}}}', 'newticket_confirm.email_user', -1000, '2013-01-23 15:02:09'),
				(NULL, '', 'new.web.user', 'N;', 1, 'a:0:{}', 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:30:\"enable_new_ticket_confirmation\";s:7:\"options\";a:1:{s:7:\"enabled\";s:1:\"1\";}}}', 'newticket_confirm.web_user', -1000, '2013-01-23 15:02:09'),
				(NULL, '', 'new.email.agent', 'N;', 0, 'a:0:{}', 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:30:\"enable_new_ticket_confirmation\";s:7:\"options\";a:1:{s:7:\"enabled\";s:1:\"1\";}}}', 'newticket_confirm.web_user', -1000, '2013-01-23 15:02:09'),
				(NULL, '', 'new.web.agent.portal', 'N;', 1, 'a:0:{}', 'a:0:{}', 'a:1:{i:0;a:2:{s:4:\"type\";s:30:\"enable_new_ticket_confirmation\";s:7:\"options\";a:1:{s:7:\"enabled\";s:1:\"1\";}}}', 'newticket_confirm.web_agent', -1000, '2013-01-23 15:02:09')
		");
	}
}