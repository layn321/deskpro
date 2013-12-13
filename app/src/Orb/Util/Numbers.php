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

/**
 * Utility functions that work with numbers.
 *
 * @static
 */
class Numbers
{
	const ROUND_MULTIPLE_NEAR = 1;
	const ROUND_MULTIPLE_UP   = 2;
	const ROUND_MULTIPLE_DOWN = 3;



	/**
	 * Check if a value is an integer value. This is like is_int() but also passes
	 * strings that are integer form.
	 *
	 * You can't use ctype_digit or is_numeric because they'll pass other numeric
	 * forms, not just integers.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static function isInteger($value)
	{
		if (!is_scalar($value) OR is_array($value)) {
			return false;
		}

		if (is_int($value) OR ((string)((int)$value)) == (string)$value) {
			return true;
		}

		return false;
	}


	/**
	 * Take a number, and force it to be within the range of $min and $max.
	 * This will make it $min if it's smaller than $min, and $max if it's larger
	 * than $max.
	 *
	 * @param int $num The number to work with
	 * @param int $min The minumum integer
	 * @param int $max The maximum integer
	 * @return int
	 */
	public static function bound($num, $min, $max)
	{
		if ($num < $min) $num = $min;
		if ($num > $max) $num = $max;
		return $num;
	}


	/**
	 * Check if something is somewhere within the range of two numbers.
	 *
	 * @param    int    $what   The thing to check
	 * @param    int    $min    The minimum number (inclusive)
	 * @param    int    $max    The maximum number (inclusive)
	 * @return   bool
	 */
	public static function inRange($what, $min = 0, $max = 10)
	{
	    if ($what >= $min AND $what <= $max) {
	        return true;
	    }

	    return false;
	}



	/**
	 * Turn a number into roman numerals.
	 *
	 * <code>
	 * echo Orb_Num::romanNumerals(42); // XLII
	 * </code>
	 *
	 * @param  int  $num  The number to romanize
	 * @return string
	 */
	public static function romanNumerals($num)
	{
		static $map = array(
			'M' => 1000,
			'CM' => 900,
			'D' => 500,
			'CD' => 400,
			'C' => 100,
			'XC' => 90,
			'L' => 50,
			'XL' => 40,
			'X' => 10,
			'IX' => 9,
			'V' => 5,
			'IV' => 4,
			'I' => 1,
		);

		$num = intval($num);
		$res = '';

		foreach ($map as $roman => $value) {
			$res .= str_repeat($roman, (int)$num/$value);
			$num %= $value;
		}

		return $res;
	}



	/**
	 * Display a filesize in bytes in the smallest unit.
	 *
	 * @param int $bytes
	 * @param string $mode 'auto' or 'si' (base 10) or 'iec' (base 2). Auto will try to detect the 'cleanest' number
	 * @return string
	 */
	public static function filesizeDisplay($bytes, $mode = 'auto')
	{
		if ($mode == 'auto') {
			$parts = self::getFilesizeDisplayParts($bytes, 'si');
			$parts['number'] = sprintf('%.2f', $parts['number']);
			if (!strpos($parts['number'], '.00')) {
				$parts = self::getFilesizeDisplayParts($bytes, 'iec');
			}
		} else {
			$parts = self::getFilesizeDisplayParts($bytes, $mode);
		}

		return sprintf('%.2f %s', $parts['number'], $parts['symbol']);
	}



	/**
	 * From a filesize in bytes return an array of the largest unit symbol
	 * and its size. If you want a string, use filesizeDisplay().
	 *
	 * @param  $bytes
	 * @return array
	 */
	public static function getFilesizeDisplayParts($bytes, $mode = 'si')
	{
		if (!$bytes OR $bytes < 1) {
			return array('number' => 0, 'symbol' => 'B');
	    }

		$x = $mode == 'si' ? 1000 : 1024;

	    $all_symbols = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $exp = floor(log($bytes)/log($x));
        $val = $bytes/pow($x, floor($exp));

        $sym = '';
        if (isset($all_symbols[$exp])) {
            $sym = $all_symbols[$exp];
        }

		return array(
			'number' => $val,
			'symbol' => $sym
		);

	}



