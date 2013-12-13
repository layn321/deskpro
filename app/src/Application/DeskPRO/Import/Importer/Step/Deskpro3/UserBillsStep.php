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

class UserBillsStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import User Bills';
	}

	public function countPages()
	{
		$count = $this->getOldDb()->fetchColumn("SELECT id FROM user_bill ORDER BY id DESC LIMIT 1");
		if (!$count) {
			return 1;
		}

		return ceil($count / 1000);
	}

	public function preRunAll()
	{
		$this->importer->removeTableIndexes('ticket_charges');
	}

	public function postRunAll()
	{
		$this->importer->restoreTableIndexes('ticket_charges');
	}

	public function run($page = 1)
	{
		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		if ($page == 1) {
			$this->preRunAll();
		}

		$start = (($page-1) * 1000) + 1;
		$end   = $page * 1000;

		$batch = $this->getOldDb()->fetchAll("
			SELECT * FROM user_bill
			WHERE id BETWEEN $start AND $end
		");

		$this->getDb()->beginTransaction();
		try {

			foreach ($batch as $l) {
				$this->processEntry($l);
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		if ($page >= $this->countPages()) {
			$this->postRunAll();
		}

		$sub_end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $sub_end_time-$sub_start_time));
	}


	public function processEntry(array $entry)
	{
		$new_agent_id = $this->getMappedNewId('tech', $entry['techid']);
		if (!$new_agent_id) {
			return;
		}

		$new_person_id = $this->getMappedNewId('user', $entry['userid']);
		if (!$new_person_id) {
			return;
		}

		$new_ticket_id = $this->getMappedNewId('ticket', $entry['ticketid']);
		if (!$new_ticket_id) {
			return;
		}

		$amount = floatval($entry['charge']);
		if ($amount <= 0) {
			$amount = null;
		}

		$time = $entry['timecharge'] > 0 ? $entry['timecharge'] : null;

		if ($amount === null && $time === null) {
			return;
		}

		$new_org_id = $this->getDb()->fetchColumn('
			SELECT organization_id
			FROM tickets
			WHERE id = ?
		', array($new_person_id));
		if (!$new_org_id) {
			$new_org_id = null;
		}

		$insert = array(
			'id' => $entry['id'],
			'person_id' => $new_person_id,
			'agent_id' => $new_agent_id,
			'organization_id' => $new_org_id,
			'ticket_id' => $new_ticket_id,
			'date_created' => date('Y-m-d H:i:s', $entry['timestamp']),
			'charge_time' => $time,
			'amount' => $amount,
			'comment' => $entry['comments'],
		);

		$this->getDb()->insert('ticket_charges', $insert);
	}
}
