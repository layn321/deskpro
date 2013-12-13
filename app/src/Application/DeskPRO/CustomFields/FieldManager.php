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
*/

namespace Application\DeskPRO\CustomFields;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\CustomDefAbstract;
use Doctrine\ORM\EntityManager;

/**
 * The custom field manager handles fetching custom fields, rendering them
 * and saving them.
 *
 * == Terms ==
 * - `field` or `field_def` is a field definition (CustomDefAbstract).
 *   A field can have children (such as a select box).
 * - `object` is the object that a field is attached to (Ticket, Person, Organization)
 * - `custom_data` is a flat array on an object that stores the data for a field (CustomDataAbstract). It's flat
 *   because Doctrine/database doesn't care about hierarchy.
 * - `field_data` is a re-structured array based off of `custom_data` that has the proper hierarchy defined by the `field_def`s
 * - `display_array` takes a `field_def` and `field_data` to produce an array that has data that can be rendered in a template as
 *   a value or a form.
 */
class FieldManager
{
	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Orb\Util\OptionsArray
	 */
	protected $options;

	/**
	 * Array of top-level fields for the current interface
	 * @var array
	 */
	protected $fields = null;

	/**
	 * Array of top-level fields
	 * @var array
	 */
	protected $real_fields = null;

	/**
	 * @var array
	 */
	protected $field_to_children = array();

	/**
	 * Array of all fields for the current interface.
	 * E.g., if this is the user interface, then agent-only fields aren't included here.
	 *
	 * @var array
	 */
	protected $all_fields = null;

