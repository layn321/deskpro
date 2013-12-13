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

class PersonUsersourceAssoc extends AbstractEntityRepository
{
	/**
	 * Finds the PersonUsersourceAssoc for a given identity.
	 * If no association exists, null is returend.
	 */
	public function getIdentityAssociation($usersource, $identity)
	{
		try {
			$assoc = $this->_em->createQuery("
				SELECT f, p
				FROM DeskPRO:PersonUsersourceAssoc f
				LEFT JOIN f.person p
				WHERE f.usersource = ?1 AND f.identity = ?2
			")->setParameter(1, $usersource)->setParameter(2, $identity)->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}

		return $assoc;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return \Application\DeskPRO\Entity\PersonUsersourceAssoc[]
	 */
	public function getAssociationsForPerson(\Application\DeskPRO\Entity\Person $person)
	{
		$associations = $this->_em->createQuery("
			SELECT assoc, us
			FROM DeskPRO:PersonUsersourceAssoc assoc
			LEFT JOIN assoc.usersource us
			WHERE assoc.person = ?0
		")->execute(array($person));

		return $associations;
	}
}
