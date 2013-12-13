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
 * General utility functions.
 *
 * @static
 */
class Util
{
	const BASE62_ALPHABET  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const BASE36_ALPHABET  = '0123456789abcdefghijklmnopqrstuvwxyz';
	const LETTERS_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';


	/**
	 * Get the type of a variable. If it's an object, also get the classname.
	 *
	 * @param mixed $var
	 * @return string
	 */
	public static function typeof($var)
	{
		$type = gettype($var);

		if ($type == 'object') {
			$type .= ':' . get_class($var);
		}

		return $type;
	}



	/**
	 * Return $param if it is set, else return $or.
	 *
	 * Example:
	 * <code>
	 * // The following two lines are the same
	 * $val = isset($var) ? $var : 'default value';
	 * $val = Orb_Util::ifsetor($var, 'default value');
	 * </code>
	 *
	 * @param    mixed    $param    The parameter to check
	 * @param    mixed    $or       The value to return if $param is not set
	 * @return   mixed
	 */
	public static function ifsetor(&$param, $or = null)
	{
		if (isset($param)) {
			return $param;
		}

		return $or;
	}



	/**
	 * Return $param if it is truthy, else return $or.
	 *
	 * Example:
	 * <code>
	 * // The following two lines are the same
	 * $val = $var ? $var : 'val';
	 * $val = Orb_Util::ifvalor($var, 'val');
	 * </code>
	 *
	 * @param    mixed    $param    The parameter to check
	 * @param    mixed    $or       The value to return if $param is not truthy
	 * @return   mixed
	 */
	public static function ifvalor($param, $or = null)
	{
		if ($param) {
			return $param;
		}
		return $or;
	}



	/**
	 * Assigns a default value to a varaible if it has not been set or it is
	 * not truthy.
	 *
	 * Example:
	 * <code>
	 * // The following two lines are the same
	 * $val = (isset($val) AND $val) ? $val : 'default';
	 * Orb_Util::defaultVal($val, 'default');
	 * </code>
	 *
	 * @param    string   $param    The parameter to use
	 * @param    string   $default  The default value
	 */
	public static function defaultVal(&$param, $default = null)
	{
		if (!isset($param) or !$param) {
			$param = $default;
		}
	}



	/**
	 * Returns either $true or $false depending on if $cond evaluates
	 * to true/false.
	 *
	 * NOTE: You should use the ternary operator in most cases, but this function
	 * exists for situations where a function is required.
	 *
	 * @param    mixed    $cond    The condition
	 * @param    mixed    $true    What to return if the condition is true
	 * @param    mixed    $false   What to return if the condition is false
	 * @return   mixed
	 */
	public static function iff($cond, $true = true, $false = false)
	{
		return $cond ? $true : $false;
	}



	/**
	 * Return the first truthy value in all arguments. If no arguments are
	 * truthy, then the last argument is returned.
	 *
	 * // Examples
	 * <code>
	 * $var = Orb_Util::coalesce(false, null, true, 1); // true
	 * $var = Orb_Util::coalesce(false, null, array()); // array, because it's the last
	 * </code>
	 *
	 * @return mixed
	 */
	public static function coalesce()
	{
		foreach (func_get_args() as $v) {
			if ($v) {
				return $v;
			}
		}

		return func_get_arg(func_num_args() - 1);
	}



	/**
	 * Encode a number using an alphabet.
	 *
	 * @param   int     $num        The number to encode
	 * @param   string  $alphabet   The alphabet to encode with
	 * @return  string
	 */
	public static function baseEncode($num, $alphabet)
	{
		if ($alphabet == 'base62') $alphabet = self::BASE62_ALPHABET;
		elseif ($alphabet == 'base36') $alphabet = self::BASE36_ALPHABET;
		elseif ($alphabet == 'letters') $alphabet = self::LETTERS_ALPHABET;

		if ($num == 0) {
			return $alphabet[0];
		}

		$arr = array();
		$base = strlen($alphabet);

		while ($num) {
			$rem = $num % $base;
			$num = (int)($num / $base);
			$arr[] = $alphabet[$rem];
		}

		$arr = array_reverse($arr);
		return implode('', $arr);
	}



