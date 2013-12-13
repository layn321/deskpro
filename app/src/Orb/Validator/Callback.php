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
 * @subpackage Validator
 */

namespace Orb\Validator;

/**
 * A validator that calls a callback function
 */
class Callback extends AbstractValidator
{
	/**
	 * Special value to represent the argument we'll place the arg in
	 */
	const ARG_PLACEHOLDER = '__ORB_VALIDATOR_CALLBACK_ARG_PLACEHOLDER__';

	/**
	 * The callback to call.
	 * @var mixed
	 */
	protected $callback_fn;

	/**
	 * Arguments to pass to the callback
	 * @var array
	 */
	protected $callback_args = array();

	/**
	 * The position where the value in args should be set
	 * @var int
	 */
	protected $callback_value_arg_pos = 0;



	/**
	 * $callback_args should contain ARG_PLACEHOLDER in the array where the value
	 * should be called. If ARG_PLACEHOLDER is not found, then the value will always
	 * be the first argument.
	 *
	 * The callback must return an error code, or an array of error codes, upon error.
	 * Return false if no errors.
	 *
	 * @param  mixed  $callback_fn    The callback to call
	 * @param  array  $callback_args  The arguments to pass to the callback
	 */
	public function init()
	{
		$callback_fn = $this->getOption('callback_function');
		$callback_args = $this->getOption('callback_args', array());

		$this->callback_fn = $callback_fn;

		// The position of the value in the callback must be defined by using the special
		// placeholder. If it's not found, it'll be the first value
		$pos = array_search(self::ARG_PLACEHOLDER, $callback_args, true);
		if (!$pos) {
			$pos = 0;
			array_unshift($callback_args, self::ARG_PLACEHOLDER);
		}

		$this->callback_args = $callback_args;
		$this->callback_value_arg_pos = $pos;
	}



	/**
	 * Check $value to see if its valid.
	 *
	 * @return bool
	 */
	protected function checkIsValid($value)
	{
		$args = $this->callback_args;
		$args[$this->callback_value_arg_pos] = $value;

		$errors = call_user_func_array($this->callback_fn, $args);

		if ($errors) {
			if (!is_array($errors)) $errors = array($errors);

			foreach ($errors as $info) {
				if (is_array($info)) {
					$this->addError($info[0], $info[1]);
				} else {
					$this->addError($info);
				}
			}
			return true;
		}

		return false;
	}
}
