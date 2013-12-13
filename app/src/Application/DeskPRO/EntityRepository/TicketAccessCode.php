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
use Application\DeskPRO\Entity;
use \Doctrine\ORM\EntityRepository;

use Orb\Util\Numbers;

class TicketAccessCode extends AbstractEntityRepository
{
	public function findByAccessCode($access_code)
	{
		$info = Entity\TicketAccessCode::decodeAccessCode($access_code);
		if (!$info) {
			return null;
		}

		try {
			$rec = $this->getEntityManager()->createQuery("
				SELECT tac
				FROM DeskPRO:TicketAccessCode tac
				WHERE tac.id = :access_code_id AND tac.auth = :auth
			")->setParameters($info)->setMaxResults(1)->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}

		return $rec;
	}

	public function getTacArrayFromAccessCode($access_code)
	{
		$info = Entity\TicketAccessCode::decodeAccessCode($access_code);
		if (!$info) {
			return null;
		}

		$tac = App::getDb()->fetchAssoc("
			SELECT *
			FROM ticket_access_codes
			WHERE id = ? AND auth = ?
		", array($info['access_code_id'], $info['auth']));

		if (!$tac) {
			return null;
		}

		return $tac;
	}

	public function findByTicketAndPerson($ticket, $person)
	{
		if (!$person->id || !$ticket->id) {
			return null;
		}
		try {
			$rec = $this->getEntityManager()->createQuery("
				SELECT tac
				FROM DeskPRO:TicketAccessCode tac
				WHERE tac.ticket = ?1 AND tac.person = ?2
			")->setParameters(array(1=>$ticket, 2=>$person))->setMaxResults(1)->getSingleResult();

			return $rec;
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
}
