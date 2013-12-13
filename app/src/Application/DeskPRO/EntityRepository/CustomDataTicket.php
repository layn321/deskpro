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

use \Doctrine\ORM\EntityRepository;

class CustomDataTicket extends AbstractEntityRepository
{
	public function getDataForTicket(Entity\Ticket $ticket)
	{
		return $this->_em->createQuery("
			SELECT d
			FROM DeskPRO:CustomDataTicket d INDEX BY d.field_id
			WHERE d.ticket = ?1
		")->setParameter(1, $ticket)->execute();
	}


	/**
	 * Fetch data for a whole bunch of things.
	 *
	 * @param array $tickets
	 * @return array
	 */
	public function getDataCollectionForTicketCollection(array $tickets)
	{
		$ids = array();
		foreach ($tickets as $t) {
			$ids[] = $t->id;
		}

		if (!$ids) {
			return arary();
		}

		$raw = $this->_em->createQuery("
			SELECT d
			FROM DeskPRO:CustomDataTicket d
			LEFT JOIN d.ticket t
			WHERE d.ticket.id IN (" . implode(',', $ids) . ")
		")->execute();

		if (!$raw) {
			return array();
		}

		// Note that this is still pretty inefficient. Doctrine doesnt know about the
		// 'ticket_id' field in the table, only the ticket object. So we had to join
		// on the ticket table to fetch it, just so we can use the id to index the array below

		// Reindex by ticket id
		$data = array();
		foreach ($raw as $r) {
			if (!isset($data[$r->ticket->id])) {
				$data[$r->ticket->id] = array();
			}

			$data[$r->ticket->id][] = $r;
		}
		return $data;
	}
}
