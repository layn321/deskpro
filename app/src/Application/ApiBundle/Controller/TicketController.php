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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\Entity\Ticket AS Ticket;
use Application\DeskPRO\App;
use Application\DeskPRO\Tickets\SnippetFormatter;
use Application\DeskPRO\Tickets\TicketDisplay;

class TicketController extends AbstractController
{
	public function newTicketAction()
	{
		if (!$this->person->hasPerm('agent_tickets.create')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$errors = array();

		$person = false;
		$org = false;

		$subject = $this->in->getString('subject');
		if ($subject === '') {
			$errors['subject'] = array('required_field', 'subject missing or empty');
		}

		$message_text = $this->in->getString('message');
		if ($message_text === '') {
			$errors['message'] = array('required_field', 'message missing or empty');
		}

		if ($this->in->checkIsset('agent_id')) {
			$agentId = $this->in->getUint('agent_id');
			if ($agentId) {
				$agent = $this->em->getRepository('DeskPRO:Person')->findOneById($agentId);
				if (!$agent || !$agent->is_agent) {
					$errors['agent_id'] = array('invalid_argument', 'Not an agent');
				}
			}
		} else {
			$agentId = 0;
		}

		$ticket = new Ticket();

		if ($id = $this->in->getUint('department_id')) {
			$ticket->setDepartmentId($id);
		}
		if ($id = $this->in->getUint('category_id')) {
			$ticket->setCategoryId($id);
		}
		if ($id = $this->in->getUint('agent_team_id')) {
			$ticket->setAgentTeamId($id);
		}
		if ($id = $this->in->getUint('product_id')) {
			$ticket->setProductId($id);
		}
		if ($id = $this->in->getUint('priority_id')) {
			$ticket->setPriorityId($id);
		}
		if ($id = $this->in->getUint('workflow_id')) {
			$ticket->setWorkflowId($id);
		}
		if ($id = $this->in->getUint('urgency')) {
			$ticket->setUrgency($id);
		}

		if (!$ticket->department) {
			$ticket->department = $this->em->getRepository('DeskPRO:Department')->getDefaultDepartment('ticket');
		}

		$sla_ids = $this->in->getCleanValueArray('sla_ids', 'uint');
		if ($sla_ids) {
			$slas = $this->em->getRepository('DeskPRO:Sla')->getByIds($sla_ids);
			foreach ($slas AS $sla) {
				if ($sla->apply_type == 'manual') {
					$ticket->addSla($sla);
				}
			}
		}

		$ticket->creation_system = Ticket::CREATED_WEB_API;
		$ticket->subject = $subject;
		$ticket->status = $this->in->getString('status') ?: 'awaiting_agent';
		if ($agentId) {
			$ticket->agent_id = $agentId;
		}

		// make this check as late as possible to reduce race conditions
		if ($this->in->checkIsset('person_id')) {
			$person = $this->em->getRepository('DeskPRO:Person')->findOneById($this->in->getInt('person_id'));
			if (!$person) {
				$errors['person_id'] = array('invalid_person', 'Invalid person ID');
			}
		} else if ($this->in->checkIsset('person_email')) {
			$email = $this->in->getString('person_email');

			if (!\Orb\Validator\StringEmail::isValueValid($email) || App::getSystemService('gateway_address_matcher')->isManagedAddress($email)) {
				$errors['person_email'] = array('invalid_email', 'Invalid email address');
			} else {
				$person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($email);
				if (!$person) {
					$person = new \Application\DeskPRO\Entity\Person();
					$person->setEmail($email);
					$person->setName($this->in->getString('person_name'));

					if ($this->in->checkIsset('person_organization')) {
						$orgName = $this->in->getString('person_organization');

						$org = $this->em->getRepository('DeskPRO:Organization')->findOneByName($orgName);
						if (!$org) {
							$org = new \Application\DeskPRO\Entity\Organization();
							$org['name'] = $orgName;
						}

						$person->organization = $org;
						$person->organization_position = $this->in->getString('person_organization_position');
					}
				}
			}
		} else {
			$errors['person_id'] = array('required_field', 'person_id or person_email missing');
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		if ($id = $this->in->getUint('language_id')) {
			$ticket->setLanguageId($id);
		} else {
			$ticket->language = $person->getRealLanguage();
		}
		$ticket->person = $person;
		if (!$person->id) {
			$ticket->person_email = $person->getPrimaryEmail();
		}

		$message = new \Application\DeskPRO\Entity\TicketMessage();
		$message->person = ($this->in->getBool('message_as_agent') ? $this->person : $person);
		$message->creation_system = \Application\DeskPRO\Entity\TicketMessage::CREATED_WEB_API;

		$formatter = new SnippetFormatter(App::getContainer()->get('twig'));
		$message_text = $formatter->formatText($message_text, $ticket);

		if ($this->in->getBool('message_is_html')) {
			$message_text = App::get('deskpro.core.input_cleaner')->clean($message_text, 'html_core');
			$message_text = \Orb\Util\Strings::trimHtml($message_text);
			$message_text = \Orb\Util\Strings::prepareWysiwygHtml($message_text);
			$message->message = $message_text;
		} else {
			$message->setMessageText($message_text);
		}

		$this->_insertTicketMessageAttachments($ticket, $message);

		$ticket->addMessage($message);

		// need to ensure we treat things as the message owner
		App::setCurrentPerson($message->person);

		$this->db->beginTransaction();

		try {
			if ($org && !$org->id) {
				$this->em->persist($org);
				$this->em->flush();
			}
			if (!$person->id) {
				$this->em->persist($person);
				$this->em->flush();
			}

			$this->em->persist($ticket);

			$labels = $this->in->getCleanValueArray('label', 'string', 'discard');
			if ($labels) {
				$ticket->getLabelManager()->preSetLabelsArray($labels, $this->em);
			}

			$this->em->flush();
			$this->em->persist($message);
			$this->em->flush();

			App::setCurrentPerson($this->person);

			$field_manager = $this->container->getSystemService('ticket_fields_manager');
			$post_custom_fields = $this->getCustomFieldInput();
			if (!empty($post_custom_fields)) {
				$field_manager->saveFormToObject($post_custom_fields, $ticket);
			}

			if ($labels) {
				$ticket->getLabelManager()->setLabelsArray($labels, $this->em);
			}

			$this->em->flush();

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createApiCreateResponse(
			array('ticket_id' => $ticket->id),
			$this->generateUrl('api_tickets_ticket', array('ticket_id' => $ticket->id), true)
		);
	}

	public function getTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$data = $ticket->toApiData();
		$ticket_flagged = $this->em->getRepository('DeskPRO:TicketFlagged')->getFlagForTicket($ticket, $this->person);
		if ($ticket_flagged) {
			$data['flag'] = $ticket_flagged;
		}

		$data = array('ticket' => $data);

		if ($this->in->getBool('with_messages')) {

			$ticket_display = new TicketDisplay($ticket, $this->person);

			$messages = $this->em->getRepository('DeskPRO:TicketMessage')->getTicketMessages($ticket, array(
				'with_notes' => true,
				'limit'      => 10,
				'order'      => 'DESC'
			));

			$data['messages'] = array();
			foreach ($messages as $m) {
				$msg_data = $m->toApiData(true);
				$msg_data['message'] = $m->procInlineAttach($msg_data['message']);

				$attach = $ticket_display->getMessageAttachments($m, false);
				if ($attach) {
					$msg_data['attachments'] = array();
					foreach ($attach as $a) {
						$msg_data['attachments'][] = $a->toApiData(true);
					}
				}

				$data['messages'][] = $msg_data;
			}
		}

		// Full data so we can re-construct an edit-type form
		if ($this->in->getBool('with_display_options')) {
			$display_options = array();

			$all_deps = $this->container->getDataService('Department')->getRootNodes();
			$ticket_deps = array();
			foreach ($all_deps as $d) {
				if ($d->is_tickets_enabled) {
					$ticket_deps[] = $d;
				}
			}

			$display_options['departments'] = $this->getApiData(array_values($ticket_deps));
			$display_options['agents']      = $this->getApiData(array_values($this->container->getAgentData()->getAgents()));

			if (App::getSetting('core.use_agent_team')) {
				$display_options['agent_teams'] = $this->getApiData(array_values($this->container->getDataService('AgentTeam')->getTeams()));
			}
			if (App::getSetting('core.use_product')) {
				$display_options['products'] = $this->getApiData(array_values($this->container->getDataService('Product')->getRootNodes()));
			}
			if (App::getSetting('core.use_ticket_category')) {
				$display_options['categories'] = $this->getApiData(array_values($this->container->getDataService('TicketCategory')->getRootNodes()));
			}
			if (App::getSetting('core.use_ticket_priority')) {
				$display_options['priorities'] = $this->getApiData(array_values($this->container->getDataService('TicketPriority')->getAll()));
			}
			if (App::getSetting('core.use_ticket_workflow')) {
				$display_options['workflows'] = $this->getApiData(array_values($this->container->getDataService('TicketWorkflow')->getAll()));
			}
			if ($this->container->getLanguageData()->isMultiLang()) {
				$display_options['languages'] = $this->getApiData(array_values($this->container->getLanguageData()->getAll()));
			}

			$data['display_options'] = $display_options;
		}

		return $this->createApiResponse($data);
	}

	public function postTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'edit');

		$fields = array(
			'department_id' => 'Uint',
			'language_id' => 'Uint',
			'category_id' => 'Uint',
			'agent_id' => 'Uint',
			'agent_team_id' => 'Uint',
			'product_id' => 'Uint',
			'priority_id' => 'Uint',
			'workflow_id' => 'Uint',
			'status' => 'String',
			'is_hold' => 'Bool',
			'flag' => 'string',
			'urgency' => 'Uint',
		);

		$editor = App::getApi('tickets')->getTicketEditor($ticket);
		$editor->setPersonContext($this->person);

		$errors = array();

		foreach ($fields AS $field => $cleanType) {
			if ($this->in->checkIsset($field)) {
				$value = $this->in->{'get' . $cleanType}($field);
				try {
					$editor->applyActions(array($field => $value));
				} catch (\InvalidArgumentException $e) {
					$errors[$field] = array("invalid_argument.$field", $e->getMessage());
				}
			}
		}

		$subject = $this->in->getString('subject');
		if ($subject) {
			$ticket->subject = $subject;
		}

		$person_id = $this->in->getUint('person_id');
		if ($person_id)
		{
			$ticket->setPersonId($person_id);
		}

		if ($this->in->checkIsset('is_locked')) {
			if ($this->in->getBool('is_locked')) {
				$ticket->setLockedByAgent($this->person);
			} else {
				$ticket->setLockedByAgent(null);
			}
		}

		if ($errors) {
			return $this->createApiMultipleErrorResponse($errors);
		}

		$this->db->beginTransaction();

		try {
			$this->em->persist($ticket);

			if ($this->person->PermissionsManager->TicketChecker->canModify($ticket, 'fields')) {
				$post_custom_fields = $this->getCustomFieldInput();
				if (!empty($post_custom_fields)) {
					$field_manager = $this->container->getSystemService('ticket_fields_manager');
					$field_manager->saveFormToObject($post_custom_fields, $ticket, true);
					$this->em->persist($ticket);
				}
			}

			$this->em->flush();

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createSuccessResponse();
	}

	public function deleteTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'delete');

