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

namespace Application\DeskPRO\People;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;

use Orb\Util\Arrays;

/**
 * The personlistener listens for changes to a ticket, and then runs inspections once the changes
 * are committed.
 */
class PersonChangeTracker extends \Application\DeskPRO\Domain\ChangeTracker
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var bool
	 */
	protected $running = false;

	public function __construct(Person $person)
	{
		$this->entity = $person;
		$this->person = $person;
	}


	/**
	 * Get the person
	 *
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}



	/**
	 * Was the person new (just created?)
	 *
	 * @return bool
	 */
	public function isNewPerson()
	{
		return $this->person->isNewPerson();
	}



	public function propertyChanged($sender, $prop, $old_val, $new_val)
	{
		if (in_array($prop, array('notes'))) {
			$this->recordMultiPropertyChanged($prop, $old_val, $new_val);
		} else {
			$this->recordPropertyChanged($prop, $old_val, $new_val);
		}
	}

	public function preSave()
	{
		if ($this->running) {
			return;
		}
		$this->running = true;

		$this->running = false;
	}



	/**
	 * Notify all listeners that changes to the person have been committed
	 *
	 * @return void
	 */
	public function done()
	{

	}
}
