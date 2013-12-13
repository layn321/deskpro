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
 * @category Search
 */

namespace Application\DeskPRO\Search;

use Application\DeskPRO\App;
use Doctrine\ORM\EntityManager;
use Application\DeskPRO\Queue\Queue;

use Orb\Util\Arrays;

/**
 * When something needs to be indexed, index it through this
 */
class SearchIndexer
{
	/**
	 * Entity manager
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * Plain database connection for raw queries
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Application\DeskPRO\Queue\Queue
	 */
	protected $queue;

	public function __construct(EntityManager $em, Queue $queue)
	{
		$this->em = $em;
		$this->db = $em->getConnection();
		$this->queue = $queue;
	}

	public function update($object, $op = 'update')
	{
		$type = \Orb\Util\Util::getBaseClassname($object);

		// These flags are used in the importer
		if (isset($GLOBALS['DP_INDEX_REALTIME']) || in_array($type, array('Article', 'Download', 'Feedback', 'News'))) {
			$this->updateNow($object, $op);
			return;
		}
		if (isset($GLOBALS['DP_INDEX_NOINDEX'])) {
			return;
		}

		$content_type = App::getContainer()->getSearchAdapter()->getContentTypeNameForObject($object);

		$this->queue->send(array('entity_class' => get_Class($object), 'id' => $object->getId(), 'op' => $op));
	}

	public function updateNow($object, $op = 'update')
	{
		if ($op == 'update') {
			App::getContainer()->getSearchAdapter()->updateObjectsInIndex(array($object));
		} else {
			App::getContainer()->getSearchAdapter()->deleteObjectsFromIndex(array($object));
		}
	}
}
