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

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person as PersonEntity;

use Orb\Util\Arrays;
use Orb\Util\Strings;

class Article extends AbstractEntityRepository
{
	public function getBySlug($slug)
	{
		$id = Strings::extractRegexMatch('#^([0-9]+)#', $slug, 1);
		if (!$id) return null;

		return $this->find($id);
	}


	/**
	 * Get articles waiting for validating
	 *
	 * @return array
	 */
	public function getValidatingArticle()
	{
		$articles = $this->getEntityManager()->createQuery("
			SELECT a
			FROM DeskPRO:Article a
			WHERE a.hidden_status = ?1
			ORDER BY a.id DESC
		")->setParameter(1, 'validating')->execute();

		return $articles;
	}


	/**
	 * Get drafts, optionally for a specific person
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return array
	 */
	public function getDraftArticles(PersonEntity $person = null)
	{
		if ($person) {
			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a
				WHERE a.hidden_status = ?1 AND a.person = ?2
				ORDER BY a.id DESC
			")->setParameter(1, 'draft')
			  ->setParameter(2, $person)
			  ->execute();
		} else {
			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a
				WHERE a.hidden_status = ?1
				ORDER BY a.id DESC
			")->setParameter(1, 'draft')->execute();
		}

		return $articles;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person|null $person
	 * @return int
	 */
	public function getDraftArticlesCount(PersonEntity $person = null)
	{
		if ($person) {
			return App::getDb()->fetchColumn("
				SELECT COUNT(*)
				FROM articles
				WHERE hidden_status = ? AND person_id = ?
			", array('draft', $person['id']));
		} else {
			return App::getDb()->fetchColumn("
				SELECT COUNT(*)
				FROM articles
				WHERE hidden_status = ?
			", array('draft'));
		}
	}


	/**
	 * Get a collection of articles by ID. If $person_context
	 * is supplied, only articles that this person is able to view will be returned.
	 *
	 * @return array
	 */
	public function getByIdsWithContext(array $ids, PersonEntity $person_context = null)
	{
		if (!$ids) return array();

		if ($person_context) {

			$cat_ids = $person_context->getPermissionsManager()->ArticleCategories->getAllowedCategories();
			if (!$cat_ids) {
				return array();
			}

			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a INDEX BY a.id
				LEFT JOIN a.categories cat
				WHERE a.id IN (" . implode(',', $ids) . ") AND cat.id IN (?0) AND a.status = 'published'
				ORDER BY a.id DESC
			")->execute(array($cat_ids));

		} else {
			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a INDEX BY a.id
				LEFT JOIN a.categories cat
				WHERE a.id IN (" . implode(',', $ids) . ") AND a.status = 'published'
				ORDER BY a.id DESC
			")->execute();
		}

		return $articles;
	}


	public function getByResultIds(array $ids)
	{
		if (!$ids) return array();

		$unsorted_articles = $this->getEntityManager()->createQuery("
			SELECT a
			FROM DeskPRO:Article a INDEX BY a.id
			WHERE a.id IN (" . implode(',', $ids) . ")
			ORDER BY a.id DESC
		")->setFetchMode('DeskPRO:ArticleCategory', 'categories', 'EAGER')
		  ->execute();

		$articles = array();

		foreach ($ids as $id) {
			if (isset($unsorted_articles[$id])) {
				$articles[$id] = $unsorted_articles[$id];
			}
		}

		return $articles;
	}



	/**
	 * Given an array of nodes (usually roots), get the top $num newest articles, and then
	 * sort them into an array keyed by the node IDs.
	 *
	 * @param  $nodes
	 * @return array
	 */
	public function getNewestInNodes($nodes, $num = 5, PersonEntity $person_context = null)
	{
		// This needs to be cached since it's quite costly
		// to get the top results in each category with mysql. And
		// then we have to worry about dupes based on articles being in
		// multiple categories as well. So this is the cleanest way i think,
		// even though its inefficient. But who cares it should be cached.

		$all_articles = array();

		// Articles can be in multiple categories, but we should only be showing them once
		// So we need to not in() them using ID's we fetched in earlier iterations
		$done_articles = array(0);

		foreach ($nodes as $node) {

			$cat_ids = $node->getTreeIds(true);

			$params = array();
			$params['cat_ids'] = array_values($cat_ids);

			$perm_where = '';
			if ($person_context && !$person_context->is_agent) {
				$dis_ids = $person_context->PermissionsManager->ArticleCategories->getDisallowedCategories();
				if ($dis_ids) {
					$perm_where = ' AND cat.id NOT IN (:cat_not_ids) ';
					$params['cat_not_ids'] = array_values($dis_ids);
				}
			}

			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a INDEX BY a.id
				LEFT JOIN a.categories cat
				WHERE
					cat.id IN (:cat_ids)
					AND a.status = 'published'
					$perm_where
				GROUP BY a.id
				ORDER BY a.id DESC
			")->setMaxResults($num)->execute($params);

			if (count($articles)) {
				$all_articles[$node['id']] = $articles;
				$done_articles = array_merge($done_articles, array_keys($articles));
			}
		}

		return $all_articles;
	}



	public function getNewest($num = 10, $node = false)
	{
		if ($node) {
			$cat_ids = $node->getTreeIds(true);
			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a INDEX BY a.id
				LEFT JOIN a.categories cat
				WHERE a.status = 'published' AND cat.id IN (" . implode(',',$cat_ids) . ")
				ORDER BY a.id DESC
			")->setMaxResults($num)->execute();
		} else {
			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a INDEX BY a.id
				WHERE a.status = 'published'
				ORDER BY a.id DESC
			")->setMaxResults($num)->execute();
		}

		return $articles;
	}


	public function getTopRated($num = 10, $node = false)
	{
		if ($node) {
			$cat_ids = $node->getTreeIds(true);
			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a INDEX BY a.id
				LEFT JOIN a.categories cat
				WHERE a.status = 'published' AND cat.id IN (" . implode(',',$cat_ids) . ")
				ORDER BY a.total_rating DESC
			")->setMaxResults($num)->execute();
		} else {
			$articles = $this->getEntityManager()->createQuery("
				SELECT a
				FROM DeskPRO:Article a INDEX BY a.id
				WHERE a.status = 'published'
				ORDER BY a.total_rating DESC
			")->setMaxResults($num)->execute();
		}

		return $articles;
	}



	public function getInNode($node)
	{
		return $this->getEntityManager()->createQuery("
			SELECT a
			FROM DeskPRO:Article a
			LEFT JOIN a.categories c
			WHERE c = ?1
			ORDER BY a.id DESC
		")->setParameter(1, $node)->execute();
	}


	public function getSectionCounts(PersonEntity $person_context = null)
	{
		$counts = array();

		$searcher = new \Application\DeskPRO\Searcher\ArticleSearch();
		if ($person_context) {
			$searcher->setPersonContext($person_context);
		}
		$searcher->addTerm('status', 'is', 'published');
		$searcher->addTerm('popular', 'is', '1');
		$counts['popular'] = $searcher->getCount();

		$searcher = new \Application\DeskPRO\Searcher\ArticleSearch();
		if ($person_context) {
			$searcher->setPersonContext($person_context);
		}
		$searcher->addTerm('status', 'is', 'published');
		$searcher->addTerm('new', 'is', '1');
		$counts['new'] = $searcher->getCount();

		return $counts;
	}

	public function getReportAssociations()
	{
		return array(
			'views' => array(
				'conditions' => '%1$s.object_type = 1 AND %1$s.object_id = %2$s.id',
				'targetEntity' => 'Application\\DeskPRO\\Entity\\PageViewLog'
			),
			'ratings' => array(
				'conditions' => '%1$s.object_type = \'article\' AND %1$s.object_id = %2$s.id',
				'targetEntity' => 'Application\\DeskPRO\\Entity\\Rating'
			)
		);
	}
}
