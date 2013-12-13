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
use Orb\Util\Numbers;

class Feedback extends AbstractEntityRepository
{
	############################################################################
	# Counters
	############################################################################

	/**
	 * Count the number of feedback that are awaiting validation
	 *
	 * @return int
	 */
	public function countAwaitingValidation()
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM feedback
			WHERE hidden_status = 'validating'
		");
	}


	/**
	 * Count the number of feedback that are 'active', grouped by status category as key.
	 * The key 0 will be used as the total.
	 *
	 * @return array
	 */
	public function countActiveGrouped()
	{
		return App::getDb()->fetchAllKeyValue("
			SELECT IFNULL(status_category_id, 0), COUNT(*) as count
			FROM feedback
			WHERE status = 'active'
			GROUP BY status_category_id WITH ROLLUP
		");
	}

	/**
	 * Count the number of feedback that are 'active', grouped by status category as key.
	 * The key 0 will be used as the total.
	 *
	 * @return array
	 */
	public function countClosedGrouped()
	{
		return App::getDb()->fetchAllKeyValue("
			SELECT IFNULL(status_category_id, 0), COUNT(*) as count
			FROM feedback
			WHERE status = 'closed'
			GROUP BY status_category_id WITH ROLLUP
		");
	}


	/**
	 * Count the number of hidden feedback, groupbed by hidden_status as key.
	 * The key 'hidden' will be used as the total.
	 *
	 * @return array
	 */
	public function countHiddenGrouped()
	{
		// We dont count validating with this number because
		// in the UI we generally show validating separately

		return App::getDb()->fetchAllKeyValue("
			SELECT IFNULL(hidden_status, 'hidden'), COUNT(*) as count
			FROM feedback
			WHERE status = ? AND hidden_status != ? AND hidden_status != ?
			GROUP BY hidden_status WITH ROLLUP
		", array('hidden', 'validating', 'temp'));
	}


	/**
	 * Count the number of feedback that are new
	 *
	 * @return int
	 */
	public function countNew()
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM feedback
			WHERE status = 'new'
		");
	}

	/**
	 * Count the number of non-hidden feedback in all categories, grouped by category ID key.
	 * Each parent category has the sum of all children.
	 *
	 * @return array
	 */
	public function countAllCategoriesGrouped()
	{
		/*
		 * Note that the order by category_id ASC is important here.
		 * The tally loop after modifies the array as we go. We cant
		 * have a parents tally using a childs tally that was already incremented,
		 * that'd result in incorrect tallies.
		 * (Could just make a 2nd new array using 1st as a lookup, but this solution is easy enough)
		 */

		$counts = App::getDb()->fetchAllKeyValue("
			SELECT category_id, COUNT(*)
			FROM feedback
			WHERE status != 'hidden'
			GROUP BY category_id
			ORDER BY category_id ASC
		");

		foreach ($counts as $cat_id => &$count) {
			$cat_childs = App::getEntityRepository('DeskPRO:FeedbackCategory')->getIdsInTree($cat_id, false);
			if ($cat_childs) {
				foreach ($cat_childs as $child_cat_id) {
					if (isset($counts[$child_cat_id])) {
						$count += $counts[$child_cat_id];
					}
				}
			}
		}

		return $counts;
	}


	/**
	 * Count the number of feedback in a status category
	 *
	 * @param $category
	 * @return int
	 */
	public function countInCategory($category)
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM feedback
			WHERE category_id = ?
		", array($category->id));
	}


	/**
	 * Count the number of feedback in a status category
	 *
	 * @param $category
	 * @return int
	 */
	public function countInStatusCategory($category)
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM feedback
			WHERE status_category_id = ?
		", array($category->id));
	}


	############################################################################
	# Fetchers
	############################################################################

	public function getBySlug($slug)
	{
		$id = Strings::extractRegexMatch('#^([0-9]+)#', $slug, 1);
		if (!$id) return null;

		return $this->find($id);
	}

	/**
	 * Get a collection of feedback by ID. If $person_context
	 * is supplied, only articles that this person is able to view will be returned.
	 *
	 * @return array
	 */
	public function getByIdsWithContext(array $ids, PersonEntity $person_context = null)
	{
		if (!$ids) return array();

		if ($person_context) {
			$feedback = $this->getEntityManager()->createQuery("
				SELECT i
				FROM DeskPRO:Feedback i INDEX BY i.id
				WHERE i.id IN (" . implode(',', $ids) . ") AND i.status != 'hidden'
				ORDER BY i.id DESC
			")->execute();
		} else {
			$feedback = $this->getEntityManager()->createQuery("
				SELECT i
				FROM DeskPRO:Feedback i INDEX BY i.id
				WHERE i.id IN (" . implode(',', $ids) . ")
				ORDER BY i.id DESC
			")->execute();
		}

		return $feedback;
	}

	public function getByResultIds(array $ids)
	{
		if (!$ids) return array();

		$unsorted_feedback = $this->getEntityManager()->createQuery("
			SELECT i
			FROM DeskPRO:Feedback i INDEX BY i.id
			WHERE i.id IN (" . implode(',', $ids) . ")
			ORDER BY i.id DESC
		")->execute();

		$feedback = array();

		foreach ($ids as $id) {
			if (isset($unsorted_feedback[$id])) {
				$feedback[$id] = $unsorted_feedback[$id];
			}
		}

		return $feedback;
	}

	public function getFeedback($status, $node = false, $sort = 'id', $num = 10)
	{
		if ($sort == 'date') $sort = 'id';
		if (!in_array($sort, array('id', 'num_ratings'))) $sort = 'id';

		if ($node) {
			$node_ids = $node->getTreeIds(true);

			$feedback = $this->getEntityManager()->createQuery("
				SELECT i
				FROM DeskPRO:Feedback i
				WHERE i.category IN (".implode(',', $node_ids).") AND i.status = ?1
				ORDER BY i.$sort DESC
			")->setParameter(1, $status)->setMaxResults($num)->execute();
		} else {
			$feedback = $this->getEntityManager()->createQuery("
				SELECT i
				FROM DeskPRO:Feedback i
				WHERE i.status = ?1
				ORDER BY i.$sort DESC
			")->setParameter(1, $status)->setMaxResults($num)->execute();
		}

		return $feedback;
	}


	public function getNewest($status, $num = 10, $node = false)
	{
		if (!$status) {
			$feedback = $this->getEntityManager()->createQuery("
				SELECT i
				FROM DeskPRO:Feedback i INDEX BY i.id
				WHERE i.status != 'closed' AND i.status != 'hidden'
				ORDER BY i.id DESC
			")->setMaxResults($num)->execute();
			return $feedback;
		}

		if (Numbers::isInteger($status)) {
			if ($node) {
				$cat_ids = $node->getTreeIds(true);
				$feedback = $this->getEntityManager()->createQuery("
					SELECT i
					FROM DeskPRO:Feedback i INDEX BY i.id
					WHERE i.status_category = ?1 AND i.category IN (" . implode(',',$cat_ids) . ")
					ORDER BY i.id DESC
				")->setParameter(1, $status)->setMaxResults($num)->execute();
			} else {
				$feedback = $this->getEntityManager()->createQuery("
					SELECT i
					FROM DeskPRO:Feedback i INDEX BY i.id
					WHERE i.status_category = ?1
					ORDER BY i.id DESC
				")->setParameter(1, $status)->setMaxResults($num)->execute();
			}
		} else {
			if ($node) {
				$cat_ids = $node->getTreeIds(true);
				$feedback = $this->getEntityManager()->createQuery("
					SELECT i
					FROM DeskPRO:Feedback i INDEX BY i.id
					WHERE i.status = ?1 AND i.category IN (" . implode(',',$cat_ids) . ")
					ORDER BY i.id DESC
				")->setParameter(1, $status)->setMaxResults($num)->execute();
			} else {
				$feedback = $this->getEntityManager()->createQuery("
					SELECT i
					FROM DeskPRO:Feedback i INDEX BY i.id
					WHERE i.status = ?1
					ORDER BY i.id DESC
				")->setParameter(1, $status)->setMaxResults($num)->execute();
			}
		}

		return $feedback;
	}

	public function getReportAssociations()
	{
		return array(
			'views' => array(
				'conditions' => '%1$s.object_type = 4 AND %1$s.object_id = %2$s.id',
				'targetEntity' => 'Application\\DeskPRO\\Entity\\PageViewLog'
			),
			'ratings' => array(
				'conditions' => '%1$s.object_type = \'feedback\' AND %1$s.object_id = %2$s.id',
				'targetEntity' => 'Application\\DeskPRO\\Entity\\Rating'
			)
		);
	}
}
