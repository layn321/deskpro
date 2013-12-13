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

namespace Application\DeskPRO\ORM\Persisters;

use Doctrine\ORM\Persisters\BasicEntityPersister;
use Doctrine\ORM\Query;
use Doctrine\ORM\PersistentCollection;

class LookupBasicEntityPersister extends BasicEntityPersister
{
	public function load(array $criteria, $entity = null, $assoc = null, array $hints = array(), $lockMode = 0, $limit = null)
	{
		$uof = $this->_em->getUnitOfWork();
		$classname = $this->_class->getName();

		// Look for ID-based entities
		if (count($criteria) == 1 && isset($criteria['id']) && $criteria['id']) {
			if ($uof->isAddedPreloadedEntity($classname)) {
				$uof->preloadEntitySet($classname);
			}
			$hit = $uof->tryGetById($criteria['id'], $classname);
			if ($hit && $hit->__hasRunLoad__() && $hit->getId()) {
				return $hit;
			}
		}

		// Search through the identity map for parent_id
		if (count($criteria) == 1 && isset($criteria['parent_id']) && $criteria['parent_id']) {
			if ($uof->isAddedPreloadedEntity($classname)) {
				$uof->preloadEntitySet($classname);
			}

			$idmap = $uof->getIdentityMap();
			if (isset($idmap[$classname])) {
				foreach ($idmap[$classname] as $ent) {
					if ($ent->__hasRunLoad__() && $ent->getId() == $criteria['parent_id']) {
						return $ent;
					}
				}
			}
		}

		return parent::load($criteria, $entity, $assoc, $hints, $lockMode, $limit);
	}

	public function loadOneToManyCollection(array $assoc, $sourceEntity, PersistentCollection $coll)
    {
		if ($sourceEntity->__dp_is_preloaded_repos && isset($assoc['fieldName']) && $assoc['fieldName'] == 'children') {
			$repos = $sourceEntity->__dp_is_preloaded_repos;
			$children = $repos->getChildren($sourceEntity);

			if ($children) {
				foreach ($children as $c) {
					$coll->hydrateAdd($c);
				}
			}
		} else {
			return parent::loadOneToManyCollection($assoc, $sourceEntity, $coll);
		}
    }
}