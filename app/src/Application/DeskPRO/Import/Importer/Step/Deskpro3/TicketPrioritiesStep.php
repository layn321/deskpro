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

use Application\DeskPRO\Entity\TicketPriority;

class TicketPrioritiesStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Ticket Priorities';
	}

	public function run($page = 1)
	{
		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM ticket_pri");
		$this->logMessage(sprintf("Importing %d ticket priorities", $count));
		if (!$count) {
			return;
		}

		$priorities = $this->getOldDb()->fetchAll("SELECT * FROM ticket_pri ORDER BY id ASC");

		$start_time = microtime(true);

		$this->getDb()->beginTransaction();

		try {
			foreach ($priorities as $pri) {
				$this->processPriority($pri);
			}

			if ($priorities) {
				$this->getDb()->replace('settings', array(
					'name' => 'core.use_ticket_priority',
					'value' => 1,
				));
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("Done all priorities. Took %.3f seconds.", $end_time-$start_time));
	}

	public function processPriority(array $pri)
	{
		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('ticket_priority', $pri['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$pri['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$new_pri = new TicketPriority();
		$new_pri->title = $pri['name'];
		$new_pri->priority = $pri['displayorder'];
		$new_pri->display_order = $pri['displayorder'];

		$this->getEm()->persist($new_pri);
		$this->getEm()->flush();

		$this->saveMappedId('ticket_priority', $pri['id'], $new_pri->id);
	}
}
