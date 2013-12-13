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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

class TechsStep extends AbstractDeskpro3Step
{
	/**
	 * @var int[]
	 */
	public $dep_ids;

	public static function getTitle()
	{
		return 'Import Techs';
	}

	public function run($page = 1)
	{
		$techs = $this->getOldDb()->fetchAllKeyed("SELECT * FROM tech");
		$tech_ids = array_keys($techs);
		$this->logMessage(sprintf("Importing %d techs", count($techs)));

		$start_time = microtime(true);

		$this->dep_ids = $this->getDb()->fetchAllCol("SELECT id FROM departments");

		$this->getDb()->beginTransaction();

		$scanner = new \Application\InstallBundle\Data\AgentGroupPermScanner();
		$perms = $scanner->getNames();

		$has_admin = false;

		try {
			foreach ($techs as $tech) {

				$check_exist = $this->getMappedNewId('tech', $tech['id']);
				if ($check_exist) {
					return;
				}

				// Check if the account already exists (ie from install, or the import was run late)
				$check_exist_email = $this->getDb()->fetchColumn("SELECT person_id FROM people_emails WHERE email = ?", array($tech['email']));
				if ($check_exist_email) {
					$agent = $this->getEm()->find('DeskPRO:Person', $check_exist_email);

				// Import the tech account
				} else {
					$agent = new \Application\DeskPRO\Entity\Person();
					$agent->setEmail($tech['email'], true);
					$agent->setRawPassword($tech['password']);
					$agent->password_scheme = 'deskpro3_tech';
					$agent->salt = $tech['salt'];
					$agent->can_agent = true;
					$agent->can_admin = (bool)$tech['is_admin'];
					$agent->can_billing = (bool)$tech['is_admin'];
					$agent->can_reports = (bool)$tech['is_admin'];

					$name_parts = \Application\DeskPRO\People\Util::guessNameParts($tech['name'], $tech['email']);
					$agent->first_name = $name_parts[0];
					$agent->last_name = $name_parts[1];
				}

				$agent->is_user = true;
				$agent->is_confirmed = true;
				$agent->is_agent_confirmed = true;
				$agent->is_agent = true;

				if (!$tech['active']) {
					$agent->is_deleted = true;
				}

				$this->getEm()->persist($agent);
				$this->getEm()->flush();

				// Copy signature
				if ($tech['signature']) {
					$this->getDb()->insert('people_prefs', array(
						'name' => 'agent.ticket_signature',
						'person_id' => $agent->id,
						'value_str' => $tech['signature'],
						'value_array' => 'N;'
					));
				}

				$this->saveMappedId('tech', $tech['id'], $agent->id);

				// This is used for email dupe checking in insert users
				$this->saveMappedId('tech_email', strtolower($agent->getPrimaryEmailAddress()), $agent->id);

				if ($agent->can_admin && !$has_admin) {
					$has_admin = true;
					$this->saveMappedId('first_admin', 0, $agent->id);
				}

				// Save old password string because its used in gateways
				$this->getDb()->insert('import_datastore', array(
					'typename' => 'dp3_techpass_' . $tech['id'],
					'data' => serialize(array('new_id' => $agent->id, 'old_pass' => $tech['password']))
				));

				#------------------------------
				# Copy Permissions
				#------------------------------

				// Admins just have everything
				if ($agent->can_admin) {
					$this->getDb()->insert('person2usergroups', array(
						'person_id' => $agent->id,
						'usergroup_id' => 3
					));

				// Otherwise we'll import perms into overrides
				} else {
					$insert_perms = array();
					foreach ($perms as $n) {
						$insert_perms[$n] = 1;
					}

					//-----
					// Tickets (Own)
					//-----

					if (!$tech['p_delete_own']) {
						unset($tech['agent_tickets.delete_own']);
					}
					if (!$tech['p_start_ticket']) {
						unset($tech['agent_tickets.create']);
					}

					if (!$tech['p_close_ticket']) {
						unset($tech['agent_tickets.modify_set_resolved_own']);
						unset($tech['agent_tickets.modify_set_resolved_unassigned']);
						unset($tech['agent_tickets.modify_set_resolved_others']);
					}

					if (!$tech['p_merge_ticket']) {
						unset($tech['agent_tickets.modify_set_merge_own']);
						unset($tech['agent_tickets.modify_set_merge_unassigned']);
						unset($tech['agent_tickets.modify_set_merge_others']);
					}


					//-----
					// Tickets (Unassigned)
					//-----

					if (!$tech['p_unassigned_view']) {
						unset(
							$insert_perms['agent_tickets.view_unassigned'],
							$insert_perms['agent_tickets.reply_unassigned'],
							$insert_perms['agent_tickets.modify_unassigned'],
							$insert_perms['agent_tickets.modify_department_unassigned'],
							$insert_perms['agent_tickets.modify_fields_unassigned'],
							$insert_perms['agent_tickets.modify_assign_agent_unassigned'],
							$insert_perms['agent_tickets.modify_assign_team_unassigned'],
							$insert_perms['agent_tickets.modify_assign_self_unassigned'],
							$insert_perms['agent_tickets.modify_cc_unassigned'],
							$insert_perms['agent_tickets.modify_merge_unassigned'],
							$insert_perms['agent_tickets.modify_labels_unassigned'],
							$insert_perms['agent_tickets.modify_notes_unassigned'],
							$insert_perms['agent_tickets.modify_set_hold_unassigned'],
							$insert_perms['agent_tickets.modify_set_awaiting_user_unassigned'],
							$insert_perms['agent_tickets.modify_set_awaiting_agent_unassigned'],
							$insert_perms['agent_tickets.modify_set_resolved_unassigned'],
							$insert_perms['agent_tickets.delete_unassigned']
						);
					}

					//-----
					// Tickets (Others)
					//-----

					if (!$tech['p_tech_view']) {
						unset(
							$insert_perms['agent_tickets.view_others'],
							$insert_perms['agent_tickets.reply_others'],
							$insert_perms['agent_tickets.modify_others'],
							$insert_perms['agent_tickets.modify_department_others'],
							$insert_perms['agent_tickets.modify_fields_others'],
							$insert_perms['agent_tickets.modify_assign_agent_others'],
							$insert_perms['agent_tickets.modify_assign_team_others'],
							$insert_perms['agent_tickets.modify_assign_self_others'],
							$insert_perms['agent_tickets.modify_cc_others'],
							$insert_perms['agent_tickets.modify_merge_others'],
							$insert_perms['agent_tickets.modify_labels_others'],
							$insert_perms['agent_tickets.modify_notes_others'],
							$insert_perms['agent_tickets.modify_set_hold_others'],
							$insert_perms['agent_tickets.modify_set_awaiting_user_others'],
							$insert_perms['agent_tickets.modify_set_awaiting_agent_others'],
							$insert_perms['agent_tickets.modify_set_resolved_others'],
							$insert_perms['agent_tickets.delete_others']
						);
					} else {
						if (!$tech['p_delete_other']) {
							unset($insert_perms['agent_tickets.delete_others']);
						}
						if (!$tech['p_tech_reply']) {
							unset($insert_perms['agent_tickets.reply_others']);
						}
						if (!$tech['p_tech_edit']) {
							unset(
								$insert_perms['agent_tickets.modify_others'],
								$insert_perms['agent_tickets.modify_department_others'],
								$insert_perms['agent_tickets.modify_fields_others'],
								$insert_perms['agent_tickets.modify_assign_agent_others'],
								$insert_perms['agent_tickets.modify_assign_team_others'],
								$insert_perms['agent_tickets.modify_assign_self_others'],
								$insert_perms['agent_tickets.modify_cc_others'],
								$insert_perms['agent_tickets.modify_merge_others'],
								$insert_perms['agent_tickets.modify_labels_others'],
								$insert_perms['agent_tickets.modify_labels_others'],
								$insert_perms['agent_tickets.modify_set_hold_others'],
								$insert_perms['agent_tickets.modify_set_awaiting_user_others'],
								$insert_perms['agent_tickets.modify_set_awaiting_agent_others'],
								$insert_perms['agent_tickets.modify_set_resolved_others']
							);
						}
					}

					//-----
					// Users
					//-----

					if (!$tech['p_create_users']) {
						unset($insert_perms['agent_people.create']);
					}

					if (!$tech['p_edit_users']) {
						unset(
							$insert_perms['agent_people.edit'],
							$insert_perms['agent_people.validate'],
							$insert_perms['agent_people.manage_emails'],
							$insert_perms['agent_people.reset_password'],
							$insert_perms['agent_people.delete']
						);
					}

					if (!$tech['p_delete_users']) {
						unset($insert_perms['agent_people.delete']);
					}

					if (!$tech['p_approve_new_registrations']) {
						unset($insert_perms['agent_people.validate']);
					}

					//-----
					// Chat
					//-----

					if (!$tech['p_chat']) {
						unset(
							$insert_perms['agent_chat.use'],
							$insert_perms['agent_chat.view_unassigned'],
							$insert_perms['agent_chat.view_others'],
							$insert_perms['agent_chat.delete']
						);
					} else {
						if (isset($tech['p_chat_del_logs']) && !$tech['p_chat_del_logs']) {
							unset($insert_perms['agent_chat.delete']);
						}
					}

					foreach ($insert_perms as $k => $v) {
						$this->getDb()->insert('permissions', array(
							'person_id' => $agent->id,
							'name' => $k,
							'value' => 1
						));
					}
				}

				#------------------------------
				# Category (department) permissions
				#------------------------------

				// DP3: Cats in cats_admin are ones that are *denied*
				// DP4: Theres an entry in department_permissions for each cat *allowed*

				$deny_cat_ids = explode(',', (string)$tech['cats_admin']);

				foreach ($this->dep_ids as $did) {
					$mapped_id = $this->getMappedOldId('ticket_category', $did);
					if (in_array($mapped_id, $deny_cat_ids)) {
						continue;
					}

					$this->getDb()->insert('department_permissions', array(
						'department_id' => $did,
						'person_id' => $agent->id,
						'app' => 'tickets',
						'name' => 'full',
						'value' => 1,
					));
				}

				// Agents can use all chat cats by default
				foreach ($this->dep_ids as $did) {
					$this->getDb()->insert('department_permissions', array(
						'department_id' => $did,
						'person_id' => $agent->id,
						'app' => 'chat',
						'name' => 'full',
						'value' => 1
					));
				}

				#------------------------------
				# Enable notifications
				#------------------------------

				$subs = array(
					1 => array(),
					2 => array(),
					3 => array(),
					4 => array(),
					5 => array(),
				);

				$f_my = 1;
				$f_team = 2;
				$f_follow = 3;
				$f_noone = 4;
				$f_all = 5;

				if ($tech['email_assigned']) {
					$subs[$f_my][] = 'email_new';
					$subs[$f_my][] = 'alert_new';
					$subs[$f_my][] = 'email_created';
					$subs[$f_my][] = 'alert_created';
				}

				if ($tech['email_add_participant']) {
					$subs[$f_follow][] = 'email_new';
					$subs[$f_follow][] = 'alert_new';
					$subs[$f_follow][] = 'email_created';
					$subs[$f_follow][] = 'alert_created';
				}

				if ($tech['email_new_email']) {
					$subs[$f_all][] = 'email_created';
					$subs[$f_all][] = 'alert_created';
					$subs[$f_noone][] = 'email_created';
					$subs[$f_noone][] = 'alert_created';
				}
				if ($tech['email_reply_email']) {
					$subs[$f_all][] = 'email_user_activity';
					$subs[$f_all][] = 'alert_user_activity';
					$subs[$f_noone][] = 'email_user_activity';
					$subs[$f_noone][] = 'alert_user_activity';

					if ($tech['email_tech_reply']) {
						$subs[$f_all][] = 'email_agent_activity';
						$subs[$f_all][] = 'alert_agent_activity';
						$subs[$f_noone][] = 'email_agent_activity';
						$subs[$f_noone][] = 'alert_agent_activity';
					}

					if ($tech['email_note']) {
						$subs[$f_all][] = 'email_agent_note';
						$subs[$f_all][] = 'alert_agent_note';
						$subs[$f_noone][] = 'email_agent_note';
						$subs[$f_noone][] = 'alert_agent_note';
					}
				}

				if ($tech['email_own_email']) {
					$subs[$f_my][] = 'email_user_activity';
					$subs[$f_my][] = 'alert_user_activity';

					if ($tech['email_tech_reply']) {
						$subs[$f_my][] = 'email_agent_activity';
						$subs[$f_my][] = 'alert_agent_activity';
					}
					if ($tech['email_note']) {
						$subs[$f_my][] = 'email_agent_note';
						$subs[$f_my][] = 'alert_agent_note';
					}
				}

				if ($tech['email_reply_participant']) {
					$subs[$f_follow][] = 'email_user_activity';
					$subs[$f_follow][] = 'alert_user_activity';

					if ($tech['email_tech_reply']) {
						$subs[$f_follow][] = 'email_agent_activity';
						$subs[$f_follow][] = 'alert_agent_activity';
					}
					if ($tech['email_note']) {
						$subs[$f_follow][] = 'email_agent_note';
						$subs[$f_follow][] = 'alert_agent_note';
					}
				}

				$subs[$f_team][] = 'email_new';
				$subs[$f_team][] = 'alert_new';
				$subs[$f_team][] = 'email_created';
				$subs[$f_team][] = 'alert_created';
				$subs[$f_team][] = 'email_user_activity';
				$subs[$f_team][] = 'email_agent_activity';
				$subs[$f_team][] = 'email_agent_note';

				$subs = \Orb\Util\Arrays::removeFalsey($subs);

				foreach ($subs as $filter_id => $opts) {
					$ins = array();
					foreach ($opts as $k) $ins[$k] = 1;

					$ins['person_id'] = $agent->id;
					$ins['filter_id'] = $filter_id;
					$this->db->insert('ticket_filter_subscriptions', $ins);
				}

				// Prefs
				$prefs = array();
				if ($tech['email_pm']) {
					$prefs['chat_message.email'] = 1;
					$prefs['chat_message.alert'] = 1;
				}
				if ($tech['email_user_registered']) {
					$prefs['new_user.email'] = 1;
					$prefs['new_user.alert'] = 1;
				}
				if ($tech['email_user_registered_validation']) {
					$prefs['new_user_validate.email'] = 1;
					$prefs['new_user_validate.alert'] = 1;
				}

				if ($tech['email_on_login']) {
					$prefs['login_attempt.email'] = 1;
				}
				if ($tech['email_on_failed_login']) {
					$prefs['login_attempt_fail.email'] = 1;
				}

				$prefs['new_feedback.email'] = 1;
				$prefs['new_feedback.alert'] = 1;
				$prefs['new_feedback_validate.email'] = 1;
				$prefs['new_feedback_validate.alert'] = 1;
				$prefs['new_comment.email'] = 1;
				$prefs['new_comment_validate.email'] = 1;

				foreach ($prefs as $p => $v) {
					$this->db->insert('people_prefs', array(
						'person_id' => $agent->id,
						'name' => 'agent_notif.' . $p,
						'value_str' => $v,
						'value_array' => 'N;',
					));
				}
			}

			$this->getEm()->flush();
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}

		#------------------------------
		# See if we should create dummy accounts for
		# orphaned posts that were on deleted agents
		#------------------------------

		$deleted_tech_ids = $this->getOldDb()->fetchAllCol("
			SELECT DISTINCT(techid)
			FROM ticket_message
			WHERE techid != 0 AND techid NOT IN (" . implode(',', $tech_ids) . ")
		");

		$deleted_tech_ids = array_unique($deleted_tech_ids, \SORT_NUMERIC);

		$email_domain = php_uname('n');
		if (!$email_domain) {
			$email_domain = 'deskpro-dummy.example.com';
		}

		$site_url = @parse_url($this->getDb()->fetchColumn("SELECT value FROM settings WHERE name = 'core.deskpro_url'"));
		if ($site_url && !empty($site_url['host'])) {
			$email_domain = $site_url['host'];
		}

		$this->logMessage(sprintf("%d orphan agents, will create dummy accounts @%s", count($deleted_tech_ids), $email_domain));

		foreach ($deleted_tech_ids as $tech_id) {
			$check_exist = $this->getMappedNewId('tech', $tech_id);
			if ($check_exist) {
				return;
			}

			$agent = new \Application\DeskPRO\Entity\Person();
			$agent->setEmail("deleted-agent-$tech_id@$email_domain", true);
			$agent->setPassword(uniqid('', true) . mt_rand(1000,9999));
			$agent->salt        = 'xxx';
			$agent->can_agent   = true;
			$agent->can_admin   = false;
			$agent->can_billing = false;
			$agent->can_reports = false;
			$agent->is_deleted  = true;
			$agent->first_name  = "Deleted";
			$agent->last_name   = "Deleted";

			$this->getEm()->persist($agent);
			$this->getEm()->flush();
			$this->saveMappedId('tech', $tech_id, $agent->id);
		}

		$end_time = microtime(true);
		$this->logMessage(sprintf("-- Done. Took %.3f seconds.", $end_time-$start_time));
	}
}
