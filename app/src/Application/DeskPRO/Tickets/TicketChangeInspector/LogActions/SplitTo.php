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

namespace Application\DeskPRO\Tickets\TicketChangeInspector\LogActions;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

class SplitTo extends AbstractLogAction
{
	protected $ticket;
	protected $old_ticket;

	public function __construct($ticket, $old_ticket)
	{
		$this->ticket = $ticket;
		$this->old_ticket = $old_ticket;
	}

	public function getLogName()
	{
		return 'ticket_split_to';
	}

	public function getLogDetails()
	{
		return array(
			'id_before' => $this->old_ticket['id'] ?: null,
			'id_after'  => $this->ticket['id'] ?: null,

			'to_ticket_id' => $this->ticket['id'],
			'messages_moved' => count($this->ticket->messages)
		);
	}

	public function getEventType()
	{
		return 'ticket_split_to';
	}
}
