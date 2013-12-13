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
use Application\DeskPRO\Entity\Organization as OrganizationEntity;

use Orb\Util\Numbers;

class OrganizationEmailDomain extends AbstractEntityRepository
{
	/**
	 * Get a org domain object by the domain
	 *
	 * @param $domain
	 * @return \Application\DeskPRO\Entity\OrganizationEmailDomain
	 */
	public function findByDomain($domain)
	{
		return $this->getEntityManager()->createQuery("
			SELECT d
			FROM DeskPRO:OrganizationEmailDomain d
			WHERE d.domain = ?1
		")->setParameter(1, $domain)->getOneOrNullResult();
	}

	public function getDomainsForOrganization(OrganizationEntity $org)
	{
		$domains = App::getDb()->fetchAllCol("
			SELECT domain
			FROM organization_email_domains
			WHERE organization_id = ?
		", array($org->id));

		return $domains;
	}

	/**
	 * Count the number of emails that belong to a domain but arent members of an org.
	 *
	 * @param Organization|int $org
	 * @param array|string $domains
	 * @return array|int
	 */
	public function countNonMembersAtDomains($org, $domains)
	{
		return $this->_getCounts($org, $domains, '!=');
	}


	/**
	 * Count the number of emails that belong to a domain and are members of an org.
	 *
	 * @param Organization|int $org
	 * @param array|string $domains
	 * @return array|int
	 */
	public function countMembersAtDomains($org, $domains)
	{
		return $this->_getCounts($org, $domains, '=');
	}

	protected function _getCounts($org, $domains, $op)
	{
		if (!$domains) {
			if (is_array($domains)) {
				return array();
			} else {
				return 0;
			}
		}

		$org_id = is_object($org) ? $org->id : $org;

		$single = false;
		if (!is_array($domains)) {
			$domains = array($domains);
			$single = true;
		}

		// Init all to zero
		$results = array_combine($domains, array_fill(0, count($domains), 0));

		if (!$domains) {
			if ($is_single) {
				return 0;
			} else {
				return $results;
			}
		}

		$domains = App::getDb()->quoteIn($domains);

		$results = array_merge($results, App::getDb()->fetchAllKeyValue("
			SELECT people_emails.email_domain, COUNT(DISTINCT people.id) as count
			FROM people_emails
			LEFT JOIN people ON (people.id = people_emails.person_id)
			WHERE people_emails.email_domain IN ($domains) AND people.organization_id $op ?
			GROUP BY people_emails.email_domain
		", array($org_id)));

		if ($single) {
			return array_pop($results);
		}

		return $results;
	}
}
