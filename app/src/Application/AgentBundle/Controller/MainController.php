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
 * @subpackage AgentBundle
 */
namespace Application\AgentBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\People\PrefNoticeSet;
use Orb\Util\Arrays;
use Orb\Util\Numbers;
use Orb\Util\Strings;

class MainController extends AbstractController
{
	public function requireRequestToken($action, $arguments = null)
	{
		if ($action == 'indexAction') {
			return false;
		}

		return parent::requireRequestToken($action, $arguments);
	}

    public function indexAction()
    {
		$this->person->loadPrefGroup('agent.ui');

		$last_message_id = $this->db->fetchColumn("
			SELECT id
			FROM client_messages
			ORDER BY id DESC
			LIMIT 1
		");
		if (!$last_message_id) {
			$last_message_id = -1;
		}

		// Used in some header menus for search options
		$titles = array();
		$titles['organizations'] = $this->container->getDataService('Organization')->getOrganizationNames();
		$titles['usergroups']    = $this->container->getDataService('Usergroup')->getUsergroupNames();

		if ($this->container->getDataService('Language')->isMultiLang()) {
			$titles['languages'] = $this->container->getDataService('Language')->getTitles();
		}

		// Person menu needs these
		$people_fields = $this->container->getSystemService('person_fields_manager')->getDisplayArray();
		$org_fields = $this->container->getSystemService('org_fields_manager')->getDisplayArray();

		// Ticket options for search pane of tickets menu
		$ticket_options = App::getApi('tickets')->getTicketOptions($this->person);

		// Agent info
		$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
		$agent_teams = $this->em->getRepository('DeskPRO:AgentTeam')->findAll();

		// Countr code
		$phone_country_info = \Orb\Data\CountryCallingCodes::getData();

		if (App::getConfig('debug.raw_assets')) {
			$has_raw_assets = true;
		} else {
			$has_raw_assets = false;
		}

		// Auto-load chats in tabs if assigned to an agent
		$open_chats = $this->em->getRepository('DeskPRO:ChatConversation')->getOpenChatsForAgent($this->person);

		$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();
		$custom_fields = App::getApi('custom_fields.tickets')->getFieldsDisplayArray($ticket_field_defs);
		$ticket_options['custom_ticket_fields'] = $custom_fields;

		// People stuff
		$ticket_options['people_organizations'] = $this->em->getRepository('DeskPRO:Organization')->getOrganizationNames();
		$people_field_defs = App::getApi('custom_fields.people')->getEnabledFields();
		$ticket_options['custom_people_fields'] = $custom_fields = App::getApi('custom_fields.people')->getFieldsDisplayArray($people_field_defs);

		$people_options = $titles;
		$people_options['custom_people_fields'] = $ticket_options['custom_people_fields'];

		$org_options = array(
			'custom_org_fields' => $this->container->getSystemService('org_fields_manager')->getDisplayArray()
		);

		$cutoff = date('Y-m-d H:i:s', time() - $this->container->getSetting('core_chat.agent_timeout'));
		$online_agent_ids = $this->db->fetchAllCol("
			SELECT p.id
			FROM sessions s
			LEFT JOIN people AS p ON p.id = s.person_id
			WHERE p.is_agent = true AND s.date_last > ?
		", array($cutoff));

		$agent_chat_depmap = $this->db->fetchAllGrouped("
			SELECT department_permissions.person_id, department_permissions.department_id
			FROM department_permissions
			WHERE
				department_permissions.person_id IS NOT NULL
				AND department_permissions.app = 'chat' AND department_permissions.value = 1
		", array(), 'person_id', null, 'department_id');

		foreach ($agent_chat_depmap as &$v) {
			if ($v) {
				$v = array_unique($v, \SORT_NUMERIC);
			}
		}

		$is_first_login = false;
		$is_first_login_name = false;

		if ($this->person->getPref('agent.first_login')) {
			$is_first_login = true;
			$is_first_login_name = $this->person->getPref('agent.first_login_name');
		}

		$chat_dep_ids = $this->person->getHelper('PermissionsManager')->get('Departments')->getAllowed('chat');

		\Application\DeskPRO\Chat\UserChat\AvailableTrigger::update();

		$version_notices = new PrefNoticeSet(
			$this->db,
			$this->person,
			'agent.ui.version_notices',
			DP_ROOT.'/docs/changelog/docs.php'
		);

		$ticket_snippet_cats = $this->em->getRepository('DeskPRO:TextSnippetCategory')->getCatsForAgent('tickets', $this->person);
		$chat_snippet_cats   = $this->em->getRepository('DeskPRO:TextSnippetCategory')->getCatsForAgent('chat', $this->person);

		return $this->render('AgentBundle:Main:index.html.twig', array(
			'has_raw_assets'      => $has_raw_assets,
			'show_listpane'       => $this->person->getPref('agent.ui.show-listpane'),
			'agent_names'         => $this->em->getRepository('DeskPRO:Person')->getAgentNames(),
			'online_agent_ids'    => $online_agent_ids,
			'agent_chat_depmap'   => $agent_chat_depmap,
			'chat_dep_ids'        => $chat_dep_ids,
			'is_demo'             => $this->in->checkIsset('show-demo-bar'),
			'last_message_id'     => $last_message_id,
			'js_debug'            => App::getConfig('debug.js', array()),
			'titles'              => $titles,
			'people_fields'       => $people_fields,
			'org_fields'          => $org_fields,
			'ticket_options'      => $ticket_options,
			'agents'              => $agents,
			'agent_teams'         => $agent_teams,
			'phone_country_info'  => $phone_country_info,
			'open_chats'          => $open_chats,
			'people_options'      => $people_options,
			'org_options'         => $org_options,
			'is_first_login'      => $is_first_login,
			'is_first_login_name' => $is_first_login_name,
			'timezones'           => \DateTimeZone::listIdentifiers(),
			'version_notices'     => $version_notices,
			'ticket_snippet_cats' => $ticket_snippet_cats,
			'chat_snippet_cats'   => $chat_snippet_cats,
		));
	}

	public function loadVersionNoticeAction($id)
	{
		$id = preg_replace('#[^a-zA-Z0-9_\-]#', 'x', $id);
		$target_dir = DP_ROOT.'/docs/changelog/' . $id;
		if (!is_dir($target_dir)) {
			throw $this->createNotFoundException();
		}

		$html = file_get_contents($target_dir . '/log.html');
		$html = Strings::extractRegexMatch('#<body>(.*?)</body>#s', $html, 1);

		if (preg_match_all('#<[^>]+src=(\'|")(.*?)(\'|")[^>]+>#', $html, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $m) {
				$attach_path = $target_dir . '/' . $m[2];
				if (file_exists($attach_path)) {
					if (Strings::getExtension($attach_path) == 'png') {
						$type = 'image/png;';
					} elseif (Strings::getExtension($attach_path) == 'gif') {
						$type = 'image/gif;';
					} else {
						$type = '';
					}
					$url = "data:{$type}base64," . base64_encode(file_get_contents($attach_path));

					$str = $m[0];
					$str = str_replace($m[2], $url, $str);
					$html = str_replace($m[0], $str, $html);
				}
			}
		}

		return $this->createResponse($html);
	}

	public function dismissVersionNoticeAction($id)
	{
		$version_notices = new PrefNoticeSet(
			$this->db,
			$this->person,
			'agent.ui.version_notices',
			DP_ROOT.'/docs/changelog/docs.php'
		);

		if ($id == 'ALL') {
			foreach ($version_notices->getWaitingIds() as $id) {
				$version_notices->dismiss($id);
			}
		} else {
			$version_notices->dismiss($id);
		}
		$version_notices->save();

		return $this->createJsonResponse(array('success' => true));
	}

	public function getCombinedSectionDataAction()
	{
		$data = array();

		foreach ($this->in->getCleanValueArray('section_ids', 'str_simple', 'discard') as $name) {
			switch ($name) {
				case 'tickets_section':
					$data[$name] = json_decode($this->forward('AgentBundle:TicketSearch:getSectionData')->getContent());
					break;

				case 'chat_section':
					$data[$name] = json_decode($this->forward('AgentBundle:UserChat:getSectionData')->getContent());
					break;

				case 'twitter_section':
					$data[$name] = json_decode($this->forward('AgentBundle:Twitter:getSectionData')->getContent());
					break;

				case 'people_section':
					$data[$name] = json_decode($this->forward('AgentBundle:PeopleSearch:getSectionData')->getContent());
					break;

				case 'feedback_section':
					$data[$name] = json_decode($this->forward('AgentBundle:Feedback:getSectionData')->getContent());
					break;

				case 'publish_section':
					$data[$name] = json_decode($this->forward('AgentBundle:Publish:getSectionData')->getContent());
					break;

				case 'tasks_section':
					$data[$name] = json_decode($this->forward('AgentBundle:Task:getSectionData')->getContent());
					break;

				case 'deals_section':
					$data[$name] = json_decode($this->forward('AgentBundle:Deal:getSectionData')->getContent());
					break;

				case 'twitter_section':
					$data[$name] = json_decode($this->forward('AgentBundle:Twitter:getSectionData')->getContent());
					break;

				case 'agent_chat_section':
					$data[$name] = json_decode($this->forward('AgentBundle:AgentChat:getSectionData')->getContent());
					break;
			}
		}

		return $this->createJsonResponse($data);
	}

	public function loadRecentTabsAction()
	{
		$recent_tabs = $this->db->fetchColumn("
			SELECT value_array
			FROM people_prefs
			WHERE person_id = ? AND name = 'agent.ui.recent_tabs_collection'
		", array($this->person->getId()));

		if ($recent_tabs) {
			$recent_tabs = @unserialize($recent_tabs);
		}

		if (!$recent_tabs) {
			$recent_tabs = array();
		} else {
			uasort($recent_tabs, function($a, $b) {
				if ($a[4] == $b[4]) {
					return 0;
				}
				return ($a[4] < $b[4]) ? -1 : 1;
			});
		}

		return $this->createJsonResponse(array_values($recent_tabs));
	}

	public function quickSearchAction()
	{
		$q = $this->in->getString('q');

		$type_to_ent = array(
			'article'      => 'DeskPRO:Article',
			'download'     => 'DeskPRO:Download',
			'feedback'     => 'DeskPRO:Feedback',
			'news'         => 'DeskPRO:News',
			'ticket'       => 'DeskPRO:Ticket',
			'person'       => 'DeskPRO:Person',
			'organization' => 'DeskPRO:Organization',
			'chat'         => 'DeskPRO:ChatConversation'
		);

		$results = array(
			'article'                => array(),
			'download'               => array(),
			'feedback'               => array(),
			'news'                   => array(),
			'ticket'                 => array(),
			'person'                 => array(),
			'person_related'         => array(),
			'organization'           => array(),
			'organization_related'   => array(),
			'chat'                   => array()
		);

		$result_meta = array();

		$people_top = false;

		if (!$q) {
			return $this->render('AgentBundle:Main:quicksearch.json.jsonphp', array(
				'q' => $q,
				'router' => App::getRouter(),
				'results' => $results,
				'result_meta' => $result_meta,
				'people_top' => $people_top,
			));
		}

		#------------------------------
		# ID based
		#------------------------------

		$is_label = false;
		if (preg_match('#^\[(.*?)\]$#', $q, $m)) {
			$is_label = $m[1];
		}

		// We dont know about past ref formats, so just always try to find
		// a ref if its a valid form
		if (preg_match('#^[0-9A-Z\-_\.]+$#', $q)) {
			$ticket = $this->em->getRepository('DeskPRO:Ticket')->findTicketRef($q);
			if ($ticket) {
				if ($ticket && $this->person->PermissionsManager->TicketChecker->canView($ticket)) {
					$results['ticket'][] = $ticket;
				}
			} elseif (strlen($q) >= 3) {
				$tickets = $this->em->getRepository('DeskPRO:Ticket')->searchTicketRef($q);
				foreach ($tickets as $ticket) {
					if ($ticket && $this->person->PermissionsManager->TicketChecker->canView($ticket)) {
						$results['ticket'][] = $ticket;
					}
				}
			}
		}

		// See if its a tac code (from the URL in user emails)
		$info = \Application\DeskPRO\Entity\Ticket::decodeAccessCode($q);
		if (!empty($info['ticket_id']) && $info['ticket_id']) {
			$ticket = $this->em->getRepository('DeskPRO:Ticket')->find($info['ticket_id']);
			if ($ticket && $this->person->PermissionsManager->TicketChecker->canView($ticket)) {
				$results['ticket'][] = $ticket;
			}
		}

		// Subject search against tickets
		$words = Strings::utf8_strtolower($q);
		$words = explode(' ', $words);
		$words = Arrays::removeFalsey($words);
		$words = array_unique($words);
		$words = array_filter($words, function($s) {
			if (strlen($s) >= 3) {
				return true;
			} else {
				return false;
			}
		});

		$after_id = App::getDbRead()->fetchColumn("SELECT id FROM tickets ORDER BY id DESC");
		$after_id = $after_id - 8000;

		if ($words) {
			$db = App::getDbRead();

			#------------------------------
			# Ticket Subject
			#------------------------------

			$where = array();
			foreach ($words as $w) {
				$where[] = "(subject LIKE " . $db->quote('%' . str_replace(array('%', '_'), array('\\%', '\\_'), $w) . '%') . ")";
			}

			$where[] = "(id > $after_id)";

			$where = implode(' AND ', $where);

			$ticket_ids = App::getDbRead()->fetchAllCol("
				SELECT id
				FROM tickets
				WHERE $where
				ORDER BY id DESC
				LIMIT 100
			");

			if ($ticket_ids) {
				$tickets = $this->em->getRepository('DeskPRO:Ticket')->getByIds($ticket_ids, true);
				foreach ($tickets as $ticket) {
					if ($ticket && $this->person->PermissionsManager->TicketChecker->canView($ticket)) {
						$results['ticket'][] = $ticket;
					}
				}
			}

			#------------------------------
			# Titles
			#------------------------------

			$where = array();
			foreach ($words as $w) {
				$where[] = "(title LIKE " . $db->quote('%' . str_replace(array('%', '_'), array('\\%', '\\_'), $w) . '%') . ")";
			}
			$where[] = "(status != 'hidden')";
			$where = implode(' AND ', $where);

			foreach (array(
				'article'      => 'articles',
				'download'     => 'downloads',
				'feedback'     => 'feedback',
				'news'         => 'news',
			) as $type => $table) {
				$ids = App::getDbRead()->fetchAllCol("
					SELECT id
					FROM $table
					WHERE $where
					ORDER BY id DESC
					LIMIT 25
				");

				if ($ids) {
					$results[$type] = $this->em->getRepository($type_to_ent[$type])->getByIds($ids, true);
				}
			}
		}

		if (!$is_label && Numbers::isInteger($q)) {
			foreach ($type_to_ent as $type => $ent) {
				if ($type == 'ticket') {
					$obj = $this->em->getRepository('DeskPRO:Ticket')->findTicketId($q);
					if ($obj && $obj->getId() != $q) {
						$result_meta['ticket_deleted'] = $obj->getId();
						$result_meta['ticket_deleted_oldid'] = $q;
					}
				} else {
					$obj = $this->em->find($ent, $q);
				}
				if ($obj) {
					if ($obj instanceof \Application\DeskPRO\Entity\Ticket && !$this->person->PermissionsManager->TicketChecker->canView($obj)) {
						continue;
					}
					$results[$type][] = $obj;
				}
			}

		} else {

			if (!$is_label) {
				#------------------------------
				# Email address: Full or partial
				#------------------------------

				if (preg_match('#^\S*@\S*$#', $q)) {

					$people_top = true;
					$people = array();

					// Complete email address
					if (\Orb\Validator\StringEmail::isValueValid($q)) {
						$p = $this->container->getSystemService('UsersourceManager')->findPersonByEmail($q);
						$people = array();
						if ($p) {
							$people[] = $p;
						}
					} else {
						if (strpos($q, '@') === 0) {
							$email = substr($q, 1);
							$email = str_replace(array('%', '_'), array('\\\\%', '\\\\_'), $email) . '%';

							if ($this->settings->get('core_tablecounts.people') < 15000) {
								$people_ids = $this->db->fetchAllCol("
									SELECT people.id
									FROM people
									LEFT JOIN people_emails ON (people_emails.person_id = people.id)
									WHERE people_emails.email_domain LIKE ?
									ORDER BY people.id DESC
									LIMIT 15
								", array($email));
							} else {
								$people_ids = $this->db->fetchAllCol("
									SELECT people.id
									FROM people
									LEFT JOIN tickets ON (tickets.person_id = people.id)
									LEFT JOIN people_emails ON (people_emails.person_id = people.id)
									WHERE
										tickets.id > ?
										AND people_emails.email_domain LIKE ?
									ORDER BY tickets.id DESC
									LIMIT 15
								", array($after_id, $email));
							}
						} else {
							$email = str_replace(array('%', '_'), array('\\\\%', '\\\\_'), $q) . '%';

							if ($this->settings->get('core_tablecounts.people') < 15000) {
								$people_ids = $this->db->fetchAllCol("
									SELECT people.id
									FROM people
									LEFT JOIN people_emails ON (people_emails.person_id = people.id)
									WHERE people_emails.email LIKE ?
									ORDER BY people.id DESC
									LIMIT 15
								", array($email));
							} else {
								$people_ids = $this->db->fetchAllCol("
									SELECT people.id
									FROM people
									LEFT JOIN tickets ON (tickets.person_id = people.id)
									LEFT JOIN people_emails ON (people_emails.person_id = people.id)
									WHERE
										tickets.id > ?
										AND people_emails.email LIKE ?
									ORDER BY tickets.id DESC
									LIMIT 15
								", array($after_id, $email));
							}
						}

						if ($people_ids) {
							$people = $this->em->getRepository('DeskPRO:Person')->getByIds($people_ids, true);
						}
					}

					foreach ($people as $p) {
						$results['person'][$p->id] = $p;

						if ($p->organization) {
							$results['organization'][$p->organization->id] = $p->organization;
						}
					}

				#------------------------------
				# Search for string match in name or email
				#------------------------------

				} else {
					$people = array();

					$q = preg_replace('#\s+#', ' ', $q);
					$q_search = '%' . str_replace(array('%', '_'), array('\\\\%', '\\\\_'), $q) . '%';

					if ($this->settings->get('core_tablecounts.people') < 15000) {
						$people_ids = $this->db->fetchAllCol("
							SELECT people.id
							FROM people
							LEFT JOIN tickets ON (tickets.person_id = people.id)
							LEFT JOIN people_emails ON (people_emails.person_id = people.id)
							WHERE
								people.name LIKE ?
								OR people.first_name LIKE ?
								OR people.last_name LIKE ?
								OR people_emails.email LIKE ?
								OR CONCAT_WS(' ' , people.first_name, people.last_name) LIKE ?
							ORDER BY people.id DESC
							LIMIT 15
						", array($q_search, $q_search, $q_search, $q_search, $q_search));
					} else {
						$people_ids = $this->db->fetchAllCol("
							SELECT people.id
							FROM people
							LEFT JOIN tickets ON (tickets.person_id = people.id)
							LEFT JOIN people_emails ON (people_emails.person_id = people.id)
							WHERE
								tickets.id > ?
								AND (
									people.name LIKE ?
									OR people.first_name LIKE ?
									OR people.last_name LIKE ?
									OR people_emails.email LIKE ?
									OR CONCAT_WS(' ' , people.first_name, people.last_name) LIKE ?
								)
							ORDER BY tickets.id DESC
							LIMIT 15
						", array($after_id, $q_search, $q_search, $q_search, $q_search, $q_search));
					}

					if ($people_ids) {
						$people = $this->em->getRepository('DeskPRO:Person')->getByIds($people_ids, true);
					}

					foreach ($people as $p) {
						$results['person'][$p->id] = $p;

						if ($p->organization) {
							$results['organization'][$p->organization->id] = $p->organization;
						}
					}

					// Organizations
					$orgs = $this->em->getRepository('DeskPRO:Organization')->search($q, 25);
					$oids = array();
					foreach ($orgs as $o) {
						$results['organization'][$o->id] = $o;
						$oids[] = $o->getId();
					}

					if ($oids) {
						// Fetch users of these orgs too
						$people = $this->em->createQuery("
							SELECT p
							FROM DeskPRO:Person p
							WHERE p.organization IN (?0)
							ORDER BY p.date_last_login DESC, p.id DESC
						")->setMaxResults(100)->execute(array($oids));
						foreach ($people as $p) {
							$results['person'][$p->id] = $p;
						}
					}
				}

				if ($results['person']) {
					$tickets = $this->em->getRepository('DeskPRO:Ticket')->getTicketsForPeople($results['person'], 250);
					foreach ($tickets as $t) {
						if (!$t->hidden_status && $this->person->PermissionsManager->TicketChecker->canView($t)) {
							if (!isset($results['person_related'][$t->person->getId()])) {
								$results['person_related'][$t->person->getId()] = array();
							}
							$results['person_related'][$t->person->getId()][] = $t;
						}
					}
				}
			} // is label

			#------------------------------
			# Labels
			#------------------------------

			$label_search = new \Application\DeskPRO\Labels\LabelSearch($this->em);
			$label_results = $label_search->search($is_label ? $is_label : $q);

			if ($label_results) {
				foreach ($label_results as $type => $type_results) {
					foreach ($type_results as $res) {
						$results[$type][] = $res;
					}
				}
			}

			if (App::getConfig('enable_twitter') && count(App::getCurrentPerson()->getTwitterAccountIds()) && preg_match('/^@[a-z0-9_]+$/i', $q)) {
				$results['twitter'] = $q;
			}
		}

		return $this->render('AgentBundle:Main:quicksearch.json.jsonphp', array(
			'q' => $q,
			'router' => App::getRouter(),
			'results' => $results,
			'result_meta' => $result_meta,
			'people_top' => $people_top,
		));
	}
}
