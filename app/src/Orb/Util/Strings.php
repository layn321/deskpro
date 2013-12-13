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

use Orb\Util\DOMDocument;

/**
 * String utility functions.
 *
 * @static
 */
class Strings
{
	private function __construct() { /* No instances allowed */ }

	/**#@+
	 * Strings of some common character ranges.
	 * @see Strings::randomString()
	 */
	const CHARS_ALPHANUM     = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	const CHARS_ALPHANUM_I   = '0123456789abcdefghijklmnopqrstuvwxyz';
	const CHARS_ALPHANUM_IU  = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const CHARS_NUM          = '0123456789';
	const CHARS_ALPHA        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	const CHARS_ALPHA_I      = 'abcdefghijklmnopqrstuvwxyz';
	const CHARS_ALPHA_IU     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const CHARS_SECURE       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%^&*()-_=+{}|[]:;,./<>?';
	const CHARS_KEY          = '23456789ABCDGHJKMNPQRSTWXYZ';
	const CHARS_KEY_ALPHA    = 'ABCDGHJKMNPQRSTWXYZ';
	const CHARS_KEY_NUM      = '23456789';
	/**#@-*/


	/**#@+
	 * End of line characters.
	 * @see Strings::standardEol()
	 */
	const EOL_LF = "\n";
	const EOL_CRLF = "\r\n";
	const EOL_CR = "\r";
	/**#@-*/


	/**#@+
	 * Flags for use with the boundary function.
	 * @see Strings::getBetweenBoundary()
	 */
	const BOUNDARY_APPEND = 1;
	const BOUNDARY_ARRAY = 2;
	const BOUNDARY_FIRST = 3;
	/**#@-*/

	/**#@+
	 * Some helpful Unicode characters
	 */
	const ZERO_WIDTH_SPACE = "\xE2\x80\x8B";
	const SOFT_HYPHEN = "\xC2\xAD";
	/**#@-*/

	/**
	 * When a dupe key is encountered, overwrite the old key.
	 * @see see Strings::parseEqualsLines()
	 */
	const EQUALSLINES_DUPE_OVERWRITE = 1;


	/**
	 * When a dupe key is encountered, overwrite the old key.
	 * @see see Strings::parseEqualsLines()
	 */
	const EQUALSLINES_DUPE_ADD_ARRAY = 2;

	/**
	 * When set, we will autoload php-utf8 functions and catch
	 * dynamic calls to utf8_xxx functions.
	 *
	 * @var string
	 */
	protected static $php_utf8_dir = null;



	/**
	 * Add slashes to a string to be used within Javascript with quotes and newlines properly
	 * escaped.
	 *
	 * Example:
	 * <code>
	 * $str = 'She said, "Wow!"';
	 * echo '<script type="text/javascript">var js_string = "' . Strings::addslashesJs($str) . '";</script>';
	 * </code>
	 *
	 * @param    string    $string    The string to escape
	 * @return   string
	 */
	public static function addslashesJs($string)
	{
		$str = str_replace(array('\\', '\'', '"', "\n", "\r"), array('\\\\', "\'", '\\"', "\\n", "\\r"), trim($string));

		// Can't have </script> or else browsers will interpret that as
		// ending the script. \x3C is hex for the '<' char, so turn </script> into
		// \x3C/script>
		$str = preg_replace('#<(\s*/script\s*>)#i', '\\x3C\\1', $str);

		return $str;
	}



	/**
	 * Generate a random string.
	 *
	 * $chars is a string of possible characters. See the CHARS_* presets.
	 *
	 * If a falsy value is provided, then CHARS_ALPHANUM is used.
	 *
	 * @param integer $len
	 * @param string  $chars
	 */
	public static function random($len = 8, $chars = null)
	{
		if (!$chars) {
			$chars = self::CHARS_ALPHANUM;
		}

		$string = '';
		$max_range = strlen($chars) - 1;

		for ($i = 0; $i < $len; $i++) {
			$string .= $chars[mt_rand(0, $max_range)];
		}

		return $string;
	}



	/**
	 * Generate a random string made up of "pronouncable" bits. Examples:
	 * - bacrimo
	 * - drestaw
	 * - swuclew
	 *
	 * @param   int     $len The maximum length of the string
	 * @return  string
	 */
	public static function randomPronounceable($len = 10)
	{
		static $vowels, $cons, $num_vowels, $num_cons;

		if (!$vowels) {
			$vowels = array('a', 'e', 'i', 'o', 'u');
			$cons = array(
				'b', 'c', 'd', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr',
				'cr', 'br', 'fr', 'th', 'dr', 'ch', 'ph', 'wr', 'st', 'sp', 'sw', 'pr', 'sl', 'cl'
				);

				$num_vowels = count($vowels);
				$num_cons = count($cons);
		}

		$string = '';
		for($i = 0; $i < $len; $i++){
			$string .= $cons[mt_rand(0, $num_cons - 1)] . $vowels[mt_rand(0, $num_vowels - 1)];
		}

		return substr($string, 0, $len);
	}



	/**
	 * Standarize the end-of-line character in a string.
	 *
	 * @param    string $string    The string to work on
	 * @param    string $eol       The end of line character to use
	 * @return   string
	 */
	public static function standardEol($string, $eol = self::EOL_LF)
	{
		return preg_replace('#\n|\r\n|\r#', $eol, $string);
	}


	/**
	 * Replaces all linebreaks with a space character, making it a single line of text
	 *
	 * @param  sstring $string The string to work on
	 * @return string
	 */
	public static function removeLineBreaks($string)
	{
		return preg_replace('#\n|\r\n|\r#', ' ', $string);
	}


	/**
	 * Return the first line of a string.
	 *
	 * @param    string    $string The string to work on
	 * @return   string
	 */
	public static function getFirstLine($string)
	{
		$string = self::standardEol($string);

		if (($pos = strpos($string, "\n")) !== false) {
			$string = substr($string, 0, $pos);
		}

		return $string;
	}



	/**
	 * Return the last line of a string.
	 *
	 * @param    string    $string The string to work on
	 * @return   string
	 */
	public static function getLastLine($string)
	{
		$string = self::standardEol($string);

		$lines = explode("\n", $string);

		return array_pop($lines);
	}



	/**
	 * Check if $needle is anywhere in $haystack. If $needle is an array,
	 * then all strings in the array must be found in $haystack for
	 * this function to return true.
	 *
	 * @param    string|array    $needle    The string to search for
	 * @param    string          $haystack  The string to search in
	 * @param    bool            $any_needle  If using array $needle, return true for any found needle instead of requiring all
	 * @return bool
	 */
	public static function isIn($needle, $haystack, $any_needle = false)
	{
		if (is_array($needle)) {
			foreach ($needle as $n) {
				if (self::isIn($n, $haystack)) {
					if ($any_needle) {
						return true;
					}
				} else {
					if (!$any_needle) {
						return false;
					}
				}
			}

			return true;
		}

		return (strpos($haystack, $needle) !== false);
	}



	/**
	 * Check if $needle is at the beginning of $haystack
	 *
	 * @param    string    $needle    The string to search for
	 * @param    string    $haystack  The string to search in
	 * @return bool
	 */
	public static function startsWith($needle, $haystack)
	{
		if (is_array($needle)) {
			foreach ($needle as $n) {
				if (self::startsWith($n, $haystack)) {
					return true;
				}
			}

			return false;
		}

		if ($needle == $haystack OR $haystack == '') {
			return true;
		}

		return (strpos($haystack, $needle) === 0);
	}



	/**
	 * Check if $needle is at the end of $haystack
	 *
	 * @param    string    $needle    The string to search for
	 * @param    string    $haystack  The string to search in
	 * @return   bool
	 */
	public static function endsWith($needle, $haystack)
	{
		if (is_array($needle)) {
			foreach ($needle as $n) {
				if (self::endsWith($n, $haystack)) {
					return true;
				}
			}

			return false;
		}

		return Strings::startsWith(strrev($needle), strrev($haystack));
	}



	/**
	 * Get characters from the beginning of a string.
	 *
	 * @param    string   $string   The string to work on
	 * @param    int      $num      The number of characters to get
	 * @return   string
	 */
	public static function getFromStart($string, $num = 1)
	{
		return substr($string, 0, $num);
	}



	/**
	 * Get characters from the end of a string.
	 *
	 * @param    string   $string   The string to work on
	 * @param    int      $num      The number of characters to get
	 * @return   string
	 */
	public static function getFromEnd($string, $num = 1)
	{
		return substr($string, strlen($string) - $num);
	}



