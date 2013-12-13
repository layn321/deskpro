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

namespace Application\DeskPRO\Chat\UserChat;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;
use Application\DeskPRO\ClientMessage\Generator\Chat as ChatClientMessageGenerator;

/**
 * Since we dont have an actual chat server that can keep track of clients timing out etc,
 * each party of the chat basically needs to keep track of one another.
 *
 * So an agent checks for a user timeout, or the user checks for an agent timeout.
 */
class StatusCheck
{
	/**
	 * @var \Application\DeskPRO\Entity\ChatConversation
	 */
	protected $conversation;
	protected $session;
	protected $person;

	protected $is_agent = false;

	public function __construct($conversation, $session)
	{
		$this->conversation = $conversation;
		$this->session = $session;

		if ($session->person) {
			$this->person = $session->person;
			if ($this->person['is_agent']) {
				$this->is_agent = true;
			}
		}
	}

	public function runChecks()
	{
		if ($this->is_agent) {
			$this->runChecksByAgents();
		} else {
			$this->runChecksByUser();
		}
	}

	/**
	 * These checks are done by the agent:
	 * - Check if user has timedout
	 *
	 * @return void
	 */
	public function runChecksByAgents()
	{
		// Get the users session
		$user_sess = $this->conversation->session;

		$cut_close = time() - App::getSetting('core_chat.user_timeout');
		if ($user_sess) {
			$last = $user_sess['date_last']->getTimestamp();
		} else {
			$last = 0;
		}

		if ($last < $cut_close) {

			$msg = $this->conversation->addSystemMessage(
				App::getTranslator()->phrase('agent.general.msg_user_timeout'),
				true
			);

			$this->conversation->setStatus('ended');
			$client_messages = ChatClientMessageGenerator::createChatEndedMessages(
				'sys',
				$this->conversation
			);
			foreach ($this->conversation->getCreatedMessages() as $msg) {
				$client_messages = array_merge($client_messages, ChatClientMessageGenerator::createNewMessageMessages('sys', $msg));
			}

			foreach ($client_messages as $cm) {
				App::getOrm()->persist($cm);
			}

			App::getOrm()->persist($this->conversation);
			App::getOrm()->flush();
		}
	}


	/**
	 * The checks run by the user:
	 * - Check if agent has tiemdout
	 *
	 * @return void
	 */
	public function runChecksByUser()
	{
		// Get the users session
		$user_sess = $this->conversation->session;

		$cut = time() - App::getSetting('core_chat.agent_timeout');
		$last = $user_sess['date_last']->getTimestamp();

		if ($last < $cut) {
			$msg = $this->conversation->addSystemMessage(
				App::getTranslator()->phrase('agent.general.msg_agent_timeout'),
				true
			);

			$client_messages = ChatClientMessageGenerator::createNewMessageMessages('sys', $msg);

			// And need to insert a "new chat" event for agents
			if (App::getSetting('core_chat.assign_mode') == 'round_robin') {

				$assign_agent = App::getEntityRepository('DeskPRO:Person')->getChatAgentRoundRobin();
				$conversation->agent = $assign_agent;

				$client_messages = array_merge($client_messages, ChatClientMessageGenerator::createNewChatRoundRobinMessages(
					'sys',
					$this->conversation,
					$msg
				));
			} else {

				$client_messages = array_merge($client_messages, ChatClientMessageGenerator::createNewChatMessages(
					'sys',
					$this->conversation,
					$msg
				));
			}

			foreach ($client_messages as $cm) {
				App::getOrm()->persist($cm);
			}

			App::getOrm()->persist($this->conversation);
			App::getOrm()->flush();
		}
	}
}
