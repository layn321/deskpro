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

class PrepareStep extends AbstractDeskpro3Step
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	public static function getTitle()
	{
		return 'Prepare';
	}

	public function run($page = 1)
	{
		$this->getDb()->exec("ALTER TABLE content_search DROP INDEX content");

		$this->importer->removeTableIndexes('content_search');
		$this->importer->removeTableIndexes('content_search_attribute');

		// Ticket archiving settings
		$archive = false;
		if ($this->importer->getConfig('archive')) {
			if ($this->importer->getConfig('archive') === 'auto') {
				$count = $this->importer->olddb->count('ticket');
				if ($count > 250000) {
					$archive = true;
				}
			} else {
				$archive = true;
			}
		}

		if ($archive) {
			$this->importer->db->replace('settings', array(
				'name' => 'core_tickets.use_archive',
				'value' => 1,
			));

			$days = $this->importer->getConfig('days_until_archive');
			if (!$days) $days = 90;

			$time = $days * 86400;
			$this->importer->db->replace('settings', array(
				'name' => 'core_tickets.auto_archive_time',
				'value' => $time,
			));
		}
	}
}
