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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use Application\DeskPRO\Domain\DomainObject;
use Application\DeskPRO\ORM\UnitOfWork;
use Application\DeskPRO\ORM\Proxy\ProxyFactory;
use Application\DeskPRO\ORM\Unprivate\UnprivateEntityManager;

/**
 * Customized EM to override proxy factory
 */
class EntityManager extends UnprivateEntityManager
{
	protected $_delayedInsert = array();
	protected $_delayedUpdate = array();

	protected function __construct(Connection $conn, Configuration $config, EventManager $eventManager)
	{
		parent::__construct($conn, $config, $eventManager);

		$this->unitOfWork = new UnitOfWork($this);
		$this->proxyFactory = new ProxyFactory(
			$this,
			$config->getProxyDir(),
			$config->getProxyNamespace(),
			$config->getAutoGenerateProxyClasses()
		);
	}

	public function clearRepositoryCache()
	{
		$this->repositories = array();
	}

	public function persist($entity)
	{
		if (dp_get_config('debug.em_persist_log')) {
			static $logger = null;

			if ($logger === null) {
				$logger = new \Orb\Log\Logger();
				$wr = new \Orb\Log\Writer\Stream(dp_get_log_dir() . '/em-persist.log', 'a');
				$logger->addWriter($wr);
			}

			$type  = get_class($entity);
			$id    = isset($entity['id']) ? $entity['id'] : '0';
			$trace = \DeskPRO\Kernel\KernelErrorHandler::formatBacktrace(debug_backtrace());
			$logger->logDebug("Persist: $type :: $id\n$trace\n\n");
		}

		if ($entity instanceof DomainObject) {
			if ($entity->_isNoPersist()) {
				$e = new \InvalidArgumentException("Entity marked as no persist: " . get_class($entity) . " (id: " . $entity->getId());
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo(\DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e));
				return;
			}
		}

		if (isset($entity->_dp_object_translatable)) {
			$entity->_dp_object_translatable->_dpTranslatePersistChanges();
		}

		parent::persist($entity);
	}

	/**
	 * Sets an entity to be insert after the next flush call completes.
	 * This is mostly useful when trying to insert an entity in a pre/post-update
	 * event, where the managed entities are already setup. This only works
	 * when inserting an entity.
	 *
	 * @param $entity
	 */
	public function delayedInsert($entity)
	{
		$oid = spl_object_hash($entity);

        if (!isset($this->_delayedInsert[$oid])) {
			$this->_delayedInsert[$oid] = $entity;
        }
	}

	/**
	 * When you need to update another entity in a pre/post-update event, the entity
	 * cannot be updated directly as it may not be saved. This method takes the code
	 * to do the update and delays it until after the flush completes and immediately
	 * does the update.
	 *
	 * @param callable $closure
	 * @param string|null $unique_key
	 */
	public function delayedUpdate(\Closure $closure, $unique_key = null)
	{
		if ($unique_key) {
			$this->_delayedUpdate[$unique_key] = $closure;
		} else {
			$this->_delayedUpdate[] = $closure;
		}
	}

	public function flush($entity = null)
	{
		if (!$entity && $this->_delayedInsert) {
			foreach ($this->_delayedInsert AS $persist) {
				$this->persist($persist);
			}
			$this->_delayedInsert = array();
		}
		if (!$entity && $this->_delayedUpdate) {
			foreach ($this->_delayedUpdate AS $closure) {
				$closure($this);
			}
			$this->_delayedUpdate = array();
		}

		parent::flush($entity);

		if (!$entity) {
			$flush_again = false;

			if ($this->_delayedInsert) {
				foreach ($this->_delayedInsert AS $persist) {
					$this->persist($persist);
				}
				$this->_delayedInsert = array();
				$flush_again = true;
			}

			if ($this->_delayedUpdate) {
				foreach ($this->_delayedUpdate AS $closure) {
					$closure($this);
				}
				$this->_delayedUpdate = array();
				$flush_again = true;
			}

			if ($flush_again) {
				$this->flush();
			}
		}
	}

	public function clearNot($class_name)
	{
		if (!is_array($class_name)) {
			$skip = array($class_name);
		} else {
			$skip = $class_name;
		}

		foreach ($this->unitOfWork->getIdentityMap() AS $class => $entity_name) {
			if (!in_array($class, $skip)) {
				$this->unitOfWork->clear($class);
			}
		}
	}
}
