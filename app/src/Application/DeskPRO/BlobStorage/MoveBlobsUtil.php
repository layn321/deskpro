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

namespace Application\DeskPRO\BlobStorage;

use Doctrine\ORM\EntityManager;
use Orb\Log\Loggable;
use Orb\Log\Logger;

class MoveBlobsUtil implements Loggable
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var DeskproBlobStorage
	 */
	protected $bs;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var int
	 */
	protected $limit;

	/**
	 * @var null
	 */
	protected $limit_time = null;

	/**
	 * @var bool
	 */
	protected $ignore_error;

	/**
	 * @var
	 */
	protected $count = null;

	/**
	 * @var null
	 */
	protected $aids_where = null;


	/**
	 * @param EntityManager $em
	 * @param DeskproBlobStorage $bs
	 */
	public function __construct(EntityManager $em, DeskproBlobStorage $bs)
	{
		$this->em = $em;
		$this->db = $em->getConnection();
		$this->bs = $bs;

		$this->aids_where = "'" . implode("','", $bs->getAdapterIds()) . "'";
	}


	/**
	 * @param Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @return Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}


	/**
	 * @param $limit
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
	}


	/**
	 * @param $limit_time
	 */
	public function setLimitTime($limit_time)
	{
		$this->limit_time = $limit_time;
	}


	/**
	 * @param bool $on
	 */
	public function setIgnoreErrors($on = true)
	{
		$this->ignore_error = (bool)$on;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->db->fetchColumn("
			SELECT COUNT(*)
			FROM blobs
			WHERE
				storage_loc_pref IS NOT NULL
				AND storage_loc_pref IN ({$this->aids_where})
		");
	}


	/**
	 * @return int
	 */
	public function run()
	{
		$start_t = microtime(true);
		$x = 0;

		while (true) {
			$this->em->clear('DeskPRO:Blob');

			$blob = $this->em->createQuery("
				SELECT b
				FROM DeskPRO:Blob b
				WHERE b.storage_loc_pref IS NOT NULL AND b.storage_loc_pref IN ({$this->aids_where})
				ORDER BY b.id ASC
			")->setMaxResults(1)->getOneOrNullResult();

			if (!$blob) {
				break;
			}

			$this->logger->logDebug("{$x}. Processing blob #{$blob['id']}");
			if ($blob->storage_loc == $blob->storage_loc_pref) {
				$this->logger->logInfo("Already using preferred storage");
				$blob->storage_loc_pref = null;
				$this->em->persist($blob);
				$this->em->flush();
				break;
			}

			$t = microtime(true);

			try {
				$this->bs->moveBlobRecordToAdapter($blob, $blob->storage_loc_pref);
			} catch (\Exception $e) {
				$this->logger->logError("Error: " . $e->getMessage());
				if (!$this->ignore_error) {
					$this->logger->logDebug('Aborting');
					break;
				}
			}

			$blob->storage_loc_pref = null;
			$this->em->persist($blob);
			$this->em->flush();

			$this->logger->logDebug(sprintf("Done in %.3fs", microtime(true) - $t));

			$x++;
			if ($this->limit) {
				if ($x >= $this->limit) {
					$this->logger->logInfo('Limit reached, breaking');
					break;
				}
			}

			if ($this->limit_time) {
				$time = time() - intval($start_t);
				if ($time > $this->limit_time) {
					$this->logger->logInfo('Time limit reached, breaking');
					break;
				}
			}
		}

		$this->logger->logInfo(sprintf("<info>Moved %d blobs in %.3fs</info>", $x, microtime(true) - $start_t));

		return $x;
	}
}