	/**
	 * Array of all enabled fields, including ones disabled for the current interface.
	 *
	 * @var array
	 */
	protected $real_all_fields = null;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em, array $options)
	{
		$this->em = $em;
		$this->db = $em->getConnection();

		$this->options = new \Orb\Util\CheckedOptionsArray($options);
		$this->options->ensureRequired(array(
			'entity_class',
			'entity_name',
			'data_entity_name',
			'data_entity_class',
		));

		$this->options->setArrayDefault(array(
			'custom_data_property' => 'custom_data'
		));
	}

	/**
	 * Get a collection of all top-level (parent) fields
	 *
	 * @return array
	 */
	public function getFields()
	{
		if ($this->fields === null) {
			$this->fields          = array();
			$this->all_fields      = array();
			$this->real_fields     = array();
			$this->real_all_fields = array();

			if ($this->options->get('disabled')) {
				return $this->fields;
			}

			$all_fields = $this->em->getRepository($this->options->get('entity_name'))->getEnabledFields();

			foreach ($all_fields as $f) {

				$this->real_all_fields[$f->getId()] = $f;

				if (!$f->getParentId()) {
					$this->real_fields[$f->getId()] = $f;
				}

				if (defined('DP_INTERFACE') && DP_INTERFACE == 'user') {
					if (!$f->is_agent_field && $f->is_user_enabled) {
						$this->all_fields[$f->getId()] = $f;
						if (!$f->getParentId()) {
							$this->fields[$f->getId()] = $f;
						}
					}
				} else {
					$this->all_fields[$f->getId()] = $f;
					if (!$f->getParentId()) {
						$this->fields[$f->getId()] = $f;
					}
				}

				if ($p = $f->getParentId()) {
					if (!isset($this->field_to_children[$p])) {
						$this->field_to_children[$p] = array();
					}
					$this->field_to_children[$p][$f->getId()] = $f;
				}
			}

			// Choice fields that have no options are considered disabled
			foreach ($this->fields as $f) {
				if ($f->isChoiceType()) {
					if (!$this->getFieldChildren($f)) {
						unset(
							$this->all_fields[$f->getId()],
							$this->real_all_fields[$f->getId()],
							$this->fields[$f->getId()],
							$this->real_fields[$f->getId()],
							$this->field_to_children[$f->getId()]
						);
					}
				}
			}
		}

		return $this->fields;
	}


	/**
	 * Get all defined fields, even ones that are not enabled for the current interface.
	 *
	 * @return array
	 */
	public function getDefinedFields()
	{
		$this->getFields();
		return $this->real_fields;
	}


	/**
	 * Get a named system field
	 *
	 * @param string $sys_name
	 * @return mixed
	 */
	public function getSystemField($sys_name)
	{
		foreach ($this->getFields() as $field) {
			if (isset($field['sys_name']) && $field['sys_name'] == $sys_name) {
				return $field;
			}
		}

		return null;
	}


	/**
	 * @param $field_def
	 * @return array
	 */
	public function getFieldChildren($field_def)
	{
		$this->getFields();
		if (!isset($this->field_to_children[$field_def->getId()])) {
			return array();
		}

		return $this->field_to_children[$field_def->getId()];
	}


	/**
	 * Get a field from an ID
	 *
	 * @param $field_id
	 * @return \Application\DeskPRO\Entity\CustomDefAbstract
	 */
	public function getFieldFromId($field_id)
	{
		$this->getFields();
		return isset($this->fields[$field_id]) ? $this->fields[$field_id] : null;
	}


	/**
	 * Get a display array for rendering a field
	 *
	 * @param array $field_data  An array of structured data from the database
	 * @param null $field_group       Optionally a form group to add form fields to
	 * @return array
	 */
	public function getDisplayArray($field_data = array(), $field_group = null, $use_default = false)
	{
		$custom_fields = array();
		foreach ($this->getFields() as $f_def) {
			$display = new FieldDisplayArray($this, $f_def, $field_data, $field_group, $use_default);
			$custom_fields[$f_def['id']] = $display;
		}

		return $custom_fields;
	}


	/**
	 * Recreates an array we'd get from a posted form based on the values already on an object.
	 * Useful when re-creating objects to pass through a validator.
	 *
	 * @param $object
	 * @return array
	 */
	public function createFormArrayForObject($object)
	{
		$field_data = $this->getFieldDataForObject($object);

		$form_data = array();

		foreach ($field_data as $field_id => $data) {
			$f = $this->getFieldFromId($field_id);

			switch ($f->getTypeName()) {
				case 'choice':
					if (!empty($data['children'])) {
						$form_data['field_' . $field_id] = array();
						foreach ($data['children'] as $k => $child) {
							$form_data['field_' . $field_id][] = $k;
						}
					}
					break;

				default:
					if (!empty($data['value']) || (isset($data['value']) && ($data['value'] === 0 || $data['value'] === '0'))) {
						$form_data['field_' . $field_id] = $data['value'];
					}
					break;
			}
		}

		return $form_data;
	}


	/**
	 * Render field data to their 'text values.
	 *
	 * @param array $field_data
	 * @return array
	 */
	public function getRenderedToText($field_data = array())
	{
		$custom_fields = array();
		foreach ($this->getFields() as $f_def) {
			$value = !empty($field_data[$f_def['id']]) && $field_data[$f_def['id']] !== 0 && $field_data[$f_def['id']] !== '0' ? $field_data[$f_def['id']] : null;

			$rendered = $value !== null ? $f_def->getHandler()->renderText($value) : null;
			if ($rendered) $has_value = true;

			$custom_fields[$f_def['id']] = array(
				'rendered'        => trim($rendered),
				'elId'            => \Orb\Util\Util::requestUniqueIdString(),
				'hasValue'        => ($value !== null),
				'id'              => $f_def['id'],
				'name'            => 'field_' . $f_def['id'],
				'handler'         => $f_def->getHandler(),
				'field_def'       => $f_def,
				'title'           => $f_def['title'],
				'value'           => $value,
				'field_handler'   => strtolower(\Orb\Util\Util::getBaseClassname($f_def->getHandler())),
			);
		}

		return $custom_fields;
	}


	/**
	 * Render field data form an object to their text values.
	 *
	 * @param $object
	 * @return array
	 */
	public function getRenderedToTextForObject($object)
	{
		$field_data = $this->getFieldDataForObject($object);
		return $this->getRenderedToText($field_data);
	}


	/**
	 * Create a field display array from an object
	 *
	 * @param $object
	 * @param null $field_group
	 * @return array
	 */
	public function getDisplayArrayForObject($object, $field_group = null)
	{
		$field_data = $this->getFieldDataForObject($object);

		// If the object has no id then it means it isnt perissted,
		// which means we should use the default value to show on a form somewhre
		$use_default = false;
		if (!$object->getId()) {
			$use_default = true;
		}

		return $this->getDisplayArray($field_data, $field_group, $use_default);
	}


	/**
	 * Create field display arrays for a collection of objects and their field object (raw values for their fields)
	 *
	 * $objects are the actual objects you want to process. For example $tickets of types Ticket
	 * $field_objects are all those records custom field objects. For example, $ticket_custom_data of types CustomDataTicket
	 *
	 * You get back an array of display arrays, the same as youd get from getDisplayArrayForObject()
	 *
	 * @param $object
	 * @param $field_group
	 * @return array
	 */
	public function getDisplayArraysForObjectCollection(array $objects, array $field_objects)
	{
		$data = array();

		foreach ($objects as $object) {
			if (!isset($field_objects[$object->getId()])) {
				continue;
			}

			$field_data = $this->createFieldDataFromArray($object, $field_objects[$object->getId()]);
			$data[$object->getId()] = $this->getDisplayArray($field_data, null);
		}

		return $data;
	}


	/**
	 * Take custom field data from an obejct and return a field data array.
	 *
	 * @param $object
	 * @return array
	 */
	public function getFieldDataForObject($object)
	{
		$prop = $this->options->get('custom_data_property');
		$data = $object->$prop;

		return $this->createFieldDataFromArray($data);
	}


	/**
	 * This converts a collection of data items into an array structure
	 * that matches the hierarchy of field definitions.
	 *
	 * Custom field values in the database are 'flat', and when displaying values
	 * we need to pass a proper structure to a field defition for rendering. This is
	 * easy for simple fields like text or textarea, but we need this method for
	 * complex fields that have multiple levels, like a choice.
	 *
	 * @param $field_datas
	 * @return array
	 */
	public function createFieldDataFromArray($field_datas)
	{
		// Create a map of keys
		$data_keys = array();
		foreach ($field_datas as $k => $v) {
			$data_keys[$v->field->getId()] = $k;
		}

		$data = $this->_createDataHierarchy($data_keys, $field_datas, $this->getFields());

		return $data;
	}

	protected function _createDataHierarchy($data_keys, $field_datas, $field_defs)
	{
		$structure = array();

		foreach ($field_defs as $def) {

			$item = array('value' => null, 'children' => null);

			if (isset($data_keys[$def['id']])) {
				$item['value'] = $field_datas[$data_keys[$def['id']]]->getData();
			}

			if (isset($this->field_to_children[$def->getId()])) {
				$item['children'] = $this->_createDataHierarchy($data_keys, $field_datas, $this->field_to_children[$def->getId()]);
			}

			if ($item['value'] || $item['children'] || $item['value'] === 0 || $item['value'] === '0') {
				$structure[$def['id']] = $item;
			}
		}

		return $structure;
	}


	/**
	 * Save a posted form of custom field data to an object
	 *
	 * @param array $form_data
	 * @param $object
	 * @return void
	 */
	public function saveFormToObject(array $form, $object, $only_set = false)
	{
		if ($object->getId()) {
			$this->em->beginTransaction();
		}

		try {

			$this->_orig_display = $this->getDisplayArrayForObject($object);

			// Remove whatever we have before
			// We'll just re-insert if its still there
			foreach ($this->getFields() as $field_def) {
				if ($only_set && !isset($form['field_' . $field_def->getId()])) {
					continue;
				}
				$this->removeCustomDataOnObject($object, $field_def);
			}
			if ($object->getId()) {
				$this->em->flush();
			}

			foreach ($this->getFields() as $field_def) {
				if ($only_set && !isset($form['field_' . $field_def->getId()])) {
					continue;
				}
				foreach ($field_def->getHandler()->getDataFromForm($form) as $info) {
					$this->setCustomDataOnObject($object, $field_def, $info);
				}
			}

			$this->_orig_display = null;

			if ($object->getId()) {
				$this->em->flush();
				$this->em->commit();
			}
		} catch (\Exception $e) {
			if ($object->getId()) {
				$this->em->rollback();
			}
			throw $e;
		}
	}


	/**
	 * Returns an array of data objects of type $data_class based on form input
	 *
	 * @return \Application\DeskPRO\Entity\CustomDataAbstract[]
	 */
	public function getStrucutredDataFromForm(array $form, $data_class)
	{
		$structured_data = array();

		foreach ($this->getFields() as $field_def) {
			foreach ($field_def->getHandler()->getDataFromForm($form) as $in_data) {
				list($set_field_id, $value_type, $value) = $in_data;

				// The field we're actually saving under
				// Usually the same as $field_def, but not always
				// Ex: Choice fields we save under the actual choice option
				$set_field = null;

				if ($field_def->getId() == $set_field_id) {
					$set_field = $field_def;
				} elseif (isset($this->field_to_children[$field_def->getId()])) {
					foreach ($this->field_to_children[$field_def->getId()] as $c) {
						if ($c->getId() == $set_field_id) {
							$set_field = $c;
							break;
						}
					}
				}

				// No value
				if ($value === null || $set_field === null) {
					continue;
				}

				$data = new $data_class();
				$data->field = $set_field;
				$data[$value_type] = $value;

				$structured_data[] = $data;
			}
		}

		return $structured_data;
	}


	/**
	 * @param $object
	 * @param \Application\DeskPRO\Entity\CustomDefAbstract $field_def
	 * @param array $in_data
	 * @return array|null
	 */
	public function setCustomDataOnObject($object, CustomDefAbstract $field_def, array $in_data)
	{
		list($set_field_id, $value_type, $value) = $in_data;

		// The field we're actually saving under
		// Usually the same as $field_def, but not always
		// Ex: Choice fields we save under the actual choice option
		$set_field = null;

		if ($field_def->getId() == $set_field_id) {
			$set_field = $field_def;
		} elseif (isset($this->field_to_children[$field_def->getId()])) {
			foreach ($this->field_to_children[$field_def->getId()] as $c) {
				if ($c->getId() == $set_field_id) {
					$set_field = $c;
					break;
				}
			}
		}

		// No value
		if ($value === null || $set_field === null) {
			return null;
		}

		if ($object->getId()) {
			$this->em->beginTransaction();
		}

		try {
			$custom_data = $this->createDataClass();
			$custom_data->field = $set_field;
			$custom_data->root_field = $field_def;
			$custom_data[$value_type] = $value;

			$object->addCustomData($custom_data);
			$this->em->persist($custom_data);

			if ($object->getId()) {
				$this->em->flush();
				$this->em->commit();
			}

		} catch (\Exception $e) {
			if ($object->getId()) {
				$this->em->rollback();
			}
			throw $e;
		}

		return $custom_data;
	}


	/**
	 * @param $object
	 * @param \Application\DeskPRO\Entity\CustomDefAbstract $field_def
	 * @return void
	 */
	public function removeCustomDataOnObject($object, CustomDefAbstract $field_def)
	{
		$prop = $this->options->get('custom_data_property');
		if ($field_def->getParentId()) {
			foreach ($object->$prop as $v) {
				if ($v->field->getId() == $field_def->getParentId()) {
					$this->em->remove($v);
					$object->$prop->removeElement($v);
				}
			}
		}

		foreach ($object->$prop as $v) {
			if ($v->field->getId() == $field_def->getId() || ($v->field->parent && $v->field->parent->getId() == $field_def->getId())) {
				$object->$prop->removeElement($v);
			}
		}
	}


	/**
	 * @return \Application\DeskPRO\Entity\CustomDataAbstract
	 */
	public function createDataClass()
	{
		$classname = $this->options->get('data_entity_class');
		return new $classname;
	}


	/**
	 * Adds custom field API data to an object along with rendered values.
	 *
	 * @param mixed $object
	 * @param array $data
	 */
	public function addApiData($object, array &$data)
	{
		if (!empty($data['custom_data'])) {
			foreach ($data['custom_data'] as &$_f) {
				if (!empty($_f['root_field']) && $_f['root_field']['id'] == $_f['id']) {
					unset($_f['root_field']);
				}
			}
			unset($_f);

			$values = $this->getRenderedToTextForObject($object);
			foreach ($values as $fid => $v) {
				$data["field{$fid}"] = $v['rendered'];

				foreach ($data['custom_data'] as &$_f) {
					if ($_f['id'] == $fid) {
						$_f['rendered_value'] = $v['rendered'];
					}
				}
				unset($_f);
			}
		}
	}
}
