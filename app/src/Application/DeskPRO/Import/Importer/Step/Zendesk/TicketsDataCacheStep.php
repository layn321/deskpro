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

use Application\DeskPRO\Import\Importer\Step\Zendesk\Ticket\ImportTicket;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

class TicketsDataCacheStep extends AbstractZendeskStep
{
	public $on_rerun = false;

	const PERPAGE = 100;

	public static function getTitle()
	{
		return 'Download Ticket Data';
	}

	public function countPages()
	{
		$count = $this->db->fetchColumn("
			SELECT data
			FROM import_datastore
			WHERE typename = 'zd_tickets_cache_total'
		");

		$this->logMessage(sprintf("%d records in %d pages", $count, ceil($count / self::PERPAGE)));

		return ceil($count / self::PERPAGE);
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		$tickets = $this->getBatch($page);
		$ids = Arrays::flattenToIndex($tickets, 'id');

		if ($ids) {
			$this->zd->cacheManyTicketAudits($ids);
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}


	/**
	 * @param $page
	 * @return array
	 */
	public function getBatch($page)
	{
		$this->logMessage(sprintf("Getting batch of %d (page %d)", self::PERPAGE, $page));
		$res = $this->zd->getTicketListPageResponse($page-1);
		$batch = $res->get('tickets');
		return $batch;
	}
}