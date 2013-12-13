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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer;

use Orb\Log\Logger;
use Orb\Service\Zendesk\Zendesk;

class ZendeskApiLogger
{
	public $count = 0;
	public $time = 0.0;

	protected $last_start = 0;
	protected $last_info = null;

	public function callback($event_name, array $ev_data)
	{
		switch ($event_name) {
			case 'preCall':
				$this->startCall($ev_data);
				break;
			case 'postCall':
				$this->endCall($ev_data);
				break;
		}

		return $ev_data;
	}

	public function startCall(array $ev_data)
	{
		$this->last_info = $ev_data;
		$this->last_start = microtime(true);
	}

	public function endCall(array $ev_data)
	{
		if (!$this->last_info) {
			return;
		}

		$this->time += microtime(true) - $this->last_start;
		$this->count++;

		$this->last_info = null;
	}
}