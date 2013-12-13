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

namespace Application\DeskPRO\EntityRepository\Helper;

use Application\DeskPRO\EntityRepository\AbstractEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;

class IdentityHelper
{
	/**
	 * @var \Application\DeskPRO\EntityRepository\AbstractEntityRepository
	 */
	protected $repos;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var string
	 */
	protected $entity_name;

	/**
	 * @var array
	 */
	protected $collections = array();


	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \Application\DeskPRO\EntityRepository\AbstractEntityRepository $repos
	 */
	public function __construct(EntityManager $em, AbstractEntityRepository $repos)
	{
		$this->repos = $repos;
		$this->em = $em;
		$this->entity_name = $repos->getName();
	}


	/**
	 * Find a set of records by ID.
	 *
	 * @param int[] $ids
	 * @return array
	 */
	public function findByIds(array $ids, $same_order = true)
	{
		$missing = array();
		$return = array();

		if (!$this->hasIdentityMap()) {
			$missing = $ids;
		} else {
			foreach ($ids as $id) {
				$obj = $this->em->getUnitOfWork()->tryGetById($id, $this->entity_name);
				if ($obj && $obj->__getPropValue__('id')) {
					$return[$id] = $obj;
				} else {
					$missing[] = $id;
				}
			}
		}

		if ($missing) {
			$q_res = $this->em->createQuery("
				SELECT o
				FROM {$this->entity_name} o INDEX BY o.id
				WHERE o.id IN(?0)
			")->execute(array($ids));

			if ($q_res) {
				$return = array_merge($return, $q_res);
			}
		}

		if ($same_order) {
			\Orb\Util\Arrays::orderIdArray($ids, $return);
		}

		return $return;
	}


	/**
	 * Get an array of all loaded entities of this type
	 *
	 * @return array
	 */
	public function findAll()
	{
		$map = $this->em->getUnitOfWork()->getIdentityMap();
		if (!isset($map[$this->entity_name])) {
			return array();
		}

		$ret = array();
		foreach ($map[$this->entity_name] as $id_hash => $object) {
			$ret[] = $this->em->getUnitOfWork()->getByIdHash($id_hash, $this->entity_name);
		}

		return $ret;
	}


	/**
	 * Check if there are any objects in the Doctrine identity map at all
	 *
	 * @return bool
	 */
	public function hasIdentityMap()
	{
		$map = $this->em->getUnitOfWork()->getIdentityMap();
		return isset($map[$this->entity_name]);
	}


	/**
	 * @param $id
	 * @return array|null
	 */
	public function getCollection($name)
	{
		if (!isset($this->collections[$name])) {
			return null;
		}

		$return = array();

		foreach ($this->collections[$name] as $find_id) {
			$obj = $this->em->getUnitOfWork()->tryGetById($find_id, $this->entity_name);
			if ($obj) {
				$return[$obj->getId()] = $obj;
			}
		}

		return $return;
	}


	/**
	 * @param string
	 * @return array
	 */
	public function getCollectionIds($name)
	{
		if (!isset($this->collections[$name])) {
			return null;
		}

		return $this->collections[$name];
	}


	/**
	 * Add a collection
	 *
	 * @param $name
	 * @param array $ids
	 */
	public function setCollectionIds($name, array $ids)
	{
		$this->collections[$name] = $ids;
	}


	/**
	 * Adds a collection from an array result set of objects
	 *
	 * @param $name
	 * @param array $results
	 */
	public function setCollectionFromResults($name, array $results)
	{
		$this->collections[$name] = array();

		foreach ($results as $k => $r) {
			$this->collections[$name][$k] = $r->getId();
		}
	}


	/**
	 * Clear a collection
	 *
	 * @param $name
	 */
	public function clearCollection($name)
	{
		unset($this->collections[$name]);
	}


	/**
	 * Check if a collection exists
	 *
	 * @param $name
	 * @return bool
	 */
	public function hasCollection($name)
	{
		return isset($this->collections[$name]);
	}
}