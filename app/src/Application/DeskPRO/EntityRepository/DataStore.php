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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\Entity\DataStore as DataStoreEntity;

use Doctrine\ORM\EntityRepository;

class DataStore extends AbstractEntityRepository
{
	public function getByCode($code, $type = null)
	{
		$info = DataStoreEntity::getPartsFromCode($code);
		if (!$info) return null;

		$tmpdata = $this->find($info['id']);
		if ($tmpdata['auth'] != $info['auth']) return null;

		if ($type AND $tmpdata->getType() != $type) return null;

		return $tmpdata;
	}


	/**
	 * Get data by its unique name
	 *
	 * @param string $name
	 * @return DataStoreEntity
	 */
	public function getByName($name, $create_unset = false)
	{
		$ds = $this->findOneBy(array('name' => $name));

		if (!$ds && $create_unset) {
			$ds = new DataStoreEntity();
			$ds->name = $name;
		}

		return $ds;
	}
}
