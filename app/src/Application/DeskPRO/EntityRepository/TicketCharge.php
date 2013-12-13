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

class TicketCharge extends AbstractEntityRepository
{
	public function getChargesForPerson(\Application\DeskPRO\Entity\Person $person, $limit = null, $offset = null)
	{
		if ($limit !== null) {
			$limit = intval($limit);
			if ($limit < 1) {
				$limit = null;
			}
		}

		$q = $this->getEntityManager()->createQuery('
			SELECT tc
			FROM DeskPRO:TicketCharge tc INDEX BY tc.id
			WHERE tc.person = ?0
			ORDER BY tc.id DESC
		');

		if ($limit) {
			$q->setMaxResults($limit);
		}
		if ($offset) {
			$q->setFirstResult($offset);
		}

		return $q->execute(array($person));
	}

	public function getTotalChargesForPerson(\Application\DeskPRO\Entity\Person $person)
	{
		return App::getDb()->fetchAssoc('
			SELECT COUNT(*) AS count, SUM(charge_time) AS charge_time, SUM(amount) AS charge
			FROM ticket_charges
			WHERE person_id = ?
		', array($person->id));
	}

	public function getChargesForOrganization(\Application\DeskPRO\Entity\Organization $org, $limit = null, $offset = null)
	{
		if ($limit !== null) {
			$limit = intval($limit);
			if ($limit < 1) {
				$limit = null;
			}
		}

		$q = $this->getEntityManager()->createQuery('
			SELECT tc
			FROM DeskPRO:TicketCharge tc INDEX BY tc.id
			WHERE tc.organization = ?0
			ORDER BY tc.id DESC
		');

		if ($limit) {
			$q->setMaxResults($limit);
		}
		if ($offset) {
			$q->setFirstResult($offset);
		}

		return $q->execute(array($org));
	}

	public function getTotalChargesForOrganization(\Application\DeskPRO\Entity\Organization $org)
	{
		return App::getDb()->fetchAssoc('
			SELECT COUNT(*) AS count, SUM(charge_time) AS charge_time, SUM(amount) AS charge
			FROM ticket_charges
			WHERE organization_id = ?
		', array($org->id));
	}
}
