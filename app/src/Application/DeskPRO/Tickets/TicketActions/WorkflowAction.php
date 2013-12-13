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
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

class WorkflowAction extends AbstractAction implements PermissionableAction
{
	protected $workflow_id;

	public function __construct($workflow)
	{
		$this->workflow_id = $workflow;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$ticket['workflow_id'] = $this->workflow_id;
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		if ($ticket['workflow_id'] == $this->workflow_id) {
			return array();
		}

		return array(
			array('action' => 'workflow', 'workflow_id' => $this->workflow_id)
		);
	}


	/**
	 * {@inheritDoc}
	 */
	public function checkPermission(Ticket $ticket, Person $person)
	{
		if ($ticket->getWorkflowId() == $this->workflow_id) {
			return true;
		}

		if (!$person->PermissionsManager->TicketChecker->canModify($ticket, 'fields')) {
			return false;
		}

		return true;
	}


	/**
	 * Get the workflow id
	 *
	 * @return int
	 */
	public function getWorkflowId()
	{
		return $this->workflow_id;
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
		$tr = App::getTranslator();

		if ($this->workflow_id == 0) {
			return $tr->phrase('agent.tickets.remove_workflow_action');
		} else {
			$names = App::getEntityRepository('DeskPRO:TicketWorkflow')->getNames();
			if (!isset($names[$this->workflow_id])) {
				$name = "<error>Unknown #{$this->workflow_id}</error>";
			} else {
				$name = $as_html ? htmlspecialchars($names[$this->workflow_id]) : $names[$this->workflow_id];
			}

			return $tr->phrase('agent.tickets.set_workflow_action', array('workflow' => $name));
		}
	}
}
