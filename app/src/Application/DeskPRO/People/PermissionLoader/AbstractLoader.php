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

namespace Application\DeskPRO\People\PermissionLoader;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;

use Orb\Util\Arrays;

/**
 * A permission loader knows how to load permissions for a thing.
 */
abstract class AbstractLoader implements \Serializable
{
	/**
	 * @var int[]
	 */
	protected $usergroup_ids;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var int
	 */
	protected $person_id = 0;

	public function setPersonContext(Person $person)
	{
		$this->person = $person;
		$this->person_id = $person->id;
	}

	/**
	 * @param int[] $usergroup_ids
	 * @param \Application\DeskPRO\Entity\Person $person Optional person to fetch overrides for
	 */
	public function __construct(array $usergroup_ids, Person $person = null)
	{
		$this->usergroup_ids = $usergroup_ids;
		if (App::getDataService('Usergroup')->find(1)->is_enabled) {
			$this->usergroup_ids[] = 1;
		} else {
			$this->usergroup_ids[] = 0;
		}
		$this->usergroup_ids = array_unique($this->usergroup_ids);
		sort($this->usergroup_ids, \SORT_NUMERIC);

		$this->person = $person;

		$this->init();
	}

	protected function init() {}


	/**
	 * Get the usergroup IDs represented by the loaded permissions
	 *
	 * @return array
	 */
	public function getUsergroupIds()
	{
		return $this->usergroup_ids;
	}


	/**
	 * Get an array of data we'll serialize
	 *
	 * @return array
	 */
	abstract protected function serializeData();

	public function serialize()
	{
		$data = $this->serializeData();
		$data['usergroup_ids'] = $this->usergroup_ids;

		return serialize($data);
	}

	/**
	 * Initialize this object with an array of saved data
	 *
	 * @param array $data
	 */
	abstract protected function unserializeData(array $data);

	public function unserialize($data)
	{
		$data = unserialize($data);

		$this->usergroup_ids = $data['usergroup_ids'];
		$this->unserializeData($data);
	}
}
