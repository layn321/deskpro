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

class TicketDeleteLogStep extends AbstractDeskpro3Step
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	public static function getTitle()
	{
		return 'Import Ticket Delete Logs';
	}

	public function countPages()
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM ticket_delete_log");
		if (!$count) {
			return 1;
		}

		return ceil($count / 1000);
	}

	public function preRunAll()
	{
		$this->importer->removeTableIndexes('tickets_deleted');
	}

	public function postRunAll()
	{
		$this->importer->restoreTableIndexes('tickets_deleted');
	}

	public function run($page = 1)
	{
		if ($page == 1) {
			$this->preRunAll();
		}

		$start = ($page - 1) * 1000;
		$batch = $this->getOldDb()->fetchAll("
			SELECT * FROM ticket_delete_log
			ORDER BY id ASC
			LIMIT $start, 1000
		");

		$this->getDb()->beginTransaction();
		try {
			foreach ($batch as $l) {
				$this->processDeleteLog($l);
			}
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		if ($page >= $this->countPages()) {
			$this->postRunAll();
		}
	}

	public function processDeleteLog(array $delete_log)
	{
		$by_agent = $this->getMappedNewId('tech', $delete_log['techid']);
		if (!$by_agent) {
			return;
		}

		$this->getDb()->replace('tickets_deleted', array(
			'ticket_id' => $delete_log['ticketid'],
			'by_person_id' => $by_agent,
			'reason' => $delete_log['subject'],
			'date_created' => date('Y-m-d H:i:s', $delete_log['timestamp'])
		));
	}
}
