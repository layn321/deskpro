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
 * @category ClientMessage
 */

namespace Application\DeskPRO\ClientMessage\Generator;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\ClientMessage;
use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;

class Chat
{
	public static function createNewChatMessages($by_client_id, ChatConversation $conversation, ChatMessage $chat_message)
	{
		if ($conversation['is_agent']) {
			$channel = 'agent_chat.new-chat';
		} else {
			$channel = 'chat.new-chat';
		}

		$new_chat_cm = new ClientMessage();
		$new_chat_cm->fromArray(array(
			'channel' => $channel,
			'data' => array(
				'conversation_id'   => $conversation['id'],
				'message_id'        => $chat_message['id'],
				'author_id'         => $chat_message['author_id'],
				'author_name'       => $chat_message['author_name'],
				'message'           => $chat_message['content'],
				'date_created'      => $chat_message['date_created']->getTimestamp()
			),
			'created_by_client' => $by_client_id
		));

		return array($new_chat_cm);
	}

	public static function createNewAddedPartMessage($by_client_id, ChatConversation $conversation, $agent)
	{
		$channel = 'chat_user_agent.added-as-part';

		$chat_message = $conversation->messages->get(0);

		$new_chat_cm = new ClientMessage();
		$new_chat_cm->fromArray(array(
			'channel' => $channel,
			'data' => array(
				'conversation_id'   => $conversation['id'],
				'message_id'        => $chat_message['id'],
				'author_id'         => $chat_message['author_id'],
				'author_name'       => $chat_message['author_name'],
				'message'           => $chat_message['content'],
				'date_created'      => $chat_message['date_created']->getTimestamp()
			),
			'created_by_client' => $by_client_id,
			'for_person'        => $agent
		));

		return array($new_chat_cm);
	}

	public static function createChatEndedMessages($by_client_id, ChatConversation $conversation)
	{
		$channel = 'chat.chat-ended';

		$chat_cm = new ClientMessage();
		$chat_cm->fromArray(array(
			'channel' => $channel,
			'data' => array(
				'conversation_id'   => $conversation['id'],
				'date_created'      => time()
			),
			'created_by_client' => $by_client_id
		));

		return array($chat_cm);
	}

	public static function createChatAssignedMessages($by_client_id, ChatConversation $conversation)
	{
		$client_messages = array();

		$chat_cm = new ClientMessage();

		$chat_message = $conversation->messages->get(0);

		// We only need to notify the one guy
		if ($conversation['agent']) {
			$chat_cm->fromArray(array(
				'channel' => 'chat.new-chat-assigned',
				'data' => array(
					'conversation_id'   => $conversation['id'],
					'message_id'        => $chat_message['id'],
					'author_id'         => $chat_message['author_id'],
					'author_name'       => $chat_message['author_name'],
					'message'           => $chat_message['content'],
					'date_created'      => $chat_message['date_created']->getTimestamp()
				),
				'created_by_client' => $by_client_id,
				'for_person' => $conversation['agent']
			));

		// Dispatch a 'new chat' type popup for everyone
		} else {
			$chat_cm = new ClientMessage();
			$chat_cm->fromArray(array(
				'channel' => 'chat.new-chat',
				'data' => array(
					'conversation_id'   => $conversation['id'],
					'message_id'        => $chat_message['id'],
					'author_id'         => $chat_message['author_id'],
					'author_name'       => $chat_message['author_name'],
					'message'           => $chat_message['content'],
					'date_created'      => $chat_message['date_created']->getTimestamp()
				),
				'created_by_client' => $by_client_id
			));
		}

		$client_messages[] = $chat_cm;

		// Dispatch a general message, so the interfaces that are beeping can
		// can hide the beep
		$chat_cm = new ClientMessage();
		$chat_cm->fromArray(array(
			'channel' => 'chat_user_agent.chat-assigned',
			'data' => array(
				'conversation_id'   => $conversation['id'],
				'agent_id' => $conversation['agent'] ? $conversation['agent']['id'] : 0
			),
			'created_by_client' => $by_client_id,
		));

		$client_messages[] = $chat_cm;

		// User should be notiifed too
		if (!$conversation['is_agent'] AND $conversation->session) {
			$chat_cm_user = new ClientMessage();
			$chat_cm_user->fromArray(array(
				'channel' => 'chat_user.chat-assigned',
				'data' => array(
					'conversation_id'   => $conversation['id'],
					'agent_id' => $conversation['agent'] ? $conversation['agent']['id'] : 0
				),
				'created_by_client' => $by_client_id,
				'for_client' => $conversation->session['id']
			));

			$client_messages[] = $chat_cm_user;
		}

		return $client_messages;
	}

