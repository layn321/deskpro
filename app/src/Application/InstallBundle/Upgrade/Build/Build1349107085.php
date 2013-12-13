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
 * @subpackage
 */

namespace Application\InstallBundle\Upgrade\Build;

class Build1349107085 extends AbstractBuild
{
	public function run()
	{
		#----------------------------------------
		# Time trigger options
		#----------------------------------------

		$this->out("Update time trigger options");

		$time_trigger_options = $this->container->getDb()->fetchAll("
			SELECT id, event_trigger, event_trigger_option
			FROM ticket_triggers
			WHERE event_trigger_option != ''
		");

		// We have the data now, can make the schema change
		$this->execMutateSql("ALTER TABLE ticket_triggers ADD event_trigger_options LONGBLOB DEFAULT NULL COMMENT '(DC2Type:array)', ADD terms_any LONGBLOB NOT NULL COMMENT '(DC2Type:array)', DROP event_trigger_option");

		// Restore proper time trigger option
		foreach ($time_trigger_options as $info) {
			$opt = array('time' => $info['event_trigger_option']);
			$opt = serialize($opt);

			$event = str_replace('time_', 'time.', $info['event_trigger']);

			$update = array(
				'event_trigger' => $event,
				'event_trigger_options' => $opt,
			);

			$this->container->getDb()->update('ticket_triggers', $update, array('id' => $info['id']));
		}

		#----------------------------------------
		# Rename for update event names
		#----------------------------------------

		// Rename trigger types
		$triggers = $this->container->getDb()->fetchAll("
			SELECT *
			FROM ticket_triggers
			WHERE event_trigger NOT LIKE 'time_%'
		");

		$this->out("Update existing triggers to new event types");

		foreach ($triggers as $trigger) {

			$trigger['terms'] = unserialize($trigger['terms']);
			$terms = null;
			$event_trigger = '';

			switch ($trigger['event_trigger']) {
				case 'new_ticket':
					$sig_term = null;
					$hit_check = array();
					$terms = $this->snipTerm($trigger['terms'], 'creation_system', $sig_term, array('gateway_account', 'gateway_address'), $hit_check);

					if (!$sig_term) {
						$event_trigger = 'new.web.user';
						if (in_array('gateway_account', $hit_check) || in_array('gateway_address', $hit_check)) {
							$event_trigger = 'new.email.user';
						}
					} else {
						$creation_system = isset($sig_term['options']['creation_system']) ? $sig_term['options']['creation_system'] : '';
						switch ($creation_system) {
							case 'web.person':
								$event_trigger = 'new.web.user';
								break;
							case 'widget':
								$event_trigger = 'new.web.user.widget';
								break;
							case 'gateway.person':
								$event_trigger = 'new.email.user';
								break;
							case 'gateway.agent':
								$event_trigger = 'new.email.agent';
								break;
							case 'gateway.agent':
								$event_trigger = 'new.email.agent';
								break;
							case 'web.agent':
								$event_trigger = 'new.web.agent.portal';
								break;
							default:
								$event_trigger = 'new.web.user';
						}
					}

					break;

				case 'new_reply':
					$sig_term = null;
					$terms = $this->snipTerm($trigger['terms'], 'creation_system', $sig_term);

					$sig_term2 = null;
					$terms = $this->snipTerm($terms, 'action_performer', $sig_term2);

					if (!$sig_term) {
						$event_trigger = 'update';
						$terms[] = array(
							'type' => 'new_reply_user',
							'op' => 'is',
							'options' => array('do' => 1)
						);
					} else {
						$creation_system = isset($sig_term['options']['creation_system']) ? $sig_term['options']['creation_system'] : '';
						switch ($creation_system) {
							case 'web.person':
							case 'gateway.person':
								$event_trigger = 'update.user';
								$terms[] = array(
									'type' => 'new_reply_user',
									'op' => 'is',
									'options' => array('do' => 1)
								);
								break;
							case 'web.agent':
							case 'gateway.agent':
								$event_trigger = 'update.agent';
								$terms[] = array(
									'type' => 'new_reply_agent',
									'op' => 'is',
									'options' => array('do' => 1)
								);
								break;
							default:
								$event_trigger = 'update';
								$terms[] = array(
									'type' => 'new_reply_user',
									'op' => 'is',
									'options' => array('do' => 1)
								);
						}
					}

					break;

				case 'property_change':
					$sig_term = null;
					$terms = $this->snipTerm($trigger['terms'], 'action_performer', $sig_term);

					if (!$sig_term) {
						$event_trigger = 'updated';
					} else {
						$action_performer = isset($sig_term['options']['action_performer']) ? $sig_term['options']['action_performer'] : '';
						switch ($action_performer) {
							case 'user':
								$event_trigger = 'updated.user';
								break;
							case 'agent':
								$event_trigger = 'updated.agent';
								break;
							default:
								$event_trigger = 'updated';
						}
					}

					break;
			}

			if ($event_trigger) {
				$this->out("-- Updated {$trigger['id']} to $event_trigger");
				$this->container->getDb()->update('ticket_triggers', array(
					'terms' => serialize($terms),
					'event_trigger' => $event_trigger
				), array('id' => $trigger['id']));
			}
		}

		#----------------------------------------
		# Set empty arrays for empty options
		#----------------------------------------

		$this->out("Set default empty option arrays");
		$this->execMutateSql("UPDATE ticket_triggers SET event_trigger_options = 'a:0:{}' WHERE event_trigger_options IS NULL OR event_trigger_options = ''");
		$this->execMutateSql("UPDATE ticket_triggers SET terms_any = 'a:0:{}' WHERE terms_any IS NULL OR terms_any = ''");
	}

	protected function snipTerm(array $terms, $find_term, &$found_val, array $hit_terms = array(), array &$hit_check = array())
	{
		$set_terms = array();

		foreach ($terms as $term) {
			if ($term['type'] == $find_term) {
				$found_val = $term;
			} else {
				if ($hit_terms && in_array($term['type'], $hit_terms)) {
					$hit_check[] = $term['type'];
				}
				$set_terms[] = $term;
			}
		}

		return $set_terms;
	}
}