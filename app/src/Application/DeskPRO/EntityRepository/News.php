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

class News extends AbstractEntityRepository
{
	public function getBySlug($slug)
	{
		$id = Strings::extractRegexMatch('#^([0-9]+)#', $slug, 1);
		if (!$id) return null;

		return $this->find($id);
	}

	/**
	 * Get a collection of posts by ID. If $person_context
	 * is supplied, only articles that this person is able to view will be returned.
	 *
	 * @return array
	 */
	public function getByIdsWithContext(array $ids, PersonEntity $person_context = null)
	{
		if (!$ids) return array();

		if ($person_context) {

			$cat_ids = $person_context->getPermissionsManager()->NewsCategories->getAllowedCategories();
			if (!$cat_ids) {
				return array();
			}

			$posts = $this->getEntityManager()->createQuery("
				SELECT p
				FROM DeskPRO:News p INDEX BY p.id
				WHERE p.id IN (" . implode(',', $ids) . ") AND p.category IN (?0) AND p.status = 'published'
				ORDER BY p.id DESC
			")->execute(array($cat_ids));
		} else {
			$posts = $this->getEntityManager()->createQuery("
				SELECT p
				FROM DeskPRO:News p INDEX BY p.id
				WHERE p.id IN (" . implode(',', $ids) . ") AND p.status = 'published'
				ORDER BY p.id DESC
			")->execute();
		}

		return $posts;
	}

	public function getByResultIds(array $ids)
	{
		if (!$ids) return array();

		$unsorted_news = $this->getEntityManager()->createQuery("
			SELECT n
			FROM DeskPRO:News n INDEX BY n.id
			WHERE n.id IN (" . implode(',', $ids) . ")
			ORDER BY n.id DESC
		")->execute();

		$news = array();

		foreach ($ids as $id) {
			if (isset($unsorted_news[$id])) {
				$news[$id] = $unsorted_news[$id];
			}
		}

		return $news;
	}

	public function getNews($node, $num = 20)
	{
		if ($node) {
			$news = $this->getEntityManager()->createQuery("
				SELECT n
				FROM DeskPRO:News n
				WHERE n.category = ?1
				ORDER BY n.id DESC
			")->setParameter(1, $node)->setMaxResults($num)->execute();
		} else {
			$news = $this->getEntityManager()->createQuery("
				SELECT n
				FROM DeskPRO:News n
				ORDER BY n.id DESC
			")->setMaxResults($num)->execute();
		}

		return $news;
	}


	public function getNewest($num = 10, $node = false)
	{
		if ($node) {
			$cat_ids = $node->getTreeIds(true);
			$articles = $this->getEntityManager()->createQuery("
				SELECT n
				FROM DeskPRO:News n INDEX BY n.id
				WHERE n.status = 'published' AND n.category IN (" . implode(',',$cat_ids) . ")
				ORDER BY n.id DESC
			")->setMaxResults($num)->execute();
		} else {
			$articles = $this->getEntityManager()->createQuery("
				SELECT n
				FROM DeskPRO:News n INDEX BY n.id
				WHERE n.status = 'published'
				ORDER BY n.id DESC
			")->setMaxResults($num)->execute();
		}

		return $articles;
	}

	public function getReportAssociations()
	{
		return array(
			'views' => array(
				'conditions' => '%1$s.object_type = 3 AND %1$s.object_id = %2$s.id',
				'targetEntity' => 'Application\\DeskPRO\\Entity\\PageViewLog'
			)
		);
	}
}
