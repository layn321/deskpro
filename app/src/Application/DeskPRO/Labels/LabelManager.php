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
 * @category ORM
 */

namespace Application\DeskPRO\Labels;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;

class LabelManager
{
	protected $entity;
	protected $label_entity_name;
	protected $labels_property;

	public function __construct($entity, $label_entity_name, $labels_property = 'labels')
	{
		$this->entity = $entity;
		$this->label_entity_name = $label_entity_name;
		$this->label_entity_classname = str_replace('DeskPRO:', 'Application\\DeskPRO\\Entity\\', $this->label_entity_name);
		$this->labels_property = $labels_property;
	}

	public function createLabelEntity()
	{
		$label = new $this->label_entity_classname;
		return $label;
	}

	public function removeLabel($label)
	{
		$label = self::normalizeLabel($label);

		foreach ($this->entity[$this->labels_property] as $k => $labelobj) {
			if ($labelobj['label'] == $label) {
				$this->entity[$this->labels_property]->remove($k);

				if ($this->entity instanceof Ticket && $this->entity->getTicketLogger()) {
					$this->entity->getTicketLogger()->recordMultiPropertyChanged('label_removed', $label, null);
				}

				$type_name = strtolower(\Orb\Util\Util::getBaseClassname($this->entity)) . 's';
				if ($type_name == 'chatconversations') {
					$type_name = 'chat_conversations';
				}

				App::getDb()->executeUpdate("
					UPDATE label_defs
					SET total = IF(total > 0, total - 1, 0)
					WHERE label_type = ? AND label = ?
				", array($type_name, $label));

				return $labelobj;
			}
		}

		return null;
	}

	public function removeLabels(array $labels)
	{
		foreach ($labels as $label) {
			$this->removeLabel($label);
		}
	}

	public function addLabel($label)
	{
		$label = self::normalizeLabel($label);

		foreach ($this->entity[$this->labels_property] as $labelobj) {
			if ($labelobj['label'] == $label) {
				return $labelobj;
			}
		}

		$labelobj = $this->createLabelEntity();
		$labelobj['label'] = $label;
		$this->entity->addLabel($labelobj);

		$type_name = strtolower(\Orb\Util\Util::getBaseClassname($this->entity)) . 's';
		if ($type_name == 'chatconversations') {
			$type_name = 'chat_conversations';
		}

		if ($type_name == 'persons') {
			$type_name = 'people';
		}

		App::getDb()->executeUpdate("
			INSERT INTO label_defs (label_type, label, total)
			VALUES (?, ?, 1)
			ON DUPLICATE KEY UPDATE total = total + 1
		", array($type_name, $label));

		if ($this->entity instanceof Ticket && $this->entity->getTicketLogger()) {
			$this->entity->getTicketLogger()->recordMultiPropertyChanged('label_added', null, $label);
		}

		return $labelobj;
	}

	public function preSetLabelsArray(array $labels)
	{
		$labels_raw = $labels;
		$labels = array();

		foreach ($labels_raw as $label) {
			$label = self::normalizeLabel($label);
			if ($label) {
				$labels[] = $label;
			}
		}

		$existing_labels = $this->getLabelsArray();
		$added = array_diff($labels, $existing_labels);
		$removed = array_diff($existing_labels, $labels);

		foreach ($added as $added_label) {
			if ($this->entity instanceof Ticket && $this->entity->getTicketLogger()) {
				$this->entity->getTicketLogger()->recordMultiPropertyChanged('label_added', null, $added_label);

				if (!isset($this->entity->_presave_state['label_added'])) $this->entity->_presave_state['label_added'] = array();
				$this->entity->_presave_state['label_added'][] = $added_label;
			}
		}
		foreach ($removed as $removed_label) {
			if ($this->entity instanceof Ticket && $this->entity->getTicketLogger()) {
				$this->entity->getTicketLogger()->recordMultiPropertyChanged('label_removed', $removed_label, null);
			}
		}
	}

	public function addLabels(array $labels)
	{
		foreach ($labels as $label) {
			$this->addLabel($label);
		}
	}

	public function getLabelsArray()
	{
		$labels = array();
		foreach ($this->entity[$this->labels_property] as $label) {
			$labels[] = $label['label'];
		}

		// If its a ticket then a label might have been added to the property array
		if (isset($this->entity->_presave_state['label_added'])) {
			foreach ($this->entity->_presave_state['label_added'] as $x) {
				$labels[] = $x;
			}
		}

		return $labels;
	}

	public function hasLabel($label)
	{
		$label_test = self::normalizeLabel($label);

		foreach ($this->entity[$this->labels_property] as $label) {
			if ($label['label'] == $label_test) {
				return true;
			}
		}

		return false;
	}

	public function setLabelsArray(array $labels, $em = null)
	{
		$labels_raw = $labels;
		$labels = array();

		foreach ($labels_raw as $label) {
			$label = self::normalizeLabel($label);
			if ($label) {
				$labels[] = $label;
			}
		}

		$existing_labels = $this->getLabelsArray();
		$added = array_diff($labels, $existing_labels);
		$removed = array_diff($existing_labels, $labels);

		foreach ($added as $added_label) {
			$obj = $this->addLabel($added_label);
			if ($em && $obj) {
				$em->persist($obj);
			}
		}
		foreach ($removed as $removed_label) {
			$this->removeLabel($removed_label);
			if ($em && $obj) {
				$em->remove($obj);
			}
		}
	}

	public static function normalizeLabel($label)
	{
		$label = strtolower(trim($label));

		return $label;
	}
}
