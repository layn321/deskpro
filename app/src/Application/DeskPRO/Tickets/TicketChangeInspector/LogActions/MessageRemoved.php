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

class MessageRemoved extends AbstractLogAction
{
	/**
	 * @var \Application\DeskPRO\Entity\TicketMessage
	 */
	protected $message;

	/**
	 * @var int
	 */
	protected $old_id;

	public function __construct($message)
	{
		$this->message = $message;

		// We need to copy the ID now because after
		// the records have been removed, Doctrine sets the IDs to 0
		$this->old_id = $message->id;
	}

	public function getLogName()
	{
		return 'message_removed';
	}

	public function getLogDetails()
	{
		$details = array();
		$details['id_after'] = $this->old_id;
		$details['message_id'] = $this->old_id;
		$details['person_id'] = $this->message->person->id;
		$details['person_name'] = $this->message->person->getDisplayName();
		$details['is_agent_note'] = $this->message->is_agent_note;
		$details['is_agent_message'] = $this->message->person->is_agent;
		$details['old_message'] = $this->message->getMessageHtml();

		return $details;
	}

	public function getEventType()
	{
		return 'property';
	}
}
