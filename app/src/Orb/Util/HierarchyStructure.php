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
 * Orb
 *
 * @package Orb
 * @category Util
 */

namespace Orb\Util;

/**
 * Utility functions that help work with hierarchies.
 *
 * Hierarchies can be anything so long as they implement an array interface.
 *
 * @static
 */
class HierarchyStructure
{
	/**
	 * @var array
	 */
	public $parent_map;

	/**
	 * @var array
	 */
	public $child_map;

	/**
	 * @var array
	 */
	public $cats;

	/**
	 * @var array
	 */
	public $roots;

	/**
	 * @var string
	 */
	public $id_key = 'id';

	/**
	 * @var string
	 */
	public $parent_key = 'parent';

	/**
	 * @var string
	 */
	public $children_key = 'children';

	protected $flat_hierarchy = array();

	public function __construct($cats)
	{
		$this->cats = $cats;
	}

	/**
	 * @return array
	 */
	public function getFlatHierarchy()
	{
		if ($this->flat_hierarchy) {
			return $this->flat_hierarchy;
		}

		$this->flat_hierarchy = array();
		$this->_getFlatHierarchyArray($this->flat_hierarchy, 0);

		return $this->flat_hierarchy;
	}

	public function _getFlatHierarchyArray(&$array, $parent_id, $depth = 0)
	{
		foreach ($this->cats as $cat) {
			if (($cat->parent && $cat->parent->getId() == $parent_id) || (!$parent_id && !$cat->parent)) {
				$array[$cat->getId()] = array(
					'id' => $cat->getId(),
					'parent_id' => $cat->parent ? $cat->parent->getId() : 0,
					'depth' => $depth,
					//'category' => $cat
				);
				$this->_getFlatHierarchyArray($array, $cat->getId(), $depth+1);
			}
		}
	}



	/**
	 * Get an array of id=>parent
	 *
	 * @param $cats
	 * @return array
	 */
	public function getParentMap()
	{
		if ($this->parent_map !== null) {
			return $this->parent_map;
		}

		$this->parent_map = array();

		foreach ($this->cats as $cat) {
			$this->parent_map[$cat[$this->id_key]] = $cat[$this->parent_key];
		}

		return $this->parent_map;
	}


	/**
	 * Get an array of parent IDs for a category in order (left to right, aka top to bottom)
	 *
	 * @param $id
	 * @return array
	 */
	public function getPathIds($cat)
	{
		$this->getParentMap();

		$cat_id = $cat[$this->id_key];

		$ids = array();
		while (!empty($this->parent_map[$cat_id])) {
			$cat_id = $this->parent_map[$cat_id];
			$ids[] = $cat_id;
		}

		$that = $this;
		uasort($ids, function ($a, $b) use ($that) {
			$a_depth = isset($that->cats[$a]) ? $that->cats[$a] : 0;
			$b_depth = isset($that->cats[$b]) ? $that->cats[$b] : 0;

			return ($a_depth < $b_depth) ? -1 : 1;
		});

		return $ids;
	}


	/**
	 * Get an array of parents for a category in order (left to right, aka top to bottom)
	 *
	 * @param $id
	 * @return array
	 */
	public function getPath($cat)
	{
		$ids = $this->getPathIds($cat);

		$cats = array();
		foreach ($ids as $id) {
			if (isset($this->cats[$id])) {
				$cats[$id] = $this->cats[$id];
			}
		}

		return $cats;
	}


	/**
	 * Get children IDs of a category
	 *
	 * @param int|\Application\DeskPRO\Entity\CategoryAbstract $category
	 * @param bool $direct Only get the immediate children?
	 * @return int[]
	 */
	public function getChildrenIds($category = null, $direct = true)
	{
		if (!isset($this->child_map[$category[$this->id_key]])) {
			return array();
		}

		$children_ids = $this->child_map[$category[$this->id_key]];
		if ($direct) {
			return $children_ids;
		}

		foreach ($children_ids as $cid) {
			$children_ids = array_merge($children_ids, $this->getChildrenIds($this->cats[$cid], false));
		}

		return $children_ids;
	}


	/**
	 * @param null $category
	 * @param bool $direct
	 * @return array
	 */
	public function getChildren($category = null, $direct = true)
	{
		$cats = array();
		foreach ($this->getChildrenIds() as $cid) {
			$cats[$cid] = $this->cats[$cid];
		}

		return $cats;
	}


	/**
	 * @param $category
	 * @return int
	 */
	public function getParentId($category)
	{
		if (!isset($this->parent_map[$category->id])) {
			return 0;
		}

		return $this->parent_map[$category->id];
	}


	/**
	 * @param $category
	 * @return mixed
	 */
	public function getParent($category)
	{
		$pid = $this->getParentId($category);
		if (!$pid || !isset($this->cats[$pid])) {
			return null;
		}

		return $this->cats[$pid];
	}
}
