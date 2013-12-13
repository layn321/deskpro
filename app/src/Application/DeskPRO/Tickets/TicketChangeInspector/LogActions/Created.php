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

class Created extends AbstractLogAction
{
	protected $ticket;
	protected $via_comment;

	public function __construct($ticket)
	{
		$this->ticket = $ticket;
	}

	public function setViaComment(array $via_comment)
	{
		$this->via_comment = $via_comment;
	}

	public function getLogName()
	{
		return 'ticket_created';
	}

	public function getLogDetails()
	{
		if ($this->via_comment) {
			return array(
				'ticket_id'   => $this->ticket['id'],
				'via_comment' => $this->via_comment,
			);
		} else {
			return array(
				'ticket_id' => $this->ticket['id']
			);
		}
	}

	public function getEventType()
	{
		return 'ticket_created';
	}
}
