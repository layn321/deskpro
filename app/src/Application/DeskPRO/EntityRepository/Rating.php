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

use Application\DeskPRO\App;
use Orb\Util\Arrays;

class Rating extends AbstractEntityRepository
{
	public function getRatingsFor($object_type, $object_id)
	{
		return $this->getEntityManager()->createQuery("
			SELECT r
			FROM DeskPRO:Rating r INDEX BY r.id
			LEFT JOIN r.person p
			WHERE r.object_type = ?1 AND r.object_id = ?2
			ORDER BY r.id
		")->execute(array(1=> $object_type, 2=> $object_id));
	}

	public function getRatingByPersonOnObject($object_type, $object_id, $person = null, $visitor = null)
	{
		if ($person) {
			return $this->getEntityManager()->createQuery("
				SELECT r
				FROM DeskPRO:Rating r INDEX BY r.id
				WHERE r.object_type = ?1 AND r.object_id = ?2 AND (r.person = ?3 OR r.visitor = ?4)
				ORDER BY r.id
			")->setMaxResults(1)->setParameters(array(1=> $object_type, 2=> $object_id, 3=> $person, 4=> $visitor))->getOneOrNullResult();
		} else {
			return $this->getEntityManager()->createQuery("
				SELECT r
				FROM DeskPRO:Rating r INDEX BY r.id
				WHERE r.object_type = ?1 AND r.object_id = ?2 AND r.visitor = ?3
				ORDER BY r.id
			")->setMaxResults(1)->setParameters(array(1=> $object_type, 2=> $object_id, 3=> $visitor))->getOneOrNullResult();
		}
	}
}
