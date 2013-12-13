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

class Hold extends AbstractLogAction
{
	/**
	 * @var bool
	 */
	protected $is_hold;

	/**
	 * @var bool
	 */
	protected $was_hold;

	public function __construct($was_hold, $is_hold)
	{
		$this->was_hold = (bool)$was_hold;
		$this->is_hold  = (bool)$is_hold;
	}

	public function getLogName()
	{
		return 'changed_hold';
	}

	public function getLogDetails()
	{
		if ($this->is_hold == $this->was_hold) {
			return array();
		}

		return array(
			'id_before' => $this->was_hold ? 1 : 0,
			'id_after'  => $this->is_hold ? 0 : 1,

			'was_hold'  => $this->was_hold,
			'is_hold'   => $this->is_hold,
		);
	}

	public function getEventType()
	{
		return 'property';
	}
}
