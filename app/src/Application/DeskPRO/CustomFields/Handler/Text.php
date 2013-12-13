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
 * @subpackage Form
 */

namespace Application\DeskPRO\CustomFields\Handler;

use Application\DeskPRO\Entity;
use Application\DeskPRO\App;
use Orb\Util\Strings;

/**
 * Handles the text field
 */
class Text extends HandlerAbstract
{
	public function getFormField($data = null)
	{
		$setData = null;
		if ($data AND (!empty($data['value']) || (isset($data['value']) && ($data['value'] === '0' || $data['value'] === 0)))) {
			$setData = $data['value'];
		}

		$field = App::getFormFactory()->createNamedBuilder('text', $this->getFormFieldName(), $setData, array('required' => false));

		return $field;
	}

	function getDataFromForm(array $form_data)
	{
		$name = $this->getFormFieldName();

		$value = null;
		if (!empty($form_data[$name]) || (isset($form_data[$name]) && $form_data[$name] === '0')) {
			$value = $form_data[$name];
		}
		if (is_array($value)) {
			$value = implode(' ', $value);
		}

		return array(
			array($this->field_def['id'], 'input', $value)
		);
	}

	public function validateFormData(array $form_data, $context = self::CONTEXT_USER, $context_data = null)
	{
		$data = isset($form_data[$this->getFormFieldName()]) ? $form_data[$this->getFormFieldName()] : '';

		if (!is_scalar($data)) {
			return $this->makeErrorArray(array('invalid_input'));
		}

		#------------------------------
		# Validate options
		#------------------------------

		$opt_prefix = '';
		if ($context == self::CONTEXT_AGENT) {
			$opt_prefix = 'agent_';
		}

		$options = array();
		foreach (array('required', 'min_length', 'max_length', 'regex') as $k) {
			$options[$k] = $this->field_def->getOption($opt_prefix . $k);
		}

		if ($options['required']) {
			$len = Strings::utf8_strlen($data);

			if ($options['min_length'] && $len < $options['min_length']) {
				if ($options['min_length'] == 1) {
					return $this->makeErrorArray(array('required'));
				} else {
					return $this->makeErrorArray(array('min_length'));
				}
			}

			if ($options['max_length'] && $len > $options['max_length']) {
				return $this->makeErrorArray(array('max_length'));
			}
		}

		if ($options['regex']) {
			$regex = Strings::getInputRegexPattern($options['regex']);
			if ($regex && !preg_match($regex, $data)) {
				return $this->makeErrorArray(array('regex_fail'));
			}
		}

		return array();
	}

	public function getSearchCapabilities()
	{
		return array('is', 'not', 'contains', 'notcontains');
	}

	public function getFilterCapabilities()
	{
		return array('is', 'not');
	}

	public function getSearchType()
	{
		return 'input';
	}
}
