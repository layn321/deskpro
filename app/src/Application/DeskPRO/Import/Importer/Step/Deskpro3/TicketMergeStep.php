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

class TicketMergeStep extends AbstractDeskpro3Step
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	public static function getTitle()
	{
		return 'Import Ticket Merges';
	}

	public function countPages()
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM ticket_merge");
		if (!$count) {
			return 1;
		}

		return ceil($count / 1000);
	}

	public function run($page = 1)
	{
		$start = ($page - 1) * 1000;
		$batch = $this->getOldDb()->fetchAll("
			SELECT * FROM ticket_merge
			ORDER BY old_id ASC
			LIMIT $start, 1000
		");

		$this->getDb()->beginTransaction();
		try {
			foreach ($batch as $l) {
				$this->processMerge($l);
			}
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	public function processMerge(array $merge_info)
	{
		$new_ticket_id = $this->getMappedNewId('ticket', $merge_info['new_id']);
		if (!$new_ticket_id) {
			return;
		}

		if (!isset($merge_info['old_authcode'])) {
			return;
		}

		$this->getDb()->replace('import_datastore', array(
			'typename' => 'dp3_ticketmerge_' . $merge_info['old_ref'],
			'data' => serialize(array(
				'old_auth' => $merge_info['old_authcode'],
				'new_id' => $new_ticket_id
			))
		));
	}
}
