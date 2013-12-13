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

class CategoryAction extends AbstractAction implements PermissionableAction
{
	protected $category_id;

	public function __construct($category)
	{
		$this->category_id = $category;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$ticket['category_id'] = $this->category_id;
	}


	/**
	 * {@inheritDoc}
	 */
	public function checkPermission(Ticket $ticket, Person $person)
	{
		if ($ticket->getCategoryId() == $this->category_id) {
			return true;
		}

		if (!$person->PermissionsManager->TicketChecker->canModify($ticket, 'fields')) {
			return false;
		}

		return true;
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		if ($ticket['category_id'] == $this->category_id) {
			return array();
		}

		return array(
			array('action' => 'category', 'category_id' => $this->category_id)
		);
	}


	/**
	 * Get the category id
	 *
	 * @return int
	 */
	public function getCategoryId()
	{
		return $this->category_id;
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

		if ($this->category_id == 0) {
			return $tr->phrase('agent.tickets.remove_category_action');
		} else {
			$names = App::getEntityRepository('DeskPRO:TicketCategory')->getFullNames();
			if (!isset($names[$this->category_id])) {
				$name = "<error>Unknown #{$this->category_id}</error>";
			} else {
				$name = $names[$this->category_id];
			}

			return $tr->phrase('agent.tickets.set_category_action', array('category' => $name));
		}
	}
}
