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
use Application\DeskPRO\App;

/**
 * Sets flag
 */
class FlagAction extends AbstractAction implements PersonContextInterface, ExecutionContextAware
{
	protected $flag;
	protected $person_context;
	protected $execution_context = null;

	public function __construct($flag)
	{
		$this->flag = $flag;
	}


	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}

	public function setExecutionContext($context)
	{
		$this->execution_context = $context;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$flag = $this->flag;

		// Context of a trigger, flagging means flag for everyone
		if ($this->execution_context == 'trigger') {
			$agents = App::getEntityRepository('DeskPRO:Person')->getAgents();
			foreach ($agents as $a) {
				$ticket->setFlagForPerson($a, $flag);
			}

		// Otherwise its a macro, flag for the performer
		} else {
			// Invalid context
			if (!$this->person_context OR !$this->person_context['is_agent']) {
				return;
			}

			$ticket->setFlagForPerson($this->person_context, $flag);
		}
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		return array(
			array('action' => 'flag', 'color' => $this->flag)
		);
	}


	/**
	 * Get the flag color
	 *
	 * @return int
	 */
	public function getFlag()
	{
		return $this->flag;
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

		if (!$this->flag) {
			return $tr->phrase('agent.tickets.unset_flag_action');
		} else {
			return $tr->phrase('agent.tickets.set_flag_to_action', array('flag' => $this->flag));
		}
	}
}
