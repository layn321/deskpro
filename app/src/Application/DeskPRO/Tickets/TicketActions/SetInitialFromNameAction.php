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

use Application\DeskPRO\App;
use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Tickets\TicketChangeTracker;

class SetInitialFromNameAction extends AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	/**
	 * @var string
	 */
	protected $pattern;

	/**
	 * @var bool
	 */
	protected $to_user = true;

	/**
	 * @var bool
	 */
	protected $to_agent = true;

	public function __construct($pattern, $to_user = true, $to_agent = true, TicketChangeTracker $tracker = null)
	{
		$this->tracker  = $tracker;
		$this->pattern  = $pattern;
		$this->to_user  = $to_user;
		$this->to_agent = $to_agent;
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

		if ($this->to_agent) {
			$address = $this->getAddress($ticket, false);
			$this->tracker->recordExtra('set_initial_from_toagent', $address);
		}
		if ($this->to_user) {
			$address = $this->getAddress($ticket, true);
			$this->tracker->recordExtra('set_initial_from_touser', $address);
		}
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getAddress(Ticket $ticket, $to_user)
	{
		return $ticket->replaceVarsInString(
			$this->pattern,
			$this->tracker->getPersonPerformer(),
			false,
			$to_user
		);
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		return array();
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
		if (!$this->to_agent || !$this->to_user) {
			if ($this->to_agent) {
				$desc = 'Emails sent to agents have the "From" name set to ';
			} else {
				$desc = 'Emails sent to users have the "From" name set to ';
			}
		} else {
			$desc = 'Emails have the "From" name set to ';
		}

		$desc .= ($as_html ? htmlspecialchars($this->pattern) : $this->pattern);

		$performer_friendly = 'the name of the action performer';

		if ($this->trigger) {
			switch ($this->trigger->event_trigger) {
				case 'new.email.user':
				case 'new.web.user':
					$performer_friendly = 'the name of the user creating the ticket';
					break;
				case 'update.user':
					$performer_friendly = 'the name of the user';
					break;

				case 'new.email.agent':
				case 'new.web.agent.portal':
					$performer_friendly = 'the name of the agent creating the ticket';
					break;
				case 'update.agent':
					$performer_friendly = 'the name of the agent';
					break;
			}
		}

		$desc = str_replace('{{performer.name}}', $performer_friendly, $desc);
		$desc = str_replace('{{ticket.department}}', 'Department', $desc);
		$desc = str_replace('{{ticket.category}}', 'Category', $desc);
		$desc = str_replace('{{ticket.product}}', 'Product', $desc);

		return $desc;
	}
}
