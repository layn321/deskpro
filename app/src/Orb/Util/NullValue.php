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

use Orb\Util\Numbers;

/**
 * A special object representing no value.
 * Used in situations where 'null' might be an acceptable value. E.g., default return value in Arrays::keyAsPath().
 *
 * @static
 */
class NullValue
{
	private function __construct() {}

	/**
	 * @return NullValue
	 */
	public static function get()
	{
		static $inst = null;

		if ($inst === null) {
			$inst = new self();
		}

		return $inst;
	}


	/**
	 * Check if a variable is a NullValue
	 *
	 * @param mixed $var
	 * @return bool
	 */
	public static function is($var)
	{
		return $var === self::get();
	}
}