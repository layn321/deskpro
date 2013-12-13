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

namespace Application\DeskPRO\EmailGateway\Ticket;

use Application\DeskPRO\EmailGateway\Reader\AbstractReader;
use Application\DeskPRO\Entity\Ticket;

/**
 * A ticket detector scans an email to try and detect if an email
 * is in reply to an existing ticket.
 */
interface TicketDetectorInterface
{
	/**
	 * Should return a ticket if one was found. If no ticket is found, return null.
	 *
	 * @param \Application\DeskPRO\EmailGateway\Parser\AbstractReader $reader
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public function findExistingTicket(AbstractReader $reader);

	/**
	 * If there was a ticket found and the email is a reply, then
	 * this shuold discover who is making the reply.
	 *
	 * This is important because we might not know the From address, but if
	 * the detector has some other way of knowing who the person is,
	 * then we can still associate accounts properly.
	 * (ex multiple participants might each get a different code etc)
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @param \Application\DeskPRO\EmailGateway\Parser\AbstractReader $reader
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function findExistingPerson(Ticket $ticket, AbstractReader $reader);

	/**
	 * If a ticket is found but a person isn't, should we add the new email address
	 * as a new CC or should we deny the message?
	 *
	 * @return void
	 */
	public function canAddUnknownPerson();
}