	public static function createPartisipatedUpdatedMessages($by_client_id, ChatConversation $conversation)
	{
		$cm_data = array(
			'conversation_id' => $conversation->getId(),
			'agent_id' => $conversation['agent'] ? $conversation['agent']['id'] : 0,
			'participant_ids' => array()
		);

		foreach ($conversation->participants as $part) {
			$cm_data['participant_ids'][] = $part['id'];
		}

		$channel = 'chat_user_agent.chat-parts-updated';

		$cms = array();

		// Assigned agent
		if ($conversation->agent) {
			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $channel,
				'data' => $cm_data,
				'created_by_client' => $by_client_id,
				'for_person' => $conversation->agent
			));

			$cms[] = $cm;
		}

		// Participants first
		foreach ($conversation->participants as $part) {
			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $channel,
				'data' => $cm_data,
				'created_by_client' => $by_client_id,
				'for_person' => $part
			));

			$cms[] = $cm;
		}

		return $cms;
	}

	public static function createNewChatRoundRobinMessages($by_client_id, ChatConversation $conversation, ChatMessage $chat_message)
	{
		$new_chat_cm = new ClientMessage();
		$new_chat_cm->fromArray(array(
			'channel' => 'chat.new-chat-assigned',
			'data' => array(
				'conversation_id'   => $conversation['id'],
				'message_id'        => $chat_message['id'],
				'author_id'         => $chat_message['author_id'],
				'author_name'       => $chat_message['author_name'],
				'message'           => $chat_message['content'],
				'date_created'      => $chat_message['date_created']->getTimestamp()
			),
			'created_by_client' => $by_client_id,
			'for_person' => $conversation['agent']
		));

		return array($new_chat_cm);
	}

	public static function createNewMessageMessages($by_client_id, ChatMessage $chat_message, &$cm_data = null)
	{
		$conversation = $chat_message->conversation;

		if ($conversation['is_agent']) {
			$channel = 'agent_chat.message';
		} else {
			$channel = 'chat.message';
		}

		$author_type = 'user';
		if ($chat_message['is_sys']) {
			$author_type = 'sys';
		} elseif ($chat_message['author'] AND $chat_message['author']['is_agent']) {
			$author_type = 'agent';
		}

		$cm_data = array(
			'conversation_id'   => $conversation['id'],
			'message_id'        => $chat_message['id'],
			'author_id'         => $chat_message['author_id'],
			'author_name'       => $chat_message['author_name'],
			'author_type'       => $author_type,
			'message'           => $chat_message['content'],
			'date_created'      => $chat_message['date_created']->getTimestamp()
		);
		if ($chat_message['is_html']) {
			$cm_data['message_html'] = $chat_message['content'];
			unset($cm_data['message']);
		}

		$cms = array();

		// Assigned agent
		if ($conversation->agent) {
			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $channel,
				'data' => $cm_data,
				'created_by_client' => $by_client_id,
				'for_person' => $conversation->agent
			));

			$cms[] = $cm;
		}

		// Participants first
		foreach ($conversation->participants as $part) {
			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $channel,
				'data' => $cm_data,
				'created_by_client' => $by_client_id,
				'for_person' => $part
			));

			$cms[] = $cm;
		}

		// And the user
		if (!$conversation['is_agent'] AND !$chat_message['is_user_hidden']) {

			$session = $conversation->session;

			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $channel,
				'data' => $cm_data,
				'created_by_client' => $by_client_id,
				'for_client' => $session['id']
			));

			$cms[] = $cm;
		}

		return $cms;
	}

	public static function createUserTypingMessages($by_client_id, ChatConversation $conversation, $partial_message)
	{
		$cm_data = array(
			'conversation_id'   => $conversation['id'],
			'partial_message' => $partial_message
		);

		$channel = 'chat.user-typing';

		$cms = array();

		// Assigned agent
		if ($conversation->agent) {
			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $channel,
				'data' => $cm_data,
				'created_by_client' => $by_client_id,
				'for_person' => $conversation->agent
			));

			$cms[] = $cm;
		}

		// Participants first
		foreach ($conversation->participants as $part) {
			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $channel,
				'data' => $cm_data,
				'created_by_client' => $by_client_id,
				'for_person' => $part
			));

			$cms[] = $cm;
		}

		return $cms;
	}
}
