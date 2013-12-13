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
*/

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\People\PersonContextInterface;

class TicketDisplay implements PersonContextInterface
{
	protected $ticket;

	protected $person_context;
	protected $person_type = 'user';

	protected $user_participants;
	protected $agent_participants;

	protected $notes;
	protected $messages;
	protected $attachments;
	protected $message_to_attach;

	protected $user_ratings;

	public function __construct(Ticket $ticket, Person $person)
	{
		$this->ticket = $ticket;
		$this->setPersonContext($person);
	}

	public function setPersonContext(Person $person, $set_type = null)
	{
		$this->person_context = $person;
		if ($person['is_agent']) {
			$this->person_type = 'agent';
		}

		if ($set_type) {
			$this->person_type = $set_type;
		}
	}

	public function getUserParticipants()
	{
		if ($this->user_participants !== null) return $this->user_participants;

		$this->user_participants = array();

		foreach ($this->ticket->getParticipants() as $part) {
			if (!$part->person['is_agent']) {
				$this->user_participants[] = $part;
			}
		}

		return $this->user_participants;
	}

	public function getAgentParticipants()
	{
		if ($this->agent_participants !== null) return $this->agent_participants;

		$this->agent_participants = array();

		foreach ($this->ticket->getParticipants() as $part) {
			if ($part->person['is_agent']) {
				$this->agent_participants[] = $part;
			}
		}

		return $this->agent_participants;
	}

	public function getNotes()
	{
		if ($this->notes !== null) return $this->notes;

		$this->getMessages();

		$this->notes = array();

		foreach ($this->messages as $message) {
			if ($message['is_agent_note']) {
				$this->notes[] = $message;
			}
		}

		return $this->notes;
	}

	public function getMessages()
	{
		if ($this->messages !== null) return $this->messages;

		if ($this->person_type == 'agent') {
			$this->messages = App::getEntityRepository('DeskPRO:TicketMessage')->getTicketMessages(
				$this->ticket,
				array('with_notes' => true)
			);
		} else {
			$this->messages = App::getEntityRepository('DeskPRO:TicketMessage')->getTicketMessages(
				$this->ticket,
				array('with_notes' => false)
			);
		}

		return $this->messages;
	}

	public function getAttachments()
	{
		if ($this->attachments !== null) return $this->attachments;

		$this->attachments = App::getEntityRepository('DeskPRO:TicketAttachment')->getTicketAttachments($this->ticket);

		return $this->attachments;
	}

	public function getMessagesToAttachments($include_inline = false)
	{
		if ($this->message_to_attach !== null) return $this->message_to_attach;

		$this->getMessages();
		$this->getAttachments();

		$this->message_to_attach = array();

		foreach ($this->attachments as $attach) {

			if (!$include_inline && $attach->is_inline) {
				continue;
			}

			if (!isset($this->message_to_attach[$attach['message']['id']])) {
				$this->message_to_attach[$attach['message']['id']] = array();
			}

			$this->message_to_attach[$attach['message']['id']][] = $attach['id'];
		}

		return $this->message_to_attach;
	}

	public function getMessageAttachments($message, $include_inline = false)
	{
		$id = $message->getId();
		$messagetoattach = $this->getMessagesToAttachments($include_inline);

		if (!isset($messagetoattach[$id])) {
			return null;
		}

		$ret = array();
		foreach ($messagetoattach[$id] as $aid) {
			$ret[$aid] = $this->attachments[$aid];
		}

		return $ret;
	}

	public function getFeedbackRatings()
	{
		if ($this->user_ratings !== null) return $this->user_ratings;

		$this->user_ratings = App::getDb()->fetchAllKeyValue("
			SELECT message_id, rating
			FROM ticket_feedback
			WHERE ticket_id = ? AND person_id = ?
		", array($this->ticket->getId(), $this->person_context->getId()));

		return $this->user_ratings;
	}

	public function getDisplayArray()
	{
		$last_user_message = 0;
		$last_agent_message = 0;

		foreach ($this->getMessages() as $message) {
			if ($message->person && $message->person->is_agent) {
				$last_agent_message = $message->id;
			} else {
				$last_user_message = $message->id;
			}
		}

		#------------------------------
		# Custom fields
		#------------------------------

		$field_manager = App::getSystemService('ticket_fields_manager');
		$custom_fields = $field_manager->getDisplayArrayForObject($this->ticket);

		return array(
			'ticket' => $this->ticket,

			'user_participants'  => $this->getUserParticipants(),
			'agent_participants' => $this->getAgentParticipants(),

			'notes' => $this->getNotes(),
			'messages' => $this->getMessages(),
			'attachments' => $this->getAttachments(),
			'message_to_attach' => $this->getMessagesToAttachments(),

			'last_user_message_id' => $last_user_message,
			'last_agent_message_id' => $last_agent_message,

			'user_ratings' => $this->getFeedbackRatings(),

			'custom_fields' => $custom_fields,
		);
	}
}
