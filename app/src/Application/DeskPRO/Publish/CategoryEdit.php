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
 * @subpackage Addons
 */

namespace Application\DeskPRO\Publish;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\ArticleCategory;
use Application\DeskPRO\Entity\DownloadCategory;
use Application\DeskPRO\Entity\NewsCategory;
use Application\DeskPRO\People\PersonContextInterface;

use Application\DeskPRO\Searcher\ArticleSearch;

use Orb\Util\Arrays;
use Orb\Util\Util;

/**
 * Helps fetch info related to structure of Publish
 */
class CategoryEdit
{
	const ARTICLES  = 'articles';
	const DOWNLOADS = 'downloads';
	const NEWS      = 'news';

	/**
	 * Add a new category to the systme
	 *
	 * @throws \InvalidArgumentException
	 * @param $type
	 * @param $title
	 * @return \Application\DeskPRO\Entity\ArticleCategory|\Application\DeskPRO\Entity\DownloadCategory|\Application\DeskPRO\Entity\NewsCategory|array
	 */
	public static function addCategory($type, $title)
	{
		switch ($type) {
			case self::ARTICLES:
				$obj = new ArticleCategory;
				break;
			case self::DOWNLOADS:
				$obj = new DownloadCategory;
				break;
			case self::NEWS:
				$obj = new NewsCategory;
				break;
			default:
				throw new \InvalidArgumentException("Unknown type `$type`");
		}

		$obj['title'] = $title;

		App::getOrm()->persist($obj);

		App::getOrm()->getRepository(get_class($obj))->repair();
		App::getOrm()->flush();

		// By default also add 'Everyone' permission
		$perm_table = App::getOrm()->getRepository(get_class($obj))->getPermissionTableName();
		App::getDb()->insert($perm_table, array(
			'category_id'  => $obj->getId(),
			'usergroup_id' => '1'
		));

		App::getContainer()->getSystemService('publish_structure_cache')->flush();

		return $obj;
	}


