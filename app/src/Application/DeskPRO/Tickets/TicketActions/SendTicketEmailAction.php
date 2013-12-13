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

namespace Application\DeskPRO\Tickets\TicketActions;

use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Translate\DelegatePhrase;
use Application\DeskPRO\App;

class SendTicketEmailAction extends AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var string
	 */
	protected $message;

	public function __construct($message, TicketChangeTracker $tracker = null)
	{
		$this->tracker = $tracker;
		$this->message = $message;
	}

	public function getFromAddress(Ticket $ticket)
	{
		$info = $ticket->getFromAddress('user');
		return array($info['email'] => $info['name']);
	}

	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$change_info = array(
			'type' => 'ticket_email_message',
			'message' => $this->message,
			'emailed' => array(),
			'cced' => array()
		);

		$person = $ticket->person;
		$parts  = $ticket->getUserParticipants();

		$change_info['emailed'] = array($person);
		$change_info['cced'] = $parts;

		$vars['ticket'] = $ticket;
		$vars['person'] = $person;
		$vars['participants'] = $parts;
		$vars['participants'] = $parts;
		$vars['access_code'] = $ticket->getAccessCode();
		$vars['message'] = $this->message;

		$from_address = $this->getFromAddress($ticket);

		$ticketdisplay = new \Application\DeskPRO\Tickets\TicketDisplay($ticket, $person);
		$vars['ticketdisplay'] = $ticketdisplay;
		$vars['messages']      = array_reverse($ticketdisplay->getMessages(), true);

		App::getTranslator()->setTemporaryLanguage($ticket->getLanguage(), function($tr, $lang) use ($vars, $from_address, $ticket, $person, $parts) {

			$email = $person->getPrimaryEmailAddress();
			if(!$email && $ticket->person_email_validating) {
				$email = $ticket->person_email_validating;
			}

			if (!$email) {
				return;
			}

			$message = App::getMailer()->createMessage();
			$message->setContextId('ticket_gateway');
			$message->setTemplate('DeskPRO:emails_user:ticket-email-blank.html.twig', $vars);

			$message->setTo($email, $person->getDisplayName());

			foreach ($parts as $part) {
				if ($part['email_address']) {
					$message->addCc($part['email_address'], $part->person->getDisplayName());
				}
			}
			$message->setFrom($from_address);
			$message->getHeaders()->get('Message-ID')->setId($ticket->getUniqueEmailMessageId());

			App::getMailer()->send($message);
		});

		if ($this->tracker) {
			$this->tracker->recordMultiPropertyChanged('log_actions', null, $change_info);
		}
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		return array(
			array('action' => 'send_ticket_email', 'message' => $this->message)
		);
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		return 'Send an email message to the user';
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		return $other_action;
	}
}