	/**
	 * Trim characters off of the end of a string.
	 *
	 * @param    string   $string  The string to work on
	 * @param    int      $num     How many characters to remove
	 * @return   string
	 */
	public static function delFromEnd($string, $num = 1)
	{
		return substr($string, 0, strlen($string) - $num);
	}



	/**
	 * Trim characters off of the start of a string.
	 *
	 * @param    string   $string  The string to work on
	 * @param    int      $num     How many characters to remove
	 * @return   string
	 */
	public static function delFromStart($string, $num = 1)
	{
		return substr($string, $num);
	}



	/**
	 * Get a string from a character index. Like substr, but doesn't works with
	 * indexes instead of lengths.
	 *
	 * @param    int     $index_start  The start index inclusive
	 * @param    int     $index_end    The end index, exclusive. Null means end of the string
	 * @return   string
	 */
	public static function getFromIndex($string, $index_start = 0, $index_end = null)
	{
		if ($index_end === null) {
			return substr($string, $index_start);
		}

		$length = ($index_end - $index_start);

		return substr($string, $index_start, $length);
	}



	/**
	 * Replace tokens in a string with values in arguments passed. The tokens must be incremented in the
	 * format of {1}, {2} etc and must start at 1.
	 *
	 * <code>
	 * echo Strings::format('* {1} smacks {2} around a bit with a big large trout', 'Christopher', 'David');
	 * // -> 'Christopher smacks David around a bit with a big large trout
	 * </code>
	 *
	 * @param   string  $string    The string to work on
	 * @param   mixed   $value...  The value(s) to replace each token with
	 * @return  string
	 */
	public static function format()
	{
		$args = func_get_args();

		#------------------------------
		# Get the string ready for vsprintf
		#------------------------------

		$string = array_shift($args);

		// Escape percents
		$string = str_replace('%', '%%', $string);

		// Replace {1} with %1$s
		$count = 0;
		$string = preg_replace('#\{([0-9]+)\}#', '%\\1$s', $string, -1, $count);

		if (!$count) {
			return $string;
		}

		// If there are too many placeholders, padd the args with empty strings
		// or else vsprintf will throw errors
		$args = array_pad($args, $count, '');


		#------------------------------
		# Return the string with placeholders replaces
		#------------------------------

		return vsprintf($string, $args);
	}



	/**
	 * Get the exention from a string. This is the last bits after the
	 * '.', i.e. as part of a path.
	 *
	 * @param   string $string  The string to work on
	 * @return  string
	 */
	public static function getExtension($string)
	{
		$matches = null;
		if (preg_match('#\.([a-zA-Z0-9]+)$#', $string, $matches)) {
			return strtolower($matches[1]);
		}

		return '';
	}



	/**
	 * Get all text above a boundary within a string.
	 *
	 * <code>
	 * $text = "Testing 123
	 * ======= BOUNDARY =======
	 * More text
	 * ";
	 *
	 * echo Strings::getAboveBoundary($text, "======= BOUNDARY ======="); // "Testing 123"
	 * </code>
	 *
	 * @param  string  $string    The string to operate on
	 * @param  string  $boundary  The boundary to look for
	 * @return string
	 */
	public static function getAboveBoundary($string, $boundary)
	{
		$pos = strpos($string, $boundary);

		if ($pos === false) return '';

		return substr($string, 0, $pos);
	}



	/**
	 * Get all text below a boundary within a string.
	 *
	 * <code>
	 * $text = "Testing 123
	 * ======= BOUNDARY =======
	 * More text
	 * ";
	 *
	 * echo Strings::getBelowBoundary($text, "======= BOUNDARY ======="); // "More text"
	 * </code>
	 *
	 * @param  string  $string    The string to operate on
	 * @param  string  $boundary  The boundary to look for
	 * @return string
	 */
	public static function getBelowBoundary($string, $boundary)
	{
		$pos = strpos($string, $boundary);

		if ($pos === false) return '';

		return substr($string, $pos+strlen($boundary));
	}



	/**
	 * Get all the text between two boundaries in a string.
	 *
	 * $mode determins what should be returned, especially in cases where there are multiple
	 * found boundary texsts:
	 * - BOUNDARY_APPEND: Append all results into a single string
	 * - BOUNDARY_ARRAY: Return an array of results
	 * - BOUNDARY_FIRST: Only return the first result as a string
	 *
	 * <code>
	 * $text = "Testing 123
	 * ======= BOUNDARY_START =======
	 * More text
	 * ======= BOUNDARY_END =======
	 * ";
	 *
	 * echo Strings::getBetweenBoundary($text, "======= BOUNDARY_START =======", "======= BOUNDARY_END ======="); // "More text"
	 * </code>
	 *
	 * @param  string  $string           The string to operate on
	 * @param  string  $boundary_start   The beginning boundary
	 * @param  string  $boundary_end     The end boundary
	 * @param  int     $mode             How to handle multiple results
	 * @return string|array
	 */
	public static function getBetweenBoundary($string, $boundary_start, $boundary_end = null, $mode = self::BOUNDARY_APPEND)
	{
		if (!$boundary_end) $boundary_end = $boundary_start;

		$boundary_start = preg_quote($boundary_start, '#');
		$boundary_end = preg_quote($boundary_end, '#');
		$regex = "#$boundary_start(.*?)$boundary_end#ms";

		$matches = array();
		if (!preg_match_all($regex, $string, $matches)) {
			if ($mode == self::BOUNDARY_ARRAY) {
				return array();
			} else {
				return '';
			}
		}

		// We want a full array
		if ($mode == self::BOUNDARY_ARRAY) {
			return $matches[1];
		}

		// Only the first
		if ($mode == self::BOUNDARY_FIRST) {
			return $matches[1][0];
		}

		// Append all results togehter
		$res = array();
		foreach ($matches[1] as $m) {
			$res[] = $m;
		}

		return implode('', $res);
	}



	/**
	 * Converts a dashed string into a camelCase string. Example:
	 * this-dash-string becomes thisDashString
	 *
	 * @param string $str
	 * @return string
	 */
	public static function dashToCamelCase($str)
	{
		$new_str = '';
		$str = strtolower($str);

		// Convert some-string to someController
		$do_upper = false;
		for ($i = 0; $i < strlen($str); $i++) {
			if ($str[$i] == '-') {
				$do_upper = true;
			} elseif ($do_upper) {
				$new_str .= strtoupper($str[$i]);
				$do_upper = false;
			} else {
				$new_str .= $str[$i];
			}
		}

		return $new_str;
	}


	/**
	 * Converts an underscored string into a camelCase string.
	 * Example: this_underscore_string becomes thisUnderscoreString
	 *
	 * @param string $str
	 * @return string
	 */
	public static function underscoreToCamelCase($str)
	{
		return self::dashToCamelCase(str_replace('_', '-', $str));
	}


	/**
	 * Converts a camelCase string to a dashed-string. Example:
	 * thisDashString becoems this-dash-string
	 *
	 * @param   string  $str  The string to work on
	 * @return  string
	 */
	public static function camelCaseToDash($str)
	{
		return strtolower(preg_replace('#([a-z0-9])([A-Z])#', '$1-$2', $str));
	}



	/**
	 * Converts a camelCase string to a underscored-string. Example:
	 * thisDashString becoems this_dash_string
	 *
	 * @param   string  $str  The string to work on
	 * @return  string
	 */
	public static function camelCaseToUnderscore($str)
	{
		return strtolower(preg_replace('#([a-z0-9])([A-Z])#', '$1_$2', $str));
	}



	/**
	 * Parse a simple 'key=value' string into an array.
	 *
	 * @param  string  $str        The string to parse, or an array of lines
	 * @param  int     $dupe_mode  What to do when dupe keys are found
	 * @return array
	 */
	public static function parseEqualsLines($str, $dupe_mode = self::EQUALSLINES_DUPE_OVERWRITE)
	{
		if (!is_array($str)) {
			$str = self::standardEol($str);
			$str = explode("\n", $str);
		}

		$values = array();

		foreach ($str as $line) {

			// No line
			if (!$line) continue;

			// Ignore 'comments'
			if ($line[0] == '#') continue;

			$vals = explode('=', $line, 2);
			if (!isset($vals[1])) continue; // wrong array size, should be two items

			$key = trim($vals[0]);
			$val = trim($vals[1]);

			if (Numbers::isInteger($val)) {
				$val = (int)$val;
			}

			// We can just overwrite
			if ($dupe_mode == self::EQUALSLINES_DUPE_OVERWRITE) {
				$values[$key] = $val;

			// Or we might need to create/add to an array of values
			} else {
				if (isset($values[$key])) {
					if (is_array($values[$key])) {
						$values[$key][] = $val;
					} else {
						$values[$key] = array($values[$key], $val);
					}
				} else {
					$values[$key] = $val;
				}
			}
		}

		return $values;
	}



