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
 * @category Tickets
 */

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\People\PersonContextInterface;

use Orb\Util\Arrays;

/**
 * Splits a ticket from one message and on into a new ticket
 */
class TicketSplit
{

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket;

	protected $old_ticket_deleted = false;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(Ticket $ticket)
	{
		$this->em = App::getOrm();

		$this->ticket = $ticket;
	}

	public function wasOldTicketDeleted()
	{
		return $this->old_ticket_deleted;
	}


	public function split($subject, array $message_ids)
	{
		$ticket = $this->ticket;

		if (!$message_ids) {
			return;
		}

		$messages = $this->em->createQuery("
			SELECT m
			FROM DeskPRO:TicketMessage m
			WHERE m.id IN (?1) AND m.ticket = ?2
		")->execute(array(1=> $message_ids, 2=> $ticket['id']));
		if (!count($messages)) {
			return;
		}

		$new_ticket = $ticket->copy();
		$first = null;
		foreach ($messages as $m) {
			$ticket->messages->removeElement($m);
			$new_ticket->addMessage($m);

			if (!$first) {
				$first = $m;
			}

			foreach ($m->attachments as $attach) {
				$attach->ticket = $new_ticket;
			}
		}

		if ($first) {
			$new_ticket->date_created = $first->date_created;
		}

		$new_ticket->creation_system = Ticket::CREATED_WEB_AGENT;

		if ($subject) {
			$new_ticket->subject = $subject;
		}

		$has_owner = false;
		foreach ($new_ticket->messages AS $message) {
			if ($message->person->id == $new_ticket->person->id) {
				$has_owner = true;
				break;
			}
		}

		if (count($messages) == 1 || !$has_owner) {
			$message = reset($messages);
			$new_ticket->person = $message->person;
			$new_ticket->person_email = $message->person->primary_email;
			$new_ticket->organization = $message->person->organization;
		}

		$new_ticket->getTicketLogger()->recordExtra('suppress_user_notify', true);
		$new_ticket->getTicketLogger()->recordExtra('suppress_agent_notify', true);
		$new_ticket->getTicketLogger()->recordExtra('ticket_split', array('old_ticket' => $ticket)); // just the split

		if (count($ticket->messages) == 0) {
			// Old ticket set to deleted so proper CM's are sent
			$ticket->getTicketLogger()->recordExtra('bare_delete', 1);
			$ticket->setStatus('hidden.deleted');
			$this->em->persist($ticket);
			$delete_ticket = true;
		} else {
			$this->em->persist($ticket);
			$delete_ticket = false;
		}

		$this->em->persist($new_ticket);
		$this->em->flush();

		if ($delete_ticket) {
			$ticket->_markRemoved();
			$ticket->setNoLog();
			$ticket->unsetTicketLogger();

			$this->em->remove($ticket);
		} else {
			// need to do this as we need the new ID
			$person = App::getCurrentPerson();
			$action = new \Application\DeskPRO\Tickets\TicketChangeInspector\LogActions\SplitTo($new_ticket, $ticket);

			$ticket_log = new \Application\DeskPRO\Entity\TicketLog();
			$ticket_log['person'] = ($person && $person->id) ? $person : null;
			$ticket_log['ticket'] = $ticket;
			$ticket_log['action_type'] = $action->getLogName();
			$ticket_log['details'] = $action->getLogDetails();

			$this->em->persist($ticket_log);
		}

		$this->em->flush();

		$this->old_ticket_deleted = $delete_ticket;

		return $new_ticket;
	}
}
