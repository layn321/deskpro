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
 * @subpackage Tickets
 */

namespace Application\DeskPRO\Tickets\TicketChangeInspector;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketFilter;
use Application\DeskPRO\Entity\ClientMessage;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Tickets\TicketChangeInspector\DetectFilterMatches;

use Orb\Log\Logger;

class ListUpdater
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeInspector\DetectFilterMatches
	 */
	protected $filter_detector;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(TicketChangeTracker $tracker, DetectFilterMatches $filter_detector)
	{
		$this->tracker = $tracker;
		$this->filter_detector = $filter_detector;
		$this->em = App::getOrm();
	}

	/**
	 * Runs the checks, inserts the client messages if there are any.
	 */
	public function run()
	{
		$filter_changes = $this->filter_detector->getFilterMatches();
		$ticket_id = $this->tracker->getTicket()->id;

		$online_agents = $this->em->getRepository('DeskPRO:Person')->getActiveAgents(true);

		$count_adds = 0;
		$count_dels = 0;

		$this->tracker->logMessage('[ListUpdater] run');
		$time = microtime(true);

		$this->em->beginTransaction();
		try {
			foreach ($filter_changes as $change_info) {
				$filter = $change_info['filter'];

				foreach ($change_info['add'] as $agent) {
					if (!isset($online_agents[$agent->id])) continue;

					$count_adds++;
					$this->em->getConnection()->insert('client_messages', array(
						'channel' => 'agent.filter-update',
						'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
						'date_created' => date('Y-m-d H:i:s'),
						'data' => serialize(array(
							'ticket_id'  => $ticket_id,
							'filter_id'  => $filter['id'],
							'op' => 'add'
						)),
						'for_person_id' => $agent->getId(),
						'created_by_client' => 'sys',
						'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
					));
				}
				foreach ($change_info['del'] as $agent) {
					if (!isset($online_agents[$agent->id])) continue;

					$count_dels++;

					$this->em->getConnection()->insert('client_messages', array(
						'channel' => 'agent.filter-update',
						'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
						'date_created' => date('Y-m-d H:i:s'),
						'data' => serialize(array(
							'ticket_id'  => $ticket_id,
							'filter_id'  => $filter['id'],
							'op' => 'del'
						)),
						'for_person_id' => $agent->getId(),
						'created_by_client' => 'sys',
						'handler_class' => 'Application\\DeskPRO\\ClientMessage\\MessageHandler\\BasicArray'
					));
				}
			}

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$this->tracker->logMessage(sprintf("[ListUpdater] Done with $count_adds adds and $count_dels dels messages sent in %.4f seconds",microtime(true)-$time));
	}
}