	/**
	 * Decode a number using an alphabet.
	 *
	 * @param   string  $string    The string-encoded number to decode
	 * @param   string  $alphabet  The alphabet used to decode
	 * @return  int
	 */
	public static function baseDecode($string, $alphabet)
	{
		if ($alphabet == 'base62') $alphabet = self::BASE62_ALPHABET;
		elseif ($alphabet == 'base36') $alphabet = self::BASE36_ALPHABET;
		elseif ($alphabet == 'letters') $alphabet = self::LETTERS_ALPHABET;

		$alphabet = str_split($alphabet);
		$base = sizeof($alphabet);
		$strlen = strlen($string);
		$num = 0;
		$idx = 0;

		$s = str_split($string);
		$tebahpla = array_flip($alphabet);

		foreach ($s as $char) {
			// Invalid character found in string
			if (!isset($tebahpla[$char])) {
				return null;
			}
			$power = ($strlen - ($idx + 1));
			$num += $tebahpla[$char] * (pow($base, $power));
			$idx += 1;
		}
		return $num;
	}



	/**
	 * Serialize a data structure and sign it with some secret key. The data
	 * is also base64.
	 *
	 * This is feedbackl when transmitting a serialized object where it could potentially
	 * be tampered with by a user. If they tamper with the data, then the sign hash
	 * becomes invalid and the unserialize method will throw an exception.
	 *
	 * @param  mixed   $data      The data you want to serialize (i.e., an array)
	 * @param  string  $sign_key  The secret key to sign with. You should most certainly provide this!
	 * @return string
	 */
	public static function signedSerialize($data, $sign_key = 'orb_util_sign_key')
	{
		$ser = base64_encode(serialize($data));
		$ser = rtrim($ser, '=');
		$md5 = md5($sign_key . $ser);

		// the :b64: part is so if in the future we change the encoding method,
		// the unserialize method below can be backwards compat by reading the b64 label
		return $md5 . ':' . $ser;
	}



	/**
	 * Unserialized a signed serialized string.
	 *
	 * @see Orb_Util::signedSeriaize()
	 * @param  mixed   $string    The string you want to unserialize
	 * @param  string  $sign_key  The secret key it was signed with. You should most certainly provide this!
	 * @return mixed
	 * @throws Exception
	 */
	public static function signedUnserialize($string, $sign_key = 'orb_util_sign_key')
	{
		$md5 = substr($string, 0, 32);
		$ser = substr($string, 33);

		$md5_check = md5($sign_key . $ser);
		if ($md5 != $md5_check) {
			throw new \Exception('Invalid data or sign key.');
		}

		return @unserialize(@base64_decode($ser));
	}



