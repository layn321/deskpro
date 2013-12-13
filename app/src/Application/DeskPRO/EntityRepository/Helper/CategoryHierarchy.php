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

namespace Application\DeskPRO\EntityRepository\Helper;

use Application\DeskPRO\App;
use Application\DeskPRO\EntityRepository\AbstractEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;

use Orb\Util\Arrays;
use Orb\Util\Strings;

class CategoryHierarchy
{
	/**
	 * @var \Application\DeskPRO\EntityRepository\AbstractCategoryRepository
	 */
	protected $repos;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Doctrine\ORM\Mapping\ClassMetadata
	 */
	protected $class;

	/**
	 * @var string
	 */
	protected $entity_name;

	/**
	 * @var string
	 */
	protected $table_name;

	/**
	 * @var string
	 */
	protected $cache_tag = null;

	protected $_cats = null;
	protected $_cat_hierarchy = null;
	protected $_cat_hierarchy_flat = null;
	protected $_cat_names = null;
	protected $_cat_ids = array();
	protected $_cat_parent_map = array();

	public function __construct(EntityManager $em, AbstractEntityRepository $repos, $entity_name, ClassMetadata $class, $cache_tag = null)
	{
		$this->repos       = $repos;
		$this->em          = $em;
		$this->class       = $class;
		$this->entity_name = $entity_name;
		$this->table_name  = $class->getTableName();

		if (!$cache_tag) {
			$cache_tag = $this->table_name;
		}

		$this->cache_tag = $cache_tag;
	}

	/**
	 * Get all root node ids
	 *
	 * @return array
	 */
	public function getRootNodeIds()
	{
		$this->getInHierarchy();

		$root_ids = array();

		foreach ($this->_cat_hierarchy as $c) {
			$root_ids[] = $c['id'];
		}

		return $root_ids;
	}


	/**
	 * Get all root nodes
	 *
	 * @return array
	 */
	public function getRootNodes()
	{
		$root_ids = $this->getRootNodeIds();

		if (!$root_ids) {
			return array();
		}

		return $this->repos->getByIds($root_ids, true);
	}


	/**
	 * Get all category IDs that exists
	 *
	 * @return array
	 */
	public function getIds()
	{
		$this->getInHierarchy();
		return $this->_cat_ids;
	}


