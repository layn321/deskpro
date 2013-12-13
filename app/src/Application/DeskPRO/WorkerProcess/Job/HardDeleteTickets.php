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
 * Goes through soft-deleted tickets that were deleted long ago,
 * and permanantly removes them now.
 */
class HardDeleteTickets extends AbstractJob
{
	const DEFAULT_INTERVAL = 86400;

	public function run()
	{
		$secs = App::getSetting('core_tickets.hard_delete_time');

		// 0 means disable
		if ($secs < 1) {
			return;
		}

		$date_cut = date('Y-m-d H:i:s', time() - $secs);

		#------------------------------
		# find tickets to proc
		#------------------------------

		$ticket_ids = App::getDb()->fetchAllCol("
			SELECT tickets_deleted.ticket_id
			FROM tickets_deleted
			LEFT JOIN tickets ON (tickets.id = tickets_deleted.ticket_id)
			WHERE tickets_deleted.date_created < ?
			AND tickets.id IS NOT NULL
			LIMIT 5000
		", array($date_cut));

		foreach ($ticket_ids as $ticket_id) {

			App::getDb()->beginTransaction();

			try {
				// Ticket log already has the deletion record, we're doing the physical delete of the actual rows here
				App::getDb()->delete('tickets_search_active', array('id' => $ticket_id));
				App::getDb()->delete('tickets_search_message', array('id' => $ticket_id));
				App::getDb()->delete('tickets_search_message_active', array('id' => $ticket_id));
				App::getDb()->delete('tickets_search_subject', array('id' => $ticket_id));
				App::getDb()->delete('tickets', array('id' => $ticket_id));
				App::getDb()->commit();
			} catch (\Exception $e) {
				App::getDb()->rollback();
				throw $e; // rethrow for error logging etc
			}
		}

		if ($ticket_ids) {
			$this->logStatus("Removed " . count($ticket_ids) . " old soft-deleted tickets");
		}
	}
}
