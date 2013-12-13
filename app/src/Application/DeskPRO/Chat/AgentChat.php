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
 * @subpackage Chat
 */

namespace Application\DeskPRO\Chat;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;
use Application\DeskPRO\Entity\ClientMessage;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Session;

/**
 * Actions to do with AgentChat.
 * See the AgentChatController for more, there are some more that need to be decoupled.
 */
class AgentChat
{
	protected $person;
	protected $session;
	protected $suppress_offline_email = false;

	public function __construct(Person $person, Session $session)
	{
		$this->person = $person;
		$this->session = $session;
	}

	public function disableOfflineEmailAlert()
	{
		$this->suppress_offline_email = true;
	}

	public function sendMessage($message, $conversation)
	{
		if (! ($conversation instanceof ChatConversation)) {
			$conversation = App::findEntity('DeskPRO:ChatConversation', $conversation);
		}

		$chat_message = $conversation->addNewMessage(
			$message,
			$this->person
		);

		$client_messages = array();
		$channel = 'chat.message';
		if ($conversation['is_agent']) {
			$channel = 'agent_chat.new-message';
		}

		$part_ids = array();
		foreach ($conversation->participants as $part) {
			$part_ids[] = $part['id'];
		}

		foreach ($conversation->participants as $part) {
			if ($part['id'] == $this->person['id']) {
				continue;
			}

			$date = clone $chat_message['date_created'];
			$date->setTimeZone(App::getCurrentPerson()->getDateTimezone());
			$time = App::getContainer()->getTranslator()->date('g:ia', $date, 'agent.time');

			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $channel,
				'data' => array(
					'conversation_id'   => $conversation['id'],
					'participant_ids'   => $part_ids,
					'message_id'        => $chat_message['id'],
					'author_id'         => $chat_message->author['id'],
					'message'           => $chat_message['content'],
					'date_created'      => $chat_message['date_created']->getTimestamp(),
					'time'              => $time
				),
				'created_by_client' => $this->session['id'],
				'for_person' => $part
			));

			$client_messages[] = $cm;
		}

		App::getOrm()->transactional(function ($em) use ($conversation, $client_messages) {
			$em->persist($conversation);

			if ($client_messages) {
				foreach ($client_messages as $cm) {
					$em->persist($cm);
				}
			}

			$em->flush();
		});

		// If any of the targets are not online, we might need to nofigy them of the message via email
		if (!$this->suppress_offline_email && !$chat_message->is_sys) {
			foreach ($conversation->participants as $part) {
				if ($part['id'] == $this->person['id']) {
					continue;
				}
				$session = App::getOrm()->getRepository('DeskPRO:Session')->getSessionForPerson($part, 30);

				if (!$session && $part->getPref('agent_notif.chat_message.email')) {
					$email_message = App::getMailer()->createMessage();
					$email_message->setTemplate('DeskPRO:emails_agent:new-agent-chat-message.html.twig', array(
						'message' => $chat_message
					));
					$email_message->setToPerson($part);
					$email_message->enableQueueHint();
					App::getMailer()->send($email_message);
				}
			}
		}

		return array(
			'conversation' => $conversation,
			'new_message'  => $chat_message
		);
	}

	public function sendAgentMessage($message, array $agent_ids, $convo_id = 0)
	{
		$em = App::getOrm();

		$agent_ids = App::getContainer()->getAgentData()->confirmAgentIds($agent_ids);

		$conversation = null;
		if ($convo_id) {
			$conversation = $em->find('DeskPRO:ChatConversation', $convo_id);
			if ($conversation AND !$conversation->hasParticipant($this->person)) {
				// invalid convo if we're not part of it
				// sneaky hobitses
				$conversation = null;
			}
		}

		// Try to find an existing convo
		if (!$conversation) {
			$date_cut = new \DateTime('-5 hours');

			$find_agent_ids = $agent_ids;
			$find_agent_ids[] = $this->person['id'];

			$conversation = App::getEntityRepository('DeskPRO:ChatConversation')->getRecentForPeople($find_agent_ids, $date_cut);
		}

		if (!$conversation) {
			$conversation = new ChatConversation();
			$conversation['is_agent'] = true;
			$conversation->addParticipant($this->person);
			foreach ($agent_ids as $aid) {
				$conversation->addParticipant($aid);
			}
		}

		if (!$conversation || !count($conversation->participants)) {
			return null;
		}

		$em->beginTransaction();
		$em->persist($conversation);
		$em->flush();
		$res = $this->sendMessage($message, $conversation);
		$em->commit();

		return $res;
	}
}
