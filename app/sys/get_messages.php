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

namespace DeskPRO\Kernel;

use Orb\Util\Arrays;
use Orb\Util\Util;
use Orb\Util\Strings;
use Application\DeskPRO\App;

if (!defined('DP_ROOT')) exit('No access');

require_once DP_ROOT.'/sys/serve_abstract.php';

class AgentMessagesLoader extends LoaderAbstract
{
	protected $_person_id;
	protected $_session_id;

	public function runAction()
	{
		require_once DP_ROOT.'/src/Orb/Util/Util.php';
		require_once DP_ROOT.'/src/Orb/Util/Strings.php';

		try {
			$agent_session_id = isset($_COOKIE['dpsid-agent']) ? strval($_COOKIE['dpsid-agent']) : '';
			if (!$agent_session_id) {
				echo "no session";
				exit;
			}

			if (!strpos($agent_session_id, '-')) {
				echo "no session";
				exit;
			}
			list ($session_id, ) = explode('-', $agent_session_id, 2);
			$session_id = Util::baseDecode($session_id, Util::BASE36_ALPHABET);

			$db = $this->getPdo();

			$agent_session = $this->getPdo()->query("
				SELECT sessions.*, people.is_agent, people_prefs.value_str AS last_message_id
				FROM sessions
				INNER JOIN people ON (sessions.person_id = people.id)
				LEFT JOIN people_prefs ON (people_prefs.person_id = people.id AND people_prefs.name = 'agent.ui.last_message_id')
				WHERE sessions.id = " . $db->quote($session_id)
			)->fetch(\PDO::FETCH_ASSOC);
			if (!$agent_session || $agent_session_id !== (Util::baseEncode($agent_session['id'], Util::BASE36_ALPHABET) . '-' . $agent_session['auth'])) {
				echo "no/invalid session";
				exit;
			}

			if (!$agent_session['is_agent']) {
				echo "invalid session";
				exit;
			}

			$this->_person_id = $agent_session['person_id'];
			$this->_session_id = $agent_session['id'];

			$new_since = isset($_REQUEST['since']) ? intval($_REQUEST['since']) : 0;
			if ($new_since < 0) {
				$new_since = 0;
			}
			$last_since = intval($agent_session['last_message_id']);
			$activity_time = isset($_REQUEST['at']) ? intval($_REQUEST['at']) : 0;
			if ($activity_time < 0) {
				$activity_time = 0;
			}
			$is_initial_pool = !empty($_REQUEST['is_initial_poll']);

			#------------------------------
			# Standard client messages
			#------------------------------

			$data = $this->getMessageData(
				$agent_session['person_id'],
				$agent_session['id'],
				$new_since,
				($is_initial_pool ? $last_since : null),
				$is_initial_pool
			);

			// We inject a rendered view for new chats, so loop through the messages to do that
			foreach ($data['messages'] as &$item) {
				$channel = $item[1];
				if ($channel == 'chat.new') {
					$cid = $item[2]['conversation_id'];
					$item[2]['html'] = $this->renderChatAlert($cid);
				}
			}
			// unset ref to $item so it isnt overwritten
			unset($item);

			#------------------------------
			# Poll requests
			#------------------------------

			$dos = (isset($_REQUEST['do']) ? (array)$_REQUEST['do'] : array());

			// Every second poll, update online agents list
			$count = isset($_REQUEST['count']) ? intval($_REQUEST['count']) : 0;
			if ($count < 0) {
				$count = 0;
			}
			if ($count && $count % 2 === 0 || 1) {
				$dos[] = 'get-online-agents';
			} elseif ($count && $count % 3 === 0) {
				$dos[] = 'get-online-visitors';
			}

			$dos = array_unique($dos);

			foreach ($dos as $do) {
				$do = Strings::dashToCamelCase($do);
				$method = $do . 'Message';

				if (!method_exists($this, $method)) {
					continue;
				}

				$method_data = $this->$method();

				if ($method_data) {
					$data['messages'] = array_merge($data['messages'], $method_data);
				}
			}

			// We save the last message we know a user got because we need to know
			// to deliver offline messages (such as chats) the next time the user logs in
			if ($new_since && $new_since > $last_since) {
				$q = $db->prepare("
					REPLACE INTO people_prefs
						(person_id, name, value_str, value_array, date_expire)
					VALUES
						(?, ?, ?, NULL, NULL);
				");
				$q->execute(array($agent_session['person_id'], 'agent.ui.last_message_id', $new_since));
			}

			// See if we should update last activity time
			if ($activity_time && $activity_time > (time()-330)) {
				// This bit makes sure theres only one record per 5 minute block
				$date_active = new \DateTime('@' . $activity_time);
				list($hour, $minute) = explode(':', $date_active->format('H:i'));
				$minute = intval($minute / 5) * 5;
				$date_active->setTime($hour, $minute, 0);

				$q = $db->prepare("
					INSERT IGNORE INTO agent_activity
						(agent_id, date_active)
					VALUES
						(?,?)
				");
				$q->execute(array($agent_session['person_id'], $date_active->format('Y-m-d H:i:s')));
			}

			$secret = $this->_getSetting('core.app_secret');
			if (!$secret) {
				$secret = 'APP_SECRET';
			}

			$token = md5($agent_session['id'] . $agent_session['auth'] . $secret . 'request_token');
			$data['request_token'] = Util::generateStaticSecurityToken($token, 10800);

			$q = $db->prepare("
				UPDATE sessions
				SET date_last = ?
				WHERE id = ?
			");
			$q->execute(array(date('Y-m-d H:i:s', time()), $agent_session['id']));

			if (!empty($_REQUEST['recent_tabs'])) {

				$post_recent_tabs = $_REQUEST['recent_tabs'];
				if (!is_array($post_recent_tabs)) {
					$post_recent_tabs = @json_decode($post_recent_tabs, true);
				}

				if (!$post_recent_tabs) {
					$post_recent_tabs = array();
				}

				$q = $db->prepare("
					SELECT value_array
					FROM people_prefs
					WHERE person_id = ? AND name = 'agent.ui.recent_tabs_collection'
				");
				$q->execute(array($this->_person_id));

				$recent_tabs = $q->fetchColumn();
				if ($recent_tabs) {
					$recent_tabs = @unserialize($recent_tabs);
				}

				if (!$recent_tabs) {
					$recent_tabs = array();
				}

				foreach ($post_recent_tabs as $item) {
					if (empty($item[0]) || empty($item[1]) || empty($item[2]) || empty($item[3]) || empty($item[4]) || count($item) != 5) {
						continue;
					}

					$id_string = $item[0] . '-' . $item[1];
					if (isset($recent_tabs[$id_string])) {
						unset($recent_tabs[$id_string]);
					}

					$recent_tabs[$id_string] = $item;
				}

				uasort($recent_tabs, function($a, $b) {
					if ($a[4] == $b[4]) {
						return 0;
					}
					return ($a[4] > $b[4]) ? -1 : 1;
				});

				while (count($recent_tabs) > 350) {
					array_pop($recent_tabs);
				}

				$recent_tabs = serialize($recent_tabs);
				$db->prepare("
					REPLACE INTO people_prefs
					SET
						person_id = ?,
						name = 'agent.ui.recent_tabs_collection',
						value_str = NULL,
						value_array = ?,
						date_expire = NULL
				")->execute(array(
					$this->_person_id,
					$recent_tabs
				));
			}

			#------------------------------
			# Dismiss messages
			#------------------------------

			if (!empty($_REQUEST['dismiss_alerts']) && is_array($_REQUEST['dismiss_alerts'])) {
				$ids = $_REQUEST['dismiss_alerts'];
				$ids = Arrays::castToType($ids, 'int', 'discard');
				$ids = Arrays::removeFalsey($ids);
				$ids = array_unique($ids);

				if ($ids) {
					if (in_array('-1', $ids)) {
						$db->exec("
							UPDATE agent_alerts
							SET is_dismissed = 1
							WHERE person_id = {$agent_session['person_id']}
						");
					} else {
						$ids_in = implode(',', $ids);
						$db->exec("
							UPDATE agent_alerts
							SET is_dismissed = 1
							WHERE person_id = {$agent_session['person_id']} AND id IN ($ids_in)
						");
					}
				}
			}

			// First poll, re-load up to the last 100 alerts
			if ($is_initial_pool) {
				$q = $db->query("
					SELECT id, typename, data
					FROM agent_alerts
					WHERE person_id = {$agent_session['person_id']} AND is_dismissed = 0 AND typename IN ('tickets')
					ORDER BY id ASC
					LIMIT 100
				");
				$q->execute();

				$count = 0;
				$last_alert_id = null;
				while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
					if ($last_alert_id === null || $r['id'] < $last_alert_id) {
						$last_alert_id = $r['id'];
					}
					$count++;

					$r['data'] = unserialize($r['data']);
					$data['messages'][] = array(
						null,
						'agent-notify.'.$r['typename'],
						array(
							'type'     => $r['typename'],
							'alert_id' => $r['id'],
							'row'      => $r['data']['browser_rendered']
						)
					);
				}

				if ($count == 100 && $last_alert_id) {
					$db->exec("
						UPDATE agent_alerts
						SET is_dismissed = 1
						WHERE person_id = {$agent_session['person_id']} AND id < $last_alert_id
					");
				}
			}

			header('Content-Type: application/json');
			echo json_encode($data);
		} catch (\Exception $exception) {
			global $DP_CONFIG;
			if (isset($DP_CONFIG['debug']['dev'])) {
				echo "\n\n[{$exception->getCode()}] {$exception->getMessage()}\n\n";

				$backtrace = $exception->getTrace();
				$trace = self::formatBacktrace($backtrace);
				echo $trace;
			}

			$this->handleException($exception);
		}
	}

	public function renderChatAlert($id)
	{
		$em = $this->_getContainer()->getEm();

		$convo = $em->find('DeskPRO:ChatConversation', $id);
		if (!$convo) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$tickets = null;
		if ($convo->person) {
			$tickets = $em->getRepository('DeskPRO:Ticket')->getLatestByUser($convo->person, 5, true);
		}

		$waiting_secs = time() - $convo->date_created->getTimestamp();

		$url = null;
		if ($convo->visitor && $convo->visitor->last_page) {
			$url = $convo->visitor->last_page;
		}

		return $this->_getContainer()->getTemplating()->render('AgentBundle:UserChat:chat-alert.html.twig', array(
			'convo'         => $convo,
			'person'        => $convo->person,
			'tickets'       => $tickets,
			'session'       => $convo->session,
			'visitor'       => $convo->visitor,
			'waiting_secs'  => $waiting_secs,
			'url'           => $url,
		));
	}

	public function getMessageData($person_id, $session_id, $since = 0, $with_last_since = null, $is_initial = false)
	{
		$data = array('messages' => array(), 'last_id' => -1);
		$all_messages = false;

		if (!$since) {
			$last_id = $this->getPdo()->query("SELECT id FROM client_messages ORDER BY id DESC LIMIT 1")->fetchColumn();
			if ($last_id) {
				$data['last_id'] = $last_id;
			} else {
				$data['last_id'] = 1;
			}
		} else {
			$all_messages = $this->getMessagesForClient($session_id, $person_id, $since);
		}

		if (is_array($all_messages) and $with_last_since !== null) {
			$all_messages = array_merge($all_messages, $this->getInitialMessagesForPerson($person_id, $with_last_since));
		}

		if ($all_messages) {
			foreach ($all_messages as $message) {
				$msg_data = unserialize($message['data']);
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
		if ($is_initial) {
			$convos = $this->_getContainer()->getEm()->getRepository('DeskPRO:ChatConversation')->getOpenForAgentAndDepartment(0, -1);
			foreach ($convos as $c) {
				$chat_data = array(
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
					$chat_data
				);
			}
		}

		if ($data['last_id'] == -1) {
			unset($data['last_id']);
		}

		return $data;
	}

	public function getMessagesForClient($client_id, $person_id, $since_id)
	{
		$channels = array();
		$channels[] = 'chat.new';
		$channels[] = 'chat.reassigned';
		$channels[] = 'chat.unassigned';
		$channels[] = 'chat.depchange';
		$channels[] = 'chat.invited';
		$channels[] = 'chat.ended';
		$channels[] = 'chat_user_agent.chat-parts-updated';

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
		$channels[] = 'agent.ui.reload';
		$channels[] = 'agent.ui.user-chat-status';

		$channels[] = 'agent.filter-update';

		if (isset($_REQUEST['chat_ids']) && is_array($_REQUEST['chat_ids'])) {
			foreach ($_REQUEST['chat_ids'] as $chat_id) {
				$chat_id = (int)$chat_id;
				if (!$chat_id) continue;
				$channels[] = 'chat_convo.' . $chat_id;
			}
		}

		$q = $this->getPdo()->prepare("
			SELECT c.id
			FROM chat_conversations c
			LEFT JOIN chat_conversation_to_person AS c2p ON c2p.conversation_id = c.id
			WHERE (c.agent_id = ? OR c2p.person_id = ?)
				AND c.date_ended IS NULL
		");
		$q->execute(array($person_id, $person_id));
		while ($row = $q->fetch(\PDO::FETCH_ASSOC)) {
			$channels[] = 'chat_convo.' . $row['id'];
		}

		$channels = array_unique($channels);

		return $this->getMessagesForClientInChannels($client_id, $person_id, $channels, $since_id);
	}

	public function getMessagesForClientInChannels($client_id, $person_id, array $channels, $since_id)
	{
		$names = array();
		$names_like = array();
		foreach ($channels as $ch) {
			$names[] = "'{$ch}'";
			$names_like[] = "channel LIKE '{$ch}.%'";
		}

		if (!$names) {
			return array();
		}

		$names = implode(',', $names);
		$names_like = implode(' OR ', $names_like);

		$q = $this->getPdo()->prepare("
			SELECT *
			FROM client_messages
			WHERE (channel IN ($names) OR ($names_like))
				AND (created_by_client <> ? OR channel LIKE 'chat.%' OR channel LIKE 'chat_convo.%')
				AND (for_client = ? OR for_person_id = ? OR (for_client IS NULL AND for_person_id IS NULL))
				AND id > ?
			ORDER BY id
			LIMIT 100
		");
		$q->execute(array($client_id, $client_id, $person_id, $since_id));
		return $q->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getInitialMessagesForPerson($person_id, $since_id)
	{
		$db = $this->getPdo();

		$q = $db->prepare("
			SELECT *
			FROM client_messages
			WHERE for_person_id = ?
				AND id > ?
			ORDER BY id
		");
		$q->execute(array($person_id, $since_id));
		return $q->fetchAll(\PDO::FETCH_ASSOC);
	}

	############################################################################
	# getFilterCounts
	############################################################################

	public function getSysFiltersDataMessage()
	{
		$this->_getContainer();
		$filters_api = new \Application\DeskPRO\Tickets\Filters();

		$filter_info = $filters_api->getGroupedFiltersForPerson($this->_getPerson());
		$filters = array();
		foreach (array('sys_filters', 'sys_filters_hold', 'archive_filters') as $k) {
			foreach ($filter_info[$k] as $f) {
				$filters[$f->id] = $f;
			}
		}

		$client_counts = null;
		if (isset($_REQUEST['filters_data_counts'])) {
			$client_counts = $_REQUEST['filters_data_counts'];
			if ($client_counts) {
				$client_counts = @json_decode($client_counts, true);
			}
		}
		if (!$client_counts) {
			$client_counts = array();
		}
		$filter_id_matches = App::getApi('tickets.filters')->getAllIdsForFiltersCollection($filters, $this->_getPerson());
		$filter_id_matches = Arrays::castToTypeDeep($filter_id_matches, 'int', 'int');

		$filter_counts = array();
		$prefs = App::getDb()->fetchAllKeyValue("
			SELECT name, value_str
			FROM people_prefs
			WHERE name LIKE 'ticket_counts.' AND person_id = ?
		", array($this->_getPerson()->getId()));
		foreach ($filters as $f) {
			if (isset($filter_id_matches[$f->id])) {
				$filter_counts[$f->id] = count($filter_id_matches[$f->id]);
			} elseif ($f->isArchiveTableFilter()) {
				$pref_key = 'ticket_counts.' . $f->sys_name;
				if (isset($prefs[$pref_key])) {
					$filter_counts[$f->id] = (int)$prefs[$pref_key];
				} else {
					$filter_counts[$f->id] = intval(App::getSetting('core_tablecounts.tickets.' . $f->sys_name) ?: 0);
				}
			}
		}

		// When the counts are the same, we dont send the full list
		// back to the client. This reduces the request size on lists that dont change
		if ($client_counts) {
			foreach (array_keys($filter_id_matches) as $filter_id) {
				if (isset($client_counts[$filter_id]) && $client_counts[$filter_id] == count($filter_id_matches[$filter_id])) {
					unset($filter_id_matches[$filter_id]);
				}
			}
		}

		$filter_counts = Arrays::castToTypeDeep($filter_counts, 'int', 'int');

		$filter_data = array(
			'ids' => $filter_id_matches,
			'counts' => $filter_counts
		);

		return array(
			array(null, 'filters.filter_data', $filter_data),
		);
	}

	public function getCustomFiltersDataMessage()
	{
		$this->_getContainer();
		$filters_api = new \Application\DeskPRO\Tickets\Filters();

		$filter_info      = $filters_api->getGroupedFiltersForPerson($this->_getPerson());

		$filter_id_matches = App::getApi('tickets.filters')->getAllIdsForFiltersCollection($filter_info['custom_filters'], $this->_getPerson());
		$filter_id_matches = Arrays::castToTypeDeep($filter_id_matches, 'int', 'int');

		$filter_data = array(
			'ids' => $filter_id_matches,
			'counts' => array()
		);

		return array(array(null, 'filters.filter_data', $filter_data));
	}


	############################################################################
	# getFlaggedCounts
	############################################################################

	public function getFlaggedCountsMessage()
	{
		$this->_getContainer();
		$filters = new \Application\DeskPRO\Tickets\Filters();
		$all_counts = $filters->getAllCountsForPersonFlagged($this->_getPerson());

		return array(array(null, 'filter-flagged.counts', array($all_counts)));
	}

	############################################################################
	# getCheckTickets
	############################################################################

	public function checkTicketsMessage()
	{
		$ticket_ids = isset($_REQUEST['check-ticket-ids']) ? (array)$_REQUEST['check-ticket-ids'] : array();
		$ticket_ids = array_map('intval', $ticket_ids);

		$tickets = $this->_getContainer()->getEm()->getRepository('DeskPRO:Ticket')->getTicketsFromIds($ticket_ids);

		$messages = array();
		foreach ($tickets as $ticket) {
			$msg_id = 'tickets.check.' . $ticket['id'];
			$msg_data = array();

			$msg_data['is_locked'] = $ticket->isLocked();

			$messages[$msg_id] = $msg_data;
		}

		return $messages;
	}

	############################################################################
	# getOnlineVisitors
	############################################################################

	public function getOnlineVisitorsMessage()
	{
		$timeout = $this->_getSetting('core_chat.user_online_time', 600);
		$cutoff = date('Y-m-d H:i:s', time() - $timeout);

		$q = $this->getPdo()->prepare("
			SELECT COUNT(*)
			FROM visitors
			WHERE date_last > ? AND last_track_id IS NOT NULL AND hint_hidden = 0
		");
		$q->execute(array($cutoff));

		$online_count = $q->fetchColumn(0);
		return array(
			array(null, 'agent.online-users-count', array('online_count' => $online_count)),
		);
	}

	############################################################################
	# getOnlineAgents
	############################################################################

	public function getOnlineAgentsMessage()
	{
		$timeout = $this->_getSetting('core_chat.agent_timeout', 20);
		$cutoff = date('Y-m-d H:i:s', time() - $timeout);

		$q = $this->getPdo()->prepare("
			SELECT DISTINCT s.person_id
			FROM sessions s
			INNER JOIN people p ON (s.person_id = p.id)
			WHERE (p.is_agent = 1 AND p.is_deleted = 0 AND s.date_last > ? AND interface = 'agent')
				OR s.person_id = ?
		");
		$q->execute(array($cutoff, $this->_person_id));

		$online_agents = array();
		while ($row = $q->fetch(\PDO::FETCH_ASSOC)) {
			$online_agents[] = $row['person_id'];
		}

		$q = $this->getPdo()->prepare("
			SELECT DISTINCT person_id
			FROM sessions
			WHERE date_last >= ? AND active_status = 'available' AND is_person = 1 AND is_chat_available = 1 AND interface = 'agent'
		");
		$q->execute(array($cutoff));

		$online_agents_userchat = array();
		while ($row = $q->fetch(\PDO::FETCH_ASSOC)) {
			$online_agents_userchat[] = $row['person_id'];
		}

		return array(
			array(null, 'agent.online-agents', array('online_agents' => $online_agents)),
			array(null, 'agent.online-agents-userchat', array('online_agents' => $online_agents_userchat))
		);
	}

	protected $_settings;

	protected function _getSetting($name, $default = null)
	{
		if (!$this->_settings) {
			$this->_settings = array();
			$q = $this->getPdo()->prepare("
				SELECT name, value
				FROM settings
			");
			$q->execute();
			while ($row = $q->fetch(\PDO::FETCH_ASSOC)) {
				$this->_settings[$row['name']] = $row['value'];
			}
		}

		return isset($this->_settings[$name]) ? $this->_settings[$name] : $default;
	}

	protected $_container;

	protected function _getContainer()
	{
		if (!$this->_container) {
			$this->_container = $this->bootFullSystem('DeskPRO\\Kernel\\AgentKernel');
		}

		return $this->_container;
	}

	protected $_person;

	protected function _getPerson()
	{
		if (!$this->_person) {
			$this->_person = $this->_getContainer()->getEm()->getRepository('DeskPRO:Person')->find($this->_person_id);

			$this->_person->loadHelper('Agent');
			$this->_person->loadHelper('AgentTeam');
			$this->_person->loadHelper('AgentPermissions');
			$this->_person->loadHelper('PermissionsManager');
			$this->_person->loadHelper('HelpMessages');
			$this->_person->loadHelper('AgentPrefs');

			App::setCurrentPerson($this->_person);
		}

		return $this->_person;
	}
}

$file_loader = new AgentMessagesLoader();
$file_loader->run();
