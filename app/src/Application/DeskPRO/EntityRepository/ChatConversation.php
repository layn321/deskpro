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
use Application\DeskPRO\Entity\Visitor as VisitorEntity;
use Application\DeskPRO\Entity\ChatConversation as ChatConversationEntity;

use Orb\Util\Arrays;

class ChatConversation extends AbstractEntityRepository
{
	public function getOpenChatsForAgent(PersonEntity $person)
	{
		$chats = $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:ChatConversation c
			WHERE c.agent = ?1 AND c.status = 'open'
		")->setParameter(1, $person)->execute();

		return $chats;
	}

	/**
	 * Gets an array of agent_id=>num that counts how many chats they have open.
	 *
	 * @return array
	 */
	public function getOpenChatsForAgents()
	{
		$counts = App::getDb()->fetchAllKeyValue("
			SELECT agent_id, COUNT(*) AS cnt
			FROM chat_conversations
			WHERE status = ? AND is_agent = 0
			GROUP BY agent_id
		", array('open'));

		return $counts;
	}

	public function getConversationsForAgent($agent)
	{
		if ($agent == null) {
			$convos = $this->getEntityManager()->createQuery("
				SELECT c
				FROM DeskPRO:ChatConversation c
				WHERE c.status = ?1 AND c.agent IS NULL AND c.is_agent = false
				ORDER BY c.id DESC
			")->setParameter(1, 'open')->execute();
		} else {
			$convos = $this->getEntityManager()->createQuery("
				SELECT c
				FROM DeskPRO:ChatConversation c
				WHERE c.status = ?1 AND c.agent = ?2
				ORDER BY c.id DESC
			")->setParameter(1, 'open')->setParameter(2, $agent)->execute();
		}

		return $convos;
	}

	public function getOpenForAgentAndDepartment($agent, $department)
	{
		$params = array();

		$qb = $this->createQueryBuilder('c');
		$qb->orderBy('c.id', 'DESC');
		$qb->where('c.status = :status AND c.is_agent = false');
		$params['status'] = 'open';

		if (is_object($agent)) {
			$agent = $agent->id;
		}
		if (!$agent) {
			$qb->andWhere('c.agent IS NULL');
		} elseif ($agent == -1) {
			// no agent criteria
		} else {
			$qb->andWhere('c.agent = :agent');
			$params['agent'] = $agent;
		}

		if (is_object($department)) {
			$department = $department->id;
		}
		if ($department == 0) {
			$qb->andWhere('c.department IS NULL');
		} elseif ($department == -1) {
			// no dep criteria
		} else {
			$children = App::getDataService('Department')->getIdsInTree($department, true);
			$qb->andWhere('c.department IN (:dep)');
			$params['dep'] = $children;
		}

		return $qb->getQuery()->execute($params);
	}


	public function getAgentList($agent)
	{
		$agent_ids = App::getDb()->fetchAllCol("
			SELECT people.id
			FROM chat_conversation_to_person convo
			LEFT JOIN chat_conversation_to_person AS convo2 ON (convo2.conversation_id = convo.conversation_id)
			LEFT JOIN people ON (people.id = convo2.person_id)
			WHERE convo.person_id = {$agent['id']} AND people.is_agent = 1 AND people.id != {$agent['id']}
		");

		return App::getEntityRepository('DeskPRO:Person')->getPeopleFromIds($agent_ids);
	}

	public function getAgentTeamList($agent)
	{
		$agent_team_ids = App::getDb()->fetchAllCol("
			SELECT c.agent_team_id
			FROM chat_conversation_to_person convo
			LEFT JOIN chat_conversations c ON (c.id = convo.conversation_id)
			WHERE convo.person_id = {$agent['id']} AND c.agent_team_id IS NOT NULL
		");

		return App::getEntityRepository('DeskPRO:AgentTeam')->getByIds($agent_team_ids);
	}

	public function getAgentChatsForPerson($agent)
	{
		$convo_ids = App::getDb()->fetchAllCol("
			SELECT convo.conversation_id
			FROM chat_conversation_to_person convo
			LEFT JOIN chat_conversation_to_person AS convo2 ON (convo2.conversation_id = convo.conversation_id)
			LEFT JOIN people ON (people.id = convo2.person_id)
			WHERE convo.person_id = {$agent['id']} AND people.is_agent = 1 AND people.id != {$agent['id']}
			ORDER BY convo.conversation_id DESC
		");

		if (!$convo_ids) {
			return array();
		}

		return $this->getByIds($convo_ids, true);
	}

	public function getChatsForPeople(array $participant_ids)
	{
		$participant_ids = Arrays::removeFalsey($participant_ids);

		$person1 = $participant_ids[0];
		$person2 = $participant_ids[1];

		$sql = "
			SELECT convo.conversation_id
			FROM chat_conversation_to_person convo
			LEFT JOIN chat_conversation_to_person AS convo2 ON (convo2.conversation_id = convo.conversation_id)
			LEFT JOIN people ON (people.id = convo2.person_id)
			WHERE convo.person_id = $person1 AND convo2.person_id = $person2
		";

		$conversation_ids = App::getDb()->fetchAllCol($sql);

		if (!$conversation_ids) {
			return null;
		}

		$conversations = $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:ChatConversation c INDEX BY c.id
			WHERE c.id IN(" . implode(',', $conversation_ids) . ")
			ORDER BY c.id ASC
		")->execute();

		return $conversations;
	}

	public function getTeamChatsForPerson($agent, $agent_team = null)
	{
		if ($agent_team === null) {
			$convo_ids = App::getDb()->fetchAllCol("
				SELECT convo.conversation_id
				FROM chat_conversation_to_person convo
				LEFT JOIN chat_conversations c ON (c.id = convo.conversation_id)
				WHERE convo.person_id = {$agent['id']} AND c.agent_team_id IS NOT NULL
				ORDER BY convo.conversation_id
			");
		} else {
			$convo_ids = App::getDb()->fetchAllCol("
				SELECT convo.conversation_id
				FROM chat_conversation_to_person convo
				LEFT JOIN chat_conversations c ON (c.id = convo.conversation_id)
				WHERE convo.person_id = {$agent['id']} AND c.agent_team_id = {$agent_team['id']}
				ORDER BY convo.conversation_id
			");
		}

		return $this->getByIds($convo_ids, true);
	}


	/**
	 * Count how many chats there have been between a person, and someone else
	 *
	 * @param $person
	 * @param $person_ids
	 */
	public function getConvoCountsBetween($person, $person_ids)
	{
		$is_array = true;
		if (!is_array($person_ids)) {
			$is_array = false;
			$person_ids = array($person_ids);
		}

		$person_ids = Arrays::removeFalsey($person_ids);
		if (!$person_ids) {
			return $is_array ? array() : 0;
		}

		$sql = "
			SELECT convo2.person_id, COUNT(*)
			FROM chat_conversation_to_person convo
			LEFT JOIN chat_conversation_to_person AS convo2 ON (convo2.conversation_id = convo.conversation_id)
			WHERE convo.person_id = {$person->id} AND convo2.person_id IN (" . implode(',', $person_ids) . ")
			GROUP BY convo2.person_id
		";

		return App::getDb()->fetchAllKeyValue($sql);
	}

	public function getTeamConvoCounts($agent)
	{
		return App::getDb()->fetchAllKeyValue("
			SELECT c.agent_team_id, COUNT(*)
			FROM chat_conversation_to_person convo
			LEFT JOIN chat_conversations c ON (c.id = convo.conversation_id)
			WHERE convo.person_id = {$agent['id']} AND c.agent_team_id IS NOT NULL
			GROUP BY c.agent_team_id
		");
	}


	/**
	 * This fetches the latest conversation where all $participants participated, and only
	 * they participated. Usually this is used to find a private conversation between two people
	 * (for agent chats see the ChatController).
	 *
	 * @param array $participant_ids
	 * @param null $date_limit
	 * @return void
	 */
	public function getRecentForPeople(array $participant_ids, $date_limit = null)
	{
		if (count($participant_ids) < 2) {
			throw new \InvalidArgumentException('$participant_ids should be an array of at least two people');
		}

		if ($date_limit !== null AND !($date_limit instanceof \DateTime)) {
			$date_limit = new \DateTime($date_limit);
		}
		if ($date_limit) {
			$date_limit = $date_limit->format('Y-m-d H:i:s');
		}

		foreach ($participant_ids as &$pid) {
			if ($pid instanceof PersonEntity) {
				$pid = $pid->getId();
			}
		}

		$participant_ids = Arrays::removeFalsey($participant_ids);
		$participant_ids = array_unique($participant_ids);
		$count = count($participant_ids);

		if (!$count) {
			return null;
		}

		$sql = "
			SELECT c.id
			FROM chat_conversations c
			INNER JOIN chat_conversation_to_person p ON (p.conversation_id = c.id)
			" . ($date_limit ? "WHERE c.date_created > '$date_limit'" : '') . "
			GROUP BY c.id
			HAVING SUM(IF(p.person_id IN (" . implode(',', $participant_ids) . "), 1, 0)) = $count AND COUNT(*) = $count
			ORDER BY c.id DESC
			LIMIT 1
		";

		$conversation_id = App::getDb()->fetchColumn($sql);
		if (!$conversation_id) {
			return null;
		}

		return $this->find($conversation_id);
	}


	public function getActiveChatForVisitor($visitor)
	{
		try {
			$conversation = $this->getEntityManager()->createQuery("
				SELECT c
				FROM DeskPRO:ChatConversation c
				WHERE c.visitor = ?1
				ORDER BY c.id ASC
			")->setParameter(1, $visitor)->setMaxResults(1)->getSingleResult();
		} catch (\Exception $e) {
			$conversation = null;
		}

		return $conversation;
	}

	public function getPastChatsForVisitor($visitor)
	{
		return $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:ChatConversation c
			WHERE c.visitor = ?1 AND c.status = 'ended'
			ORDER BY c.id ASC
		")->setParameter(1, $visitor)->execute();
	}

	public function getPastChatsForPerson($person)
	{
		return $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:ChatConversation c INDEX BY c.id
			WHERE (c.person = ?1 OR c.person_email = ?2) AND c.status = 'ended'
			ORDER BY c.id ASC
		")->setParameter(1, $person)
		  ->setParameter(2, $person->getPrimaryEmailAddress())
		  ->execute();
	}

	public function getLatestChatForSession($session, $allow_timeout = false)
	{
		try {
			$conversation = $this->getEntityManager()->createQuery("
				SELECT c
				FROM DeskPRO:ChatConversation c
				WHERE c.session = ?1
				ORDER BY c.id DESC
			")->setParameter(1, $session)->setMaxResults(1)->getSingleResult();
		} catch (\Exception $e) {
			$conversation = null;
		}

		if ($conversation && $conversation['status'] != ChatConversationEntity::STATUS_OPEN) {
			if ($allow_timeout && $conversation['ended_by'] == 'timeout' && (time() - $conversation['date_ended']->getTimestamp() < 1800)) {
			} else {
				$conversation = null;
			}
		}

		return $conversation;
	}


	public function getRecentForOrganization(OrganizationEntity $org)
	{
		$chats = $this->getEntityManager()->createQuery("
			SELECT c
			FROM DeskPRO:ChatConversation c
			LEFT JOIN c.person p
			WHERE p.organization = ?0 AND c.is_agent = false
			ORDER BY c.id DESC
		")->execute(array($org));

		return $chats;
	}

	public function getCountForOrganization(OrganizationEntity $org)
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM chat_conversations
			LEFT JOIN people ON chat_conversations.person_id = people.id
			WHERE people.organization_id = ? AND chat_conversations.is_agent = 0
		", array($org->getId()));

		return $chats;
	}

	public function getCountForPerson(PersonEntity $person)
	{
		return App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM chat_conversations
			WHERE person_id = ? AND is_agent = 0
		", array($person->getId()));
	}
}
