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

use Application\DeskPRO\Entity\Person;

class PreRunStep extends AbstractZendeskStep
{
	public static function getTitle()
	{
		return 'PreRun';
	}

	public function run($page = 1)
	{
		if ($this->importer->run_mode == 'rerun') {
			$this->logMessage("-- Initial run");
			$this->db->delete('import_datastore', array('typename' => 'zd_tickets_cache_time'));
			$this->db->delete('import_datastore', array('typename' => 'zd_tickets_rerun_time'));

			// Ticket archiving settings
			$archive = false;
			if ($this->importer->getConfig('archive')) {
				if ($this->importer->getConfig('archive') === 'auto') {
					$res = $this->zd->sendGet('tickets', array('per_page' => 1));
					$count = (int)$res->get('count');

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
		} else {
			$this->logMessage("-- Is ReRun");

			// Clear out attach queue that was processed at the end of the last run
			$this->db->executeUpdate("
				DELETE FROM import_datastore
				WHERE typename LIKE 'attach.person_picture.%'
			");

			$this->db->executeUpdate("
				DELETE FROM import_datastore
				WHERE typename LIKE 'attach.ticket.%'
			");
		}
	}
}