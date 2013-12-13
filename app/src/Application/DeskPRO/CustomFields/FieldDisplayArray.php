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

class FieldDisplayArray implements \ArrayAccess
{
	/**
	 * @var FieldManager
	 */
	protected $field_manager;

	/**
	 * @var \Application\DeskPRO\Entity\CustomDefAbstract
	 */
	protected $field_def;

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var string|null
	 */
	protected $field_group = null;

	/**
	 * @var bool
	 */
	protected $use_default = false;

	public function __construct(FieldManager $field_manager, CustomDefAbstract $field_def, $field_data = array(), $field_group = null, $use_default = false)
	{
		$this->field_manager = $field_manager;
		$this->field_def     = $field_def;
		$this->field_group   = $field_group;
		$this->use_default   = $use_default;

		$value = !empty($field_data[$field_def['id']]) && $field_data[$field_def['id']] !== 0 && $field_data[$field_def['id']] !== '0' ? $field_data[$field_def['id']] : null;

		$default_value = $field_def->default_value;
		if ($field_def->getTypeName() == 'hidden') {
			if ($field_def->getOption('cookie_name') && !empty($_COOKIE[$field_def->getOption('cookie_name')])) {
				$default_value = $_COOKIE[$field_def->getOption('cookie_name')];
			} elseif ($field_def->getOption('param_name') && !empty($_REQUEST[$field_def->getOption('param_name')])) {
				$default_value = $_REQUEST[$field_def->getOption('param_name')];
			}
		}

		if ($value === null && $use_default && $default_value) {
			if ($field_def['handler_class'] == 'Application\\DeskPRO\\CustomFields\\Handler\\Choice') {
				$value = array('children' => array($default_value => array('value' => 1)));
			} else {
				$value = array('value' => $default_value);
			}

		}
		if (!$field_def->isFormField()) {
			$value = array();
		}

		$this->data = array(
			'elId'            => \Orb\Util\Util::requestUniqueIdString(),
			'hasValue'        => ($value !== null),
			'id'              => $field_def->getId(),
			'name'            => 'field_' . $field_def->getId(),
			'title'           => $field_def->getTitle(),
			'value'           => $value,
			'field_handler'   => strtolower(\Orb\Util\Util::getBaseClassname($field_def->getHandler())),
		);
	}

	public function initValue($offset)
	{
		switch ($offset) {
			case 'field_def':
				$this->data['field_def'] = $this->field_def;
				break;

			case 'handler':
				$this->data['handler'] = $this->field_def->getHandler();
				break;

			case 'form':
			case 'formView':
				$field_group = $this->field_group;
				if (!$field_group) {
					$field_group = App::get('form.factory')->createNamedBuilder('form', 'custom_fields');
				}

				$f = $this->field_def->getHandler()->getFormField($this->data['value']);

				if ($field_group) {
					if (!$field_group->has($this->data['name'])) {
						$field_group->add($f);
					}

					$form = $field_group->getForm();
					$formView = $form->createView();
					$formView = $formView[$this->data['name']];
				} else {
					$form = $f->getForm();
					$formView = $form->createView();
				}

				$this->data['form']     = $form;
				$this->data['formView'] = $formView;
				break;
		}
	}

	public function offsetExists($offset)
	{
		if (!isset($this->data[$offset])) {
			$this->initValue($offset);
		}

		return isset($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		if (!isset($this->data[$offset])) {
			$this->initValue($offset);
		}

		return $this->data[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}

	public function mergeArray(array $array)
	{
		array_merge($this->data, $array);
	}

	public function toArray()
	{
		return $this->data;
	}
}