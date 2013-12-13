<?php if (!defined('DP_ROOT')) exit('No access');
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
 * Loads config. After this call, $DP_CONFIG is available.
 */
function dp_load_config()
{
	global $DP_CONFIG;
	static $has_loaded = false;

	if ($has_loaded) {
		return;
	}

	if (!is_array($DP_CONFIG)) {
		if (file_exists(DP_CONFIG_FILE)) {
			require DP_CONFIG_FILE;

			if (!isset($DP_CONFIG) || !is_array($DP_CONFIG)) {
				$DP_CONFIG = array();
			}

			if (!isset($DP_CONFIG['db'])) $DP_CONFIG['db'] = array();
			if (!isset($DP_CONFIG['db']['host']))      $DP_CONFIG['db']['host']      = defined('DP_DATABASE_HOST')     ? DP_DATABASE_HOST     : 'localhost';
			if (!isset($DP_CONFIG['db']['user']))      $DP_CONFIG['db']['user']      = defined('DP_DATABASE_USER')     ? DP_DATABASE_USER     : 'YOUR_DATABASE_USER';
			if (!isset($DP_CONFIG['db']['password']))  $DP_CONFIG['db']['password']  = defined('DP_DATABASE_PASSWORD') ? DP_DATABASE_PASSWORD : 'YOUR_DATABASE_PASS';
			if (!isset($DP_CONFIG['db']['dbname']))    $DP_CONFIG['db']['dbname']    = defined('DP_DATABASE_NAME')     ? DP_DATABASE_NAME     : 'YOUR_DATABASE_NAME';
			if (!isset($DP_CONFIG['technical_email'])) $DP_CONFIG['technical_email'] = defined('DP_TECHNICAL_EMAIL')   ? DP_TECHNICAL_EMAIL   : '';
		} else {
			if (!isset($DP_CONFIG) || !is_array($DP_CONFIG)) {
				$DP_CONFIG = array();
				$DP_CONFIG['NO_CONFIG'] = true;
				$DP_CONFIG['db'] = array();
				$DP_CONFIG['db']['host']      = 'localhost';
				$DP_CONFIG['db']['user']      = 'YOUR_DATABASE_USER';
				$DP_CONFIG['db']['password']  = 'YOUR_DATABASE_PASS';
				$DP_CONFIG['db']['dbname']    = 'YOUR_DATABASE_NAME';
				$DP_CONFIG['technical_email'] = '';
			}
		}
	}

	if (!defined('DP_BUILD_TIME')) {
		if (file_exists(DP_ROOT.'/sys/config/build-time.php')) {
			require(DP_ROOT.'/sys/config/build-time.php');
		} else {
			define('DP_BUILD_TIME', 1323444089); // would be used by someone who hasnt built yet
		}
	}
	if (!defined('DP_BUILD_NUM')) {
		if (file_exists(DP_ROOT.'/sys/config/build-num.php')) {
			require(DP_ROOT.'/sys/config/build-num.php');
		} else {
			define('DP_BUILD_NUM', 0); // would be used by someone who isnt using default distro
		}
	}
}


/**
 * Loads a PHP array file into config
 *
 * @param string $file
 * @param string $key
 */
function dp_load_file_into_config($file, $key)
{
	global $DP_CONFIG;
	if (isset($DP_CONFIG[$key])) {
		return;
	}

	if (is_file($file)) {
		$data = include($file);
		if (is_array($data)) {
			$DP_CONFIG[$key] = $data;
		} else {
			$DP_CONFIG[$key] = array();
		}
	} else {
		$DP_CONFIG[$key] = array();
	}
}


/**
 * Get a value from config using dot notation
 *
 * @param string $key
 * @param null $default
 * @return mixed
 */
function dp_get_config($path, $default = null)
{
	global $DP_CONFIG;

	dp_load_config();
	$array = $DP_CONFIG;

	// Special value handling
	if ($path == 'is_installed_flag') {
		return file_exists(dp_get_data_dir() . '/is_installed.dat');
	}

	// If its not a path at all, we can do a simple lookup
	if (strpos($path, '.') === false) {
		return isset($array[$path]) ? $array[$path] : $default;
	}

	$parts = explode('.', $path);

	if (!$parts) {
		return $default;
	}

	$depth = 0;
	while ($key = array_shift($parts)) {
		if (!isset($array[$key])) {
			if ($depth == 0 && $key == 'instance_data') {
				dp_load_file_into_config(DP_ROOT.'/sys/config/instance-data.php', 'instance_data');
				$array = $DP_CONFIG;
			} else {
				return $default;
			}
		}

		$array = $array[$key];
		$depth++;
	}

	return $array;
}


