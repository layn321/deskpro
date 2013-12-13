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

class TechTimelogStep extends AbstractDeskpro3Step
{
	public $on_fast = false;

	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	/**
	 * @var array
	 */
	public $batch_insert = array();

	public static function getTitle()
	{
		return 'Import Tech Timelog';
	}

	public function countPages()
	{
		$count = $this->getOldDb()->fetchColumn("SELECT id FROM tech_timelog ORDER BY id DESC LIMIT 1");
		if (!$count) {
			return 1;
		}

		if ($count > 45000) {
			return 1;
		}

		return ceil($count / 1000);
	}

	public function preRunAll()
	{
		$this->importer->removeTableIndexes('agent_activity');
	}

	public function postRunAll()
	{
		$this->importer->restoreTableIndexes('agent_activity');
	}

	public function run($page = 1)
	{
		$count = $this->getOldDb()->fetchColumn("SELECT id FROM tech_timelog ORDER BY id DESC LIMIT 1");
		if ($count > 45000) {
			$this->logMessage('Too many records, skipping');
			return;
		}

		$sub_start_time = microtime(true);
		$this->logMessage("-- Processing batch {$page}");

		if ($page == 1) {
			$this->preRunAll();
		}

		$start = (($page-1) * 1000) + 1;
		$end   = $page * 1000;

		$batch = $this->getOldDb()->fetchAll("
			SELECT * FROM tech_timelog
			WHERE id BETWEEN $start AND $end
		");

		$this->getDb()->beginTransaction();
		try {

			foreach ($batch as $l) {
				$this->processLog($l);
			}

			$this->flushBatch();

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


	public function processLog(array $log)
	{
		$agent_id = $this->getMappedNewId('tech', $log['techid']);
		if (!$agent_id) {
			return;
		}

		$date_start = new \DateTime('@' . $log['startstamp']);
		list($hour, $minute) = explode(':', $date_start->format('H:i'));
		$minute = intval($minute / 5) * 5;
		$date_start->setTime($hour, $minute, 0);

		$date_end = new \DateTime('@' . $log['endstamp']);

		do {
			$this->addBatch($agent_id, $date_start);
			$date_start->add(new \DateInterval('PT5M'));
		} while ($date_start < $date_end);
	}

	public function flushBatch()
	{
		if (!$this->batch_insert) {
			return;
		}

		$sql = "REPLACE INTO agent_activity (agent_id, date_active) VALUES " . implode(', ', $this->batch_insert);
		$this->getDb()->exec($sql);

		$this->batch_insert = array();
	}

	public function addBatch($agent_id, \DateTime $datetime)
	{
		$this->batch_insert[] = "($agent_id, '" . $datetime->format('Y-m-d H:i:s') . "')";

		if (count($this->batch_insert) >= 250) {
			$this->flushBatch();
		}
	}
}
