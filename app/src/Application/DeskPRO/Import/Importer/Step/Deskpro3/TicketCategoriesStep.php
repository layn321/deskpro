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

use Application\DeskPRO\Entity\Department;

class TicketCategoriesStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Ticket Categories';
	}

	public function run($page = 1)
	{
		// If there arent any tickets besides the default one, then remove the default data
		$default_check = $this->getDb()->fetchColumn("SELECT COUNT(*) FROM tickets ORDER BY id DESC LIMIT 2");
		if (!$default_check || $default_check == 1) {
			$this->getDb()->exec("DELETE FROM tickets");
			$this->getDb()->exec("DELETE FROM departments");
		}

		$count = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM ticket_cat");
		$this->logMessage(sprintf("Importing %d ticket categories", $count));
		if (!$count) {
			return;
		}

		$categories_top = $this->getOldDb()->fetchAll("SELECT * FROM ticket_cat WHERE parent = 0 ORDER BY id ASC");
		$categories_sub = $this->getOldDb()->fetchAll("SELECT * FROM ticket_cat WHERE parent != 0 ORDER BY id ASC");

		$start_time = microtime(true);

		$this->getDb()->beginTransaction();

		try {
			// First do the parents ....
			foreach ($categories_top as $cat) {
				$this->processCategory($cat);
			}

			// Then the children after. Easiest way to make sure a parent exists before the child
			// is to just do them separately like this.
			foreach ($categories_sub as $cat) {
				$this->processCategory($cat);
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("Done all categories. Took %.3f seconds.", $end_time-$start_time));
	}

	public function processCategory(array $cat)
	{
		#------------------------------
		# Make sure we havent already done them
		#------------------------------

		$check_exist = $this->getMappedNewId('ticket_category', $cat['id']);
		if ($check_exist) {
			$this->getLogger()->log("{$cat['id']} already mapped, skipping", 'DEBUG');
			return;
		}

		#------------------------------
		# Create it
		#------------------------------

		$dep = new Department();
		$dep->title = $cat['name'];
		$dep->display_order = $cat['displayorder'];
		$dep->is_tickets_enabled = true;
		$dep->is_chat_enabled = false;

		if ($cat['parent']) {
			$parent = $this->getEm()->find('DeskPRO:Department', $this->getMappedNewId('ticket_category', $cat['parent']));
			if ($parent) {
				$dep->parent = $parent;
			}
		}

		$this->getEm()->persist($dep);
		$this->getEm()->flush();

		$this->saveMappedId('ticket_category', $cat['id'], $dep->id);
	}
}
