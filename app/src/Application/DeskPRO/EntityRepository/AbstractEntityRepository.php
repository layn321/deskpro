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

use Orb\Util\Arrays;
use Orb\Util\Strings;

class AbstractEntityRepository extends \Doctrine\ORM\EntityRepository
{
	/**
	 * @var \Application\DeskPRO\EntityRepository\Helper\IdentityHelper
	 */
	protected $identity_helper;

	/**
	 * @return \Application\DeskPRO\EntityRepository\Helper\IdentityHelper
	 */
	public function getIdentityHelper()
	{
		if (!$this->identity_helper) {
			$this->identity_helper = new Helper\IdentityHelper($this->getEntityManager(), $this);
		}

		return $this->identity_helper;
	}


	/**
	 * Get a collection of entities by ID
	 *
	 * @param bool $keep_order True to order the resulting array in the same order that ids are provided in $ids
	 * @return array
	 */
	public function getByIds(array $ids, $keep_order = false)
	{
		if (!$ids) return array();

		$class = $this->getName();

		$ids = array_values($ids);

		if ($this->getEntityManager()->getUnitOfWork()->isAddedPreloadedEntity($this->getName())) {
			$this->getEntityManager()->getUnitOfWork()->preloadEntitySet($this->getName());
			return $this->getIdentityHelper()->findByIds($ids, $keep_order);
		} else {
			$q_res = $this->getEntityManager()->createQuery("
				SELECT o
				FROM {$class} o INDEX BY o.id
				WHERE o.id IN(?0)
			")->execute(array($ids));

			if ($keep_order) {
				$q_res = Arrays::orderIdArray($ids, $q_res);
			}

			return $q_res;
		}
	}


	/**
	 * Alias for find
	 *
	 * @param int $id
	 * @return object
	 */
	public function get($id)
	{
		return $this->find($id);
	}


	/**
	 * @return int
	 */
	public function countAll()
	{
		return $this->_em->getConnection()->fetchColumn("SELECT COUNT(*) FROM `" . $this->getTableName() . "`");
	}


	/**
	 * @return string
	 */
	public function getTableName()
	{
		return $this->getClassMetadata()->getTableName();
	}

	public function getFieldMappings()
	{
		return $this->getClassMetadata()->fieldMappings;
	}

	public function getAssociationMappings()
	{
		return $this->getClassMetadata()->getAssociationMappings();
	}

	public function getReportAssociations()
	{
		return array();
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->getClassMetadata()->getName();
	}
}
