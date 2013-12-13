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

use Application\DeskPRO\Entity\TicketWorkflow;

class TicketWorkflowsStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Ticket Workflows';
	}

	public function run($page = 1)
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM ticket_workflow");
		$this->logMessage(sprintf("Importing %d ticket workflows", $count));
		if (!$count) {
			return;
		}

		$workflows = $this->getOldDb()->fetchAll("SELECT * FROM ticket_workflow ORDER BY id ASC");

		$start_time = microtime(true);

		$this->getDb()->beginTransaction();

		try {
			foreach ($workflows as $work) {
				$this->processWorkflow($work);
			}

			if ($workflows) {
				$this->getDb()->replace('settings', array(
					'name' => 'core.use_ticket_workflow',
					'value' => 1,
				));
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("Done all workflows. Took %.3f seconds.", $end_time-$start_time));
	}

	public function processWorkflow(array $work)
	{
		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('ticket_workflow', $work['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$work['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$new_work = new TicketWorkflow();
		$new_work->title = $work['name'];
		$new_work->display_order = $work['displayorder'];

		$this->getEm()->persist($new_work);
		$this->getEm()->flush();

		$this->saveMappedId('ticket_workflow', $work['id'], $new_work->id);
	}
}
