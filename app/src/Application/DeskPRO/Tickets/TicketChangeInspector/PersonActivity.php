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
use Application\DeskPRO\Entity;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\People\ActivityLogger\ActionType\NewTicket as NewTicketAction;
use Application\DeskPRO\People\ActivityLogger\ActionType\NewTicketReply as NewTicketReplyAction;

class PersonActivity
{
	/**
	 * @var TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket;

	public function __construct(TicketChangeTracker $tracker)
	{
		$this->tracker = $tracker;
		$this->ticket = $tracker->getTicket();
	}

	public function run()
	{
		if ($this->tracker->isExtraSet('ticket_created')) {
			$action = new NewTicketAction($this->ticket->person, $this->ticket);
			App::getPersonActivityLogger()->saveAction($action);
		} elseif ($this->tracker->isPropertyChanged('messages')) {
			$message_info = $this->tracker->getChangedProperty('messages');
			$message_info = array_pop($message_info); //array wrapper, possible for multi

			// New val means a new ticket (ie not delete)
			if ($message_info['new']) {
				$message = $message_info['new'];
				$action = new NewTicketReplyAction($message->person, $message);
				App::getPersonActivityLogger()->saveAction($action);
			}
		}
	}
}
