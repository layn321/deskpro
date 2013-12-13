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

class Message extends AbstractLogAction
{
	protected $message;

	public function __construct($message)
	{
		$this->message = $message;
	}

	public function getLogName()
	{
		return 'message_created';
	}

	public function getLogDetails()
	{
		if (!$this->message) {
			return array();
		}

		$details = array();
		$details['id_after'] = $this->message['id'];
		$details['message_id'] = $this->message['id'];
		$details['creation_system'] = $this->message['creation_system'];
		$details['is_agent_note'] = $this->message->is_agent_note;
		$details['is_agent_message'] = $this->message->person->is_agent;

		if ($this->message['ip_address']) {
			$details['ip_address'] = $this->message['ip_address'];
		}

		if ($this->message['email']) {
			$details['email'] = $this->message['email'];
		}

		return $details;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getEventType()
	{
		return 'message_created';
	}
}
