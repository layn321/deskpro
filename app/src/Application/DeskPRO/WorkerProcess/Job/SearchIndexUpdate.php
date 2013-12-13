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
 * @subpackage WorkerProcess
 */

namespace Application\DeskPRO\WorkerProcess\Job;

use Application\DeskPRO\App;
use Application\DeskPRO\Log\Logger;

/**
 * This cleans up various temporary data
 */
class SearchIndexUpdate extends AbstractJob
{
	const DEFAULT_INTERVAL = 60; // 1 min

	/**
	 * @var \Application\DeskPRO\Queue\Queue
	 */
	protected $queue;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	public function run()
	{
		$time = time();

		$this->em = App::getContainer()->getEm();
		$this->db = $this->em->getConnection();
		$this->queue = App::getContainer()->getQueue('search_object_update');

		$batch = $this->queue->receive(20);
		while (count($batch)) {
			$update = array();
			$delete = array();

			foreach ($batch as $info) {
				$op = isset($info->op) ? $info->op : 'update';

				$exists = false;
				try {
					if (@$this->em->getClassMetadata($info->entity_class)) {
						$exists = true;
					}
				} catch (\Exception $e) {}

				if (!$exists) {
					$this->queue->deleteMessage($info);
					continue;
				}

				$idx = "{$info->entity_class}.{$info->id}";

				if ($op == 'update') {
					if (!isset($delete[$idx])) {
						$entity = $this->em->find($info->entity_class, array('id' => $info->id ?: 0));
						if ($entity) {
							$update[$idx] = $entity;
						}
					}
				} else {
					$doc = new \Application\DeskPRO\Search\Indexer\Document($info->id, $info->entity_class);
					$delete[$idx] = $doc;

					if (isset($update[$idx])) {
						unset($update[$idx]);
					}
				}
				$this->queue->deleteMessage($info);
			}

			// Sometimes an object might have been detached/deleted
			// before it got here, so we should just ignore reindex
			// commands on them
			if ($update && $update instanceof \Doctrine\ORM\Proxy\Proxy) {
				try {
					$update->__load();
				} catch (\Doctrine\ORM\EntityNotFoundException $e) {
					continue;
				}
			}

			if ($update) {
				App::getContainer()->getSearchAdapter()->updateObjectsInIndex($update);
			}
			if ($delete) {
				App::getContainer()->getSearchAdapter()->deleteDocumentsFromIndex($delete);
			}

			if (time() - $time > 30) {
				break;
			}

			$batch = $this->queue->receive(20);
		}
	}
}
