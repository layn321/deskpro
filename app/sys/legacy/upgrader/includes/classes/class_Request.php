<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

/**
 * admin header generator
 *
 * @package DeskPRO
 */

define('XML_HTMLSAX3', PEAR . 'XML/HTMLSax3/');
require_once (INC . '3rdparty/safehtml/safehtml.php');

/**
 * Request
 *
 * wrap request family objects and provide functions to access those
 *
 * @package DeskPRO
 * @version $Id: class_Request.php 6756 2010-03-17 04:09:33Z chroder $
 */
class Request
{

	var $get = array();

	var $post = array();

	var $cookie = array();

	var $request = array();



	function Request()
	{

		$this->get = $_GET;
		$this->post = $_POST;
		$this->cookie = $_COOKIE;

		// Force our order
		$this->request = array_merge($_GET, $_POST, $_COOKIE);

		/*********************************
		 * Deal with evil magic quotes
		 *********************************/

		// get rid of slashes in get / post / cookie data
		if (get_magic_quotes_gpc()) {

			$this->get = $this->stripslashesarray($this->get);
			$this->post = $this->stripslashesarray($this->post);
			$this->cookie = $this->stripslashesarray($this->cookie);
			$this->request = $this->stripslashesarray($this->request);

		}

		// Depreciated in PHP 5.3(thus @), gone in 6(thus function exists)
		if (function_exists('set_magic_quotes_runtime')) {
			@set_magic_quotes_runtime(0);
		}

	}



	/**
	 * get the full superglobal array, with XSS protection
	 *
	 * @access	public
	 *
	 * @param	string	which superglobal array
	 *
	 * @return	int	raw string
	 */
	function getAll($type = 'request')
	{
		return $this->xssclean($this->{$type});
	}



	/**
	 * get a raw variable from the superglobal. Only checks existance. This
	 * is the function that other functions should call to access the superglobals
	 *
	 * @access	private
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	int	raw string
	 */
	function getRaw($name, $type = 'request')
	{
		static $shortcuts = array('r' => 'request', 'p' => 'post', 'g' => 'get', 'c' => 'cookie');
		$type = strtolower($type);

		if (isset($shortcuts[$type])) {
			$type = $shortcuts[$type];
		}

		if (is_array($name)) {

			if (!is_array($this->{$type}[$name[0]])) {
				return null;
			}

			$part = $this->{$type}[$name[0]];

			unset($name[0]);

			foreach ($name as $var) {

				if (!isset($part[$var])) {
					return null;
				}

				$part = $part[$var];

			}

			return $part;

		} else {

			if (!isset($this->{$type}[$name])) {
				return null;
			}

			return $this->{$type}[$name];

		}
	}



	/**
	 * Returns true if the variable is set in the superglobal, false if it is not.
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	int	raw string
	 */
	function getIsset($name, $type = 'request')
	{
		$val = $this->getRaw($name, $type);

		return is_null($val) ? false : true;
	}



	/**
	 * get a cleaned number from a superglobal
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return		int	cleaned number
	 */
	function getNumber($name, $type = 'request')
	{
		$number = $this->getRaw($name, $type);

		return (int)$number;
	}



	/**
	 * Get a cleaned float number from a superglobal
	 *
	 * @param string $name Variable name
	 * @param string $type Which superglobal array
	 */
	function getFloat($name, $type = 'request')
	{
		$number = $this->getRaw($name, $type);

		return (float)$number;
	}



	/**
	 * get a XSS cleaned string from a superglobal
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	int	XSS cleaned string
	 */
	function getString($name, $type = 'request')
	{
		$string = (string)$this->getRaw($name, $type);

		return $this->xssclean($string);
	}



	/**
	 * get 1/0
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	int	1 or 0 for true or false
	 */
	function getBool($name, $type = 'request')
	{
		$bool = $this->getRaw($name, $type);

		return (int)((bool)$bool);
	}



	/**
	 * get a XSS cleaned array from a superglobal
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	int	XSS cleaned array
	 */
	function getArray($name, $type = 'request')
	{
		$array = $this->getRaw($name, $type);

		if (!is_array($array)) {
			$array = array();
		}

		return $this->xssclean($array);

	}



	/**
	 * get a XSS cleaned array from a superglobal
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	int	XSS cleaned array
	 */
	function getArraySafe($name, $type = 'request')
	{
		$array = $this->getRaw($name, $type);

		if (!is_array($array)) {
			$array = array();
		}

		$array = $this->utf8clean($array);

		array_walk($array, 'dp_html');
		array_walk($array, 'trim');

		return $array;

	}



	/**
	 * get a XSS cleaned string or array from a superglobal
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	mixed	XSS cleaned string or array
	 */
	function getArrayString($name, $type = 'request')
	{
		if (!$arraystring = $this->getRaw($name, $type)) {
			return false;
		}

		return $this->xssclean($arraystring);

	}



	/**
	 * get a XSS cleaned string or array from a superglobal
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	mixed	XSS cleaned string or array
	 */
	function getArrayStringSafe($name, $type = 'request')
	{
		if (!$arraystring = $this->getRaw($name, $type)) {
			return false;
		}

		if (is_array($arraystring)) {
			array_walk($arraystring, 'dp_html');
			array_walk($arraystring, 'trim');
		} else {
			$arraystring = trim(dp_html($arraystring));
		}

		return $arraystring;

	}



	/**
	 * Get a XSS-cleaned integer or integer array from a superglobal
	 *
	 * @access	public
	 *
	 * @param	string	Variable name
	 * @param	string	Superglobal name
	 *
	 * @return	mixed	XSS-cleaned integer or integer array
	 */
	function getArrayNumber($name, $type = 'request')
	{
		if (!$number = $this->getRaw($name, $type)) {
			return false;
		}

		if (!is_array($number)) {
			return intval($number);
		}

		foreach ($number AS $key => $value) {
			if (is_array($value)) {
				$array["$key"] = $this->getArrayNumber($value);
			} else {
				$array["$key"] = intval($value);
			}
		}

		return $array;
	}



