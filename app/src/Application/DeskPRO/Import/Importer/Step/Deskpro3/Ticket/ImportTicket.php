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

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3\Ticket;

use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Import\Importer\Step\Deskpro3\TicketsRerunStep;
use Application\DeskPRO\Import\Importer\Step\Deskpro3\TicketsStep;
use Application\DeskPRO\Import\Importer\Step\Deskpro3\User\ImportUser;
use Orb\Util\Arrays;

class ImportTicket
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	/**
	 * @var \Application\DeskPRO\Import\Importer\Step\Deskpro3\TicketsStep
	 */
	public $step;

	####################################################################################################################
	# Import a ticket
	####################################################################################################################

	public function importTicket($all_ticket_info)
	{
		$ticket_info = $all_ticket_info['ticket'];
		$ticket_id = $ticket_info['id'];

		#------------------------------
		# Dont import old spam tickets that are removed on cron anyway
		#------------------------------

		if ($ticket_info['nodisplay'] == 'spam' && (time() - $ticket_info['timestamp_opened']) > 1296000 /* 15 days */) {
			return 0;
		}

		#------------------------------
		# Make sure we havent already done it
		#------------------------------

		$check_exist = $this->importer->getMappedNewId('ticket', $ticket_id);
		if ($check_exist) {
			return $check_exist;
		}

		#------------------------------
		# Make the ticket
		#------------------------------

		$search_content = array();

		$new_person_id = $this->importer->getMappedNewId('user', $ticket_info['userid']);
		$new_agent_id = null;
		if ($ticket_info['tech']) {
			$new_agent_id = $this->importer->getMappedNewId('tech', $ticket_info['tech']);
		}

		if (!$new_person_id) {
			if ($this->step instanceof TicketsRerunStep) {
				$import_user = new ImportUser();
				$import_user->importer          = $this->importer;
				$import_user->usersources       = $this->step->usersources;
				$import_user->fieldmanager      = $this->step->user_fieldmanager;
				$import_user->custom_field_info = $this->step->user_custom_field_info;

				$new_person_id = $import_user->importUserId($ticket_info['userid']);
			}

			if (!$new_person_id) {
				return 0;
			}
		}

		$new_department_id = $this->importer->getMappedNewId('ticket_category', $ticket_info['category']);
		if (!$new_department_id) {
			$new_department_id = $this->importer->db->fetchColumn("SELECT id FROM departments ORDER BY id ASC LIMIT 1");
		}

		$new_workflow_id = null;
		if ($ticket_info['workflow']) {
			$new_workflow_id = $this->importer->getMappedNewId('ticket_workflow', $ticket_info['workflow']);
		}

		$new_priority_id = null;
		if ($ticket_info['priority']) {
			$new_priority_id = $this->importer->getMappedNewId('ticket_priority', $ticket_info['priority']);
		}

		$new_org_id = null;
		if ($ticket_info['company']) {
			$new_org_id = $this->importer->getMappedNewId('company', $ticket_info['company']);
		}

		if (!$new_priority_id) $new_priority_id = null;
		if (!$new_workflow_id) $new_workflow_id = null;
		if (!$new_org_id) $new_org_id = null;
		if (!$new_agent_id) $new_agent_id = null;

		$insert_ticket = array(
			'id' => $ticket_info['id'],
			'subject' => trim($ticket_info['subject']),
			'person_id' => $new_person_id,
			'agent_id' => $new_agent_id,
			'department_id' => $new_department_id,
			'workflow_id' => $new_workflow_id,
			'priority_id' => $new_priority_id,
			'organization_id' => $new_org_id,
			'language_id' => 1,
			'ticket_hash' => sha1(microtime(true) . mt_rand(1000,99999)), // bogus hash
			'date_created' => date('Y-m-d H:i:s', $ticket_info['timestamp_opened']),
			'ref' => $ticket_info['ref'],
			'auth' => \Orb\Util\Strings::random(15, \Orb\Util\Strings::CHARS_KEY),
			'urgency' => 1,
		);

		if ($ticket_info['creation'] == 'gateway') {
			$insert_ticket['creation_system'] = Ticket::CREATED_GATEWAY_PERSON;;
		} elseif ($ticket_info['creation'] == 'web' && !$ticket_info['tech_creator']) {
			$insert_ticket['creation_system'] = Ticket::CREATED_WEB_AGENT;
		} else {
			$insert_ticket['creation_system'] = Ticket::CREATED_WEB_PERSON;
		}

		if ($ticket_info['timestamp_closed']) {
			$insert_ticket['date_resolved'] = date('Y-m-d H:i:s', $ticket_info['timestamp_closed']);
		}
		if ($ticket_info['timestamp_lastreply_user']) {
			$insert_ticket['date_last_user_reply'] = date('Y-m-d H:i:s', $ticket_info['timestamp_lastreply_user']);
		}
		if ($ticket_info['timestamp_lastreply_tech']) {
			$insert_ticket['date_last_agent_reply'] = date('Y-m-d H:i:s', $ticket_info['timestamp_lastreply_tech']);
		}
		if ($ticket_info['total_user_waiting']) {
			$insert_ticket['total_user_waiting'] = $ticket_info['total_user_waiting'];
		}
		if ($ticket_info['timestamp_tech_waiting']) {
			$insert_ticket['date_agent_waiting'] = date('Y-m-d H:i:s', $ticket_info['timestamp_tech_waiting']);
		}
		if ($ticket_info['timestamp_user_waiting']) {
			$insert_ticket['date_user_waiting'] = date('Y-m-d H:i:s', $ticket_info['timestamp_user_waiting']);
		}

		if ($ticket_info['timestamp_lastreply']) {
			$insert_ticket['date_status'] = date('Y-m-d H:i:s', $ticket_info['timestamp_lastreply']);
		} else {
			$insert_ticket['date_status'] = date('Y-m-d H:i:s', $ticket_info['timestamp_opened']);
		}

		// Set the proper email account (the "From" address)
		if ($ticket_info['accountid'] && isset($this->step->old_gateway_addresses[$ticket_info['accountid']])) {
			$old_address = strtolower($this->step->old_gateway_addresses[$ticket_info['accountid']]['email']);
			foreach ($this->step->gateway_addresses as $address_info) {
				if (strtolower($address_info['match_pattern']) == $old_address) {
					$insert_ticket['email_gateway_id'] = $address_info['email_gateway_id'];
					$insert_ticket['email_gateway_address_id'] = $address_info['id'];
					break;
				}
			}
		}

		if ($this->importer->getConfig('days_until_autoresolve') && $ticket_info['status'] == 'awaiting_user' && $ticket_info['timestamp_lastreply_user']) {
			$days_since = floor((time() - $ticket_info['timestamp_lastreply_user']) / 86400);
			if ($days_since > $this->importer->getConfig('days_until_autoresolve')) {
				$insert_ticket['status'] = 'closed';
				$ticket_info['timestamp_closed'] = $ticket_info['timestamp_lastreply_user'] + ($days_since * 86400);
			}
		}

		switch ($ticket_info['status']) {
			case 'awaiting_tech':
				$insert_ticket['status'] = Ticket::STATUS_AWAITING_AGENT;

				if ($ticket_info['timestamp_user_waiting']) {
					$insert_ticket['date_status'] = date('Y-m-d H:i:s', $ticket_info['timestamp_user_waiting']);
				}
				break;

			case 'awaiting_user':
				$insert_ticket['status'] = Ticket::STATUS_AWAITING_USER;

				if ($ticket_info['timestamp_tech_waiting']) {
					$insert_ticket['date_status'] = date('Y-m-d H:i:s', $ticket_info['timestamp_tech_waiting']);
				}
				break;

			case 'closed':
				$insert_ticket['status'] = Ticket::STATUS_RESOLVED;

				if ($this->importer->isArchiveEnabled()) {
					if ($ticket_info['timestamp_closed']) {
						$time_closed = time() - $ticket_info['timestamp_closed'];
					} else {
						// Buggy v3 with no closed date means we can just use the opened date
						$time_closed = time() - $ticket_info['timestamp_opened'];
					}

					if ($time_closed > $this->importer->getArchiveTime()) {
						$insert_ticket['status'] = Ticket::STATUS_CLOSED;
					}
				}

				break;

			case 'nodisplay':
				$insert_ticket['status'] = Ticket::STATUS_HIDDEN;
				switch ($ticket_info['nodisplay']) {
					case 'spam':
						$insert_ticket['hidden_status'] = Ticket::HIDDEN_STATUS_SPAM;
						break;
					case 'validating':
						$insert_ticket['hidden_status'] = TIcket::HIDDEN_STATUS_VALIDATING;
						break;
				}
				break;
		}

		$this->importer->db->insert('tickets', $insert_ticket);
		$insert_ticket['id'] = $this->importer->db->lastInsertId();

		$this->importer->saveMappedId('ticket', $ticket_id, $insert_ticket['id'], true);

		$search_content[] = $ticket_info['subject'];

		// Save old ref and auth used in gateways
		$this->importer->db->insert('import_datastore', array(
			'typename' => 'dp3_ticketref_' . $ticket_info['ref'],
			'data' => serialize(array('new_id' => $insert_ticket['id'], 'old_auth' => $ticket_info['authcode']))
		));

		// Save the old language data so we can reconnect it later
		if ($ticket_info['language'] && $ticket_info['language'] != 1) {
			$this->importer->db->insert('import_datastore', array(
				'typename' => 'dp3_ticketlang_' . $insert_ticket['id'],
				'data' => $ticket_info['language']
			));
		}

		#------------------------------
		# Notes
		#------------------------------

		// used in ticket logs below
		$note_map = array();

		$ticket_notes = $all_ticket_info['ticket_notes'];
		foreach ($ticket_notes as $note_info) {

			$pid = $this->importer->getMappedNewId('tech', $note_info['techid']);
			if (!$pid) {
				continue;
			}

			$insert_message = array();
			$insert_message['message_hash'] = sha1(microtime(true) . mt_rand(1000,99999)); // bogus hash
			$insert_message['message'] = nl2br(htmlspecialchars(trim($note_info['note']), \ENT_QUOTES));
			$insert_message['person_id'] = $pid;
			$insert_message['ticket_id'] = $insert_ticket['id'];
			$insert_message['is_agent_note'] = 1;
			$insert_message['creation_system'] = 'web';
			$insert_message['date_created'] = date('Y-m-d H:i:s', $note_info['timestamp']);

			$search_content[] = $note_info['note'];

			$this->importer->db->insert('tickets_messages', $insert_message);

			$note_map[$note_info['id']] = $this->importer->db->lastInsertId();

			$this->importer->saveMappedId('ticket_note', $note_info['id'], $note_map[$note_info['id']], true);
		}

		#------------------------------
		# Messages
		#------------------------------

		$first_agent_reply_ts = null;
		$first_agent_reply = null;
		$last_agent_reply = null;
		$last_user_reply = null;

		$first_charset = null;
		$ticket_messages = $all_ticket_info['ticket_message'];

		// used in ticket logs below
		$message_map = array();

		$last_agent_message_id = 0;

		foreach ($ticket_messages as $message_info) {

			if ($message_info['techid']) {
				$pid = $this->importer->getMappedNewId('tech', $message_info['techid']);
				if (!$first_agent_reply) {
					$first_agent_reply_ts = $message_info['timestamp'];
					$first_agent_reply = date('Y-m-d H:i:s', $message_info['timestamp']);
				}
				$last_agent_reply = date('Y-m-d H:i:s', $message_info['timestamp']);
			} else {
				$pid = $this->importer->getMappedNewId('user', $message_info['userid']);
				$last_user_reply = date('Y-m-d H:i:s', $message_info['timestamp']);
			}
			if (!$pid) {
				continue;
			}

			$message_info['message'] = trim($message_info['message']);

			$insert_message = array();
			$insert_message['message_hash'] = sha1(microtime(true) . mt_rand(1000,99999)); // bogus hash
			$insert_message['message'] = $message_info['message'];
			$insert_message['person_id'] = $pid;
			$insert_message['ticket_id'] = $insert_ticket['id'];
			$insert_message['creation_system'] = 'web';
			$insert_message['date_created'] = date('Y-m-d H:i:s', $message_info['timestamp']);
			$insert_message['ip_address'] = $message_info['ipaddress'];

			// Attempt to fix malformed email emssages to that wrap with ='s and =20
			// This is a quick test to see if theres a line that ends with an = which marks a soft warp
			// in quoted-printable, which is a pretty good indicator we can use
			if (preg_match('#=$#', $insert_message['message'])) {
				$new_msg = @quoted_printable_decode($insert_message['message']);
				if ($new_msg) {
					$insert_message['message'] = $new_msg;
				}
			}

			$save_raw = false;
			$orig_charset = $message_info['charset'];

			// Fix common missing charsets
			if (strtoupper($message_info['charset']) == 'US-ASCII' || !trim($message_info['charset'])) {
				$message_info['charset'] = 'ISO-8859-1';
			}

			if (function_exists('mb_detect_encoding')) {
				$charset = mb_detect_encoding($message_info['message']);
				if ($charset == 'UTF-8') {
					$message_info['charset'] = 'UTF-8';
				}
			}

			if ($message_info['charset'] && strtoupper($message_info['charset']) != 'UTF-8') {
				$new_msg = \Orb\Util\Strings::convertToUtf8($message_info['message'], $message_info['charset']);
				if ($new_msg) {
					$message_info['message'] = $new_msg;

					if (!$first_charset) {
						$first_charset = $message_info['charset'];
					}
				} else {
					$save_raw = true;
				}
			}

			$insert_message['message'] = \Orb\Util\Strings::htmlEntityDecodeUtf8($insert_message['message']);

			$insert_message['message'] = trim(\Orb\Util\Strings::utf8_bad_strip($insert_message['message']));
			$insert_message['message'] = nl2br(htmlspecialchars($insert_message['message'], \ENT_QUOTES, 'UTF-8'));

			$search_content[] = $message_info['message'];

			$this->importer->db->insert('tickets_messages', $insert_message);
			$insert_message['id'] = $this->importer->db->lastInsertId();

			$this->importer->saveMappedId('ticket_message', $message_info['id'], $insert_message['id'], true);

			$message_map[$message_info['id']] = $insert_message['id'];

			if (!$last_agent_message_id && $message_info['techid']) {
				$last_agent_message_id = $insert_message['id'];
			}

			if ($save_raw) {
				$this->importer->db->insert('tickets_messages_raw', array(
					'message_id' => $insert_message['id'],
					'raw'        => $message_info['message'],
					'charset'    => $orig_charset,
				));
			}
		}

		$up = array();
		if ($first_agent_reply) {
			$up['date_first_agent_reply'] = $first_agent_reply;
			$up['total_to_first_reply'] = $first_agent_reply_ts - $ticket_info['timestamp_opened'];
			if ($up['total_to_first_reply'] < 0) {
				$up['total_to_first_reply'] = 0;
			}
		}
		if ($last_agent_reply) {
			$up['date_last_agent_reply'] = $last_agent_reply;
		}
		if ($last_user_reply) {
			$up['date_last_user_reply'] = $last_user_reply;
		}

		// If we have a valid charset from the first message,
		// we'll convert the subject too
		if ($first_charset) {
			$subject = $ticket_info['subject'];
			$subject = \Orb\Util\Strings::convertToUtf8($subject, $first_charset);
			$subject = \Orb\Util\Strings::htmlEntityDecodeUtf8($subject);
			$subject = trim(\Orb\Util\Strings::utf8_bad_strip($subject));
			if ($subject) {
				$up['subject'] = $subject;
			}
		}

		if ($ticket_info['rating'] && $last_agent_message_id) {
			$insert_ticket_feedback = array(
				'ticket_id'    => $insert_ticket['id'],
				'message_id'   => $last_agent_message_id,
				'person_id'    => $new_person_id,
				'rating'       => ($ticket_info['rating'] < 3 ? -1 : 1),
				'message'      => '',
				'date_created' => date('Y-m-d H:i:s', $ticket_info['timestamp_rating'] ?: time())
			);
			$this->importer->db->insert('ticket_feedback', $insert_ticket_feedback);
		}

		if ($up) {
			$this->importer->db->update('tickets', $up, array('id' => $insert_ticket['id']));
		}

		#------------------------------
		# Search Tables
		#------------------------------

		$search_content = implode(' ', $search_content);

		$this->importer->db->replace('content_search', array(
			'object_type' => 'ticket',
			'object_id' => $insert_ticket['id'],
			'content' => $search_content,
		));

		$fields = array(
			'id', 'language_id', 'department_id', 'category_id', 'priority_id', 'workflow_id', 'product_id', 'person_id', 'agent_id',
			'agent_team_id', 'organization_id', 'email_gateway_id', 'creation_system', 'status', 'urgency', 'is_hold', 'date_created', 'date_first_agent_reply',
			'date_last_agent_reply', 'date_last_user_reply', 'date_agent_waiting', 'date_user_waiting', 'total_user_waiting', 'total_to_first_reply',
		);

		$set_data = array();
		foreach ($fields as $k) {
			if (isset($insert_ticket[$k])) {
				$set_data[$k] = $insert_ticket[$k];
			}
		}

		$set_data_content = $set_data;
		$set_data_content['content'] = $search_content;

		$this->importer->db->replace('tickets_search_message', $set_data_content);
		$this->importer->db->replace('tickets_search_subject', array(
			'id' => $insert_ticket['id'],
			'subject' => $insert_ticket['subject']
		));

		if ($insert_ticket['status'] != 'closed' && $insert_ticket['status'] != 'hidden') {
			$this->importer->db->replace('tickets_search_active', $set_data);
			$this->importer->db->replace('tickets_search_message_active', $set_data_content);
		}

		#------------------------------
		# Attachments
		#------------------------------

		$ticket_attachments = $all_ticket_info['ticket_attachments'];

		// used for add_attach in ticketlog
		$ticket_attach_map = array();
		$ticket_attach_info = array();

		foreach ($ticket_attachments as $attach_info) {

			if ($attach_info['techid']) {
				$pid = $this->importer->getMappedNewId('tech', $attach_info['techid']);
			} else {
				$pid = $this->importer->getMappedNewId('user', $attach_info['userid']);
			}
			if (!$pid) {
				continue;
			}

			$blob_id = $this->importer->getMappedNewId('ticket_attachments-blob', $attach_info['id']);
			if (!$blob_id) {
				continue;
			}

			$insert_attach = array();
			$insert_attach['ticket_id'] = $insert_ticket['id'];
			$insert_attach['person_id'] = $pid;
			$insert_attach['message_id'] = null;

			if ($attach_info['messageid']) {
				$insert_attach['message_id'] = isset($message_map[$attach_info['messageid']]) ? $message_map[$attach_info['messageid']] : null;
			}

			// If the message is invalid or is unset (v3 bug would save "temp attachments" forever)
			// then just add it to first message
			if (!isset($insert_attach['message_id']) || !$insert_attach['message_id']) {
				$insert_attach['message_id'] = Arrays::getFirstItem($message_map);
			}

			if (!$insert_attach['message_id']) {
				$insert_attach['message_id'] = null;
			}

			$insert_attach['blob_id'] = $blob_id;

			$this->importer->db->insert('tickets_attachments', $insert_attach);

			$id = $this->importer->db->lastInsertId();
			$ticket_attach_info[$id] = array('blob_id' => $blob_id, 'filename' => $attach_info['filename'], 'filesize' => 0);
			$ticket_attach_map[$attach_info['id']] = $id;

			$this->importer->saveMappedId('ticket_attach', $attach_info['id'], $id, true);
		}


		#------------------------------
		# Participants
		#------------------------------

		$ticket_parts = $all_ticket_info['ticket_participant'];
		foreach ($ticket_parts as $part_info) {
			if ($part_info['user_type'] == 'tech') {
				$is_agent = true;
				$pid = $this->importer->getMappedNewId('tech', $part_info['user']);
			} else {
				$is_agent = false;
				$pid = $this->importer->getMappedNewId('user', $part_info['user']);
			}

			if (!$pid) {
				continue;
			}

			$insert_tac = array();
			$insert_tac['auth'] = \Orb\Util\Strings::random(6, \Orb\Util\Strings::CHARS_KEY);
			$insert_tac['person_id'] = $pid;
			$insert_tac['ticket_id'] = $insert_ticket['id'];
			$this->importer->db->insert('ticket_access_codes', $insert_tac);
			$insert_tac['id'] = $this->importer->db->lastInsertId();

			$insert_part = array();
			$insert_part['person_id'] = $pid;
			$insert_part['ticket_id'] = $insert_ticket['id'];
			$insert_part['access_code_id'] = $insert_tac['id'];
			$this->importer->db->insert('tickets_participants', $insert_part);
		}

		#------------------------------
		# Ticket reminders become tasks
		#------------------------------

		$ticket_reminders = $all_ticket_info['tech_ticket_watch'];

		foreach ($ticket_reminders as $reminder) {
			$agent_id = $this->importer->getMappedNewId('tech', $reminder['techid']);
			if (!$agent_id) {
				continue;
			}

			$insert_task = array();
			$insert_task['person_id'] = $agent_id;
			$insert_task['title'] = 'Ticket Reminder: ' . $insert_ticket['subject'];
			$insert_task['assigned_agent_id'] = $agent_id;
			$insert_task['date_created'] = date('Y-m-d H:i:s', $reminder['timestamp_created']);
			if ($reminder['completed']) {
				$insert_task['is_completed'] = 1;
				$insert_task['date_completed'] = date('Y-m-d H:i:s', $reminder['timestamp_complete']);
			}

			$this->importer->db->insert('tasks', $insert_task);
			$insert_task['id'] = $this->importer->db->lastInsertId();

			$insert_task_assoc = array();
			$insert_task_assoc['task_id'] = $insert_task['id'];
			$insert_task_assoc['ticket_id'] = $insert_ticket['id'];
			$insert_task_assoc['assoc_type'] = 'ticket';

			$this->importer->db->insert('task_associations', $insert_task_assoc);
		}

		#------------------------------
		# Ticket log
		#------------------------------

		$this->importer->db->insert('tickets_logs', array(
			'ticket_id' => $insert_ticket['id'],
			'action_type' => 'free',
			'date_created' => date('Y-m-d H:i:s', $ticket_info['timestamp_opened']),
			'details' => serialize(array(
				'message' => "Ticket imported. (Ticket ID: {$insert_ticket['id']}, Original Ticket ID: {$ticket_info['id']})"
			))
		));

		$ticket_logs = $all_ticket_info['tickets_logs'];

		$log_sql = array();

		foreach ($ticket_logs as $tlog) {
			if (!$tlog['timestamp']) {
				continue;
			}

			$insert_tlog = array(
				'ticket_id' => 0,
				'person_id' => null,
				'action_type' => '',
				'id_object' => null,
				'id_before' => null,
				'id_after' => null,
				'details' => array(),
				'date_created' => date('Y-m-d H:i:s')
			);
			$insert_tlog['ticket_id'] = $insert_ticket['id'];

			if ($tlog['techid']) {
				$insert_tlog['person_id'] = $this->importer->getMappedNewId('tech', $tlog['techid']);
				if (!$insert_tlog['person_id']) {
					continue;
				}
			} elseif ($tlog['userid']) {
				$insert_tlog['person_id'] = $this->importer->getMappedNewId('user', $tlog['userid']);
				if (!$insert_tlog['person_id']) {
					continue;
				}
			} else {
				$insert_tlog['person_id'] = null;
			}

			$insert_tlog['date_created'] = date('Y-m-d H:i:s', $tlog['timestamp']);
			$insert_tlog['action_type'] = null;
			$insert_tlog['details'] = array();

			switch ($tlog['actionlog']) {

				case 'add_attach':
					if (!isset($ticket_attach_map[$tlog['id_before']])) {
						break;
					}

					$id = $ticket_attach_map[$tlog['id_before']];
					$info = $ticket_attach_info[$id];

					$insert_tlog['action_type'] = 'attach_added';
					$insert_tlog['id_after'] = $id;
					$insert_tlog['details'] = array('attach_id' => $id, 'blob_id' => $info['blob_id'], 'filename' => $info['filename'], 'filesize' => $info['filesize']);
					break;

				case 'del_attach':
					$insert_tlog['action_type'] = 'attach_removed';
					$insert_tlog['details'] = array('blob_id' => 0, 'filename' => $tlog['detail_before'], 'filesize' => 0);
					break;

				case 'category':
					$old_id = $new_id = 0;
					if ($tlog['id_before']) {
						$old_id = $this->importer->getMappedNewId('ticket_category', $tlog['id_before']);
						if (!$old_id) {
							break;
						}
					}
					if ($tlog['id_after']) {
						$new_id = $this->importer->getMappedNewId('ticket_category', $tlog['id_after']);
						if (!$new_id) {
							break;
						}
					}

					if ($old_id) {
						$insert_tlog['id_before'] = $old_id;
						$insert_tlog['details']['old_department_id']    = $old_id;
						$insert_tlog['details']['old_department_title'] = $this->step->getThingTitle('department', $old_id);
					} else {
						$insert_tlog['details']['old_department_id']    = 0;
						$insert_tlog['details']['old_department_title'] = '';
					}
					if ($new_id) {
						$insert_tlog['id_before'] = $new_id;
						$insert_tlog['details']['new_department_id']    = $new_id;
						$insert_tlog['details']['new_department_title'] = $this->step->getThingTitle('department', $new_id);
					} else {
						$insert_tlog['details']['new_department_id']    = 0;
						$insert_tlog['details']['new_department_title'] = '';
					}

					$insert_tlog['action_type'] = 'changed_department';
					break;

				case 'workflow':
					$old_id = $new_id = 0;
					if ($tlog['id_before']) {
						$old_id = $this->importer->getMappedNewId('ticket_workflow', $tlog['id_before']);
						if (!$old_id) {
							break;
						}
					}
					if ($tlog['id_after']) {
						$new_id = $this->importer->getMappedNewId('ticket_workflow', $tlog['id_after']);
						if (!$new_id) {
							break;
						}
					}

					if ($old_id) {
						$insert_tlog['id_before'] = $old_id;
						$insert_tlog['details']['old_workflow_id']    = $old_id;
						$insert_tlog['details']['old_workflow_title'] = $this->step->getThingTitle('ticket_workflow', $old_id);
					} else {
						$insert_tlog['details']['old_workflow_id']    = 0;
						$insert_tlog['details']['old_workflow_title'] = '';
					}
					if ($new_id) {
						$insert_tlog['id_before'] = $new_id;
						$insert_tlog['details']['new_workflow_id']    = $new_id;
						$insert_tlog['details']['new_workflow_title'] = $this->step->getThingTitle('ticket_workflow', $new_id);
					} else {
						$insert_tlog['details']['new_workflow_id']    = 0;
						$insert_tlog['details']['new_workflow_title'] = '';
					}

					$insert_tlog['action_type'] = 'changed_workflow';
					break;

				case 'priority':
					$old_id = $new_id = 0;
					if ($tlog['id_before']) {
						$old_id = $this->importer->getMappedNewId('ticket_priority', $tlog['id_before']);
						if (!$old_id) {
							break;
						}
					}
					if ($tlog['id_after']) {
						$new_id = $this->importer->getMappedNewId('ticket_priority', $tlog['id_after']);
						if (!$new_id) {
							break;
						}
					}

					if ($old_id) {
						$insert_tlog['id_before'] = $old_id;
						$insert_tlog['details']['old_priority_id']    = $old_id;
						$insert_tlog['details']['old_priority_title'] = $this->step->getThingTitle('ticket_priority', $old_id);
						$insert_tlog['details']['old_priority_pri']   = $this->step->getThingTitle('ticket_priority_pri', $old_id);
					} else {
						$insert_tlog['details']['old_priority_id']    = 0;
						$insert_tlog['details']['old_priority_title'] = '';
						$insert_tlog['details']['old_priority_pri']   = 0;
					}
					if ($new_id) {
						$insert_tlog['id_before'] = $new_id;
						$insert_tlog['details']['new_priority_id']    = $new_id;
						$insert_tlog['details']['new_priority_title'] = $this->step->getThingTitle('ticket_priority', $new_id);
						$insert_tlog['details']['new_priority_pri']   = $this->step->getThingTitle('ticket_priority_pri', $new_id);
					} else {
						$insert_tlog['details']['new_priority_id']    = 0;
						$insert_tlog['details']['new_priority_title'] = '';
						$insert_tlog['details']['new_priority_pri']   = 0;
					}

					$insert_tlog['action_type'] = 'changed_priority';
					break;

				case 'status':
					if (!$tlog['detail_before'] || !in_array($tlog['detail_before'], array('awaiting_tech', 'awaiting_user', 'closed'))) {
						break;
					}
					if (!$tlog['detail_after'] || !in_array($tlog['detail_after'], array('awaiting_tech', 'awaiting_user', 'closed'))) {
						break;
					}

					if ($tlog['detail_before'] == 'awaiting_tech') {
						$insert_tlog['details']['old_status'] = 'awaiting_agent';
					} elseif ($tlog['detail_before'] == 'awaiting_user') {
						$insert_tlog['details']['old_status'] = 'awaiting_user';
					} else {
						$insert_tlog['details']['old_status'] = 'resolved';
					}

					if ($tlog['detail_after'] == 'awaiting_tech') {
						$insert_tlog['details']['new_status'] = 'awaiting_agent';
					} elseif ($tlog['detail_after'] == 'awaiting_user') {
						$insert_tlog['details']['new_status'] = 'awaiting_user';
					} else {
						$insert_tlog['details']['new_status'] = 'resolved';
					}

					$insert_tlog['id_before']   = Ticket::getStatusInt($insert_tlog['details']['old_status']);
					$insert_tlog['id_after']    = Ticket::getStatusInt($insert_tlog['details']['new_status']);
					$insert_tlog['action_type'] = 'changed_status';
					break;

				case 'spam':
					$insert_tlog['details']['old_status'] = 'awaiting_agent';
					$insert_tlog['details']['new_status'] = 'hidden.spam';

					$insert_tlog['id_before']   = Ticket::getStatusInt($insert_tlog['details']['old_status']);
					$insert_tlog['id_after']    = Ticket::getStatusInt($insert_tlog['details']['new_status']);
					$insert_tlog['action_type'] = 'changed_status';
					break;

				case 'nospam':
					$insert_tlog['details']['old_status'] = 'hidden.spam';
					$insert_tlog['details']['new_status'] = 'awaiting_agent';

					$insert_tlog['id_before']   = Ticket::getStatusInt($insert_tlog['details']['old_status']);
					$insert_tlog['id_after']    = Ticket::getStatusInt($insert_tlog['details']['new_status']);
					$insert_tlog['action_type'] = 'changed_status';
					break;

				case 'tech':
					$old_id = $new_id = 0;
					$old_agent_info = $new_agent_info = array('id' => 0, 'display_name' => '', 'primary_email_address' => '');
					if ($tlog['id_before']) {
						$old_id = $this->importer->getMappedNewId('tech', $tlog['id_before']);
						if (!$old_id) {
							break;
						}
						$old_agent_info = $this->step->getPersonInfo($old_id);
					}
					if ($tlog['id_after']) {
						$new_id = $this->importer->getMappedNewId('tech', $tlog['id_after']);
						if (!$new_id) {
							break;
						}
						$new_agent_info = $this->step->getPersonInfo($new_id);
					}

					if ($old_id) $insert_tlog['id_before'] = $old_id;
					if ($new_id) $insert_tlog['id_after']  = $new_id;

					$insert_tlog['details']['old_agent_id']    = $old_agent_info['id'];
					$insert_tlog['details']['old_agent_name']  = $old_agent_info['display_name'];
					$insert_tlog['details']['old_agent_email'] = $old_agent_info['primary_email_address'];
					$insert_tlog['details']['new_agent_id']    = $new_agent_info['id'];
					$insert_tlog['details']['new_agent_name']  = $new_agent_info['display_name'];
					$insert_tlog['details']['new_agent_email'] = $new_agent_info['primary_email_address'];

					$insert_tlog['action_type'] = 'changed_agent';
					break;

				case 'note':
					if (!isset($note_map[$tlog['id_before']])) {
						break;
					}

					$insert_tlog['id_after'] = $note_map[$tlog['id_before']];
					$insert_tlog['details']['message_id'] = $note_map[$tlog['id_before']];
					$insert_tlog['details']['creation_system'] = Ticket::CREATED_WEB_AGENT;
					$insert_tlog['details']['is_agent_note'] = 1;
					$insert_tlog['details']['is_agent_message'] = 1;

					$insert_tlog['action_type'] = 'message_created';
					break;

				case 'note_deleted':
					$insert_tlog['details']['message_id'] = 0;
					$insert_tlog['details']['is_agent_note'] = 1;
					$insert_tlog['details']['is_agent_message'] = 1;
					$insert_tlog['action_type'] = 'message_removed';
					break;

				case 'message_edit':
					if (!isset($message_map[$tlog['id_before']])) {
						break;
					}
					$insert_tlog['id_after'] = $message_map[$tlog['id_before']];
					$insert_tlog['details']['message_id']     = $message_map[$tlog['id_before']];
					$insert_tlog['details']['message_before'] = $tlog['detail_before'];
					$insert_tlog['details']['message_after']  = $tlog['detail_after'];
					break;

				case 'reply_tech':
					$id_before = isset($message_map[$tlog['id_before']]) ? $message_map[$tlog['id_before']] : null;

					$insert_tlog['id_after'] = $id_before;
					$insert_tlog['details']['message_id'] = $id_before ?: 0;
					$insert_tlog['details']['creation_system'] = $tlog['agent'] == 'gateway' ? Ticket::CREATED_GATEWAY_AGENT : Ticket::CREATED_WEB_AGENT;
					$insert_tlog['details']['is_agent_note'] = 0;
					$insert_tlog['details']['is_agent_message'] = 1;

					$insert_tlog['action_type'] = 'message_created';
					break;

				case 'reply_user':
					$id_before = isset($message_map[$tlog['id_before']]) ? $message_map[$tlog['id_before']] : null;

					$insert_tlog['id_after'] = $id_before;
					$insert_tlog['details']['message_id'] = $id_before ?: 0;
					$insert_tlog['details']['creation_system'] = $tlog['agent'] == 'gateway' ? Ticket::CREATED_GATEWAY_PERSON : Ticket::CREATED_WEB_PERSON;
					$insert_tlog['details']['is_agent_note'] = 0;
					$insert_tlog['details']['is_agent_message'] = 0;

					$insert_tlog['action_type'] = 'message_created';
					break;

				case 'subject':
					$insert_tlog['action_type'] = 'changed_subject';
					$insert_tlog['details']     = array('old_subject' => $tlog['detail_before'], 'new_subject' => $tlog['detail_after']);
					break;

				case 'created':
					$insert_tlog['action_type'] = 'ticket_created';
					$insert_tlog['details']     = array('ticket_id' => $insert_ticket['id']);
					break;

				case 'tech_participant_add':
					$new_id = $this->importer->getMappedNewId('tech', $tlog['id_before']);
					if (!$new_id) {
						break;
					}
					$new_agent_info = $this->step->getPersonInfo($new_id);

					$insert_tlog['id_after'] = $new_id;
					$insert_tlog['details']['person_id'] = $new_agent_info['id'];
					$insert_tlog['details']['name']      = $new_agent_info['display_name'];
					$insert_tlog['details']['email']     = $new_agent_info['primary_email_address'];
					$insert_tlog['details']['is_agent']  = true;

					$insert_tlog['action_type'] = 'participant_added';
					break;

				case 'tech_participant_del':
					$new_id = $this->importer->getMappedNewId('tech', $tlog['id_before']);
					if (!$new_id) {
						break;
					}
					$new_agent_info = $this->step->getPersonInfo($new_id);

					$insert_tlog['id_after'] = $new_id;
					$insert_tlog['details']['person_id'] = $new_agent_info['id'];
					$insert_tlog['details']['name']      = $new_agent_info['display_name'];
					$insert_tlog['details']['email']     = $new_agent_info['primary_email_address'];
					$insert_tlog['details']['is_agent']  = true;

					$insert_tlog['action_type'] = 'participant_removed';
					break;

				case 'user_participant_add':
					if (!$tlog['id_before']) {
						break;
					}
					$new_id = $this->importer->getMappedNewId('user', $tlog['id_before']);
					if (!$new_id) {
						break;
					}
					$new_user_info = $this->step->getPersonInfo($new_id);

					$insert_tlog['id_after'] = $new_id;
					$insert_tlog['details']['person_id'] = $new_user_info['id'];
					$insert_tlog['details']['name']      = $new_user_info['display_name'];
					$insert_tlog['details']['email']     = $new_user_info['primary_email_address'];
					$insert_tlog['details']['is_agent']  = false;

					$insert_tlog['action_type'] = 'participant_added';
					break;

				case 'user_participant_del':
					if (!$tlog['id_before']) {
						break;
					}
					$new_id = $this->importer->getMappedNewId('user', $tlog['id_before']);
					if (!$new_id) {
						break;
					}
					$new_user_info = $this->step->getPersonInfo($new_id);

					$insert_tlog['id_after'] = $new_id;
					$insert_tlog['details']['person_id'] = $new_user_info['id'];
					$insert_tlog['details']['name']      = $new_user_info['display_name'];
					$insert_tlog['details']['email']     = $new_user_info['primary_email_address'];
					$insert_tlog['details']['is_agent']  = false;

					$insert_tlog['action_type'] = 'participant_removed';
					break;

				case 'ticket_email':
					$email_id_before = $email_id_after = null;
					if ($tlog['detail_before']) {
						$email_id_before = $this->importer->db->fetchColumn("SELECT id FROM people_emails WHERE email = ? LIMIT 1", array($tlog['detail_before']));
						if (!$email_id_before) {
							$email_id_before = null;
						}
					}
					if ($tlog['detail_after']) {
						$email_id_before = $this->importer->db->fetchColumn("SELECT id FROM people_emails WHERE email = ? LIMIT 1", array($tlog['detail_after']));
						if (!$email_id_after) {
							$email_id_after = null;
						}
					}

					$insert_tlog['id_before'] = $email_id_before;
					$insert_tlog['id_after']  = $email_id_after;
					$insert_tlog['details']['email_id_before'] = $email_id_before ?: 0;
					$insert_tlog['details']['email_before']    = $tlog['detail_before'];
					$insert_tlog['details']['email_id_after']  = $email_id_before ?: 0;
					$insert_tlog['details']['email_after']     = $tlog['detail_before'];

					$insert_tlog['action_type'] = 'user_email_change';
					break;

				case 'company':
					$id_before = $id_after = null;
					$info_before = $info_after = array(
						'id' => 0,
						'name' => ''
					);

					if ($tlog['id_before']) {
						$id_before = $this->importer->getMappedNewId('company', $tlog['id_before']);
						if (!$id_before) {
							break;
						}
						$info_before = $this->importer->getEm()->find('DeskPRO:Organization', $id_before);
					}
					if ($tlog['id_after']) {
						$id_after = $this->importer->getMappedNewId('company', $tlog['id_after']);
						if (!$id_after) {
							break;
						}
						$info_after = $this->importer->getEm()->find('DeskPRO:Organization', $id_after);
					}

					$insert_tlog['id_before'] = $id_before;
					$insert_tlog['id_after']  = $id_after;
					$insert_tlog['details']['old_org_id']   = $info_before['id'];
					$insert_tlog['details']['old_org_name'] = $info_before['name'];
					$insert_tlog['details']['new_org_id']    = $info_after['id'];
					$insert_tlog['details']['new_org_name']  = $info_after['name'];

					$insert_tlog['action_type'] = 'changed_organization';
					break;

				case 'changed_person':
					$old_id = $new_id = 0;
					$old_user_info = $new_user_info = array('id' => 0, 'display_name' => '', 'primary_email_address' => '');
					if ($tlog['id_before']) {
						$old_id = $this->importer->getMappedNewId('user', $tlog['id_before']);
						if (!$old_id) {
							break;
						}
						$old_user_info = $this->step->getPersonInfo($old_id);
					}
					if ($tlog['id_after']) {
						$new_id = $this->importer->getMappedNewId('user', $tlog['id_after']);
						if (!$new_id) {
							break;
						}
						$new_user_info = $this->step->getPersonInfo($new_id);
					}

					if ($old_id) $insert_tlog['id_before'] = $old_id;
					if ($new_id) $insert_tlog['id_after']  = $new_id;

					$insert_tlog['details']['old_person_id']    = $old_user_info['id'];
					$insert_tlog['details']['old_person_name']  = $old_user_info['display_name'];
					$insert_tlog['details']['old_person_email'] = $old_user_info['primary_email_address'];
					$insert_tlog['details']['new_person_id']    = $new_user_info['id'];
					$insert_tlog['details']['new_person_name']  = $new_user_info['display_name'];
					$insert_tlog['details']['new_person_email'] = $new_user_info['primary_email_address'];

					$insert_tlog['action_type'] = 'changed_agent';
					break;

				case 'email_tech':

					$extra = @unserialize($tlog['extra']);
					if (!$extra) {
						break;
					}

					$who = array();
					foreach ($extra as $techid) {
						$id = $this->importer->getMappedNewId('tech', $techid);
						if ($id) {
							$p = $this->step->getPersonInfo($id);
							$who[] = array(
								'person_id'    => $p['id'],
								'person_name'  => $p['display_name'],
								'person_email' => $p['primary_email_address']
							);
						}
					}

					if (!$who) {
						break;
					}

					$insert_tlog['details']['who_emailed'] = $who;

					if (strpos($tlog['detail_before'], 'reply') !== false) {
						$insert_tlog['details']['type'] = 'newreply';
					} else {
						$insert_tlog['details']['type'] = 'newticket';
					}

					$insert_tlog['action_type'] = 'agent_notify';
					break;

				case 'email_user':
					$id = $this->importer->getMappedNewId('user', $tlog['id_before']);
					if (!$id) {
						break;
					}

					if ($this->importer->getConfig('fast_import')) {
						$p = array(
							'id'                    => $id,
							'display_name'          => 'User #'.$id,
							'primary_email_address' => 'default'
						);
					} else {
						$p = $this->step->getPersonInfo($id);
					}

					$insert_tlog['details']['who_emailed'] = array(array(
						'person_id'    => $p['id'],
						'person_name'  => $p['display_name'],
						'person_email' => $p['primary_email_address']
					));
					$insert_tlog['details']['who_cced'] = array();

					if (strpos($tlog['detail_before'], 'reply') !== false) {
						$insert_tlog['details']['type'] = 'newreply';
					} else {
						$insert_tlog['details']['type'] = 'newticket';
					}

					$insert_tlog['action_type'] = 'user_notify';
					break;

				case 'custom':
					$field_id = $this->importer->getMappedNewId('ticket_def_name', $tlog['extra']);
					if (!$field_id) {
						break;
					}

					$field_title = $this->importer->db->fetchColumn("SELECT title FROM custom_def_ticket WHERE id = ?", array($field_id));

					$insert_tlog['details']['value_before'] = $tlog['detail_before'];
					$insert_tlog['details']['value_after']  = $tlog['detail_after'];
					$insert_tlog['details']['field_id']     = $field_id;
					$insert_tlog['details']['field_name']   = $field_title;

					$insert_tlog['action_type'] = 'changed_custom_field';
					break;

				case 'merge':
					$insert_tlog['id_after'] = $insert_ticket['id'];
					$insert_tlog['details']['new_ticket_id'] = $insert_ticket['id'];
					$insert_tlog['details']['old_ticket_id'] = 0;
					$insert_tlog['action_type'] = 'merged';
					break;

				case 'merge_message':
					if (!isset($message_map[$tlog['id_before']])) {
						break;
					}
					$insert_tlog['id_object'] = $message_map[$tlog['id_before']];
					$insert_tlog['id_before'] = 0;
					$insert_tlog['id_after'] = $insert_ticket['id'];
					$insert_tlog['details']['new_ticket_id'] = $insert_ticket['id'];
					$insert_tlog['details']['old_ticket_id'] = 0;
					$insert_tlog['details']['message_id'] = $message_map[$tlog['id_before']];
					$insert_tlog['action_type'] = 'merged_message';
					break;

				case 'merge_attachment':
					if (!isset($attach_info[$tlog['id_before']])) {
						break;
					}

					$id = $ticket_attach_map[$tlog['id_before']];
					$info = $ticket_attach_info[$id];

					$insert_tlog['id_object'] = $id;
					$insert_tlog['id_before'] = 0;
					$insert_tlog['id_after'] = $insert_ticket['id'];
					$insert_tlog['details']['new_ticket_id'] = $insert_ticket['id'];
					$insert_tlog['details']['old_ticket_id'] = 0;
					$insert_tlog['details']['attach_id'] = $id;
					$insert_tlog['details']['blob_id'] = $info['blob_id'];
					$insert_tlog['details']['filename'] = $info['filename'];
					$insert_tlog['details']['filesize'] = $info['filesize'];
					$insert_tlog['action_type'] = 'merged_attach';
					break;

				case 'split_from':
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['details']['message'] = "Ticket split into " . $tlog['id_after'];
					$insert_tlog['details']['preimport_new_ticket_id'] = $tlog['id_after'];
					break;

				case 'split_message_from':
					if (!isset($message_map[$tlog['id_before']])) {
						break;
					}
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['id_object'] = $message_map[$tlog['id_before']];
					$insert_tlog['details']['message'] = "Message {$tlog['id_before']} split from " . $tlog['id_after'];
					$insert_tlog['details']['message_id'] = $message_map[$tlog['id_before']];
					$insert_tlog['details']['preimport_new_ticket_id'] = $tlog['id_after'];
					break;

				case 'split_message_to':
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['details']['message'] = "Message {$tlog['id_before']} split into " . $tlog['id_after'];
					$insert_tlog['details']['preimport_old_message_id'] = $tlog['id_before'];
					$insert_tlog['details']['preimport_new_ticket_id'] = $tlog['id_after'];
					break;

				case 'split_to':
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['details']['message'] = "Ticket split into " . $tlog['id_after'];
					$insert_tlog['details']['preimport_new_ticket_id'] = $tlog['id_after'];
					break;

				case 'rating':
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['details']['message'] = "User rated " . $tlog['id_after'];
					break;

				case 'billing_added':
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['details']['message'] = "Billing added " . $tlog['detail_before'];
					break;

				case 'billing_deleted':
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['details']['message'] = "Billing removed " . $tlog['detail_before'];
					break;

				case 'cc':
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['details']['message'] = "Ticket Summary CC sent to " . $tlog['detail_before'];
					break;

				case 'cc_single':
					$insert_tlog['action_type'] = 'free';
					$insert_tlog['details']['message'] = "Ticket Reply CC sent to " . $tlog['detail_before'];
					break;

				case 'lock':
					break;

				case 'unlock':
					break;

				case 'pin':
					break;

				case 'unpin':
					break;
			} // switch

			if ($insert_tlog['action_type']) {
				$insert_tlog['details'] = serialize($insert_tlog['details']);

				$log_row = array();
				foreach ($insert_tlog as $k => $v) {
					if (is_null($v)) {
						$log_row[] = 'NULL';
					} elseif (\Orb\Util\Numbers::isInteger($v)) {
						$log_row[] = $v;
					} else {
						$log_row[] = $this->importer->db->quote($v);
					}
				}

				$log_sql[] = "(" . implode(',', $log_row) . ")";
			}
		}

		if ($log_sql) {
			$log_sql = "INSERT INTO tickets_logs (ticket_id, person_id, action_type, id_object, id_before, id_after, details, date_created) VALUES " . implode(',', $log_sql);
			$this->importer->db->executeUpdate($log_sql);
		}

		#------------------------------
		# Custom fields
		#------------------------------

		foreach ($this->step->custom_field_info as $field_info) {
			$name = $field_info['name'];
			if (!isset($ticket_info[$name]) || !$ticket_info[$name]) {
				continue;
			}

			$field = $this->step->fieldmanager->getFieldFromId($this->importer->getMappedNewId('ticket_def', $field_info['id']));
			if (!$field) {
				continue;
			}

			$data = null;
			switch ($field->handler_class) {
				case 'Application\\DeskPRO\\CustomFields\\Handler\\Text':
				case 'Application\\DeskPRO\\CustomFields\\Handler\\Textarea':
					$this->importer->db->insert('custom_data_ticket', array(
						'ticket_id' => $insert_ticket['id'],
						'field_id' => $field->id,
						'input' => $ticket_info[$name]
					));
					break;

				case 'Application\\DeskPRO\\CustomFields\\Handler\\Choice':
					$vals = explode('|||', $ticket_info[$name]);
					foreach ($vals as $val) {
						$new_val = $this->importer->getMappedNewId('ticket_def_choice', $field_info['id'].'_'.$val);
						if ($new_val) {
							$this->importer->db->insert('custom_data_ticket', array(
								'ticket_id'     => $insert_ticket['id'],
								'field_id'      => $new_val,
								'root_field_id' => $field['id'],
								'value'         => 1
							));
						}
					}
					break;
			}
		}

		return $insert_ticket['id'];
	}

	####################################################################################################################
	# Import or update a ticket
	####################################################################################################################

	public function importOrUpdateTicket($all_ticket_info)
	{
		$ticket_info = $all_ticket_info['ticket'];
		$ticket_id = $ticket_info['id'];

		#------------------------------
		# Dont import old spam tickets that are removed on cron anyway
		#------------------------------

		if ($ticket_info['nodisplay'] == 'spam' && (time() - $ticket_info['timestamp_opened']) > 1296000 /* 15 days */) {
			return 0;
		}

		#------------------------------
		# Check if we've done it already
		#------------------------------

		$check_exist = $this->importer->getMappedNewId('ticket', $ticket_id);
		if (!$check_exist) {
			// We havent done it yet, so we should insert it now
			$this->importer->logMessage("Inserting new ticket $ticket_id");
			return $this->importTicket($all_ticket_info);
		}

		$this->importer->logMessage("Updating existing ticket $ticket_id");

		// Already imported the ticket, we need to make sure its up to date

		#------------------------------
		# Make the ticket
		#------------------------------

		$search_content = array();

		$new_person_id = $this->importer->getMappedNewId('user', $ticket_info['userid']);
		$new_agent_id = null;
		if ($ticket_info['tech']) {
			$new_agent_id = $this->importer->getMappedNewId('tech', $ticket_info['tech']);
		}

		if (!$new_person_id) {
			if ($this->step instanceof TicketsRerunStep) {
				$import_user = new ImportUser();
				$import_user->importer          = $this->importer;
				$import_user->usersources       = $this->step->usersources;
				$import_user->fieldmanager      = $this->step->user_fieldmanager;
				$import_user->custom_field_info = $this->step->user_custom_field_info;

				$new_person_id = $import_user->importUserId($ticket_info['userid']);
			}

			if (!$new_person_id) {
				return 0;
			}
		}

		$new_department_id = $this->importer->getMappedNewId('ticket_category', $ticket_info['category']);
		if (!$new_department_id) {
			$new_department_id = $this->importer->db->fetchColumn("SELECT id FROM departments ORDER BY id ASC LIMIT 1");
		}

		$new_workflow_id = null;
		if ($ticket_info['workflow']) {
			$new_workflow_id = $this->importer->getMappedNewId('ticket_workflow', $ticket_info['workflow']);
		}

		$new_priority_id = null;
		if ($ticket_info['priority']) {
			$new_priority_id = $this->importer->getMappedNewId('ticket_priority', $ticket_info['priority']);
		}

		$new_org_id = null;
		if ($ticket_info['company']) {
			$new_org_id = $this->importer->getMappedNewId('company', $ticket_info['company']);
		}

		if (!$new_priority_id) $new_priority_id = null;
		if (!$new_workflow_id) $new_workflow_id = null;
		if (!$new_org_id) $new_org_id = null;
		if (!$new_agent_id) $new_agent_id = null;

		$insert_ticket = array(
			'subject' => trim($ticket_info['subject']),
			'person_id' => $new_person_id,
			'agent_id' => $new_agent_id,
			'department_id' => $new_department_id,
			'workflow_id' => $new_workflow_id,
			'priority_id' => $new_priority_id,
			'organization_id' => $new_org_id,
			'language_id' => 1
		);

		if ($ticket_info['timestamp_closed']) {
			$insert_ticket['date_resolved'] = date('Y-m-d H:i:s', $ticket_info['timestamp_closed']);
		}
		if ($ticket_info['timestamp_lastreply_user']) {
			$insert_ticket['date_last_user_reply'] = date('Y-m-d H:i:s', $ticket_info['timestamp_lastreply_user']);
		}
		if ($ticket_info['timestamp_lastreply_tech']) {
			$insert_ticket['date_last_agent_reply'] = date('Y-m-d H:i:s', $ticket_info['timestamp_lastreply_tech']);
		}
		if ($ticket_info['total_user_waiting']) {
			$insert_ticket['total_user_waiting'] = $ticket_info['total_user_waiting'];
		}
		if ($ticket_info['timestamp_tech_waiting']) {
			$insert_ticket['date_agent_waiting'] = date('Y-m-d H:i:s', $ticket_info['timestamp_tech_waiting']);
		}
		if ($ticket_info['timestamp_user_waiting']) {
			$insert_ticket['date_user_waiting'] = date('Y-m-d H:i:s', $ticket_info['timestamp_user_waiting']);
		}

		if ($ticket_info['timestamp_lastreply']) {
			$insert_ticket['date_status'] = date('Y-m-d H:i:s', $ticket_info['timestamp_lastreply']);
		} else {
			$insert_ticket['date_status'] = date('Y-m-d H:i:s', $ticket_info['timestamp_opened']);
		}

		switch ($ticket_info['status']) {
			case 'awaiting_tech':
				$insert_ticket['status'] = Ticket::STATUS_AWAITING_AGENT;

				if ($ticket_info['timestamp_user_waiting']) {
					$insert_ticket['date_status'] = date('Y-m-d H:i:s', $ticket_info['timestamp_user_waiting']);
				}
				break;

			case 'awaiting_user':
				$insert_ticket['status'] = Ticket::STATUS_AWAITING_USER;

				if ($ticket_info['timestamp_tech_waiting']) {
					$insert_ticket['date_status'] = date('Y-m-d H:i:s', $ticket_info['timestamp_tech_waiting']);
				}
				break;

			case 'closed':
				$insert_ticket['status'] = Ticket::STATUS_RESOLVED;
				break;

			case 'nodisplay':
				$insert_ticket['status'] = Ticket::STATUS_HIDDEN;
				switch ($ticket_info['nodisplay']) {
					case 'spam':
						$insert_ticket['hidden_status'] = Ticket::HIDDEN_STATUS_SPAM;
						break;
					case 'validating':
						$insert_ticket['hidden_status'] = TIcket::HIDDEN_STATUS_VALIDATING;
						break;
				}
				break;
		}

		$this->importer->db->update('tickets', $insert_ticket, array('id' => $ticket_id));
		$insert_ticket['id'] = $ticket_id;

		$search_content[] = $ticket_info['subject'];

		#------------------------------
		# Notes
		#------------------------------

		// used in ticket logs below
		$note_map = array();

		$ticket_notes = $all_ticket_info['ticket_notes'];
		foreach ($ticket_notes as $note_info) {

			$pid = $this->importer->getMappedNewId('tech', $note_info['techid']);
			if (!$pid) {
				continue;
			}

			if ($this->importer->getMappedNewId('ticket_note', $note_info['id'])) {
				continue;
			}

			$insert_message = array();
			$insert_message['message_hash'] = sha1(microtime(true) . mt_rand(1000,99999)); // bogus hash
			$insert_message['message'] = nl2br(htmlspecialchars(trim($note_info['note']), \ENT_QUOTES));
			$insert_message['person_id'] = $pid;
			$insert_message['ticket_id'] = $insert_ticket['id'];
			$insert_message['is_agent_note'] = 1;
			$insert_message['creation_system'] = 'web';
			$insert_message['date_created'] = date('Y-m-d H:i:s', $note_info['timestamp']);

			$search_content[] = $note_info['note'];

			$this->importer->db->insert('tickets_messages', $insert_message);
			$note_map[$note_info['id']] = $this->importer->db->lastInsertId();

			$this->importer->saveMappedId('ticket_note', $note_info['id'], $note_map[$note_info['id']], true);
		}

		#------------------------------
		# Messages
		#------------------------------

		$first_agent_reply_ts = null;
		$first_agent_reply = null;
		$last_agent_reply = null;
		$last_user_reply = null;

		$first_charset = null;
		$ticket_messages = $all_ticket_info['ticket_message'];

		// used in ticket logs below
		$message_map = array();

		$last_agent_message_id = 0;

		foreach ($ticket_messages as $message_info) {

			if ($this->importer->getMappedNewId('ticket_message', $message_info['id'])) {
				continue;
			}

			if ($message_info['techid']) {
				$pid = $this->importer->getMappedNewId('tech', $message_info['techid']);
				if (!$first_agent_reply) {
					$first_agent_reply_ts = $message_info['timestamp'];
					$first_agent_reply = date('Y-m-d H:i:s', $message_info['timestamp']);
				}
				$last_agent_reply = date('Y-m-d H:i:s', $message_info['timestamp']);
			} else {
				$pid = $this->importer->getMappedNewId('user', $message_info['userid']);
				$last_user_reply = date('Y-m-d H:i:s', $message_info['timestamp']);
			}
			if (!$pid) {
				continue;
			}

			$message_info['message'] = trim($message_info['message']);

			$insert_message = array();
			$insert_message['message_hash'] = sha1(microtime(true) . mt_rand(1000,99999)); // bogus hash
			$insert_message['message'] = $message_info['message'];
			$insert_message['person_id'] = $pid;
			$insert_message['ticket_id'] = $insert_ticket['id'];
			$insert_message['creation_system'] = 'web';
			$insert_message['date_created'] = date('Y-m-d H:i:s', $message_info['timestamp']);
			$insert_message['ip_address'] = $message_info['ipaddress'];

			// Attempt to fix malformed email emssages to that wrap with ='s and =20
			// This is a quick test to see if theres a line that ends with an = which marks a soft warp
			// in quoted-printable, which is a pretty good indicator we can use
			if (preg_match('#=$#', $insert_message['message'])) {
				$new_msg = @quoted_printable_decode($insert_message['message']);
				if ($new_msg) {
					$insert_message['message'] = $new_msg;
				}
			}

			$save_raw = false;
			$orig_charset = $message_info['charset'];

			// Fix common missing charsets
			if (strtoupper($message_info['charset']) == 'US-ASCII' || !trim($message_info['charset'])) {
				$message_info['charset'] = 'ISO-8859-1';
			}

			if (function_exists('mb_detect_encoding')) {
				$charset = mb_detect_encoding($message_info['message']);
				if ($charset == 'UTF-8') {
					$message_info['charset'] = 'UTF-8';
				}
			}

			if ($message_info['charset'] && strtoupper($message_info['charset']) != 'UTF-8') {
				$new_msg = \Orb\Util\Strings::convertToUtf8($message_info['message'], $message_info['charset']);
				if ($new_msg) {
					$message_info['message'] = $new_msg;

					if (!$first_charset) {
						$first_charset = $message_info['charset'];
					}
				} else {
					$save_raw = true;
				}
			}

			$insert_message['message'] = \Orb\Util\Strings::htmlEntityDecodeUtf8($insert_message['message']);

			$insert_message['message'] = trim(\Orb\Util\Strings::utf8_bad_strip($insert_message['message']));
			$insert_message['message'] = nl2br(htmlspecialchars($insert_message['message'], \ENT_QUOTES, 'UTF-8'));

			$search_content[] = $message_info['message'];

			$this->importer->db->insert('tickets_messages', $insert_message);
			$insert_message['id'] = $this->importer->db->lastInsertId();

			$this->importer->saveMappedId('ticket_message', $message_info['id'], $insert_message['id'], true);

			$message_map[$message_info['id']] = $insert_message['id'];

			if (!$last_agent_message_id && $message_info['techid']) {
				$last_agent_message_id = $insert_message['id'];
			}

			if ($save_raw) {
				$this->importer->db->insert('tickets_messages_raw', array(
					'message_id' => $insert_message['id'],
					'raw'        => $message_info['message'],
					'charset'    => $orig_charset,
				));
			}
		}

		$up = array();
		if ($last_agent_reply) {
			$up['date_last_agent_reply'] = $last_agent_reply;
		}
		if ($last_user_reply) {
			$up['date_last_user_reply'] = $last_user_reply;
		}

		if ($up) {
			$this->importer->db->update('tickets', $up, array('id' => $insert_ticket['id']));
		}

		#------------------------------
		# Attachments
		#------------------------------

		$ticket_attachments = $all_ticket_info['ticket_attachments'];

		// used for add_attach in ticketlog
		$ticket_attach_map = array();
		$ticket_attach_info = array();

		foreach ($ticket_attachments as $attach_info) {

			if ($this->importer->getMappedNewId('ticket_attach', $attach_info['id'])) {
				continue;
			}

			if ($attach_info['techid']) {
				$pid = $this->importer->getMappedNewId('tech', $attach_info['techid']);
			} else {
				$pid = $this->importer->getMappedNewId('user', $attach_info['userid']);
			}
			if (!$pid) {
				continue;
			}

			$blob_id = $this->importer->getMappedNewId('ticket_attachments-blob', $attach_info['id']);
			if (!$blob_id) {
				continue;
			}

			$insert_attach = array();
			$insert_attach['ticket_id'] = $insert_ticket['id'];
			$insert_attach['person_id'] = $pid;
			$insert_attach['message_id'] = null;

			if ($attach_info['messageid']) {
				$insert_attach['message_id'] = isset($message_map[$attach_info['messageid']]) ? $message_map[$attach_info['messageid']] : null;
			}

			if (!$insert_attach['message_id']) {
				$insert_attach['message_id'] = null;
			}

			$insert_attach['blob_id'] = $blob_id;

			$this->importer->db->insert('tickets_attachments', $insert_attach);

			$id = $this->importer->db->lastInsertId();
			$ticket_attach_info[$id] = array('blob_id' => $blob_id, 'filename' => $attach_info['filename'], 'filesize' => 0);
			$ticket_attach_map[$attach_info['id']] = $id;

			$this->importer->saveMappedId('ticket_attach', $attach_info['id'], $id, true);
		}

		#------------------------------
		# Custom fields
		#------------------------------

		// Just re-insert them
		$this->importer->db->delete('custom_data_ticket', array('ticket_id' => $ticket_id));

		foreach ($this->step->custom_field_info as $field_info) {
			$name = $field_info['name'];
			if (!isset($ticket_info[$name]) || !$ticket_info[$name]) {
				continue;
			}

			$field = $this->step->fieldmanager->getFieldFromId($this->importer->getMappedNewId('ticket_def', $field_info['id']));
			if (!$field) {
				continue;
			}

			$data = null;
			switch ($field->handler_class) {
				case 'Application\\DeskPRO\\CustomFields\\Handler\\Text':
				case 'Application\\DeskPRO\\CustomFields\\Handler\\Textarea':
					$this->importer->db->insert('custom_data_ticket', array(
						'ticket_id' => $insert_ticket['id'],
						'field_id' => $field->id,
						'input' => $ticket_info[$name]
					));
					break;

				case 'Application\\DeskPRO\\CustomFields\\Handler\\Choice':
					$vals = explode('|||', $ticket_info[$name]);
					foreach ($vals as $val) {
						$new_val = $this->importer->getMappedNewId('ticket_def_choice', $field_info['id'].'_'.$val);
						if ($new_val) {
							$this->importer->db->insert('custom_data_ticket', array(
								'ticket_id'     => $insert_ticket['id'],
								'field_id'      => $new_val,
								'root_field_id' => $field['id'],
								'value'         => 1
							));
						}
					}
					break;
			}
		}

		#------------------------------
		# Search Tables
		#------------------------------

		$this->importer->db->delete('tickets_search_message', array('id' => $ticket_id));
		$this->importer->db->delete('tickets_search_subject', array('id' => $ticket_id));
		$this->importer->db->delete('tickets_search_active', array('id' => $ticket_id));
		$this->importer->db->delete('tickets_search_message_active', array('id' => $ticket_id));

		$search_content = implode(' ', $search_content);

		$this->importer->db->replace('content_search', array(
			'object_type' => 'ticket',
			'object_id' => $insert_ticket['id'],
			'content' => $search_content,
		));

		$fields = array(
			'id', 'language_id', 'department_id', 'category_id', 'priority_id', 'workflow_id', 'product_id', 'person_id', 'agent_id',
			'agent_team_id', 'organization_id', 'email_gateway_id', 'creation_system', 'status', 'urgency', 'is_hold', 'date_created', 'date_first_agent_reply',
			'date_last_agent_reply', 'date_last_user_reply', 'date_agent_waiting', 'date_user_waiting', 'total_user_waiting', 'total_to_first_reply',
		);

		$set_data = array();
		foreach ($fields as $k) {
			if (isset($insert_ticket[$k])) {
				$set_data[$k] = $insert_ticket[$k];
			}
		}

		$set_data_content = $set_data;
		$set_data_content['content'] = $search_content;

		$this->importer->db->replace('tickets_search_message', $set_data_content);
		$this->importer->db->replace('tickets_search_subject', array(
			'id' => $insert_ticket['id'],
			'subject' => $insert_ticket['subject']
		));

		if ($insert_ticket['status'] != 'closed' && $insert_ticket['status'] != 'hidden') {
			$this->importer->db->replace('tickets_search_active', $set_data);
			$this->importer->db->replace('tickets_search_message_active', $set_data_content);
		}

		return $ticket_id;
	}
}