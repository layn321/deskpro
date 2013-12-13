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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\TicketAttachment;
use Application\DeskPRO\Entity\TicketParticipant;

class TicketsRerunCacheStep extends AbstractDeskpro3Step
{
	public $on_rerun = true;
	public $on_run = false;

	public static function getTitle()
	{
		return 'Import Tickets (ReRun Cache)';
	}

	public function countPages()
	{
		return 1;
	}

	public function run($page = 1)
	{
		$this->logMessage("Fetching tickets to rerun");

		$days = $this->importer->getConfig('rerun_max_date');
		if (!$days) {
			$days = 35;
		}
		$max_time = time() - ($days * 86400);

		$min_ticket_id = $this->importer->olddb->fetchColumn("
			SELECT id
			FROM ticket
			WHERE timestamp_opened >= $max_time
			ORDER BY id ASC
			LIMIT 1
		");

		if (!$min_ticket_id) {
			$min_ticket_id = 0;
		}

		$this->logMessage("-- Oldest ticket: $min_ticket_id ($days days old)");

		$time = $this->db->fetchColumn("
			SELECT data
			FROM import_datastore
			WHERE typename = 'dp3_tickets_rerun_lasttime'
		");

		$this->logMessage("-- Or change time newer than: $time (". date('Y-m-d H:i:s', $time) . ")");

		$ticket_ids = $this->importer->olddb->fetchAllCol("
			SELECT id
			FROM ticket
			WHERE
				id >= $min_ticket_id
				AND (
					status IN ('awaiting_user', 'awaiting_tech')
					OR timestamp_closed >= $time
					OR timestamp_user_waiting >= $time
					OR timestamp_tech_waiting >= $time
				)
		");

		$this->db->replace('import_datastore', array(
			'typename' => 'dp3_tickets_rerun_ids',
			'data' => serialize($ticket_ids)
		));

		$this->db->replace('import_datastore', array(
			'typename' => 'dp3_tickets_rerun_lasttime',
			'data' => time()
		));

		$this->logMessage(sprintf("-- %s tickets to rerun: %s", count($ticket_ids), implode(', ', $ticket_ids)));
	}
}