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

class TicketSlaUpdated extends AbstractLogAction
{
	protected $ticket_sla;

	public function __construct(Entity\TicketSla $ticket_sla)
	{
		$this->ticket_sla = $ticket_sla;
	}

	public function getLogName()
	{
		return 'ticket_sla_updated';
	}

	public function getLogDetails()
	{
		$output = array();

		if ($this->ticket_sla->sla_status != $this->ticket_sla->getOriginalStatus()) {
			$output['sla_status'] = $this->ticket_sla->sla_status;
			$output['original_status'] = $this->ticket_sla->getOriginalStatus();
			$output['status_changed'] = true;
		}

		if ($this->ticket_sla->is_completed != $this->ticket_sla->getOriginalIsCompleted()) {
			$output['is_completed'] = $this->ticket_sla->is_completed;
			$output['original_is_completed'] = $this->ticket_sla->getOriginalIsCompleted();
			$output['is_completed_changed'] = true;
		}

		if (!$output) {
			return array();
		}

		$output['title'] = $this->ticket_sla->sla->title;

		return $output;
	}

	public function getEventType()
	{
		return 'property';
	}
}
