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
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\App;

class SendAutocloseWarnEmailAction extends AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var string
	 */
	protected $tpl;

	public function __construct($template_name = '', TicketChangeTracker $tracker = null)
	{
		$this->tpl = $template_name;
		$this->tracker = $tracker;
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
		$tpl = $this->tpl;
		if (!$tpl || !App::getDataService('Template')->customEmailExists($tpl)) {
			$tpl = 'DeskPRO:emails_user:ticket-autoclose-warn.html.twig';
		}

		$change_info = array(
			'type' => 'user_notify',
			'notify_type' => 'autoclose_warn',
			'emailed' => array(),
			'cced' => array()
		);

		$person = $ticket->person;
		$parts  = $ticket->getUserParticipants();

		$change_info['emailed'] = array($person);

		$vars['ticket'] = $ticket;
		$vars['person'] = $person;

		$ticketdisplay = new \Application\DeskPRO\Tickets\TicketDisplay($ticket, $person);
		$vars['ticketdisplay'] = $ticketdisplay;
		$vars['messages']      = array_reverse($ticketdisplay->getMessages(), true);

		$from_address = $this->getFromAddress($ticket);

		App::getTranslator()->setTemporaryLanguage($ticket->getLanguage(), function($tr, $lang) use ($vars, $from_address, $ticket, $person, $parts, $tpl) {
			$message = App::getMailer()->createMessage();
			$message->setContextId('ticket_gateway');
			$message->setTemplate($tpl, $vars);
			$message->setToPerson($person);

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
			array('action' => 'send_autoclose_warn_email')
		);
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		return 'Send user an email warning their ticket will be closed automatically';
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
