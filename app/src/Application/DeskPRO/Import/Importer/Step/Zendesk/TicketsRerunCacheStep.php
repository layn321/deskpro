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

class TicketsRerunCacheStep extends AbstractZendeskStep
{
	public $on_rerun = true;
	public $on_run = false;

	const PERPAGE = 100;
	const PERBATCH = 5;

	public static function getTitle()
	{
		return 'ReRun Tickets (Cache)';
	}

	public function countPages()
	{
		$ticket_ids = $this->db->fetchColumn("SELECT data FROM import_datastore WHERE typename = 'zd_tickets_rerun_ids'");
		if ($ticket_ids) {
			$ticket_ids = @unserialize($ticket_ids);
		}
		if (!$ticket_ids) {
			$ticket_ids = array();
		}

		$count = count($ticket_ids);
		$pages = ceil($count / self::PERPAGE);
		$batches = ceil($pages / self::PERBATCH);

		$this->db->replace('import_datastore', array(
			'typename' => 'zd_tickets_cache_rerun_pages',
			'data' => $pages
		));

		$this->db->replace('import_datastore', array(
			'typename' => 'zd_tickets_cache_rerun_total',
			'data' => $count
		));

		$this->logMessage(sprintf("%d records in %d pages fetched using %d batches of %d request", $count, $pages, $batches, self::PERBATCH));

		return $batches;
	}

	public function run($batch = 1)
	{
		$ticket_ids = $this->db->fetchColumn("SELECT data FROM import_datastore WHERE typename = 'zd_tickets_rerun_ids'");

		if ($ticket_ids) {
			$ticket_ids = @unserialize($ticket_ids);
		}
		if (!$ticket_ids) {
			$ticket_ids = array();
		}

		if ($batch == 1) {
			$this->db->executeUpdate("DELETE FROM import_datastore WHERE typename LIKE 'zd_tickets_rerun_cache.p%'");
		}

		if ($batch != 1) {
			// Cooldown from older rate limit
			sleep(60);
		}

		$all_ticket_ids = $ticket_ids;
		$ticket_ids = array_chunk($ticket_ids, self::PERPAGE);
		$page_ticket_ids = array();

		$reqs = array();

		for ($i = 1; $i <= self::PERBATCH; $i++) {
			$page = (($batch-1)*self::PERBATCH) + $i;
			$idx = $page-1;

			if (empty($ticket_ids[$idx])) {
				continue;
			}

			$page_ticket_ids = array_merge($page_ticket_ids, $ticket_ids[$idx]);

			$reqs[$page] = array(
				'tickets/show_many',
				array('per_page' => self::PERPAGE, 'ids' => implode(',', $ticket_ids[$idx]))
			);
		}

		$this->logMessage("Fetching page info with " . count($reqs) . " requests");

		$results = $this->zd->sendGetMulti($reqs);

		$retry_pages = array();

		foreach ($results as $page => $info) {
			if ($info['exception'] || !$info['response']) {
				$this->logMessage("Page $page error");
				$retry_pages[$page] = $reqs[$page];
			} else {
				$this->db->replace('import_datastore', array(
					'typename' => 'zd_tickets_rerun_cache.p'.$page,
					'data' => serialize($info['response'])
				));
			}
		}

		$this->logMessage("Finished requests with " . count($retry_pages) . " to retry");

		$try = 0;
		while ($try++ < 6 and $retry_pages) {
			sleep(30);
			$results = $this->zd->sendGetMulti($retry_pages);

			$retry_pages = array();
			foreach ($results as $page => $info) {
				if ($info['exception'] || !$info['response']) {
					$this->logMessage("Page $page error (on retry)");
					$retry_pages[$page] = $reqs[$page];
				} else {
					$this->db->replace('import_datastore', array(
						'typename' => 'zd_tickets_rerun_cache.p'.$page,
						'data' => serialize($info['response'])
					));
				}
			}
		}

		$this->logMessage("Finished requests retries, getting audits for " . count($page_ticket_ids) . " tickets");

		// Also get audits
		sleep(30);
		$this->zd->cacheManyTicketAudits($page_ticket_ids);

		$this->logMessage("Finished getting audits");
	}
}