	/**
	 * Encode a string as quoted-printable.
	 *
	 * @param $input
	 * @param $line_max
	 * @return string
	 */
	public static function quotedPrintableEncode($input, $line_max = 75)
	{
		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines = preg_split("/(?:\r\n|\r|\n)/", $input);
		$linebreak = "=0D=0A=\r\n";

		$line_max = $line_max - strlen($linebreak);
		$escape = "=";
		$output = "";
		$cur_conv_line = "";
		$length = 0;
		$whitespace_pos = 0;
		$addtl_chars = 0;

		for ($j=0; $j<count($lines); $j++) {
			$line = $lines[$j];
			$linlen = strlen($line);

			for ($i = 0; $i < $linlen; $i++) {
				$c = substr($line, $i, 1);
				$dec = ord($c);

				$length++;

				if ($dec == 32) {
					// space occurring at end of line, need to encode
					if (($i == ($linlen - 1))) {
						$c = "=20";
						$length += 2;
					}

					$addtl_chars = 0;
					$whitespace_pos = $i;
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) {
					$h2 = floor($dec/16); $h1 = floor($dec%16);
					$c = $escape . $hex["$h2"] . $hex["$h1"];
					$length += 2;
					$addtl_chars += 2;
				}

				// length for wordwrap exceeded, get a newline into the text
				if ($length >= $line_max) {
					$cur_conv_line .= $c;

					// read only up to the whitespace for the current line
					$whitesp_diff = $i - $whitespace_pos + $addtl_chars;
					$output .= substr($cur_conv_line, 0,
					(strlen($cur_conv_line) - $whitesp_diff)) .
					$linebreak;

					/* the text after the whitespace will have to be read
					 * again ( + any additional characters that came into
					 * existence as a result of the encoding process after the whitespace) */
					$i =  $i - $whitesp_diff + $addtl_chars;

					$cur_conv_line = "";
					$length = 0;
					$whitespace_pos = 0;
				} else {
					// length for wordwrap not reached, continue reading
					$cur_conv_line .= $c;
				}
			} // end of for

			$length = 0;
			$whitespace_pos = 0;
			$output .= $cur_conv_line;
			$cur_conv_line = "";

			if ($j<=count($lines)-1) {
				$output .= $linebreak;
			}
		}

		return trim($output);
	}



	/**
	 * Executes regex on a string and returns the match at index $index.
	 *
	 * If no matches were found, or if the index doesn't exist, null is returned.
	 *
	 * @param string   $regex   Regex to run
	 * @param string   $string  The string to run it on
	 * @param int      $index   The index to return, same rules. Or if -1 or null, all matches.
	 * @param int      $flags   Flags to pass to preg_match
	 * @param int      $offset  Offset to pass to preg_match
	 * @return string
	 */
	static public function extractRegexMatch($regex, $string, $index = 1, $flags = null, $offset = null)
	{
		$matches = null;
		if (!preg_match($regex, $string, $matches, $flags, $offset)) {
			return null;
		}

		if ($index == -1 OR $index === null) {
			return $matches;
		}

		return isset($matches[$index]) ? $matches[$index] : null;
	}


	/**
	 * This only wraps long words within a string (e.g., to prevent text-overflow in a browser).
	 * Use PHP's wordwrap() for normal word-wrapping at a speciifc column.
	 *
	 * For $break, check out Strings::ZERO_WIDTH_SPACE or Strings::SOFT_HYPHEN which might be useful.
	 *
	 * UTF-8 safe.
	 *
	 * @param string $string       The string to work on
	 * @param int    $length       The max length of a word before it wraps
	 * @param string $break        The character to insert at break points
	 * @param string $split_cahrs  The characters that separate words
	 * @return string
	 */
	static public function smartWordWrap($string, $max_len = 75, $break = ' ', $split_chars = " \t\n")
	{
		$string = self::standardEol($string);

		$r_split_chars = preg_quote($split_chars, '#');
		$segs = preg_split('#([' . $r_split_chars . '])#', $string, -1, \PREG_SPLIT_DELIM_CAPTURE);
		$string = '';

		foreach ($segs as $seg) {
			if (strlen($seg) > $max_len && self::utf8_strlen($seg) > $max_len) {
				$chars = self::utf8_str_split($seg);
				$chars = array_chunk($chars, $max_len);

				foreach ($chars as $chunk) {
					$string .= implode('', $chunk) . $break;
				}
			} else {
				$string .= $seg;
			}
		}

		$string = preg_replace('#' . $r_split_chars . '$#', '', $string);

		return $string;
	}


	/**
	 * Like str_replace but lets you specify the max number of times
	 * to replace the string.
	 *
	 * If $search/$replace are arrays, then each string will be tried once.
	 * So the total number of actual replacements may be the number of items in the arrays.
	 *
	 * @param string $search   The string to search for
	 * @param string $replace  The string to replace with
	 * @param string $subject  The string to apply changes to
	 * @param int    $limit    How many replacements to make
	 * @param bool   $reverse  Replace from end of the string instead
	 * @return string
	 */
	static public function strReplaceLimit($search, $replace, $subject, $limit = 1, $reverse = false)
	{
		if (is_array($search)) {
			foreach ($search as $k => $s) {
				if (is_array($replace)) {
					$r = $replace[$k];
				} else {
					$r = $replace;
				}

				$subject = self::strReplaceLimit($s, $r, $subject, $limit, $reverse);
			}
		} else {
			$x = 0;
			while ($x++ < $limit) {
				if ($reverse) {
					$pos = strrpos($subject, $search);
				} else {
					$pos = strpos($subject, $search);
				}
				if ($pos === false) break;
				$subject = substr_replace($subject, $replace, $pos, strlen($search));
			}
		}

		return $subject;
	}


	/**
	 * Cuts a section of a string out
	 *
	 * @param string $string  The string to work on
	 * @param int $cut_start  The index to start cutting (inclusive)
	 * @param int $cut_end    The index to stop the cut (exclusive)
	 * @return string
	 */
	static public function cut($string, $cut_start, $cut_end)
	{
		if ($cut_start == 0) {
			return substr($string, $cut_end);
		}

		return substr($string, 0, $cut_start) . substr($string, $cut_end);
	}


	/**
	 * Injects a string into the position at $inject_at
	 *
	 * @param string $string
	 * @param string $inject_string
	 * @param string $inject_at
	 */
	static public function inject($string, $inject_string, $inject_at)
	{
		if ($inject_at == 0) {
			return $inject_string . $string;
		} elseif ($inject_at > 0 && !isset($string[$inject_at])) {
			return $string . $inject_string;
		}

		return substr($string, 0, $inject_at) . $inject_string . substr($string, $inject_at);
	}



	/**
	 * Tries to verify and fix a regular expression, usually used to turn a regex inputted into a
	 * form into a real regex with delims.
	 *
	 * @param string $input
	 * @return string
	 */
	static public function getInputRegexPattern($input)
	{
		// Might be missing delims
		if (@preg_match($input, 'test') === false) {
			$input = "/" . str_replace('/', '\\/', $input) . "/";
		}

		// Check if its still invalid
		if (@preg_match($input, 'test') === false) {
			return false;
		}

		$delim = $input[0];
		if (($pos = strrpos($input, $delim)) === false) {
			// Handle special delims that could
			switch ($delim) {
				case '{': $delim = '}'; break;
				case '<': $delim = '>'; break;
				case '[': $delim = ']'; break;
				case '(': $delim = ')'; break;
			}
			$pos = strrpos($input, $delim);
		}

		if ($pos === false) {
			return false;
		}

		$modifiers = substr($input, $pos+1);
		if (strpos($modifiers, 'e') !== false) {
			return false;
		}

		return $input;
	}



	/**
	 * Turns a string into an acceptable URL slug.
	 * "My Great Title!" becomes "my-great-title"
	 *
	 * @param  string $string The string title to work on
	 * @return string
	 */
	static public function slugifyTitle($string)
	{
		$string = preg_replace('#[^a-zA-Z0-9]#', '-', $string);
		$string = preg_replace('#\-{2,}#', '-', $string); // remove  double dashes
		$string = preg_replace('#^\-+#', '', $string); // remove leading dashes
		$string = preg_replace('#\-+$#', '', $string); // remove trailing dashes
		$string = strtolower($string);

		return $string;
	}



	/**
	 * Converts newlines to paragraphs and breaks. Two consecutive newlines are paragrpahs, all else
	 * are breaks.
	 *
	 * @param  string $string    The string to work on
	 * @return stirng
	 */
	public static function nl2p($string)
	{
		$string = '<p>' . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p><p>', $string) . '</p>';
		$string = str_replace('<p></p>', '', $string);
		$string = nl2br($string);

		return $string;
	}



