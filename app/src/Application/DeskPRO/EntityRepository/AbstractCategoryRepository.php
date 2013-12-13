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

use Application\DeskPRO\EntityRepository\Helper\CategoryHierarchy;

use Orb\Util\Arrays;

class AbstractCategoryRepository extends AbstractEntityRepository
{
	protected $_cat_helper = null;

	/**
	 * @return \Application\DeskPRO\EntityRepository\Helper\CategoryHierarchy
	 */
	public function getCategoryHelper()
	{
		if ($this->_cat_helper !== null) return $this->_cat_helper;

		$this->_cat_helper = new CategoryHierarchy(
			$this->getEntityManager(),
			$this,
			$this->getEntityName(),
			$this->getClassMetadata(),
			$this->getPermissionTableName()
		);

		return $this->_cat_helper;
	}

	public function getPermissionTableName()
	{
		return null;
	}


	/**
	 * Runs through the hierarchy to reset 'depth' and 'root' values,
	 * and updates all 'display_order' so that they are stored in
	 * real tree order.
	 *
	 * This isnt just "bad" thing, it sholud be called for example
	 * when a new category is created, or one is deleted.
	 *
	 * @return void
	 */
	public function repair()
	{
		$cats = $this->_em->getConnection()->fetchAllKeyed("
			SELECT id, parent_id
			FROM `".$this->getTableName()."`
			ORDER BY display_order ASC, id ASC
		", array(), 'id');

		$flat = Arrays::intoHierarchy($cats);
		$flat = Arrays::flattenHierarchy($flat);

		$all = $this->_em->createQuery("
			SELECT c
			FROM {$this->getEntityName()} c INDEX BY c.id
		")->execute();

		$display_order = 0;

		$current_root = null;

		$this->_em->getConnection()->beginTransaction();

		try {
			foreach ($flat as $cid => $cinfo) {
				$cat = $all[$cid];

				$display_order += 10;
				$cat->display_order = $display_order;
				$cat->depth = $cinfo['depth'];

				if (!$cat->parent) {
					$current_root = $cat;
					$cat->root = $cat->getId();
				} else {
					$cat->root = $current_root['id'];
				}

				$this->_em->persist($cat);
			}

			$this->_em->flush();
			$this->_em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->_em->getConnection()->rollback();
			throw $e;
		}
	}


	/**
	 * Pass through to helper
	 *
	 * @param $method
	 * @param $args
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->getCategoryHelper(), $method), $args);
	}
}
