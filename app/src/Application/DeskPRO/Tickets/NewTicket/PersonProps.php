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
 * @subpackage UserBundle
 */

namespace Application\DeskPRO\Tickets\NewTicket;

use Application\DeskPRO\Entity;

/**
 * This wraps up the 'person' data of a newticket.
 */
class PersonProps
{
	/**
	 * A real person object, represents a logged in user
	 * if the user is logged in. Otherwise this should be null for a guest
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	public $person_obj;

	public $name = '';
	public $email = '';

	public function __construct(Entity\Person $person = null)
	{
		$this->person_obj = $person;

		if ($person) {
			$this->first_name = $person['first_name'];
			$this->last_name  = $person['last_name'];
			if ($person['first_name'] && $person['last_name']) {
				$this->name = $this->first_name . ' ' . $this->last_name;
			} elseif ($person['name']) {
				$this->name = $person['name'];
			} else {
				$this->name = '';
			}

			$this->email = $person->getPrimaryEmailAddress();
		}
	}
}
