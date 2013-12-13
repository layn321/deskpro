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
*/

namespace Application\DeskPRO\Chat\UserChat;

use Application\DeskPRO\App;
use Orb\Util\Util;

use Application\DeskPRO\Entity\CustomDefAbstract;
use Doctrine\ORM\EntityManager;
use Application\DeskPRO\Translate\Translate;

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Department;
use Application\DeskPRO\Entity\Session;
use Application\DeskPRO\Entity\Visitor;
use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;
use Application\DeskPRO\Entity\ClientMessage;

use Application\DeskPRO\ClientMessage\Generator\Chat as ChatClientMessageGenerator;
use Application\DeskPRO\Chat\StatusCheck as ChatStatusCheck;
use Orb\Validator\StringEmail;

/**
 * Manages interactions between users, agents and the server.
 */
class UserChatManager
{
	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\Translate\Translate
	 */
	protected $tr;

	/**
	 * @var \Application\DeskPRO\Entity\Session
	 */
	protected $session;

	/**
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Chat\UserChat\AutoAssigner
	 */
	protected $auto_assigner;

	public function __construct(Session $session = null, EntityManager $em, Translate $translate)
	{
		$this->em = $em;
		$this->db = $em->getConnection();
		$this->tr = $translate;

		if ($session) {
			$this->session     = $session;
			$this->visitor     = $session->getVisitor();
			$this->person      = $session->getPerson();
		}
	}


	/**
	 * Set the auto-assigner
	 *
	 * @param $assigner
	 * @return void
	 */
	public function setAutoAssigner(AutoAssigner $assigner)
	{
		$this->auto_assigner = $assigner;
	}


