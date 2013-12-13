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

class UserNotify extends AbstractLogAction
{
	protected $type;
	protected $who_emailed;
	protected $who_cced;
	protected $from_name = '';
	protected $from_email = '';

	public function __construct(array $info)
	{
		$this->type        = $info['notify_type'];
		$this->who_emailed = $info['emailed'];
		$this->who_cced    = $info['cced'];
		$this->from_name   = isset($info['from_name']) ? $info['from_name'] : '';
		$this->from_email  = isset($info['from_email']) ? $info['from_email'] : '';
	}

	public function getLogName()
	{
		return 'user_notify';
	}

	public function getLogDetails()
	{
		// Gateway accounts could be an alias, so we need to look it up
		if ($this->from_email) {
			$matcher = new \Application\DeskPRO\EmailGateway\AddressMatcher(App::getContainer()->getEm());
			$this->from_email = $matcher->getOutgoingEmailAliasAddress($this->from_email);
		}

		$details = array();
		$details['type']        = $this->type;
		$details['who_emailed'] = array();
		$details['who_cced']    = array();
		$details['from_name']   = $this->from_name;
		$details['from_email']  = $this->from_email;

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
		return 'user_notify';
	}
}
