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
use \Application\DeskPRO\Entity\TwitterAccount AS TwitterAccountEntity;

use Orb\Util\Numbers;

class TwitterStatus extends AbstractEntityRepository
{
	public function getByTwitterStatusId($id)
	{
		return $this->getEntityManager()->createQuery("
			SELECT s
			FROM DeskPRO:TwitterStatus s
			WHERE s.id = ?0
		")->setParameters(array($id))->getOneOrNullResult();
	}

	/**
	 * @param integer $id
	 * @param array|null $from_user_ids If not null, only from these users
	 * @param Boolean $includeArchived (optional)
	 * @param string $sortByDate (optional)
	 * @param integer $limit (optional)
	 * @param integer $page (optional)
	 * @return array
	 */
	public function findMessagesForUserId($id, array $from_user_ids = null, $includeArchived = false, $sortByDate = 'asc', $limit = 25, $page = 1)
	{
		if ($from_user_ids !== null && !$from_user_ids) {
			return array();
		}

		$query = "
			SELECT s
			FROM DeskPRO:TwitterStatus s INDEX BY s.id
			WHERE s.recipient IS NOT NULL
		";

		if ($from_user_ids) {
			if (in_array($id, $from_user_ids)) {
				// make sure we can see anything this account sent
				$query .= " AND ((s.user = :user_id) OR (s.user IN (:from_user_ids) AND s.recipient = :user_id)) ";
			} else {
				$from_user_ids[] = $id;
				$query .= " AND ((s.user = :user_id AND s.recipient IN (:from_user_ids)) OR (s.user IN (:from_user_ids) AND s.recipient = :user_id)) ";
			}
			$params = array('user_id' => $id, 'from_user_ids' => $from_user_ids);
		} else {
			$query .= " AND (s.user = :user_id OR s.recipient = :user_id) ";
			$params = array('user_id' => $id);
		}

		if (!$includeArchived) {
			$query .= " AND s.is_archived = 0 ";
		}

		$query .= sprintf("
			ORDER BY s.date_created %s
		", $this->normalizeSortByDate($sortByDate));

		return $this
			->getEntityManager()
			->createQuery($query)
			->setMaxResults($limit)
			->setFirstResult($this->calculateOffset($limit, $page))
			->execute($params);
	}

	/**
	 * @param integer $id
	 * @param array|null $from_user_ids If not null, only from these users
	 * @param Boolean $includeArchived (optional)
	 * @return integer
	 */
	public function countMessagesForUserId($id, array $from_user_ids = null, $includeArchived = false)
	{
		if ($from_user_ids !== null && !$from_user_ids) {
			return 0;
		}

		$query = "
			SELECT COUNT(s.id)
			FROM DeskPRO:TwitterStatus s
			WHERE s.recipient IS NOT NULL
		";

		if ($from_user_ids) {
			if (in_array($id, $from_user_ids)) {
				// make sure we can see anything this account sent
				$query .= " AND ((s.user = :user_id) OR (s.user IN (:from_user_ids) AND s.recipient = :user_id)) ";
			} else {
				$from_user_ids[] = $id;
				$query .= " AND ((s.user = :user_id AND s.recipient IN (:from_user_ids)) OR (s.user IN (:from_user_ids) AND s.recipient = :user_id)) ";
			}
			$params = array('user_id' => $id, 'from_user_ids' => $from_user_ids);
		} else {
			$query .= " AND (s.user = :user_id OR s.recipient = :user_id) ";
			$params = array('user_id' => $id);
		}

		if (!$includeArchived) {
			$query .= " AND s.is_archived = 0 ";
		}

		return $this
			->getEntityManager()
			->createQuery($query)
			->setParameters($params)
			->getSingleScalarResult();
	}

	/**
	 * @param integer $id
	 * @param Boolean $includeArchived (optional)
	 * @param string $sortByDate (optional)
	 * @param integer $limit (optional)
	 * @param integer $page (optional)
	 * @return array
	 */
	public function findOutgoingForUserId($id, $includeArchived = false, $sortByDate = 'asc', $limit = 25, $page = 1)
	{
		$query = "
			SELECT s
			FROM DeskPRO:TwitterStatus s INDEX BY s.id
			WHERE s.user = :user_id
				AND s.recipient IS NULL
		";

		if (!$includeArchived) {
			$query .= " AND s.is_archived = 0 ";
		}

		$query .= sprintf("
			ORDER BY s.date_created %s
		", $this->normalizeSortByDate($sortByDate));


		return $this
			->getEntityManager()
			->createQuery($query)
			->setMaxResults($limit)
			->setFirstResult($this->calculateOffset($limit, $page))
			->execute(array(
				'user_id' => $id
			));
	}

	/**
	 * @param integer $id
	 * @param Boolean $includeArchived (optional)
	 * @param string $sortByDate (optional)
	 * @param integer $limit (optional)
	 * @param integer $page (optional)
	 * @return array
	 */
	public function findRepliesForUserId($id, $includeArchived = false, $sortByDate = 'asc', $limit = 25, $page = 1)
	{
		$query = "
			SELECT r
			FROM DeskPRO:TwitterStatus r INDEX BY s.id
			WHERE r.in_reply_to_status IN (
				SELECT s.id
				FROM DeskPRO:TwitterStatus s
				WHERE s.user = :user_id
			)
		";

		if (!$includeArchived) {
			$query .= " AND r.is_archived = 0 ";
		}

		$query .= sprintf("
			ORDER BY s.date_created %s
		", $this->normalizeSortByDate($sortByDate));

		return $this
			->getEntityManager()
			->createQuery($query)
			->setMaxResults($limit)
			->setFirstResult($this->calculateOffset($limit, $page))
			->execute(array(
				'user_id' => $id
			));
	}

	/**
	 * @param integer $id
	 * @param array|null $from_user_ids If not null, only from these users
	 * @param boolean $includeArchived (optional)
	 * @param string $sortByDate (optional)
	 * @param integer $limit (optional)
	 * @param integer $page (optional)
	 * @return array
	 */
	public function findMentionsForUserId($id, array $from_user_ids = null, $includeArchived = false, $sortByDate = 'asc', $limit = 25, $page = 1)
	{
		if ($from_user_ids !== null && !$from_user_ids) {
			return array();
		}

		$query = "
			SELECT s
			FROM DeskPRO:TwitterStatus s INDEX BY s.id
			INNER JOIN s.mentions m
		";

		$params = array(
			'user_id' => $id
		);

		if ($from_user_ids) {
			$query .= "WHERE ((m.user = :user_id AND s.user IN (:from_user_ids)) OR (s.user = :user_id AND m.user IN (:from_user_ids))) ";
			$params['from_user_ids'] = $from_user_ids;
		} else {
			$query .= "WHERE m.user = :user_id";
		}

		if (!$includeArchived) {
			$query .= " AND s.is_archived = 0 ";
		}

		$query .= sprintf("
			ORDER BY s.date_created %s
		", $this->normalizeSortByDate($sortByDate));

	 	return $this
			->getEntityManager()
			->createQuery($query)
			->setMaxResults($limit)
			->setFirstResult($this->calculateOffset($limit, $page))
			->execute($params);
	}

	/**
	 * @param integer $id
	 * @param array|null $from_user_ids If not null, only from these users
	 * @param boolean $includeArchived (optional)
	 * @return array
	 */
	public function countMentionsForUserId($id, array $from_user_ids = null, $includeArchived = false)
	{
		if ($from_user_ids !== null && !$from_user_ids) {
			return 0;
		}

		$query = "
			SELECT COUNT(s.id)
			FROM DeskPRO:TwitterStatus s
			LEFT JOIN s.mentions m
		";

		$params = array(
			'user_id' => $id
		);

		if ($from_user_ids) {
			$query .= "WHERE ((m.user = :user_id AND s.user IN (:from_user_ids)) OR (s.user = :user_id AND m.user IN (:from_user_ids))) ";
			$params['from_user_ids'] = $from_user_ids;
		} else {
			$query .= "WHERE m.user = :user_id";
		}

		if (!$includeArchived) {
			$query .= " AND s.is_archived = 0 ";
		}

	 	return $this
			->getEntityManager()
			->createQuery($query)
			->setParameters($params)
			->getSingleScalarResult();
	}

	/**
	 * @param string $sortByDate (optional)
	 * @return string
	 */
	protected function normalizeSortByDate($sortByDate = 'asc')
	{
		// check that sort by date is asc or desc
		if (!in_array(strtolower($sortByDate), array('asc', 'desc'))) {
			$sortByDate = 'asc';
		}

		return strtoupper($sortByDate);
	}


	/**
	 * @param integer $limit
	 * @param integer $page
	 * @return integer
	 */
	protected function calculateOffset($limit, $page)
	{
		$page = max(1, intval($page));

		return ($page - 1) * $limit;
	}
}
