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
 * Helps fetch stuff about the server/environment
 *
 * @static
 */
class Env
{
	/**
	 * Static class
	 */
	private function __construct() {}

	/**
	 * Return 'upload_max_filesize' size in bytes
	 *
	 * @return int
	 */
	public static function getMaxUploadSize()
	{
		$size = @ini_get('upload_max_filesize');
		if (!$size) {
			return 0;
		}

		return Numbers::parseIniSize($size);
	}


	/**
	 * Return 'post_max_size' size in bytes
	 *
	 * @return int
	 */
	public static function getMaxPostSize()
	{
		$size = @ini_get('post_max_size');
		if (!$size) {
			return 0;
		}

		return Numbers::parseIniSize($size);
	}


	/**
	 * Get the size in bytes of the effective maximum upload size.
	 *
	 * Upload size is determined by the smallest of these three settings:
	 * - upload_max_filesize
	 * - post_max_size
	 * - memory_limit (just because you need memory to accept the file)
	 *
	 * @return int
	 */
	public static function getEffectiveMaxUploadSize()
	{
		$min = min(self::getMaxUploadSize(), self::getMaxPostSize());

		$mem = self::getMemoryLimit();
		if ($mem != -1) {
			$min = min($min, $mem / 3);
		}

		return $min;
	}


	/**
	 * Return 'memory_limit' size in bytes or -1 if there is no limit
	 *
	 * @return int
	 */
	public static function getMemoryLimit()
	{
		$size = @ini_get('memory_limit');
		if ($size == -1) {
			return -1;
		}

		return Numbers::parseIniSize($size);
	}


	/**
	 * Get phpinfo() as a string
	 *
	 * @return string
	 */
	public static function getPhpInfo()
	{
		ob_start();
		phpinfo();
		$phpinfo = ob_get_clean();

		return $phpinfo;
	}


	/**
	 * Gets the path to the laoded php.ini file by scanning phpinfo
	 *
	 * @return false|string
	 */
	public static function getPhpIniPath()
	{
		if (self::isFunctionDisabled('phpinfo')) {
			return false;
		}

		ob_start();
		phpinfo();
		$phpinfo = ob_get_clean();

		return self::getPhpIniPathFromInfo($phpinfo);
	}


	/**
	 * Get php.ini path from the phpinfo HTML string
	 *
	 * @param $phpinfo
	 * @return false|string
	 */
	public static function getPhpIniPathFromInfo($phpinfo)
	{
		$phpinfo = html_entity_decode(strip_tags($phpinfo), ENT_QUOTES);

		if (preg_match('#^Loaded Configuration File (.*?)$#m', $phpinfo, $m)) {
			$path = $m[1];
			$path = str_replace('=>', '', $path);
			$path = trim($path);
			return $path;
		}

		return false;
	}


	/**
	 * Check if a function has been disabled in php.ini with 'disable_functions'
	 *
	 * @param string $func_name
	 * @return string
	 */
	public static function isFunctionDisabled($func_name)
	{
		$func_name = strtolower($func_name);

		$disabled = self::getDisabledFunctions();
		return isset($disabled[$func_name]);
	}


	/**
	 * Check if a class has been disabled in php.ini with 'disable_classes'
	 *
	 * @param string $class_name
	 * @return bool
	 */
	public static function isClassDisabled($class_name)
	{
		$class_name = strtolower($class_name);
		$class_name = trim($class_name, '\\');

		$disabled = self::getDisabledClasses();
		return isset($disabled[$class_name]);
	}


	/**
	 * Check to see if two phpinfo's appear to be the same.
	 * This does a string check but ignores meaningless differences like request time.
	 *
	 * @param string $phpinfo1
	 * @param string $phpinfo2
	 */
	public static function isSamePhpInfo($phpinfo1, $phpinfo2, &$mutated = null)
	{
		$cleaner = function($str) {
			$str = preg_replace("#(\r|\r\n|\n)#", "\n", $str);

			$pos1 = strpos($str, 'phpinfo()');
			$pos2 = strpos($str, 'This program makes use of the Zend Scripting Language Engine');

			$str = substr($str, $pos1, $pos2);
			$str = trim($str);
			$str = preg_replace('#\s+$#m', '', $str);

			return $str;
		};

		$phpinfo1 = $cleaner($phpinfo1);
		$phpinfo2 = $cleaner($phpinfo2);

		$mutated = array(0 => $phpinfo1, 1 => $phpinfo2);

		return $phpinfo1 == $phpinfo2;
	}


	/**
	 * True if the current OS is Windows
	 *
	 * @return bool
	 */
	public static function isWindows()
	{
		return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
	}


	/**
	 * Get the upload temp directory.
	 *
	 * Returns null when the tmp dir is invalid. For example,
	 * if the tmp dir is configured to be outside of open_basedir restrictions, then this
	 * is a sysadmin error that we cant fix and attachmetns just wont work.
	 *
	 * @return string|null
	 */
	public static function getUploadTempDir()
	{
		$dirname = ini_get('upload_tmp_dir');

		if ($dirname) {
			$dirname = @realpath($dirname);
		}

		if (!$dirname) {
			$dirname = @realpath(@sys_get_temp_dir());
		}

		if (!$dirname) {
			$dirname = null;
		}

		return $dirname;
	}


	/**
	 * Get an array of disabled functions
	 *
	 * @return array
	 */
	public static function getDisabledFunctions()
	{
		static $functions = null;

		if ($functions === null) {
			$functions = array();
			$list = @ini_get('disable_functions') . ',' . @ini_get('suhosin.executor.func.blacklist');
			$list = explode(',', $list);

			foreach ($list as $f) {
				$f = trim($f);
				if ($f) {
					$f = strtolower($f);
					$functions[$f] = $f;
				}
			}
		}

		return $functions;
	}


	/**
	 * Get an array of disabled classes
	 *
	 * @return array
	 */
	public static function getDisabledClasses()
	{
		static $classes = null;

		if ($classes === null) {
			$classes = array();
			$list = @ini_get('disable_classes');
			$list = explode(',', $list);

			foreach ($list as $c) {
				$c = trim($c);
				if ($c) {
					$c = strtolower($c);
					$c = trim($c, '\\');

					$classes[$c] = $c;
				}
			}
		}

		return $classes;
	}


	/**
	 * Get the max input vars for a request.
	 *
	 * This is the min between max_input_vars, suhosin.post.max_vars and suhosin.request.max_vars
	 *
	 * @return int
	 */
	public static function getMaxPostVars()
	{
		$vals = array(
			(int)ini_get('max_input_vars'),
			(int)ini_get('suhosin.post.max_vars'),
			(int)ini_get('suhosin.request.max_vars')
		);

		$min = null;
		foreach ($vals as $v) {
			if (!$v) continue;

			if ($min === null) {
				$min = $v;
			} elseif ($v < $min) {
				$min = $v;
			}
		}

		return $min;
	}


	/**
	 * Get the max input vars for a GET request.
	 *
	 * This is the min between max_input_vars, suhosin.get.max_vars and suhosin.request.max_vars
	 *
	 * @return int
	 */
	public static function getMaxGetVars()
	{
		$vals = array(
			(int)ini_get('max_input_vars'),
			(int)ini_get('suhosin.get.max_vars'),
			(int)ini_get('suhosin.request.max_vars')
		);

		$min = null;
		foreach ($vals as $v) {
			if (!$v) continue;

			if ($min === null) {
				$min = $v;
			} elseif ($v < $min) {
				$min = $v;
			}
		}

		return $min;
	}
}
