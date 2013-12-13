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

class SendAgentEmailAction extends AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var string
	 */
	protected $template;

	/**
	 * @var array
	 */
	protected $notify_agents = array();

	public function __construct($template, $agents, TicketChangeTracker $tracker = null)
	{
		$this->tracker = $tracker;
		$this->template = $template;
		$this->notify_agents = (array)$agents;
	}

	public function getFromAddress(Ticket $ticket)
	{
		$info = $ticket->getFromAddress('agent');
		return $info['email'];
	}

	public function getFromName(Ticket $ticket)
	{
		$info = $ticket->getFromAddress('agent');
		return $info['name'];
	}

	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		if (!App::getTemplating()->exists($this->template)) {
			return;
		}

		if (!$this->tracker) {
			return;
		}

		$agent_ids = array();
		foreach ($this->notify_agents as $a) {
			if ($a == 'assigned_agent') {
				if ($ticket->agent) {
					$agent_ids[] = $ticket->agent->getId();
				}
			} elseif ($a == 'assigned_agent_team') {
				if ($ticket->agent_team) {
					$agent_ids = array_merge($agent_ids, App::getOrm()->getRepository('DeskPRO:AgentTeam')->getMemberIds($ticket->agent_team->getId()));
				}
			} else {
				if (App::getContainer()->getAgentData()->get($a)) {
					$agent_ids[] = $a;
				}
			}
		}

		if (!$agent_ids) {
			$this->tracker->logMessage("[SendAgentEmail] No agents");
			return;
		}

		$this->tracker->logMessage("[SendAgentEmail] Agents: " . implode(', ', $agent_ids));

		$change_info = array(
			'type' => 'agent_notify',
			'notify_type' => 'template',
			'template' => $this->template,
			'emailed' => array()
		);

		$tpl = $this->template;
		$from_name = $this->getFromName($ticket);

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

		foreach ($agent_ids as $agent_id) {

			/** @var $agent \Application\DeskPRO\Entity\Person */
			$agent = App::getEntityRepository('DeskPRO:Person')->find($agent_id);

			if (!$agent || !$agent->getPrimaryEmailAddress()) {
				$this->tracker->logMessage("[SendAgentEmail] Bad agent: " . $agent_id);
				continue;
			}

			if (!$agent->is_agent) {
				$this->tracker->logMessage("[SendAgentEmail] Not an agent: " . $agent_id);
				$e = new \InvalidArgumentException("[SendAgentEmail] Not an agent");
				$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);
				continue;
			}

			$agent->loadHelper('Agent');
			$agent->loadHelper('AgentTeam');
			$agent->loadHelper('AgentPermissions');
			$agent->loadHelper('PermissionsManager');

			if (!$agent->PermissionsManager->TicketChecker->canView($ticket)) {
				$this->tracker->logMessage("[SendAgentEmail] Skipping notify agent $agent_id (noperm)");
				continue;
			}

			$performer = App::getCurrentPerson();
			$sla = null;
			$sla_status = null;

			if ($this->tracker && $this->tracker->getExtra('sla')) {
				$performer = null;
				$sla = $this->tracker->getExtra('sla');
				$sla_status = $this->tracker->getExtra('sla_status');
			}

			$vars = array(
				'action_performer'   => $performer,
				'sla'                => $sla,
				'sla_status'         => $sla_status,
				'ticket_logs'        => $ticket_logs,
				'agent'              => $agent,
				'custom_fields'      => $custom_fields,
				'page_display'       => $page_display,
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

			$this->tracker->logMessage("[SendAgentEmail] From address: " . $this->getFromAddress($ticket));

			$from_address = $this->getFromAddress($ticket);
			$from_address = array($from_address => $from_address ? $from_name : $from_address);
			$message->setFrom($from_address);

			$email_time = microtime(true);
			App::getMailer()->send($message);

			$this->tracker->logMessage("[SendAgentEmail] Email to " . $agent_id . ' ' . $agent->getPrimaryEmailAddress() . " with template $tpl (took " . sprintf("%.4f", microtime(true)-$email_time) . " s)");

			$change_info['emailed'][] = $agent;
		}

		$this->tracker->recordMultiPropertyChanged('log_actions', null, $change_info);
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		if (!App::getTemplating()->exists($this->template)) {
			return array();
		}

		return array(
			array('action' => 'send_agent_email', 'template' => $this->template)
		);
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		return 'Send an email to agents using template ' . $this->template;
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		return $other_action;
	}
}
