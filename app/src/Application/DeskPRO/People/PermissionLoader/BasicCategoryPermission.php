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
abstract class BasicCategoryPermission extends AbstractLoader
{
	/**
	 * An array of categories allowed for real, that we get by computing
	 * inheritance.
	 * @var array
	 */
	protected $allowed_cats = array();

	/**
	 * An array of disallowed categories
	 * @var array
	 */
	protected $disallowed_cats = array();

	abstract protected function getCategoryEntity();

	protected function init()
	{
		$this->allowed_cats= App::getEntityRepository($this->getCategoryEntity())->getCategoriesForUsergroups($this->getUsergroupIds());

		$all_ids = array_keys(App::getEntityRepository($this->getCategoryEntity())->getCategoryOptions());
		$this->disallowed_cats = array_diff($all_ids, $this->allowed_cats);
	}


	/**
	 * Are there access permissions at all applied to this user?
	 *
	 * That is, is this user denied access to any category? If not, then we can forgo applying
	 * permissions in various places because the user just has access to everything.
	 *
	 * @return bool
	 */
	public function hasRestrictions()
	{
		return !empty($this->disallowed_cats);
	}


	/**
	 * Is a cateogry allowed?
	 *
	 * @return bool
	 */
	public function isCategoryAllowed($id)
	{
		return in_array($id, $this->allowed_cats);
	}


	/**
	 * Get an array of all allowed categories.
	 *
	 * @return array
	 */
	public function getAllowedCategories()
	{
		return $this->allowed_cats;
	}


	/**
	 * Get an array of disallowed categories. (i.e., inverse of getDisallowedCategories)
	 *
	 * @return array
	 */
	public function getDisallowedCategories()
	{
		return $this->disallowed_cats;
	}


	/**
	 * Returns the smallet set of ID's that can be used to apply permissions.
	 *
	 * For example, if you have access to all categories except one, then it's better
	 * to just EXCLUDE that one category. Conversely, if you're denied all categories
	 * except one, it's better to just INCLUDE that one category.
	 *
	 * The return value will be array('type' => 'allowed|disallowed', 'ids' => array(1,2,3))
	 *
	 * @return array
	 */
	public function getSmallestSet()
	{
		if (count($this->disallowed_cats) > count($this->allowed_cats)) {
			return array('type' => 'allowed', 'ids' => $this->allowed_cats);
		} else {
			return array('type' => 'disallowed', 'ids' => $this->diallowed_cats);
		}
	}


	/**
	 * Get an array of data we'll serialize
	 *
	 * @return array
	 */
	protected function serializeData()
	{
		return array(
			'allowed_cats'    => $this->allowed_cats,
			'disallowed_cats' => $this->disallowed_cats
		);
	}


	/**
	 * Initialize this object with an array of saved data
	 *
	 * @param array $data
	 */
	protected function unserializeData(array $data)
	{
		$this->allowed_cats     = $data['allowed_cats'];
		$this->disallowed_cats  = $data['disallowed_cats'];
	}
}
