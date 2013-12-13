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
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Tickets\TicketChangeTracker;

/**
 * Sets/removes on hold status
 */
class HoldAction extends AbstractAction implements PermissionableAction
{
	/**
	 * @var bool
	 */
	protected $is_hold;

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;

	public function __construct($is_hold, TicketChangeTracker $tracker = null)
	{
		$this->is_hold = (bool)$is_hold;
		$this->tracker = $tracker;
	}

	/**
	 * True to stop processing actions after this one
	 *
	 * @return bool
	 */
	public function checkPermission(Ticket $ticket, Person $person)
	{
		// No change, sure they can apply no change
		if ($ticket->is_hold == $this->is_hold) {
			return true;
		}

		if (!$person->PermissionsManager->TicketChecker->canModify($ticket, 'set_hold')) {
			return false;
		}

		return true;
	}

	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$ticket->is_hold = $this->is_hold;
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		if ($ticket->is_hold == $this->is_hold) {
			return array();
		}

		return array(
			array('action' => 'hold', 'is_hold' => $this->is_hold)
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
        if ($this->is_hold) {
			return "Put ticket on hold";
		} else {
			return "Remove ticket from hold";
		}
	}
}
