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
 * Orb
 *
 * @package Orb
 * @category Util
 */

namespace Orb\Util;

use Orb\Validator\ValidatorInterface;

/**
 * Like a normal options array except that we run validations
 */
class CheckedOptionsArray extends OptionsArray
{
	/**
	 * Array if name=>array(ValidatorInterface)
	 *
	 * @var array
	 */
	protected $validators = array();

	public function ensureRequired(array $required_names)
	{
		$diff = array_diff($required_names, array_keys($this->options));
		if ($diff) {
			throw new CheckedOptionsException("Missing required options: " . implode(', ', $diff), array('required'), array('names' => $diff));
		}

		if ($this->validators) {
			foreach ($this->options as $name => $value) {
				if (isset($this->validators[$name])) {
					foreach ($this->validators[$name] as $validator) {
						if (!$validator->isValid($value)) {
							throw new CheckedOptionsException("`$name` has an invalid option value", $validator->getErrors(), $validator->getErrorsInfo());
						}
					}
				}
			}
		}
	}

	public function addCheckedOption($name, ValidatorInterface $validator)
	{
		if (!isset($this->validators[$name])) {
			$this->validators[$name] = array();
		}

		$this->validators[$name][] = $validator;
	}

	public function addTypeCheckedOption($name, $type, $allow_null = false)
	{
		$fn = function($val) use ($name, $type, $allow_null) {
			if ($val === null && $allow_null) {
				return;
			}

			if (!is_object($val)) {
				return array(array('null_value', array('expected_type' => $type)));
			}
			if (get_class($val) != $type) {
				return array(array('invalid_type', array('expected_type' => $type, 'got_type' => get_class($val))));
			}
		};

		$validator = new \Orb\Validator\Callback(array('callback_function' => $fn));

		$this->addCheckedOption($name, $validator);
	}

	public function set($name, $value)
	{
		if (isset($this->validators[$name])) {
			foreach ($this->validators[$name] as $validator) {
				if (!$validator->isValid($value)) {
					throw new CheckedOptionsException("`$name` has an invalid option value", $validator->getErrors(), $validator->getErrorsInfo());
				}
			}
		}

		parent::set($name, $value);
	}
}
