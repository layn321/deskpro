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
 * @category Input
 */

namespace Orb\Input\Cleaner\CleanerPlugin;

use Orb\Input\Cleaner\Cleaner;
use Orb\Util\Strings;

/**
 * A cleaner plugin registeres typenames and callbacks
 */
class Basic implements CleanerPlugin
{
	protected $use_utf_funcs = false;

	public function getCleanerId()
	{
		return 'basic';
	}

	public function enableUtfHandling()
	{
		$this->use_utf_funcs = true;
	}

	public function getCleanerTypes()
	{
		return array(
			'discard'      ,
			'raw'          ,
			'bool'         ,
			'boolean'      ,
			'bool_int'     ,
			'ibool'        ,
			'int'          ,
			'integer'      ,
			'uint'         ,
			'float'        ,
			'ufloat'       ,
			'num'          ,
			'number'       ,
			'unum'         ,
			'str'          ,
			'string'       ,
			'str_notrim'   ,
			'str_nohtml'   ,
			'nohtml'       ,
			'str_striphtml',
			'striphtml'    ,
			'str_simple'   ,
			'simplestr'    ,
			'str_key'      ,
			'str_raw'      ,
			'rawstr'       ,
			'rawstring'    ,
			'array'        ,
		);
	}

	/**
	 * Clean a value.
	 *
	 * @param   mixed  $value    The value to clean
	 * @param   int    $type     The type to cast to
	 * @param   mixed  $options  Options for the type
	 * @return  mixed  The cleaned value
	 */
	public function cleanValue($value, $type, array $options, Cleaner $cleaner)
	{
		#----------------------------------------
		# Do the cleaning
		#----------------------------------------

		switch ($type) {
			case 'bool':
				$value = (bool)$value;
				break;

			case 'bool_int':
			case 'ibool':
				$value = (int)((bool)$value);
				break;

			case 'int':
			case 'integer':
				$value = (int)$value;
				break;

			case 'uint':
				$value = (int)$value;

				if ($value < 0) {
					$value = 0;
				}
				break;

			case 'num':
			case 'number':
				$value = ((string)$value) + 0;
				break;

			case 'unum':
				$value = ((string)$value) + 0;

				if ($value < 0) {
					$value = 0;
				}
				break;

			case 'float':
				$value = (float)$value;
				break;

			case 'ufloat':
				$value = (float)$value;
				if ($value < 0) {
					$value = 0.0;
				}
				break;

			case 'str':
			case 'string':
				if (!is_scalar($value)) $value = '';
				$value = trim($this->cleanString($value));
				break;

			case 'str_notrim':
				if (!is_scalar($value)) $value = '';
				$value = (string)$this->cleanString($value);
				break;

			case 'str_nohtml':
			case 'nohtml':
				if (!is_scalar($value)) $value = '';
				$value = htmlspecialchars(trim($this->cleanString($value)));
				break;

			case 'str_striphtml':
			case 'striphtml':
				if (!is_scalar($value)) $value = '';
				$value = strip_tags(trim($this->cleanString($value)));
				break;

			case 'str_simple':
			case 'str_key':
				if (!is_scalar($value)) $value = '';
				$value = preg_replace('#[^a-zA-Z0-9 _\-\.:]#', '', trim($this->cleanString($value)));
				break;

			case 'str_raw':
				if (!is_scalar($value)) $value = '';
				$value = (string)$value;
				break;

			case 'array':
				$value = (array)$value;
				break;
		}

		return $value;
	}


	/**
	 * If a string has mb characters, this will ensure the string is well-formed and
	 * fix it if it's not (most secure thing to do).
	 *
	 * This uses phputf8 from sourceforge through the Strings util class which acts
	 * as the loader.
	 *
	 * @param string|array $string The string to work on, or an array to go through
	 * @return string
	 */
	public function cleanString($string)
	{
		#-------------------------
		# Recursively clean arrays
		#-------------------------

		if (is_array($string)) {
			foreach ($string as $k => $v) {
				$k = $this->cleanString($k);
				$v = $this->cleanString($v);

				$string[$k] = $v;
			}

			return $string;
		}


		#-------------------------
		# Clean normal strings
		#-------------------------

		if (!is_string($string)) {
			return $string;
		}

		$string = Strings::removeInvisibleCharacters($string);

		if ($this->use_utf_funcs) {
			$string = Strings::utf8_bad_strip($string);
		}

		return $string;
	}
}
