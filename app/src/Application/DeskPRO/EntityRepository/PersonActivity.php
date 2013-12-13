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
use Application\DeskPRO\Entity\Person as PersonEntity;
use Application\DeskPRO\Entity\Organization as OrganizationEntity;

class PersonActivity extends AbstractEntityRepository
{
	public function getForPerson(PersonEntity $person, $max = 30, $offset = 0)
	{
		return $this->getEntityManager()->createQuery("
			SELECT a
			FROM DeskPRO:PersonActivity a
			WHERE a.person = ?1
			ORDER BY a.id DESC
		")->setMaxResults($max)->setFirstResult($offset)->execute(array(1=>$person));
	}

	public function countForPerson(PersonEntity $person)
	{
		return $this->getEntityManager()->getConnection()->fetchColumn('
			SELECT COUNT(*)
			FROM person_activity
			WHERE person_id = ?
		', array($person->id));
	}

	public function getForOrganization(OrganizationEntity $org, $max = 30, $offset = 0)
	{
		return $this->getEntityManager()->createQuery("
			SELECT a
			FROM DeskPRO:PersonActivity a
			LEFT JOIN a.person p
			WHERE p.organization = ?1
			ORDER BY a.id DESC
		")->setMaxResults($max)->setFirstResult($offset)->execute(array(1=>$org));
	}

	public function countForOrganization(OrganizationEntity $org)
	{
		return $this->getEntityManager()->getConnection()->fetchColumn('
			SELECT COUNT(*)
			FROM people
			INNER JOIN person_activity AS act ON (act.person_id = people.id)
			WHERE people.organization_id = ?
		', array($org->id));
	}
}
