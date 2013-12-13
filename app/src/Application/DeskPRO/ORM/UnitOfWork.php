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
 * @subpackage
 */

namespace Application\DeskPRO\ORM;

use Application\DeskPRO\ORM\Unprivate\UnprivateUnitOfWork as DoctrineUnitOfWork;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Application\DeskPRO\EntityRepository\Preloadable;

class UnitOfWork extends DoctrineUnitOfWork
{
	/**
	 * @var \Application\DeskPRO\ORM\Persisters\LookupBasicEntityPersister[]
	 */
	private $_persisters;

	/**
	 * Sets that have already been loaded in full
	 *
	 * @var array
	 */
	protected $loaded_sets = array();

	/**
	 * These are types of entities where when one is fetched, the whole set
	 * should be fetched.
	 *
	 * @var array
	 */
	protected $enable_preload_set = array(
		'Application\\DeskPRO\\Entity\\Department'                     => 1,
		'Application\\DeskPRO\\Entity\\TicketCategory'                 => 1,
		'Application\\DeskPRO\\Entity\\ArticleCategory'                => 1,
		'Application\\DeskPRO\\Entity\\DownloadCategory'               => 1,
		'Application\\DeskPRO\\Entity\\FeedbackCategory'               => 1,
		'Application\\DeskPRO\\Entity\\NewsCategory'                   => 1,
		'Application\\DeskPRO\\Entity\\Product'                        => 1,
		'Application\\DeskPRO\\Entity\\CustomDefArticle'               => 1,
		'Application\\DeskPRO\\Entity\\CustomDefFeedback'              => 1,
		'Application\\DeskPRO\\Entity\\CustomDefOrganization'          => 1,
		'Application\\DeskPRO\\Entity\\CustomDefPerson'                => 1,
		'Application\\DeskPRO\\Entity\\CustomDefTicket'                => 1,
		'Application\\DeskPRO\\Entity\\Languages'                      => 1,
		'Application\\DeskPRO\\Entity\\AgentTeam'                      => 1,
	);

	/**
	 * Add a type of entity that sholud be pre-fetched
	 *
	 * @param $entity_class
	 */
	public function addPreloadedEntity($entityName)
	{
		$class = $this->em->getClassMetadata($entityName);
		$classname = $class->getName();

		$this->enable_preload_set[$classname] = 1;
	}


	/**
	 * Marks a repository as prelaoded
	 *
	 * @param $entityName
	 */
	public function markAsPreloaded($entityName)
	{
		$class = $this->em->getClassMetadata($entityName);
		$classname = $class->getName();

		$this->loaded_sets[$classname] = true;
	}


	/**
	 * Load the full set of a particular entity
	 *
	 * @param $entity_name
	 */
	public function preloadEntitySet($entityName)
	{
		$class = $this->em->getClassMetadata($entityName);
		$classname = $class->getName();

		if (isset($this->loaded_sets[$classname])) {
			return;
		}

		// Important to set this before executing the query,
		// or else we'll get recursion with doctrine trying to
		// getEntityPersister() which fires this preload etc
		$this->loaded_sets[$classname] = true;

		$repos = $this->em->getRepository($classname);

		if ($repos instanceof Preloadable) {
			$repos->preload();
		} else {
			foreach ($repos->findAll() as $e) {
				if ($e instanceof \Doctrine\ORM\Proxy\Proxy) {
					$e->__load();
				}
			}
		}
	}


	/**
	 * Check if an entity is set to be preloaded
	 *
	 * @param $entityName
	 * @return bool
	 */
	public function isAddedPreloadedEntity($entityName)
	{
		$class = $this->em->getClassMetadata($entityName);
		$classname = $class->getName();

		return isset($this->enable_preload_set[$classname]);
	}


	public function getEntityPersister($entityName)
	{
		$class = $this->em->getClassMetadata($entityName);
		$classname = $class->getName();

		if (isset($this->_persisters[$classname])) {
			return $this->_persisters[$classname];
		}

		if ($class->isInheritanceTypeNone()) {
			$persister = new Persisters\LookupBasicEntityPersister($this->em, $class);
			$this->_persisters[$classname] = $persister;
			return $persister;
		}

		return parent::getEntityPersister($entityName);
	}

	/**
	 * Copy of default executeUpdates except for two places to check for existence of array key, see documented
	 * lines below.
	 *
	 * See: https://github.com/doctrine/doctrine2/pull/126
	 */
	protected  function executeUpdates($class)
	{
		$className = $class->name;
		$persister = $this->getEntityPersister($className);

		$hasPreUpdateLifecycleCallbacks = isset($class->lifecycleCallbacks[\Doctrine\ORM\Events::preUpdate]);
		$hasPreUpdateListeners          = $this->evm->hasListeners(\Doctrine\ORM\Events::preUpdate);

		$hasPostUpdateLifecycleCallbacks = isset($class->lifecycleCallbacks[\Doctrine\ORM\Events::postUpdate]);
		$hasPostUpdateListeners          = $this->evm->hasListeners(\Doctrine\ORM\Events::postUpdate);

		foreach ($this->entityUpdates as $oid => $entity) {
			if ( ! (get_class($entity) === $className || $entity instanceof \Doctrine\ORM\Proxy\Proxy && get_parent_class($entity) === $className)) {
				continue;
			}

			if ($hasPreUpdateLifecycleCallbacks) {
				$class->invokeLifecycleCallbacks(\Doctrine\ORM\Events::preUpdate, $entity);

				$this->recomputeSingleEntityChangeSet($class, $entity);
			}

			// DESKPRO CHANGE: added empty check \/
			if ($hasPreUpdateListeners && !empty($this->entityChangeSets[$oid])) {
				$this->evm->dispatchEvent(
					\Doctrine\ORM\Events::preUpdate,
					new \Doctrine\ORM\Event\PreUpdateEventArgs($entity, $this->em, $this->entityChangeSets[$oid])
				);
			}

			// DESKPRO CHANGE: added isset check \/
			if (isset($this->entityChangeSets[$oid]) && $this->entityChangeSets[$oid]) {
				$persister->update($entity);
			}

			unset($this->entityUpdates[$oid]);

			if ($hasPostUpdateLifecycleCallbacks) {
				$class->invokeLifecycleCallbacks(\Doctrine\ORM\Events::postUpdate, $entity);
			}

			if ($hasPostUpdateListeners) {
				$this->evm->dispatchEvent(\Doctrine\ORM\Events::postUpdate, new \Doctrine\ORM\Event\LifecycleEventArgs($entity, $this->em));
			}
		}
	}
}