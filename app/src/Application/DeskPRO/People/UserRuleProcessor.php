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
 */

namespace Application\DeskPRO\People;

use Application\DeskPRO\ORM\EntityManager;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;

class UserRuleProcessor
{
	/**
	 * @var \Application\DeskPRO\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @param \Application\DeskPRO\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * @param Person $person
	 */
	public function newRegister(Person $person)
	{
		$email = $person->getPrimaryEmail();

		if ($email) {
			$this->newEmail($person, $email);
		}
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param \Application\DeskPRO\Entity\PersonEmail $email
	 */
	public function newEmail(Person $person, PersonEmail $email)
	{
		$change = false;
		$email_address = $email->email;
		$domain = $email->getEmailDomain();

		$rules = $this->em->getRepository('DeskPRO:UserRule')->getMatching($email_address);
		if ($rules) {
			foreach ($rules as $r) {
				if ($r->add_usergroup) {
					$change = true;
					$person->addUsergroup($r->add_usergroup);
				}
				if ($r->add_organization && !$person->organization) {
					$change = true;
					$person->setOrganization($r->add_organization);
				}
			}
		}

		// And check orgs with domain assocs
		if (!$person->organization) {
			$orgem = $this->em->createQuery("
				SELECT od, org
				FROM DeskPRO:OrganizationEmailDomain od
				LEFT JOIN od.organization org
				WHERE od.domain = ?1
			")->setParameter(1, $domain)->setMaxResults(1)->getOneOrNullResult();

			if ($orgem) {
				$change = true;
				$person->setOrganization($orgem->organization);
			}

			if ($change) {
				$this->em->persist($person);
				$this->em->flush();
			}
		}
	}
}
