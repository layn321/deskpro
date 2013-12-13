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

use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\TicketAttachment;
use Application\DeskPRO\Entity\TicketParticipant;

class TicketsSavedStep extends AbstractDeskpro3Step
{
	public static function getTitle()
	{
		return 'Import Saved Tickets';
	}

	public function run($page = 1)
	{
		$techs = $this->getOldDb()->fetchAllKeyed("SELECT * FROM tech");
		$folder_infos = $this->getOldDb()->fetchAllKeyValue("SELECT techid, categories FROM tech_folders WHERE type = 'savedtickets'");

		$this->getDb()->beginTransaction();
		try {
			foreach ($techs as $tech) {
				$folder_info = isset($folder_infos[$tech['id']]) ? @unserialize($folder_infos[$tech['id']]) : null;
				if (!$folder_info) {
					$folder_info = array();
				}
				$this->processTech($tech, $folder_info);
			}

			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	public function processTech(array $tech, array $folder_info)
	{
		$agent_id = $this->getMappedNewId('tech', $tech['id']);

		if (!$agent_id) {
			return;
		}

		if ($folder_info) {
			$saved_tickets_cats = $this->getOldDb()->fetchAllKeyValue("
				SELECT category, COUNT(*) AS count
				FROM tech_ticket_save
				WHERE techid = ?
				ORDER BY count DESC
			", array($tech['id']));

			$cat_map = array();

			// Flag colors except for yellow, which we'll use as 'top' or when theres too many
			$flags = array('blue', 'green','orange','pink','purple','red');

			foreach ($saved_tickets_cats as $cat => $count) {

				// Unknown category id for the tech
				if (!isset($folder_info[$cat])) {
					break;
				}

				$color = array_shift($flags);

				// No more colors left to map
				if (!$color) {
					break;
				}

				$cat_map[$cat] = $color;

				$this->getDb()->insert('people_prefs', array(
					'person_id'   => $agent_id,
					'name'        => 'agent.ui.flag.' . $color,
					'value_str'   => $folder_info[$cat],
					'value_array' => null,
				));
			}
		}

		$saved_tickets = $this->getOldDb()->fetchAll("
			SELECT ticketid, category
			FROM tech_ticket_save
			WHERE techid = ?
		", array($tech['id']));

		foreach ($saved_tickets as $st) {
			$new_ticket_id = $this->getMappedNewId('ticket', $st['ticketid']);
			if (!$new_ticket_id) {
				continue;
			}

			$this->getOldDb()->insert('tickets_flagged', array(
				'person_id' => $agent_id,
				'ticket_id' => $new_ticket_id,
				'color'     => isset($cat_map[$st['category']]) ? $cat_map[$st['category']] : 'yellow'
			));
		}
	}
}