	/**
	 * Like urlencode() but encodes all characters, not just special ones.
	 *
	 * @param  string $string The string to encode
	 * @return string
	 */
	public static function urlencodeFull($string)
	{
		$ret = '';
		$len = strlen($string);
		for ($i = 0; $i < $len; $i++) {
			$hex = hexdec(ord($string[$i]));
			if ($hex) {
				$ret .= isset($hex[1]) ? '%' . strtoupper($hex) : '%0' . strtoupper($hex);
			} else {
				$ret .= rawurlencode($string[$i]);
			}
		}

		return $ret;
	}


	/**
	 * Turn links in text to HTML anchors
	 *
	 * @param string $text
	 * @param bool $short True to shorten off the part after the, like google.com/...
	 * @return string
	 */
	public static function autoLink($text, $short = true)
	{
		$pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
		$callback = function($matches) use ($short) {
			$url       = array_shift($matches);

			$text = parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);
			$text = preg_replace("/^www./", "", $text);

			if ($short) {
				$last = -(strlen(strrchr($text, "/"))) + 1;
				if ($last < 0) {
				   $text = substr($text, 0, $last) . "&hellip;";
				}
			}

			return sprintf('<a href="%s">%s</a>', $url, $text);
		};

		return preg_replace_callback($pattern, $callback, $text);
	}



	/**
	 * Like htmlentities() but encodes all characters, not just special ones.
	 * Useful for email addresses since most bots are stupid.
	 *
	 * @param string $string The string to encode
	 * @return string
	 */
	public static function htmlentitiesFull($string)
	{
		$ret = '';
		$len = strlen($string);

		for ($i = 0; $i < $len; $i++) {
			$enc = htmlentities($string[$i], ENT_QUOTES);
			$ret .= $string[$i] == $enc[0] ? '&#' . ord($string[$i]) : $enc;
		}

		return $ret;
	}



	/**
	 * Creates a string of k="v", suitable for use as HTML tag attributes.
	 *
	 * @param  array $attributes  The attributes to string together
	 * @param  bool  $do_escape   Escape values?
	 * @return string
	 */
	public static function htmlAttributes(array $attributes, $do_escape = true)
	{
		$attr = array();

		foreach ($attributes as $k => $v) {
			if ($v === false OR $v === null OR ($v === '' AND $k != 'value')) {
				continue;
			}

			if ($v === true) {
				$attr[] = "$k=\"$k\"";
			} else {
				$attr[] = $k . '="' . ($do_escape ? htmlspecialchars((string)$v, ENT_QUOTES) : $v) . '"';
			}
		}

		return implode(' ', $attr);
	}



	/**
	 * Just like explode() except its meant to be used with a limit where the explode happens
	 * from right to left.
	 *
	 * <code>
	 * $str = 'my.example.string.here';
	 * print_r(explode('.', $str, 2)); // array('my', 'example.string.here');
	 * print_r(Strings::rexplode('.', $str, 2)); // array('my.example.string', 'here');
	 * </code>
	 *
	 * @param  $delim
	 * @param  $string
	 * @param int $count
	 * @return array
	 */
	public static function rexplode($delim, $string, $count = 2)
	{
		$parts = explode($delim, $string);
		$len = count($parts);
		if ($len <= $count) {
			return $parts;
		}

		$offset = $len - $count + 1;
		$parts = array_merge(
			array(implode($delim, array_slice($parts, 0, $offset))),
			array_slice($parts, $offset)
		);

		return $parts;
	}



	/**
	 * Test a "star" wildcard match. This is a simplified sort of regex
	 * match where a star in the pattern is a non-greedy dot.
	 *
	 * Example: test-*@example.com is the regex test\-(.*?)@example\.com
	 *
	 * @param string $pattern The pattern that has the star wildcard character in it
	 * @param string $test    The string to test
	 * @param mixed  $matches A variable to put matches into
	 * @return string
	 */
	public static function isStarMatch($pattern, $test, &$matches = null)
	{
		// No wildcard in it, just a straight up comparison is needed
		if (strpos($pattern, '*') === false) {
			return ($pattern == $test);
		}

		$pattern = preg_quote($pattern, '#');
		$pattern = str_replace('\\*', '(.*?)', $pattern);
		$pattern = "#^$pattern$#";

		return preg_match($pattern, $test, $matches);
	}


	/**
	 * Trims whitespace and whitespace-like HTML from beginning/end of a string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function trimHtml($string)
	{
		// Handle HTML whitespace
		do {
			$old_string = $string;

			$string = trim($string);

			// Leading whitespace in a leading div wrapper
			$string = preg_replace('#^\s*(<div[^>]*>)\s*(<br>|<br />|<p></p>|<p>\s*</p>|<p><br\s*/?></p>|<p>&nbsp;</p>|&nsbp;)\s*#iu', '$1', $string);
			$string = preg_replace('#^\s*(<br>|<br />|<p></p>|<p>\s*</p>|<p><br\s*/?></p>|<p>&nbsp;</p>|&nsbp;)\s*#iu', '', $string);

			// Trailing whitespace in a trailing div wrapper
			$string = preg_replace('#\s*(<br>|<br />|<p></p>|<p>\s*</p>|<p><br\s*/?></p>|<p>&nbsp;</p>|<p>&\#xA0;</p>|<p>'.Strings::chrUni(160).'</p>|&nsbp;)\s*</div>$#iu', '</div>', $string);
			$string = preg_replace('#(<br>|<br />|<p></p>|<p>\s*</p>|<p><br\s*/?></p>|<p>&nbsp;</p>|<p>&\#xA0;</p>|<p>'.Strings::chrUni(160).'</p>|&nsbp;)$#i', '', $string);

			$string = preg_replace('#^(\s|<br>|<br />|<br/>|<p>\s*</p>)#iu', '', $string);
			$string = preg_replace('#(\s|<br>|<br />|<br/>|<p>\s*</p>)$#iu', '', $string);

			$string = preg_replace('#(<hr />|<hr>|<hr></hr>)+$#iu', '', $string);
			$string = preg_replace('#(<hr />|<hr>|<hr></hr>)+$#iu', '', $string);
		} while ($string != $old_string);

		return $string;
	}


	/**
	 * More advanced version of trimHtml is able to better detect empty elements to trim them out,
	 * and replaces empty divs or ps with simple newlines.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function trimHtmlAdvanced($html)
	{
		$html = Strings::extractBodyTag($html);
		$html = str_replace('<span></span>', '', $html);

		// Always wrap with body, or else in an attempt to fix structure
		// we'll end up with superfluous <p> wrappers around some top-level text nodes
		$html = '<body>' . $html . '</body>';

		do {
			$changed = false;
			$newhtml = preg_replace('#<span[^>]*>( | |&nbsp;|&\#xA0;)*</span>#i', '', $html);
			$newhtml = preg_replace('#<p[^>]*>( | |&nbsp;|&\#xA0;)*</p>#i', '<br />', $newhtml);
			$newhtml = preg_replace('#<div[^>]*>( | |&nbsp;|&\#xA0;)*</div>#i', '', $newhtml);

			if ($newhtml != $html) {
				$changed = true;
				$html = $newhtml;
			}
		} while ($changed);

		$qp = \QueryPath::withHTML($html, null, array('convert_to_encoding' => null));

		// Unwrap divs
		do {
			$changed = false;
			$qp->top()->find('div');
			foreach ($qp as $div) {
				if (!trim($div->text())) {
					$changed = true;
					$children = $div->branch();
					$children->children();
					foreach ($children as $child) {
						@$div->before($child);
					}
					@$div->remove();
					break;
				}
			}

			$qp->top();
		} while ($changed);

		ob_start();
		$qp->writeXHTML();
		$html = ob_get_clean();
		$html = Strings::extractBodyTag($html);

		// Unwrap outer divs, p's, spans
		do {
			$qp = \QueryPath::withHTML($html, null, array('convert_to_encoding' => null));
			$changed = false;

			/** @var $div \QueryPath\DOMQuery */
			$div = $qp->top()->find('body > *');
			if ($div->length == 1 && ($div->first() && ($div->tag() == 'div' || $div->tag() == 'p' || $div->tag() == 'span')) && !trim($div->textBefore().$div->textAfter())) {
				$changed = true;
				$html = $div->html();
				$html = trim($html);

				if ($div->tag() == 'div') {
					$html = preg_replace('#^<div.*?>#', '', $html);
					$html = preg_replace('#</div>$#', '', $html);
				} elseif ($div->tag() == 'span') {
					$html = preg_replace('#^<span.*?>#', '', $html);
					$html = preg_replace('#</span>$#', '', $html);
				} else {
					$html = preg_replace('#</p>$#', '', $html);
					$html = preg_replace('#^<p.*?>#', '', $html);
				}

				$html = Strings::extractBodyTag($html);
				$html = '<body>' . $html . '</body>';
			}

			$qp->top();
		} while($changed);

		ob_start();
		$qp->writeXHTML();
		$html = ob_get_clean();

		$html = Strings::extractBodyTag($html);
		$html = str_replace('<br></br>', '<br />', $html);

		$html_before = $html;
		$html = self::trimHtml($html);

		if (!$html) {
			$html = $html_before;
		}

		// Working with DOMDocument will have encoded things as HTML entities, convert back
		$html = \Orb\Util\Strings::decodeUnicodeEntities($html);

		return $html;
	}


	/**
	 * Linkfy in a string
	 *
	 * @static
	 * @param string $text
	 * @param string $attr
	 * @return string
	 */
	public static function linkify($text, $attr = '')
	{
		$search_replace = array();

		$text = preg_replace_callback('#(?<!\=(\'|")mailto:)([a-zA-Z0-9\-\._]+)@([a-zA-Z0-9\-\.]+)\.([a-zA-Z]+)\b#iu',function($m) use (&$search_replace, $attr) {
			$email = $m[2] . '@' . $m[3] . '.' . $m[4];
			$key = md5(mt_rand(0,9999) . microtime());
			$search_replace[$key] = '<a href="mailto:' . $email . '" '.$attr.'>' . htmlspecialchars($email, \ENT_QUOTES, 'UTF-8') . '</a>';
			return $key;
		}, $text);

		$text = preg_replace_callback('#(?<!\=(\'|"))(https?:\/\/[^\s<>]+([a-zA-Z0-9\?_\-]))#iu',function($m) use (&$search_replace, $attr) {
			$url = $m[2];
			$key = md5(mt_rand(0,9999) . microtime());
			$search_replace[$key] = '<a href="' . $url . '" '.$attr.'>' . htmlspecialchars($m[2], \ENT_QUOTES, 'UTF-8') . '</a>';
			return $key;
		}, $text);

		$text = preg_replace_callback('#(?<!\=(\'|"))(https?://|mailto:)?([a-zA-Z0-9\.\-]+\.(com|net|org|co\.uk)[^\s<>]*)#iu',function($m) use (&$search_replace, $attr) {
			if ($m[2]) return $m[0];

			$url = ($m[2] ? $m[2] : 'http://') . $m[3];
			$key = md5(mt_rand(0,9999) . microtime());
			$search_replace[$key] = '<a href="' . $url . '" '.$attr.'>' . htmlspecialchars($m[3], \ENT_QUOTES, 'UTF-8') . '</a>';
			return $key;
		}, $text);

		$text = str_replace(array_keys($search_replace), array_values($search_replace), $text);

		return $text;
	}


	/**
	 * Linkify in HTML
	 *
	 * @static
	 * @param string $html
	 * @param string $attr
	 * @return string
	 */
	public static function linkifyHtml($html, $new_window = false)
	{
		libxml_use_internal_errors(true);

		$orig_html = $html;

		$html = self::extractBodyTag($html);
		$html = self::preDomDocument($html);

		$dom = new DOMDocument('1.0', 'UTF-8');
		if (strpos($html, '<body') === false) {
			$html = "<body>$html</body>";
		}
		if (strpos($html, '<?xml') === false) {
			$html = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".$html;
		}

		if (!$dom->loadHTML($html)) {
			return $orig_html;
		}

		$xpath = new \DOMXPath($dom);

		foreach ($xpath->query('//text()') as $text)
		{
			if (strpos($text->getNodePath(), '/a/') !== false) {
				continue;
			}

			$origText = $text->nodeValue;
			$newText  = self::linkify(self::postDomDocument($origText), '');
			$newText  = self::preDomDocument($newText);

			if ($origText != $newText) {
				$frag = new DOMDocument('1.0', 'UTF-8');
				$frag->loadHTML('<?xml encoding="UTF-8" version="1.0" ?><body>' . $newText . '</body>');
				$xpath2 = new \DOMXPath($frag);

				foreach ($xpath2->query('body')->item(0)->childNodes as $node) {
					$node2 = $dom->importNode($node, true);
					if ($node2) {
						$text->parentNode->insertBefore($node2, $text);
					}
				}
				$text->parentNode->removeChild($text);
			}
		}

		$html = $dom->saveHTML();
		$html = self::extractBodyTag($html);
		$html = self::postDomDocument($html);

		if ($new_window) {
			$html = str_replace('<a', '<a target="_blank"', $html);
		}

		// Attempt to fix bad www. urls. These will be invalid input from the client,
		// but we can try to fix them easily enough
		// We're just looking for schema-less links to insert http://
		$html = preg_replace('#<a([^>]*)href=("|\')(?![a-zA-Z0-9]+:)#', '<a$1href=$2http://', $html);

		return $html;
	}


	/**
	 * In older versions of libxml (<2.7), DOMDocument can screw around with entities. So the easiest solution
	 * is to just encode non-ascii characters as our own ascii sequences, and then reverse them again after.
	 *
	 * @param string $string  A UTF-8 string
	 * @return string
	 */
	public static function preDomDocument($string)
	{
		// Convert any existing entities into their real chars
		// so we can then convert those to our special placeholders.
		$string = self::decodeUnicodeEntities($string);

		$string = str_replace(array('&lt;', '&gt;', '&amp;', '&nbsp;'), array('__DP_AMP_LT__', '__DP_AMP_GT__', '__DP_AMP_AMP__', '__DP_AMP_NBSP__'), $string);
		$string = self::htmlEntityEncodeUtf8($string, '__DPUNI_%s_DPUNI__');
		$string = str_replace('__DPUNI_194_DPUNI____DPUNI_160_DPUNI__', '__DP_AMP_NBSP__', $string);
		$string = str_replace('__DPUNI_160_DPUNI__', '__DP_AMP_NBSP__', $string);

		return $string;
	}


	/**
	 * @see preDomDocument
	 * @param string $string
	 * @return string
	 */
	public static function postDomDocument($string)
	{
		// Undo unicode encode
		$string = preg_replace_callback('#__DPUNI_([0-9]+)_DPUNI__#', function ($m) {
			return Strings::chrUtf8($m[1]);
		}, $string);

		$string = str_replace(array('__DP_AMP_LT__', '__DP_AMP_GT__', '__DP_AMP_AMP__', '__DP_AMP_NBSP__'), array('&lt;', '&gt;', '&amp;', '&nbsp;'), $string);

		return $string;
	}


	/**
	 * Get text between the body tags in an html doc
	 *
	 * @param string $value
	 */
	public static function extractBodyTag($value)
	{
		$value = preg_replace('#(<body[^>]*>)#i', '<body>', $value);
		$count = substr_count($value, '<body>');

		if (!$count) {
			return $value;
		}

		// Most common case, only one body tag
		if ($count == 1) {
			do {
				$changed = false;

				$pos = strpos($value, "<body");
				if ($pos !== false) {
					$changed = true;
					$value = substr($value, $pos);

					// Cut out the rest of the body tag too, eg if it was <body class="abc"> we're finding the ">" part of that
					$pos = strpos($value, ">");
					$value = substr($value, $pos+1);
				}
			} while($changed);

			$pos = strpos($value, '</body>');
			if ($pos !== false) {
				$value = substr($value, 0, $pos);
			}

		// Less common case of multiple body tags, we'll treat it as one big doc and just get rid of html/meta etc tags
		} else {
			$value = preg_replace('#<(style|head|meta)[^>]*>.*?</\\1>#is', '', $value);
			$value = preg_replace('#<(html|body)[^>]*>#is', '', $value);
			$value = preg_replace('#</(html|body)>#is', '', $value);
			$value = str_replace('<?xml version="1.0" encoding="UTF-8"??>', '', $value);
			$value = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $value);
		}

		$value = trim($value);

		return $value;
	}

	/**
	 * Parses out data URLs in <img> tags and replaces them with unique tokens you can later
	 * str_replace with real paths.
	 *
	 * Returns an array:
	 *     array('string' => $string, 'files' => array(array('token' => 'xxx', 'type' => 'mime/type', 'data' => 'xxx')));
	 *
	 * If $raw is enabled the format changes slightly:
	 *    array('string' => $string, 'files' => array(array('token' => 'xxx', 'raw_data' => 'xxx')));
	 *
	 * @param string  $string The string to process
	 * @param boolean $raw    Dont base64 decode the images, return the raw string
	 * @return array
	 */
	public static function parseImageDataUrls($string, $raw = false)
	{
		$matches = null;
		if (!preg_match_all('#<img[^>]*/?>#i', $string, $matches[0])) {
			return array('string' => $string, 'tokens' => array());
		}

		$files = array();

		foreach ($matches[0] as $m) {
			$url_m = null;
			if (!preg_match('#src=(?:\'|")(data:[A-Za-z0-9+/=:;,]+)#i', $m[0], $url_m)) {
				continue;
			}

			$tok = '__DP_TOK_' . self::random(20, self::CHARS_ALPHANUM_IU) . '__';

			$new_str = str_replace($url_m[1], $tok, $m[0]);

			$string = str_replace(
				$m[0],
				$new_str,
				$string
			);

			if ($raw) {
				$files[] = array(
					'token'    => $tok,
					'raw_data' => $url_m[1]
				);
			} else {
				$info = self::decodeDataUrl($url_m[1]);
				$files[] = array(
					'token' => $tok,
					'type'  => $info['type'],
					'data'  => $info['data']
				);
				unset($info);
			}
		}

		return array(
			'string' => $string,
			'files'  => $files
		);
	}


	/**
	 * Takes a data url and returns array('type' => 'mime/type', 'data' => 'binary_data').
	 * Returns null on failure.
	 *
	 * Data URLs look like:
	 *     data:image/png;base64,datahere
	 *
	 * @param string $data_url
	 * @return array|null
	 */
	public static function decodeDataUrl($data_url)
	{
		if ($data_url[0] === ' ') {
			$data_url = trim($data_url);
		}

		if (substr($data_url, 0, 5) == 'data:') {
			$data_url = substr($data_url, 5);
		}

		$colon_pos  = strpos($data_url, ';');
		$comma_pos  = strpos($data_url, ',');
		$mime_type  = substr($data_url, 0, $colon_pos);
		$data      = substr($data_url, $comma_pos+1);
		$data      = @base64_decode($data);

		return array('type' => $mime_type, 'data' => $data);
	}


	/**
	 * Just like explode() except it runs each item through trim as well.
	 *
	 * @param $string
	 * @param $delim
	 * @return array
	 */
	public static function explodeTrim($delim, $string, $limit = null)
	{
		$array = explode($delim, $string, $limit);
		array_walk($array, 'trim');

		return $array;
	}


	/**
	 * Removes "invisible" characters from strings, except for legit ones like newlines
	 * and tabs.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function removeInvisibleCharacters($string)
	{
		$string = preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+#S', '', $string);
		return $string;
	}


	/**
	 * Try to convert a string from one encoding into UTF-8
	 *
	 * @param string $string
	 * @param string $from_charset
	 * @return string
	 */
	public static function convertToUtf8($string, $from_charset)
	{
		// Some missing aliases in iconv
		static $charset_map = array(
			'KS_C_5601-1987' => 'CP949',
        	'ISO-8859-8-I'   => 'ISO-8859-8'
		);

		$from_charset_u = strtoupper($from_charset);

		if (isset($charset_map[$from_charset_u])) {
			$from_charset   = $charset_map[$from_charset_u];
			$from_charset_u = $charset_map[$from_charset_u];
		}

		if ($from_charset_u == 'UTF-8') {
			$string = self::utf8_bad_strip($string);
			return $string;
		}

		// Fix charsets with a country prepended like en_US.ISO-8859-1
		if (strpos($from_charset, '.')) {
			$parts = explode('.', $from_charset, 2);
			$from_charset = $parts[1];
		}

		// Surrounded in curlies like {windows-1251} (why? dont ask me, appears in some emails)
		if (preg_match('#^\{(.*?)\}$#', $from_charset, $m)) {
			$from_charset = $m[1];
		}

		$new = '';
		if (function_exists('iconv')) {
			$new = @iconv($from_charset, 'UTF-8//IGNORE//TRANSLIT', $string);
		} elseif (function_exists('mb_convert_encoding')) {
			$new = mb_convert_encoding($string, 'UTF-8', $from_charset);
		} else if (strtoupper($from_charset) == 'ISO-8859-1') {
			$new = utf8_encode($string);
		}

		$new = self::utf8_bad_strip($new);

		return $new;
	}


	/**
	 * Just like chr() except works with UTF-8 code points too.
	 *
	 * @param $code
	 * @return string
	 */
	public static function chrUtf8($code)
	{
		$code = (int)$code;

		// Invalid code
		if ($code < 0) {
			return false;
		}

		// Standard ascii
		if ($code < 128) {
			return chr($code);
		}

		// Remove Windows Illegals Cars
		if ($code < 160) {
			if ($code==128) $code=8364;
			elseif ($code==129) $code=160; // not affected
			elseif ($code==130) $code=8218;
			elseif ($code==131) $code=402;
			elseif ($code==132) $code=8222;
			elseif ($code==133) $code=8230;
			elseif ($code==134) $code=8224;
			elseif ($code==135) $code=8225;
			elseif ($code==136) $code=710;
			elseif ($code==137) $code=8240;
			elseif ($code==138) $code=352;
			elseif ($code==139) $code=8249;
			elseif ($code==140) $code=338;
			elseif ($code==141) $code=160; // not affected
			elseif ($code==142) $code=381;
			elseif ($code==143) $code=160; // not affected
			elseif ($code==144) $code=160; // not affected
			elseif ($code==145) $code=8216;
			elseif ($code==146) $code=8217;
			elseif ($code==147) $code=8220;
			elseif ($code==148) $code=8221;
			elseif ($code==149) $code=8226;
			elseif ($code==150) $code=8211;
			elseif ($code==151) $code=8212;
			elseif ($code==152) $code=732;
			elseif ($code==153) $code=8482;
			elseif ($code==154) $code=353;
			elseif ($code==155) $code=8250;
			elseif ($code==156) $code=339;
			elseif ($code==157) $code=160; // not affected
			elseif ($code==158) $code=382;
			elseif ($code==159) $code=376;
		}

		if ($code < 2048) {
			return chr(192 | ($code >> 6)) . chr(128 | ($code & 63));
		} elseif ($code < 65536) {
			return chr(224 | ($code >> 12)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
		} else {
			return chr(240 | ($code >> 18)) . chr(128 | (($code >> 12) & 63)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
		}
	}


	/**
	 * Takes a string with HTML entities and decodes them into their real UTF-8 characters.
	 *
	 * Note that the string is expected to already be UTF-8 or in a charset that it doesn't matter (ie ascii).
	 * This doesn't do actual charset conversion, it just reverses entities.
	 *
	 * @param string $string
	 * @param bool $escape_html True to pass result through htmlspecialchars again to escape HTML
	 * @return string
	 */
	public static function htmlEntityDecodeUtf8($string, $escape_html = false)
	{
		$fn = function($matches) {
			if ($matches[2]) {
				return Strings::chrUtf8(hexdec($matches[3]));
			} elseif ($matches[1]) {
				return Strings::chrUtf8($matches[3]);
			}

			return '';
		};

		// This catches all the normal named entities (nbsp etc)
		$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');

		// Replaces the rest like &#x20AC; (euro)
		$string = preg_replace_callback('/&(#(x?))?([^;]+);/', $fn, $string);

		if ($escape_html) {
			$string = htmlspecialchars($string, \ENT_QUOTES, 'UTF-8', false);
		}

		return $string;
	}


	/**
	 * Takes a unicode string and encodes multi-byte characters as HTML entities.
	 *
	 * Use $encodeString to use a pattern other than &#<code>;. (Used when you need to mark
	 * the unicode points with something other than entities). Use %s as the placeholder.
	 *
	 * @param string $string
	 * @param bool $encodeString
	 * @return string
	 */
	public static function htmlEntityEncodeUtf8($string, $encodeString = null)
	{
		if (!$string) {
			return $string;
		}

		$new_string = preg_replace_callback('/[^\x00-\x7F]/u', function($match) use ($encodeString) {
			$string = $match[0];
			$c1 = ord($string[0]);
			if ($c1 < 0x80) {
				return $c1;
			}

			$code = null;

			if (($c1 & 0xF8) == 0xF0) {
				// 4 bytes
				$code = (($c1 & 0x07) << 18) | ((ord($string[1]) & 0x3F) << 12) | ((ord($string[2]) & 0x3F) << 6) | (ord($string[3]) & 0x3F);
			} else if (($c1 & 0xF0) == 0xE0) {
				// 3 bytes
				$code = (($c1 & 0x0F) << 12) | ((ord($string[1]) & 0x3F) << 6) | (ord($string[2]) & 0x3F);
			} else if (($c1 & 0xE0) == 0xC0) {
				// 2 bytes
				$code = (($c1 & 0x1F) << 6) | (ord($string[1]) & 0x3F);
			}

			if ($code) {
				if ($encodeString) {
					return sprintf($encodeString, $code);
				} else {
					return '&#' . $code . ';';
				}
			} else {
				return '?';
			}
		}, $string);

		if (!$new_string) {
			return $string;
		}

		return $new_string;
	}


	/**
	 * Like str_replace() except it only does the first.
	 *
	 * @see Strings::strReplaceLimit
	 *
	 * @param string $find
	 * @param string $replace
	 * @param string $string
	 * @param bool   $reverse
	 * @return string
	 */
	public static function strReplaceOne($find, $replace, $string, $reverse = false)
	{
		return self::strReplaceLimit($find, $replace, $string, 1, $reverse);
	}


	/**
	 * Trims every line in a string
	 *
	 * @param $string
	 * @return string
	 */
	public static function trimLines($string, $chars = null, $mode = 'trim')
	{
		if ($mode != 'trim' && $mode != 'rtrim' && $mode != 'ltrim') {
			throw new \InvalidArgumentException("Invalid trim mode. Must be trim, rtrim or ltrim");
		}

		$string = explode("\n", $string);
		if ($chars !== null) {
			foreach ($string as &$l) $l = $mode($l, $chars);
		} else {
			foreach ($string as &$l) {
				$l = $mode($l);

				// Other unicode whitespace chars
				if ($mode == 'trim') {
					$l = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '$1', $l);
				} elseif ($mode == 'ltrim') {
					$l = preg_replace('/^[\pZ\pC]+/u', '$1', $l);
				} elseif ($mode == 'rtrim') {
					$l = preg_replace('/[\pZ\pC]+$/u', '$1', $l);
				}
			}
		}
		return implode("\n", $string);
	}


	/**
	 * Does a "real" trim, triming other whitespace like non-breaking spaces.
	 *
	 * @param $string
	 */
	public static function trimWhitespace($string)
	{
		$string = trim($string);
		$string = trim($string, "\x7f..\xff\x0..\x1f");
		return $string;
	}


	/**
	 * Attempts to turn HTML into plaintext
	 *
	 * @param string $string
	 * @return string
	 */
	public static function html2Text($string)
	{
		$body = self::standardEol($string);
		$body = str_replace("\n", '', $body);
		$body = preg_replace('#<br[^>]*>#i', "\n", $body);
		$body = preg_replace('#<p[^>]*>#i', "\n", $body);
		$body = strip_tags($body);
		$body = Strings::decodeHtmlEntities($body);
		$body = preg_replace('#\x{00a0}#u', ' ', $body); // nbsp's
		$body = trim($body);

		return $body;
	}


	/**
	 * Remove all empty lines in a string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function removeEmptyLines($string)
	{
		$string = explode("\n", $string);

		$ret = array();
		foreach ($string as $l) {
			if (trim($l) !== '') {
				$ret[] = $l;
			}
		}

		$ret = implode("\n", $ret);
		return $ret;
	}


	/**
	 * Adds a prefix and/or suffix to every line
	 * @param $string
	 * @param $prefix
	 */
	public static function modifyLines($string, $prefix = '', $suffix = '', $trim = false)
	{
		$string = explode("\n", $string);
		foreach ($string as &$l) {
			$l = $prefix . ($trim ? trim($l) : $l) . $suffix;
		}

		return implode("\n", $string);
	}


	/**
	 * Create an ASCII table around a key=>value array
	 *
	 * @param string $array
	 * @param string $key_title
	 * @param string $val_title
	 * @return string
	 */
	public static function asciiTable($array, array $titles = null, $line_sep = false)
	{
		$lines = array();
		$lens = array();

		if ($titles) {
			foreach ($titles as $idx => $t) {
				$tmp = Strings::utf8_strlen($t);
				if (!isset($lens[$idx]) || $tmp > $lens[$idx]) {
					$lens[$idx] = $tmp;
				}
			}
		}

		foreach ($array as $row) {
			foreach ($row as $idx => $t) {
				$tmp = Strings::utf8_strlen($t);
				if (!isset($lens[$idx]) || $tmp > $lens[$idx]) {
					$lens[$idx] = $tmp;
				}
			}
		}

		$fn_line_sep = function() use ($lens) {
			$l = array();
			foreach ($lens as $len) {
				$l[] = str_repeat('-', $len);
			}

			return '+-' . implode('-+-', $l) . '-+';
		};

		$fn_line = function($cells) use ($lens) {
			$l = array();
			foreach ($cells as $idx => $t) {
				$l[] = Strings::utf8_str_pad($t, $lens[$idx], ' ');
			}

			return '| ' . implode(' | ', $l) . ' |';
		};

		if ($titles) {
			$lines[] = $fn_line_sep();
			$lines[] = $fn_line($titles);
		}

		$lines[] = $fn_line_sep();

		foreach ($array as $row) {
			$lines[] = $fn_line($row);

			if ($line_sep) {
				$lines[] = $fn_line_sep();
			}
		}

		if (!$line_sep) {
			$lines[] = $fn_line_sep();
		}

		$lines = implode("\n", $lines);

		return $lines;
	}


	/**
	 * Make an ascii table from a keyvalue pair
	 *
	 * @param array $array
	 * @param string $key_title
	 * @param string $val_title
	 * @param bool $line_sep
	 */
	public static function keyValueAsciiTable(array $array, $key_title = '', $val_title = '', $line_sep = false)
	{
		$new_array = array();

		foreach ($array as $k => $v) {
			$new_array[] = array($k, $v);
		}

		$titles = array();
		if ($key_title || $val_title) {
			$titles = array($key_title, $val_title);
		}

		return self::asciiTable($new_array, $titles, $line_sep);
	}


	/**
	 * Prepares WYSIWYG HTML where <p> tags only take up one line
	 * by translating into <divs> or replacing with a simple <br>
	 *
	 * @param $html
	 *
	 * @return string
	 */
	public static function prepareWysiwygHtml($html)
	{
		$html = str_replace(array('<p', '</p>'), array('<div', '</div>'), $html);
		$html = preg_replace('#(<br\s*/?>)\s*</div>#', '</div>', $html);
		$html = preg_replace('#<div[^>]*>\s*(<br\s*/?>)?\s*</div>\s*#i', "<br />\n", $html);
		do {
			$original = $html;
			$html = preg_replace('#<div>(.*)</div>\s*?#siU', "\\1<br />\n", $html);
			// need to loop to handle nested divs - may not be perfect
		} while ($original != $html);

		$html = preg_replace('#(<br\s*/?>\s*)+$#', '', $html);

		return trim($html);
	}

	/**
	 * Converts WYISWYG HTML to plain text.
	 *
	 * @param string $html
	 * @param bool $p_one_line If true, P tags are treated as one line break
	 *
	 * @return string
	 */
	public static function convertWysiwygHtmlToText($html, $p_one_line = true)
	{
		$html = preg_replace('#</p>\s*#', $p_one_line ? "\n" : "\n\n", $html);
		$html = preg_replace('#</(div|ul|ol|li)>\s*#', "\n", $html);
		$html = preg_replace('#<br\s*/?>\s*#', "\n", $html);

		return trim(htmlspecialchars_decode(strip_tags($html)));
	}

	/**
	 * Compares 2 HTML strings to see if they the same (or nearly the same)
	 *
	 * @param string $html1
	 * @param string $html2
	 *
	 * @return bool
	 */
	public static function compareHtml($html1, $html2)
	{
		return ($html1 == $html2 || self::_prepareCompareHtml($html1) == self::_prepareCompareHtml($html2));
	}

	protected static function _prepareCompareHtml($html)
	{
		$replace = array(
			'<br />' => '<br>',
			'<div' => '<p',
			'</div>' => '</p>'
		);
		$html = str_replace(array_keys($replace), $replace, $html);

		// try to normalize whitespace
		$html = preg_replace('#>\s+#', '>', $html);
		$html = preg_replace('#\s+#', ' ', $html);
		$html = trim($html);

		$html = preg_replace('#^<[^>]+>#', '', $html);
		$html = preg_replace('#<[^>]+>$#', '', $html);

		return trim($html);
	}


	/**
	 * Splits a UTF-8 string into words
	 *
	 * @param string $string
	 * @return array
	 */
	public static function splitWords($string)
	{
		$split = preg_split('/\b([\(\).,\-\',:!\?;"\{\}\[\]„“»«‘\r\n]*)/u', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		return array_filter($split, function ($v) {
			if ($v = trim($v)) {
				return $v;
			}
			return false;
		});
	}


	/**
	 * Decodes entities that are whitespace into their UTF-8 characters
	 *
	 * @param string $string
	 * @return string
	 */
	public static function decodeWhitespaceHtmlEntities($string)
	{
		// Sometimes these chars are encoded by clients
		$repl = array(
			'&#10;'  => "\n",
			'&#xa;'  => "\n",
			'&#13;'  => "\r",
			'&#xd;'  => "\r",
			'&#9;'   => "	",
			'&#x9;'  => "	",
			'&#32;'  => ' ',
			'&#x20;' => ' ',
			'&#160;' => '&nbsp;',
			'&#xa0;' => '&nbsp;',
		);
		$string = str_ireplace(array_keys($repl), array_values($repl), $string);

		return $string;
	}


	/**
	 * Just like html_entity_decode but decodes UTF-8 entities too.s
	 *
	 * @param string $html
	 * @return mixed
	 */
	public static function decodeHtmlEntities($html)
	{
		$html = self::decodeUnicodeEntities($html);

		// Decode normal stuff
		$html = html_entity_decode($html, \ENT_QUOTES, 'UTF-8');

		return $html;
	}


	/**
	 * Remove the byte order mark from the beginning of a file string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function removeBom($string)
	{
		static $bom = null;

		if ($bom === null) {
			$bom = pack('CCC', 0xEF, 0xBB, 0xBF);
		}

		if (substr($string, 0, 3) === $bom) {
			$string = substr($string, 3);
		}

		return $string;
	}


	/**
	 * Decodes unicode html entities into their actual characters.
	 *
	 * @param string $html
	 * @return mixed
	 */
	public static function decodeUnicodeEntities($html)
	{
		// HTML special chars that we dont want to decode this way
		$skip_chars = array(
			34  => true, // "
			39  => true, // '
			38  => true, // &
			60  => true, // <
			62  => true, // >
			160 => true, // nbsp
		);

		$html = preg_replace_callback('/&#([0-9]+);/', function($m) use ($skip_chars) {
			if (isset($skip_chars[$m[1]])) {
				return $m[0];
			}
			return Strings::chrUni($m[1]);
		}, $html);

		$html = preg_replace_callback('/&#x([0-9A-F]+);/', function($m) use ($skip_chars) {
			$int = hexdec($m[1]);
			if (isset($skip_chars[$int])) {
				return $m[0];
			}
			return Strings::chrUni($int);
		}, $html);

		return $html;
	}


	/**
	 * Like chr() but works with UTF-8 codepoints
	 *
	 * @param string $val
	 * @return string
	 */
	public static function chrUni($val)
	{
		$val = intval($val);
		switch ($val) {
			case 0: return chr(0);
			case ($val & 0x7F): return chr($val);
			case ($val & 0x7FF): return chr(0xC0 | (($val >> 6) & 0x1F)) . chr(0x80 | ($val & 0x3F));
			case ($val & 0xFFFF): return chr(0xE0 | (($val >> 12) & 0x0F)) . chr(0x80 | (($val >> 6) & 0x3F)) . chr (0x80 | ($val & 0x3F));
			case ($val & 0x1FFFFF): return chr(0xF0 | ($val >> 18)) . chr(0x80 | (($val >> 12) & 0x3F)) . chr(0x80 | (($val >> 6) & 0x3F)) . chr(0x80 | ($val & 0x3F));
		}

		return '';
	}


	/**
	 * Strips out invalid UTF-8 characters from strings.
	 *
	 * @param $string
	 * @return string
	 */
	public static function utf8_bad_strip($string)
	{
		if (function_exists('iconv')) {
			return @iconv('UTF-8', 'UTF-8//IGNORE', $string);
		} elseif (function_exists('mb_convert_encoding')) {
			return @mb_convert_encoding($string, 'UTF-8', 'UTF-8');
		} else {

			$time = time();

			// see app/vendor/php-utf8/utils/bad.php
			$UTF8_BAD =
				'([\x00-\x7F]'.                          # ASCII (including control chars)
				'|[\xC2-\xDF][\x80-\xBF]'.               # non-overlong 2-byte
				'|\xE0[\xA0-\xBF][\x80-\xBF]'.           # excluding overlongs
				'|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}'.    # straight 3-byte
				'|\xED[\x80-\x9F][\x80-\xBF]'.           # excluding surrogates
				'|\xF0[\x90-\xBF][\x80-\xBF]{2}'.        # planes 1-3
				'|[\xF1-\xF3][\x80-\xBF]{3}'.            # planes 4-15
				'|\xF4[\x80-\x8F][\x80-\xBF]{2}'.        # plane 16
				'|(.{1}))';                              # invalid byte
			ob_start();
			while (preg_match('/'.$UTF8_BAD.'/S', $string, $matches)) {
				if ( !isset($matches[2])) {
					echo $matches[0];
				}
				$string = substr($string,strlen($matches[0]));

				// Going too long, the string is clearly corrupt!
				if (time() - $time > 6) {
					return '';
				}
			}
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}
	}


	/**
	 * Set the path to the php-utf8 library functions, and thereby enable
	 * dynamic calling of utf8_xxx calls on this string class.
	 *
	 * @param string $dir
	 * @return void
	 */
	public static function setPhpUtf8Dir($dir)
	{
		self::$php_utf8_dir = $dir;
	}

	public static function __callStatic($name, $args)
	{
		if (!self::$php_utf8_dir) {
			if (defined('ORB_STRINGS_UTF8_DIR')) {
				self::$php_utf8_dir = \ORB_STRINGS_UTF8_DIR;
			} else {
				throw new \BadMethodCallException('Unknown method `'.$name.'`');
			}
		}

		static $funcmap = array(
			'utf8_strlen'                      => '__CORE__',
			'utf8_strpos'                      => '__CORE__',
			'utf8_strrpos'                     => '__CORE__',
			'utf8_substr'                      => '__CORE__',
			'utf8_strtolower'                  => '__CORE__',
			'utf8_strtoupper'                  => '__CORE__',
			'utf8_ord'                         => 'ord.php',
			'utf8_ireplace'                    => 'str_ireplace.php',
			'utf8_str_pad'                     => 'str_pad.php',
			'utf8_str_split'                   => 'str_split.php',
			'utf8_strcasecmp'                  => 'strcasecmp.php',
			'utf8_strcspn'                     => 'strcspn.php',
			'utf8_stristr'                     => 'stristr.php',
			'utf8_strrev'                      => 'strrev.php',
			'utf8_strspn'                      => 'strspn.php',
			'utf8_substr_replace'              => 'substr_replace.php',
			'utf8_ltrim'                       => 'trim.php',
			'utf8_rtrim'                       => 'trim.php',
			'utf8_trim'                        => 'trim.php',
			'utf8_ucfirst'                     => 'ucfirst.php',
			'utf8_ucwords'                     => 'ucwords.php',
			'utf8_is_ascii'                    => 'utils/ascii.php',
			'utf8_is_ascii_ctrl'               => 'utils/ascii.php',
			'utf8_strip_non_ascii'             => 'utils/ascii.php',
			'utf8_strip_ascii_ctrl'            => 'utils/ascii.php',
			'utf8_strip_non_ascii_ctrl'        => 'utils/ascii.php',
			'utf8_accents_to_ascii'            => 'utils/ascii.php',
			'utf8_bad_find'                    => 'utils/bad.php',
			'utf8_bad_findall'                 => 'utils/bad.php',
			'utf8_bad_strip'                   => 'utils/bad.php',
			'utf8_bad_replace'                 => 'utils/bad.php',
			'utf8_bad_identify'                => 'utils/bad.php',
			'utf8_bad_explain'                 => 'utils/bad.php',
			'utf8_byte_position'               => 'utils/position.php',
			'utf8_locate_current_chr'          => 'utils/position.php',
			'utf8_locate_next_chr'             => 'utils/position.php',
			'utf8_specials_pattern'            => 'utils/specials.php',
			'utf8_is_word_chars'               => 'utils/specials.php',
			'utf8_strip_specials'              => 'utils/specials.php',
			'utf8_to_unicode'                  => 'utils/unicode.php',
			'utf8_from_unicode'                => 'utils/unicode.php',
			'utf8_is_valid'                    => 'utils/validation.php',
			'utf8_compliant'                   => 'utils/validation.php',
		);

		if (isset($funcmap[$name])) {
			require_once(self::$php_utf8_dir . '/ORB_LOAD.php');
			if ($funcmap[$name] !== '__CORE__') {
				require_once(self::$php_utf8_dir . '/' . $funcmap[$name]);
			}
			return call_user_func_array($name, $args);
		} else {
			throw new \BadMethodCallException('Unknown method `'.$name.'`');
		}
	}
}
