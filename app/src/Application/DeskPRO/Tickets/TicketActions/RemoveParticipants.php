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

/**
 * Remove participants
 */
class RemoveParticipants extends AbstractAction
{
	protected $remove_people_ids;

	public function __construct(array $remove_participants)
	{
		$this->remove_people_ids = $remove_participants;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$people = App::getEntityRepository('DeskPRO:Person')->getPeopleFromIds($this->remove_people_ids);
		foreach ($people as $person) {
			$ticket->removeParticipantPerson($person);
		}
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		$actions = array();

		foreach ($this->remove_people_ids as $pid) {
			$actions[] = array(
				'action' => 'remove_participant',
				'person_id' => $pid
			);
		}

		return $actions;
	}


	/**
	 * Get the agent id
	 *
	 * @return int
	 */
	public function getPersonIds()
	{
		return $this->remove_people_ids;
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		$ids = $this->getPersonIds();
		$ids = array_merge($ids, $other_action->getPersonIds());
		$ids = array_unique($ids);

		return new self($ids);
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		$tr = App::getTranslator();
		$people = App::getEntityRepository('DeskPRO:Person')->getPeopleFromIds($this->remove_people_ids);
		if (!$people) return '';

		$names = array();
		foreach ($people as $p) {
			$names[$p->id] = $as_html ? htmlspecialchars($p->getDisplayName()) : $p->getDisplayName();
		}

		foreach ($this->remove_people_ids as $id) {
			if (!isset($names[$id])) {
				$names[$id] = "<error>Unknown #$id</error>";
			}
		}

		return $tr->phrase('agent.tickets.remove_participants_action', array('parts' => implode(', ', $names)));
	}
}