	/**
	 * Get a plain hierarchy array
	 *
	 * @return null
	 */
	public function getInHierarchy($reset = false)
	{
		if (!$reset && $this->_cat_hierarchy !== null) return $this->_cat_hierarchy;

		if (is_array($reset)) {
			$cats = $reset;
		} else {
			if ($this->em->getUnitOfWork()->isAddedPreloadedEntity($this->entity_name)) {
				$this->em->getUnitOfWork()->preloadEntitySet($this->entity_name);
				$cats = array();
				$select_keys = array('id', 'parent_id', 'title', 'display_order');
				if ($this->table_name == 'departments') {
					$select_keys = array('id', 'parent_id', 'title', 'user_title', 'display_order');
				}
				foreach ($this->repos->getIdentityHelper()->findAll() as $c) {
					$id = $c->getId();
					$cats[$id] = array();
					foreach ($select_keys as $k) {
						if ($k == 'id') {
							$cats[$id]['id'] = $id;
						} elseif ($k == 'parent_id') {
							$cats[$id][$k] = $c->parent ? $c->parent->getId() : 0;
						} else {
							$cats[$id][$k] = $c[$k];
						}
					}
				}

				uasort($cats, function($a, $b) {
					if ($a['display_order'] == $b['display_order']) {
						return 0;
					}

					return $a['display_order'] < $b['display_order'] ? -1 : 1;
				});
			} else {
				$select = 'id, parent_id, title';
				if ($this->table_name == 'departments') {
					$select = 'id, parent_id, title, user_title';
				}

				$cats = $this->em->getConnection()->fetchAllKeyed("
					SELECT $select
					FROM {$this->table_name}
					ORDER BY display_order ASC, id ASC
				", array(), 'id');
			}
		}

		$this->_cat_ids = array();
		foreach ($cats as &$c) {
			$c['url_slug'] = $c['id'] . '-' . Strings::slugifyTitle($c['title']);

			if (!isset($c['user_title']) || !$c['user_title']) {
				$c['user_title'] = $c['title'];
			}

			$this->_cat_ids[] = $c['id'];
			$this->_cats[$c['id']] = $c;
		}
		unset($c);

		foreach ($cats as $c) {
			$this->_cat_parent_map[$c['id']] = $c['parent_id'] ? $c['parent_id'] : 0;
		}

		$this->_cat_names = Arrays::flattenToIndex($cats, 'title');

		$cats = Arrays::intoHierarchy($cats, null);
		$this->_cat_hierarchy = $cats;
		$this->_cat_hierarchy_flat = Arrays::flattenHierarchy($cats);

		return $this->_cat_hierarchy;
	}


	/**
	 * Get an array of child=>parent for all categories.
	 *
	 * @return array
	 */
	public function getParentMap()
	{
		return $this->_cat_parent_map;
	}


	/**
	 * Gets the names for each cat, indexed by cat ID.
	 *
	 * @return array
	 */
	public function getNames($for_ids = null)
	{
		$this->getInHierarchy();
		if ($for_ids === null) {
			$for_ids = $this->_cat_ids;
		}

		$ret = array();
		foreach ($for_ids as $id) {
			if (isset($this->_cat_names[$id])) {
				$ret[$id] = $this->_cats[$id]['title'];
			}
		}

		return $ret;
	}



	/**
	 * Gets a flat array of cat names, indexed by cat ID. Children
	 * names are separated by $sep.
	 *
	 * @return array
	 */
	public function getFullNames($sep = ' > ', $include_tops = true)
	{
		if ($sep === null) {
			$sep = ' > ';
		}
		return $this->_getFullNames(array(), $this->getInHierarchy(), $sep, $include_tops);
	}

	protected function _getFullNames($basenames, $cats, $sep, $include_tops)
	{
		$names = array();

		foreach ($cats as $k => $cat) {
			$name = $basenames;
			$name[] = $cat['title'];

			if (!$cat['children'] OR $include_tops) {
				$names[$k] = implode($sep, $name);
			}
			if ($cat['children']) {
				$names = Arrays::mergeAssoc($names, $this->_getFullNames($name, $cat['children'], $sep, $include_tops));
			}
		}

		return $names;
	}


	/**
	 * Get a flat hierarchy, where children are in the main array but have an increasing 'depth'
	 *
	 * @return array
	 */
	public function getFlatHierarchy()
	{
		$this->getInHierarchy();
		return $this->_cat_hierarchy_flat;
	}


	/**
	 * Get IDs of parents in order (left to right)
	 *
	 * @param $category
	 * @return array
	 */
	public function getPathIds($category)
	{
		$ids = array();

		$cat_id = is_object($category) ? $category->getId() : $category;

		while (!empty($this->_cat_parent_map[$cat_id])) {
			$cat_id = $this->_cat_parent_map[$cat_id];
			$ids[] = $cat_id;
		}

		$ids = array_reverse($ids);

		return $ids;
	}


	/**
	 * Get category entities for all parents
	 *
	 * @param $category
	 * @return array
	 */
	public function getPath($category)
	{
		$ids = $this->getPathIds($category);

		if (!$ids) {
			return array();
		}

		return $this->repos->getByIds($ids, true);
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
		$this->getInHierarchy();

		// All ids if null
		if ($category === null) {
			return $this->_cat_ids;
		}

		$cat_id = is_object($category) ? $category->getId() : $category;
		$child_ids = array();

		if (!isset($this->_cat_hierarchy_flat[$cat_id])) {
			return array();
		}

		$start = false;
		$depth = null;
		foreach ($this->_cat_hierarchy_flat as $c) {
			if ($start) {
				// Once we go under the cat depth, we're no
				// longer traversing this category tree
				if ($c['depth'] <= $depth) {
					break;
				}

				// Once we get one level deeper, then we're
				// no longer direct children
				if ($direct && $c['depth'] >= $depth+2) {
					continue;
				}

				$child_ids[] = $c['id'];
			} elseif ($c['id'] == $cat_id) {
				$start = true;
				$depth = $c['depth'];
			}
		}

		return $child_ids;
	}


	/**
	 * Get children IDs of a category
	 *
	 * @param int|\Application\DeskPRO\Entity\CategoryAbstract $category
	 * @param bool $direct Only get the immediate children?
	 * @return \Application\DeskPRO\Entity\CategoryAbstract[]
	 */
	public function getChildren($category = null, $direct = true)
	{
		$ids = $this->getChildrenIds($category, $direct);
		if (!$ids) {
			return array();
		}
		return $this->repos->getByIds($ids, true);
	}

	/**
	 * @deprecated
	 */
	public function children($category = null, $direct = true)
	{
		return $this->getChildren($category, $direct);
	}


	/**
	 * Get an array of all cat IDs in a tree including the parent itself (optionally disabled).
	 *
	 * @param int $parent_id
	 * @return array
	 */
	public function getIdsInTree($parent_id, $incude_top = true)
	{
		$parent_id = is_object($parent_id) ? $parent_id->getId() : $parent_id;

		$ids = $this->getChildrenIds($parent_id);

		if ($incude_top) {
			array_unshift($ids, $parent_id);
		}

		return $ids;
	}


	/**
	 * Get IDs of all categories that are leafs (dont have children)
	 *
	 * @retrun array
	 */
	public function getLeafIds()
	{
		return App::getDb()->fetchAllCol("
			SELECT DISTINCT c.id
			FROM feedback_categories c
			LEFT JOIN feedback_categories AS c2 ON (c2.parent_id = c.id)
			WHERE c2.id IS NULL
		");
	}





	public function getTotalCounts(array $counts)
	{
		$counts['0_total'] = 0;

		foreach ($this->getIds() as $c_id) {
			$total = 0;
			if (isset($counts[$c_id])) {
				$total = $counts[$c_id];
			}

			foreach ($this->getChildrenIds($c_id, false) as $child_id) {
				if (isset($counts[$child_id])) {
					$total += $counts[$child_id];
				}
			}

			$counts["{$c_id}_total"] = $total;
			$counts['0_total'] += $total;
		}

		return $counts;
	}

	public function getCategoriesForUsergroups(array $usergroup_ids)
	{
		$permission_table_name = $this->repos->getPermissionTableName();

		if (!$permission_table_name) {
			throw new \BadMethodCallException('There is no permissions table set');
		}

		if (!$usergroup_ids) {
			return array();
		}

		$cat_ids = App::getDb()->fetchAllCol("
			SELECT category_id
			FROM {$permission_table_name}
			WHERE usergroup_id IN (" . implode(',', $usergroup_ids) . ")
		");

		return $cat_ids;
	}
}
