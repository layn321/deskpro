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

class Download extends AbstractEntityRepository
{
	public function getBySlug($slug)
	{
		$id = Strings::extractRegexMatch('#^([0-9]+)#', $slug, 1);
		if (!$id) return null;

		return $this->find($id);
	}

	/**
	 * Get a collection of downloads by ID. If $person_context
	 * is supplied, only articles that this person is able to view will be returned.
	 *
	 * @return array
	 */
	public function getByIdsWithContext(array $ids, PersonEntity $person_context = null)
	{
		if (!$ids) return array();

		if ($person_context) {

			$cat_ids = $person_context->getPermissionsManager()->DownloadCategories->getAllowedCategories();
			if (!$cat_ids) {
				return array();
			}

			$downloads = $this->getEntityManager()->createQuery("
				SELECT d
				FROM DeskPRO:Download d INDEX BY d.id
				WHERE d.id IN (" . implode(',', $ids) . ") AND d.category IN (?0) AND d.status = 'published'
				ORDER BY d.id DESC
			")->execute(array($cat_ids));
		} else {
			$downloads = $this->getEntityManager()->createQuery("
				SELECT d
				FROM DeskPRO:Download d INDEX BY d.id
				WHERE d.id IN (" . implode(',', $ids) . ")
				ORDER BY d.id DESC
			")->execute();
		}

		return $downloads;
	}

	public function getByResultIds(array $ids)
	{
		if (!$ids) return array();

		$unsorted_downloads = $this->getEntityManager()->createQuery("
			SELECT d
			FROM DeskPRO:Download d INDEX BY d.id
			WHERE d.id IN (" . implode(',', $ids) . ")
			ORDER BY d.id DESC
		")->execute();

		$downloads = array();

		foreach ($ids as $id) {
			if (isset($unsorted_downloads[$id])) {
				$downloads[$id] = $unsorted_downloads[$id];
			}
		}

		return $downloads;
	}

	public function getNewest($num = 10, $node = false)
	{
		if ($node) {
			$downloads = $this->getEntityManager()->createQuery("
				SELECT d
				FROM DeskPRO:Download d
				WHERE d.category = ?1 AND d.status = 'published'
				ORDER BY d.id DESC
			")->setParameter(1, $node)->setMaxResults($num)->execute();
		} else {
			$downloads = $this->getEntityManager()->createQuery("
				SELECT d
				FROM DeskPRO:Download d
				WHERE d.status = 'published'
				ORDER BY d.id DESC
			")->setMaxResults($num)->execute();
		}

		return $downloads;
	}


	public function getPopular($num = 10, $node = false)
	{
		if ($node) {
			$downloads = $this->getEntityManager()->createQuery("
				SELECT d
				FROM DeskPRO:Download d
				WHERE d.category = ?1
				ORDER BY d.num_downloads DESC
			")->setParameter(1, $node)->setMaxResults($num)->execute();
		} else {
			$downloads = $this->getEntityManager()->createQuery("
				SELECT d
				FROM DeskPRO:Download d
				ORDER BY d.num_downloads DESC
			")->setMaxResults($num)->execute();
		}

		return $downloads;
	}



	public function getInNode($node)
	{
		return $this->getEntityManager()->createQuery("
			SELECT d
			FROM DeskPRO:Download d
			WHERE d.category = ?1
			ORDER BY d.title DESC
		")->setParameter(1, $node)->execute();
	}


	public function getSectionCounts(PersonEntity $person_context = null)
	{
		$counts = array();

		$searcher = new \Application\DeskPRO\Searcher\DownloadSearch();
		if ($person_context) {
			$searcher->setPersonContext($person_context);
		}
		$searcher->addTerm('status', 'is', 'published');
		$searcher->addTerm('popular', 'is', '1');
		$counts['popular'] = $searcher->getCount();

		$searcher = new \Application\DeskPRO\Searcher\DownloadSearch();
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
				'conditions' => '%1$s.object_type = 2 AND %1$s.object_id = %2$s.id',
				'targetEntity' => 'Application\\DeskPRO\\Entity\\PageViewLog'
			)
		);
	}
}
