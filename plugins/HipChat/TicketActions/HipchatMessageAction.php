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

namespace HipChat\TicketActions;

use Application\DeskPRO\App;
use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

class HipchatMessageAction extends \Application\DeskPRO\Tickets\TicketActions\AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker|null
	 */
	protected $tracker;

	/**
	 * @var string
	 */
	protected $room;

	public function __construct($room, \Application\DeskPRO\Tickets\TicketChangeTracker $tracker = null)
	{
		$this->tracker = $tracker;
		$this->room = $room;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		if (!$this->tracker) {
			return;
		}

		$is_new_ticket = false;
		$is_new_agent_reply = false;
		$is_new_agent_note = false;
		$is_new_user_reply = false;
		if ($this->tracker->isNewTicket()) {
			$is_new_ticket = true;
		}
		if ($this->tracker->hasNewAgentReply()) {
			$is_new_agent_reply = true;
			if ($this->tracker->getNewAgentReply()->is_agent_note) {
				$is_new_agent_note = true;
			}
		}
		if ($this->tracker->hasNewUserReply()) {
			$is_new_user_reply = true;
		}

		$vars = array(
			'is_new_ticket'      => $is_new_ticket,
			'is_new_agent_reply' => $is_new_agent_reply,
			'is_new_agent_note'  => $is_new_agent_note,
			'is_new_user_reply'  => $is_new_user_reply,
			'ticket'             => $ticket,
			'performer'          => $this->tracker->getPersonPerformer(),
			'log_items'          => $this->tracker->getLogInspector()->getTicketLogs(),
		);

		if ($is_new_ticket) {
			$vars['performer'] = $ticket->person;
		} elseif ($is_new_agent_reply || $is_new_user_reply) {
			$message = $this->tracker->getNewReply();
			if ($message) {
				$vars['performer'] = $message->person;
			}
		}

		$message = App::getTemplating()->render('AgentBundle:TicketSearch:notify-row.html.twig', $vars);
		if (preg_match('#<div[^>]+class="info"[^>]*>(.*)</div>#s', $message, $match)) {
			$message = trim($match[1]);
		}
		$message = '<a href="' . App::getSetting('core.deskpro_url') . 'agent/#app.tickets,t:' . $ticket->id . '">'
			. htmlspecialchars($ticket->subject) . ' (#' . $ticket->id . ')</a> - '
			. $message;

		// hipchat wants entities for unicode characters so convert them
		$message = preg_replace_callback('/[\x{80}-\x{FFFFFF}]/u', function($match) {
			$string = $match[0];
			$c1 = ord($string[0]);
			if ($c1 < 0x80) {
				return $c1;
			}

			$code = null;

			if (($c1 & 0xF8) == 0xF0) {
				// 4 bytes
				$code = (($c1 & 0x07) << 18) | ((ord($string[1]) & 0x3F) << 12) | ((ord($string[2]) & 0x3F) << 6) | (ord($string[3]) & 0x3F);
			} else if (($c1 & 0xF0) == 0xE0) {
				// 3 bytes
				$code = (($c1 & 0x0F) << 12) | ((ord($string[1]) & 0x3F) << 6) | (ord($string[2]) & 0x3F);
			} else if (($c1 & 0xE0) == 0xC0) {
				// 2 bytes
				$code = (($c1 & 0x1F) << 6) | (ord($string[1]) & 0x3F);
			}

			if ($code) {
				return '&#' . $code . ';';
			} else {
				return '?';
			}
		}, $message);

		try {
			$api = new \HipChatApi(App::getSetting('HipChat.api_token'));
			$api->message_room(
				$this->room,
				'DeskPRO',
				$message,
				App::getSetting('HipChat.notify')
			);
		} catch (\Exception $e) {}
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		return array(
			array('action' => 'hipchat_message', 'room' => $this->room)
		);
	}


	/**
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
		if (!$this->room) {
			return '';
		} else {
			return 'Send message to HipChat room ' . $this->room;
		}
	}
}
