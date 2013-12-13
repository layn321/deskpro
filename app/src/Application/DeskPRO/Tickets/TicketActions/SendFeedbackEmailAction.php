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

class SendFeedbackEmailAction extends AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	public function __construct(TicketChangeTracker $tracker = null)
	{
		$this->tracker = $tracker;
	}

	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$ticket_message = App::getOrm()->createQuery("
			SELECT m
			FROM DeskPRO:TicketMessage m
			LEFT JOIN m.person person
			WHERE m.is_agent_note = false AND person.is_agent = true AND m.ticket = ?0
			ORDER BY m.id DESC
		")->setParameter(0, $ticket)->setMaxResults(1)->getOneOrNullResult();

		if (!$ticket_message) {
			return;
		}

		$change_info = array(
			'type' => 'user_notify',
			'notify_type' => 'feedback',
			'emailed' => array(),
			'cced' => array()
		);

		$person = $ticket->person;

		$change_info['emailed'] = array($person);

		$vars['ticket'] = $ticket;
		$vars['person'] = $person;
		$vars['message'] = $ticket_message;

		$from = $ticket->getFromAddress();
		$from_email = !empty($from['email']) ? $from['email'] : '';
		$from_name  = !empty($from['email']) ? $from['email'] : '';
		$from_address = array($from_email => $from_name);

		App::getTranslator()->setTemporaryLanguage($ticket->getLanguage(), function($tr, $lang) use ($vars, $ticket, $person, $from_address) {
			$message = App::getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_user:ticket-rate.html.twig', $vars);
			$message->setToPerson($person);
			$message->setContextId('ticket_gateway');
			$message->getHeaders()->get('Message-ID')->setId($ticket->getUniqueEmailMessageId());
			$message->setFrom($from_address);

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
			array('action' => 'send_feedback_email')
		);
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		return 'Send user an email asking for feedback';
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
