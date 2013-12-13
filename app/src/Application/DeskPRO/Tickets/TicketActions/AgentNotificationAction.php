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
 * @subpackage Tickets
 */

namespace Application\DeskPRO\Tickets\TicketActions;

use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\App;

use Application\DeskPRO\Tickets\Util as TicketUtil;
use Orb\Util\Arrays;

/**
 * An agent notification sends emails to agents when a ticket in one
 * of their subscribed filters is affected.
 *
 * This is a built-in action, it cannot be added via the trigger interface.
 * (But it can be modified)
 */
class AgentNotificationAction extends AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var array
	 */
	protected $notify_agents = array();

	/**
	 * @var array
	 */
	protected $mention_agents = array();

	/**
	 * @var array
	 */
	protected $notify_info = array();

	/**
	 * @var string
	 */
	protected $newticket_email_tpl = 'DeskPRO:emails_agent:new-ticket.html.twig';

	/**
	 * @var string
	 */
	protected $newreply_user_email_tpl = 'DeskPRO:emails_agent:new-reply-user.html.twig';

	/**
	 * @var string
	 */
	protected $newreply_agent_email_tpl = 'DeskPRO:emails_agent:new-reply-agent.html.twig';

	/**
	 * @var string
	 */
	protected $ticket_update_email_tpl = 'DeskPRO:emails_agent:ticket-update.html.twig';

	/**
	 * @var string
	 */
	protected $from_address;

	public function __construct(TicketChangeTracker $tracker)
	{
		$this->tracker = $tracker;

		$notify_list = $this->tracker->getNotifyListBuilder()->getNotifyList('email');
		foreach ($notify_list as $agent_id => $matches) {
			$filters = array();
			foreach ($matches as $filter_info) {
				if (in_array('email', $filter_info['types'])) {
					$filters[] = $filter_info['filter'];
				}
			}

			if ($filters) {
				$this->notify_agents[] = $agent_id;
				$this->notify_info[$agent_id] = array('filters' => $filters);
			}
		}

		if ($tracker->isExtraSet('mention_agents')) {
			$this->mention_agents = $tracker->getExtra('mention_agents');
			$this->mention_agents = Arrays::keyFromData($this->mention_agents, 'id');

			foreach ($this->mention_agents as $agent) {
				$this->notify_agents[] = $agent->id;
			}
		}
	}

	public function getFromAddress(Ticket $ticket)
	{
		$info = $ticket->getFromAddress();
		return $info['email'];
	}

	public function getFromName(Ticket $ticket)
	{
		$info = $ticket->getFromAddress('agent', array(
			'default_from' => $this->tracker->isExtraSet('set_initial_from_toagent') ? $this->tracker->getExtra('set_initial_from_toagent') : null
		));
		return $info['name'];
	}

	/**
	 * @param string $tpl
	 */
	public function setEmailTemplate($tpl, $type = '')
	{
		switch ($tpl) {
			case 'agent_new_ticket':
				$this->newticket_email_tpl = $tpl;
				break;
			case 'agent_new_reply_agent':
				$this->newreply_agent_email_tpl = $tpl;
				break;
			case 'agent_new_reply_user':
				$this->newreply_user_email_tpl = $tpl;
				break;
		}
	}

	/**
	 * Add additional agents to notify.
	 * This is used by any modifiers
	 *
	 * @return
	 */
	public function addAdditionalAgents($codes)
	{
		if (!is_array($codes)) {
			$codes = array($codes);
		}

		$this->tracker->logMessage("[AgentAlertNotificationAction] addAdditionalAgents " . implode(', ', $codes));
		$agent_ids = TicketUtil::resolveAgentCodes($codes, $this->tracker->getTicket());

		// Dont notify about self action
		$person_context = App::getCurrentPerson();
		if ($person_context && $person_context->getId() && !$person_context->getPref("agent_notify_override.all.email")) {
			$agent_ids = Arrays::removeValue($agent_ids, $person_context->getId());
		}

		if ($agent_ids) {
			$this->notify_agents = array_merge($this->notify_agents, $agent_ids);
			$this->notify_agents = array_unique($this->notify_agents);
		}
	}

	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		if ($this->tracker->isExtraSet('suppress_agent_notify')) {
			$this->tracker->logMessage("[AgentNotificationAction] Notification suppressed");
			return;
		}

		if ($this->tracker->isExtraSet('force_email_validation')) {
			$this->tracker->logMessage("[AgentNotificationAction] Ticket validating, no notify");
			return;
		}

		if ($this->tracker->isExtraSet('force_notify_email')) {
			$this->notify_agents = array_merge($this->notify_agents, (array)$this->tracker->getExtra('action_performer'));
			$this->notify_agents = array_unique($this->notify_agents);
		}

		if (!$this->notify_agents) {
			$this->tracker->logMessage("[AgentNotificationAction] No agents");
			return;
		}

		if ($this->tracker->isExtraSet('email_template_agent_newticket')) {
			$this->newticket_email_tpl = $this->tracker->getExtra('email_template_agent_newticket');
			$this->tracker->logMessage("[AgentNotification] Set newticket template: " . $this->newticket_email_tpl);
		}
		if ($this->tracker->isExtraSet('email_template_agent_newreply_agent')) {
			$this->newreply_agent_email_tpl = $this->tracker->getExtra('email_template_agent_newreply_agent');
			$this->tracker->logMessage("[AgentNotification] Set newticket template: " . $this->newreply_agent_email_tpl);
		}
		if ($this->tracker->isExtraSet('email_template_agent_newreply_user')) {
			$this->newreply_user_email_tpl = $this->tracker->getExtra('email_template_agent_newreply_user');
			$this->tracker->logMessage("[AgentNotification] Set newticket template: " . $this->newreply_user_email_tpl);
		}

		$this->tracker->logMessage("[AgentNotificationAction] Agents: " . implode(', ', $this->notify_agents));

		$from_name    = $this->getFromName($ticket);
		$from_address = $this->getFromAddress($ticket);

		$change_info = array(
			'type'         => 'agent_notify',
			'notify_type'  => 'updated',
			'emailed'      => array(),
			'emailed_info' => array(),
			'from_name'    => $from_name,
			'from_address' => $from_address
		);

		$is_new_ticket      = false;
		$is_new_agent_reply = false;
		$is_new_user_reply  = false;

		$new_message = null;
		if ($this->tracker->isNewTicket()) {
			$this->tracker->logMessage("[AgentNotificationAction] isNewTicket");
			$change_info['notify_type'] = 'newticket';
			$tpl = $this->newticket_email_tpl;
			$is_new_ticket = true;
			$new_message = $this->tracker->getNewReply();

			if (!$new_message) {
				$new_message = App::getOrm()->getRepository('DeskPRO:TicketMessage')->getFirstTicketMessage($ticket);
			}
		} elseif ($this->tracker->hasNewAgentReply()) {
			$this->tracker->logMessage("[AgentNotificationAction] hasNewAgentReply");
			$change_info['notify_type'] = 'newreply';
			$tpl = $this->newreply_agent_email_tpl;
			$is_new_agent_reply = true;
			$new_message = $this->tracker->getNewAgentReply();
		} elseif ($this->tracker->hasNewUserReply()) {
			$this->tracker->logMessage("[AgentNotificationAction] hasNewUserReply");
			$change_info['notify_type'] = 'newreply';
			$tpl = $this->newreply_user_email_tpl;
			$is_new_user_reply = true;
			$new_message = $this->tracker->getNewUserReply();
		} else {
			$this->tracker->logMessage("[AgentNotificationAction] Generic update");
			$tpl = $this->ticket_update_email_tpl;
		}

		$agent_change = $this->tracker->getChangedProperty('agent');
		$team_change  = $this->tracker->getChangedProperty('agent_team');
		$part_change  = $this->tracker->getChangedProperty('participants');

		$tr = App::getTranslator();

		// This will generate an array of ticket logs that will be saved after notifcations are sent
		// But we call now so getting the diff for the email is easier, same logic as logs
		$ticket_logs = $this->tracker->getLogInspector()->getTicketLogsForAlert();

		$field_manager = App::getSystemService('ticket_fields_manager');
		$custom_fields = $field_manager->getDisplayArrayForObject($ticket);

		$ticket_display = new \Application\DeskPRO\PageDisplay\Page\TicketPageZoneCollection('view');
		$ticket_display->addPagesFromDb('agent');
		$page = $ticket_display->getDepartmentPage($ticket->getDepartmentId());
		$page_display = $page->getPageDisplay('default')->data;

		foreach ($this->notify_agents as $agent_id) {

			// Dont send an update notification to the agent for agent replies made by themselves
			if ($change_info['notify_type'] == 'newreply') {
				if ($new_message && !$this->tracker->isExtraSet('is_user_reply') && $new_message->person->getId() == $agent_id && !$new_message->person->getPref("agent_notify_override.all.email")) {
					$this->tracker->logMessage("[AgentNotificationAction] Skipping notify agent $agent_id of agent message by himself");
					continue;
				}
			}

			/** @var $agent \Application\DeskPRO\Entity\Person */
			$agent = App::getEntityRepository('DeskPRO:Person')->find($agent_id);

			if (!$agent || !$agent->getPrimaryEmailAddress()) {
				$this->tracker->logMessage("[AgentNotificationAction] Bad agent: " . $agent_id);
				continue;
			}

			if (!$agent->is_agent) {
				// This could happen if the agent was turned back into a regular user
				$this->tracker->logMessage("[AgentNotificationAction] Not an agent: " . $agent_id);
				continue;
			}

			$agent->loadHelper('Agent');
			$agent->loadHelper('AgentTeam');
			$agent->loadHelper('AgentPermissions');
			$agent->loadHelper('PermissionsManager');

			if (!$agent->PermissionsManager->TicketChecker->canView($ticket)) {
				$this->tracker->logMessage("[AgentNotificationAction] Skipping notify agent $agent_id (noperm)");
				continue;
			}

			$type_flag = null;
			if ($change_info['notify_type'] == 'updated') {
				if ($agent_change && $agent_change['new'] && $agent_change['new']->getId() == $agent_id) {
					$type_flag = 'assigned';
				} elseif ($team_change && $team_change['new'] && $agent->getHelper('Agent')->isTeamMember($team_change['new']->getId())) {
					$type_flag = 'assigned_team';
				} elseif ($part_change) {
					$added_part = false;
					foreach ($part_change as $change) {
						if ($change['new'] && $change['new']->getId() == $agent_id) {
							$added_part = true;
							break;
						}
					}
					if ($added_part) {
						$type_flag = 'added_part';
					}
				}
			}

			if (!$type_flag) {
				$type_flag = $change_info['notify_type'];
			}

			if ($type_flag == 'updated' && ($status_change = $this->tracker->getChangedProperty('status'))) {
				$type_flag = 'status_changed';
			}

			$this->tracker->logMessage("[AgentNotificationAction] Type flag: " . $type_flag);

			$performer = App::getCurrentPerson();
			$sla = null;
			$sla_status = null;

			if ($this->tracker->getExtra('by_agent')) {
				$performer = $this->tracker->getExtra('by_agent');
			} else if ($this->tracker->getExtra('person_performer')) {
				$performer = $this->tracker->getExtra('person_performer');
			}

			if ($this->tracker->getExtra('sla')) {
				$performer = null;
				$sla = $this->tracker->getExtra('sla');
				$sla_status = $this->tracker->getExtra('sla_status');
			}

			$vars = array(
				'type_flag'          => $type_flag,
				'is_new_ticket'      => $is_new_ticket,
				'is_new_agent_reply' => $is_new_agent_reply,
				'is_new_user_reply'  => $is_new_user_reply,
				'action_performer'   => $performer,
				'sla'                => $sla,
				'sla_status'         => $sla_status,
				'ticket_logs'        => $ticket_logs,
				'new_message'        => $new_message,
				'agent'              => $agent,
				'custom_fields'      => $custom_fields,
				'page_display'       => $page_display,
				'mention_agents'     => $this->mention_agents,
				'is_my_mention'      => isset($this->mention_agents[$agent->getId()]),
				'tracker'            => $this->tracker,
			);

			if (isset($this->notify_info[$agent->id]) && $this->notify_info[$agent->id]) {
				$vars['notify_info'] = $this->notify_info[$agent->id];
			}

			$tac = TicketUtil::getTacForPerson($ticket, $agent);
			$vars['ticket'] = $ticket;
			$vars['person'] = $agent;
			$vars['tac'] = $tac;

			$ticketdisplay = new \Application\DeskPRO\Tickets\TicketDisplay($ticket, $agent);
			$vars['ticketdisplay'] = $ticketdisplay;
			$vars['messages']      = array_reverse($ticketdisplay->getMessages(), true);

			$message = App::getMailer()->createMessage();
			$message->setContextId('ticket_gateway');
			$message->setTemplate($tpl, $vars);
			$message->setTo($agent->getPrimaryEmailAddress(), $agent->getDisplayName());
			$message->getHeaders()->get('Message-ID')->setId($tac->getUniqueEmailMessageId());
			$message->getHeaders()->addIdHeader('References', $ticket->getEmailReferencesHeader());

			if ($is_new_ticket) {
				$this->_addAttachments($message, $new_message->attachments, $new_message->getUsedSignatureImageBlobs());
			} elseif ($is_new_agent_reply || $is_new_user_reply) {
				$new_message = \Orb\Util\Arrays::getFirstItem($vars['messages']);
				$attachments = $ticketdisplay->getMessageAttachments($new_message, true);

				if ($new_message) {
					$this->_addAttachments($message, $attachments, $new_message->getUsedSignatureImageBlobs());
				}
			}

			$this->tracker->logMessage("[AgentNotificationAction] From name: " . $from_name);
			$this->tracker->logMessage("[AgentNotificationAction] From address: " . $from_address);

			$from_address_set = array($from_address => $from_address ? $from_name : $from_address);
			$message->setFrom($from_address_set);

			$email_time = microtime(true);
			App::getMailer()->send($message);

			$this->tracker->logMessage("[AgentNotificationAction] Email to " . $agent_id . ' ' . $agent->getPrimaryEmailAddress() . " with template $tpl (took " . sprintf("%.4f", microtime(true)-$email_time) . " s)");

			$change_info['emailed'][] = $agent;

			if (!empty($this->notify_info[$agent->id])) {
				$change_info['emailed_info'][$agent->id] = $this->notify_info[$agent->id];
			} else {
				$change_info['emailed_info'][$agent->id] = array('is_via_trigger' => true);
			}
		}

		$this->tracker->recordMultiPropertyChanged('log_actions', null, $change_info);
	}

	protected function _addAttachments($message, $attachments, array $signature_blobs = array())
	{
		$max = App::getSetting('core.sendemail_attach_maxsize');
		$max_embed = App::getSetting('core.sendemail_embed_maxsize');
		$embedded = array();
		$size = 0;

		if ($attachments) {
			foreach ($attachments as $attach) {
				if ($attach->is_inline) {
					$embedded[] = $attach;
					continue;
				}

				$size += $attach->blob->filesize;
				if ($size > $max) {
					break;
				}

				$message->attachBlob($attach->blob, $attach->blob->getDownloadUrl(true));
			}
		}

		// add embeds last so we don't miss out something not embedded
		foreach ($embedded as $attach) {
			if ($attach->blob->filesize > $max_embed) {
				continue;
			}

			$size += $attach->blob->filesize;
			if ($size > $max) {
				break;
			}

			$message->attachBlob($attach->blob, $attach->blob->getDownloadUrl(true), true);
		}

		foreach ($signature_blobs as $blob) {
			if ($blob->filesize > $max_embed) {
				continue;
			}

			$size += $blob->filesize;
			if ($size > $max) {
				break;
			}

			$message->attachBlob($blob, $blob->getDownloadUrl(true), true);
		}
	}

	/**
	 * This is a built-in trigger, merging should never happen.
	 *
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		return $other_action;
	}

	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		return '';
	}
}
