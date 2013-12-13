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
 * @subpackage UserBundle
 */

namespace Application\AgentBundle\Validator;

use Application\DeskPRO\App;
use Application\DeskPRO\CustomFields\Handler\HandlerAbstract;
use Application\DeskPRO\Entity;

use Application\AgentBundle\Form\Model\NewTicket;

use Orb\Util\Arrays;
use Orb\Validator\AbstractValidator;

class NewTicketValidator extends AbstractValidator
{
	protected $run_validators = array();

	/**
	 * @var \Application\AgentBundle\Form\Model\NewTicket
	 */
	protected $newticket;

	/**
	 * @var array
	 */
	protected $page_data = array();

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $mock_ticket;

	/**
	 * @var bool
	 */
	protected $is_resolved = false;

	/**
	 * @param array $page_data
	 */
	public function setPageData($page_data)
	{
		$this->page_data = $page_data;
	}

	/**
	 * Check $value to see if its valid.
	 *
	 * @param \Application\AgentBundle\Form\Model\NewTicket $newticket
	 * @return bool
	 */
	protected function checkIsValid($newticket)
	{
		$this->newticket = $newticket;
		$this->is_resolved = $newticket->status == 'resolved';

		$this->mock_ticket = new \Application\DeskPRO\Entity\Ticket(false);
		$this->mock_ticket->_setNoPersist();
		if ($newticket->department_id) {
			$this->mock_ticket->setDepartmentId($newticket->department_id);
		}
		if ($newticket->category_id) {
			$this->mock_ticket->setCategoryId($newticket->category_id);
		}
		if ($newticket->product_id) {
			$this->mock_ticket->setProductId($newticket->product_id);
		}
		if ($newticket->priority_id) {
			$this->mock_ticket->setPriorityId($newticket->priority_id);
		}
		if ($newticket->workflow_id) {
			$this->mock_ticket->setWorkflowId($newticket->workflow_id);
		}

		$this->_traverseItems($this->page_data);

		if ($this->errors) {
			return false;
		}

		return true;
	}

	protected function _traverseItems(array $items)
	{
		foreach ($items as $item) {
			if ($item['field_type'] == 'group') {
				if (empty($item['items'])) {
					continue;
				}

				$this->_traverseItems($item['items']);
			} else {
				$this->_validateItem($item);
			}
		}
	}

	protected function _validateItem($item)
	{
		if (!empty($item['rules'])) {
			$terms = new \Application\DeskPRO\Tickets\TicketTerms($item['rules']);
			if ($item['rule_match_type'] == 'any') {
				if (!$terms->doesTicketMatchAny($this->mock_ticket)) {
					return;
				}
			} else {
				if (!$terms->doesTicketMatch($this->mock_ticket)) {
					return;
				}
			}
		}

		switch ($item['field_type']) {
			case 'ticket_product':
				if (App::getSetting('core.use_product')) {
					$validator = new \Application\DeskPRO\Validator\GenericCategory(array(
						'category_repository' => App::getEntityRepository('DeskPRO:Product'),
						'allow_none' => !App::getSetting('core_tickets.field_validation_ticket_prod_agent_required')
					));
					if (!$validator->isValid($this->newticket->product_id)) {
						$this->addError('ticket.product_id', array('message' => 'Select a product'));
					}
				}
				break;

			case 'ticket_category':
				if (App::getSetting('core.use_ticket_category')) {
					$validator = new \Application\DeskPRO\Validator\GenericCategory(array(
						'category_repository' => App::getEntityRepository('DeskPRO:TicketCategory'),
						'allow_none' => !App::getSetting('core_tickets.field_validation_ticket_cat_agent_required')
					));
					if (!$validator->isValid($this->newticket->category_id)) {
						$this->addError('ticket.category_id', array('message' => 'Select a category'));
					}
				}
				break;

			case 'ticket_priority':
				if (App::getSetting('core.use_ticket_priority')) {
					$validator = new \Application\DeskPRO\Validator\TicketPriority(array(
						'allow_none' => !App::getSetting('core_tickets.field_validation_ticket_pri_agent_required')
					));
					if (!$validator->isValid($this->newticket->priority_id)) {
						$this->addError('ticket.priority_id', array('message' => 'Select a priority'));
					}
				}
				break;

			case 'ticket_workflow':
				if (App::getSetting('core.use_ticket_workflow')) {
					$validator = new \Application\DeskPRO\Validator\TicketWorkflow(array(
						'allow_none' => !App::getSetting('core_tickets.field_validation_ticket_work_agent_required')
					));
					if (!$validator->isValid($this->newticket->workflow_id)) {
						$this->addError('ticket.workflow_id', array('message' => 'Select a workflow'));
					}
				}
				break;

			case 'ticket_field':
				$field = App::getSystemService('TicketFieldsManager')->getFieldFromId($item['field_id']);
				if ($field && $field->is_enabled) {
					if ($field->getOption('agent_validation_resolve') && !$this->is_resolved) {
						// no validation, its only on resolve
					} else {
						if ($this->newticket->exist_ticket) {
							$errors = $field->getHandler()->validateFormData($this->newticket->ticket_fields, HandlerAbstract::CONTEXT_AGENT, array('exist_ticket' => $this->newticket->exist_ticket));
						} else {
							$errors = $field->getHandler()->validateFormData($this->newticket->ticket_fields, HandlerAbstract::CONTEXT_AGENT);
						}
						foreach ($errors as $code) {
							$title = $field->getTitle();
							$str = "Please correct $title";
							$code = str_replace('field_' . $field->getId() . '.', '', $code);
							switch ($code) {
								case 'required':
									$str = "$title is required";
									break;
								case 'min_length':
									$str = "$title is too short";
									break;
								case 'max_length':
									$str = "$title is too long";
									break;
								case 'regex':
									$str = "$title is invalid";
									break;
							}

							$this->addError('ticket.' . $code, array('message' => $str));
						}
					}
				}
				break;
		}
	}
}
