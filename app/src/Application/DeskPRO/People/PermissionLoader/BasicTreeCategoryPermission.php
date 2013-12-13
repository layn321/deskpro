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
use Application\DeskPRO\Entity;
use Application\DeskPRO\Entity\Person;

use Orb\Util\Arrays;

/**
 * A generic category loader
 */
abstract class BasicTreeCategoryPermission extends BasicCategoryPermission
{
	/**
	 * An array of specific categories allowed by actual database records
	 * @var array
	 */
	protected $specific_cats = array();

	protected function init()
	{
		$this->specific_cats = App::getEntityRepository($this->getCategoryEntity())->getCategoriesForUsergroups($this->getUsergroupIds());
		$full = App::getEntityRepository($this->getCategoryEntity())->getRootNodes();

		$this->_computeTree($full);

		$all_ids = App::getEntityRepository($this->getCategoryEntity())->getIds();
		$this->disallowed_cats = array_diff($all_ids, $this->allowed_cats);
	}

	protected function _computeTree($tree, $default = null)
	{
		foreach ($tree as $node) {
			if ($default OR in_array($node['id'], $this->specific_cats)) {
				$this->allowed_cats[] = $node['id'];

				if ($node['children']) {
					$this->_computeTree($node['children']);
				}
			}
		}
	}


	/**
	 * Get an array of specific categories allowed as defined by the db.
	 * This is before inheritance is considered.
	 *
	 * @return array
	 */
	public function getSpecificCategories()
	{
		return $this->specific_cats;
	}


	/**
	 * Get an array of data we'll serialize
	 *
	 * @return array
	 */
	protected function serializeData()
	{
		$data = parent::serializeData();
		$data['specific_cats'] = $this->specific_cats;

		return $data;
	}


	/**
	 * Initialize this object with an array of saved data
	 *
	 * @param array $data
	 */
	protected function unserializeData(array $data)
	{
		parent::unserializeData($data);
		$this->specific_cats = $data['specific_cats'];
	}
}
