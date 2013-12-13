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

namespace Application\DeskPRO\Import\Importer\Step\Zendesk\Ticket;

use Application\DeskPRO\Import\Importer\Step\Zendesk\User\ImportUser;
use Orb\Service\Zendesk\ApiException;
use Orb\Util\Arrays;
use Orb\Util\OptionsArray;

class ImportTicket
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\ZendeskImporter
	 */
	public $importer;

	/**
	 * @var \Application\DeskPRO\CustomFields\FieldManager
	 */
	public $fieldmanager;

	####################################################################################################################
	# import
	####################################################################################################################

	public function import($ticket_info)
	{
		$ticket_id = $ticket_info['id'];

		if ($this->importer->db->fetchColumn("SELECT id FROM tickets WHERE id = ?", array($ticket_id))) {
			// Already imported (skip)
			return $ticket_id;
		}

		if (!$this->importer->getMappedNewId('zd_user_id', $ticket_info['requester_id'])) {
			$import_user = new ImportUser();
			$import_user->importer = $this->importer;
			$import_user->importUserId($ticket_info['requester_id']);
			$this->importer->flushSaveMappedIdBuffer();

			if (!$this->importer->getMappedNewId('zd_user_id', $ticket_info['requester_id'])) {
				throw new \Exception("Unknown user and could not import: {$ticket_info['requester_id']}");
			}
		}

		$search_content = array();
		$search_content[] = $ticket_info['subject'];

		#------------------------------
		# Create the ticket
		#------------------------------

		$insert_ticket = array();
		$insert_ticket['id']            = $ticket_id;
		$insert_ticket['auth']          = \Orb\Util\Strings::random(6, \Orb\Util\Strings::CHARS_KEY);
		$insert_ticket['date_created']  = date('Y-m-d H:i:s', strtotime($ticket_info['created_at']));
		$insert_ticket['person_id']     = $this->importer->getMappedNewId('zd_user_id', $ticket_info['requester_id']);
		$insert_ticket['subject']       = $ticket_info['subject'] ?: '(no subject)';
		$insert_ticket['ref']           = 'TICKET-' . $ticket_id;
		$insert_ticket['department_id'] = $this->importer->getMappedNewId('zd_groupdep_id', $ticket_info['group_id']) ?: null;

		switch ($ticket_info['status']) {
			case 'new':
			case 'open':
			case 'hold':
				$insert_ticket['status'] = 'awaiting_agent';

				if ($ticket_info['status'] == 'hold') {
					$insert_ticket['is_hold'] = 1;
				}
				break;

			case 'pending':
				$insert_ticket['status'] = 'awaiting_user';
				break;

			case 'solved':
			case 'closed':
				$insert_ticket['status'] = 'resolved';
				break;
		}

		if ($ticket_info['organization_id'] && $this->importer->getMappedNewId('zd_org_id', $ticket_info['organization_id'])) {
			$insert_ticket['organization_id'] = $this->importer->getMappedNewId('zd_org_id', $ticket_info['organization_id']);
		}
		if ($ticket_info['assignee_id'] && $this->importer->getMappedNewId('zd_user_id', $ticket_info['assignee_id'])) {
			$insert_ticket['agent_id'] = $this->importer->getMappedNewId('zd_user_id', $ticket_info['assignee_id']);
		}
		if ($ticket_info['group_id'] && $this->importer->getMappedNewId('zd_group_id', $ticket_info['group_id'])) {
			$insert_ticket['agent_team_id'] = $this->importer->getMappedNewId('zd_group_id', $ticket_info['group_id']);
		}

		if ($ticket_info['priority']) {
			switch ($ticket_info['priority']) {
				case 'low':
					$insert_ticket['priority_id'] = 1;
					break;
				case 'normal':
					$insert_ticket['priority_id'] = 2;
					break;
				case 'high':
					$insert_ticket['priority_id'] = 3;
					break;
				case 'urgent':
					$insert_ticket['priority_id'] = 4;
					break;
			}
		}

		$insert_ticket['date_status']            = $insert_ticket['date_created'];
		$insert_ticket['date_first_agent_reply'] = null;
		$insert_ticket['date_last_agent_reply']  = null;
		$insert_ticket['date_last_user_reply']   = $insert_ticket['date_created'];
		$insert_ticket['date_agent_waiting']     = null;
		$insert_ticket['date_user_waiting']      = null;
		$insert_ticket['total_user_waiting']     = 0;
		$insert_ticket['total_to_first_reply']   = 0;

		$this->importer->db->insert('tickets', $insert_ticket);
		$this->importer->saveMappedId('zd_ticekt_id', $insert_ticket['id'], $ticket_id);

		$first_agent_time  = null;
		$last_agent_time   = null;
		$last_user_time    = null;
		$total_user_time   = 0;
		$total_first_reply = 0;

		#------------------------------
		# Insert labels
		#------------------------------

		if ($ticket_info['tags']) {
			$insert_bulk = array();
			foreach ($ticket_info['tags'] as $tag) {
				$row = array();
				$row['ticket_id'] = $ticket_id;
				$row['label'] = strtolower($tag);

				$search_content[] = md5("lbl" . md5(strtolower(trim($tag))));

				$insert_bulk[] = $row;
			}

			$this->importer->db->batchInsert('labels_tickets', $insert_bulk, true);
		}

		#------------------------------
		# Custom fields
		#------------------------------

		if (!empty($ticket_info['custom_fields'])) {
			$insert_field_data = array();

			$all_field_info = Arrays::keyFromData($ticket_info['custom_fields'], 'id', 'value');

			foreach ($all_field_info as $old_field_id => $field_val) {

				$field_id = $this->importer->getMappedNewId('zd_ticket_field_id', $old_field_id);

				$field = $this->fieldmanager->getFieldFromId($field_id);
				if (!$field) {
					continue;
				}

				if ($field_val === null) {
					if ($field->getOption('zd_type') == 'integer') {
						$field_val = 0;
					} else {
						continue;
					}
				}

				switch ($field->handler_class) {
					case 'Application\\DeskPRO\\CustomFields\\Handler\\Text':
					case 'Application\\DeskPRO\\CustomFields\\Handler\\Textarea':
						$insert_field_data[] = array(
							'ticket_id'     => $ticket_id,
							'field_id'      => $field_id,
							'root_field_id' => $field_id,
							'value'         => 0,
							'input'         => $field_val
						);
						break;

					case 'Application\\DeskPRO\\CustomFields\\Handler\\ToggleField':
						$insert_field_data[] = array(
							'ticket_id'     => $ticket_id,
							'field_id'      => $field_id,
							'root_field_id' => $field_id,
							'value'         => 1,
							'input'         => ''
						);
						break;

					case 'Application\\DeskPRO\\CustomFields\\Handler\\Choice':

						$sub_field_id = $this->importer->getMappedNewId('zd_tagger_id', $field_id . '_' . $field_val);
						if (!$sub_field_id) {
							continue;
						}

						$insert_field_data[] = array(
							'ticket_id'     => $ticket_id,
							'field_id'      => $sub_field_id,
							'root_field_id' => $field_id,
							'value'         => 1,
							'input'         => ''
						);
						break;
				}
			}

			if ($insert_field_data) {
				$this->importer->db->batchInsert('custom_data_ticket', $insert_field_data);
			}
		}

		#------------------------------
		# Get reply/log/note data
		#------------------------------

		$add_logs      = array();
		$add_datastore = array();

		$add_logs[] = array(
			'ticket_id'    => $ticket_id,
			'action_type'  => 'free',
			'date_created' => date('Y-m-d H:i:s'),
			'details'      => serialize(array(
				'message'  => 'Ticket imported',
			))
		);

		$audits_raw = $this->importer->zd->getTicketAudits($ticket_id);
		$audits = array();

		// Format into a "flat" structure
		foreach ($audits_raw as $audit) {
			if (empty($audit['events'])) {
				continue;
			}

			foreach ($audit['events'] as $event) {
				$line = $event;
				$line['via'] = $audit['via'];

				if (empty($line['author_id']) || !$line['author_id']) {
					$line['author_id'] = $audit['author_id'];
				}
				if (empty($line['created_at']) || !$line['created_at']) {
					$line['created_at'] = $audit['created_at'];
				}
				if (empty($line['metadata']) || !$line['metadata']) {
					$line['metadata'] = $audit['metadata'];
				}

				$audits[] = $line;
			}
		}

		foreach ($audits as $line) {
			switch ($line['type']) {
				case 'Comment':
					$add_message = array(
						'ticket_id'       => $ticket_id,
						'person_id'       => $this->importer->getMappedNewId('zd_user_id', $line['author_id']) ?: $insert_ticket['person_id'],
						'date_created'    => date('Y-m-d H:i:s', strtotime($line['created_at'])),
						'is_agent_note'   => $line['public'] ? 0 : 1,
						'creation_system' => 'web',
						'message_hash'    => sha1(microtime(true) . mt_rand(1000,99999)), // bogus hash
						'message'         => $line['html_body'],
					);

					if ($add_message['person_id'] != $insert_ticket['person_id']) {
						if (!$first_agent_time) {
							$first_agent_time = $add_message['date_created'];
						}
						$last_agent_time = $add_message['date_created'];
					} else {
						$last_user_time = $add_message['date_created'];;
					}

					$this->importer->db->insert('tickets_messages', $add_message);
					$message_id =  $this->importer->db->lastInsertId();

					$this->importer->saveMappedId('zd_ticekt_message_id', $line['id'], $message_id);

					$search_content[] = $line['body'];

					if (!empty($line['attachments'])) {
						foreach ($line['attachments'] as $attach) {
							$add_datastore[] = array(
								'typename' => 'attach.ticket.' . uniqid('t'.$ticket_id),
								'data'     => serialize(array(
									'type'          => 'ticket',
									'ticket_id'     => $ticket_id,
									'message_id'    => $message_id,
									'person_id'     => $this->importer->getMappedNewId('zd_user_id', $line['author_id']) ?: $insert_ticket['person_id'],
									'is_agent_note' => $line['public'] ? 0 : 1,
									'url'           => $attach['content_url'],
									'zd_attach_id'  => $attach['id'],
									'filename'      => $attach['file_name'],
									'filesize'      => $attach['size'],
									'content_type'  => $attach['content_type'],
								))
							);
						}
					}
					break;
			}
		}

		if ($add_logs) {
			$this->importer->db->batchInsert('tickets_logs', $add_logs);
		}
		if ($add_datastore) {
			$this->importer->db->batchInsert('import_datastore', $add_datastore);
		}



		$update = array();
		if ($first_agent_time) {
			$update['date_first_agent_assign'] = $first_agent_time;
			$update['date_first_agent_reply'] = $first_agent_time;

			if (!$insert_ticket['total_to_first_reply']) {
				$total_first_reply = strtotime($first_agent_time) - strtotime($insert_ticket['date_created']);
				if ($total_first_reply) {
					$update['total_to_first_reply'] = $total_first_reply;
				}
				if (!$insert_ticket['total_user_waiting']) {
					$update['total_user_waiting'] = $total_first_reply;
				}
			}
		}
		if ($last_agent_time) {
			$update['date_last_agent_reply'] = $last_agent_time;
			if ($insert_ticket['status'] == 'awaiting_agent') {
				$update['date_agent_waiting'] = $last_agent_time;
			}
		}
		if ($last_user_time) {
			$update['date_last_user_reply'] = $last_user_time;

			if ($insert_ticket['status'] == 'awaiting_user') {
				$update['date_user_waiting'] = $last_user_time;
			}
		}

		if ($update) {
			$this->importer->db->update('tickets', $update, array('id' => $ticket_id));
		}

		#------------------------------
		# Search Tables
		#------------------------------

		$search_content = implode(' ', $search_content);

		$this->importer->db->replace('content_search', array(
			'object_type' => 'ticket',
			'object_id'   => $ticket_id,
			'content'     => $search_content,
		));

		$fields = array(
			'id', 'department_id', 'priority_id', 'person_id', 'agent_id',
			'agent_team_id', 'organization_id', 'creation_system', 'status', 'is_hold', 'date_created', 'date_first_agent_reply',
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
			'id' => $ticket_id,
			'subject' => $insert_ticket['subject']
		));

		if ($insert_ticket['status'] != 'closed' && $insert_ticket['status'] != 'hidden') {
			$this->importer->db->replace('tickets_search_active', $set_data);
			$this->importer->db->replace('tickets_search_message_active', $set_data_content);
		}

		return $ticket_id;
	}





	####################################################################################################################
	# importOrUpdate
	####################################################################################################################

	public function importOrUpdate($ticket_info)
	{
		$ticket_id = $ticket_info['id'];

		$have_ticket = $this->importer->db->fetchAssoc("SELECT * FROM tickets WHERE id = ?", array($ticket_id));

		// Doesnt exist, insert it now
		if (!$have_ticket) {
			return $this->import($ticket_info);
		}

		// Does exist, need to update it
		if (!$this->importer->getMappedNewId('zd_user_id', $ticket_info['requester_id'])) {
			$import_user = new ImportUser();
			$import_user->importer = $this->importer;
			$import_user->importUserId($ticket_info['requester_id']);

			if (!$this->importer->getMappedNewId('zd_user_id', $ticket_info['requester_id'])) {
				return 0;
			}
		}

		$search_content = array();
		$search_content[] = $ticket_info['subject'];

		#------------------------------
		# Create the ticket
		#------------------------------

		$insert_ticket = array();
		$insert_ticket['person_id']     = $this->importer->getMappedNewId('zd_user_id', $ticket_info['requester_id']);
		$insert_ticket['subject']       = $ticket_info['subject'];
		$insert_ticket['department_id'] = $this->importer->getMappedNewId('zd_groupdep_id', $ticket_info['group_id']) ?: null;

		switch ($ticket_info['status']) {
			case 'new':
			case 'open':
			case 'hold':
				$insert_ticket['status'] = 'awaiting_agent';

				if ($ticket_info['status'] == 'hold') {
					$insert_ticket['is_hold'] = 1;
				}
				break;

			case 'pending':
				$insert_ticket['status'] = 'awaiting_user';
				break;

			case 'solved':
			case 'closed':
				$insert_ticket['status'] = 'resolved';
				break;
		}

		if ($ticket_info['organization_id'] && $this->importer->getMappedNewId('zd_org_id', $ticket_info['organization_id'])) {
			$insert_ticket['organization_id'] = $this->importer->getMappedNewId('zd_org_id', $ticket_info['organization_id']);
		}
		if ($ticket_info['assignee_id'] && $this->importer->getMappedNewId('zd_user_id', $ticket_info['assignee_id'])) {
			$insert_ticket['agent_id'] = $this->importer->getMappedNewId('zd_user_id', $ticket_info['assignee_id']);
		}
		if ($ticket_info['group_id'] && $this->importer->getMappedNewId('zd_group_id', $ticket_info['group_id'])) {
			$insert_ticket['agent_team_id'] = $this->importer->getMappedNewId('zd_group_id', $ticket_info['group_id']);
		}

		if ($ticket_info['priority']) {
			switch ($ticket_info['priority']) {
				case 'low':
					$insert_ticket['priority_id'] = 1;
					break;
				case 'normal':
					$insert_ticket['priority_id'] = 2;
					break;
				case 'high':
					$insert_ticket['priority_id'] = 3;
					break;
				case 'urgent':
					$insert_ticket['priority_id'] = 4;
					break;
			}
		}

		$insert_ticket['date_status']            = $have_ticket['date_created'];
		$insert_ticket['date_first_agent_reply'] = null;
		$insert_ticket['date_last_agent_reply']  = null;
		$insert_ticket['date_last_user_reply']   = $have_ticket['date_created'];
		$insert_ticket['date_agent_waiting']     = null;
		$insert_ticket['date_user_waiting']      = null;
		$insert_ticket['total_user_waiting']     = 0;
		$insert_ticket['total_to_first_reply']   = 0;

		$this->importer->db->update('tickets', $insert_ticket, array('id' => $ticket_id));

		$first_agent_time  = null;
		$last_agent_time   = null;
		$last_user_time    = null;
		$total_user_time   = 0;
		$total_first_reply = 0;

		#------------------------------
		# Custom fields
		#------------------------------

		if (!empty($ticket_info['custom_fields'])) {
			$this->importer->db->delete('custom_data_ticket', array('ticket_id' => $ticket_id));
			$insert_field_data = array();

			$all_field_info = Arrays::keyFromData($ticket_info['custom_fields'], 'id', 'value');

			foreach ($all_field_info as $old_field_id => $field_val) {

				$field_id = $this->importer->getMappedNewId('zd_ticket_field_id', $old_field_id);

				$field = $this->fieldmanager->getFieldFromId($field_id);
				if (!$field) {
					continue;
				}

				if ($field_val === null) {
					if ($field->getOption('zd_type') == 'integer') {
						$field_val = 0;
					} else {
						continue;
					}
				}

				switch ($field->handler_class) {
					case 'Application\\DeskPRO\\CustomFields\\Handler\\Text':
					case 'Application\\DeskPRO\\CustomFields\\Handler\\Textarea':
						$insert_field_data[] = array(
							'ticket_id'     => $ticket_id,
							'field_id'      => $field_id,
							'root_field_id' => $field_id,
							'value'         => 0,
							'input'         => $field_val
						);
						break;

					case 'Application\\DeskPRO\\CustomFields\\Handler\\ToggleField':
						$insert_field_data[] = array(
							'ticket_id'     => $ticket_id,
							'field_id'      => $field_id,
							'root_field_id' => $field_id,
							'value'         => 1,
							'input'         => ''
						);
						break;

					case 'Application\\DeskPRO\\CustomFields\\Handler\\Choice':

						$sub_field_id = $this->importer->getMappedNewId('zd_tagger_id', $field_id . '_' . $field_val);
						if (!$sub_field_id) {
							continue;
						}

						$insert_field_data[] = array(
							'ticket_id'     => $ticket_id,
							'field_id'      => $sub_field_id,
							'root_field_id' => $field_id,
							'value'         => 1,
							'input'         => ''
						);
						break;
				}
			}

			if ($insert_field_data) {
				$this->importer->db->batchInsert('custom_data_ticket', $insert_field_data);
			}
		}

		#------------------------------
		# Get reply/log/note data
		#------------------------------

		$add_logs      = array();
		$add_datastore = array();

		$audits_raw = $this->importer->zd->getTicketAudits($ticket_id);
		$audits = array();

		// Format into a "flat" structure
		foreach ($audits_raw as $audit) {
			if (empty($audit['events'])) {
				continue;
			}

			foreach ($audit['events'] as $event) {
				$line = $event;
				$line['via'] = $audit['via'];

				if (empty($line['author_id']) || !$line['author_id']) {
					$line['author_id'] = $audit['author_id'];
				}
				if (empty($line['created_at']) || !$line['created_at']) {
					$line['created_at'] = $audit['created_at'];
				}
				if (empty($line['metadata']) || !$line['metadata']) {
					$line['metadata'] = $audit['metadata'];
				}

				$audits[] = $line;
			}
		}

		foreach ($audits as $line) {
			switch ($line['type']) {
				case 'Comment':

					if ($this->importer->getMappedNewId('zd_ticekt_message_id', $line['id'])) {
						continue;
					}

					$add_message = array(
						'ticket_id'       => $ticket_id,
						'person_id'       => $this->importer->getMappedNewId('zd_user_id', $line['author_id']) ?: $insert_ticket['person_id'],
						'date_created'    => date('Y-m-d H:i:s', strtotime($line['created_at'])),
						'is_agent_note'   => $line['public'] ? 0 : 1,
						'creation_system' => 'web',
						'message_hash'    => sha1(microtime(true) . mt_rand(1000,99999)), // bogus hash
						'message'         => $line['html_body'],
					);

					if ($add_message['person_id'] != $insert_ticket['person_id']) {
						if (!$first_agent_time) {
							$first_agent_time = $add_message['date_created'];
						}
						$last_agent_time = $add_message['date_created'];
					} else {
						$last_user_time = $add_message['date_created'];;
					}

					$this->importer->db->insert('tickets_messages', $add_message);
					$message_id =  $this->importer->db->lastInsertId();

					$search_content[] = $line['body'];

					if (!empty($line['attachments'])) {
						foreach ($line['attachments'] as $attach) {
							$add_datastore[] = array(
								'typename' => 'attach.ticket.' . uniqid('t'.$ticket_id),
								'data'     => serialize(array(
									'type'          => 'ticket',
									'ticket_id'     => $ticket_id,
									'message_id'    => $message_id,
									'person_id'     => $this->importer->getMappedNewId('zd_user_id', $line['author_id']) ?: $insert_ticket['person_id'],
									'is_agent_note' => $line['public'] ? 0 : 1,
									'url'           => $attach['content_url'],
									'zd_attach_id'  => $attach['id'],
									'filename'      => $attach['file_name'],
									'filesize'      => $attach['size'],
									'content_type'  => $attach['content_type'],
								))
							);
						}
					}
					break;
			}
		}

		if ($add_logs) {
			$this->importer->db->batchInsert('tickets_logs', $add_logs);
		}
		if ($add_datastore) {
			$this->importer->db->batchInsert('import_datastore', $add_datastore);
		}

		$update = array();
		if ($first_agent_time) {
			$update['date_first_agent_assign'] = $first_agent_time;
			$update['date_first_agent_reply'] = $first_agent_time;

			if (!$insert_ticket['total_to_first_reply']) {
				$total_first_reply = strtotime($first_agent_time) - strtotime($have_ticket['date_created']);
				if ($total_first_reply) {
					$update['total_to_first_reply'] = $total_first_reply;
				}
				if (!$insert_ticket['total_user_waiting']) {
					$update['total_user_waiting'] = $total_first_reply;
				}
			}
		}
		if ($last_agent_time) {
			$update['date_last_agent_reply'] = $last_agent_time;
			if ($insert_ticket['status'] == 'awaiting_agent') {
				$update['date_agent_waiting'] = $last_agent_time;
			}
		}
		if ($last_user_time) {
			$update['date_last_user_reply'] = $last_user_time;

			if ($insert_ticket['status'] == 'awaiting_user') {
				$update['date_user_waiting'] = $last_user_time;
			}
		}

		if ($update) {
			$this->importer->db->update('tickets', $update, array('id' => $ticket_id));
		}

		return $ticket_id;
	}
}