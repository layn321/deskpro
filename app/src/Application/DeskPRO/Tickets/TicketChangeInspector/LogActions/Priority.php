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

class Priority extends AbstractLogAction
{
	protected $old_pri;
	protected $new_pri;

	public function __construct($old_pri, $new_pri)
	{
		$this->old_pri = $old_pri;
		$this->new_pri = $new_pri;
	}

	public function getLogName()
	{
		return 'changed_priority';
	}

	public function getLogDetails()
	{
		if ($this->old_pri and $this->new_pri) {
			if ($this->old_pri->getId() == $this->new_pri->getId()) {
				return array();
			}
		} else if (!$this->old_pri and !$this->new_pri) {
			return array();
		}

		return array(
			'id_before' => $this->old_pri['id'] ?: null,
			'id_after'  => $this->new_pri['id'] ?: null,

			'old_priority_id'    => $this->old_pri ? $this->old_pri['id'] : 0,
			'old_priority_title' => $this->old_pri ? $this->old_pri['title'] : '',
			'old_priority_pri'   => $this->old_pri ? $this->old_pri['priority'] : 0,
			'new_priority_id'    => $this->new_pri ? $this->new_pri['id'] : 0,
			'new_priority_title' => $this->new_pri ? $this->new_pri['title'] : '',
			'new_priority_pri'   => $this->new_pri ? $this->new_pri['priority'] : 0,
		);
	}

	public function getEventType()
	{
		return 'property';
	}
}
