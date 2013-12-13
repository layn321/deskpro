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
 * @category People
 */

namespace Application\DeskPRO\People\ActivityLogger\ActionType;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\PersonActivity;
use Application\DeskPRO\People\PersonContextInterface;

use Orb\Util\Arrays;

class NewTicketReply extends ActionTypeAbstract
{
	protected $ticket_message;


	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param \Application\DeskPRO\Entity\TicketMessage $ticket_message
	 */
	public function __construct(Person $person, TicketMessage $ticket_message)
	{
		$this->person = $person;
		$this->ticket_message = $ticket_message;
	}


	/**
	 * Get a plain array of details that'll be stored in the databaes
	 * @return array
	 */
	public function getDetails()
	{
		return array(
			'ticket_id'  => $this->ticket_message->ticket['id'],
			'message_id' => $this->ticket_message['id'],
			'subject' => $this->ticket_message->ticket['subject'],
			'message'    => $this->ticket_message->getMessageText()
		);
	}
}
