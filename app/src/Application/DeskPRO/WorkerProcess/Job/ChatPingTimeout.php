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
use Application\DeskPRO\Log\Logger;

/**
 * This cycles through chats and cleans up abandonded ones
 */
class ChatPingTimeout extends AbstractJob
{
	const DEFAULT_INTERVAL = 30; // 30 secs (though cron prolly only possible to do 1 min)

	public function run()
	{
		/** @var $chat_manager \Application\DeskPRO\Chat\UserChat\UserChatManager */
		$chat_manager = App::getSystemObject('user_chat_manager', array('session' => null));

		#------------------------------
		# Agent timeouts
		#------------------------------

		$cutoff = date('Y-m-d H:i:s', time() - 20); // 20 secs for agents

		// Agnets who we know are online
		$agent_ids = App::getDb()->fetchAllCol("
			SELECT sessions.person_id
			FROM sessions
			LEFT JOIN people ON people.id = sessions.person_id
			WHERE people.is_agent = 1 AND sessions.date_last > '$cutoff'
		");

		$agent_ids[] = 0;

		// Get all open chats belonging to agents who arent online/have timed out
		$timeouts = App::getDb()->fetchAllKeyValue("
			SELECT c.id, c.agent_id
			FROM chat_conversations c
			WHERE c.status = 'open' AND c.agent_id NOT IN (" . implode(',', $agent_ids) . ")
		");

		$count_agents = 0;
		foreach ($timeouts as $chat_id => $agent_id) {
			$chat = App::getEntityRepository('DeskPRO:ChatConversation')->find($chat_id);
			$agent = App::getEntityRepository('DeskPRO:Person')->find($agent_id);
			$chat_manager->agentTimeout($chat, $agent);

			$count_agents++;
			$this->logger->log("Agent {$agent->id} {$agent->display_name} timed out in chat {$chat->id}", Logger::INFO);
		}

		\Application\DeskPRO\Chat\UserChat\AvailableTrigger::update();

		#------------------------------
		# User timeouts
		#------------------------------

		$cutoff = time() - 50;

		$chat_ids = App::getDb()->fetchAllCol("
			SELECT DISTINCT c.id
			FROM chat_conversations c
			LEFT JOIN chat_conversation_pings AS p ON (p.chat_id = c.id AND p.ping_time > $cutoff)
			WHERE c.status = 'open' AND c.is_agent = 0 AND p.id IS NULL
		");

		$count_users = 0;
		while ($chat_id = array_pop($chat_ids)) {
			$chat = App::getEntityRepository('DeskPRO:ChatConversation')->find($chat_id);
			$chat_manager->userTimeout($chat);

			$count_users++;
			$this->logger->log("User timed out in chat {$chat->id}", Logger::INFO);
		}

		#------------------------------
		# Max waiting times
		#------------------------------

		$max_time = App::getSetting('core_chat.max_wait_time');
		$count_wait = 0;

		if ($max_time) {
			$timesnip = date('Y-m-d H:i:s', time() - $max_time);
			$chat_ids = App::getDb()->fetchAllCol("
				SELECT id
				FROM chat_conversations c
				WHERE c.status = 'open' AND c.date_user_waiting < ?
			", array($timesnip));

			while ($chat_id = array_pop($chat_ids)) {
				$chat = App::getEntityRepository('DeskPRO:ChatConversation')->find($chat_id);
				$chat_manager->waitTimeout($chat);

				$count_wait++;
				$secs = time() - $chat->date_user_waiting->getTimestamp();
				$this->logger->log("Wait timed out chat {$chat->id} (waiting $secs seconds)", Logger::INFO);
			}
		}

		#------------------------------
		# Abandoned chats after user timeout
		#------------------------------

		$max_time = App::getSetting('core_chat.abandoned_time');
		$count_abandoned = 0;

		if ($max_time) {
			$timesnip = date('Y-m-d H:i:s', time() - $max_time);
			$chat_ids = App::getDb()->fetchAllCol("
				SELECT id
				FROM chat_conversations c
				WHERE c.status = 'ended' AND c.ended_by = 'timeout' AND c.date_ended < ?
			", array($timesnip));

			while ($chat_id = array_pop($chat_ids)) {
				$chat = App::getEntityRepository('DeskPRO:ChatConversation')->find($chat_id);
				$chat_manager->userAbandoned($chat);

				$count_abandoned++;
				$secs = time() - $chat->date_ended->getTimestamp();
				$this->logger->log("Timed out user abandoned chat {$chat->id} (its been $secs seconds)", Logger::INFO);
			}
		}

		if ($count_agents || $count_users) {
			$this->logStatus("Chat timeouts: {$count_agents} agents, {$count_users} users, {$count_wait} wait", array(
				'count_agents' => $count_agents,
				'count_users'  => $count_users,
				'count_wait'   => $count_wait
			));
		}
	}
}
