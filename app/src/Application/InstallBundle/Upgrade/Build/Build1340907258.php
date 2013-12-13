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

class Build1340907258 extends AbstractBuild
{
	public function run()
	{
		$this->out("Fix bad ticket_send_email action");

		$bad = $this->container->getDb()->fetchAll("SELECT id, actions FROM ticket_triggers WHERE actions LIKE '%ticket_send_email%'");
		foreach ($bad as $tr) {
			$tr['actions'] = unserialize($tr['actions']);
			$change = false;
			foreach ($tr['actions'] as &$act) {
				if ($act['type'] == 'ticket_send_email') {
					$act['type'] = 'send_ticket_email';
					$change = true;
				}
			}

			if ($change) {
				$this->out("Fixing trigger #{$tr['id']}");

				$tr['actions'] = serialize($tr['actions']);
				$this->container->getDb()->update('ticket_triggers', array('actions' => $tr['actions']), array('id' => $tr['id']));
			}
		}
	}
}