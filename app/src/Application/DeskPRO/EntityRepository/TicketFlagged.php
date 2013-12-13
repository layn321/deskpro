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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use \Doctrine\ORM\EntityRepository;

use Orb\Util\Arrays;

class TicketFlagged extends AbstractEntityRepository
{
	public function getFlagForTicket($ticket, Entity\Person $person)
	{
		$color = App::getDb()->fetchColumn("
			SELECT color
			FROM tickets_flagged
			WHERE ticket_id = ? AND person_id = ?
		", array($ticket->id, $person->id));

		return $color;
	}

	public function getFlagsForTickets($tickets, Entity\Person $person)
	{
		$ids = Arrays::flattenToIndex($tickets, 'id');

		if (!$ids) return array();

		return App::getDb()->fetchAllKeyValue("
			SELECT ticket_id, color
			FROM tickets_flagged
			WHERE ticket_id IN(" . implode(',', $ids) . ") AND person_id = ?
		", array($person['id']));
	}

	public function getCountsForPerson(Entity\Person $person)
	{
		return App::getDb()->fetchAllKeyValue("
			SELECT color, COUNT(*)
			FROM tickets_flagged
			WHERE person_id = ?
			GROUP BY color
		", array($person['id']));
	}
}
