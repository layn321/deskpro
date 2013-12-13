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
use Application\DeskPRO\HttpFoundation\Session as HttpSession;

use Doctrine\ORM\EntityRepository;

class ClientMessage extends AbstractEntityRepository
{
	/**
	 * Get message data suitable to return
	 *
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @param \Application\DeskPRO\HttpFoundation\Session $session
	 * @param int $since
	 * @return array
	 */
	public function getMessageData(PersonEntity $person, HttpSession $session, $since = 0, $with_last_since = null, $is_initial = false)
	{
		$data = array('messages' => array(), 'last_id' => -1);
		$all_messages = false;

		if (!$since) {
			$last_id = $this->_em->getConnection()->fetchColumn("SELECT id FROM client_messages ORDER BY id DESC LIMIT 1");
			if ($last_id) {
				$data['last_id'] = $last_id;
			} else {
				$data['last_id'] = 1;
			}

		} else {
			$all_messages = $this->getMessagesForClient($session->getEntityId(), $person, $since);
		}

		if ($all_messages and $with_last_since) {
			$all_messages = array_merge($all_messages, $this->getInitialMessagesForPerson($person, $with_last_since));
		}

		if ($all_messages) {
			foreach ($all_messages as $message) {
				$handler = $message->getHandler();

				// Mesasge is a numeric array
				// 0 => id
				// 1 => channel
				// 2 => data
				// 3 => (optional) flags

				$msg_data = $handler->getMessage('ajax');
				$msg_data['from_client'] = $message['created_by_client'];

				$info = array(
					$message['id'],
					$message['channel'],
					$msg_data
				);

				if ($message['id'] < $since && $with_last_since) {
					$info[] = array(
						'offline_messsage' => true
					);
				}

				$data['messages'][] = $info;

				if ($message['id'] > $data['last_id']) {
					$data['last_id'] = $message['id'];
				}
			}
		}

		// If this is the first poll, check if there are any chats waiting to be taken and show those as alerts
		if ($is_initial && $person->is_agent) {
			$convos = $this->_em->getRepository('DeskPRO:ChatConversation')->getOpenForAgentAndDepartment(0, -1);
			foreach ($convos as $c) {
				$chatdata = array(
					'conversation_id'=> $c->getId(),
					'author_id' => $c->person ? $c->person->getId() : 0,
					'author_name' => $c->person ? $c->person->getDisplayName() : 0,
					'author_email' => $c->person ? $c->person->getEmailAddress() : 0,
					'subject_line' => 'Chat ' . $c->getId(),
					'agent_id' => 0,
					'agent_name' => '',
					'department_id' => $c->department ? $c->department->getId() : 0,
					'department_name' => $c->department ? $c->department->getTitle() : '',
					'date_created' => $c->date_created->getTimestamp()
				);

				$data['messages'][] = array(
					null,
					'chat.new',
					$chatdata
				);
			}
		}

		if ($data['last_id'] == -1) {
			unset($data['last_id']);
		}

		return $data;
	}

	/**
	 * Get messages for a client for specific channels
	 *
	 * @param  $client_id
	 * @param null $person_id
	 * @param array $channels
	 * @param null $since_id
	 * @return array|mixed
	 */
	public function getMessagesForClientInChannels($client_id, $person_id = null, array $channels, $since_id = null)
	{
		$names = array();
		$names_like = array();
		foreach ($channels as $ch) {
			$names[] = "'{$ch}'";
			$names_like[] = "m.channel LIKE '{$ch}.%'";
		}

		if (!$names) {
			return array();
		}

		$names = implode(',', $names);
		$names_like = implode(' OR ', $names_like);

		$params = array();

		$qb = $this->createQueryBuilder('m');
		$qb->select('m');
		$qb->where('m.channel IN (' . $names . ') OR ('. $names_like . ')');

		// Dont get our own messages, unless they're chat messages then we'll
		// handle them specially in the code. But we deliver them anyway to consolodate
		// some UI syncing based on return of AJAX requests
		$qb->andWhere('m.created_by_client != :n_created_by_client OR (m.channel LIKE \'chat.%\') OR (m.channel LIKE \'chat_convo.%\')');
		$params['n_created_by_client'] = $client_id;

		if ($person_id) {
			$qb->andWhere('m.for_client = :for_client OR m.for_person = :for_person OR (m.for_client IS NULL AND m.for_person IS NULL)');
			$params['for_client'] = $client_id;
			$params['for_person'] = $person_id;
		} else {
			$qb->andWhere('m.for_client = :for_client OR (m.for_client IS NULL AND m.for_person IS NULL)');
			$params['for_client'] = $client_id;
		}

		if ($since_id) {
			$qb->andWhere('m.id > :since_id');
			$params['since_id'] = $since_id;
		} else {
			$qb->setMaxResults(100);
		}

		$qb->orderBy('m.id', 'asc');

		return $qb->getQuery()->execute($params);
	}