	/**
	 * Round a number to the nearest multiple.
	 *
	 * <code>
	 * echo Orb_Num::roundToMultiple(26, 5); // 25
	 * echo Orb_Num::roundToMultiple(26, 5, Orb_Num::ROUND_MULTIPLE_UP); // 30
	 * echo Orb_Num::roundToMultiple(29, 5, Orb_Num::ROUND_MULTIPLE_DOWN); // 25
	 * </code>
	 *
	 * @param  int|float  $number     The number to round
	 * @param  int        $multiple   The multiple to round to
	 * @param  int        $mode       Rounding mode to use
	 * @return mixed
	 */
	public static function roundToMultiple($number, $multiple, $mode = self::ROUND_MULTIPLE_NEAREST)
	{
		if ($mode == self::ROUND_MULTIPLE_NEAR) {
			return round($number / $multiple) * $multiple;
		} elseif ($mode == self::ROUND_MULTIPLE_DOWN) {
			return floor(floor($number) / $multiple) * $multiple;
		} else {
			return ceil(ceil($number) / $multiple) * $multiple;
		}
	}



	/**
	 * Get an array of pageinfo useful for building up paginination in templates.
	 * You get an array with:
	 * - prev: int of previous page, or false if no prev
	 * - next: int of next page, or false if no next
	 * - first: int of first page (1, obviously)
	 * - last: int of last page
	 * - pages: range() of page numbers useful in a loop
	 * - curpage: The current page
	 *
	 *
	 * @param int $num_results   The total number of results
	 * @param int $page          The current page you're on
	 * @param int $per_page      How many results per page
	 * @param int $pad           How many page numbers around the current to show
	 * @return array
	 */
	public static function getPaginationPages($num_results, $page, $per_page, $pad = 5)
	{
		$info = array();

		$num_pages = ceil($num_results / $per_page);
		if (!$num_pages) $num_pages = 1;

		$range_start = max(1, $page - floor(($pad-1) / 2));
		$range_end = max(min($num_pages, $page + floor(($pad-1) / 2)), $pad);

		if ($range_end > $num_pages) {
			$range_end = $num_pages;
		}

		$info['per_page'] = $per_page;
		$info['pages'] = range($range_start, $range_end);
		$info['prev'] = ($page != 1) ? $page-1 : false;
		$info['next'] = ($page < $num_pages) ? $page+1 : false;
		$info['first'] = 1;
		$info['last'] = $num_pages;
		$info['curpage'] = $page;
		$info['total_results'] = $num_results;
		$info['first_result'] = (($page-1) * $per_page) + 1;
		$info['last_result'] = (($page-1) * $per_page) + $per_page;

		$info['curpage'] = self::bound($info['curpage'], 1, $info['last']);

		$info['cursor'] = $page;
		$info['limit'] = $per_page;

		return $info;
	}


	/**
	 * Parses a filesize where the size may be expressed in php.ini shorthand notation with suffixes K, M or G.
	 * The returned size is in bytes.
	 *
	 * @param $size_string
	 * @return int
	 */
	public static function parseIniSize($val)
	{
		$val = trim($val);
		$last = strtoupper($val[strlen($val)-1]);

		// Already in bytes
		if (ctype_digit($last)) {
			return (int)$val;
		}

		$val = (int)$val;

		if ($last != 'G' && $last != 'M' && $last != 'K') {
			throw new \InvalidArgumentException("Invalid size string `$val`");
		}

		switch($last) {
			case 'G':
				$val *= 1024;
			case 'M':
				$val *= 1024;
			case 'K':
				$val *= 1024;
		}

		return $val;
	}


	/**
	 * @deprcated use Colors instead
	 */
	public static function hex2rgb($hex)
	{
		return Colors::hex2rgb($hex);
	}


	/**
	 * Get the ordinal suffix for a number
	 *
	 * @param $number
	 */
	public static function ordinalSuffix($number)
	{
		if (!$number) {
			return '';
		}

		if ($number % 100 > 10 && $number % 100 < 14) {
			$suffix = 'th';
		} else {
			switch(substr($number, -1, 1)) {
				case '1': $suffix = 'st'; break;
				case '2': $suffix = 'nd'; break;
				case '3': $suffix = 'rd'; break;
				default:  $suffix = 'th';
			}
    	}

		return $suffix;
	}


	/**
	 * True if input looks like a unix timestamp.
	 * "Looks like" means a positive integer that is no longer than 10 chars.
	 *
	 * @param string $input
	 * @return bool
	 */
	public static function isTimestamp($input)
	{
		return self::isInteger($input) && strlen($input) <= 10 && ctype_digit($input);
	}
}
