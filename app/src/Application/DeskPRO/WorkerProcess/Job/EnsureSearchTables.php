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
 * @subpackage WorkerProcess
 */

namespace Application\DeskPRO\WorkerProcess\Job;

use Application\DeskPRO\App;
use Application\DeskPRO\Log\Logger;

/**
 * Goes through queued messages
 */
class EnsureSearchTables extends AbstractJob
{
	const DEFAULT_INTERVAL = 60;

	public function run()
	{
		$last_search_refill = App::getSetting('core.last_searchtables_refill');
		if (!$last_search_refill) {
			$last_search_refill = 0;
		}

		// If we've done a refill within the last 15 mins,
		// dont try again (dont want to continously refill, could slow everything down)
		if ($last_search_refill > strtotime('-15 minutes')) {
			return;
		}

		$do_refill = false;
		if (App::getSetting('core.do_searchtables_refill')) {
			$do_refill = true;
		} else {
			// Check to see if the search table isnt already filled
			$has_one = App::getDb()->fetchColumn("SELECT id FROM tickets_search_active LIMIT 1");
			if (!$has_one) {
				$has_one = App::getDb()->fetchColumn("SELECT id FROM tickets WHERE status IN ('awaiting_agent', 'awaiting_user') LIMIT 1");
				if ($has_one) {
					$do_refill = true;
				}
			}
		}

		if ($do_refill) {
			App::getContainer()->getSettingsHandler()->setSetting('core.last_searchtables_refill', time());
			App::getContainer()->getSettingsHandler()->setSetting('core.do_searchtables_refill', 0);

			App::getEntityRepository('DeskPRO:Ticket')->fillSearchTable();
			$this->logStatus("Filled tickets_search_active table");

			// Broadcast a refresh event to all agents
			$cm = new \Application\DeskPRO\Entity\ClientMessage();
			$cm->fromArray(array(
				'channel' => 'agent.ui.reload',
				'data' => array(
					'type'        => 'admin',
					'person_id'   => 0,
					'person_name' => 'System'
				)
			));

			App::getOrm()->persist($cm);
			App::getOrm()->flush();
		}
	}
}
