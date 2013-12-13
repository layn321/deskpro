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
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Translate\DelegatePhrase;
use Application\DeskPRO\App;

class UserNotificationNewReplyAgentAction extends AbstractUserNotificationAction
{
	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$tpl = $this->getTemplate('user_new_reply_agent', 'DeskPRO:emails_user:new-reply-agent.html.twig');

		if ($this->tracker->isExtraSet('email_template_user_newreply_agent')) {
			$tpl = $this->tracker->getExtra('email_template_user_newreply_agent');
			$this->tracker->logMessage("[UserNotificationNewReplyAgent] Set newreply_agent template: " . $this->tracker->getExtra('email_template_user_newreply_agent'));
		}

		// Agents can supress user notifications by unticking the option in the replybox
		if ($this->tracker->isExtraSet('suppress_user_notify')) {
			$this->tracker->logMessage("[UserNotificationNewReplyAgent] Notification suppressed");
			return;
		}

		$this->via_message = $this->tracker->getNewAgentReply();

		// Users arent notified of notes ofc
		if ($this->via_message->is_agent_note) {
			$this->tracker->logMessage("[UserNotificationNewReplyAgent] No notify of agent note");
			return;
		}

		$change_info = array(
			'type' => 'user_notify',
			'notify_type' => 'newreply',
			'emailed' => array(),
			'cced' => array()
		);

		$vars = array(
			'action' => 'new_user_reply',
			'show_rating_link' => App::getSetting('core.tickets.enable_feedback')
		);

		// Dont rate own, dont rate notes, dont rate replies by non agents
		if (
			$this->via_message->is_agent_note
			|| !$this->via_message->person->is_agent
			|| $this->via_message->person->getId() == $ticket->person->getId()
		) {
			if (App::getSetting('core.tickets.enable_feedback')) {
				$this->tracker->logMessage("[UserNotificationNewReplyAgent] No rating links");
				$vars['show_rating_link'] = false;
			}
		}

		if ($ticket->getProperty('send_reply_service')) {
			$this->tracker->logMessage("[UserNotificationNewReplyAgent] Send via send_reply_service");
			try {
				$client = new \Zend\Http\Client(null, array('timeout' => 30, 'strictredirects' => true));
				$client->setMethod(\Zend\Http\Request::METHOD_POST);
				$client->setUri($ticket->getProperty('send_reply_service'));

				$data = array();
				$data['subject'] = $ticket->getSubject();
				$data['message'] = $this->via_message->getMessageHtml();
				$data['email']   = $this->via_message->person->getPrimaryEmailAddress();
				$data['name']    = $this->via_message->person->name;

				if ($ticket->getProperty('send_reply_tac')) {
					$data['tac'] = $ticket->getProperty('send_reply_tac');
				}

				$data['my_tac'] = $ticket->getAccessCode();
				$data['my_reply_service'] = App::getRouter()->generateUrl('user') . 'api/open/tickets/new-ticket-message';

				$client->getRequest()->post()->fromArray($data);
				$r = $client->send();
				$r = $r->getBody();

				$data = null;
				if ($r) {
					$data = @json_decode($r, true);
				}

				$this->tracker->logMessage("[UserNotificationNewReplyAgent] send_reply_service result: " . $r);

				if (!$data || !isset($data['success'])) {
					$this->tracker->logMessage("[UserNotificationNewReplyAgent] Invalid result from send_reply_service");
					throw new \InvalidArgumentException("Invalid result from send_reply_service");
				}

				$ticket->setProperty('send_reply_tac', $data['tac']);
				App::getOrm()->persist($ticket);
				App::getOrm()->flush();

			} catch (\Exception $e) {
				$this->tracker->logMessage("[UserNotificationNewReplyAgent] send_reply_service exception: " . $e->getMessage());
				$this->doSend($tpl, $vars, $ticket, $change_info);
			}
		} else {
			$this->doSend($tpl, $vars, $ticket, $change_info);
		}

		$this->tracker->recordMultiPropertyChanged('log_actions', null, $change_info);
	}

	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		return '';
	}
}
