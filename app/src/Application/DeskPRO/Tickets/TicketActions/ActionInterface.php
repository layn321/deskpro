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

use Application\DeskPRO\Entity\Ticket;

/**
 * Important: Constructors shouldn't do any work or require any transient values like "current user."
 * The action should always be usable from any context if possible. But at the very least, getDescription()
 * needs to be able to run and return a suitable string.
 */
interface ActionInterface
{
	/**
	 * Apply the action to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return void
	 */
	public function apply(Ticket $ticket);


	/**
	 * Merge this action into another, and return the new merged action.
	 *
	 * For example, if a property is set, then the "other" action would overwrite the
	 * "this" action, so you could just return "other"
	 *
	 * But if you were adding a value to a collection, then you could merge the two collections
	 * together so the new action had new items from both actions.
	 *
	 * @param ActionInterface $action
	 * @return ActionInterface
	 */
	public function merge(ActionInterface $other_action);


	/**
	 * Get a text description of the action
	 *
	 * @return string
	 */
	public function getDescription($as_html = true);


	/**
	 * @param array $metadata
	 * @return mixed
	 */
	public function setMetaData(array $metadata);


	/**
	 * @return array
	 */
	public function getMetaData();

	/**
	 * @return bool
	 */
	public function doPrepend();

	/**
	 * @return string
	 */
	public function getActionName();
}