	/**
	 * Create a new object and pass $args as arguments to the constructor.
	 * Same as callUserConstructor but this takes an array of arguments instead.
	 *
	 * @param string $classname  The class to instantiate
	 * @param array  $args       Args to pass to the constructor
	 * @return $classname
	 */
	public static function callUserConstructorArray($classname, array $args)
	{
		$args = array_values($args);

		switch (count($args)) {
			// Most constructors wont take any more than a handful arguments
			case 0:  $obj = new $classname(); break;
			case 1:  $obj = new $classname($args[0]); break;
			case 2:  $obj = new $classname($args[0], $args[1]); break;
			case 3:  $obj = new $classname($args[0], $args[1], $args[2]); break;
			case 4:  $obj = new $classname($args[0], $args[1], $args[2], $args[3]); break;
			case 5:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4]); break;
			case 6:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]); break;
			case 7:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]); break;
			case 8:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]); break;
			case 9:  $obj = new $classname($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]); break;

			// But if there's more, fallback on reflection
			default:
				$ref = new \ReflectionClass($classname);
				$obj = $ref->newInstanceArgs($args);
				break;
		}

		return $obj;
	}



	/**
	 * Create a new object and pass arguments to the constructor.
	 *
	 * @param string $classname  The class to instantiate
	 * @param mixed  $param...   Parameters to call the constructor with
	 * @return $classname
	 */
	public static function callUserConstructor($classname)
	{
		$args = func_get_args();
		array_shift($args); // get rid of $classname

		return self::callUserConstructorArray($classname, $args);
	}



	/**
	 * An integer that is guarenteed to be unique for this one request.
	 * It's simply a global counter.
	 *
	 * @return int
	 */
	public static function requestUniqueId()
	{
		static $x = 0;

		return ++$x;
	}


	/**
	 * A unique string based on time and a random number, plus the requestUniqueId.
	 *
	 * @return string
	 */
	public static function requestUniqueIdString($prefix = 'id')
	{
		$str = $prefix . '_' . substr(time(), -4) . '_' . self::requestUniqueId();

		return $str;
	}



	/**
	 * Generate a random security token using some secret.
	 *
	 * @param string  $secret   A secret to encode the token with.
	 * @param int     $timeout  How long (seconds) is the token valid for? 0 disables
	 * @return string
	 */
	public static function generateStaticSecurityToken($secret, $timeout = 0)
	{
		if ($timeout) {
			// rand is so we never give the exact real time the token was made
			// since we have to put that in plaintext
			$expire_time = time() + $timeout + mt_rand(1, 10);
			$expire_time_enc = base_convert($expire_time, 10, 36);
		} else {
			$expire_time = 0;
			$expire_time_enc = 0;
		}

		$rand_str = Strings::random(10, Strings::CHARS_ALPHA_I);

		$token = $expire_time_enc . '-' . $rand_str . '-' . sha1($secret . $expire_time_enc . $rand_str);

		return $token;
	}



	/**
	 * Check a security token to see if its valid.
	 *
	 * @param string $token   The token to check
	 * @param string $secret  The same secret used to create the token
	 * @return bool
	 */
	public static function checkStaticSecurityToken($token, $secret)
	{
		// Check to make sure its a valid format
		if (substr_count($token, '-') != 2) {
			return false;
		}

		list($expire_time_enc, $rand_str, $hash) = explode('-', $token, 3);

		// Check the hash first
		$check_hash = sha1($secret . $expire_time_enc . $rand_str);

		if ($check_hash != $hash) {
			return false;
		}

		// Check the time now
		if ($expire_time_enc != '0') {
			$expire_time = base_convert($expire_time_enc, 36, 10);
			if (time() > $expire_time) {
				return false;
			}
		}

		return true;
	}



	/**
	 * Get all the parts of a classname (i.e., split up by namespace).
	 *
	 * @param mixed $obj_or_classname An object or string classname
	 * @return array
	 */
	public static function getClassnameParts($obj_or_classname)
	{
		$classname = $obj_or_classname;
		if (is_object($classname)) {
			$classname = get_class($classname);
		}

		return explode('\\', $classname);
	}


	/**
	 * Get the namespace of a class
	 *
	 * @return string
	 */
	public static function getClassNamespace($obj_or_classname)
	{
		$parts = self::getClassnameParts($obj_or_classname);
		array_pop($parts);

		return implode('\\', $parts);
	}



	/**
	 * Get the base name of a class. That is, the classname itself without the full
	 * namespace path.
	 *
	 * @param mixed $obj
	 * @return string
	 */
	public static function getBaseClassname($obj)
	{
		$parts = self::getClassnameParts($obj);
		return array_pop($parts);
	}



	/**
	 * Create a UUIDv4 string.
	 *
	 * @return string
	 */
	public static function uuid4()
	{
		$bits = self::randomData(16);

		$time_low = bin2hex(substr($bits, 0, 4));
		$time_mid = bin2hex(substr($bits, 4, 2));

		$time_hi_and_version = bin2hex(substr($bits, 6, 2));
		$time_hi_and_version = hexdec($time_hi_and_version);
		$time_hi_and_version = $time_hi_and_version >> 4;
		$time_hi_and_version = $time_hi_and_version | 0x4000;

		$clock_seq_hi_and_reserved = bin2hex(substr($bits, 8, 2));
		$clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
		$clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
		$clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

		$node = bin2hex(substr($bits,10, 6));

		return sprintf(
			'%08s-%04s-%04x-%04x-%012s',
			$time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node
		);
	}


	/**
	 * Converts a hex string to binary (opposite of bin2hex).
	 *
	 * @see http://php.net/manual/en/function.hex2bin.php
	 * @param string $hex_string
	 * @return string
	 */
	public static function hex2bin($hex_string)
	{
		// PHP 5.4
		if (function_exists('hex2bin')) {
			return @hex2bin($hex_string);
		}

		$len = strlen($hex_string);
		$bin_string = '';

        $pos = 0;
        while($pos < $len) {
			$bin_string .= pack("H*", substr($hex_string, $pos, 2));
            $pos += 2;
        }

        return $bin_string;
	}



	/**
	 * Generate random bytes.
	 *
	 * @param int $len
	 * @return string
	 */
	public static function randomData($len = 250)
	{
		$data = '';
		if (function_exists('openssl_random_pseudo_bytes')) {
			$data = openssl_random_pseudo_bytes($len);
		} else {
			$fp = @fopen('/dev/urandom','rb');
			if ($fp !== false) {
				$data = fread($fp, $len);
				fclose($fp);
			} else {
				// Fallback on just rand
				for($x=0; $x < $len; $x++){
					$data .= chr(mt_rand(0, 255));
				}
			}
		}

		return $data;
	}



	/**
	 * This takes an array of numbers, and encodes them as an alpha string (0-26 as a-z).
	 *
	 * @param array $parts
	 * @return string
	 */
	public static function encodeNumberSegments(array $parts, $alphabet = 'base36')
	{
		// This works by encoding the numbers as strings,
		// and then prefixing each number in a final string
		// by the length (num chars) its encoded form is.
		// So CBC, B means the first number is 2 digits long, so the
		// decoder reads 'BC', and decodes it into an integer part.
		// Then repeat. This works because the signifier digit (the first C)
		// is always 1 digit long, which allows for 26-digit numbers (huge!).

		$enc_numbers = array();
		foreach ($parts as $num) {
			$enc_numbers[] = self::baseEncode($num, $alphabet);
		}

		$enc_string = array();
		foreach ($enc_numbers as $enc_num) {
			$len = strlen($enc_num);
			$len_enc = self::baseEncode($len, $alphabet);
			$enc_string[] = "{$len_enc}{$enc_num}";
		}

		return implode('', $enc_string);
	}



	/**
	 * Decodes an array of integers from encodeNumberSegments().
	 *
	 * @param  $encoded_string
	 * @return int[]
	 */
	public static function decodeNumberSegments($encoded_string, $alphabet = 'base36')
	{
		$encoded_string = strtolower($encoded_string);

		// Must be A-Z only
		if (!preg_match('#^[a-z0-9]+$#', $encoded_string)) {
			return array();
		}

		$parts = array();
		$len = strlen($encoded_string);
		$pos = 0;
		$state = 0; // 0=sig, 1=num
		$read_len = 0;

		while ($pos < $len) {
			if ($state == 0) {
				$read_len = self::baseDecode($encoded_string[$pos], $alphabet);
				$state = 1;
				$pos++;
			} elseif ($state == 1) {

				$read = '';
				for ($i = 0; $i < $read_len; $i++) {
					$read .= $encoded_string[$pos];
					$pos++;
					if ($pos > $len) return array(); // invalid
				}

				$num = self::baseDecode($read, $alphabet);
				$parts[] = $num;

				$state = 0;
				$read_len = 0;
			}
		}

		return $parts;
	}


	/**
	 * Gets a numerically indexed array (suitable for call user func) by using
	 * named parameters from $func_refl and values from options.
	 *
	 * For example: function example($hello, $world);
	 * With options: array('world' => 1, 'hello' => 2, 'blah' => 'unrelated')
	 * Thie method returns: array(2, 1)
	 *
	 * @param \ReflectionFunctionAbstract $func_refl
	 * @param array $options
	 * @return array
	 */
	public static function getFunctionParamsFromArray(\ReflectionFunctionAbstract $func_refl, array $options)
	{
		$ret = array();

		$params = $func_refl->getParameters();
		foreach ($params as $param) {
			$name = $param->getName();
			if (isset($options[$name])) {
				$ret[] = $options[$name];
			} else {
				if ($param->isDefaultValueAvailable()) {
					$ret[] = $param->getDefaultValue();
				} else {
					// We have to stop now since we cant go any further
					break;
				}
			}
		}

		return $ret;
	}


	/**
	 * Get the filename a class is defined in
	 *
	 * @param string|\ReflectionClass $classname
	 * @return string
	 */
	public static function getClassFilename($classname)
	{
		if ($classname instanceof \ReflectionClass) {
			$relf = $classname;
		} else {
			$refl = new \ReflectionClass($classname);
		}

		return $refl->getFileName();
	}


	/**
	 * @static
	 * @param $var
	 * @return string
	 */
	public static function debugVar($var, $d = 0)
	{
		if (is_object($var)) {
			if (method_exists($var, '__tostring')) {
				return str_repeat("\t", $d) . "[" . get_class($var) . ":" . $var->__tostring() . "]";
			} else {
				return str_repeat("\t", $d) . "[" . get_class($var) . "]";
			}
		} else if (is_array($var)) {
			$str = array();
			$str[] = str_repeat("\t", $d) . "array(";
			foreach ($var as $k => $v) {
				$str[] = str_repeat("\t", $d+1) . "$k: " . self::debugVar($v, $d + 1);
			}
			$str[] = str_repeat("\t", $d) . ")";
			return implode("\n", $str);
		} else {
			return str_repeat("\t", $d) . $var;
		}
	}
}