/**
 * @return string
 */
function dp_get_os()
{
	static $os = null;

	if ($os === null) {
		if (strpos(strtoupper(PHP_OS), 'WIN') === 0) {
			$os = 'win';
		} elseif (strpos(strtoupper(PHP_OS), 'DARWIN') === 0) {
			$os = 'mac';
		} elseif (strpos(strtoupper(PHP_OS), 'FREEBSD') === 0) {
			$os = 'freebsd';
		} elseif (strpos(strtoupper(PHP_OS), 'LINUX') === 0) {
			$os = 'linux';
		} else {
			$os = PHP_OS;
		}
	}

	return $os;
}


/**
 * @return string
 */
function dp_get_data_dir()
{
	dp_load_config();

	global $DP_CONFIG;
	if (isset($DP_CONFIG['dir_data']) && $DP_CONFIG['dir_data']) {
		$dir_data = $DP_CONFIG['dir_data'];
	} else {
		$dir_data = DP_WEB_ROOT . DIRECTORY_SEPARATOR . 'data';
	}

	if (!is_dir($dir_data)) {
		@mkdir($dir_data, 0777, true);
		@chmod($dir_data, 0777);
	}

	return $dir_data;
}


/**
 * @return string
 */
function dp_get_debug_dir()
{
	$dir = dp_get_data_dir() . DIRECTORY_SEPARATOR . 'debug';

	if (!is_dir($dir)) {
		@mkdir($dir, 0777, true);
		@chmod($dir, 0777);
	}

	return $dir;
}


/**
 * @return string
 */
function dp_get_log_dir()
{
	$dir = dp_get_data_dir() . DIRECTORY_SEPARATOR . 'logs';

	if (!is_dir($dir)) {
		@mkdir($dir, 0777, true);
		@chmod($dir, 0777);
	}

	return $dir;
}


/**
 * @return string
 */
function dp_get_backup_dir()
{
	$dir = dp_get_data_dir() . DIRECTORY_SEPARATOR . 'backups';

	if (!is_dir($dir)) {
		@mkdir($dir, 0777, true);
		@chmod($dir, 0777);
	}

	return $dir;
}


/**
 * @return string
 */
function dp_get_blob_dir()
{
	$dir = dp_get_data_dir() . DIRECTORY_SEPARATOR . 'files';

	if (!is_dir($dir)) {
		@mkdir($dir, 0777, true);
		@chmod($dir, 0777);
	}

	return $dir;
}


/**
 * @return string
 */
function dp_get_tmp_dir()
{
	$dir = dp_get_data_dir() . DIRECTORY_SEPARATOR . 'tmp';

	if (!is_dir($dir)) {
		@mkdir($dir, 0777, true);
		@chmod($dir, 0777);
	}

	return $dir;
}


/**
 * Check to see if some action should be throttled based on a filesystem
 * marker.
 *
 * @param string $id
 * @param int $min_time
 * @return bool
 */
function dp_should_throttle_action($id, $min_time)
{
	$file = dp_get_data_dir() . '/last-' . $id . '.dat';
	if (!file_exists($file)) {
		@file_put_contents($file, time());
		return false;
	}

	$last = (int)file_get_contents($file);
	if ($last > time()-$min_time) {
		return true;
	}

	@file_put_contents($file, time());
	return false;
}


/**
 * Try to locate a binary in the current path
 *
 * Based on Symfony\Component\Process\ExecutableFinder
 *
 * @param $name
 * @param array|null $use_suffixes
 * @return mixed|null|string
 */
function dp_find_binary($name, array $use_suffixes = null)
{
	if (!$use_suffixes) {
		$use_suffixes = array('', '.exe', '.bat', '.cmd', '.com');
	}

	$is_windows = (0 === stripos(PHP_OS, 'win'));

	if (ini_get('open_basedir')) {
		$searchPath = explode(PATH_SEPARATOR, getenv('open_basedir'));
		$dirs = array();
		foreach ($searchPath as $path) {
			if (is_dir($path)) {
				$dirs[] = $path;
			} else {
				$file = str_replace(dirname($path), '', $path);
				if ($file == $name && is_executable($path)) {
					return $path;
				}
			}
		}
	} else {
		$dirs = explode(PATH_SEPARATOR, getenv('PATH') ? getenv('PATH') : getenv('Path'));
	}

	$suffixes = DIRECTORY_SEPARATOR == '\\' ? (getenv('PATHEXT') ? explode(PATH_SEPARATOR, getenv('PATHEXT')) : $use_suffixes) : array('');
	foreach ($suffixes as $suffix) {
		foreach ($dirs as $dir) {
			if (is_file($file = $dir.DIRECTORY_SEPARATOR.$name.$suffix) && ($is_windows || is_executable($file))) {
				return $file;
			}
		}
	}

	return null;
}


