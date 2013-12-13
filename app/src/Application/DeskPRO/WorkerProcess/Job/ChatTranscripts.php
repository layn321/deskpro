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
 * @subpackage WorkerProcess
 */

namespace Application\DeskPRO\WorkerProcess\Job;

use Application\DeskPRO\App;
use Orb\Log\Logger;

class ChatTranscripts extends AbstractJob
{
	const DEFAULT_INTERVAL = 600;

	public function run()
	{
		$chat_ids = App::getDb()->fetchAllCol("
			SELECT id
			FROM chat_conversations
			WHERE should_send_transcript = 1 AND status = 'ended' AND ended_by != 'timeout' AND date_ended < ? AND date_ended > ?
		", array(date('Y-m-d H:i:s', time() - 300), date('Y-m-d H:i:s', time() - 18000)));

		if (!$chat_ids) {
			return;
		}

		App::getDb()->executeUpdate("
			UPDATE chat_conversations
			SET should_send_transcript = 0, date_transcript_sent = '" . date('Y-m-d H:i:s') . "'
			WHERE id IN (" . implode(',', $chat_ids) . ")
		");

		foreach ($chat_ids as $chat_id) {
			$chat = App::getOrm()->find('DeskPRO:ChatConversation', $chat_id);

			$email = '';
			$name = '';
			if ($chat->person && $chat->person->getPrimaryEmailAddress()) {
				$email = $chat->person->getPrimaryEmailAddress();
			} else if ($chat->person_email) {
				$email = $chat->person_email;
			}
			if ($chat->person && $chat->person->name) {
				$name = $chat->person->name;
			} else if ($chat->person_name) {
				$name = $chat->person_name;
			}

			if ($email) {
				$convo_messages = App::getOrm()->createQuery("
					SELECT m
					FROM DeskPRO:ChatMessage m
					WHERE m.conversation = ?1 AND m.is_user_hidden = false
					ORDER BY m.id DESC
				")->setParameter(1, $chat)->execute();

				$vars = array(
					'convo' => $chat,
					'convo_messages' => $convo_messages
				);

				$message = App::getMailer()->createMessage();
				$message->setTo($email, $name);
				$message->setTemplate('DeskPRO:emails_user:chat-transcript.html.twig', $vars);
				$message->setSuppressAutoreplies(true);
				App::getMailer()->send($message);

				// Add a chat log line for it
				App::getDb()->insert('chat_messages', array(
					'conversation_id' => $chat->getId(),
					'is_sys'          => 1,
					'is_user_hidden'  => 0,
					'is_html'         => 0,
					'metadata'        => serialize(array('phrase_id' => 'transcript_sent', 'email' => $email)),
					'date_created'    => date('Y-m-d H:i:s'),
				));
			}

			App::getOrm()->detach($chat);
			$chat = null;
		}

		$this->logger->log("Sent " . count($chat_ids) . " chat transcripts", Logger::INFO);
	}
}
