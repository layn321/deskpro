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

use Application\DeskPRO\Entity\Session as SessionEntity;
use Application\DeskPRO\Entity\Person as PersonEntity;
use Application\DeskPRO\Entity\Visitor as VisitorEntity;
use \Doctrine\ORM\EntityRepository;
use Orb\Util\Util;

class Session extends AbstractEntityRepository
{
	/**
	 * Checks for active sessions (with standard chat timeout) for agents
	 * that have their status to available
	 */
	public function hasAvailableAgents($for_chat = false)
	{
		$datecut = date('Y-m-d H:i:s', time() - App::getSetting('core_chat.agent_timeout'));

		$check = App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM sessions
			WHERE date_last >= ? AND active_status = ? AND is_person = 1 " . ($for_chat ? " AND is_chat_available = 1 " : '') . "
			LIMIT 1
		", array($datecut, 'available'));

		return $check;
	}


	/**
	 * Get an array of agent IDs
	 *
	 * @return array
	 */
	public function getAvailableAgentIds()
	{
		$datecut = date('Y-m-d H:i:s', time() - App::getSetting('core_chat.agent_timeout'));

		$ids = App::getDb()->fetchAllCol("
			SELECT DISTINCT(sessions.person_id)
			FROM sessions
			LEFT JOIN people ON (people.id = sessions.person_id)
			WHERE sessions.date_last >= ? AND sessions.is_chat_available = 1 AND people.is_agent = 1
		", array($datecut));

		if (App::getCurrentPerson() && App::getCurrentPerson()->is_agent) {
			\Orb\Util\Arrays::pushUnique($ids, App::getCurrentPerson()->getId());
		}

		return $ids;
	}


	/**
	 * @return Session
	 */
	public function getSessionFromCode($sess_code)
	{
		$session_id = SessionEntity::getIdFromCode($sess_code);
		if (!$session_id) {
			return null;
		}

		$session = $this->getEntityManager()->createQuery("
			SELECT session, person, pic, email, vis, org, org_pic, lang
			FROM DeskPRO:Session session

			LEFT JOIN session.person person
			LEFT JOIN session.visitor vis

			LEFT JOIN person.picture_blob pic
			LEFT JOIN person.primary_email email

			LEFT JOIN person.organization org
			LEFT JOIN org.picture_blob org_pic

			LEFT JOIN person.language lang

			WHERE session.id = :id
		")->setParameters(array('id' => $session_id))->getOneOrNullResult();

		$session = $this->find($session_id);
		if (!$session OR !$session->checkSessionCode($sess_code)) {
			return null;
		}

		return $session;
	}


	/**
	 * Find an active session that is tied to a visitor.
	 *
	 * @param  $visitor
	 * @return Session
	 */
	public function getSessionFromVisitor(VisitorEntity $visitor)
	{
		$session = $this->getEntityManager()->createQuery("
			SELECT s
			FROM DeskPRO:Session s
			WHERE s.visitor = ?1
			ORDER BY s.id DESC
		")->setParameter(1, $visitor)->setMaxResults(1)->execute();

		if (!count($session)) {
			return null;
		}

		return $session[0];
	}


	/**
	 * Get the latest active session for a particular user
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param integer|null $offset Max number of seconds old the session's last page can be (null for session lifetime)
	 */
	public function getSessionForPerson(PersonEntity $person, $offset = null)
	{
		if ($offset === null) {
			$offset = App::getSetting('core.sessions_lifetime');
		}
		$datecut = date('Y-m-d H:i:s', time() - $offset);

		return $this->getEntityManager()->createQuery("
			SELECT s
			FROM DeskPRO:Session s
			LEFT JOIN s.visitor v
			WHERE s.person = ?1 AND s.date_last > ?2
			ORDER BY s.id
		")->setMaxResults(1)
		  ->setParameter(1, $person)
		  ->setParameter(2, $datecut)
		  ->getOneOrNullResult();
	}


	/**
	 * Count online users
	 *
	 * @return int
	 */
	public function countOnlineUsers()
	{
		return $this->_em->getConnection()->fetchColumn("
			SELECT COUNT(DISTINCT sessions.visitor_id)
			FROM sessions
			LEFT JOIN people ON (people.id = sessions.person_id)
			WHERE sessions.date_last > ? AND sessions.is_helpdesk = 1 AND (people.id IS NULL OR people.is_agent = 0) AND sessions.is_bot = 0
		", array(date('Y-m-d H:i:s', time() - App::getSetting('core.sessions_lifetime'))));
	}
}
