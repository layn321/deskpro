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
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\TicketFilter;
use Symfony\Component\DependencyInjection\ContainerAware;
use Orb\Util\Util;

/**
 * @deprecated Use the FieldFanager with the field manager service
 */
abstract class AbstractFields
{
	const ENTITY_CLASS = 'Application\\DeskPRO\\Entity\\CustomDefAbstract';
	const ENTITY_NAME  = 'DeskPRO:CustomDefAbstract';

	/**
	 * Get a collection of all defined person fields
	 *
	 * @return array
	 */
	public function getFields()
	{
		$fields = App::getOrm()->getRepository(static::ENTITY_NAME)->getTopFields();

		return $fields;
	}

	public function getEnabledFields()
	{
		return App::getOrm()->getRepository(static::ENTITY_NAME)->getEnabledTopFields();
	}

	public function getFieldFromId($field_id)
	{
		$field = App::getOrm()->getRepository(static::ENTITY_NAME)->find($field_id);

		return $field;
	}

	public function getFieldsDisplayArray($field_defs, $data_structured = array(), $field_group = null)
	{
		$custom_fields = array();
		$has_value = false;
		foreach ($field_defs as $f_def) {
			$default_value = $f_def['default_value'];
			if ($f_def->getTypeName() == 'hidden') {
				if ($f_def->getOption('cookie_name') && !empty($_COOKIE[$f_def->getOption('cookie_name')])) {
					$default_value = $_COOKIE[$f_def->getOption('cookie_name')];
				} elseif ($f_def->getOption('param_name') && !empty($_REQUEST[$f_def->getOption('param_name')])) {
					$default_value = $_REQUEST[$f_def->getOption('param_name')];
				}
			}

			$value = !empty($data_structured[$f_def['id']]) && $data_structured[$f_def['id']]['value'] !== null ? $data_structured[$f_def['id']] : array('value' => $default_value);

			$f = $f_def->getHandler()->getFormField($value);

			$name = 'field_' . $f_def['id'];

			if ($field_group) {
				$field_group->add($f);
				$form = $field_group->getForm();
				$formView = $form->createView();
				$formView = $formView[$name];
			} else {
				$form = $f->getForm();
				$formView = $form->createView();
			}

			$rendered = $value ? $f_def->getHandler()->renderHtml($value) : null;
			if ($rendered) $has_value = true;

			$custom_fields[$f_def['id']] = array(
				'elId'            => Util::requestUniqueIdString(),
				'id'              => $f_def['id'],
				'name'            => 'field_' . $f_def['id'],
				'handler'         => $f_def->getHandler(),
				'field_def'       => $f_def,
				'title'           => $f_def['title'],
				'form'            => $form,
				'formView'        => $formView,
				'value'           => $value,
				'field_handler'   => strtolower(Util::getBaseClassname($f_def->getHandler())),
			);
		}

		return $custom_fields;
	}


	public function getEntityName()
	{
		return static::ENTITY_NAME;
	}

	public function getEntityClassname()
	{
		return static::ENTITY_CLASS;
	}
}