	/**
	 * get a HTML cleaned string from a superglobal
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	int	html cleaned string
	 */
	function getSafeString($name, $type = 'request')
	{
		$string = (string)$this->getRaw($name, $type);

		$string = $this->utf8clean($string);

		// remove all < and >
		return trim(dp_html($string));
	}



	/**
	 * Return a raw string with no HTML or XSS cleaning. Note that this still
	 * does utf8 cleaning. If you need a truly raw value, use getRaw().
	 *
	 * @param string $name The variable name
	 * @param string $type Which superglobal array
	 * @return string The string
	 */
	function getRawString($name, $type = 'request')
	{
		$string = (string)$this->getRaw($name, $type);

		$string = $this->utf8clean($string);

		return $string;
	}



	/**
	 * set a variable if one has not been set
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	variable to set
	 * @param	string	which superglobal array
	 */
	function setDefault($name, $value, $type = 'request')
	{
		if (!$this->getIsset($name, $type)) {
			$this->setVariable($name, $value, $type);
		}
	}



	/**
	 * unsets a variable by setting it to '', which for our purposes is like removing it
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	variable to unset
	 * @param	string	which superglobal array
	 */
	function setEmpty($name, $type = 'request')
	{
		$this->setVariable($name, '', $type);
	}



	/**
	 * set a variable
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	variable to set
	 * @param	string	which superglobal array
	 */
	// needs to work with ARRAYS ie [x][y] = z
	function setVariable($name, $value, $type = 'request')
	{

		if (is_array($name)) {

			// $elem starts as the main superglobal
			$elem = $this->{$type};

			$size = count($name);
			for($i = 0; $i < $size; $i++) {

				// if not set, need to create this as an array
				if (!isset($elem[$name[$i]]) or !is_array($elem[$name[$i]])) {
					$elem[$name[$i]] = array();
				}

				// now elem is the n'th sub element in the array matrix.
				// might be array, might be leaf.
				$elem = $elem[$name[$i]];
			}

			$elem = $value;

		} else {
			$this->{$type}[$name] = $value;
		}
	}



	/**
	 * set a variable
	 *
	 * @access	public
	 *
	 * @param	array	array of keys/values to replace with
	 */
	function setFullArray($array, $type = 'request')
	{
		if (!is_array($array)) {
			return;
		}

		foreach ($array as $key => $var) {
			$this->setVariable($key, $var, $type);
		}
	}



	/**
	 * forces a string to be an array by doing $var[] = $value
	 * is mainly used when we want to allow a specific field e.g. category=1 as wel
	 * as category[]=1&category[]=2
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 */
	function setArray($name, $type = 'request')
	{
		if (!is_array($this->getRaw($name, $type))) {
			$this->setVariable($name, array($this->getRaw($name, $type)), $type);
		}
	}



	/**
	 * append string. Is like doing $var .=
	 *
	 * @access	public
	 *
	 * @param	string	variable name
	 * @param	string	variable to set
	 * @param	string	which superglobal array
	 */
	function appendVariable($name, $value, $type = 'request')
	{
		$this->{$type}[$name] .= $value;
	}



	/**
	 * Clean a variable from XSS. Does not prevent HTML injection,
	 * but acts as an extra defense against XSS atttacks.
	 *
	 * @access	private
	 *
	 * @param	$name	variable name (string/array)
	 *
	 * @return	string	cleaned string / array
	 */
	function xssclean($var)
	{

		/********************
		 * An array
		 ********************/

		if (is_array($var)) {
			foreach ($var as $var_key => $var_part) {
				$var_key = $this->xssclean($var_key);
				$var[$var_key] = $this->xssclean($var_part);
			}


		/********************
		 * A normal var
		 ********************/

		} else {

			$var = $this->utf8clean($var);
			$normal_var = (is_numeric($var) or !$var or preg_match('#^[a-zA-Z0-9@\. _\-]*$#', $var));

			if (!$normal_var) {
				$safehtml = new SafeHTML();
				$var = $safehtml->parse($var);
			}

		}

		return $var;
	}



	/**
	 * Make sure a UTF8 string is safe.
	 *
	 * @param mixed $var The variable (string or array of strings)
	 * @return mixed
	 */
	function utf8clean($var)
	{
		// TODO: 3.6 handling utf8
		return $var;

		
//		/********************
//		 * An array
//		 ********************/
//
//		if (is_array($var)) {
//			foreach ($var as $var_key => $var_part) {
//				$var_key = utf8_get_safe($var_key);
//				$var[$var_key] = $this->utf8clean($var_part);
//			}
//
//
//		/********************
//		 * A normal var
//		 ********************/
//
//		} else {
//			$var = utf8_get_safe($var);
//		}
//
//		return $var;
	}



	/**
	 * strip slashes
	 *
	 * @access	private
	 *
	 * @param	string	variable name
	 * @param	string	which superglobal array
	 *
	 * @return	 int	raw string
	 */
	function stripslashesarray($array)
	{

		if (is_array($array)) {

			foreach ($array as $key => $val) {
				if (is_array($val)) {
					$array["$key"] = $this->stripslashesarray($val);
				} elseif (is_string($val)) {
					if (get_cfg_var('magic_quotes_sybase')) {
						$array["$key"] = str_replace("''", "'", $val);
					} else {
						$array["$key"] = stripslashes($val);
					}
				}
			}
		}

		return $array;
	}
}

