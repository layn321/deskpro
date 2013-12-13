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

class Build1366194317 extends AbstractBuild
{
	public function run()
	{
		$this->out("Move trigger for sending ticket email from agent newticket form");

		$is_enabled = $this->container->getDb()->fetchColumn("
			SELECT is_enabled
			FROM ticket_triggers
			WHERE sys_name = 'newticket_confirm.web_agent'
			LIMIT 1
		");

		$this->container->getDb()->delete('ticket_triggers', array('sys_name' => 'newticket_confirm.web_agent'));

		$this->container->getDb()->replace('settings', array(
			'name'  => 'core_tickets.new_default_send_user_notify',
			'value' => $is_enabled ? 1 : 0
		));
	}
}