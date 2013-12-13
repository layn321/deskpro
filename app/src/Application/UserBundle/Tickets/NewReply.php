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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\TicketAttachment;
use Application\DeskPRO\Entity\Person;

class NewReply
{
	public $message;

	/**
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile
	 */
	public $new_upload = null;

	public $attach_ids = array();
	public $attach_ids_authed = false;

	protected $ticket;
	protected $person;

	protected $ticket_message;

	public function __construct(Ticket $ticket, Person $person)
	{
		$this->ticket = $ticket;
		$this->person = $person;
	}

	public function save()
	{
		$ticket_message = new TicketMessage();
		$ticket_message->setMessageText($this->message);
		$ticket_message->ticket = $this->ticket;
		$ticket_message->person = $this->person;
		$ticket_message->creation_system = TicketMessage::CREATED_WEB_PERSON_PORTAL;
		$ticket_message->ip_address = dp_get_user_ip_address();
		$ticket_message->visitor = App::getSession()->getVisitor();

		$attach = false;
		if ($this->new_upload) {
			$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromFile(
				$this->new_upload->getRealPath(),
				$this->new_upload->getClientOriginalName(),
				$this->new_upload->getClientMimeType()
			);
			$blob_id = $blob->getId();

			$attach = new \Application\DeskPRO\Entity\TicketAttachment();
			$attach['blob'] = $blob;
			$attach['person'] = $this->person;

			$ticket_message->addAttachment($attach);
		}
		// Existing (pre-uploaded temp) attachments
		if ($this->attach_ids) {
			foreach ($this->attach_ids as $blob_id) {
				if ($this->attach_ids_authed) {
					$blob = App::getEntityRepository('DeskPRO:Blob')->getByAuthId($blob_id);
				} else {
					$blob = App::findEntity('DeskPRO:Blob', $blob_id);
				}
				if ($blob) {
					$attach = new \Application\DeskPRO\Entity\TicketAttachment();
					$attach['blob'] = $blob;
					$attach['person'] = $this->person;

					$ticket_message->addAttachment($attach);

					$blob->is_temp = false;
					App::getOrm()->persist($attach);
					App::getOrm()->persist($blob);
				}
			}
		}

		if ($dupe_message = App::getOrm()->getRepository('DeskPRO:TicketMessage')->checkDupeMessage($ticket_message, $this->ticket)) {
			$ticket_message = $dupe_message;
		} else {
			$this->ticket->addMessage($ticket_message);

			if ($dupe_message = App::getEntityRepository('DeskPRO:TicketMessage')->checkDupeMessage($ticket_message, $this->ticket)) {
				$this->ticket_message = $dupe_message;
				return;
			}

			// If status is pending, we'll switch it to open so agents will see it
			if ($this->ticket['status'] == Ticket::STATUS_AWAITING_USER || $this->ticket['status'] == Ticket::STATUS_RESOLVED) {
				$this->ticket['status'] = Ticket::STATUS_AWAITING_AGENT;
			}

			if ($this->person->id && !$this->ticket->hasParticipantPerson($this->person)) {
				// someone like the org manager replying - need to make sure they're CC'd
				$this->ticket->addParticipantPerson($this->person);
			}

			App::getOrm()->beginTransaction();
			App::getOrm()->persist($ticket_message);
			App::getOrm()->persist($this->ticket);
			App::getOrm()->flush();
			App::getOrm()->commit();
		}
	}

	public function getNewMessage()
	{
		return $this->ticket_message;
	}
}
