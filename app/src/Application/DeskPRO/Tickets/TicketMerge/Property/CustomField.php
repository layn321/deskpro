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
 * @category Tickets
 */

namespace Application\DeskPRO\Tickets\TicketMerge\Property;


use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\CustomDefTicket;
use Application\DeskPRO\Entity\CustomDataTicket;

use Orb\Util\Arrays;

/**
 * Merges custom fields
 */
class CustomField extends PropertyAbstract
{
	/**
	 * @var \Application\DeskPRO\Entity\CustomDefTicket
	 */
	protected $field;

	/**
	 * Data that will potentially be lost
	 *
	 * @var mixed|null
	 */
	public $lost = null;

	public function setField(CustomDefTicket $field)
	{
		$this->field = $field;
	}

	public function merge()
	{
		if ($this->strategy == self::STRATEGY_LEFT) {
			return;
		}

		// No children means its a simple field (text input etc)
		if (!count($this->field->children)) {
			if ($this->strategy == self::STRATEGY_RIGHT) {
				$other_exist = $this->other_ticket->getCustomDataForField($this->field);
				if ($other_exist) {
					$this->ticket->removeCustomDataForField($this->field);
					$this->_addCustomData($other_exist);
				}
			} elseif ($this->strategy == self::STRATEGY_COMBINE) {
				$exist = $this->ticket->getCustomDataForField($this->field->id);
				$other_exist = $this->other_ticket->getCustomDataForField($this->field->id);
				if ($exist && $exist->input !== '') {
					if ($other_exist && $exist->getData() != $other_exist->getData()) {
						$this->lost = $other_exist->getData();
					}
					return;
				}

				if ($other_exist) {
					$this->ticket->removeCustomDataForField($this->field);
					$this->_addCustomData($other_exist);
				}
			}

		// Children means we can potentially merge selections
		} else {
			$multiple = $this->field->getOption('multiple');
			$hasValue = false;
			$hasOtherValue = false;
			foreach ($this->field->children as $child) {
				if ($this->ticket->getCustomDataForField($child)) {
					$hasValue = true;
				}
				if ($this->other_ticket->getCustomDataForField($child)) {
					$hasOtherValue = true;
				}
			}

			if ($this->strategy == self::STRATEGY_COMBINE) {
				foreach ($this->field->children as $child) {
					// Ignore if left already has a value
					$exist = $this->ticket->getCustomDataForField($child);
					$other_exist = $this->other_ticket->getCustomDataForField($child);
					if ($exist) {
						if ($other_exist && $exist->getData() != $other_exist->getData()) {
							$this->lost = $other_exist->getData();
						}
						continue;
					}

					if ($other_exist) {
						if (!$multiple && $hasValue) {
							// already have a value for this field, so losing the other
							$this->lost = $other_exist->getData();
							continue;
						}

						$this->_addCustomData($other_exist);
					}
				}
			} elseif ($this->strategy == self::STRATEGY_RIGHT) {
				// Take right ones over left ones
				foreach ($this->field->children as $child) {
					$exist = $this->ticket->getCustomDataForField($child);
					if ($exist && $hasOtherValue && !$multiple) {
						// remove this value as we'll get another
						$this->ticket->removeCustomDataForField($child);
					}

					$other_exist = $this->other_ticket->getCustomDataForField($child);
					if ($other_exist) {
						$this->_addCustomData($other_exist);
					}
				}
			}
		}
	}

	protected function _addCustomData(CustomDataTicket $data)
	{
		$new_data = new CustomDataTicket();
		$new_data->value = $data->value;
		$new_data->input = $data->input;
		$new_data->field = $data->field;
		$new_data->root_field = $data->root_field;
		$new_data->ticket = $this->ticket;

		$this->ticket->addCustomData($new_data);
	}
}
