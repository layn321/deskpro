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

namespace Application\DeskPRO\Search\EntityWatcher;

use Application\DeskPRO\App;
use Application\DeskPRO\Search\Adapter\AbstractAdapter;
use Application\DeskPRO\DBAL\DoctrineEvent;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Orb\Util\Util;
use Orb\Filter\FilterInterface;

use Application\DeskPRO\DependencyInjection\DeskproContainer;

class EntityWatcher implements \Doctrine\Common\EventSubscriber
{
	public static $watched_entities = array(
		'Application\\DeskPRO\\Entity\\Article' => 1,
		'Application\\DeskPRO\\Entity\\LabelArticle' => 1,
		'Application\\DeskPRO\\Entity\\Download' => 1,
		'Application\\DeskPRO\\Entity\\LabelDownload' => 1,
		'Application\\DeskPRO\\Entity\\Feedback' => 1,
		'Application\\DeskPRO\\Entity\\LabelFeedback' => 1,
		'Application\\DeskPRO\\Entity\\News' => 1,
		'Application\\DeskPRO\\Entity\\LabelNews' => 1,
		'Application\\DeskPRO\\Entity\\Ticket' => 1,
		'Application\\DeskPRO\\Entity\\TicketMessage' => 1,
	);

	/**
	 * @var \Orb\Filter\FilterInterface[]
	 */
	protected $entity_filters = array();

	/**
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected $container;

	/**
	 * @var bool
	 */
	protected $is_running = false;

	/**
	 * @var array
	 */
	protected $updates = array('updates' => array(), 'deletes' => array());

	public function __construct(DeskproContainer $container)
	{
		$this->container = $container;
		\DpShutdown::add(array($this, 'flushUpdatesQuiet'));
	}

	public function flushUpdatesQuiet()
	{
		try {
			$this->flushUpdates();
		} catch (\Exception $e) {}
	}

	public function flushUpdates()
	{
		if ($this->is_running) return;
		$this->is_running = true;

		$queue = $this->container->getQueue('search_object_update');

		foreach ($this->updates['updates'] as $info) {
			$id = $info['id'] ?: $info['ent']->getId();
			if ($id) {
				$queue->send(array('entity_class' => $info['entity'], 'id' => $id, 'op' => 'update'));
			}
		}
		foreach ($this->updates['deletes'] as $info) {
			$id = $info['id'];
			if ($id) {
				$queue->send(array('entity_class' => $info['entity'], 'id' => $id, 'op' => 'delete'));
			}
		}

		$this->updates = array('updates' => array(), 'deletes' => array());

		$this->is_running = false;
	}

	protected function _lazyInit()
	{
		static $has_init = false;
		if ($has_init === true) return;
		$has_init = true;

		$ticket_filter = new MysqlFilter\TicketFilter($this->container->getEm());
		$this->addEntityTypeFilter('Application\\DeskPRO\\Entity\\Ticket', $ticket_filter);
	}

	/**
	 * Add an entity filter.
	 *
	 * Filters take entities that we've detected changes on, and is meant to
	 * take a look at the changes to see if we actually need to update the index.
	 * For example, if a ticket status is just changed, we dont need to update the fulltext index
	 *
	 * @param $entity
	 * @param \Orb\Filter\FilterInterface $filter
	 */
	public function addEntityTypeFilter($entity_type, FilterInterface $filter)
	{
		$this->entity_filters[$entity_type] = $filter;
	}


	/**
	 * Filter an entity to see if it sholud be updated
	 *
	 * @param $entity
	 * @return bool
	 */
	public function filterEntity($entity)
	{
		$entity_type = get_class($entity);
		if (isset($this->entity_filters[$entity_type])) {
			return $this->entity_filters[$entity_type]->filter($entity);
		}

		// Default to true
		return true;
	}


	public function onFlush(OnFlushEventArgs $eventArgs)
	{
		if ($this->is_running) return;
		$this->is_running = true;

		$this->_lazyInit();

		$update = array();
		$delete = array();

		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		foreach ($uow->getScheduledEntityInsertions() as $ent) {
			if (self::isWatchedEntity($ent) && $this->filterEntity($ent)) {
				$ent = $this->replaceEntity($ent);
				$update[] = $ent;
			}
		}
		foreach ($uow->getScheduledEntityUpdates() as $ent) {
			if (self::isWatchedEntity($ent) && $this->filterEntity($ent)) {
				$ent = $this->replaceEntity($ent);
				$update[] = $ent;
			}
		}
		foreach ($uow->getScheduledEntityDeletions() as $ent) {
			if (self::isWatchedEntity($ent)) {
				$ent = $this->replaceEntity($ent);
				$delete[] = $ent;
			}
		}

		if ($update || $delete) {
			foreach ($update as $ent) {
				$name = self::getEntityClassName($ent);
				$id = $ent->getId();
				$this->updates['updates']["$name-$id"] = array('entity' => $name, 'id' => $id, 'ent' => $ent);
			}
			foreach ($delete as $ent) {
				$name = self::getEntityClassName($ent);
				$id = $ent->getId();
				$this->updates['deletes']["$name-$id"] = array('entity' => $name, 'id' => $id, 'ent' => $ent);
			}
		}

		$this->is_running = false;
	}

	public function replaceEntity($ent)
	{
		if ($ent instanceof \Application\DeskPRO\Entity\LabelArticle) {
			return $ent->article;
		} elseif ($ent instanceof \Application\DeskPRO\Entity\LabelNews) {
			return $ent->news;
		} elseif ($ent instanceof \Application\DeskPRO\Entity\LabelDownload) {
			return $ent->download;
		} elseif ($ent instanceof \Application\DeskPRO\Entity\LabelFeedback) {
			return $ent->feedback;
		} elseif ($ent instanceof \Application\DeskPRO\Entity\TicketMessage) {
			return $ent->ticket;
		}

		return $ent;
	}


	public function getSubscribedEvents()
	{
		return array(
			\Doctrine\ORM\Events::onFlush
		);
	}


	/**
	 * Check if an entity is watched
	 *
	 * @param $entity
	 * @return bool
	 */
	public static function isWatchedEntity($entity)
	{
		$name = self::getEntityClassName($entity);
		return isset(self::$watched_entities[$name]);
	}

	public static function getEntityClassName($entity)
	{
		if (is_string($entity)) {
			return $entity;
		} elseif ($entity instanceof \Doctrine\ORM\Proxy\Proxy) {
			return get_parent_class($entity);
		} else {
			return get_class($entity);
		}
	}
}