		$this->em->getConnection()->beginTransaction();

		try {
			$ticket->setStatus('hidden.deleted');
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$this->db->insert('tickets_deleted', array(
			'ticket_id' => $ticket->id,
			'by_person_id' => $this->person->id,
			'new_ticket_id' => 0,
			'reason' => $this->in->getString('reason'),
			'date_created' => date('Y-m-d H:i:s')
		));

		if ($this->in->getBool('ban')) {
			foreach ($ticket->person->emails as $email) {
				$email_addy = strtolower($email->email);
				App::getDb()->replace('ban_emails', array(
					'banned_email' => $email_addy,
					'is_pattern' => 0
				));
			}
		}

		return $this->createSuccessResponse();
	}

	public function undeleteTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'delete');

		$this->em->getConnection()->beginTransaction();

		try {
			$ticket->setStatus('awaiting_agent');
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->createSuccessResponse();
	}

	public function getTicketLogsAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$ticket_logs = $this->em->getRepository('DeskPRO:TicketLog')->getLogsForTicket($ticket);
		foreach ($ticket_logs AS $key => $log)
		{
			if ($log->action_type == 'executed_triggers') {
				unset($ticket_logs[$key]);
			} elseif ($log->action_type == 'executed_escalations') {
				unset($ticket_logs[$key]);
			}
		}

		$trackers = App::getDb()->fetchAllCol("
			SELECT log
			FROM ticket_changetracker_logs
			WHERE ticket_id = ?
			ORDER BY id ASC
		", array($ticket->getId()));

		return $this->createApiResponse(array(
			'logs' => $this->getApiData($ticket_logs),
			'tracker_logs' => $trackers
		));
	}

	public function getTicketMessagesAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		return $this->createApiResponse(array('messages' => $this->getApiData($ticket->messages)));
	}

	public function getTicketMessageAction($ticket_id, $message_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$message = $this->em->createQuery("
			SELECT m
			FROM DeskPRO:TicketMessage m
			WHERE m.ticket = ?0 AND m.id = ?1
		")->setParameters(array($ticket, $message_id))->setMaxResults(1)->getOneOrNullResult();

		if (!$message) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Message $message_id not found in $ticket_id");
		}

		return $this->createApiResponse(array('message' => $message->toApiData()));
	}

	public function getTicketMessageDetailsAction($ticket_id, $message_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$message = $this->em->createQuery("
			SELECT m
			FROM DeskPRO:TicketMessage m
			WHERE m.ticket = ?0 AND m.id = ?1
		")->setParameters(array($ticket, $message_id))->setMaxResults(1)->getOneOrNullResult();

		if (!$message) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Message $message_id not found in $ticket_id");
		}

		return $this->createApiResponse(array(
			'unformatted' => $message->message_text,
			'email_source' => $message->email_source ? $message->email_source->raw_source : null,
			'email_log' => $message->email_source ? implode("\n", $message->email_source->source_info) : null
		));
	}

	public function replyTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'reply');

		if ($this->in->getString('message') === '') {
			return $this->createApiErrorResponse('required_field', "message cannot be empty");
		}

		$message = new \Application\DeskPRO\Entity\TicketMessage();
		$message['ticket'] = $ticket;
		$message['person'] = ($this->in->getBool('message_as_agent') ? $this->person : $ticket->person);
		$message['ip_address'] = dp_get_user_ip_address();
		$message['creation_system'] = \Application\DeskPRO\Entity\TicketMessage::CREATED_WEB_API;

		if ($this->in->getBool('dp_is_mobile')) {
			$message['creation_system'] = \Application\DeskPRO\Entity\TicketMessage::CREATED_MOBILE_AGENT;
		}

		$notify_agent_ids = array();

		if ($this->in->getBool('message_is_html')) {
			$message_text = \Orb\Util\Strings::trimHtml($this->in->getHtmlCore('message'));
			$message_text = \Orb\Util\Strings::prepareWysiwygHtml($message_text);
			$message->message = $message_text;

			preg_match_all('/<span[^>]+data-notify-agent-id="(\d+)"/i', $this->in->getString('message'), $matches, PREG_SET_ORDER);
			foreach ($matches AS $match) {
				$notify_agent_ids[] = $match[1];
			}
		} else {
			$message->setMessageText($this->in->getString('message'));
		}

		if ($this->in->getBool('is_note')) {
			$message['is_agent_note'] = true;
		}

		if ($dupe_message = $this->em->getRepository('DeskPRO:TicketMessage')->checkDupeMessage($message, $ticket)) {
			return $this->createApiResponse(array(
				'dupe_message' => true,
				'message_id' => $dupe_message['id']
			));
		}

		$this->_insertTicketMessageAttachments($ticket, $message);

		$ticket->addMessage($message);

		if ($this->in->getBool('suppress_user_notify')) {
			$ticket->getTicketLogger()->recordExtra('suppress_user_notify', true);
		}

		if ($this->in->getString('status')) {
			$ticket->status = $this->in->getString('status');
		}

		// need to ensure we treat things as the message owner
		App::setCurrentPerson($message->person);

		$this->db->beginTransaction();

		try {
			$this->em->persist($ticket);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		App::setCurrentPerson($this->person);

		if ($this->in->getBool('message_as_agent') && $this->in->getBool('is_note') && $notify_agent_ids) {
			$agent_chat = new \Application\DeskPRO\Chat\AgentChat($this->person, $this->session->getEntity());
			$agent_chat->disableOfflineEmailAlert(); // we'll handle offline notifs as part of normal notifications

			$notify_chat   = array();
			$notify_email  = array();

			$notify_agent_ids = array_unique($notify_agent_ids);
			foreach ($notify_agent_ids as $agent_id) {
				if (!($agent = $this->container->getAgentData()->get($agent_id))) {
					continue;
				}

				$notify_chat[$agent->id] = $agent;

				$pref = $agent->getPref('agent_notif.ticket_mention', 'always_send');
				if ($pref == 'always_send' || ($pref == 'smart_send' && !$this->container->getAgentData()->isAgentOnline($agent))) {
					$notify_email[$agent->id] = $agent;
				}
			}

			if ($notify_chat) {
				$notify_text = $this->person->getDisplayName() . " alerted you in a note in {{t-$ticket->id}}: $ticket->subject";
				$agent_chat->sendAgentMessage($notify_text, array_keys($notify_chat));
			}

			if ($notify_email) {
				$ticket->getTicketLogger()->recordExtra('mention_agents', $notify_email);
			}
		}

		return $this->createApiCreateResponse(
			array('message_id' => $message->id),
			$this->generateUrl('api_tickets_ticket_message', array('ticket_id' => $ticket->id, 'message_id' => $message->id), true)
		);
	}

	protected function _insertTicketMessageAttachments(Ticket $ticket, \Application\DeskPRO\Entity\TicketMessage $message)
	{
		$attachments = $this->request->files->get('attach');
		if (!is_array($attachments)) {
			$attachments = array($attachments);
		}
		$accept = $this->container->getAttachmentAccepter();

		foreach ($attachments AS $file) {
			$error = $accept->getError($file, 'agent');
			if (!$error) {
				$blob = $accept->accept($file);
				$this->_addTicketMessageAttachment($blob, $ticket, $message);
			}
		}

		foreach ($this->in->getCleanValueArray('attach_id') as $blob_id) {
			$this->_addTicketMessageAttachment($blob_id, $ticket, $message);
		}
	}

	protected function _addTicketMessageAttachment($blob_id, Ticket $ticket, \Application\DeskPRO\Entity\TicketMessage $message)
	{
		if ($blob_id instanceof \Application\DeskPRO\Entity\Blob) {
			$blob = $blob_id;
		} else {
			$blob = $this->em->getRepository('DeskPRO:Blob')->find($blob_id);
		}

		if ($blob) {
			$attach = new \Application\DeskPRO\Entity\TicketAttachment();
			$attach['blob'] = $blob;
			$attach['person'] = $this->person;

			$message->addAttachment($attach);
			$ticket->addAttachment($attach);
		}
	}

	public function claimTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_assign_self');

		$ticket->agent_id = $this->person->id;
		$this->em->persist($ticket);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function splitTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_merge');
		$message_ids = $this->in->getCleanValueArray('message_ids', 'uint', 'discard');
		$subject = $this->in->getString('subject');

		$split = new \Application\DeskPRO\Tickets\TicketSplit($ticket);

		try {
			$this->em->beginTransaction();
			$new_ticket = $split->split($subject, $message_ids);
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		if (!$split->wasOldTicketDeleted()) {
			$ticket->recountStats();
			$this->em->persist($ticket);
		}

		if ($new_ticket) {
			$new_ticket->recountStats();
			$this->em->persist($new_ticket);
		}

		$this->em->flush();

		return $this->createApiResponse(array(
			'success' => true,
			'ticket_id' => $new_ticket ? $new_ticket['id'] : null,
			'old_ticket_deleted' => $split->wasOldTicketDeleted()
		));
	}

	public function mergeTicketAction($ticket_id, $merge_ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_merge');
		$other_ticket = $this->_getTicketOr404($merge_ticket_id, 'modify_merge');

		try {
			$this->em->beginTransaction();
			$merge = new \Application\DeskPRO\Tickets\TicketMerge\TicketMerge($this->person, $ticket, $other_ticket);
			$merge->merge();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return $this->createSuccessResponse();
	}

	public function spamTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'delete');

		$this->em->getConnection()->beginTransaction();

		try {
			$ticket->setStatus('hidden.spam');
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		if ($this->in->getBool('ban')) {
			foreach ($ticket->person->emails as $email) {
				$email_addy = strtolower($email->email);
				App::getDb()->replace('ban_emails', array(
					'banned_email' => $email_addy,
					'is_pattern' => 0
				));
			}
		}

		return $this->createSuccessResponse();
	}

	public function unspamTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'delete');

		$this->em->getConnection()->beginTransaction();

		try {
			$ticket->setStatus('awaiting_agent');
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->createSuccessResponse();
	}

	public function lockTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		if ($ticket->hasLock()) {
			return $this->createApiErrorResponse('action_impossible', 'Ticket already locked');
		}

		$ticket->setLockedByAgent($this->person);
		$this->em->persist($ticket);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function unlockTicketAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		if ($ticket->hasLock()) {
			$ticket->setLockedByAgent(null);
			$this->em->persist($ticket);
			$this->em->flush();
		}

		return $this->createSuccessResponse();
	}

	public function getTicketTasksAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$tasks = $this->em->getRepository('DeskPRO:Task')->findLinkedTicketTasks($ticket, $this->person);

		return $this->createApiResponse(array('tasks' => $this->getApiData($tasks)));
	}

	public function postTicketTasksAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$title = $this->in->getString('title');
		if (!$title) {
			return $this->createApiErrorResponse('required_field.title', 'title is empty or missing');
		}

		$task = new \Application\DeskPRO\Entity\Task();
		$task->title = $title;
		$task->person = $this->person;
		$task->assigned_agent = $this->person;

		$assoc = new \Application\DeskPRO\Entity\TaskAssociatedTicket();
		$assoc->ticket = $ticket;
		$assoc->task   = $task;
		$task->task_associations->add($assoc);

		$this->em->persist($task);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $task->id),
			$this->generateUrl('api_tasks_task', array('task_id' => $task->id), true)
		);
	}

	public function getTicketBillingChargesAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$charges = $ticket->charges;

		$time = 0;
		$charge_amount = 0;

		foreach ($charges AS $charge) {
			$time += $charge->charge_time;
			$charge_amount += $charge->amount;
		}

		return $this->createApiResponse(array(
			'total_charge_time' => $time,
			'total_charge_amount' => $charge_amount,
			'total' => count($charges),
			'charges' => $this->getApiData($charges)
		));
	}

	public function postTicketBillingChargesAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$time = $this->in->getUint('time');
		$amount = $this->in->getUFloat('amount');

		if (!$time && !$amount) {
			return $this->createApiErrorResponse('required_field', 'time or amount is required');
		}

		if ($time) {
			$amount = null;
		} else {
			$time = null;
		}

		$comment = $this->in->getString('comment');

		$charge = $ticket->addCharge($this->person, $time, $amount, $comment);
		$this->em->persist($ticket);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $charge->id),
			$this->generateUrl('api_tickets_ticket_billing_charge', array('ticket_id' => $ticket->id, 'charge_id' => $charge->id), true)
		);
	}

	public function getTicketBillingChargeAction($ticket_id, $charge_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$charge = false;

		foreach ($ticket->charges AS $ticket_charge) {
			if ($ticket_charge->id == $charge_id) {
				$charge = $ticket_charge;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => (bool)$charge));
	}

	public function deleteTicketBillingChargeAction($ticket_id, $charge_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		foreach ($ticket->charges AS $key => $ticket_charge) {
			if ($ticket_charge->id == $charge_id) {
				$ticket->charges->remove($key);
				$this->em->persist($ticket);
				$this->em->flush();
				break;
			}
		}

		return $this->createSuccessResponse();
	}

	public function getTicketSlasAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		return $this->createApiResponse(array(
			'ticket_slas' => $this->getApiData($ticket->ticket_slas)
		));
	}

	public function postTicketSlasAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_slas');

		$sla_id = $this->in->getUint('sla_id');
		$sla = $this->em->getRepository('DeskPRO:Sla')->find($sla_id);
		if (!$sla) {
			return $this->createApiErrorResponse('invalid_argument.sla_id', 'SLA not found');
		}
		if ($sla->apply_type != 'manual') {
			return $this->createApiErrorResponse('invalid_argument.sla_id', 'no permission to add that SLA');
		}

		$ticket_sla = $ticket->addSla($sla);
		$this->em->persist($ticket);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('id' => $ticket_sla->id),
			$this->generateUrl('api_tickets_ticket_sla', array('ticket_id' => $ticket->id, 'ticket_sla_id' => $ticket_sla->id), true)
		);
	}

	public function getTicketSlaAction($ticket_id, $ticket_sla_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		$exists = false;

		foreach ($ticket->ticket_slas AS $ticket_sla) {
			if ($ticket_sla->id == $ticket_sla_id) {
				$exists = true;
				break;
			}
		}

		return $this->createApiResponse(array('exists' => $exists));
	}

	public function deleteTicketSlaAction($ticket_id, $ticket_sla_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_slas');

		foreach ($ticket->ticket_slas AS $key => $ticket_sla) {
			if ($ticket_sla->id == $ticket_sla_id) {
				if ($ticket_sla->sla->apply_type != 'manual') {
					return $this->createApiErrorResponse('invalid_argument', 'do not have permission to remove ticket SLA ' . $ticket_sla_id);
				}

				$ticket->ticket_slas->remove($key);
				$this->em->persist($ticket);
				$this->em->flush();
				break;
			}
		}

		return $this->createSuccessResponse();
	}

	public function getParticipantsAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		return $this->createApiResponse(array('participants' => $this->getApiData($ticket->participants)));
	}

	public function postParticipantsAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_cc');

		$person = null;
		if ($this->in->getUint('person_id')) {
			$person = $this->em->find('DeskPRO:Person', $this->in->getUint('person_id'));
		} elseif ($email_address = $this->in->getString('email')) {
			if (!\Orb\Validator\StringEmail::isValueValid($email_address) || App::getSystemService('gateway_address_matcher')->isManagedAddress($email_address)) {
				return $this->createApiErrorResponse('invalid_email', 'Invalid email address');
			}

			$person = $this->em->getRepository('DeskPRO:Person')->findOneByEmail($email_address);

			if (!$person) {
				$person = new \Application\DeskPRO\Entity\Person();
				$person->setEmail($email_address);
			}
		} else {
			return $this->createApiErrorResponse('required_field', 'person_id or email must be provided');
		}

		if (!$person) {
			return $this->createApiErrorResponse('not_found', 'Person not found');
		}

		if ($person->id && $ticket->hasParticipantPerson($person)) {
			return $this->createApiCreateResponse(
				array('person_id' => $person->id),
				$this->generateUrl('api_tickets_ticket_participant', array('ticket_id' => $ticket->id, 'person_id' => $person->id), true)
			);
		}

		$this->db->beginTransaction();

		try {

			if (!$person->id) {
				$this->em->persist($person);
				$this->em->flush();
			}

			$part = $ticket->addParticipantPerson($person);
			if ($part) {
				$this->em->persist($part);
			}
			$this->em->persist($ticket);
			$this->em->flush();

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createApiCreateResponse(
			array('person_id' => $part->person->id),
			$this->generateUrl('api_tickets_ticket_participant', array('ticket_id' => $ticket->id, 'person_id' => $part->person->id), true)
		);
	}

	public function getParticipantAction($ticket_id, $person_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);
		$person = $this->em->find('DeskPRO:Person', $person_id);

		if (!$person) {
			return $this->createApiResponse(array('exists' => false));
		}

		$part = $this->em->createQuery("
			SELECT part
			FROM DeskPRO:TicketParticipant part
			WHERE part.ticket = ?0 AND part.person = ?1
		")->setParameters(array($ticket, $person))->setMaxResults(1)->getOneOrNullResult();

		if (!$part) {
			return $this->createApiResponse(array('exists' => false));
		}

		return $this->createApiResponse(array('exists' => true));
	}

	public function deleteParticipantAction($ticket_id, $person_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_cc');
		$person = $this->em->find('DeskPRO:Person', $person_id);

		if (!$person) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$part = $this->em->createQuery("
			SELECT part
			FROM DeskPRO:TicketParticipant part
			WHERE part.ticket = ?0 AND part.person = ?1
		")->setParameters(array($ticket, $person))->setMaxResults(1)->getOneOrNullResult();

		if (!$part) {
			return $this->createSuccessResponse();
		}

		$this->db->beginTransaction();

		try {
			$this->em->remove($part);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createSuccessResponse();
	}

	public function getLabelsAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		return $this->createApiResponse(array('labels' => $this->getApiData($ticket->labels)));
	}

	public function postLabelsAction($ticket_id)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_labels');
		$label = $this->in->getString('label');

		if ($label === '') {
			return $this->createApiErrorResponse('required_field', "Field 'label' missing or empty");
		}

		$ticket->getLabelManager()->addLabel($label);
		$this->em->persist($ticket);
		$this->em->flush();

		return $this->createApiCreateResponse(
			array('label' => $label),
			$this->generateUrl('api_tickets_ticket_label', array('ticket_id' => $ticket->id, 'label' => $label), true)
		);
	}

	public function getLabelAction($ticket_id, $label)
	{
		$ticket = $this->_getTicketOr404($ticket_id);

		if ($ticket->getLabelManager()->hasLabel($label)) {
			return $this->createApiResponse(array('exists' => true));
		} else {
			return $this->createApiResponse(array('exists' => false));
		}
	}

	public function deleteLabelAction($ticket_id, $label)
	{
		$ticket = $this->_getTicketOr404($ticket_id, 'modify_labels');

		$ticket->getLabelManager()->removeLabel($label);
		$this->em->persist($ticket);
		$this->em->flush();

		return $this->createSuccessResponse();
	}

	public function getFieldsAction()
	{
		$field_manager = $this->container->getSystemService('ticket_fields_manager');
		$fields = $field_manager->getFields();

		return $this->createApiResponse(array('fields' => $this->getApiData($fields)));
	}

	public function getDepartmentsAction()
	{
		$department_list = $this->em->getRepository('DeskPRO:Department')->findAll();
		$departments = $this->em->getRepository('DeskPRO:Department')->getFlatHierarchy();
		foreach ($department_list AS $department) {
			if (!$department->is_tickets_enabled) {
				unset($departments[$department->id]);
			}
		}

		return $this->createApiResponse(array('departments' => $departments));
	}

	public function getProductsAction()
	{
		$products = $this->em->getRepository('DeskPRO:Product')->getFlatHierarchy();

		return $this->createApiResponse(array('products' => $products));
	}

	public function getCategoriesAction()
	{
		$categories = $this->em->getRepository('DeskPRO:TicketCategory')->getFlatHierarchy();

		return $this->createApiResponse(array('categories' => $categories));
	}

	public function getPrioritiesAction()
	{
		$priorities = $this->em->createQuery("
			SELECT p
			FROM DeskPRO:TicketPriority p
			ORDER BY p.priority
		")->execute();

		return $this->createApiResponse(array('priorities' => $this->getApiData($priorities)));
	}

	public function getWorkflowsAction()
	{
		$workflows = $this->em->createQuery("
			SELECT w
			FROM DeskPRO:TicketWorkflow w
			ORDER BY w.display_order
		")->execute();

		return $this->createApiResponse(array('workflows' => $this->getApiData($workflows)));
	}

	public function getSlasAction()
	{
		$slas = $this->em->getRepository('DeskPRO:Sla')->getAllSlas();

		return $this->createApiResponse(array('slas' => $this->getApiData($slas)));
	}

	public function getSlaAction($sla_id)
	{
		$sla = $this->em->getRepository('DeskPRO:Sla')->find($sla_id);
		if (!$sla) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no SLA with ID $sla_id");
		}

		return $this->createApiResponse(array('sla' => $sla->toApiData()));
	}

	public function getSlaPeopleAction($sla_id)
	{
		$sla = $this->em->getRepository('DeskPRO:Sla')->find($sla_id);
		if (!$sla) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no SLA with ID $sla_id");
		}

		return $this->createApiResponse(array('people' => $this->getApiData($sla->people)));
	}

	public function getSlaOrganizationsAction($sla_id)
	{
		$sla = $this->em->getRepository('DeskPRO:Sla')->find($sla_id);
		if (!$sla) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no SLA with ID $sla_id");
		}

		return $this->createApiResponse(array('organizations' => $this->getApiData($sla->organizations)));
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\Ticket
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getTicketOr404($id, $check_perm = null)
	{
		$q = $this->em->createQuery("SELECT t FROM DeskPRO:Ticket t WHERE t.id = ?0");
		$q->setFetchMode('DeskPRO:Person', 'person', 'EAGER');
		$q->setFetchMode('DeskPRO:Person', 'agent', 'EAGER');
		$q->setParameters(array($id));

		$ticket = $q->getOneOrNullResult();

		if (!$ticket || !$this->person->PermissionsManager->TicketChecker->canView($ticket)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no ticket with ID $id");
		}

		if ($check_perm && !$this->checkTicketPerm($ticket, $check_perm)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no ticket with ID $id");
		}

		return $ticket;
	}

	public function checkTicketPerm(Ticket $ticket, $check_perm)
	{
		if (strpos($check_perm, 'modify_') === 0) {
			$check_perm = str_replace('modify_', '', $check_perm);
			if (!$this->person->PermissionsManager->TicketChecker->canModify($ticket, $check_perm)) {
				return false;
			}
		} elseif ($check_perm == 'delete') {
			if (!$this->person->PermissionsManager->TicketChecker->canDelete($ticket)) {
				return false;
			}
		} elseif ($check_perm == 'reply') {
			if (!$this->person->PermissionsManager->TicketChecker->canReply($ticket)) {
				return false;
			}
		}

		return true;
	}
}
