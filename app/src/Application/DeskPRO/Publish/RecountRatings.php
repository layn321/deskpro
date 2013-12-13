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
 */

namespace Application\DeskPRO\Publish;

use Doctrine\ORM\EntityManager;

class RecountRatings
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var int
	 */
	protected $batch_size = 1000;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * @param int $batch_size
	 */
	public function setBatchSize($batch_size)
	{
		$this->batch_size = $batch_size;
	}


	/**
	 * @return int
	 */
	public function getIdMax()
	{
		static $id_max = null;

		if ($id_max === null) {
			$id_max = $this->em->getConnection()->fetchColumn("SELECT id FROM ratings ORDER BY id DESC LIMIT 1");
		}

		return $id_max;
	}


	/**
	 * @return float
	 */
	public function countBatches()
	{
		static $num_batches = null;

		if ($num_batches === null) {
			$num_batches = ceil($this->getIdMax() / $this->batch_size);
		}

		return $num_batches;
	}


	/**
	 * @param callable $status_fn
	 */
	public function recountAll($status_fn = null)
	{
		if (!$status_fn) {
			$status_fn = function($status_type, array $info) { };
		}

		$pages = $this->countBatches();
		$status_fn('start', array('num_batches' => $pages, 'batch_size' => $this->batch_size));

		for($i = 1; $i <= $pages; $i++) {
			$status_fn('batch_start', array('batch' => $i));
			$this->recountBatch($i);
			$status_fn('batch_end', array('batch' => $i));
		}

		$status_fn('end', $i);
	}


	/**
	 * @param int $page
	 */
	public function recountBatch($page)
	{
		$start = (($page-1) * $this->batch_size) + 1;
		$end   = $page * $this->batch_size;

		$ratings = $this->em->getConnection()->fetchAll("
			SELECT object_id, object_type, rating
			FROM ratings
			WHERE id BETWEEN $start AND $end
		");

		$update_set = array();
		foreach ($ratings as $rating) {
			$key = $rating['object_type'] . $rating['object_id'];
			if (!isset($update_set[$key])) {
				$update_set[$key] = array('type' => $rating['object_type'], 'id' => $rating['object_id'], 'count' => 0, 'rating' => 0);
			}

			$update_set[$key]['count']++;
			$update_set[$key]['rating'] += $rating['rating'];
		}

		$this->em->getConnection()->beginTransaction();
		try {
			foreach ($update_set as $set) {
				switch ($set['type']) {
					case 'article':   $table = 'articles'; break;
					case 'download':  $table = 'downloads'; break;
					case 'news':      $table = 'news'; break;
					case 'feedback':  $table = 'feedback'; break;
					default: continue;
				}

				if ($page == 0) {
					$this->em->getConnection()->executeUpdate("
						UPDATE $table
						SET total_rating = ?, num_ratings ?
						WHERE id = ?
					", array($set['rating'], $set['count'], $set['id']));
				} else {
					$this->em->getConnection()->executeUpdate("
						UPDATE $table
						SET total_rating = total_rating + ?, num_ratings = num_ratings + ?
						WHERE id = ?
					", array($set['rating'], $set['count'], $set['id']));
				}
			}
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}
	}
}