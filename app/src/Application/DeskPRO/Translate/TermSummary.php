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
 * @category Translate
 */

namespace Application\DeskPRO\Translate;

use Application\DeskPRO\App;
use Orb\Util\Arrays;
use Orb\Util\Util;

use Application\DeskPRO\Searcher\OrganizationSearch;

/**
 * Summarizes terms
 */
class TermSummary
{
	const OP_IS          = 'is';
	const OP_NOT         = 'not';
	const OP_LT          = 'lt';
	const OP_GT          = 'gt';
	const OP_LTE         = 'lte';
	const OP_GTE         = 'gte';
	const OP_BETWEEN     = 'between';
	const OP_CONTAINS    = 'contains';
	const OP_NOTCONTAINS = 'notcontains';
	const OP_NOOP        = null;
	const OP_IS_REGEX    = 'is_regex';
	const OP_NOT_REGEX   = 'not_regex';

	const OP_CHANGED            = 'changed';
	const OP_CHANGED_TO         = 'changed_to';
	const OP_CHANGED_FROM       = 'changed_from';
	const OP_NOT_CHANGED_TO     = 'not_changed_to';
	const OP_NOT_CHANGED_FROM   = 'not_changed_from';

	const OP_CHANGED_TO_GTE         = 'changed_to_gte';
	const OP_CHANGED_TO_LTE         = 'changed_to_lte';
	const OP_CHANGED_FROM_GTE       = 'changed_from_gte';
	const OP_CHANGED_FROM_LTE       = 'changed_from_lte';
	const OP_NOT_CHANGED_TO_GTE     = 'not_changed_to_gte';
	const OP_NOT_CHANGED_TO_LTE     = 'not_changed_to_let';
	const OP_NOT_CHANGED_FROM_GTE   = 'not_changed_from_gte';
	const OP_NOT_CHANGED_FROM_LTE   = 'not_changed_from_lte';

	public function getSummary($term, $op, $choice)
	{
		$tr = App::getTranslator();
		$summary = false;

		$term_id = null;

		$m = null;
		if (preg_match('#^(.*?)\[(.*?)\]$#', $term, $m)) {
			$term = $m[1];
			$term_id = $m[2];
		}

		switch ($term) {
			case 'id':
				$summary = $this->_rangeSummary($tr->phrase('agent.general.id'), $op, $choice);
				break;

			case 'text':
				if (is_array($choice)) {
					$choice = array_pop($choice);
				}
				$summary = $tr->phrase('agent.general.content_matches_summary', array('pattern' => $choice));
				break;

			case 'agent_performer':
				$summary = $this->_choiceSummary("Agent performer", $op, $choice['agent_ids'], function($choice) {
					$titles = App::getDataService('Agent')->getNames((array)$choice);
					return $titles;
				});
				break;

			case 'is_via_email':
				return 'Update is triggered by an email';
				break;

			case 'is_via_email_reply':
				return 'Update is triggered by an email reply';
				break;

			case 'is_via_interface':
				return 'Update is triggered from the web interface';
				break;

			case 'user_performer_email':
				$summary = $this->_stringMatchSummary("User performer email address", $op, $choice['user_email']);
				break;

			case 'department':
				$summary = $this->_choiceSummary($tr->phrase('agent.general.department'), $op, $choice, function($choice) {
					$titles = App::getDataService('Department')->getNames((array)$choice);
					return $titles;
				});
				break;

			case 'ticket_deleted':
				$summary = $tr->phrase('agent.tickets.ticket_is_deleted');
				break;

			case 'ticket_category':
				$summary = $this->_choiceSummary($tr->phrase('agent.general.category'), $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:TicketCategory')->getNames((array)$choice);
					return $titles;
				});
				break;

			case 'product':
				$summary = $this->_choiceSummary($tr->phrase('agent.general.product'), $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:Product')->getNames((array)$choice);
					return $titles;
				});
				break;

