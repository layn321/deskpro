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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Form\CustomField\Model;

use Orb\Util\Strings;

class ChoiceField extends CustomFieldAbstract
{
	public $multiple = false;
	public $expanded = false;
	public $min_length;
	public $max_length;

	public $agent_min_length;
	public $agent_max_length;

	public $field_type = null;
	public $choices_structure = '';
	public $choices_removed_structure = '';

	public $default_option = '';

	protected function init()
	{
		if ($this->_field->default_value) {
			$this->default_option = $this->_field->default_value;
		}

		if ($this->_field->getOption('multiple')) {
			$this->multiple = true;
		}
		if ($this->_field->getOption('expanded')) {
			$this->expanded = true;
		}

		if ($this->_field->getOption('min_length')) {
			$this->validation_type = 'required';
			$this->required = true;
			$this->min_length = $this->_field->getOption('min_length');
		}
		if ($this->_field->getOption('max_length')) {
			$this->validation_type = 'required';
			$this->required = true;
			$this->max_length = $this->_field->getOption('max_length');
		}

		if ($this->_field->getOption('agent_min_length')) {
			$this->agent_validation_type = 'required';
			$this->agent_required = true;
			$this->agent_min_length = $this->_field->getOption('agent_min_length');
		}
		if ($this->_field->getOption('agent_max_length')) {
			$this->agent_validation_type = 'required';
			$this->agent_required = true;
			$this->agent_max_length = $this->_field->getOption('agent_max_length');
		}

		if ($this->multiple) {
			if ($this->expanded) {
				$this->field_type = 'checkbox';
			} else {
				$this->field_type = 'multi_select';
			}
		} else {
			if ($this->expanded) {
				$this->field_type = 'radio';
			} else {
				$this->field_type = 'select';
			}
		}

		if (!$this->isNewField()) {
			foreach ($this->_field->children as $child) {
				$this->choices[$child->id] = $child->title;
			}
		}
	}

	public function setFieldType($field_type)
	{
		$this->field_type = $field_type;
		if ($this->field_type == 'checkbox' || $this->field_type == 'radio') {
			$this->expanded = true;
		} else {
			$this->expanded = false;
		}
		if ($this->field_type == 'checkbox' || $this->field_type == 'multi_select') {
			$this->multiple = true;
		} else {
			$this->multiple = false;
		}
	}

	protected function setFieldProperties()
	{
		$field = $this->_field;

		$field->setOption('multiple', $this->multiple);
		$field->setOption('expanded', $this->expanded);

		if ($this->validation_type == 'required') {
			$field->setOption('required', true);
			$field->setOption('min_length', $this->min_length);
			$field->setOption('max_length', $this->max_length);
		} else {
			$field->setOption('required', null);
			$field->setOption('min_length', null);
			$field->setOption('max_length', null);
		}

		if ($this->agent_validation_type == 'required') {
			$field->setOption('agent_required',   true);
			$field->setOption('agent_min_length', $this->agent_min_length);
			$field->setOption('agent_max_length', $this->agent_max_length);
		} else {
			$field->setOption('agent_required', null);
			$field->setOption('agent_min_length', null);
			$field->setOption('agent_max_length', null);
		}

		if (!$this->default_option) {
			$field->default_value = null;
		}
	}

	protected function saveAdditional()
	{
		$choices_structure = @json_decode($this->choices_structure, true);
		$choices_removed   = @json_decode($this->choices_removed_structure, true);
		if (!$choices_removed) $choices_removed = array();

		$choices = array();
		foreach ($this->_field->children as $child) {
			$choices[$child->getId()] = $child;
		}

		// Maps string IDs generated on the client with real
		// field IDs saved in the database that we've saved right now
		$new_id_map = array();

		foreach ($choices_structure as $k => $info) {
			$parent_id = $info['parent_id'];
			if ($parent_id && is_string($parent_id)) {
				if (!isset($new_id_map[$parent_id])) {
					continue;
				}
				$parent_id = $new_id_map[$parent_id];
			}
			if (!$parent_id) {
				$parent_id = 0;
			}

			$id = $info['id'];
			$title = $info['title'];

			if (in_array($id, $choices_removed)) {
				continue;
			}

			if (isset($choices[$id])) {
				$choices[$id]->setTitle($title);
				$choices[$id]->setDisplayOrder($k);

				$this->_em->persist($choices[$id]);
			} else {
				$child = $this->_field->createChild();
				$child->setTitle($title);
				$child->setDisplayOrder($k);
				$child->setOption('parent_id', $parent_id);

				$this->_em->persist($child);
				$this->_em->flush();

				$new_id_map[$id] = $child->getId();
				$choices[$child->getId()] = $child;
			}
		}

		foreach ($choices_removed as $id) {
			if (isset($choices[$id])) {
				$this->_field->children->removeElement($choices[$id]);
				$this->_em->remove($choices[$id]);
				unset($choices[$id]);
				foreach ($choices as $cid => $c) {
					if ($c->getOption('parent_id') == $id) {
						$this->_field->children->removeElement($choices[$cid]);
						$this->_em->remove($choices[$cid]);
						unset($choices[$cid]);
					}
				}
			}
		}

		if ($this->default_option) {
			if (isset($choices[$this->default_option])) {
				$this->_field->default_value = $this->default_option;
			} elseif (isset($new_id_map[$this->default_option])) {
				$this->_field->default_value = $new_id_map[$this->default_option];
			} else {
				$this->_field->default_value = null;
			}

			$this->_em->persist($this->_field);
		}

		$this->_em->flush();
	}
}
