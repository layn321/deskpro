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

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\App;

use Orb\Util\Arrays;

/**
 * Adds labels
 */
class AddLabelsAction extends AbstractAction implements PermissionableAction
{
	protected $add_labels;

	public static function newFromString($add_labels)
	{
		$add_labels = explode(',', $add_labels);

		return new self($add_labels);
	}

	public function __construct(array $add_labels)
	{
		array_walk($add_labels, 'trim');
		$add_labels = Arrays::removeEmptyString($add_labels);

		$this->add_labels = $add_labels;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		if (!$this->add_labels) {
			return;
		}

		$ticket->getLabelManager()->addLabels($this->add_labels);
	}


	/**
	 * {@inheritDoc}
	 */
	public function checkPermission(Ticket $ticket, Person $person)
	{
		if (!$person->PermissionsManager->TicketChecker->canModify($ticket, 'labels')) {
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
		$added_labels = array();
		foreach ($this->add_labels as $l) {
			if (!$ticket->getLabelManager()->hasLabel($l)) {
				$added_labels[] = $l;
			}
		}

		if (!$added_labels) {
			return array();
		}

		return array(
			array('action' => 'add_labels', 'label' => $added_labels)
		);
	}


	/**
	 * Get labels
	 *
	 * @return array
	 */
	public function getLabels()
	{
		return $this->add_labels;
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		$labels = array_merge($this->add_labels, $other_action->getLabels());
		$labels = array_unique($labels);

		return new self($labels);
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
        $tr = App::getTranslator();
		return $tr->phrase('agent.tickets.add_labels_action', array('labels' => implode(', ', $this->add_labels)));
	}
}
