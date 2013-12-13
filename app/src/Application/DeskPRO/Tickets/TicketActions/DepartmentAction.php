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

class DepartmentAction extends AbstractAction implements PermissionableAction
{
	protected $department_id;

	public function __construct($department)
	{
		$this->department_id = $department;
	}

	public function checkPermission(Ticket $ticket, Person $person)
	{
		if ($ticket->getDepartmentId() == $this->department_id) {
			return true;
		}

		if (!$person->PermissionsManager->TicketChecker->canModify($ticket, 'department')) {
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
		$dep_id = $this->department_id;
		if ($dep_id == 'email_account') {
			if ($ticket->email_gateway && $ticket->email_gateway->department && $ticket->email_gateway->department->is_tickets_enabled) {
				$dep_id = $ticket->email_gateway->department->getId();
			} else {
				// no op
				return;
			}
		}

		$ticket['department_id'] = $dep_id;
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		if ($ticket['department_id'] == $this->department_id) {
			return array();
		}

		return array(
			array('action' => 'department', 'department_id' => $this->department_id)
		);
	}


	/**
	 * Get the department id
	 *
	 * @return int
	 */
	public function getDepartmentId()
	{
		return $this->department_id;
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
		if ($this->department_id == 'email_account') {
			return 'Set department as the linked department for the email account';
		}

		$tr = App::getTranslator();

		$names = App::getDataService('Department')->getFullNames();
		if (!isset($names[$this->department_id])) {
			$name = "<error>Unknown #{$this->department_id}</error>";
		} else {
			$name = $names[$this->department_id];
		}

		return $tr->phrase('agent.tickets.set_department_action', array('department' => $name));
	}
}
