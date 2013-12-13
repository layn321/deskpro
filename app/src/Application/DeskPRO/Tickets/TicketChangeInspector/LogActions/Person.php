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

class Person extends AbstractLogAction
{
	protected $old_person;
	protected $new_person;

	public function __construct($old_person, $new_person)
	{
		$this->old_person = $old_person;
		$this->new_person = $new_person;
	}

	public function getLogName()
	{
		return 'changed_person';
	}

	public function getLogDetails()
	{
		return array(
			'id_before' => $this->old_person['id'],
			'id_after'  => $this->new_person['id'],

			'old_person_id'     => $this->old_person['id'],
			'old_person_name'   => $this->old_person['display_name'],
			'old_person_email'  => $this->old_person['primary_email_address'],
			'new_person_id'     => $this->new_person['id'],
			'new_person_name'   => $this->new_person['display_name'],
			'new_person_email'  => $this->new_person['primary_email_address'],
		);
	}

	public function getEventType()
	{
		return 'property';
	}
}
