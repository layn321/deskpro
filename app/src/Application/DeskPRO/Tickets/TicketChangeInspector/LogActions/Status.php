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
use Application\DeskPRO\Entity\Ticket;

class Status extends AbstractLogAction
{
	protected $old_status;
	protected $new_status;

	public function __construct($old_status, $new_status)
	{
		if ($old_status == 'hidden.') {
			$old_status = null;
		}

		$this->old_status = $old_status;
		$this->new_status = $new_status;
	}

	public function getLogName()
	{
		return 'changed_status';
	}

	public function getLogDetails()
	{
		if ($this->old_status == $this->new_status) {
			return array();
		}

		return array(
			'id_before' => Ticket::getStatusInt($this->old_status) ?: null,
			'id_after'  => Ticket::getStatusInt($this->new_status) ?: null,

			'old_status' => $this->old_status,
			'new_status' => $this->new_status,
		);
	}

	public function getEventType()
	{
		return 'property';
	}

	public function setNewStatus($new_status)
	{
		$this->new_status = $new_status;
	}

	public function getNewStatus()
	{
		return $this->new_status;
	}

	public function setOldStatus($old_status)
	{
		$this->old_status = $old_status;
	}

	public function getOldStatus()
	{
		return $this->old_status;
	}
}