/**
 * Get the path to the PHP CLI binary. Returns null if we can't locate it and php_path isn't configured.
 *
 * @return string
 */
function dp_get_php_path($test = false)
{
	static $path = null;

	if ($path === null) {
		if (dp_get_config('php_path')) {
			$path = dp_get_config('php_path');
		}
		if (!$path) {
			if (defined('PHP_BINARY')) {
				$path = PHP_BINARY;
			} else {
				$GLOBALS['DP_PHP_PATH_GUESSED'] = true;
				$path = dp_find_binary('php');
			}
		}

		if (!$path) {
			$path = false;
		} else {
			$path = escapeshellarg($path);
		}
	}

	static $pass_test = null;
	if ($path && $test && $pass_test === null) {
		$out = null;
		$ret = null;

		// php -v: PHP 5.3.10 (cli) (built: May 18 2012 10:07:25) etc
		exec($path . " -v", $out, $ret);

		$out = is_array($out) ? implode("\n", $out) : (string)$out;
		if (!$ret || stripos($out, 'php') !== false) {
			$pass_test = true;
		} else {
			$pass_test = false;
			$path = false;
		}
	}

	return $path;
}

/**
 * @param string $script
 * @param string $params
 * @return string
 */
function dp_get_php_command($script, $params = '')
{
	$cmd = dp_get_php_path() . ' '
		. escapeshellarg($script) . ' '
		. (defined('DP_PHP_BIN_ARGS') ? DP_PHP_BIN_ARGS . ' ' : '')
		. $params;
	return $cmd;
}


/**
 * Was the path to the PHP CLI guessed?
 *
 * @return bool
 */
function dp_is_php_path_guessed()
{
	dp_get_php_path(false);
	if (isset($GLOBALS['DP_PHP_PATH_GUESSED']) && $GLOBALS['DP_PHP_PATH_GUESSED']) {
		return true;
	}

	return false;
}


/**
 * Get the path to the mysqldump binary. Returns null if we can't locate it and mysqldump_path isn't configured.
 *
 * @return string
 */
function dp_get_mysqldump_path($test = false)
{
	static $path = null;

	if ($path === null) {
		if (dp_get_config('mysqldump_path')) {
			$path = dp_get_config('mysqldump_path');
		}
		if (!$path) {
			$path = dp_find_binary('mysqldump');
		}

		if (!$path) {
			$path = false;
		} else {
			$path = escapeshellarg($path);
		}
	}

	static $pass_test = null;
	if ($path && $test && $pass_test === null) {
		$out = null;
		$ret = null;

		// mysqldump (no args): Usage: mysqldump [OPTIONS] database [tables]  etc
		exec($path, $out, $ret);

		$out = is_array($out) ? implode("\n", $out) : (string)$out;
		if (!$ret || stripos($out, 'mysqldump') !== false) {
			$pass_test = true;
		} else {
			$pass_test = false;
			$path = false;
		}
	}

	return $path;
}


/**
 * Get the path to the mysql binary. Returns null if we can't locate it and mysql_path isn't configured.
 *
 * @return string
 */
function dp_get_mysql_path($test = false)
{
	static $path = null;

	if ($path === null) {
		if (dp_get_config('mysql_path')) {
			$path = dp_get_config('mysql_path');
		}
		if (!$path) {
			$path = dp_find_binary('mysql');
		}

		if (!$path) {
			$path = false;
		} else {
			$path = escapeshellarg($path);
		}
	}

	static $pass_test = null;
	if ($path && $test && $pass_test === null) {
		$out = null;
		$ret = null;

		// mysql --help: Lots of stuff but we can find mysql
		exec($path . " --help", $out, $ret);

		$out = is_array($out) ? implode("\n", $out) : (string)$out;
		if (!$ret || stripos($out, 'mysql') !== false) {
			$pass_test = true;
		} else {
			$pass_test = false;
			$path = false;
		}
	}

	return $path;
}