			case 'ticket_priority':
				$summary = $this->_choiceSummary($tr->phrase('agent.general.priority'), $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:TicketPriority')->getNames((array)$choice);
					return $titles;
				});
				break;

			case 'ticket_urgency':
				$summary = $this->_rangeSummary($tr->phrase('agent.general.urgency'), $op, $choice);
				break;

			case 'date_created':
				$summary = $this->_dateRangeSummary($tr->phrase('agent.general.date_created'), $op, $choice);
				break;

			case 'date_resolved':
				$summary = $this->_dateRangeSummary($tr->phrase('agent.general.date_resolved'), $op, $choice);
				break;

			case 'date_closed':
				$summary = $this->_dateRangeSummary($tr->phrase('agent.general.date_closed'), $op, $choice);
				break;

			case 'date_last_user_reply':
				$summary = $this->_dateRangeSummary($tr->phrase('agent.general.date_of_last_user_reply'), $op, $choice);
				break;

			case 'date_last_agent_reply':
				$summary = $this->_dateRangeSummary($tr->phrase('agent.general.date_of_last_agent_reply'), $op, $choice);
				break;

			case 'ticket_workflow':
				$summary = $this->_choiceSummary($tr->phrase('agent.general.workflow'), $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:TicketWorkflow')->getNames((array)$choice);
					return $titles;
				});
				break;

			case 'language':
				$summary = $this->_choiceSummary($tr->phrase('agent.general.language'), $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:Language')->getTitles((array)$choice);
					return $titles;
				});
				break;

			case 'agent':
				$info = $this->_normalizeAgentChoice($choice);
				$unassigned = $info['unassigned'];
				$agent_ids = $info['agent_ids'];
				$not_id = $info['not_id'];

				if (!is_array($agent_ids)) {
					$agent_ids = array($agent_ids);
				}

				$names = array();
				if ($unassigned) {
					$names[] = $tr->phrase('agent.general.unassigned');
				}
				if ($agent_ids) {
					$names = array_merge($names, App::getContainer()->getAgentData()->getNames($agent_ids));
				}
				if ($not_id) {
					$summary = $tr->phrase('agent.general.agent_is_not_me');
				} else {
					if ($op == self::OP_IS || $op == self::OP_CONTAINS) {
						$summary = 'Agent is ' . implode(', ', $names);
					} else {
						$summary = 'Agent is not ' . implode(', ', $names);
					}
				}
				break;

			case 'agent_team':
				$info = $this->_normalizeAgentTeamChoice($choice);
				$team_ids = $info['team_ids'];
				$not_ids = $info['not_ids'];
				$no_team = $info['no_team'];

				if ($no_team) {
					$summary = $this->_choiceSummary($tr->phrase('agent.general.agent_team'), $op, $tr->phrase('agent.general.agent_team'));

				} else {
					if ($team_ids) {
						$summary = $this->_choiceSummary($tr->phrase('agent.general.agent_team'), $op, $team_ids, function($choice) {
							$titles = App::getEntityRepository('DeskPRO:AgentTeam')->getTeamNames((array)$choice);
							return $titles;
						});
					}

					if ($not_ids) {
						$summary = $this->_choiceSummary($tr->phrase('agent.general.agent_team'), 'not', $not_ids, function($choice) {
							$titles = App::getEntityRepository('DeskPRO:AgentTeam')->getTeamNames((array)$choice);
							return $titles;
						});
					}
				}
				break;

			case 'ticket_status':
				if (isset($choice['status'])) {
					$choice = $choice['status'];
				}
				foreach ((array)$choice as $c) {
					if (strpos($c, '.') !== false) {
						$choice_str[] = $tr->phrase('agent.tickets.hidden_status_' . $c);
					} else {
						$choice_str[] = $tr->phrase('agent.tickets.status_' . $c);
					}
				}

				$choice_str = implode(' ' . $tr->phrase('agent.general.or_sep') . ' ', $choice_str);
				$summary = $this->_choiceSummary($tr->phrase('agent.general.status'), $op, $choice_str);
				break;

			case 'ticket_status_hidden':
				$choice_str = array();

				foreach ((array)$choice as $c) {
					$choice_str[] = $tr->phrase('agent.tickets.hidden_status_' . $c);
				}

				$choice_str = implode(', ', $choice_str);
				break;

			case 'feedback_rating':
				$choice = isset($choice['rating']) ? $choice['rating'] : 'set';

				if ($choice == 'set') {
					if ($op == self::OP_IS) {
						$summary = 'Ticket feedback has been submitted';
					} else {
						$summary = 'Ticket feedback has not been submitted';
					}
				} else {
					$op = $op == self::OP_IS ? 'is' : 'is not';
					if ($choice == 'positive') {
						$summary = "Ticket feedback $op positive";
					} elseif ($choice == 'negative') {
						$summary = "Ticket feedback $op negative";
					} else {
						$summary = "Ticket feedback $op neutral";
					}
				}
				break;

			case 'sla':
				$sla = App::getEntityRepository('DeskPRO:Sla')->find($choice['sla_id']);
				$summary = $this->_choiceSummary($tr->phrase('agent.general.sla'), $op, ($sla ? $sla->title : '[unknown]'));
				break;

			case 'sla_status':
				// todo: phrase the status
				if (empty($choice['sla_id'])) {
					$summary = $this->_choiceSummary($tr->phrase('agent.general.sla_status'), $op, $choice['sla_status'] . " for any SLA");
				} else {
					$sla = App::getEntityRepository('DeskPRO:Sla')->find($choice['sla_id']);
					$summary = $this->_choiceSummary($tr->phrase('agent.general.sla_status'), $op, $choice['sla_status'] . " for SLA " . ($sla ? $sla->title : '[unknown]'));
				}
				break;

			case 'ticket_hold':
				$summary = $tr->phrase('agent.tickets.tickets_on_hold');
				break;

			case 'organization':
				$summary = $this->_choiceSummary("Organization", $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:Organization')->getOrganizationNames((array)$choice);
					return $titles;
				});
				break;

			case 'usergroup':
				$summary = $this->_choiceSummary("Usergroup", $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:Usergroup')->getUsergroupNames((array)$choice);
					return $titles;
				});
				break;

			case 'email':
				$name = array_pop($choice);
				$summary = $this->_choiceSummary($tr->phrase('agent.general.email'), $op, $name);
				break;

			case 'email_domain':
				$name = array_pop($choice);
				$summary = $this->_choiceSummary($tr->phrase('agent.general.email_domain'), $op, $name);
				break;

			case 'name':
				$name = array_pop($choice);
				$summary = $this->_choiceSummary($tr->phrase('agent.general.name'), $op, $name);
				break;

			case 'ticket_participant':
				$choice_info = $this->_normalizeAgentChoice($choice);
				if (!empty($choice_info['agent_ids'])) {
					$choice = $choice_info['agent_ids'];
					$summary = $this->_choiceSummary($tr->phrase('agent.general.followers'), $op, $choice, function($choice) {
						$titles = App::getEntityRepository('DeskPRO:Person')->getAgentNames((array)$choice);
						return $titles;
					}, true);
				}
				break;

			case 'ticket_subject':
				switch ($op) {
					case self::OP_IS:
						$summary = $tr->phrase('agent.general.x_is_y', array('field' => $tr->phrase('agent.general.subject'), 'value' => $choice['subject']));
						break;
					case self::OP_CONTAINS:
						$summary = $tr->phrase('agent.general.x_include_y', array('field' => $tr->phrase('agent.general.subject'), 'value' => $choice['subject']));
						break;
					case self::OP_NOTCONTAINS:
						$summary = $tr->phrase('agent.general.x_not_include_y', array('field' => $tr->phrase('agent.general.subject'), 'value' => $choice['subject']));
						break;
					default:
						$summary = $tr->phrase('agent.general.x_is_not_y', array('field' => $tr->phrase('agent.general.subject'), 'value' => $choice['subject']));
						break;
				}
				break;

			case 'ticket_sent_to_address':
				switch ($op) {
					case self::OP_IS:
						$summary = $tr->phrase('agent.general.x_is_y', array('field' => 'Ticket sent to address', 'value' => $choice['sent_to_address']));
						break;
					case self::OP_CONTAINS:
						$summary = $tr->phrase('agent.general.x_include_y', array('field' => 'Ticket sent to address', 'value' => $choice['sent_to_address']));
						break;
					case self::OP_NOTCONTAINS:
						$summary = $tr->phrase('agent.general.x_not_include_y', array('field' => 'Ticket sent to address', 'value' => $choice['sent_to_address']));
						break;
					default:
						$summary = $tr->phrase('agent.general.x_is_not_y', array('field' => 'Ticket sent to address', 'value' => $choice['sent_to_address']));
						break;
				}
				break;

			case 'flagged':
				$color = $choice;
				if ($color == 'any') {
					$summary = $tr->phrase('agent.general.flagged');
				} else {
					$summary = $tr->phrase('agent.general.flagged_with_color_summary', array('color' => $color));
				}
				break;

			case 'label':
				$summary = $this->_choiceSummary($tr->phrase('agent.general.label'), $op, $choice);
				break;

			case 'org_label':
				$summary = $this->_choiceSummary('Organisation label', $op, $choice);
				break;

			case 'person_field':
				$field = App::getEntityRepository('DeskPRO:CustomDefPerson')->find($term_id);
				if (!$field) {
					$summary = $term . '.' . $term_id;
					break;
				}

				$search_type = $field->getHandler()->getSearchType();
				$text = $choice['custom_fields']['field_' . $field->getId()];

				switch ($search_type) {
					case 'input':
					case 'value':

						if (is_array($text)) {
							$text = implode(', ', $text);
						}

						if ($op == self::OP_IS) {
							$summary = $tr->phrase('agent.general.x_is_y', array('field' => $field['title'], 'value' => $text));
						} else {
							$summary = $tr->phrase('agent.general.x_is_not_y', array('field' => $field['title'], 'value' => $text));
						}

						break;

					case 'id':
						if (is_array($text)) {
							$real = array();
							foreach ($text as $c_id) {
								$c = $field->getChildById($c_id);
								if ($c) {
									$real[] = $c->getTitle();
								}
							}
							$text = implode(', ', $real);
						}

						if ($op == self::OP_IS OR $op== self::OP_CONTAINS) {
							$summary = $tr->phrase('agent.general.x_is_y', array('field' => $field['title'], 'value' => $text));
						} else {
							$summary = $tr->phrase('agent.general.x_is_not_y', array('field' => $field['title'], 'value' => $text));
						}
						break;
				}
				break; // end TERM_PERSON_FIELD

			case 'ticket_field':
				$field = App::getEntityRepository('DeskPRO:CustomDefTicket')->find($term_id);
				if (!$field) {
					$summary = $term . '.' . $term_id;
					break;
				}

				$search_type = $field->getHandler()->getSearchType();
				$text = $choice['custom_fields']['field_' . $field->getId()];

				switch ($search_type) {
					case 'input':
					case 'value':

						if (is_array($text)) {
							$text = implode(', ', $text);
						}

						if ($op == self::OP_IS) {
							$summary = $tr->phrase('agent.general.x_is_y', array('field' => $field['title'], 'value' => $text));
						} else {
							$summary = $tr->phrase('agent.general.x_is_not_y', array('field' => $field['title'], 'value' => $text));
						}

						break;

					case 'id':
						if (is_array($text)) {
							$real = array();
							foreach ($text as $c_id) {
								$c = $field->getChildById($c_id);
								if ($c) {
									$real[] = $c->getTitle();
								}
							}
							$text = implode(', ', $real);
						}

						if ($op == self::OP_IS OR $op== self::OP_CONTAINS) {
							$summary = $tr->phrase('agent.general.x_is_y', array('field' => $field['title'], 'value' => $text));
						} else {
							$summary = $tr->phrase('agent.general.x_is_not_y', array('field' => $field['title'], 'value' => $text));
						}
						break;
				}
				break; // end break TERM_TICKET_FIELD

			case 'user_waiting':
				$summary = $tr->phrase('agent.general.user_waiting_x', array('time' => \Orb\Util\Dates::secsToReadable($choice)));
				break;

			case 'agent_waiting':
				$summary = $tr->phrase('agent.general.agent_waiting_x', array('time' => \Orb\Util\Dates::secsToReadable($choice)));
				break;

			case 'total_user_waiting':
				$summary = $tr->phrase('agent.general.total_user_waiting_x', array('time' => \Orb\Util\Dates::secsToReadable($choice)));
				break;

			case 'ticket_creation_system':
				$vals = array();

				foreach ((array)$choice as $c) {
					$vals[] = $tr->phrase('agent.tickets.creation_system_' . str_replace('.', '_', $c));
				}

				$vals = implode(', ', $vals);

				$summary = $tr->phrase('agent.tickets.creation_system_via') . ' ' . $vals;
				break;

			case 'gateway_address':
				$summary = $this->_choiceSummary($tr->phrase('agent.tickets.sent_to_gateway_address'), $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:EmailGatewayAddress')->getOptions((array)$choice);
					return $titles;
				});
				break;

			case 'recieving_gateway':
				$summary = $this->_choiceSummary($tr->phrase('agent.tickets.receiving_gateway'), $op, $choice, function($choice) {
					$titles = App::getEntityRepository('DeskPRO:EmailGateway')->getGatewayNames((array)$choice);
					return $titles;
				});
				break;

			case 'robot_email':
				$summary = $tr->phrase('agent.general.email_send_by_robot_summary');
				break;

			case 'time_created':
				$summary = $tr->phrase('agent.general.time_created_summary', array('op' => $op, 'hour' => $choice['hour1'], 'minute' =>$choice['minute1']));

				if (!empty($choice['timezone'])) {
					$summary .= " (" . \Orb\Util\Dates::getTimezoneOffsetString($choice['timezone']) . ")";;
				}

				break;

			case 'current_time':
				$summary = "Time $op {$choice['hour1']}:{$choice['minute1']}";

				if (!empty($choice['timezone'])) {
					$summary .= " (" . \Orb\Util\Dates::getTimezoneOffsetString($choice['timezone']) . ")";;
				}

				break;

			case 'time_last_user_reply':
				$summary = $tr->phrase('agent.general.time_user_reply_summary', array('op' => $op, 'hour' => $choice['hour1'], 'minute' =>$choice['minute1']));
				break;

			case 'day_created':
				foreach ($choice['days'] as &$d) {
					switch ($d) {
						case 0: $d = 'Sunday'; break;
						case 1: $d = 'Monday'; break;
						case 2: $d = 'Tuesday'; break;
						case 3: $d = 'Wednesday'; break;
						case 4: $d = 'Thursday'; break;
						case 5: $d = 'Friday'; break;
						case 6: $d = 'Saturday'; break;
					}
				}
				$summary = $tr->phrase('agent.general.day_created_summary', array('op' => $op, 'days' => implode(', ', $choice['days'])));
				break;

			case 'current_day':
				foreach ($choice['days'] as &$d) {
					switch ($d) {
						case 0: $d = 'Sunday'; break;
						case 1: $d = 'Monday'; break;
						case 2: $d = 'Tuesday'; break;
						case 3: $d = 'Wednesday'; break;
						case 4: $d = 'Thursday'; break;
						case 5: $d = 'Friday'; break;
						case 6: $d = 'Saturday'; break;
					}
				}
				$summary = "Day $op " . implode(', ', $choice['days']);
				break;

			case 'day_last_user_reply':
				$summary = $tr->phrase('agent.general.day_user_replay_summary', array('op' => $op, 'days' => implode(', ', $choice['days'])));
				break;

			case 'is_new_user':
				$summary = $tr->phrase('agent.general.new_user_summary');
				break;

			case 'is_not_new_user':
				$summary = $tr->phrase('agent.general.not_new_user_summary');
				break;

			case 'gateway_account':
				$names = App::getOrm()->getRepository('DeskPRO:EmailGateway')->getGatewayNames((array)$choice['gateway_account']);
				if ($op == self::OP_NOT) {
					$summary = $tr->phrase('agent.general.gateway_is_not_summary', array('names' => implode(', ', $names)));
				} else {
					$summary = $tr->phrase('agent.general.gateway_is_summary', array('names' => implode(', ', $names)));
				}
				break;

			case 'action_performer':
				$summary = 'Performed by ' . $choice['action_performer'];
				break;

			case 'creation_system_option':
				$summary = $this->_stringMatchSummary("Submission URL", $op, $choice);
				break;

			case 'email_from_email':
				$summary = $this->_stringMatchSummary("From email address", $op, $choice);
				break;

			case 'to_address':
			case 'email_to_email':
				$summary = $this->_stringMatchSummary("To email address", $op, $choice);
				break;

			case 'email_to_name':
				$summary = $this->_stringMatchSummary("To name", $op, $choice);
				break;

			case 'email_from_name':
				$summary = $this->_stringMatchSummary("From name", $op, $choice);
				break;

			case 'cc_address':
			case 'email_cc_email':
				$summary = $this->_stringMatchSummary("CC email address", $op, $choice);
				break;

			case 'email_cc_name':
				$summary = $this->_stringMatchSummary("CC name", $op, $choice);
				break;

			case 'email_subject':
				$summary = $this->_stringMatchSummary("Email subject", $op, $choice);
				break;

			case 'email_body':
				$summary = $this->_stringMatchSummary("Email body", $op, $choice);
				break;

			case 'email_header':
				$c = $choice;
				unset($c['header_name']);
				$summary = $this->_stringMatchSummary("Email header " .  $choice['header_name'], $op, $c);
				break;

			case 'message':
				$summary = $this->_stringMatchSummary("Message", $op, $choice);
				break;

			case 'new_reply_agent':
				$summary = $this->_stringMatchSummary("Is a new agent reply", $op, $choice);
				break;

			case 'new_reply_user':
				$summary = $this->_stringMatchSummary("Is a new user reply", $op, $choice);
				break;

			case 'new_reply_note':
				$summary = $this->_stringMatchSummary("Is a new agent note", $op, $choice);
				break;

			case 'email_has_attach':
				$summary = "Email has an attachment";
				break;

			case 'email_account_bcc':
				$summary = "Helpdesk was BCC'd";
				break;

			case 'day_created':
				$days = isset($choice['days']) ? (array)$choice['days'] : array();

				foreach ($days as &$_) {
					switch ($_) {
						case 0: $_ = 'Sunday'; break;
						case 1: $_ = 'Monday'; break;
						case 2: $_ = 'Tuesday'; break;
						case 3: $_ = 'Wednesday'; break;
						case 4: $_ = 'Thursday'; break;
						case 5: $_ = 'Friday'; break;
						case 6: $_ = 'Saturday'; break;
					}
				}

				$summary = "Day created is " . implode(', ', $days);

				break;

			case 'api_key':
				$summary = $this->_choiceSummary('API Key', $op, $choice, function($choice) {
					$titles = App::getDataService('ApiKey')->getApiKeyTitles((array)$choice);
					return $titles;
				});
				break;

			############################################################################################################
			# Organization Terms
			############################################################################################################

			case OrganizationSearch::TERM_NAME:
				$name = array_pop($choice);
				$summary = $this->_stringMatchSummary('Organization name', $op, $name);
				break;

			case OrganizationSearch::TERM_EMAIL_DOMAIN:
				$name = array_pop($choice);
				$summary = $this->_stringMatchSummary('Organization email domain', $op, $name);
				break;

			case OrganizationSearch::TERM_CONTACT_ADDRESS:
			case OrganizationSearch::TERM_CONTACT_IM:
			case OrganizationSearch::TERM_CONTACT_PHONE:
				if ($term == OrganizationSearch::TERM_CONTACT_ADDRESS) $field = 'Contact address';
				if ($term == OrganizationSearch::TERM_CONTACT_IM)      $field = 'Contact IM';
				if ($term == OrganizationSearch::TERM_CONTACT_PHONE)   $field = 'Contact phone';
				$name = array_pop($choice);

				$summary = $this->_stringMatchSummary('Organization ' . $field, $op, $name);
				break;

			case OrganizationSearch::TERM_LABEL:
				$summary = $this->_choiceSummary($tr->phrase('agent.general.label'), $op, $choice);
				break;

			case OrganizationSearch::TERM_ORGANIZATION_FIELD:
				$field = App::getSystemService('OrgFieldsManager')->getFieldFromId($term_id);
				if ($field) {
					switch ($op) {
						case self::OP_IS:
							$summary = $tr->phrase('agent.general.x_is_y', array('field' => $field->title, 'value' => $choice['subject']));
							break;
						case self::OP_CONTAINS:
							$summary = $tr->phrase('agent.general.x_include_y', array('field' => $field->title, 'value' => $choice['subject']));
							break;
						case self::OP_NOTCONTAINS:
							$summary = $tr->phrase('agent.general.x_not_include_y', array('field' => $field->title, 'value' => $choice['subject']));
							break;
						default:
							$summary = $tr->phrase('agent.general.x_is_not_y', array('field' => $field->title, 'value' => $choice['subject']));
							break;
					}
				}
				break;

			case 'org_manager':
				switch ($op) {
					case self::OP_IS:
						$summary = 'Organization has a manager';
						break;
					case self::OP_NOT:
						$summary = 'Organization does not have a manager';
						break;
				}
		}

		return $summary;
	}

	protected function _stringMatchSummary($field, $op, $choice, $suffix_only = false, $force_like = false)
	{
		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		if ($op == self::OP_IS_REGEX || $op	== self::OP_NOT_REGEX) {

			if (is_array($choice)) {
				$choice = array_pop($choice);
			}

			$regex = (string)$choice;

			if (!$regex) {
				return '';
			}

			if ($op == self::OP_IS_REGEX) {
				return "$field matches regex $regex";
			} else {
				return "$field does not match regex $regex";
			}

		} elseif (!$force_like AND ($op == self::OP_IS OR $op == self::OP_NOT)) {
			$choices_in = (array)$choice;
			$choices_in = implode(', ', $choices_in);

			if ($op == self::OP_IS) {
				return "$field is " . $choices_in;
			} else {
				return "$field not is " . $choices_in;
			}

		} else {

			$choices_in = (array)$choice;
			$choices_in = implode(', ', $choices_in);

			if ($op == self::OP_CONTAINS) {
				return "$field contains \"" . $choices_in . '"';
			} else {
				return "$field does not contain " . $choices_in;
			}
		}
	}

	/**
	 * Get a summary string for a term
	 *
	 * @param  $field
	 * @param  $op
	 * @param  $choice
	 * @return string
	 */
	protected function _rangeSummary($field, $op, $choice)
	{
		$summary = '';

		$choice = (array)$choice;
		$choice = array_values($choice);

		$range1 = !empty($choice[0]) ? $choice[0] : null;
		$range2 = !empty($choice[1]) ? $choice[1] : null;

		// There should always be at least one
		if ($range1 === null AND $range2 === null) {
			return '';
		}

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		if ($op == self::OP_BETWEEN && ($range1 === null or $range2 === null)) {
			if ($range1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if ($op == self::OP_BETWEEN) {
			$summary = App::getTranslator()->phrase('agent.general.x_is_between_y_and_z', array(
				'field' => $field,
				'value1' => $range1,
				'value2' => $range2
			));
		} elseif ($op == self::OP_GTE) {
			$summary = App::getTranslator()->phrase('agent.general.x_is_greater_than_y', array(
				'field' => $field,
				'value' => $range1,
			));
		} elseif ($op == self::OP_NOT) {
			$summary = App::getTranslator()->phrase('agent.general.x_is_not_y', array(
				'field' => $field,
				'value' => $range1,
			));
		} elseif ($op == self::OP_IS) {
			$summary = App::getTranslator()->phrase('agent.general.x_is_y', array(
				'field' => $field,
				'value' => $range1,
			));
		} else {
			$summary = App::getTranslator()->phrase('agent.general.x_is_less_than_y', array(
				'field' => $field,
				'value' => $range1,
			));
		}

		return $summary;
	}


	/**
	 * Get summary of the range summary
	 *
	 * @param $field
	 * @param $op
	 * @param $choice
	 * @return string
	 */
	public function _dateRangeSummary($field, $op, $choice)
	{
		$summary = '';

		$choice = (array)$choice;

		$date1 = null;
		if (!empty($choice['date1'])) {
			$date1 = $choice['date1'];
		} else if (!empty($choice['date1_relative']) AND !empty($choice['date1_relative_type'])) {
			return App::getTranslator()->phrase('agent.general.x_is_y', array(
				'field' => $field,
				'value' => (int)$choice['date1_relative'] . " {$choice['date1_relative_type']} ago"
			));
		} else if (!empty($choice[0])) {
			$date1 = $choice[0];
		}

		$date2 = null;
		if (!empty($choice['date2'])) {
			$date2 = $choice['date2'];
		} else if (!empty($choice['date2_relative']) AND !empty($choice['date2_relative_type'])) {
			return App::getTranslator()->phrase('agent.general.x_is_y', array(
				'field' => $field,
				'value' => (int)$choice['date2_relative'] . " {$choice['date2_relative_type']} ago"
			));
		} else if (!empty($choice[1])) {
			$date2 = $choice[1];
		}

		if ($date1 AND !($date1 instanceof \DateTime)) {
			try {
				$date1 = new \DateTime("@{$date1}");
			} catch (\Exception $e) { $date1 = null; }
		}
		if ($date2 AND !($date2 instanceof \DateTime)) {
			try {
				$date2 = new \DateTime("@{$date2}");
			} catch (\Exception $e) { $date2 = null; }
		}

		// There should always be at least one date
		if ($date1 === null AND $date2 === null) {
			return '';
		}

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		if ($op == self::OP_BETWEEN && ($date1 === null or $date2 === null)) {
			if ($date1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if ($op == self::OP_BETWEEN) {
			$summary = App::getTranslator()->phrase('agent.general.x_is_between_y_and_z', array(
				'field' => $field,
				'value1' => $date1->format('M j, Y'),
				'value2' => $date2->format('M j, Y')
			));
		} elseif ($op == self::OP_GTE) {
			$summary = App::getTranslator()->phrase('agent.general.x_after_y', array(
				'field' => $field,
				'value' => $date1->format('M j, Y'),
			));
		} else {
			$summary = App::getTranslator()->phrase('agent.general.x_before_y', array(
				'field' => $field,
				'value' => $date1->format('M j, Y'),
			));
		}

		return $summary;
	}

	/**
	 * @param  $field
	 * @param  $op
	 * @param  $choice
	 * @param bool $is_id
	 * @return string
	 */
	protected function _choiceSummary($field, $op, $choice, $title_callback = null, $always_choice = false)
	{
		$summary = '';

		if (!$choice) {
			return '';
		}

		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		// Normalize op
		if (is_array($choice)) {
			if ($op == self::OP_IS) $op = self::OP_CONTAINS;
			if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;
		} else {
			if ($op == self::OP_CONTAINS) $op = self::OP_IS;
			if ($op == self::OP_NOTCONTAINS) $op = self::OP_NOT;
		}

		if ($always_choice) {
			if ($op == self::OP_IS) $op = self::OP_CONTAINS;
			if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;
		}

		if ($title_callback) {
			$title = call_user_func($title_callback, $choice, $field);

			if (is_array($title)) {
				foreach ((array)$choice as $id) {
					if (!isset($title[$id])) {
						$title[$id] = '<error>Unknow #' . $id . '</error>';
					}
				}
			} else if (!$title) {
				$title = '<error>Unknow #' . $choice . '</error>';
			}
		} else {
			$title = $choice;
		}

		if (is_array($title)) {
			$last = array_pop($title);
			$title = implode(', ', (array)$title);
			if ($title) {
				$title .= ' or ';
			}
			$title .= $last;
		}

		switch ($op) {
			case self::OP_IS:
				$summary = App::getTranslator()->phrase('agent.general.x_is_y', array('field' => $field, 'value' => $title));
				break;
			case self::OP_NOT:
				$summary = App::getTranslator()->phrase('agent.general.x_is_not_y', array('field' => $field, 'value' => $title));
				break;
			case self::OP_CONTAINS:
				$summary = App::getTranslator()->phrase('agent.general.x_is_y', array('field' => $field, 'value' => $title));
				break;
			case self::OP_NOTCONTAINS:
				$summary = App::getTranslator()->phrase('agent.general.x_is_not_y', array('field' => $field, 'value' => $title));
				break;
			case self::OP_CHANGED:
				$summary = "$field changed";
				break;
			case self::OP_CHANGED_TO:
				$summary = "$field changed to $title";
				break;
			case self::OP_CHANGED_FROM:
				$summary = "$field changed from $title";
				break;
		}

		return $summary;
	}


	protected function _normalizeOpAndChoice(&$op, &$choice)
	{
		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		// Normalize op
		if (is_array($choice)) {
			if ($op == self::OP_IS) $op = self::OP_CONTAINS;
			if ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;
		} else {
			if ($op == self::OP_CONTAINS) $op = self::OP_IS;
			if ($op == self::OP_NOTCONTAINS) $op = self::OP_NOT;
		}
	}

	protected function _normalizeAgentChoice($choice)
	{
		if (isset($choice['agent'])) {
			$choice = $choice['agent'];
		}
		$choice = (array)$choice;

		$agent_ids = array();
		$not_id = null;
		$unassigned = false;

		foreach ($choice as $c) {
			$c = (int)$c;
			if ($c === 0) {
				$unassigned = true;
				break;
			} elseif ($c == -1) {
				$agent_ids[] = -1;
			} elseif ($c == -2) {
				$not_id = -1;
			} else {
				$agent_ids = $c;
			}
		}

		return array(
			'agent_ids' => $agent_ids,
			'not_id' => $not_id,
			'unassigned' => $unassigned
		);
	}

	protected function _normalizeAgentTeamChoice($choice)
	{
		$choice = (array)$choice;

		$team_ids = array();
		$not_ids = null;
		$no_team = false;

		$agent = null;

		foreach ($choice as $c) {
			$c = (int)$c;
			if ($c === 0) {
				$no_team = true;
				break;
			} elseif ($c == -1) {
				if ($agent) {
					$team_ids = Arrays::removeFalsey($agent->getAgentTeamIds());
				} else {
					$team_ids = array();
				}
				$team_ids[] = -1;
			} elseif ($c == -2) {
				if ($agent) {
					$not_ids = Arrays::removeFalsey($agent->getAgentTeamIds());
				} else {
					$not_ids = array();
				}
				$not_ids[] = -1;
			} else {
				$team_ids = $c;
			}
		}

		return array(
			'team_ids' => $team_ids,
			'not_ids' => $not_ids,
			'no_team' => $no_team
		);
	}
}
