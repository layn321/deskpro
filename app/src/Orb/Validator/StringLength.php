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

use Orb\Util\Strings;

class StringLength extends AbstractValidator
{
	protected $min;
	protected $max;

	public function init()
	{
		$this->min = $this->getOption('min', -1);
		$this->max = $this->getOption('max', -1);
	}

	/**
	 * Check $value to see if its valid.
	 *
	 * @return bool
	 */
	protected function checkIsValid($value)
	{
		$value = trim($value);

		if ($this->min != -1) {
			if (Strings::utf8_strlen($value) < $this->min) {
				$this->addError('length_too_short');
				return false;
			}
		}

		if ($this->max != -1) {
			if (Strings::utf8_strlen($value) > $this->max) {
				$this->addError('length_too_long');
				return false;
			}
		}

		return true;
	}
}
