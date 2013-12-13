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

use Application\DeskPRO\CustomFields\FieldManager;
use Application\DeskPRO\Entity\CustomDefTicket;

use Application\DeskPRO\App;

class TicketFieldAction extends AbstractAction implements PermissionableAction
{
	/**
	 * @var \Application\DeskPRO\CustomFields\FieldManager
	 */
	protected $field_manager;

	/**
	 * @var \Application\DeskPRO\Entity\CustomDefTicket
	 */
	protected $field_def;

	/**
	 * @var mixed
	 */
	protected $set_value;

	public function __construct(FieldManager $field_manager, CustomDefTicket $field_def, $set_value)
	{
		$this->field_manager = $field_manager;
		$this->field_def     = $field_def;
		$this->set_value     = $set_value;
	}


	/**
	 * @return \Application\DeskPRO\Entity\CustomDefTicket
	 */
	public function getFieldDef()
	{
		return $this->field_def;
	}


	/**
	 * @return mixed
	 */
	public function getFieldValue()
	{
		return $this->set_value;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$this->field_manager->saveFormToObject($this->set_value['custom_fields'], $ticket, true);
	}


	/**
	 * {@inheritDoc}
	 */
	public function checkPermission(Ticket $ticket, Person $person)
	{
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
		return array(
			array('action' => 'ticket_field', 'ticket_field_id' => $this->field_def->id, 'value' => $this->set_value)
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
		$tr = App::getTranslator();
		$title = $this->field_def->title;
		$value = $this->set_value;

		$value = isset($value['custom_fields']['field_' . $this->field_def->getId()]) ? $value['custom_fields']['field_' . $this->field_def->getId()] : '';
		if ($this->field_def->getTypeName() == 'choice') {
			$value_ids = (array)$value;
			$value = array();
			$titles = $this->field_def->getAllChildTitles();
			foreach ($value_ids as $id) {
				if (isset($titles[$id])) {
					$value[] = $titles[$id];
				}
			}
			$value = implode(', ', $value);
		}

		return $tr->phrase('agent.tickets.set_x_to_y_action', array('title' => $title, 'value' => $value));
	}


	/**
	 * @return string
	 */
	public function getActionName()
	{
		return get_class($this) . '[' . $this->field_def->getId() . ']';
	}
}
