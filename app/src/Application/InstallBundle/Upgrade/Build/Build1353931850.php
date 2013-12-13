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

class Build1353931850 extends AbstractBuild
{
	public function run()
	{
		$this->out("Update auto_close.warn_user trigger");

		$this->execMutateSql("
			UPDATE ticket_triggers
			SET
				terms = 'a:1:{i:0;a:3:{s:4:\"type\";s:6:\"status\";s:2:\"op\";s:2:\"is\";s:7:\"options\";a:1:{s:6:\"status\";a:1:{i:0;s:14:\"awaiting_agent\";}}}}',
				actions = 'a:1:{i:0;a:2:{s:4:\"type\";s:25:\"send_autoclose_warn_email\";s:7:\"options\";a:2:{s:13:\"template_name\";s:51:\"DeskPRO:emails_user:ticket-autoclose-warn.html.twig\";s:10:\"new_option\";s:0:\"\";}}}',
				title = '',
				sys_name = 'auto_close.warn_user'
			WHERE title = 'auto_close.warn_user'
		");
	}
}