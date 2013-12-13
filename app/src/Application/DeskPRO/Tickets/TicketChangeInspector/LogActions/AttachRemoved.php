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

class AttachRemoved extends AbstractLogAction
{
	/**
	 * @var \Application\DeskPRO\Entity\TicketAttachment
	 */
	protected $attach;

	/**
	 * @var int
	 */
	protected $old_id;

	/**
	 * @var int
	 */
	protected $old_blob_id;

	public function __construct(\Application\DeskPRO\Entity\TicketAttachment $attach)
	{
		$this->attach = $attach;

		// We need to copy the IDs now because after
		// the records have been removed, Doctrine sets the IDs to 0
		$this->old_id = $attach->id;
		$this->old_blob_id = $attach->blob->id;
	}

	public function getLogName()
	{
		return 'attach_removed';
	}

	public function getLogDetails()
	{
		if (!$this->attach) {
			return array();
		}

		$details = array();
		$details['id_before']     = $this->old_id;
		$details['old_attach_id'] = $this->old_id;
		$details['blob_id']       = $this->attach->blob->id;
		$details['filename']      = $this->attach->blob->filename;
		$details['filesize']      = $this->attach->blob->filesize;
		$details['message_id']    = $this->attach->message ? $this->attach->message->getId() : null;
		$details['message_person_id'] = $this->attach->message ? $this->attach->message->person->getId() : null;
		$details['message_person_name'] = $this->attach->message ? $this->attach->message->person->getDisplayName() : null;

		return $details;
	}

	public function getEventType()
	{
		return 'property';
	}
}
