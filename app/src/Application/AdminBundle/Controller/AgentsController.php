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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\Entity;
use Application\DeskPRO\App;

use Application\AdminBundle\Form\EditAgentType;
use Application\AdminBundle\FormModel as AdminFormModel;
use Application\DeskPRO\Entity\Usersource;

use Orb\Util\Numbers;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

use QueryPath\Entities;
use Symfony\Component\Form;

class AgentsController extends AbstractController
{
	protected $num_agents = 0;
	protected $max_agents = 0;

	protected function init()
	{
		parent::init();

		$this->num_agents = $this->db->fetchColumn("SELECT COUNT(*) FROM people WHERE is_agent = 1 AND is_deleted = 0");
		$this->max_agents = \DeskPRO\Kernel\License::getLicense()->getMaxAgents();
		if (!$this->max_agents) {
			$this->max_agents = 999999999;
		}

		$this->get('templating.globals')->setVariable('num_agents', $this->num_agents);
		$this->get('templating.globals')->setVariable('max_agents', $this->max_agents);
	}

	public function canAddAgent($context)
	{
		return $this->num_agents < $this->max_agents;
	}

	public function showLicenseError()
	{
		$billing_admins = $this->em->createQuery("
			SELECT p
			FROM DeskPRO:Person p
			WHERE p.can_admin = true AND p.can_billing = true
		")->execute();

		return $this->render('AdminBundle:Agents:error-max-agents.html.twig', array(
			'billing_admins' => $billing_admins,
			'num_agents' => $this->num_agents,
			'max_agents' => $this->max_agents,
		));
	}

	############################################################################
	# agents
	############################################################################

	public function agentsAction()
	{
		$all_agents = $this->em->createQuery("
			SELECT p, pic, email
			FROM DeskPRO:Person p INDEX BY p.id
			LEFT JOIN p.primary_email email
			LEFT JOIN p.picture_blob pic
			WHERE p.is_agent = true AND p.is_deleted = false
			ORDER BY p.first_name, p.last_name
		")->execute();

		$count_deleted = $this->db->fetchColumn("SELECT COUNT(*) FROM people WHERE is_agent = 1 AND is_deleted = 1");

		foreach ($all_agents as $agent) {
			$agent->loadHelper('Agent');
		}

		$all_teams = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:AgentTeam t INDEX BY t.id
			ORDER BY t.name ASC
		")->execute();

		$all_usergroups = $this->em->createQuery("
			SELECT ug
			FROM DeskPRO:Usergroup ug INDEX BY ug.id
			WHERE ug.is_agent_group = true
			ORDER BY ug.title ASC
		")->execute();

		$team_member_ids = $this->em->getRepository('DeskPRO:AgentTeam')->getSortedMemberIds();
		$usergroup_member_ids = $this->em->getRepository('DeskPRO:Usergroup')->getSortedAgentIds();

		$agent_to_groups = array();
		foreach ($usergroup_member_ids as $ug_id => $members) {
			foreach ($members as $pid) {
				if (!isset($agent_to_groups[$pid])) $agent_to_groups[$pid] = array();
				$agent_to_groups[$pid][] = $ug_id;
			}
		}

		$agent_to_teams = array();
		foreach ($team_member_ids as $team_id => $members) {
			foreach ($members as $pid) {
				if (!isset($agent_to_teams[$pid])) $agent_to_teams[$pid] = array();
				$agent_to_teams[$pid][] = $team_id;
			}
		}

		$agents_to_deps = $this->db->fetchAllGrouped("
			SELECT department_id, person_id
			FROM department_permissions
			WHERE person_id IS NOT NULL
				AND name = 'full' AND value = 1
		", array(), 'person_id', null, 'department_id');

		$agents_to_deps_assign = $this->db->fetchAllGrouped("
			SELECT department_id, person_id
			FROM department_permissions
			WHERE person_id IS NOT NULL
				AND name = 'assign' AND value = 1
		", array(), 'person_id', null, 'department_id');

		$overrides_counts = $this->db->fetchAllKeyValue("
			SELECT person_id, COUNT(*)
			FROM permissions
			WHERE person_id IS NOT NULL
			GROUP BY person_id
		");

		$all_departments = $this->em->createQuery("
			SELECT d
			FROM DeskPRO:Department d INDEX BY d.id
			ORDER BY d.display_order
		")->execute();

		$add_from_usersource = array();
		$usersources = $this->em->createQuery("
			SELECT us
			FROM DeskPRO:Usersource us
			ORDER BY us.display_order ASC, us.title ASC
		")->execute();
		foreach ($usersources as $us) {
			if (in_array($us['source_type'], array('ldap', 'active_directory'))) {
				$add_from_usersource[] = $us;
			}
		}

		$active_agents = $this->em->getRepository('DeskPRO:Person')->getActiveAgents();

		$online_agents = array_keys($active_agents);
		$online_agents_userchat = $this->em->getRepository('DeskPRO:Person')->getActiveAgentIdsForUserChat();

		return $this->render('AdminBundle:Agents:list.html.twig', array(
			'all_agents'     => $all_agents,
			'agent_to_groups' => $agent_to_groups,
			'agent_to_teams' => $agent_to_teams,
			'agents_to_deps' => $agents_to_deps,
			'agents_to_deps_assign' => $agents_to_deps_assign,
			'all_teams'      => $all_teams,
			'all_usergroups' => $all_usergroups,
			'all_departments' => $all_departments,
			'add_from_usersource' => $add_from_usersource,

			'online_agents' => $online_agents,
			'online_agents_userchat' => $online_agents_userchat,

			'team_member_ids'      => $team_member_ids,
			'usergroup_member_ids' => $usergroup_member_ids,
			'overrides_counts' => $overrides_counts,

			'count_deleted' => $count_deleted,
		));
	}

	public function deletedAgentsAction()
	{
		$all_agents = $this->em->createQuery("
			SELECT p, pic, email
			FROM DeskPRO:Person p INDEX BY p.id
			LEFT JOIN p.primary_email email
			LEFT JOIN p.picture_blob pic
			WHERE p.is_agent = true AND p.is_deleted = true
			ORDER BY p.first_name, p.last_name
		")->execute();

		if (!$all_agents) {
			return $this->redirectRoute('admin_agents');
		}

		return $this->render('AdminBundle:Agents:list-deleted.html.twig', array(
			'all_agents'     => $all_agents,
		));
	}

	public function killAgentSessionAction($agent_id)
	{
		$sessions = App::getDb()->fetchAll("
			SELECT id, data
			FROM sessions
			WHERE is_chat_available = 1 AND person_id = ?
			LIMIT 10
		", array($agent_id));

		foreach ($sessions as $s) {
			$data = str_replace('"is_chat_available";i:1;', '"is_chat_available";i:0;', $s['data']);
			App::getDb()->executeUpdate("
				UPDATE sessions
				SET is_chat_available = 0, data = ?
				WHERE person_id = ?
			", array($data, $agent_id));
		}

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################
	# agent-preferences
	############################################################################

	public function agentPrefsAction($person_id)
	{
		$agent = $this->getAgentOr404($person_id);

		$did_save = false;

		if ($this->isPostRequest()) {
			$did_save = true;

			$tz = $this->in->getString('agent.timezone');
			if (!in_array($tz, \DateTimeZone::listIdentifiers())) {
				$tz = null;
			}

			$agent->timezone = $tz;

			if ($blob_id = $this->in->getString('new_blob_id')) {
				$blob = $this->em->getRepository('DeskPRO:Blob')->getByAuthId($blob_id);
				if ($blob) {
					$agent->picture_blob = $blob;
				}
			}

			if ($this->in->getBool('is_html_signature')) {
				$signature_html = $this->in->getHtmlCore('ticket_signature');
				$signature_html = \Orb\Util\Strings::trimHtml($signature_html);

				foreach ($this->in->getCleanValueArray('blob_inline_ids', 'uint', 'discard') AS $blob_id) {
					$blob = App::getEntityRepository('DeskPRO:Blob')->find($blob_id);
					if ($blob) {
						$regex = '#(<img[^>]+src=")' . preg_quote($blob->getDownloadUrl(true), '#') . '("[^>]*>)#i';
						$replace = $blob->getEmbedCode(true, 'signature_image');
						$signature_html = preg_replace($regex, $replace, $signature_html);
					}
				}

				$regex = '#<img[^>]+class="dp-signature-image" alt="([^"]+)"[^>]*>#i';
				$signature_html = preg_replace($regex, '$1', $signature_html);

				$signature_html = str_replace(array('<div', '</div>'), array('<p', '</p>'), $signature_html);
				$signature_html = preg_replace('/^<p>/', '<p class="dp-signature-start">', trim($signature_html));

				$signature = strip_tags($signature_html);
			} else {
				$signature = $this->in->getString('ticket_signature');
				$signature_html = nl2br(htmlspecialchars($signature));
				if ($signature_html) {
					$signature_html = '<p class="dp-signature-start">' . $signature . '</p>';
				}
			}

			$agent->setPreference('agent.ticket_signature', $signature);
			$agent->setPreference('agent.ticket_signature_html', $signature_html);

			$this->db->executeUpdate("
				DELETE FROM permissions
				WHERE person_id = ? AND name IN ('agent_general.signature', 'agent_general.picture')
			", array($agent->getId()));

			$this->em->persist($agent);
			$this->em->flush();
		}

		$timezone_options = \DateTimeZone::listIdentifiers();

		return $this->render('AdminBundle:Agents:edit-agent-prefs.html.twig', array(
			'did_save'         => $did_save,
			'agent'            => $agent,
			'timezone_options' => $timezone_options,
			'signature'        => $agent->getSignature(),
	        'signature_html'   => $agent->getSignatureHtml(),
		));
	}

	############################################################################
	# add-from
	############################################################################

	public function newFromUsersourceAction($usersource_id)
	{
		if (!$this->canAddAgent('view_uc')) return $this->showLicenseError();

		$usersource = $this->em->find('DeskPRO:Usersource', $usersource_id);

		return $this->render('AdminBundle:Agents:add-from-usersource.html.twig', array(
			'usersource' => $usersource
		));
	}

	public function newFromUsersourceMakeAction($usersource_id)
	{
		if (!$this->canAddAgent('save_uc')) return $this->showLicenseError();

		$username = $this->in->getString('search_term');

		/** @var $usersource \Application\DeskPRO\Entity\Usersource */
		$usersource = $this->em->find('DeskPRO:Usersource', $usersource_id);

		$identity = $usersource->getAdapter()->findIdentityByInput($username);

		if (!$identity) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$mapped_fields = $usersource->getFieldsFromIdentity($identity);
		$mapped_fields = Arrays::removeEmptyString($mapped_fields);

		// User already exists in the db so dont attempt to re-init them through
		// the login processor
		if (!empty($mapped_fields['email'])) {
			$person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($mapped_fields['email']);
			if ($person) {
				$person->is_agent = true;
				$person->can_agent = true;
				$this->em->persist($person);
				$this->em->flush();

				return $this->redirectRoute('admin_agents_edit', array('person_id' => $person->getId()));
			}
		}

		$this->db->beginTransaction();
		try {
			$login_processor = new \Application\DeskPRO\Auth\LoginProcessor($usersource, $identity);
			$person = $login_processor->getPerson();

			$person->is_agent = true;
			$person->can_agent = true;

			$this->em->persist($person);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_agents_edit', array('person_id' => $person->getId()));
	}

	public function newFromUsersourceSearchAction($usersource_id)
	{
		$username = $this->in->getString('search_term');

		/** @var $usersource \Application\DeskPRO\Entity\Usersource */
		$usersource = $this->em->find('DeskPRO:Usersource', $usersource_id);

		$identity = $usersource->getAdapter()->findIdentityByInput($username);
		$raw_info = null;
		if ($identity) {
			$raw_info = $identity->getRawData();
		}

		return $this->render('AdminBundle:Agents:add-from-usersource-result.html.twig', array(
			'usersource' => $usersource,
			'raw_info' => $raw_info,
			'search_term' => $username,
		));
	}

	public function removeAgentAction($agent_id)
	{
		$agent = $this->em->find('DeskPRO:Person', $agent_id);

		if (!$agent || !$agent->is_agent) {
			throw $this->createNotFoundException();
		}

		return $this->render('AdminBundle:Agents:remove-agent.html.twig', array(
			'agent' => $agent
		));
	}

	public function convertToUserAction($agent_id)
	{
		$agent = $this->em->find('DeskPRO:Person', $agent_id);

		if (!$agent || !$agent->is_agent) {
			throw $this->createNotFoundException();
		}

		$agent->is_agent = false;
		$agent->can_agent = false;
		$agent->can_admin = false;
		$agent->can_billing = false;
		$agent->can_reports = false;
		$agent->was_agent = true;
		$agent->override_display_name = ''; // only settable for agents currently

		$this->db->beginTransaction();
		try {

			$this->em->persist($agent);
			$this->em->flush();

			// Specific department permissions are agent-only feature, remove those
			// Users get them from their usergroups
			$this->db->executeUpdate("
				DELETE FROM department_permissions
				WHERE person_id = ?
			", array($agent->id));

			// Agent groups
			$agent_groups = $this->em->getRepository('DeskPRO:Usergroup')->getAgentUsergroups();
			if ($agent_groups) {
				$agent_group_ids = Arrays::flattenToIndex($agent_groups, 'id');
				$this->db->executeUpdate("
					DELETE FROM person2usergroups
					WHERE person_id = ? AND usergroup_id IN (" . implode(',', $agent_group_ids) . ")
				", array($agent->id));
			}

			// Assigned tickets
			$this->db->executeUpdate("
				UPDATE tickets SET agent_id = NULL
				WHERE agent_id = ?
			", array($agent->id));
			$this->db->executeUpdate("
				UPDATE tickets_search_active SET agent_id = NULL
				WHERE agent_id = ?
			", array($agent->id));

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_agents');
	}

	############################################################################
	# mass-add
	############################################################################

	public function massAddAgentsAction()
	{
		#------------------------------
		# Get and verify addresses
		#------------------------------

		$emails = $this->in->getString('email_addresses');
		$emails = explode(',', $emails);
		$emails = Arrays::func($emails, 'trim');
		$emails = Arrays::func($emails, 'strtolower');
		$emails = array_unique($emails);

		// Filter out non-addresses
		$emails = array_filter($emails, function($email) {
			if (\Orb\Validator\StringEmail::isValueValid($email) && !App::getSystemService('gateway_address_matcher')->isManagedAddress($email)) {
				return true;
			}
			return false;
		});

		if (!$emails) {
			return $this->createJsonResponse(array('error' => 'no_emails'));
		}

		// Filter out addresses that are already agents
		$agent_emails = Arrays::flattenToIndex($this->container->getDataService('Agent')->getAgents(), 'primary_email_address');
		$agent_emails = array_combine($agent_emails, $agent_emails);

		$emails = array_filter($emails, function($email) use ($agent_emails) {
			return !isset($agent_emails[$email]);
		});

		if (!$emails) {
			return $this->createJsonResponse(array('error' => 'no_new_agents'));
		}

		if (count($emails) > 20) {
			return $this->createJsonResponse(array('error' => 'too_many'));
		}

		$res = $this->_preMassAddAgents($emails);
		if ($res) {
			return $res;
		}

		#------------------------------
		# Create the accounts
		#------------------------------

		$new_agents = array();

		foreach ($emails as $email) {
			// In case they are an existing account already
			$agent = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($email);

			if (!$agent) {
				$agent = new \Application\DeskPRO\Entity\Person();
			}

			$agent->is_user = true;
			$agent->is_confirmed = true;
			$agent->is_agent = true;
			$agent->can_agent = true;
			$agent->setPassword(Strings::random(20));

			$email = $agent->addEmailAddressString($email);

			// Attempt to figure out name from email address
			list ($name,) = explode('@', $email->email, 2);
			$name = str_replace('_', ' ', $name);
			$name = str_replace('.', ' ', $name);
			$name = str_replace('+', ' ', $name);
			$name = preg_replace('#[ ]{2,}#', ' ', $name); //consec spaces to single space

			$name = Strings::utf8_ucwords($name);
			$agent->setName($name);

			$this->db->beginTransaction();

			try {

				$this->em->persist($agent);
				$this->em->flush();

				// Default to non-destructive perm group, or if thats deleted, the default all perms group
				$has_ug = App::getDb()->fetchColumn("
					SELECT id
					FROM usergroups
					WHERE id IN (4,3) AND is_agent_group = 1 AND is_enabled = 1
					ORDER BY id DESC
				");
				if ($has_ug) {
					$this->db->insert('person2usergroups', array(
						'person_id' => $agent->getId(),
						'usergroup_id' => $has_ug
					));
				}

				// Default access to all departments
				$batch = array();
				foreach (App::getDataService('Department')->getAll() as $dep) {
					$batch[] = array(
						'department_id' => $dep->getId(),
						'person_id'     => $agent->getId(),
						'app'           => $dep->is_tickets_enabled ? 'tickets' : 'chat',
						'name'          => 'full',
						'value'         => 1
					);
				}
				$this->db->batchInsert('department_permissions', $batch);

				// Default notifications
				$agent_id = $agent->getId();

				if (!$this->container->getSetting('core_tickets.disable_agent_notifications')) {
					$this->db->executeUpdate("
						INSERT INTO `ticket_filter_subscriptions` (`id`, `filter_id`, `person_id`, `email_created`, `email_new`, `email_user_activity`, `email_agent_activity`, `email_agent_note`, `email_property_change`, `alert_new`, `alert_user_activity`, `alert_agent_activity`, `alert_agent_note`, `alert_property_change`)
						VALUES
							(NULL, 1, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
							(NULL, 2, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
							(NULL, 3, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
							(NULL, 4, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
							(NULL, 5, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)
					");

					$this->db->executeUpdate("
						INSERT INTO `people_prefs` (`person_id`, `name`, `value_str`, `value_array`, `date_expire`)
						VALUES
							($agent_id, 'agent_notif.chat_message.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.login_attempt_fail.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.task_assign_self.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.task_assign_self.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.task_assign_team.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.task_assign_team.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.task_complete.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.task_complete.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.task_due.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.task_due.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_assign_self.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_assign_self.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_assign_team.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_assign_team.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_reply.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_reply.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_new_dm.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_new_dm.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_new_reply.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_new_reply.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_new_mention.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_new_mention.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_new_retweet.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.tweet_new_retweet.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_comment.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_comment.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_comment_validate.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_comment_validate.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_feedback.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_feedback.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_feedback_validate.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_feedback_validate.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_user.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_user_validate.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_user_validate.email', '1', X'4E3B', NULL)
					");
				}

				// Add pref for first login marker
				$this->db->insert('people_prefs', array(
					'person_id'   => $agent_id,
					'name'        => 'agent.first_login',
					'value_str'   => 1,
					'value_array' => null,
					'date_expire' => null
				));
				$this->db->insert('people_prefs', array(
					'person_id'   => $agent_id,
					'name'        => 'agent.first_login_name',
					'value_str'   => 1,
					'value_array' => null,
					'date_expire' => null
				));

				$this->db->commit();

				// Send welcome email
				$message = $this->container->getMailer()->createMessage();
				$message->setToPerson($agent);
				$message->setTemplate('DeskPRO:emails_agent:agent-welcome.html.twig', array('agent' => $agent));
				$this->container->getMailer()->send($message);

				$new_agents[] = $agent;
			} catch (\Exception $e) {
				$this->db->rollback();
				throw $e;
			}
		}

		$agents_data = array();
		foreach ($new_agents as $agent) {
			$agents_data[] = array(
				'id'    => $agent->getId(),
				'name'  => $agent->getName(),
				'email' => $agent->primary_email_address
			);
		}

		$this->sendAgentReloadSignal();

		return $this->createJsonResponse(array(
			'success' => true,
			'agents' => $agents_data
		));
	}

	protected function _preMassAddAgents(array $emails)
	{
		$new_total = $this->num_agents + count($emails);

		if ($new_total > $this->max_agents) {
			return $this->createJsonResponse(array(
				'error' => 'license'
			));
		}

		return null;
	}

	############################################################################
	# new-agent-pre
	############################################################################

	public function newAgentPreAction()
	{
		return $this->render('AdminBundle:Agents:new-pre.html.twig');
	}

	############################################################################
	# edit-agent
	############################################################################

	public function editAgentAction($person_id)
	{
		$agent_base = null;

		if ($person_id) {
			$agent = $this->getAgentOr404($person_id);
		} else {
			if (!$this->canAddAgent('view')) return $this->showLicenseError();
			$agent = new \Application\DeskPRO\Entity\Person();

			if ($agent_base_id = $this->in->getUint('base_agent_id')) {
				$agent_base = $this->getAgentOr404($agent_base_id);

				$agent->can_admin   = $agent_base->can_admin;
				$agent->can_billing = $agent_base->can_billing;
				$agent->can_reports = $agent_base->can_reports;
			}
		}

		$all_teams = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:AgentTeam t
			ORDER BY t.name ASC
		")->execute();

		$all_usergroups = $this->em->createQuery("
			SELECT ug
			FROM DeskPRO:Usergroup ug
			WHERE ug.is_agent_group = true
			ORDER BY ug.title ASC
		")->execute();

		$ug_perms = $this->db->fetchAllGrouped("
			SELECT usergroup_id, name, value
			FROM permissions
			LEFT JOIN usergroups ON (usergroups.id = permissions.id)
			WHERE usergroups.is_agent_group = 1
		", array(), 'usergroup_id', 'name', 'value');

		$override_perms = $this->db->fetchAllKeyValue("
			SELECT name, value
			FROM permissions
			WHERE person_id = ?
		", array($agent->id));

		$departments = $this->container->getDataService('Department')->getAll();

		$load_agent = $agent_base ?: $agent;

		$agent_usergroups = $this->db->fetchAllCol("SELECT usergroup_id FROM person2usergroups WHERE person_id = ?", array($load_agent->id));
		$agent_teams = $this->db->fetchAllCol("SELECT team_id FROM agent_team_members WHERE person_id = ?", array($load_agent->id));

		$agent_deps = $this->db->fetchAllGrouped("
			SELECT department_id, app
			FROM department_permissions
			WHERE person_id = ?
				AND name = 'full' AND value = 1
		", array($load_agent->id), 'department_id', 'app', 'app');

		$agent_deps_assign = $this->db->fetchAllGrouped("
			SELECT department_id, app
			FROM department_permissions
			WHERE person_id = ?
				AND name = 'assign' AND value = 1
		", array($load_agent->id), 'department_id', 'app', 'app');

		$all = $this->db->fetchAll("
			SELECT usergroup_id, name, value
			FROM permissions
			WHERE usergroup_id IS NOT NULL
		");
		$usergroup_values = array();
		foreach ($all as $r) {
			if (!isset($usergroup_values[$r['usergroup_id']])) {
				$usergroup_values[$r['usergroup_id']] = array();
			}
			$usergroup_values[$r['usergroup_id']][$r['name']] = $r['value'];
		}

		$usergroup_values['override'] = $this->db->fetchAllKeyValue("
			SELECT name, value
			FROM permissions
			WHERE person_id = ?
		", array($load_agent->id));

		if ($agent && $agent->getId()) {
			$associations = $this->em->getRepository('DeskPRO:PersonUsersourceAssoc')->getAssociationsForPerson($agent);
		} else {
			$associations = array();
		}

		return $this->render('@Agents:edit-agent.html.twig', array(
			'agent'             => $agent,
			'agent_base'        => $agent_base,
			'user_usersources'  => $associations,
			'all_usergroups'    => $all_usergroups,
			'all_teams'         => $all_teams,
			'agent_usergroups'  => $agent_usergroups,
			'agent_teams'       => $agent_teams,
			'usergroup_values'  => $usergroup_values,
			'ug_perms'          => $ug_perms,
			'override_perms'    => $override_perms,
			'departments'       => $departments,
			'agent_deps'        => $agent_deps,
			'agent_deps_assign' => $agent_deps_assign,
			'random_password'   => Strings::randomPronounceable(10)
		));
	}

	public function quickEditFormValidateAction($person_id)
	{
		$agent = null;
		if ($person_id) {
			$agent = $this->getAgentOr404($person_id);
		}

		$errors = array();

		$email = $this->in->getString('agent.email');
		if (!$email or !\Orb\Validator\StringEmail::isValueValid($email)) {
			$errors[] = 'The email address you entered is not valid.';
		} elseif (App::getSystemService('gateway_address_matcher')->isManagedAddress($email)) {
			$errors[] = 'The email address you entered belongs to a ticket account.';
		} elseif (!$agent or !$agent->findEmailAddress($email)) {
			$exist_check = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($email);
			if ($exist_check && !$this->in->getBool('confirm_email_dupe')) {
				$errors[] = 'show_dupe_confirm';
			}
		}

		if (!$this->in->getString('agent.name')) {
			$errors[] = 'You did not a name';
		}

		// Check new emails
		foreach ($this->in->getCleanValueArray('new_emails', 'string', 'discard') as $new_email) {
			if (!$email or !\Orb\Validator\StringEmail::isValueValid($new_email)) {
				$errors[] = 'The email address "'.$new_email.'" is not valid.';
			} elseif (App::getSystemService('gateway_address_matcher')->isManagedAddress($new_email)) {
				$errors[] = 'The email address "'.$new_email.'" belongs to a ticket account.';
			} elseif (!$agent or !$agent->findEmailAddress($new_email)) {
				$exist_check = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($new_email);
				if ($exist_check && !$this->in->getBool('confirm_email_dupe')) {
					$errors[] = 'show_dupe_confirm';
				}
			}
		}

		if ($errors) {
			return $this->createJsonResponse(array('error' => true, 'error_messages' => $errors));
		}

		return $this->createJsonResponse(array('success' => true));
	}

	public function editAgentSaveAction($person_id)
	{
		$set_email = $this->in->getString('agent.email');
		$exist_check = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($set_email);
		$did_merge = false;

		if (!$person_id && $exist_check && !$exist_check->is_agent && $this->in->getBool('confirm_email_dupe')) {
			$person_id = $exist_check->getId();
			$agent = $exist_check;
			$agent->is_user = true;
			$agent->is_confirmed = true;
			$agent->is_agent = true;
			$is_new = false;

		} elseif ($person_id) {
			$agent = $this->getAgentOr404($person_id);
			$is_new = false;

			// We may be merging
			if ($exist_check && $exist_check->getId() != $agent->getId() && $this->in->getBool('confirm_email_dupe')) {
				$email_id = $exist_check->primary_email->getId();

				$did_merge = true;
				$merge = new \Application\DeskPRO\People\PersonMerge\PersonMerge($this->person, $agent, $exist_check);
				$merge->merge();

				// Switch primary email around
				$this->db->executeUpdate("
					UPDATE people
					SET primary_email_id = ?
					WHERE id = ?
				", array($email_id, $agent->getId()));
			}
		} else {

			$agent = new \Application\DeskPRO\Entity\Person();

			$agent->setPassword(Strings::random(20));
			$agent->is_user = true;
			$agent->is_confirmed = true;
			$agent->is_agent = true;

			$is_new = true;
		}

		if ($is_new) {
			if (!$this->canAddAgent('save')) return $this->showLicenseError();
		}

		$agent->name = $this->in->getString('agent.name');
		$agent->override_display_name = $this->in->getString('agent.override_display_name');

		$errors = array();

		if (!$agent->name) {
			$errors[] = 'You did not enter a name';
		}

		foreach (array('can_agent', 'can_admin', 'can_billing', 'can_reports') as $prop) {
			$agent->$prop = $this->in->getBool('agent.' . $prop);
		}

		if (!$agent->findEmailAddress($set_email)) {
			if (!\Orb\Validator\StringEmail::isValueValid($set_email)) {
				$errors[] = 'The email address you entered is invalid';
			} elseif (App::getSystemService('gateway_address_matcher')->isManagedAddress($set_email)) {
				$errors[] = 'The email address you entered belongs to a ticket account.';
			} else {
				if ($exist_check && $exist_check->id != $agent->id) {
					$errors[] = 'The new email address you entered already belongs to a different user.';
				}
			}
		} else {
			$set_email = null;
		}

		// We do client-side validation, so errors checks here are
		// a backup check.
		if ($errors) {
			return $this->renderStandardError(
				"Please correct these errors and try again.",
				"Errors with your form",
				200,
				array('error_list' => $errors)
			);
		}

		if ($this->in->getString('agent.password')) {
			$agent->setPassword($this->in->getString('agent.password'));
			$this->db->delete('sessions', array('person_id' => $agent->id));
		}

		$this->em->getConnection()->beginTransaction();

		try {

			#------------------------------
			# Basic properties
			#------------------------------

			if (!$did_merge && $set_email) {
				$old_email = $agent->getPrimaryEmail();
				if ($old_email && $old_email->email != $set_email) {
					$agent->removeEmailAddressId($old_email->id);
				}

				$email = $agent->setEmail($set_email, true);
				$this->em->persist($email);
			}

			$this->em->persist($agent);
			$this->em->flush();

			#------------------------------
			# Teams
			#------------------------------

			$this->db->delete('agent_team_members', array('person_id' => $agent->id));

			$team_ids = $this->in->getCleanValueArray('agent.teams', 'uint', 'discard');
			if ($team_ids) {
				$team_ids = $this->db->fetchAllCol("SELECT id FROM agent_teams WHERE id IN (" . implode(',', $team_ids) . ")");
			}

			foreach ($team_ids as $tid) {
				$this->db->insert('agent_team_members', array('person_id' => $agent->id, 'team_id' => $tid));
			}

			$this->em->flush();

			#------------------------------
			# Usergroups
			#------------------------------

			$ug_ids = $this->in->getCleanValueArray('agent.usergroups', 'uint', 'discard');
			$usergroups = $this->em->getRepository('DeskPRO:Usergroup')->getByIds($ug_ids);

			$ch = new \Application\DeskPRO\ORM\CollectionHelper($agent, 'usergroups');
			$ch->setCollection($usergroups);

			$this->em->flush();

			if ($ug_ids) {
				$all_ug_perms = $this->db->fetchAllKeyValue("
					SELECT name, value
					FROM permissions
					WHERE usergroup_id IN (" . implode(',', $ug_ids) .")
					ORDER BY value DESC
				");
			} else {
				$all_ug_perms = array();
			}

			#------------------------------
			# Departments
			#------------------------------

			$dep_matrix = $this->in->getCleanValueArray('agent.departments', 'raw', 'uint');
			$dep_assign_matrix = $this->in->getCleanValueArray('agent.departments_assign', 'raw', 'uint');

			 /**
			 * The Other Guys | 201401261008 @Frankie -- Get default department radio button values, strip empties, save to db
			 */
			$dep_default_matrix = $this->in->getCleanValueArray('dep_default', 'raw', 'uint');
			$dep_default_matrix = Arrays::removeFalsey($dep_default_matrix);
			
			
			 $agent->setDepartmentID($dep_default_matrix[0]);
			 /*
			$this->db->executeUpdate("
					UPDATE people
					SET department_id = ?
					WHERE id = ?
				", array($dep_default_matrix[0], $agent->id));
			 */
			/* end #201401261008 */

			$this->db->delete('department_permissions', array(
				'person_id' => $agent->id,
				'name' => 'full',
				'value' => 1
			));
			$this->db->delete('department_permissions', array(
				'person_id' => $agent->id,
				'name' => 'assign',
				'value' => 1
			));
			foreach ($dep_matrix as $dep_id => $apps) {
				foreach ($apps as $app => $v) {
					if (!$v) continue;
					$this->db->insert('department_permissions', array(
						'department_id' => $dep_id,
						'person_id' => $agent->id,
						'app' => $app,
						'name' => 'full',
						'value' => 1
					));
				}
			}
			foreach ($dep_assign_matrix as $dep_id => $apps) {
				foreach ($apps as $app => $v) {
					if (!$v) continue;
					$this->db->insert('department_permissions', array(
						'department_id' => $dep_id,
						'person_id' => $agent->id,
						'app' => $app,
						'name' => 'assign',
						'value' => 1
					));
				}
			}

			#------------------------------
			# Permissions on groups and overrides, oh my
			#------------------------------

			$ug_perm_matrix = $this->in->getCleanValueArray('permissions', 'raw', 'raw');
			$overrides = \Application\DeskPRO\People\Util::resolveOverridePermissions($ug_perm_matrix, $usergroups, $all_ug_perms);

			// Save overrides
			$this->db->delete('permissions', array('person_id' => $agent->id));
			foreach ($overrides as $perm_name => $v) {
				$this->db->insert('permissions', array('person_id' => $agent->id, 'name' => $perm_name, 'value' => 1));
			}

			// If they're new, enable notifications for them by default
			if ($is_new && !$this->container->getSetting('core_tickets.disable_agent_notifications')) {
				$agent_id = $agent->getId();

				$agent_base = null;
				if ($agent_base_id = $this->in->getUint('base_agent_id')) {
					$agent_base = $this->getAgentOr404($agent_base_id);
				}

				// New agent based on someone else, copy their notify settings
				if ($agent_base) {

					$subs = $this->db->fetchAll("
						SELECT `filter_id`, `person_id`, `email_created`, `email_new`, `email_user_activity`, `email_agent_activity`, `email_agent_note`, `email_property_change`, `alert_new`, `alert_user_activity`, `alert_agent_activity`, `alert_agent_note`, `alert_property_change`
						FROM ticket_filter_subscriptions
						WHERE person_id = ?
					", array($agent_base->getId()));

					foreach ($subs as &$_s) {
						$_s['person_id'] = $agent->getId();
					}
					unset($_s);

					if ($subs) {
						$this->db->batchInsert('ticket_filter_subscriptions', $subs, true);
					}

					$sub_prefs = $this->db->fetchAll("
						SELECT person_id, name, value_str, value_array
						FROM people_prefs
						WHERE
							name IN ('agent_notif.chat_message.email', 'agent_notif.login_attempt_fail.email', 'agent_notif.new_comment.alert', 'agent_notif.new_comment.email', 'agent_notif.new_comment_validate.alert', 'agent_notif.new_comment_validate.email', 'agent_notif.new_feedback.alert', 'agent_notif.new_feedback.email', 'agent_notif.new_feedback_validate.alert', 'agent_notif.new_feedback_validate.email', 'agent_notif.new_user.alert', 'agent_notif.new_user_validate.alert', 'agent_notif.new_user_validate.email')
							AND person_id = ?
					", array($agent_base->getId()));

					foreach ($subs as &$_s) {
						$_s['person_id'] = $agent->getId();
					}
					unset($_s);

					if ($sub_prefs) {
						$this->db->batchInsert('people_prefs', $sub_prefs, true);
					}

				// Brand new agents, insert some defaults
				} else {
					$this->db->executeUpdate("
						INSERT INTO `ticket_filter_subscriptions` (`id`, `filter_id`, `person_id`, `email_created`, `email_new`, `email_user_activity`, `email_agent_activity`, `email_agent_note`, `email_property_change`, `alert_new`, `alert_user_activity`, `alert_agent_activity`, `alert_agent_note`, `alert_property_change`)
						VALUES
							(NULL, 1, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
							(NULL, 2, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
							(NULL, 3, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
							(NULL, 4, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
							(NULL, 5, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)
					");

					$this->db->executeUpdate("
						INSERT INTO `people_prefs` (`person_id`, `name`, `value_str`, `value_array`, `date_expire`)
						VALUES
							($agent_id, 'agent_notif.chat_message.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.login_attempt_fail.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_comment.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_comment.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_comment_validate.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_comment_validate.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_feedback.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_feedback.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_feedback_validate.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_feedback_validate.email', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_user.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_user_validate.alert', '1', X'4E3B', NULL),
							($agent_id, 'agent_notif.new_user_validate.email', '1', X'4E3B', NULL),
							($agent_id, 'agent.hide_claimed_chat', '1', X'4E3B', NULL)
					");
				}
			}

			$this->em->flush();
			$this->em->getConnection()->commit();

		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		if ($is_new) {

			// Send welcome email
			$message = $this->container->getMailer()->createMessage();
			$message->setToPerson($agent);
			$message->setTemplate('DeskPRO:emails_agent:agent-welcome.html.twig', array('agent' => $agent));
			$this->container->getMailer()->send($message);

			$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.task_completed_add_agents', time());

			// Add pref for first login marker
			$this->db->insert('people_prefs', array(
				'person_id'   => $agent->getId(),
				'name'        => 'agent.first_login',
				'value_str'   => 1,
				'value_array' => null,
				'date_expire' => null
			));

			// If still in demo mode, send the default ticket as well
			if (\DeskPRO\Kernel\License::getLicense()->isDemo()) {
				\Application\InstallBundle\Data\DataInitializer::newDefaultTicket($agent);
			}
		}

		$this->em->refresh($agent);

		// Additional email addresses
		foreach ($this->in->getCleanValueArray('new_emails', 'string', 'discard') as $new_email) {
			if ($agent->hasEmailAddress($new_email)) {
				continue;
			}

			$exist_check = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($new_email);
			if ($exist_check) {
				if ($exist_check->getId() == $agent->getId()) {
					continue;
				}

				$merge = new \Application\DeskPRO\People\PersonMerge\PersonMerge($this->person, $agent, $exist_check);
				$merge->merge();

				$this->em->refresh($agent);
			} else {
				$email_address = $agent->addEmailAddressString($new_email);
				$this->em->persist($email_address);
				$this->em->flush();
			}
		}

		// Removing email addresses
		foreach ($this->in->getCleanValueArray('remove_emails', 'uint', 'discard') as $remove_email_id) {
			$agent->removeEmailAddressId($remove_email_id);
			$this->em->flush();
		}

		$this->session->setFlash('saved_agent', 1);
		$this->session->save();

		$this->sendAgentReloadSignal();

		return $this->redirectRoute('admin_agents_edit', array('person_id' => $agent->id));
	}

	public function getAgentPermissionsAction($person_id)
	{
		$agent = $this->getAgentOr404($person_id);

		$perms = $agent->getPermissionsManager()->get('Usergroups')->getAllPermissions();

		return $this->createJsonResponse($perms);
	}

	public function setVacationModeAction($person_id, $set_to = 0)
	{
		$this->ensureRequestToken();

		$agent = $this->getAgentOr404($person_id);
		$agent->is_vacation_mode = $set_to;

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($agent);
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->redirectRoute('admin_agents_edit', array('person_id' => $agent->id));
	}

	public function setDeletedAction($person_id, $set_to = 0)
	{
		$this->ensureRequestToken();

		$agent = $this->getAgentOr404($person_id);

		if ($agent->getId() == $this->person->getId()) {
			return $this->renderStandardError('You cannot delete yourself');
		}

		$agent->is_deleted = (bool)$set_to;

		if (!$set_to) {
			if (!$this->canAddAgent('save_deleted')) return $this->showLicenseError();
		}

		$this->em->getConnection()->beginTransaction();

		try {
			if ($set_to) {
				// Remove their permissions
				App::getDb()->delete('department_permissions', array('person_id' => $agent->getId()));
				App::getDb()->delete('permissions', array('person_id' => $agent->getId()));
				App::getDb()->delete('agent_team_members', array('person_id' => $agent->getId()));
				App::getDb()->delete('ticket_filter_subscriptions', array('person_id' => $agent->getId()));

				// Any open tickets should be unassigned
				$this->db->executeUpdate("
					UPDATE tickets SET agent_id = NULL
					WHERE agent_id = ? AND status IN ('awaiting_agent')
				", array($agent->id));
				$this->db->executeUpdate("
					UPDATE tickets_search_active SET agent_id = NULL
					WHERE agent_id = ? AND status IN ('awaiting_agent')
				", array($agent->id));
			}

			$this->em->persist($agent);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();

		if ($set_to) {
			return $this->redirectRoute('admin_agents');
		} else {
			return $this->redirectRoute('admin_agents_edit', array('person_id' => $agent->id));
		}
	}

	############################################################################
	# edit-team
	############################################################################

	/**
	 * Edit a team
	 */
	public function editTeamAction($team_id = 0)
	{
		if ($team_id) {
			$team = $this->getAgentTeamOr404($team_id);
		} else {
			$team = new Entity\AgentTeam();
		}

		if ($this->in->getBool('process')) {

			$this->em->getConnection()->beginTransaction();

			try {
				$team->name = $this->in->getString('team.name');
				if (!$team->name) {
					$team->name = 'New Team';
				}

				$this->em->persist($team);
				$this->em->flush();

				$this->db->delete('agent_team_members', array('team_id' => $team->id));

				foreach ($this->in->getCleanValueArray('team.members', 'uint', 'discard') as $pid) {
					$this->db->insert('agent_team_members', array('team_id' => $team->id, 'person_id' => $pid));
				}

				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_agent_team', '1');

				$this->em->getConnection()->commit();
			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}

			$this->sendAgentReloadSignal();

			return $this->redirectRoute('admin_agents');
		}

		$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();

		$team_members = array();
		if ($team_id) {
			$team_members = App::getDb()->fetchAllKeyValue("
				SELECT person_id, 1
				FROM agent_team_members
				WHERE team_id = ?
			", array($team_id), 0, 1);
		}

		return $this->render('AdminBundle:Agents:edit-team.html.twig', array(
			'team' => $team,
			'agents' => $agents,
			'team_members' => $team_members,
		));
	}

	public function deleteTeamAction($team_id, $security_token)
	{
		$team = $this->getAgentTeamOr404($team_id);

		if (!$this->session->getEntity()->checkSecurityToken('delete_team', $security_token)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->remove($team);
			$this->em->flush();

			$count = $this->db->fetchColumn("SELECT COUNT(*) FROM agent_teams");
			if (!$count) {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.use_agent_team', '0');
			}

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();
		return $this->redirectRoute('admin_agents');
	}

	############################################################################
	# edit
	############################################################################

	public function editGroupAction($usergroup_id)
	{
		if (!$usergroup_id) {
			$usergroup = new Entity\Usergroup();
		} else {
			$usergroup = $this->getAgentGroupOr404($usergroup_id);
		}

		if ($this->in->getBool('process')) {

			$this->em->getConnection()->beginTransaction();
			$proc_agents = array();

			try {
				$usergroup->title = $this->in->getString('usergroup.title');
				$usergroup->is_agent_group = true;

				$this->em->persist($usergroup);
				$this->em->flush();

				$this->db->delete('person2usergroups', array('usergroup_id' => $usergroup->id));

				foreach ($this->in->getCleanValueArray('usergroup.members', 'uint', 'discard') as $pid) {
					$agent = $this->container->getAgentData()->get($pid);
					if ($agent) {
						$proc_agents[] = $agent;
						$this->db->insert('person2usergroups', array('usergroup_id' => $usergroup->id, 'person_id' => $pid));
					}
				}

				$this->db->delete('permissions', array('usergroup_id' => $usergroup->id));
				$perms_groups = $this->in->getCleanValueArray('permissions', 'raw', 'string');
				foreach ($perms_groups as $perm_group => $perms) {
					foreach ($perms as $name => $v) {
						$perm_name = "{$perm_group}.$name";
						$this->db->insert('permissions', array('usergroup_id' => $usergroup->id, 'name' => $perm_name, 'value' => 1));
					}
				}

				$this->em->getConnection()->commit();

			} catch (\Exception $e) {
				$this->em->getConnection()->rollback();
				throw $e;
			}

			// Agents in this group need to have their overrides resolved
			if ($proc_agents) {
				foreach ($proc_agents as $agent) {
					$overrides = \Application\DeskPRO\People\Util::resolveOverridePermissionsForAgent($agent);

					// Save overrides
					$this->db->delete('permissions', array('person_id' => $agent->id));
					foreach ($overrides as $perm_name => $v) {
						$this->db->insert('permissions', array('person_id' => $agent->id, 'name' => $perm_name, 'value' => 1));
					}
				}
			}

			$this->sendAgentReloadSignal();
			return $this->redirectRoute('admin_agents_groups_edit', array('usergroup_id' => $usergroup->id));
		}

		if ($usergroup_id) {
			$members = $this->em->getRepository('DeskPRO:Person')->getUsergroupMemberIds($usergroup);
		} else {
			$members = array();
		}

		$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();

		$usergroup_values = $this->db->fetchAllKeyValue("
			SELECT name, value
			FROM permissions
			WHERE usergroup_id = ?
		", array($usergroup->id));

		return $this->render('AdminBundle:Agents:edit-usergroup.html.twig', array(
			'usergroup' => $usergroup,
			'members' => $members,
			'agents' => $agents,
			'usergroup_values' => $usergroup_values,
		));
	}

	public function deleteGroupAction($usergroup_id, $security_token)
	{
		$usergroup = $this->getAgentGroupOr404($usergroup_id);

		if (!$this->session->getEntity()->checkSecurityToken('delete_group', $security_token)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->remove($usergroup);
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->sendAgentReloadSignal();
		return $this->redirectRoute('admin_agents');
	}

	############################################################################
	# notifications
	############################################################################

	public function notificationsAction()
	{
		$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken();

			$this->container->getDb()->delete('people_prefs', array('name' => 'agent_notif.no_allow_set_email'));
			$this->container->getDb()->delete('people_prefs', array('name' => 'agent_notif.no_allow_set_browser'));

			if (isset($_REQUEST['settings']['core_tickets.disable_agent_notifications']) && $_REQUEST['settings']['core_tickets.disable_agent_notifications']) {
				$this->container->getSettingsHandler()->setSetting('core_tickets.disable_agent_notifications', 1);
				$this->container->getDb()->executeUpdate("DELETE FROM ticket_filter_subscriptions");
				$this->container->getDb()->executeUpdate("DELETE FROM people_prefs WHERE name LIKE 'agent_notif.'");
			} else {
				$this->container->getSettingsHandler()->setSetting('core_tickets.disable_agent_notifications', 0);
				$allow_set_agent   = $this->in->getCleanValueArray('allow_set_email', 'uint');
				$allow_set_browser = $this->in->getCleanValueArray('allow_set_browser', 'uint');

				$insert_prefs = array();

				foreach ($agents as $agent) {
					if (!in_array($agent->getId(), $allow_set_agent)) {
						$insert_prefs[] = array(
							'person_id'   => $agent->getId(),
							'name'        => 'agent_notif.no_allow_set_email',
							'value_str'   => '1',
							'value_array' => 'N;',
							'date_expire' => null
						);
					}

					if (!in_array($agent->getId(), $allow_set_browser)) {
						$insert_prefs[] = array(
							'person_id'   => $agent->getId(),
							'name'        => 'agent_notif.no_allow_set_browser',
							'value_str'   => '1',
							'value_array' => 'N;',
							'date_expire' => null
						);
					}
				}

				if ($insert_prefs) {
					$this->container->getDb()->batchInsert('people_prefs', $insert_prefs);
				}
			}
		}

		$custom_filters = $this->em->getRepository('DeskPRO:TicketFilter')->getAllGlobalFilters();

		$no_allow_set_email = $this->container->getDb()->fetchAllKeyValue("
			SELECT person_id
			FROM people_prefs
			WHERE name = 'agent_notif.no_allow_set_email'
		", array(), 0, 0);

		$no_allow_set_browser = $this->container->getDb()->fetchAllKeyValue("
			SELECT person_id
			FROM people_prefs
			WHERE name = 'agent_notif.no_allow_set_browser'
		", array(), 0, 0);

		return $this->render('AdminBundle:Agents:agent-notifications.html.twig', array(
			'agents'               => $agents,
			'no_allow_set_email'   => $no_allow_set_email,
			'no_allow_set_browser' => $no_allow_set_browser,
			'custom_filters'       => $custom_filters,
		));
	}

	public function notificationsGetAction($person_id)
	{
		$agent = $this->getAgentOr404($person_id);

		$prefs = $this->container->getDb()->fetchAll("
			SELECT * FROM ticket_filter_subscriptions
			WHERE person_id = ?
		", array($agent->getId()));

		$my_prefs = $this->em->getRepository('DeskPRO:PersonPref')->getPrefgroupForPersonId('agent_notif', $agent->id, true);

		return $this->createJsonResponse(array(
			'ticket_filters' => $prefs,
			'other' => $my_prefs
		));
	}

	public function notificationsSaveAction($person_id)
	{
		$this->ensureRequestToken();

		$agent = $this->getAgentOr404($person_id);

		$subs = $this->in->getCleanValueArray('filter_sub', 'array', 'uint');
		$person_editor = $this->container->getSystemService('person_edit_manager');
		$person_editor->saveFilterSubscriptions($agent, $subs);

		$prefs = $this->in->getCleanValueArray('my_prefs', 'bool', 'string');
		$person_editor->saveNotificationPreferences($agent, $prefs);

		return $this->createJsonResponse(array('success' => true));
	}

	############################################################################

	public function adminLoginAsAction($agent_id)
	{
		if (!$this->person->can_admin || !$this->container->getAgentData()->get($agent_id)) {
			return $this->createNotFoundException();
		}

		foreach (array('dpsid-agent') as $cookie_name) {
			if (!empty($_COOKIE[$cookie_name])) {
				$sess2 = $this->em->getRepository('DeskPRO:Session')->getSessionFromCode($_COOKIE[$cookie_name]);
				if ($sess2) {
					$this->em->remove($sess2);
					$this->em->flush();
				}
			}

			$cookie = \Application\DeskPRO\HttpFoundation\Cookie::makeDeleteCookie($cookie_name);
			$cookie->send();
		}

		$tmp = Entity\TmpData::create('admin_agent_login', array(
			'admin_id' => $this->person->getId(),
			'agent_id' => $agent_id
		), '+5 minutes');
		$this->em->persist($tmp);
		$this->em->flush();

		return $this->redirectRoute('agent_login_adminlogin', array('code' => $tmp->getCode()));
	}

	############################################################################

	public function loginLogsAction()
	{
		$per_page = 50;
		$p = $this->in->getUint('p');

		$agent_id = $this->in->getUint('agent_id');

		if (!$p) {
			$p = 1;
		}

		if ($agent_id) {
			$agent = $this->container->getAgentData()->get($agent_id);
			if (!$agent) {
				return $this->createNotFoundException();
			}

			$logs_count = $this->db->fetchColumn("
				SELECT COUNT(*)
				FROM login_log
				WHERE person_id = ?
			", array($agent->id));


			$limit = ($p - 1) * $per_page;

			$logs = $this->db->fetchAll("
				SELECT *
				FROM login_log
				WHERE person_id = ?
				ORDER BY id DESC
				LIMIT $limit, $per_page
			", array($agent->id));
		} else {
			$agent = null;

			$logs_count = $this->db->fetchColumn("
				SELECT COUNT(*)
				FROM login_log
				LEFT JOIN people ON (people.id = login_log.person_id)
				WHERE people.is_agent
			");

			$limit = ($p - 1) * $per_page;

			$logs = $this->db->fetchAll("
				SELECT login_log.*
				FROM login_log
				LEFT JOIN people ON (people.id = login_log.person_id)
				WHERE people.is_agent
				ORDER BY login_log.id DESC
				LIMIT $limit, $per_page
			");
		}

		$pageinfo = Numbers::getPaginationPages($logs_count, $p, $per_page, 5);

		return $this->render('AdminBundle:Agents:login-log.html.twig', array(
			'logs'       => $logs,
			'logs_count' => $logs_count,
			'agent'      => $agent,
			'agent_id'   => $agent ? $agent->getId() : 0,
			'pageinfo'   => $pageinfo,
		));
	}

	############################################################################

	/**
	 * @return \Application\DeskPRO\Entity\AgentTeam
	 */
	protected function getAgentTeamOr404($id)
	{
		$team = $this->em->getRepository('DeskPRO:AgentTeam')->find($id);
		if (!$team) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no team with ID $id");
		}

		return $team;
	}

	/**
	 * @return \Application\DeskPRO\Entity\Usergroup
	 */
	protected function getAgentGroupOr404($id)
	{
		$ug = $this->em->getRepository('DeskPRO:Usergroup')->find($id);
		if (!$ug || !$ug->is_agent_group) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no usergroup with ID $id");
		}

		return $ug;
	}

	/**
	 * @return \Application\DeskPRO\Entity\Person
	 */
	public function getAgentOr404($id)
	{
		$agent = $this->em->find('DeskPRO:Person', $id);
		if (!$agent || !$agent->is_agent) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no agent with ID $id");
		}

		$agent->loadHelper('Agent');

		return $agent;
	}
}
