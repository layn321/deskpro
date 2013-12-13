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
use Application\DeskPRO\Entity\Article;

/**
 * Archives old tickets
 */
class ArchiveTickets extends AbstractJob
{
	const DEFAULT_INTERVAL = 14400; // 4 hours

	public function run()
	{
		if (!App::getSetting('core_tickets.use_archive')) {
			return;
		}

		$datecut = date('Y-m-d H:i:s', time() - App::getSetting('core_tickets.auto_archive_time'));

		$ticket_ids = App::getDb()->fetchAllCol("
			SELECT id FROM tickets
			WHERE status = 'resolved' AND date_resolved < ?
			LIMIT 3000
		", array($datecut));

		$count = count($ticket_ids);
		$ticket_ids = array_chunk($ticket_ids, 500, false);

		$details_arr = serialize(array(
			'old_status' => 'resolved',
			'new_status' => 'closed'
		));

		$date_created = date('Y-m-d H:i:s');

		foreach ($ticket_ids as $ids) {
			$ids_str = implode(',', $ids);

			$batch = array();
			foreach ($ids as $id) {
				$batch[] = array(
					'ticket_id'    => $id,
					'action_type'  => 'changed_status',
					'id_before'    => 200,
					'id_after'     => 210,
					'details'      => $details_arr,
					'date_created' => $date_created
				);
			}

			App::getDb()->executeUpdate("
				UPDATE tickets
				SET status = 'closed' WHERE id IN ($ids_str)
			");

			App::getDb()->batchInsert('tickets_logs', $batch);

			App::getDb()->executeUpdate("
				DELETE FROM tickets_search_active
				WHERE id IN ($ids_str)
			");
		}

		if ($count) {
			$this->logStatus("$count tickets archived");
		}
	}
}
