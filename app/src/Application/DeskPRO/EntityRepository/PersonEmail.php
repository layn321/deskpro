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

class PersonEmail extends AbstractEntityRepository
{
	public function getEmail($email_address)
	{
		return $this->getEntityManager()->createQuery("
			SELECT e
			FROM DeskPRO:PersonEmail e
			WHERE e.email = ?1
		")->setParameters(array(1=> $email_address))->setMaxResults(1)->getOneOrNullResult();

	}


	/**
	 * Count the number of email addresses at one or more arrays.
	 *
	 * @param array|string $domains
	 * @return array|int
	 */
	public function countDomains($domains)
	{
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

		$domains = App::getDb()->quoteIn($d);

		$results = array_merge($results, App::getDb()->fetchAllKeyValue("
			SELECT email_domain, COUNT(*) as count
			FROM email_domain
			WHERE email_domain IN ($domains)
			GROUP BY email_domain
		"));

		if ($single) {
			return array_pop($results);
		}

		return $results;
	}


	/**
	 * Count the number of email addresses at one or more emails where the user belongs to a company
	 * that isnt this one.
	 *
	 * @param array|string $domains
	 * @return array|int
	 */
	public function countDomainsWithOtherCompany($domains, $org)
	{
		$single = false;

		if (!$domains) {
			if (!is_array($domains)) {
				return 0;
			} else {
				return array();
			}
		}

		if (!is_array($domains)) {
			$domains = array($domains);
			$single = true;
		}

		// Init all to zero
		$results = array_combine($domains, array_fill(0, count($domains), 0));

		$domains = App::getDb()->quoteIn($domains);

		$org = is_object($org) ? $org->id : $org;

		$results = array_merge($results, App::getDb()->fetchAllKeyValue("
			SELECT people_emails.email_domain, COUNT(DISTINCT people.id) as count
			FROM people_emails
			LEFT JOIN people ON (people.id = people_emails.person_id)
			WHERE people_emails.email_domain IN ($domains) AND people.organization_id != ? AND people.organization_id IS NOT NULL
			GROUP BY people_emails.email_domain
		", array($org)));

		if ($single) {
			return array_pop($results);
		}

		return $results;
	}

	/**
	 * Count the number of email addresses at one or more emails where the user does not
	 * belong to any company.
	 *
	 * @param array|string $domains
	 * @return array|int
	 */
	public function countDomainsWithNoCompany($domains)
	{
		if (!$domains) {
			if (!is_array($domains)) {
				return 0;
			} else {
				return array();
			}
		}

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
			WHERE people_emails.email_domain IN ($domains) AND people.id IS NOT NULL AND people.organization_id IS NULL
			GROUP BY people_emails.email_domain
		"));

		if ($single) {
			return array_pop($results);
		}

		return $results;
	}
}