	/**
	 * Gets the initials messages to send to the client after they load the interface, and request
	 * messages for the first time.
	 *
	 * This is like getMessagesForClientInChannels() except we check specifically for a person,
	 * and $since is an ID from the preference from the last one the user got.
	 *
	 * @param $person_id
	 * @param $since_id
	 * @return array
	 */
	public function getInitialMessagesForPerson($person_id, $since_id = null)
	{
		$qb = $this->createQueryBuilder('m');
		$qb->select('m');
		$qb->andWhere('m.for_person = :for_person');
		$params['for_person'] = $person_id;

		if ($since_id) {
			$qb->andWhere('m.id > :since_id');
			$params['since_id'] = $since_id;
		} else {
			$qb->setMaxResults(100);
		}

		$qb->orderBy('m.id', 'asc');

		return $qb->getQuery()->execute($params);
	}


	/**
	 * Get messages for a client based on their registered subscriptions
	 *
	 * @param string $client_id
	 * @param int|null $person_id
	 * @param int|null $since_id
	 * @return array
	 */
	public function getMessagesForClient($client_id, $person_or_id = null, $since_id = null)
	{
		$person = null;
		if ($person_or_id instanceof PersonEntity) {
			$person = $person_or_id;
		} elseif ($person_or_id) {
			$person = $this->_em->find('DeskPRO:Person', $person_or_id);
		}

		if ($person) {
			$person_id = $person->getId();
		} else {
			$person_id = 0;
		}

		$channels = array();

		// Implicit subscriptions
		if ($person && $person->is_agent && $since_id) {
			$channels[] = 'chat.new';
			$channels[] = 'chat.reassigned';
			$channels[] = 'chat.unassigned';
			$channels[] = 'chat.ended';

			$channels[] = 'agent_chat.new-message';
			$channels[] = 'agent.new-agent-online';

			$channels[] = 'agent-notification';
			$channels[] = 'agent-notify';
			$channels[] = 'agent-notify.tickets';
			$channels[] = 'agent-notify.tasks';
			$channels[] = 'agent.ticket-updated';
			$channels[] = 'agent.ticket-sla-updated';
			$channels[] = 'agent.ticket-draft-updated';
			$channels[] = 'agent.tweet-added';
			$channels[] = 'agent.tweet-updated';
			$channels[] = 'agent.twitter-follower';
			$channels[] = 'agent.twitter-friend';
			$channels[] = 'agent.ui.new-feedback';
			$channels[] = 'agent.ui.new-pending';

			$channels[] = 'agent.filter-update';
			$channels[] = 'chat.new';
			$channels[] = 'chat.reassigned';
			$channels[] = 'chat.reassigned';
			$channels[] = 'chat.unassigned';
			$channels[] = 'chat.ended';
			$channels[] = 'chat.depchange';
			$channels[] = 'chat.invited';
		}

		// They're automatically subscribed to their own chats of course
		$chat_ids = App::getDb()->fetchAllCol("
			SELECT c.id
			FROM chat_conversations c
			LEFT JOIN chat_conversation_to_person AS c2p ON c2p.conversation_id = c.id
			WHERE c.agent_id = ? OR c2p.person_id = ?
		", array($person_id, $person_id));

		foreach ($chat_ids as $chat_id) {
			$channels[] = 'chat_convo.' . $chat_id;
		}

		return self::getMessagesForClientInChannels($client_id, $person_id, $channels, $since_id);
	}
}
