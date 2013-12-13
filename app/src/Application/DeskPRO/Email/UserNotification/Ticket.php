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

namespace Application\DeskPRO\Email\UserNotification;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Used to send email notifications
 */
class Ticket
{
	protected $ticket;
	protected $actions;

	public function __construct(Entity\Ticket $ticket, array $actions = array())
	{
		$this->ticket = $ticket;
		$this->actions = $actions;
	}

	public function sendNotifications(array $notify_types)
	{
		$notify_types = Entity\AgentNotification::reduceNotificationTypes($notify_types);
		$person = $this->ticket['person'];

		App::getTranslator()->setLanguage($person->getLanguage());

		foreach ($notify_types as $type) {
			switch ($type) {
				case Entity\AgentNotification::NOTIFY_NEW_TICKET:
					$this->sendNewTicket($person);
					break;

				case Entity\AgentNotification::NOTIFY_NEW_REPLY:
					$this->sendNewReply($person);
					break;

				case Entity\AgentNotification::NOTIFY_NEW_AGENT_REPLY:
					$this->sendNewReply($person);
					break;
			}
		}

		// Reset translator to current context
		App::getTranslator()->setLanguage(null);
	}

	public function sendNewTicket(Entity\Person $person)
	{
		$tac = $this->ticket->findAccessCodeForPerson($person);

		$new_message = App::getEntityRepository('DeskPRO:TicketMessage')->getFirstTicketMessage($this->ticket);
		$vars = array(
			'ticket' => $this->ticket,
			'new_message' => $new_message,
			'subject' => $email_subject,
			'person' => $person,
			'access_code' => $tac['code'],
			'access_code_full' => $this->ticket['ref'] . '-' . $tac['code']
		);

		$message = App::getMailer()->createMessage();
		$message->setTemplate('DeskPRO:emails_user:new-ticket.html.twig', $vars);

		$message->setTo($person->getPrimaryEmailAddress(), $person->getDisplayName());
		$message->getHeaders()->addIdHeader('ticket-' . $this->ticket['ref'] . '-' . Strings::random(10, Strings::CHARS_ALPHA_IU) . '@deskpro');
		$message->getHeaders()->addTextHeader('In-Reply-To', 'ticket-' . $this->ticket['ref'] . '@deskpro');
		$message->enableQueueHint();

		App::getMailer()->send($message);

		// Send another if the ticket is hidden and needs validation
		if ($this->ticket['hidden_status'] == Entity\Ticket::HIDDEN_STATUS_VALIDATING) {
			$vars = array(
				'ticket' => $this->ticket,
				'subject' => $email_subject,
				'person' => $person
			);

			$message = App::getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_user:new-ticket-validate.html.twig', $vars);
			$message->setTo($person->getPrimaryEmailAddress(), $person->getDisplayName());
			$message->enableQueueHint();

			App::getMailer()->send($message);
		}
	}

	public function sendNewReply(Entity\Person $person)
	{
		$tac = $this->ticket->findAccessCodeForPerson($person);

		$new_message = $this->actions['message_created']->getMessage();
		$vars = array(
			'ticket' => $this->ticket,
			'new_message' => $new_message,
			'subject' => $email_subject,
			'person' => $person,
			'access_code' => $tac['code'],
			'access_code_full' => $this->ticket['ref'] . '-' . $tac['code']
		);

		$message = App::getMailer()->createMessage();
		$message->setTemplate('DeskPRO:emails_agent:new-reply.html.twig', $vars);
		$message->setTo($person->getPrimaryEmailAddress(), $person->getDisplayName());
		$message->getHeaders()->addIdHeader('ticket-' . $this->ticket['ref'] . '-' . Strings::random(10, Strings::CHARS_ALPHA_IU) . '@deskpro');
		$message->getHeaders()->addTextHeader('In-Reply-To', 'ticket-' . $this->ticket['ref'] . '@deskpro');
		$message->enableQueueHint();

		App::getMailer()->send($message);
	}
}