	/**
	 * Update titles for categoryes. $titles is id=>title
	 *
	 * @param $type
	 * @param array $titles
	 * @return array
	 */
	public static function updateTitles($type, array $titles)
	{
		$entity = self::getEntityNameFor($type);

		$ids = array_keys($titles);
		$ids = Arrays::castToType($ids, 'integer');

		if (!$ids) {
			return array();
		}

		$cats = App::getOrm()->createQuery("
			SELECT c
			FROM $entity c INDEX BY c.id
			WHERE c.id IN (" . implode(',', $ids) . ")
		")->execute();

		App::getOrm()->beginTransaction();

		foreach ($titles as $id => $title) {
			if (!isset($cats[$id])) {
				continue;
			}

			$cats[$id]['title'] = $title;
			App::getOrm()->persist($cats[$id]);
		}

		App::getOrm()->flush();

		App::getContainer()->getSystemService('publish_structure_cache')->flush();
		App::getOrm()->commit();

		return $cats;
	}

	public static function update($type, $category_id, $title, array $usergroup_ids)
	{
		$entity = self::getEntityNameFor($type);
		$perm_table = App::getEntityRepository($entity)->getPermissionTableName();
		$cat = App::getOrm()->find($entity, $category_id);

		if (!$cat) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$usergroup_ids = Arrays::castToType($usergroup_ids, 'integer');
		$usergroup_ids = Arrays::removeFalsey($usergroup_ids);
		$usergroup_ids = array_unique($usergroup_ids);

		App::getOrm()->beginTransaction();

		try {
			$cat->title = $title;

			if ($perm_table) {
				App::getDb()->delete($perm_table, array('category_id' => $cat->id));

				foreach ($usergroup_ids as $uid) {
					App::getDb()->insert($perm_table, array(
						'category_id' => $cat->id,
						'usergroup_id' => $uid
					));
				}
			}

			App::getOrm()->persist($cat);
			App::getOrm()->flush();
			App::getOrm()->commit();

			App::getContainer()->getSystemService('publish_structure_cache')->flush();
			App::getDb()->query("DELETE FROM permissions_cache");

		} catch (\Exception $e) {
			App::getOrm()->rollback();
			throw $e;
		}
	}


	/**
	 * Update orders. $orders is an array of ID's in the order you want them.
	 *
	 * @param $type
	 * @param array $orders
	 * @return void
	 */
	public static function updateOrders($type, array $orders)
	{
		$entity = self::getEntityNameFor($type);

		$ids = array_values($orders);
		$ids = array_unique($ids);
		$ids = Arrays::castToType($ids, 'integer');

		if (!$ids) {
			return;
		}

		$cats = App::getOrm()->createQuery("
			SELECT c
			FROM $entity c INDEX BY c.id
			WHERE c.id IN (" . implode(',', $ids) . ")
		")->execute();

		App::getDb()->beginTransaction();

		try {
			foreach ($ids as $order => $id) {
				if (!isset($cats[$id])) {
					continue;
				}

				$cats[$id]['display_order'] = ($order+1) * 10; // 10,20,30, etc
				App::getOrm()->persist($cats[$id]);
			}

			App::getOrm()->flush();

			App::getOrm()->getRepository($entity)->repair();

			App::getContainer()->getSystemService('publish_structure_cache')->flush();
			App::getOrm()->flush();
			App::getDb()->commit();
		} catch (\Exception $e) {
			App::getDb()->rollback();
			throw $e;
		}
	}

	/**
	 * Update the structure based off a map of ids to categories
	 *
	 * @param $type
	 * @param array $map
	 * @return array
	 */
	public static function updateStructure($type, array $map, array $check_map = null)
	{
		$entity = self::getEntityNameFor($type);

		$cats = App::getOrm()->createQuery("
			SELECT c
			FROM $entity c INDEX BY c.id
		")->execute();

		// If theres a check map then we want to verify that the current tree is the same,
		// or else error out
		if ($check_map) {
			$table = App::getOrm()->getRepository($entity)->getTableName();
			$current_tree = App::getDb()->fetchAllKeyValue("SELECT id, parent_id FROM $table");

			$accurate = true;
			foreach ($check_map as $id => $parent_id) {
				if (array_key_exists($parent_id, $current_tree)) {
					$current_parent_id = isset($current_tree[$id]) ? $current_tree[$id] : null;
					if ($current_parent_id === null) {
						$current_parent_id = 0;
					}

					if ($current_parent_id != $parent_id) {
						$accurate = false;
						break;
					}
				}
			}

			if (!$accurate) {
				throw new \OutOfBoundsException("Structure check failed");
			}
		}

		App::getOrm()->beginTransaction();

		foreach ($map as $id => $parent_id) {
			if (!isset($cats[$id])) {
				continue;
			}

			if (!$parent_id) {
				$cats[$id]['parent'] = null;
			} else {
				if (!isset($cats[$parent_id])) {
					continue;
				}
				$cats[$id]['parent'] = $cats[$parent_id];
			}

			App::getOrm()->persist($cats[$id]);
		}

		App::getOrm()->getRepository($entity)->repair();

		App::getOrm()->flush();

		App::getContainer()->getSystemService('publish_structure_cache')->flush();
		App::getOrm()->commit();

		return $cats;
	}


	/**
	 * Deletes a category and all its children if they are empty.
	 *
	 * @throws \InvalidArgumentException
	 * @param $type
	 * @param $category_id
	 * @return void
	 */
	public static function deleteCategory($type, $category_id)
	{
		$entity = self::getEntityNameFor($type);
		$repos  = App::getOrm()->getRepository($entity);
		$cat = $repos->find($category_id);

		if (!$cat) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$counts = App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM article_to_categories
			WHERE category_id = ?
			LIMIT 1
		", array($category_id));

		if (count($cat->children) || $counts) {
			throw new \OutOfBoundsException("Category is not empty");
		}

		App::getOrm()->beginTransaction();

		$fn = function($delcat) use (&$fn) {
			if ($delcat->children) {
				foreach ($delcat->children as $subcat) {
					$fn($subcat);
				}
			}

			App::getOrm()->remove($delcat);
		};

		$fn($cat);

		App::getOrm()->flush();

		App::getOrm()->getRepository($entity)->repair();
		App::getContainer()->getSystemService('publish_structure_cache')->flush();
		App::getOrm()->commit();

		return $cat;
	}


	/**
	 * Get the content entity for a publish type
	 *
	 * @static
	 * @throws \InvalidArgumentException
	 * @param $type
	 * @return string
	 */
	public static function getEntityNameFor($type)
	{
		return AgentHelper::getCatEntityNameFor($type);
	}
}