	/**
	 * Start a new chat conversation, or if its within time and sitll open, resume the previous.
	 *
	 * @return \Application\DeskPRO\Entity\ChatConversation|null
	 */
	public function startChat(array $chat_options, $is_window_mode = false, &$error_code = false)
	{
		$convo = $this->em->getRepository('DeskPRO:ChatConversation')->getLatestChatForSession($this->session);

		$is_new_convo = false;
		$new_person = false;
		if (!$convo) {
			$convo = new ChatConversation();
			$convo->session = $this->session;
			$convo->visitor = $this->visitor;
			if ($this->person) {
				$convo->person = $this->person;
			}

			if (!empty($chat_options['department_id'])) {
				$dep = $this->em->getRepository('DeskPRO:Department')->find($chat_options['department_id']);
				if ($dep) {
					$convo->department = $dep;
				}
			}

			$chat_options['name']  = empty($chat_options['name']) ? '' : $chat_options['name'];
			$chat_options['email'] = empty($chat_options['email']) ? '' : $chat_options['email'];

			// Mixed up name/email boxes
			if ($chat_options['name'] && $chat_options['email'] && StringEmail::isValueValid($chat_options['name']) && !StringEmail::isValueValid($chat_options['email'])) {
				$tmp = $chat_options['email'];
				$chat_options['email'] = $chat_options['name'];
				$chat_options['name']  = $tmp;
			// Put email into name box
			} elseif ($chat_options['name'] && !$chat_options['email'] && StringEmail::isValueValid($chat_options['name'])) {
				$chat_options['email'] = $chat_options['name'];
				$chat_options['name']  = '';
			}

			if (!empty($chat_options['name'])) {
				$convo->person_name = $chat_options['name'];
			}
			if (!empty($chat_options['email']) && \Orb\Validator\StringEmail::isValueValid($chat_options['email']) && !App::getSystemService('gateway_address_matcher')->isManagedAddress($chat_options['email'])) {
				$convo->person_email = $chat_options['email'];

				$related_person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($chat_options['email']);
				if ($related_person) {
					$convo->person = $related_person;
				} else {
					$new_person = Person::newContactPerson();
					if ($convo->person_name) {
						$new_person->name = $convo->person_name;
					}
					$new_person->setEmail($convo->person_email);
					$convo->person = $new_person;
				}
			}
			$is_new_convo = true;

			if ($convo->person && $convo->person->is_disabled) {
				$error_code = 'person_disabled';
				return null;
			}

			// Update the visitor name/email while we have a chance,
			// its used elsewhere and stays for a long time
			if ($this->visitor) {
				if ($convo->person_name) {
					$this->visitor->name = $convo->person_name;
				}
				if ($convo->person_email) {
					$this->visitor->email = $convo->person_email;
				}
				if ($convo->person) {
					$this->visitor->person = $convo->person;
				}
			}
		}

		if ($is_window_mode) {
			$convo['is_window'] = true;
		}

		$this->em->beginTransaction();

		try {

			if ($new_person) {
				$this->em->persist($new_person);
				$this->em->flush();
			}

			$this->em->persist($convo);
			if ($this->visitor) {
				$this->em->persist($this->visitor);
			}
			$this->em->flush();

			if (isset($chat_options['chat_fields']) && is_array($chat_options['chat_fields']) && !empty($chat_options['chat_fields'])) {
				$field_manager = App::getSystemService('chat_fields_manager');
				if ($chat_options['chat_fields']) {
					$field_manager->saveFormToObject($chat_options['chat_fields'], $convo);
				}
			}

			if ($is_new_convo) {
				$this->addSystemMessage($convo, 'message_started', array(), array(
					'user_hidden' => true,
					'is_html' => false,
				));
				if (isset($_GET['parent_url']) && is_string($_GET['parent_url'])) {
					$this->addUserTrack($convo, $_GET['parent_url']);
				} else {
					$url = $this->visitor->getLastPage();
					if ($k = strpos($url, 'parent_url=')) {
						$str = substr($url, $k);
						$vars = null;
						@parse_str($str, $vars);

						if (!empty($vars['parent_url']) && is_string($vars['parent_url'])) {
							$url = $vars['parent_url'];
						}
					}

					$this->addUserTrack($convo, $url);
				}
			}

			if (!$convo->agent && $this->auto_assigner) {
				$assign_agent = $this->auto_assigner->getAgent($convo);
				if ($assign_agent) {
					$this->assignAgent($convo, $assign_agent);
				}
			}

			$this->em->flush();

			if (isset($chat_options['content']) && $chat_options['content']) {
				$this->addUserMessage($convo, $chat_options['content']);
				$newchat_cm_data['initial_message'] = $chat_options['content'];

				$this->em->flush();
			}

			if ($is_new_convo) {
				$newchat_cm_data = $convo->getInfo();

				$cm = new ClientMessage();
				$cm->fromArray(array(
					'channel' => 'chat.new',
					'data' => $newchat_cm_data,
					'created_by_client' => $this->getCurrentClientId(),
				));

				$this->em->persist($cm);
				$this->em->flush();
			}

			if ($convo->person) {
				$action = new \Application\DeskPRO\People\ActivityLogger\ActionType\NewChat($convo->person, $convo);
				App::getPersonActivityLogger()->saveAction($action);
			}

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $convo;
	}


	/**
	 * Get an open chat for the users session
	 *
	 * @return \Application\DeskPRO\Entity\ChatConversation
	 */
	public function getChat($allow_timeout = false)
	{
		$convo = $this->em->getRepository('DeskPRO:ChatConversation')->getLatestChatForSession($this->session, $allow_timeout);
		return $convo;
	}


	/**
	 * @param $chat
	 */
	public function reopenTimoutChat(ChatConversation $convo)
	{
		$convo['status'] = 'open';
		$convo['date_ended'] = null;
		$convo['ended_by'] = '';

		$this->em->beginTransaction();
		try {
			$this->em->persist($convo);
			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$this->addSystemMessage(
			$convo,
			'message_user-returned'
		);

		// Resend the new chat alerts to agents
		$newchat_cm_data = $convo->getInfo();
		$newchat_cm_data['restarted'] = true;

		$cm = new ClientMessage();
		$cm->fromArray(array(
			'channel' => 'chat.new',
			'data' => $newchat_cm_data,
			'created_by_client' => $this->getCurrentClientId(),
		));

		$this->em->persist($cm);
		$this->em->flush();
	}


	/**
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param \Application\DeskPRO\Entity\Person $person
	 * @return void
	 */
	public function personJoined(ChatConversation $convo, Person $person)
	{
		$tag1 = 'user_joined.' . $person->getId();
		$tag2 = 'user_left.' . $person->getId();

		$joined_left_counts = App::getDb()->fetchAllKeyValue("
			SELECT tag, COUNT(*)
			FROM chat_messages
			WHERE tag IN (?, ?)
			GROUP BY tag
		", array($tag1, $tag2));

		if (
			$joined_left_counts
			&& isset($joined_left_counts[$tag1])
			&& isset($joined_left_counts[$tag2])
			&& $joined_left_counts[$tag1] != $joined_left_counts[$tag2]
		) {
			// We dont need to add another "Joined" message
			// if the user left/returned before the system had a change to register
			return;
		}

		$this->em->beginTransaction();
		try {

			$convo->addParticipant($person);
			$this->em->persist($convo);

			$this->addSystemMessage(
				$convo,
				'message_user-joined',
				array('name' => $person->display_name_user),
				array('user_joined' => true, 'person_name' => $person->display_name_user, 'person_id' => $person->id)
			);

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	/**
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param \Application\DeskPRO\Entity\Person $who
	 * @return void
	 */
	public function personLeft(ChatConversation $convo, Person $person)
	{
		$tag1 = 'user_joined.' . $person->getId();
		$tag2 = 'user_left.' . $person->getId();

		$joined_left_counts = App::getDb()->fetchAllKeyValue("
			SELECT tag, COUNT(*)
			FROM chat_messages
			WHERE tag IN (?, ?)
			GROUP BY tag
		", array($tag1, $tag2));

		if (
			$joined_left_counts
			&& isset($joined_left_counts[$tag1])
			&& isset($joined_left_counts[$tag2])
			&& $joined_left_counts[$tag1] != $joined_left_counts[$tag2]
		) {
			// We dont need to add another "Left" message
			// if the user left/returned before the system had a change to register
			return;
		}

		$this->em->beginTransaction();
		try {

			$convo->removeParticipant($person);
			$this->em->persist($convo);

			$this->addSystemMessage(
				$convo,
				'message_user-left',
				array('name' => $person->display_name_user),
				array('user_left' => true, 'person_name' => $person->display_name_user, 'person_id' => $person->id)
			);

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	/**
	 * Change the department of a chat
	 *
	 * @throws \Exception
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param \Application\DeskPRO\Entity\Department|null $dep
	 * @param \Application\DeskPRO\Entity\Person $who
	 * @return
	 */
	public function setDepartment(ChatConversation $convo, Department $dep = null, Person $who)
	{
		// Already the departmetn
		if (!$dep && !$convo->department) {
			return;
		}
		if ($dep && $convo->department && $convo->department->id == $dep->id) {
			return;
		}

		$old_dep_id = $convo->department_id;

		$this->em->beginTransaction();
		try {
			$convo->department = $dep;
			$this->em->persist($convo);

			if ($dep) {
				$dep_name = $dep->full_title;
			} else {
				$dep_name = $this->tr->phrase('agent.general.none');
			}
			$this->addSystemMessage(
				$convo,
				'message_set-department',
				array('name' => $who->display_name_user, 'department' => $dep_name),
				array('department_changed' => true, 'new_department_id' => $convo->department_id)
			);

			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => 'chat.depchange',
				'data' => array_merge($convo->getInfo(), array('old_department_id' => $old_dep_id)),
				'created_by_client' => $this->getCurrentClientId(),
			));

			$this->em->persist($cm);

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	/**
	 * Assigns a chat to an agent
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param $agent
	 * @return void
	 */
	public function assignAgent(ChatConversation $convo, Person $agent)
	{
		if (!$agent->is_agent) {
			throw new \InvalidArgumentException("Person `{$agent->id}` is not an agent");
		}

		// Already assigned to that agent
		if ($convo->agent && $convo->agent->id == $agent->id) {
			return;
		}

		$old_agent_id = $convo->agent_id;
		$old_agent_name = '';

		if ($convo->agent) {
			$old_agent_name = $convo->agent->getDisplayNameUser();
		}

		$this->em->beginTransaction();
		try {
			$convo->agent = $agent;
			$this->em->persist($convo);

			$this->addSystemMessage($convo, 'message_assigned', array('name' => $agent->display_name_user), array(
				'chat_assigned' => true,
				'assigned_to' => $agent->id,
				'assigned_name' => $agent->getDisplayNameUser(),
				'assigned_avatar' => $agent->getPictureUrl(16),
				'old_assigned_to' => $old_agent_id,
				'old_assigned_name' => $old_agent_name,
			));

			$this->em->flush();

			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => 'chat.reassigned',
				'data' => array_merge($convo->getInfo(), array('old_agent_id' => $old_agent_id, 'new_agent_name' => $agent->display_name_user)),
				'created_by_client' => $this->getCurrentClientId(),
			));

			$this->em->persist($cm);

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}

	/**
	 * Add a new user track (the page theyre viewing) message
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param string $url
	 * @return void
	 */
	public function addUserTrack(ChatConversation $convo, $url)
	{
		$url_show = preg_replace('#^https?://(www\.)?#i', '', $url);
		if (strlen($url_show) > 50) {
			$url_show = substr($url_show, 0, 50) . '...';
		}

		$url = htmlspecialchars($url);
		$url_show = htmlspecialchars($url_show);

		$label = "<a href=\"$url\" target=\"_blank\" title=\"$url\">$url_show</a>";

		$this->addSystemMessage($convo, 'msg_new_user_track', array('label' => $label), array(
			'new_user_track' => $url,
			'user_hidden' => true,
			'is_html' => true,
		));
	}


	/**
	 * Unassign the chat
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param $agent
	 * @return void
	 */
	public function unassignAgent(ChatConversation $convo)
	{
		// Already unassigned
		if (!$convo->agent) {
			return;
		}
		$old_agent_id = $convo->agent_id;
		$old_agent_name = '';

		$old_agent_name = $convo->agent->getDisplayNameUser();

		$convo->agent = null;
		$this->em->persist($convo);

		$this->addSystemMessage($convo, 'message_unassigned', array(), array('chat_unassigned' => true, 'old_assigned_to' => $old_agent_id, 'old_assigned_name' => $old_agent_name));

		// Try to reassign
		if ($this->auto_assigner) {
			$assign_agent = $this->auto_assigner->getAgent($convo);
			if ($assign_agent) {
				$this->assignAgent($convo, $assign_agent);
			}
		}

		$this->em->flush();

		// If no agent auto-assigned,
		// need to broadcast an alert to other agents
		if (!$convo->agent && $convo->status == 'open') {
			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => 'chat.unassigned',
				'data' => array_merge($convo->getInfo(), array('old_agent_id' => $old_agent_id)),
				'created_by_client' => $this->getCurrentClientId(),
			));
			$this->em->persist($cm);
		}
	}


	/**
	 * Mark an agent as timed out and unassign the chat
	 *
	 * @throws \Exception
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return void
	 */
	public function agentTimeout(ChatConversation $convo, Person $person)
	{
		if (!$convo->agent) {
			return;
		}

		$this->em->beginTransaction();
		try {

			$this->addSystemMessage(
				$convo,
				'message_agent-timeout',
				array('name' => $convo->agent->display_name_user),
				array('agent_timed_out' => true)
			);

			$this->personLeft($convo, $person);

			if ($convo->agent && $convo->agent->id == $person->id) {
				$this->unassignAgent($convo);
			}

			$this->em->flush();
			$this->em->commit();

		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	/**
	 * Mark a user as timed out and end the chat
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return void
	 */
	public function userTimeout(ChatConversation $convo)
	{
		$this->em->beginTransaction();
		try {

			$this->addSystemMessage(
				$convo,
				'message_user-timeout',
				array(),
				array('user_timed_out' => true)
			);
			$this->endChat($convo, null, 'timeout');

			$this->em->flush();
			$this->em->commit();

		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	/**
	 * Mark the chat as ended due to a wait timeout
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return void
	 */
	public function waitTimeout(ChatConversation $convo)
	{
		$this->em->beginTransaction();
		try {

			$this->addSystemMessage(
				$convo,
				'message_wait-timeout',
				array(),
				array('wait_timed_out' => true)
			);
			$this->endChat($convo, null, 'wait_timeout');

			$this->em->flush();
			$this->em->commit();

		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	/**
	 * Mark the chat as ended due to a wait timeout
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return void
	 */
	public function userAbandoned(ChatConversation $convo)
	{
		$this->em->beginTransaction();
		try {

			$this->addSystemMessage(
				$convo,
				'message_ended-by-user',
				array(),
				array('user_abandoned' => true)
			);
			$this->endChat($convo, null, 'abandoned');

			$this->em->flush();
			$this->em->commit();

		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	/**
	 * @param $reason
	 * @return void
	 */
	public function endChat(ChatConversation $convo, Person $author = null, $reason = '')
	{
		$convo->status = 'ended';

		if ($author) {
			$convo->ended_by = \Application\DeskPRO\Entity\ChatConversation::ENDED_AGENT;
		} elseif ($reason == 'timeout') {
			$reason = '';
			$convo->ended_by = \Application\DeskPRO\Entity\ChatConversation::ENDED_TIMEOUT;
		} elseif ($reason == 'wait_timeout') {
			$reason = '';
			$convo->ended_by = \Application\DeskPRO\Entity\ChatConversation::ENDED_WAIT_TIMEOUT;
		} elseif ($reason == 'abandoned') {
			$reason = '';
			$convo->ended_by = \Application\DeskPRO\Entity\ChatConversation::ENDED_ABANDONED;
		}

		if ($convo->ended_by != 'timeout' && $convo->ended_by != 'wait_timeout' && $convo->ended_by != 'abandoned') {
			if ($author) {
				$this->addSystemMessage($convo, 'message_ended-by', array('name' => $author->getDisplayNameUser()), array('chat_ended' => true));
			} else {
				$this->addSystemMessage($convo, 'message_ended', array(), array('chat_ended' => true));
			}
		}

		$cm = new ClientMessage();
		$cm->fromArray(array(
			'channel' => 'chat.ended',
			'data' => $convo->getInfo(),
			'created_by_client' => $this->getCurrentClientId(),
		));

		$this->em->persist($cm);

		$this->em->flush();

		if ($reason !== 'timeout' && $reason !== 'wait_timeout' && $reason != 'abandoned') {
			$this->autoSendChatTranscript($convo);
		}
	}


	/**
	 * The user ended the chat
	 *
	 * @throws \Exception
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return void
	 */
	public function endChatUser(ChatConversation $convo, $ended_by = null)
	{
		// Already ended
		if ($convo->status == 'ended') {
			return;
		}

		$convo->status = 'ended';

		if ($ended_by) {
			$convo->ended_by = $ended_by;
		}

		$this->addSystemMessage($convo, 'message_ended-by-user', array(), array('chat_ended'));

		$cm = new ClientMessage();
		$cm->fromArray(array(
			'channel' => 'chat.ended',
			'data' => $convo->getInfo(),
			'created_by_client' => $this->getCurrentClientId(),
		));

		$this->em->persist($cm);
		$this->em->flush();

		$this->autoSendChatTranscript($convo);
	}


	/**
	 * Send a transcript of a chat to a user
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param $email
	 * @param string $name
	 */
	public function sendChatTranscript(ChatConversation $convo, $email, $name = '')
	{
		$convo_messages = $this->em->createQuery("
			SELECT m
			FROM DeskPRO:ChatMessage m
			WHERE m.conversation = ?1 AND m.is_user_hidden = false
			ORDER BY m.id DESC
		")->setParameter(1, $convo)->execute();

		$vars = array(
			'convo' => $convo,
			'convo_messages' => $convo_messages
		);

		$message = App::getMailer()->createMessage();
		$message->setTo($email, $name);
		$message->setTemplate('DeskPRO:emails_user:chat-transcript.html.twig', $vars);
		$message->enableQueueHint();

		App::getMailer()->send($message);
	}


	/**
	 * Send a chat transcript to the user who started a chat if we have an email for them
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return bool
	 */
	public function autoSendChatTranscript(ChatConversation $convo)
	{
		if (!$convo->date_first_agent_message) {
			return false;
		}

		$email = '';
		$name = '';
		if ($convo->person && $convo->person->getPrimaryEmailAddress()) {
			$email = $convo->person->getPrimaryEmailAddress();
		} else if ($convo->person_email) {
			$email = $convo->person_email;
		}
		if ($convo->person && $convo->person->name) {
			$name = $convo->person->name;
		} else if ($convo->person_name) {
			$name = $convo->person_name;
		}

		if ($email) {
			$convo->should_send_transcript = true;
			App::getOrm()->persist($convo);
			App::getOrm()->flush($convo);

			return true;
		}

		return false;
	}


	/**
	 * Add a new message form the user who started the chat.
	 *
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param $message
	 * @param array $metadata
	 * @return \Application\DeskPRO\Entity\ChatMessage
	 */
	public function addUserMessage(ChatConversation $convo, $message, array $metadata = array())
	{
		$person = $this->person;
		if (!$person || !$this->person->id) {
			$person = null;
		}

		$metadata['is_user_message'] = true;

		return $this->addMessage($convo, $person, $message, $metadata);
	}


	/**
	 * Add a new message from a user
	 *
	 * @param \Application\DeskPRO\Entity\Person $author
	 * @param $message
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return \Application\DeskPRO\Entity\ChatMessage
	 */
	public function addMessage(ChatConversation $convo, Person $author = null, $message, array $metadata = array())
	{
		$msg = new ChatMessage();

		if (DP_INTERFACE == 'agent') {
			$msg->origin = 'agent';
		} elseif (DP_INTERFACE == 'user') {
			$msg->origin = 'user';
		}

		if ($author) {
			$msg->author = $author;
		}
		$msg->content = $message;

		if (isset($metadata['user_hidden'])) {
			$msg->is_user_hidden = true;
			unset($metadata['user_hidden']);
		}

		if (isset($metadata['is_html'])) {
			$msg->is_html = true;
			unset($metadata['is_html']);
		}

		if ($author) {
			$metadata['person_avatar'] = $author->getPictureUrl(40);
			$metadata['person_avatar_icon'] = $author->getPictureUrl(16);
		}

		if (isset($metadata['is_html'])) {
			$msg->is_html = (bool)$metadata['is_html'];
			unset($metadata['is_html']);
		}

		$msg->metadata = $metadata;
		$convo->addMessage($msg);

		// If subject less than 250 chars, add message onto it so subject is like a little preview
		if (!$msg->is_user_hidden and !$msg->is_sys and strlen($convo->subject) < 250) {
			if ($convo->subject) {
				$convo->subject .= ' | ';
			}

			$convo->subject .= strip_tags($msg->content);
		}

		$this->em->beginTransaction();
		try {
			$this->em->persist($msg);
			$this->em->persist($convo);
			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$channel = $convo->getChannelId('newmessage');
		if ($msg->is_user_hidden) {
			$channel = $convo->getChannelId('newmessage_hidden');
		}

		$data = $msg->getInfo();
		$cm = new ClientMessage();
		$cm->fromArray(array(
			'channel' => $channel,
			'data' => $data,
			'created_by_client' => $this->getCurrentClientId()
		));

		$this->em->persist($cm);

		$this->em->flush();

		return $msg;
	}


	/**
	 * @param $message_id
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @return \Application\DeskPRO\Entity\ChatMessage
	 */
	public function addSystemMessage(ChatConversation $convo, $message_id, array $vars = array(), $metadata = array())
	{
		$message = $vars;
		\Orb\Util\Arrays::unshiftAssoc($message, 'phrase_id', $message_id);

		// Metadata is used when rendering the phrase in PHP,
		// so add vars to the metadata array
		$metadata = array_merge($metadata, $vars);
		$metadata['phrase_id'] = $message_id;

		$message = json_encode($message);

		$msg = new ChatMessage();
		$msg->is_sys = true;
		$msg->content = $message;

		if (isset($metadata['user_joined']) && isset($metadata['person_id']) && $metadata['person_id']) {
			$msg->tag = 'user_joined.' . $metadata['person_id'];
		} elseif (isset($metadata['user_left']) && isset($metadata['person_id']) && $metadata['person_id']) {
			$msg->tag = 'user_left.' . $metadata['person_id'];
		}

		if (isset($metadata['user_hidden'])) {
			$msg->is_user_hidden = true;
			unset($metadata['user_hidden']);
		}

		if (isset($metadata['is_html'])) {
			$msg->is_html = true;
			unset($metadata['is_html']);
		}

		$msg->metadata = $metadata;

		$convo->addMessage($msg);
		$this->em->beginTransaction();

		try {
			$this->em->persist($msg);
			$this->em->persist($convo);
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$channel = $convo->getChannelId('newmessage');
		if ($msg->is_user_hidden) {
			$channel = $convo->getChannelId('hidden_newmessage');
		}

		$this->em->flush();

		$cm = new ClientMessage();
		$cm->fromArray(array(
			'channel' => $channel,
			'data' => $msg->getInfo(),
		));
		$this->em->persist($cm);

		$this->em->flush();

		return $msg;
	}

	protected function getCurrentClientId()
	{
		if ($this->session) {
			return $this->session->getId();
		}

		return '';
	}


	/**
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param $preview_string
	 * @return void
	 */
	public function setUserTypingIndicator(ChatConversation $convo, $preview_string)
	{
		$this->em->beginTransaction();

		try {
			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $convo->getChannelId('usertyping'),
				'data' => array('preview' => $preview_string),
				'created_by_client' => $this->getCurrentClientId()
			));
			$this->em->persist($cm);

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	/**
	 * @param \Application\DeskPRO\Entity\ChatConversation $convo
	 * @param array $message_ids
	 */
	public function ackMessages(ChatConversation $convo, array $message_ids)
	{
		if (!$message_ids) {
			return;
		}

		$this->em->beginTransaction();

		try {
			$d = date('Y-m-d H:i:s');
			$this->db->executeUpdate("
				UPDATE chat_messages
				SET date_received = ?
				WHERE
					id IN (" . implode(', ', $message_ids) . ")
					AND conversation_id = ?
			", array($d, $convo->getId()));

			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => $convo->getChannelId('ack_messages'),
				'data' => array('message_ids' => $message_ids),
				'created_by_client' => $this->getCurrentClientId()
			));
			$this->em->persist($cm);

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}
	}


	public function getSession()
	{
		return $this->session;
	}
}
