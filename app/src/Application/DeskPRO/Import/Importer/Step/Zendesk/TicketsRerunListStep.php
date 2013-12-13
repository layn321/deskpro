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

namespace Application\DeskPRO\Import\Importer\Step\Zendesk;

use Orb\Util\Arrays;

class TicketsRerunListStep extends AbstractZendeskStep
{
	public $on_rerun = true;
	public $on_run = false;

	public static function getTitle()
	{
		return 'ReRun Tickets (Change List)';
	}

	public function countPages()
	{
		// We dont actually know how long it'll take,
		// but by setting 10 we at least have some paginiation/UI updates on the CLI
		// and this handles changes to 10,000.
		return 10;
	}

	public function run($page = 1)
	{
		if ($page == 1) {
			$this->db->replace('import_datastore', array('typename' => 'zd_tickets_rerun_list_cont', 'data' => 1));
		}

		$do_run = (bool)$this->db->fetchColumn("SELECT data FROM import_datastore WHERE typename = 'zd_tickets_rerun_list_cont'");
		if (!$do_run) {
			return;
		}

		$ticket_ids = $this->db->fetchColumn("SELECT data FROM import_datastore WHERE typename = 'zd_tickets_rerun_ids'");
		if ($ticket_ids) {
			$ticket_ids = @unserialize($ticket_ids);
		}

		if (!$ticket_ids) {
			$ticket_ids = array();
		}

		// Alter the cooldown period because the exports call timeout is 1 minute
		$this->zd->try_time_ratelimit = 61;

		$last_time = $this->db->fetchColumn("SELECT data FROM import_datastore WHERE typename = 'zd_tickets_rerun_lasttime'");

		while (1) {

			if (!$last_time || $last_time > (time() - 300)) {
				break;
			}

			$res = $this->zd->sendGet('exports/tickets', array(
				'start_time' => $last_time
			));

			if ($res->get('results') && count($res->get('results')) >= 1) {
				$last_time = $res->get('end_time');

				$x = 0;
				foreach ($res->get('results') as $res) {
					$x++;
					$ticket_ids[] = (int)$res['id'];
				}

				if (!$last_time) {
					$last_time = time();
				}

				$this->db->replace('import_datastore', array(
					'typename' => 'zd_tickets_rerun_lasttime',
					'data' => $last_time,
				));

				if ($x >= 1000) {
					$this->db->replace('import_datastore', array('typename' => 'zd_tickets_rerun_list_cont', 'data' => 1));
				} else {
					$this->db->replace('import_datastore', array('typename' => 'zd_tickets_rerun_list_cont', 'data' => 0));
				}

			// No more results
			} else {
				$this->db->replace('import_datastore', array('typename' => 'zd_tickets_rerun_list_cont', 'data' => 0));
				break;
			}

			// For anything but the last page, we only do one
			// request per page so we can update the % done indicator in the CLI.

			// If we're on page 10 then it means we might have more pages to fetch,
			// but no more pages will be request via the CLI steps, so we need to do them
			// all in this current invocation
			if ($page < 10) {
				break;
			}
		}

		$ticket_ids = array_unique($ticket_ids, SORT_NUMERIC);

		$this->db->replace('import_datastore', array(
			'typename' => 'zd_tickets_rerun_ids',
			'data' => serialize($ticket_ids)
		));
	}
}