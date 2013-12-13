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

use \Doctrine\ORM\EntityRepository;

class PermissionCache extends AbstractEntityRepository
{
	public function loadPermissionTypes($usergroup_key, $person_id, array $types)
	{
		// A simple filter to make sure only valid names are included
		$types = array_filter($types, function($var) {
			return !preg_match('#[^a-zA-Z0-9_]#', $var);
		});

		if (!$types) {
			return array();
		}

		$key = $usergroup_key;

		if ($person_id) {
			$key .= ".$person_id";
		}

		$types = '\'' . implode('\',\'', $types) . '\'';

		$caches = $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:PermissionCache c
			WHERE c.name IN ($types) AND (c.usergroup_key = ?1 OR c.usergroup_key = ?2)
		")->setParameter(1, $usergroup_key)
		  ->setParameter(2, $key)
		  ->getResult();

		return $caches;
	}
}
