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
 * @category Mail
 */

namespace Application\DeskPRO\Organizations;

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\Organization;
use Application\DeskPRO\Entity\OrganizationEmailDomain;

use Doctrine\ORM\EntityManager;

use Orb\Util\Strings;
use Orb\Util\Util;

class EmailDomainManager
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->db = $em->getConnection();
	}

	/**
	 * Check to see if a domain is currently being used by soem other company.
	 *
	 * @param $domain
	 * @return bool
	 */
	public function isInUse($domain)
	{
		$orgdomain = $this->em->getRepository('DeskPRO:OrganizationEmailDomain')->findByDomain($domain);
		if ($orgdomain) {
			return $orgdomain->organization;
		}

		return false;
	}

	/**
	 * Assign a domain to an org
	 */
	public function assignDomain($domain, Organization $org)
	{
		$domain = ltrim($domain, '@');

		$orgdomain = new OrganizationEmailDomain();
		$orgdomain->domain = $domain;
		$orgdomain->organization = $org;

		$this->em->beginTransaction();
		try {
			$this->em->persist($orgdomain);
			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $orgdomain;
	}

	public function moveNonCompanyUsers(OrganizationEmailDomain $orgdomain)
	{
		$this->em->beginTransaction();
		try {

			// Update tickets
			$this->db->executeUpdate("
				UPDATE tickets
				LEFT JOIN people ON (people.id = tickets.person_id)
				LEFT JOIN people_emails ON (people_emails.person_id = people.id)
				SET tickets.organization_id = ?
				WHERE people.organization_id IS NULL AND people_emails.email_domain = ?
			", array($orgdomain->organization->id, $orgdomain->domain));

			$this->db->executeUpdate("
				UPDATE tickets_search_active
				LEFT JOIN people ON (people.id = tickets_search_active.person_id)
				LEFT JOIN people_emails ON (people_emails.person_id = people.id)
				SET tickets_search_active.organization_id = ?
				WHERE people.organization_id IS NULL AND people_emails.email_domain = ?
			", array($orgdomain->organization->id, $orgdomain->domain));

			$count = $this->db->executeUpdate("
				UPDATE people
				LEFT JOIN people_emails ON (people_emails.person_id = people.id)
				SET people.organization_id = ?
				WHERE people.organization_id IS NULL AND people_emails.email_domain = ?
			", array($orgdomain->organization->id, $orgdomain->domain));

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $count;
	}

	public function moveOtherCompanyUsers(OrganizationEmailDomain $orgdomain)
	{
		$this->em->beginTransaction();
		try {
			$this->db->executeUpdate("
				UPDATE tickets
				LEFT JOIN people ON (people.id = tickets.person_id)
				LEFT JOIN people_emails ON (people_emails.person_id = people.id)
				SET tickets.organization_id = ?
				WHERE people.organization_id IS NOT NULL AND people_emails.email_domain = ?
			", array($orgdomain->organization->id, $orgdomain->domain));

			$this->db->executeUpdate("
				UPDATE tickets_search_active
				LEFT JOIN people ON (people.id = tickets_search_active.person_id)
				LEFT JOIN people_emails ON (people_emails.person_id = people.id)
				SET tickets_search_active.organization_id = ?
				WHERE people.organization_id IS NOT NULL AND people_emails.email_domain = ?
			", array($orgdomain->organization->id, $orgdomain->domain));

			$count = $this->db->executeUpdate("
				UPDATE people
				LEFT JOIN people_emails ON (people_emails.person_id = people.id)
				SET people.organization_id = ?
				WHERE people.organization_id IS NOT NULL AND people_emails.email_domain = ?
			", array($orgdomain->organization->id, $orgdomain->domain));

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $count;
	}

	public function unassignDomain($orgdomain, $remove_users = false)
	{
		$this->em->beginTransaction();

		try {
			$count = 0;
			if ($remove_users) {
				$this->db->executeUpdate("
					UPDATE tickets
					LEFT JOIN people ON (people.id = tickets.person_id)
					LEFT JOIN people_emails ON (people_emails.person_id = people.id)
					SET tickets.organization_id = ?
					WHERE people.organization_id = ? AND people_emails.email_domain = ?
				", array($orgdomain->organization->id, $orgdomain->domain));

				$this->db->executeUpdate("
					UPDATE tickets_search_active
					LEFT JOIN people ON (people.id = tickets_search_active.person_id)
					LEFT JOIN people_emails ON (people_emails.person_id = people.id)
					SET tickets_search_active.organization_id = ?
					WHERE people.organization_id = ? AND people_emails.email_domain = ?
				", array($orgdomain->organization->id, $orgdomain->domain));

				$count = $this->db->executeUpdate("
					UPDATE people
					LEFT JOIN people_emails ON (people_emails.person_id = people.id)
					SET people.organization_id = NULL
					WHERE people.organization_id = ? AND people_emails.email_domain = ?
				", array($orgdomain->organization->id, $orgdomain->domain));
			}

			$this->em->remove($orgdomain);
			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $count;
	}
}
