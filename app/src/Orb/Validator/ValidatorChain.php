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

use \Orb\Util\Util;

/**
 * A validator composite.
 */
class ValidatorChain extends AbstractValidator
{
	/**
	 * Array of validators to run
	 * @var array
	 */
	protected $validators = array();


	/**
	 * Add a validator to the chain.
	 *
	 * @param  \Orb\Validator\AbstractValidator  $validator         The validator to add
	 * @param  bool                              $break_on_invalid  If the validator says the value is invalid, break the chain (stop executing further ones)
	 */
	public function addValidator(\Orb\Validator\AbstractValidator $validator, $break_on_invalid = false)
	{
		$this->validators[] = array(
			$validator,
			(bool)$break_on_invalid
		);
	}

	

	/**
	 * Get an array of currently set validators
	 *
	 * @return array
	 */
	public function getValidators()
	{
		return $this->validators;
	}
	


	/**
	 * Check $value to see if its valid.
	 *
	 * @return bool
	 */
	protected function checkIsValid($value)
	{
		if (!$this->validators) {
			return true;
		}

		foreach ($this->validators as $x) {
			$validator = $x[0];
			$break_on_invalid = $x[1];

			if (!$validator->isValid($value)) {

				$this->errors      = array_merge($this->errors, $validator->getErrors());
				$this->errors_info = array_merge($this->errors_info, $validator->getErrorsInfo());

				if ($break_on_invalid) {
					break;
				}
			}
		}

		if (!$this->errors) {
			return true;
		}

		return false;
	}
}
