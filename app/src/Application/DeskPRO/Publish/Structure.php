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
 * @subpackage Publish
 */

namespace Application\DeskPRO\Publish;

use	Doctrine\ORM\EntityManager;
use Orb\Doctrine\Common\Cache\PreloadedMysqlCache;
use Orb\Util\Arrays;

use Application\DeskPRO\Searcher\ArticleSearch;
use Application\DeskPRO\Searcher\FeedbackSearch;
use Application\DeskPRO\Searcher\DownloadSearch;
use Application\DeskPRO\Searcher\NewsSearch;

use Application\DeskPRO\People\PersonContextInterface;

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Article;
use Application\DeskPRO\Entity\ArticleCategory;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\FeedbackCategory;
use Application\DeskPRO\Entity\Download;
use Application\DeskPRO\Entity\DownloadCategory;
use Application\DeskPRO\Entity\News;
use Application\DeskPRO\Entity\NewsCategory;

class Structure implements PersonContextInterface
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Orb\Doctrine\Common\Cache\PreloadedMysqlCache
	 */
	protected $cache = null;

	/**
	 * @var array
	 */
	protected $category_data = array();

	/**
	 * Category data processed in the context of $person_context
	 *
	 * @var array
	 */
	protected $context_category_data = array();

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(Person $person_context, EntityManager $em, \Doctrine\Common\Cache\Cache $cache)
	{
		$this->person_context = $person_context;

		$this->em = $em;
		$this->db = $em->getConnection();
		$this->cache = $cache;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person $person_context
	 */
	public function setPersonContext(Person $person_context)
	{
		if ($this->person_context == $person_context) {
			return;
		}
		if ($this->person_context && $person_context && $this->person_context->id == $person_context->id) {
			return;
		}

		$this->person_context = $person_context;
		$this->context_category_data = array();
	}


	####################################################################################################################
	# Article Fetchers
	####################################################################################################################

	/**
	 * Get all categories in proper displaying order. You can also determine hierarchy by 'depth'.
	 *
	 * @return array
	 */
	public function getArticleCategories()
	{
		$ent = 'DeskPRO:ArticleCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['all'];
	}


	/**
	 * @return mixed
	 */
	public function getArticleRootCategories()
	{
		$ent = 'DeskPRO:ArticleCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['hierarchy'];
	}


	/**
	 * Get an array of all category IDs
	 *
	 * @return array
	 */
	public function getArticleCategoryIds()
	{
		$ent = 'DeskPRO:ArticleCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['ids'];
	}


	/**
	 * @param $slug
	 * @return
	 */
	public function getArticleCategory($id)
	{
		$ent = 'DeskPRO:ArticleCategory';
		$this->loadCategories($ent);

		if (!isset($this->context_category_data[$ent]['all'][$id])) {
			throw new \InvalidArgumentException("Invalid category id `$id`");
		}

		return $this->context_category_data[$ent]['all'][$id];
	}


	/**
	 * @param $id
	 * @return bool
	 */
	public function hasArticleCategory($id)
	{
		$ent = 'DeskPRO:ArticleCategory';
		$this->loadCategories($ent);

		return isset($this->context_category_data[$ent]['all'][$id]);
	}


	/**
	 * Get an array of id=>name
	 *
	 * @param string $sep
	 * @param bool $include_tops
	 * @return array
	 */
	public function getArticleCategoryNames($sep = ' > ', $include_tops = true)
	{
		$ent = 'DeskPRO:ArticleCategory';
		$this->loadCategories($ent);
		return $this->_getFullNames(array(), $this->context_category_data[$ent]['hierarchy'], $sep, $include_tops);
	}


	/**
	 * @return \Orb\Util\HierarchyStructure
	 */
	public function getArticleCategoryHelper()
	{
		$ent = 'DeskPRO:ArticleCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['helper'];
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person|null $person_context
	 * @return array
	 */
	public function getArticleCategoryCounts(Person $person_context = null)
	{
		$ent = 'DeskPRO:ArticleCategory';
		$id = 'categories.counts.' . $ent;
		$this->loadCategories($ent);

		if ($counts = $this->cache->fetch($id)) {
			return $counts;
		}

		$counts = array('0' => 0, '0_total' => 0);

		foreach ($this->context_category_data[$ent]['ids'] as $cid) {
			$searcher = new ArticleSearch();
			$searcher->setPersonContext($person_context);
			$searcher->addTerm(ArticleSearch::TERM_CATEGORY_SPECIFIC, 'is', $cid);
			$searcher->addTerm(ArticleSearch::TERM_STATUS, 'is', 'published');

			$counts[$cid] = $searcher->getCount();
		}

		$counts = $this->_getTotalCounts($counts, $this->context_category_data[$ent]['all'], $this->context_category_data[$ent]['helper']);

		$this->cache->save($id, $counts, time() + 3600);

		return $counts;
	}

	####################################################################################################################
	# Feedback Fetchers
	####################################################################################################################

	/**
	 * Get all categories in proper displaying order. You can also determine hierarchy by 'depth'.
	 *
	 * @return array
	 */
	public function getFeedbackCategories()
	{
		$ent = 'DeskPRO:FeedbackCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['all'];
	}


	/**
	 * @return mixed
	 */
	public function getFeedbackRootCategories()
	{
		$ent = 'DeskPRO:FeedbackCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['hierarchy'];
	}


	/**
	 * Get an array of all category IDs
	 *
	 * @return array
	 */
	public function getFeedbackCategoryIds()
	{
		$ent = 'DeskPRO:FeedbackCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['ids'];
	}


	/**
	 * @param $slug
	 * @return
	 */
	public function getFeedbackCategory($id)
	{
		$ent = 'DeskPRO:FeedbackCategory';
		$this->loadCategories($ent);

		if (!isset($this->context_category_data[$ent]['all'][$id])) {
			throw new \InvalidArgumentException("Invalid category id `$id`");
		}

		return $this->context_category_data[$ent]['all'][$id];
	}


	/**
	 * @param $id
	 * @return bool
	 */
	public function hasFeedbackCategory($id)
	{
		$ent = 'DeskPRO:FeedbackCategory';
		$this->loadCategories($ent);

		return isset($this->context_category_data[$ent]['all'][$id]);
	}


	/**
	 * Get an array of id=>name
	 *
	 * @param string $sep
	 * @param bool $include_tops
	 * @return array
	 */
	public function getFeedbackCategoryNames($sep = ' > ', $include_tops = true)
	{
		$ent = 'DeskPRO:FeedbackCategory';
		$this->loadCategories($ent);
		return $this->_getFullNames(array(), $this->context_category_data[$ent]['hierarchy'], $sep, $include_tops);
	}


	/**
	 * @return \Orb\Util\HierarchyStructure
	 */
	public function getFeedbackCategoryHelper()
	{
		$ent = 'DeskPRO:FeedbackCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['helper'];
	}


	public function getFeedbackStatusCounts($category = null, Person $person_context = null)
	{
		$ent = 'DeskPRO:FeedbackStatusCategory';

		if ($category) {
			$id = 'status.counts.' . $ent . '.' . $category->id .  '.' . $person_context->getUsergroupSetKey();
		} else {
			$id = 'status.counts.' . $ent . '.' . $person_context->getUsergroupSetKey();
		}

		//if ($counts = $this->cache->fetch($id)) {
		//	return $counts;
		//}

		$in_cats = '';
		if ($category) {
			$in_cats = implode(',', $category->getTreeIds(true));
		}

		if ($category) {
			$counts = $this->db->fetchAllKeyValue("
				SELECT status, COUNT(*)
				FROM feedback
				WHERE category_id IN ($in_cats) AND hidden_status IS NULL
				GROUP BY status
			");
		} else {
			$counts = $this->db->fetchAllKeyValue("
				SELECT status, COUNT(*)
				FROM feedback
				WHERE hidden_status IS NULL
				GROUP BY status
			");
		}

		$counts['all'] = array_sum($counts);

		if ($category) {
			$counts_status_cats = $this->db->fetchAllKeyValue("
				SELECT status_category_id, COUNT(*)
				FROM feedback
				WHERE status_category_id IS NOT NULL AND category_id IN ($in_cats)
				GROUP BY status_category_id
			");
		} else {
			$counts_status_cats = $this->db->fetchAllKeyValue("
				SELECT status_category_id, COUNT(*)
				FROM feedback
				WHERE status_category_id IS NOT NULL
				GROUP BY status_category_id
			");
		}

		foreach ($counts_status_cats as $id => $c) {
			$counts[$id] = $c;
		}

		$counts['open'] = (isset($counts['new']) ? $counts['new'] : 0) + (isset($counts['active']) ? $counts['active'] : 0);

		$this->cache->save($id, $counts, time() + 3600);

		return $counts;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person|null $person_context
	 * @return array
	 */
	public function getFeedbackCategoryCounts(Person $person_context = null)
	{
		$ent = 'DeskPRO:FeedbackCategory';
		$id = 'categories.counts.' . $ent . '.' . $person_context->getUsergroupSetKey();
		$this->loadCategories($ent);

		if ($counts = $this->cache->fetch($id)) {
			return $counts;
		}

		$counts = array(0 => array('popular' => 0, 'new' => 0, 'active' => 0, 'closed' => 0));
		foreach ($this->getFeedbackCategories() as $c) {

			$cat_counts = array();

			$searcher = new FeedbackSearch();
			$searcher->setPersonContext($person_context);
			$searcher->addTerm(FeedbackSearch::TERM_CATEGORY, 'is', $c['id']);
			$searcher->addTerm(FeedbackSearch::TERM_STATUS, 'is', Feedback::STATUS_NEW);
			$cat_counts['new'] = $searcher->getCount();

			$searcher = new FeedbackSearch();
			$searcher->setPersonContext($person_context);
			$searcher->addTerm(FeedbackSearch::TERM_CATEGORY, 'is', $c['id']);
			$searcher->addTerm(FeedbackSearch::TERM_STATUS, 'is', Feedback::STATUS_ACTIVE);
			$cat_counts['active'] = $searcher->getCount();

			$searcher = new FeedbackSearch();
			$searcher->setPersonContext($person_context);
			$searcher->addTerm(FeedbackSearch::TERM_CATEGORY, 'is', $c['id']);
			$searcher->addTerm(FeedbackSearch::TERM_STATUS, 'is', Feedback::STATUS_CLOSED);
			$cat_counts['closed'] = $searcher->getCount();

			$cat_counts['all'] = array_sum($cat_counts);


			$counts[$c['id']] = $cat_counts;

			// 0 is sum of all root nodes
			if (!$c['depth']) {
				$counts[0]['new']     += $counts[$c['id']]['new'];
				$counts[0]['active']  += $counts[$c['id']]['active'];
				$counts[0]['closed']  += $counts[$c['id']]['closed'];
			}
		}

		$counts[0]['all'] = array_sum($counts[0]);

		$this->cache->save($id, $counts, time() + 3600);

		return $counts;
	}


	####################################################################################################################
	# Download Fetchers
	####################################################################################################################

	/**
	 * Get all categories in proper displaying order. You can also determine hierarchy by 'depth'.
	 *
	 * @return array
	 */
	public function getDownloadCategories()
	{
		$ent = 'DeskPRO:DownloadCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['all'];
	}


	/**
	 * @return mixed
	 */
	public function getDownloadRootCategories()
	{
		$ent = 'DeskPRO:DownloadCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['hierarchy'];
	}


	/**
	 * Get an array of all category IDs
	 *
	 * @return array
	 */
	public function getDownloadCategoryIds()
	{
		$ent = 'DeskPRO:DownloadCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['ids'];
	}


	/**
	 * @param $slug
	 * @return
	 */
	public function getDownloadCategory($id)
	{
		$ent = 'DeskPRO:DownloadCategory';
		$this->loadCategories($ent);

		if (!isset($this->context_category_data[$ent]['all'][$id])) {
			throw new \InvalidArgumentException("Invalid category id `$id`");
		}

		return $this->context_category_data[$ent]['all'][$id];
	}


	/**
	 * @param $id
	 * @return bool
	 */
	public function hasDownloadCategory($id)
	{
		$ent = 'DeskPRO:DownloadCategory';
		$this->loadCategories($ent);

		return isset($this->context_category_data[$ent]['all'][$id]);
	}


	/**
	 * Get an array of id=>name
	 *
	 * @param string $sep
	 * @param bool $include_tops
	 * @return array
	 */
	public function getDownloadCategoryNames($sep = ' > ', $include_tops = true)
	{
		$ent = 'DeskPRO:DownloadCategory';
		$this->loadCategories($ent);
		return $this->_getFullNames(array(), $this->context_category_data[$ent]['hierarchy'], $sep, $include_tops);
	}


	/**
	 * @return \Orb\Util\HierarchyStructure
	 */
	public function getDownloadCategoryHelper()
	{
		$ent = 'DeskPRO:DownloadCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['helper'];
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person|null $person_context
	 * @return array
	 */
	public function getDownloadCategoryCounts(Person $person_context = null)
	{
		$ent = 'DeskPRO:DownloadCategory';
		$id = 'categories.counts.' . $ent . '.' . $person_context->getUsergroupSetKey();
		$this->loadCategories($ent);

		if ($counts = $this->cache->fetch($id)) {
			return $counts;
		}

		$counts = array('0' => 0, '0_total' => 0);

		foreach ($this->getDownloadCategories() as $cat) {
			$searcher = new DownloadSearch();
			$searcher->setPersonContext($person_context);
			$searcher->addTerm(DownloadSearch::TERM_CATEGORY_SPECIFIC, 'is', $cat->id);
			$searcher->addTerm(DownloadSearch::TERM_STATUS, 'is', 'published');

			$counts[$cat->id] = $searcher->getCount();
		}

		$counts = $this->_getTotalCounts($counts, $this->context_category_data[$ent]['all'], $this->context_category_data[$ent]['helper']);

		$this->cache->save($id, $counts, time() + 3600);

		return $counts;
	}


	####################################################################################################################
	# News Fetchers
	####################################################################################################################

	/**
	 * Get all categories in proper displaying order. You can also determine hierarchy by 'depth'.
	 *
	 * @return array
	 */
	public function getNewsCategories()
	{
		$ent = 'DeskPRO:NewsCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['all'];
	}


	/**
	 * @return mixed
	 */
	public function getNewsRootCategories()
	{
		$ent = 'DeskPRO:NewsCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['hierarchy'];
	}


	/**
	 * Get an array of all category IDs
	 *
	 * @return array
	 */
	public function getNewsCategoryIds()
	{
		$ent = 'DeskPRO:NewsCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['ids'];
	}


	/**
	 * @param $slug
	 * @return
	 */
	public function getNewsCategory($id)
	{
		$ent = 'DeskPRO:NewsCategory';
		$this->loadCategories($ent);

		if (!isset($this->context_category_data[$ent]['all'][$id])) {
			throw new \InvalidArgumentException("Invalid category id `$id`");
		}

		return $this->context_category_data[$ent]['all'][$id];
	}


	/**
	 * @param $id
	 * @return bool
	 */
	public function hasNewsCategory($id)
	{
		$ent = 'DeskPRO:NewsCategory';
		$this->loadCategories($ent);

		return isset($this->context_category_data[$ent]['all'][$id]);
	}


	/**
	 * Get an array of id=>name
	 *
	 * @param string $sep
	 * @param bool $include_tops
	 * @return array
	 */
	public function getNewsCategoryNames($sep = ' > ', $include_tops = true)
	{
		$ent = 'DeskPRO:NewsCategory';
		$this->loadCategories($ent);
		return $this->_getFullNames(array(), $this->context_category_data[$ent]['hierarchy'], $sep, $include_tops);
	}


	/**
	 * @return \Orb\Util\HierarchyStructure
	 */
	public function getNewsCategoryHelper()
	{
		$ent = 'DeskPRO:NewsCategory';
		$this->loadCategories($ent);
		return $this->context_category_data[$ent]['helper'];
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person|null $person_context
	 * @return array
	 */
	public function getNewsCategoryCounts(Person $person_context = null)
	{
		$ent = 'DeskPRO:NewsCategory';
		$id = 'categories.counts.' . $ent . '.' . $person_context->getUsergroupSetKey();
		$this->loadCategories($ent);

		if ($counts = $this->cache->fetch($id)) {
			return $counts;
		}

		$counts = array('0' => 0, '0_total' => 0);

		foreach ($this->getNewsCategories() as $c) {
			$searcher = new NewsSearch();
			$searcher->setPersonContext($person_context);
			$searcher->addTerm(NewsSearch::TERM_CATEGORY_SPECIFIC, 'is', $c['id']);
			$searcher->addTerm(NewsSearch::TERM_STATUS, 'is', 'published');

			$counts[$c['id']] = $searcher->getCount();
		}

		$counts = $this->_getTotalCounts($counts, $this->getNewsCategories(), $this->getNewsCategoryHelper());

		$this->cache->save($id, $counts, time() + 3600);

		return $counts;
	}


	####################################################################################################################
	# Helpers
	####################################################################################################################

	public function getCategoryHelperForCategory($obj)
	{
		if ($obj instanceof ArticleCategory) {
			return $this->getArticleCategoryHelper();
		} elseif ($obj instanceof FeedbackCategory) {
			return $this->getFeedbackCategoryHelper();
		} elseif ($obj instanceof DownloadCategory) {
			return $this->getDownloadCategoryHelper();
		} elseif ($obj instanceof NewsCategory) {
			return $this->getNewsCategoryHelper();
		}

		return null;
	}

	/**
	 * Helper used when generating a full names list
	 *
	 * @param $basenames
	 * @param $cats
	 * @param $sep
	 * @param $include_tops
	 * @return array
	 */
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

	protected function _getTotalCounts(array $counts, $cats, \Orb\Util\HierarchyStructure $h, array &$called_on = null)
	{
		$flat = $h->getFlatHierarchy();
		$highest = 0;
		foreach ($flat as $c) {
			$total_key = $c['id'] . '_total';
			if (!isset($counts[$total_key])) {
				$counts[$total_key] = $counts[$c['id']];
			}
			if ($c['depth'] > $highest) {
				$highest = $c['depth'];
			}
		}

		while ($highest >= 0) {
			foreach ($flat as $c) {
				$total_key = $c['id'] . '_total';
				if ($c['depth'] == $highest) {
					foreach ($h->getChildrenIds($c, true) as $subcatid) {
						$counts[$total_key] += isset($counts["{$subcatid}_total"]) ? $counts["{$subcatid}_total"] : 0;
					}
				}
			}

			$highest--;
		}

		$counts['0'] = 0;
		$counts['0_total'] = 0;

		foreach ($cats as $c) {
			$k = $c['id'] . '_total';
			if ($c['parent_id']) {
				continue;
			}
			$counts['0_total'] += isset($counts[$k]) ? $counts[$k] : 0;
		}

		return $counts;
	}



	/**
	 * Loads category data from the database
	 *
	 * @param $ent
	 * @return mixed
	 */
	protected function loadCategories($ent)
	{
		if (isset($this->category_data[$ent]) && isset($this->context_category_data[$ent])) {
			return;
		}

		#------------------------------
		# Category data: This is un-permissioned data
		#------------------------------

		if ($this->cache instanceof PreloadedMysqlCache) {
			$this->cache->preloadPrefix('categories');
		}

		$cats = $this->em->createQuery("
			SELECT cat
			FROM $ent cat INDEX BY cat.id
			ORDER BY cat.display_order ASC
		")->setResultCacheDriver($this->cache)->setResultCacheId('categories.recs.'.$ent)
		  ->execute();

		foreach ($cats as $c) {
			$c->structure_helper = $this;
		}

		$this->category_data[$ent] = array();
		$this->category_data[$ent]['all'] = $cats;
		$this->category_data[$ent]['ids'] = array_keys($cats);

		$maps = $this->cache->fetch('categories.maps.' . $ent);
		if (!$maps) {
			$parent_map = $this->em->getConnection()->fetchAll("
				SELECT id, COALESCE(parent_id, 0) AS parent_id
				FROM " . $this->em->getRepository($ent)->getTableName() . "
				ORDER BY display_order ASC
			");
			$parent_map = Arrays::keyFromData($parent_map, 'id', 'parent_id');

			$child_map = array(0 => array());
			foreach ($parent_map as $parent_id => $child_id) {
				if ($parent_id == 0) {
					$child_map[0][] = $child_id;
				}
			}

			foreach ($parent_map as $child_id => $parent_id) {
				if (!isset($child_map[$parent_id])) {
					$child_map[$parent_id] = array();
				}
				$child_map[$parent_id][] = $child_id;
			}

			$maps = array('parent_map' => $parent_map, 'child_map' => $child_map);
			$this->cache->save('categories.maps.' . $ent, $maps);
		}

		$this->category_data[$ent]['parent_map'] = $parent_map = $maps['parent_map'];
		$this->category_data[$ent]['child_map'] =  $child_map  = $maps['child_map'];

		// Getting hierarchy is easy because they already have parent/children,
		// hierarchy then is simply getting the root nodes from our collection
		$this->category_data[$ent]['hierarchy'] = array();

		foreach ($child_map[0] as $cat_id) {
			$this->category_data[$ent]['hierarchy'][$cat_id] = $cats[$cat_id];
		}

		$h = new \Orb\Util\HierarchyStructure($cats);
		$h->parent_map = $this->category_data[$ent]['parent_map'];
		$h->child_map = $this->category_data[$ent]['child_map'];
		$this->category_data[$ent]['helper'] = $h;

		#------------------------------
		# Categories viewable by the user
		#------------------------------

		// No valid context means all categories
		// Agents dont have permissions on publish categories
		if (!$this->person_context || ($this->person_context->is_agent && DP_INTERFACE == 'agent')) {
			$this->context_category_data[$ent] = $this->category_data[$ent];
			return;
		}

		foreach ($cats as $c) {
			$c->structure_helper = null;
		}

		$perm_manager = null;
		switch ($ent) {
			case 'DeskPRO:ArticleCategory':  $perm_manager = $this->person_context->PermissionsManager->get('ArticleCategories');   break;
			case 'DeskPRO:DownloadCategory': $perm_manager = $this->person_context->PermissionsManager->get('DownloadCategories');  break;
			case 'DeskPRO:NewsCategory':     $perm_manager = $this->person_context->PermissionsManager->get('NewsCategories');      break;
			case 'DeskPRO:FeedbackCategory': $perm_manager = $this->person_context->PermissionsManager->get('FeedbackCategories');  break;
		}

		// They're allowed to see it all
		if (!$perm_manager || !$perm_manager->getDisallowedCategories()) {
			$this->context_category_data[$ent] = $this->category_data[$ent];
			return;
		}

		$this->context_category_data[$ent] = array();
		$this->context_category_data[$ent]['all'] = array();
		foreach ($perm_manager->getAllowedCategories() as $id) {
			if (isset($this->category_data[$ent]['all'][$id])) {
				$this->context_category_data[$ent]['all'][$id] = $this->category_data[$ent]['all'][$id];
			}
		}
		$this->context_category_data[$ent]['ids'] = array_keys($this->context_category_data[$ent]['all']);

		// Getting hierarchy is easy because they already have parent/children,
		// hierarchy then is simply getting the root nodes from our collection
		$this->context_category_data[$ent]['hierarchy'] = array();

		foreach ($child_map[0] as $cat_id) {
			if ($perm_manager->isCategoryAllowed($cat_id)) {
				$this->context_category_data[$ent]['hierarchy'][$cat_id] = $cats[$cat_id];
			}
		}

		$h = new \Orb\Util\HierarchyStructure($this->context_category_data[$ent]['all']);
		$h->parent_map = $this->category_data[$ent]['parent_map'];
		$h->child_map = $this->category_data[$ent]['child_map'];
		$this->context_category_data[$ent]['helper'] = $h;

		foreach ($cats as $c) {
			$c->structure_helper = $this;
		}
	}
}
