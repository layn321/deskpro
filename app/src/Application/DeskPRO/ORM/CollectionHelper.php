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
 * @category ORM
 */

namespace Application\DeskPRO\ORM;

class CollectionHelper
{
	protected $entity;
	protected $prop;

	public function __construct($entity, $prop)
	{
		$this->entity = $entity;
		$this->prop = $prop;
	}

	/**
	 * Given an array of records we want the entity to contain ("only $set"),
	 * get an array of records that need to be added or removed. Essentially an easy diff
	 *
	 * @param array $set
	 */
	public function getAddRemoveForSet(array $set)
	{
		$prop = $this->prop;

		$have_ids = array();
		$want_ids = array();

		foreach ($this->entity->$prop as $item) {
			$have_ids[] = $item->id;
		}

		foreach ($set as $item) {
			$want_ids[] = $item->id;
		}

		$add_ids = array_diff($want_ids, $have_ids);
		$del_ids = array_diff($have_ids, $want_ids);

		return array(
			'add' => $add_ids,
			'del' => $del_ids,
		);
	}


	/**
	 * Add or remove from the collection so it matcehs $set.
	 *
	 * @param array $set
	 */
	public function setCollection(array $set)
	{
		$prop = $this->prop;

		$info = $this->getAddRemoveForSet($set);
		$add_ids = $info['add'];
		$del_ids = $info['del'];

		foreach ($del_ids as $id) {
			foreach ($this->entity->$prop as $k => $item) {
				if ($item->id == $id) {
					$this->entity->$prop->remove($k);
					break;
				}
			}
		}

		foreach ($set as $item) {
			if (in_array($item->id, $add_ids)) {
				$this->entity->$prop->add($item);
			}
		}
	}
}
