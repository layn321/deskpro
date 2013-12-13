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

class TicketEmailMessage extends AbstractLogAction
{
	protected $message;
	protected $who_emailed;
	protected $who_cced;

	public function __construct(array $info)
	{
		$this->message = '';
		if (!empty($info['message'])) {
			$this->message = $info['message'];
		} elseif (!empty($info['template'])) {
			$this->message = $info['template'];
		}
		$this->who_emailed = $info['emailed'];
		$this->who_cced = $info['cced'];
	}

	public function getLogName()
	{
		return 'user_notify';
	}

	public function getLogDetails()
	{
		$details = array();
		$details['message'] = $this->message;
		$details['who_emailed'] = array();
		$details['who_cced'] = array();

		foreach ($this->who_emailed as $person) {
			$details['who_emailed'][] = array(
				'person_id'    => $person['id'],
				'person_name'  => $person['display_name'],
				'person_email' => $person['primary_email_address']
			);
		}
		foreach ($this->who_cced as $part) {
			$person = $part->person;
			$details['who_cced'][] = array(
				'person_id'    => $person['id'],
				'person_name'  => $person['display_name'],
				'person_email' => $person['primary_email_address']
			);
		}

		if (!$details['who_emailed'] && !$details['who_cced']) {
			return array();
		}

		return $details;
	}

	public function getEventType()
	{
		return 'ticket_email_message';
	}
}