/**
 * Gets the users IP address. This will try to return the actual IP of the user.
 * If the client machine is a trusted proxy, we will try to get the forwarded IP address
 * of the real user.
 *
 * @return string|false
 */
function dp_get_user_ip_address()
{
	if (dp_trust_proxy_data() && $ip = dp_get_proxied_ip_address()) {
		return $ip;
	}

	return dp_get_client_ip_address();
}


/**
 * Gets the users IP address from behind possible proxies.
 *
 * @return string|null
 */
function dp_get_proxied_ip_address()
{
	$ip_address = null;
	if ($ip_address !== null) return $ip_address ? $ip_address : null;

	$validate_ip = function($ip) {
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	};

	if (!empty($_SERVER['HTTP_CLIENT_IP']) && $validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
		$ip_address = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		foreach ($iplist as $ip) {
			if ($validate_ip($ip)) {
				$ip_address = $ip;
			}
		}
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED']) && $validate_ip($_SERVER['HTTP_X_FORWARDED'])) {
		$ip_address = $_SERVER['HTTP_X_FORWARDED'];
	} elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
		$ip_address = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $validate_ip($_SERVER['HTTP_FORWARDED_FOR'])) {
		$ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
	} elseif (!empty($_SERVER['HTTP_FORWARDED']) && $validate_ip($_SERVER['HTTP_FORWARDED'])) {
		$ip_address = $_SERVER['HTTP_FORWARDED'];
	} else {
		$ip_address = false;
	}

	return $ip_address ? $ip_address : null;
}


/**
 * Gets the real client IP (e.g., the machine that actually made the request).
 * To get the users IP address, try dp_get_user_ip_address().
 *
 * @return string|null
 */
function dp_get_client_ip_address()
{
	static $ip_address = null;
	if ($ip_address !== null) return $ip_address ? $ip_address : null;

	if (!empty($_SERVER['REMOTE_ADDR'])) {
		$ip_address = $_SERVER['REMOTE_ADDR'];
	} else if (!empty($_ENV['REMOVE_ADDR'])) {
		$ip_address = $_ENV['REMOTE_ADDR'];
	} else {
		$ip_address = false;
	}

	return $ip_address ? $ip_address : null;
}


/**
 * Checks to see if we should trust proxy data, generally by checking the client IP based on a whitelist
 * of trusted networks.
 *
 * @return bool
 */
function dp_trust_proxy_data()
{
	static $do_trust = null;

	if ($do_trust !== null) {
		return $do_trust;
	}

	// CLI, there is no IP
	if (php_sapi_name() == 'cli') {
		$do_trust = false;
		return false;
	}

	$trust_option = isset($GLOBALS['DP_CONFIG']['trust_proxy_data']) && $GLOBALS['DP_CONFIG']['trust_proxy_data'] ? $GLOBALS['DP_CONFIG']['trust_proxy_data'] : null;
	if (!$trust_option) {
		$do_trust = false;
		return false;
	}

	#------------------------------
	# If we have an array, it means we have
	# a set of IPs we trust
	#------------------------------

	if (is_array($trust_option)) {

		try {
			$client_ip = \Leth\IPAddress\IP\Address::factory(dp_get_client_ip_address());
		} catch (\Exception $e) {
			$do_trust = false;
			return false;
		}

		foreach ($trust_option as $ip_range) {
			// $ip_range is a file reference: @/path/to/file
			if (is_string($ip_range) && $ip_range[0] == '@') {
				$ip_range = substr($ip_range, 1);

				// A relative file starts with ~
				if ($ip_range[0] == '~') {
					$ip_range = DP_ROOT . substr($ip_range, 1);
				}

				$ip_range = include($ip_range);

				foreach ($ip_range as $check_ip_range) {
					try {
						$ip_range = \Leth\IPAddress\IP\NetworkAddress::factory($check_ip_range);
						if ($ip_range->encloses_address($client_ip)) {
							$do_trust = true;
							break 2;
						}
					} catch (\Exception $e) {}
				}
			} else {
				try {
					$ip_range = \Leth\IPAddress\IP\NetworkAddress::factory($ip_range);
					if ($ip_range->encloses_address($client_ip)) {
						$do_trust = true;
						break;
					}
				} catch (\Exception $e) {}
			}
		}

	#------------------------------
	# A trust option of any other is just
	# cast to a bool
	#------------------------------

	} else {
		$do_trust = (bool)$trust_option;
	}

	return $do_trust;
}