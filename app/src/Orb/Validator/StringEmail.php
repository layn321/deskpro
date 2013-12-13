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

class StringEmail extends AbstractValidator implements StaticValidator
{
	/**
	 * @param $value
	 * @return bool
	 */
	public static function isValueValid($value)
	{
		$validator = new self();
		return $validator->isValid($value);
	}


	/**
	 * Check $value to see if its valid.
	 *
	 * @return bool
	 */
	protected function checkIsValid($value)
	{
		if (strpos($value, '@') === false) {
			$this->addError('bad_email_format');
			return false;
		}

		list($name, $domain) = explode('@', $value, 2);
		$name   = trim($name);
		$domain = trim($domain);

		if ($name === "" || !$domain) {
			$this->addError('empty_email');
			return false;
		}

		// Match the part before the @
		$regex_name = '#^[a-z0-9!\\#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!\\#$%&\'*+/=?^_`{|}~-]+)*$#i';

		// Match a regular domain name after the @
		$regex_domain = '#^(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2,6})$#i';

		// Match a IP address after the @
		$regex_ip = '#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#i';

		if (!preg_match($regex_name, $name)) {
			$this->addError('bad_email_name');
			return false;
		}

		if (!preg_match($regex_domain, $domain) AND !preg_match($regex_ip, $domain)) {
			$this->addError('bad_email_domain');
			return false;
		}

		return true;
	}
}
