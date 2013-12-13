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
 * Goes through tickets marked as spam and deletes them
 */
class DeleteSpamTickets extends AbstractJob
{
	const DEFAULT_INTERVAL = 86400;

	public function run()
	{
		$secs = App::getSetting('core_tickets.spam_delete_time');

		// 0 means disable
		if ($secs < 1) {
			return;
		}

		$secs = 0;
		$date_cut = new \DateTime('@' . (time() - $secs));

		#------------------------------
		# find tickets to proc
		#------------------------------

		$ticket_count = 0;

		$all_tickets = App::getDb()->fetchAll("
			SELECT id, person_id
			FROM tickets
			WHERE tickets.hidden_status = 'spam' AND tickets.date_status < ?
			LIMIT 1000
		", array($date_cut->format('Y-m-d H:i:s')));

		$this->logger->log(sprintf("[DeleteSpamTickets] %d tickets to delete", count($all_tickets)), 'DEBUG');
		$date_str = date('Y-m-d H:i:s');

		foreach ($all_tickets as $ticket) {

			App::getDb()->beginTransaction();
			try {
				$this->logger->log(sprintf("[DeleteSpamTickets] Deleted ticket %d", $ticket['id']), 'DEBUG');

				App::getDb()->delete('tickets_deleted', array('ticket_id' => $ticket['id']));
				App::getDb()->replace('tickets_deleted', array('ticket_id' => $ticket['id'], 'by_person_id' => null, 'new_ticket_id' => 0, 'date_created' => $date_str, 'reason' => 'Deleted as spam (system cleanup)'));

				App::getDb()->delete('tickets', array('id' => $ticket['id']));
				App::getDb()->delete('tickets_search_active', array('id' => $ticket['id']));
				App::getDb()->delete('tickets_search_message', array('id' => $ticket['id']));
				App::getDb()->delete('tickets_search_message_active', array('id' => $ticket['id']));
				App::getDb()->executeUpdate("DELETE FROM tickets_search_subject WHERE id = ?", array($ticket['id']));

				$ticket_count++;

				App::getDb()->commit();
			} catch (\Exception $e) {
				App::getDb()->rollback();
				throw $e;
			}
		}

		if ($ticket_count) {
			$this->logStatus("Removed " . count($ticket_count) . " spam tickets");
		}
	}
}
