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
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage InstallBundle
 */

namespace Application\InstallBundle\Install;

use Application\DeskPRO\DBAL\Connection;
use Orb\Log\Logger;

use Application\DeskPRO\App;
use Orb\Util\Env;
use Orb\Util\Strings;

class ServerChecks
{
	const MODE_NORMAL = 'normal';
	const MODE_CRON = 'cron';

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger = null;

	/**
	 * @var array
	 */
	protected $server_errors = array();

	/**
	 * @var bool
	 */
	protected $has_fatal_server_errors = false;

	/**
	 * @var bool
	 */
	protected $has_fatal_db_errors = false;

	/**
	 * @var string
	 */
	protected $mode = 'normal';

	/**
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Set check mode
	 *
	 * @param string $mode
	 */
	public function setMode($mode)
	{
		$this->mode = $mode;
	}

	protected function getLogger()
	{
		if ($this->logger === null) {
			$this->logger = new \Orb\Log\Logger();
		}

		return $this->logger;
	}


	/**
	 * Are there any errors?
	 *
	 * @return bool
	 */
	public function hasErrors()
	{
		if ($this->server_errors) {
			return true;
		}

		return false;
	}


	/**
	 * Are there any fatal errors?
	 *
	 * @return bool
	 */
	public function hasFatalErrors()
	{
		foreach ($this->server_errors as $e) {
			if ($e['level'] == 'fatal') {
				return true;
			}
		}

		return false;
	}


	/**
	 * Are there any fatal server (pre-db checks) errors?
	 *
	 * @return bool
	 */
	public function hasFatalServerErrors()
	{
		return $this->has_fatal_server_errors;
	}


	/**
	 * Are there any fatal DB (post server) errors?
	 *
	 * @return bool
	 */
	public function hasFatalDbErrors()
	{
		return $this->has_fatal_db_errors;
	}


	/**
	 * Are there any non-fatal errors?
	 *
	 * @return bool
	 */
	public function hasNonFatalErrors()
	{
		foreach ($this->server_errors as $e) {
			if ($e['level'] != 'fatal') {
				return true;
			}
		}

		return false;
	}


	/**
	 * Check if a speciifc error occurred
	 *
	 * @param string $type
	 * @return bool
	 */
	public function hasErrorType($type)
	{
		return isset($this->server_errors[$type]);
	}


	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->server_errors;
	}


	/**
	 * Get only fatal errors
	 *
	 * @return array
	 */
	public function getFatalErrors()
	{
		$ret = array();
		foreach ($this->server_errors as $k => $e) {
			if ($e['level'] == 'fatal') {
				$ret[$k] = $e;
			}
		}

		return $ret;
	}


	/**
	 * Get only non-fatal errors
	 *
	 * @return array
	 */
	public function getNonFatalErrors()
	{
		$ret = array();
		foreach ($this->server_errors as $k => $e) {
			if ($e['level'] != 'fatal') {
				$ret[$k] = $e;
			}
		}

		return $ret;
	}


	/**
	 * @return array
	 */
	public function getErrorTypes()
	{
		return array_keys($this->server_errors);
	}


	/**
	 * Runs through basic server checks
	 *
	 * @return bool True if all okay, or false if there are errors
	 */
	public function checkServer($type = 'all')
	{
		#------------------------------
		# php_version
		#------------------------------

		if ($type == 'php_version' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking PHP version >= 5.3.2", Logger::DEBUG);
			if (deskpro_install_check_version()) {
				$this->getLogger()->log("[OK] PHP version of " . phpversion() . " is OK", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "[FATAL] Install PHP 5.3.2 or newer. You currently have " . phpversion();
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['php_version'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);

				// Lets not go any further in case the php version is so old something in this script fails
				return false;
			}
		}

		#------------------------------
		# config
		#------------------------------

		if ($type == 'config' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking config file", Logger::DEBUG);
			if (file_exists(DP_CONFIG_FILE)) {
				require_once(DP_CONFIG_FILE);

				if (defined('DATABASE_HOST')) {
					$this->has_fatal_server_errors = true;
					$msg = "/config.php exists but appears to contain configuration from a DeskPRO v3 file";
					$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
					$this->server_errors['config_dp3_values'] = array(
						'message' => $msg,
						'level' => 'fatal'
					);
				} else if (!defined('DP_DATABASE_HOST') || !defined('DP_DATABASE_USER') || !defined('DP_DATABASE_PASSWORD') || !defined('DP_DATABASE_NAME')) {
					$this->has_fatal_server_errors = true;
					$msg = "/config.php exists but does not contain the required database values";
					$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
					$this->server_errors['config_values'] = array(
						'message' => $msg,
						'level' => 'fatal'
					);
				} else if (!defined('DP_TECHNICAL_EMAIL') || !DP_TECHNICAL_EMAIL || !strpos(DP_TECHNICAL_EMAIL, '@')) {
					$this->has_fatal_server_errors = true;
					$msg = "/config.php exists but does not contain the required DP_TECHNICAL_EMAIL value";
					$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
					$this->server_errors['config_technical_email'] = array(
						'message' => $msg,
						'level' => 'recommended'
					);
				} else {
					$this->getLogger()->log("[OK] config file exists and contains required values", Logger::DEBUG);
				}
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "/config.php file is missing";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['config'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# php_functions
		#------------------------------

		if ($type == 'php_functions' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking for disabled functions", Logger::DEBUG);

			$check = array(
				'escapeshellarg',
				'exec',
				'passthru',
				'chdir',
				'proc_open'
			);

			$has_disabled = array();
			foreach ($check as $n) {
				if (\Orb\Util\Env::isFunctionDisabled($n)) {
					$has_disabled[] = $n;
				}
			}

			if (!$has_disabled) {
				$this->getLogger()->log("[OK] No common disabled functions", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$has_disabled_str = implode(', ', $has_disabled);
				$msg = "Edit php.ini and remove the disabled_functions line (Found these disabled functions: $has_disabled_str)";
				$this->getLogger()->log($msg, Logger::INFO);
				$this->server_errors['php_functions'] = array(
					'message' => $msg,
					'level' => 'recommended',
					'has_disabled' => $has_disabled,
					'has_disabled_str' => $has_disabled_str,
				);
			}
		}

		#------------------------------
		# libxml_ext
		#------------------------------

		if ($type == 'libxml_ext' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking for libxml extension", Logger::DEBUG);
			if (extension_loaded('libxml')) {
				$this->getLogger()->log("[OK] libxml extension installed", Logger::DEBUG);

				$phpinfo = Env::getPhpInfo();
				if (
					($libxml_version = Strings::extractRegexMatch('#libXML Compiled Version => ([0-9.]+)\b#', $phpinfo))
					|| ($libxml_version = Strings::extractRegexMatch('#libXML Compiled Version\s*</td><td[^>]*>\s*([0-9.]+)\s*</td>#', $phpinfo))
				) {
					if (!version_compare('2.7', $libxml_version, '<=')) {
						$this->has_fatal_server_errors = true;
						$msg = "PHP is built against a very old version of libxml. This can cause errors in parsing HTML. You need to upgrade libxml and re-build PHP.";
						$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
						$this->server_errors['libxml_version'] = array(
							'message' => $msg,
							'level' => 'fatal',
							'detected_version' => $libxml_version
						);
					}
				}


			} else {
				$this->has_fatal_server_errors = true;
				$msg = "Install and enable the libxml extension";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['libxml_ext'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# json_ext
		#------------------------------

		if ($type == 'json_ext' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking for json extension", Logger::DEBUG);
			if (function_exists('json_encode')) {
				$this->getLogger()->log("[OK] json extension installed", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "Install and enable the json extension";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['json_ext'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# session_ext
		#------------------------------

		if ($type == 'session_ext' || ($type == 'all' && $this->mode != 'cron')) {
			$this->getLogger()->log("[CHECK] Checking for session extension", Logger::DEBUG);
			if (function_exists('session_start')) {
				$this->getLogger()->log("[OK] session extension installed", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "Install and enable the session extension";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['session_ext'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# image_manip
		#------------------------------

		if ($type == 'image_manip' || ($type == 'all' && $this->mode != 'cron')) {
			$this->getLogger()->log("[CHECK] Checking for an image manipulation extension", Logger::DEBUG);
			if (deskpro_install_check_image_manip()) {
				$this->getLogger()->log("[OK] An image manipulation extension is installed", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "Install and enable the Imagick, Gmagick or GD extension";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['image_manip'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# ctype_ext
		#------------------------------

		if ($type == 'ctype_ext' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking for ctype extension", Logger::DEBUG);
			if (function_exists('ctype_alpha')) {
				$this->getLogger()->log("[OK] ctype session installed", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "Install and enable the ctype extension";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['ctype_ext'] = array(
					'message' => "Install and enable the ctype extension",
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# tokenizer_ext
		#------------------------------

		if ($type == 'tokenizer_ext' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking for tokenizer extension", Logger::DEBUG);
			if (function_exists('token_get_all')) {
				$this->getLogger()->log("[OK] tokenizer session installed", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "Install and enable the tokenizer extension";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['tokenizer_ext'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# pdo_ext
		#------------------------------

		if ($type == 'pdo_ext' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking for PDO extension", Logger::DEBUG);
			if (deskpro_install_check_pdo()) {
				$this->getLogger()->log("[OK] PDO installed", Logger::DEBUG);

				$this->getLogger()->log("[CHECK] Checking for PDO_MySQL", Logger::DEBUG);
				if (deskpro_install_check_pdo_mysql()) {
					$this->getLogger()->log("[OK] PDO_MySQL installed", Logger::DEBUG);
				} else {
					$this->has_fatal_server_errors = true;
					$msg = "You need to install the MySQL PDO driver";
					$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
					$this->server_errors['pdo_mysql_ext'] = array(
						'message' => $msg,
						'level' => 'fatal'
					);
				}
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "Install and enable the PDO/PDO_MySQL extension";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['pdo_ext'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# openssl
		#------------------------------

		if ($type == 'openssl_ext' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking if the OpenSSL extension is enabled", Logger::DEBUG);
			if (extension_loaded('openssl')) {
				$this->getLogger()->log("[OK] OpenSSL installed", Logger::DEBUG);
			} else {
				$msg = "We recommend installing the OpenSSL extension so you can use resources that require a secure connection such as Gmail or Google Apps, Facebook and Twitter.";
				$this->getLogger()->log("$msg", Logger::INFO);
				$this->server_errors['openssl_ext'] = array(
					'message' => $msg,
					'level' => 'recommended'
				);
			}
		}

		#------------------------------
		# apc_check
		#------------------------------

		if ($type == 'apc_check' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking if APC is enabled", Logger::DEBUG);
			if ((function_exists('apc_store') && ini_get('apc.enabled') || extension_loaded('wincache'))) {
				$this->getLogger()->log("[OK] APC store installed", Logger::DEBUG);
			} else {
				$msg = "We recommend installing the APC extension for PHP to dramatically improve performance";
				$this->getLogger()->log("$msg", Logger::INFO);
				$this->server_errors['apc_check'] = array(
					'message' => $msg,
					'level' => 'recommended'
				);
			}
		}

		#------------------------------
		# magic_quotes_check
		#------------------------------

		if (function_exists('get_magic_quotes_gpc')) {
			if ($type == 'magic_quotes_gpc_check' || $type == 'all') {
				$this->getLogger()->log("[CHECK] Checking if magic_quotes_gpc is enabled", Logger::DEBUG);
				if (!get_magic_quotes_gpc()) {
					$this->getLogger()->log("[OK] magic_quotes_gpc is disabled", Logger::DEBUG);
				} else {
					$msg = "We recommend disabling the `magic_quotes_gpc` setting in your php.ini file.";
					$this->getLogger()->log("$msg", Logger::INFO);
					$this->server_errors['magic_quotes_gpc_check'] = array(
						'message' => $msg,
						'level' => 'recommended'
					);
				}
			}
		}

		#------------------------------
		# iconv_ext
		#------------------------------

		if ($type == 'iconv_ext' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking iconv is installed", Logger::DEBUG);
			if (function_exists('iconv') || function_exists('mb_convert_encoding')) {
				$this->getLogger()->log("[OK] iconv or mb_convert_encoding is installed", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "You must install and enabled the iconv or mb_convert_encoding extension";
				$this->getLogger()->log("$msg", Logger::INFO);
				$this->server_errors['iconv_ext'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# dom_ext
		#------------------------------

		if ($type == 'dom_ext' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking dom is installed", Logger::DEBUG);
			if (extension_loaded('dom')) {
				$this->getLogger()->log("[OK] dom is installed", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "You must install and enabled the dom extension";
				$this->getLogger()->log("$msg", Logger::INFO);
				$this->server_errors['dom_ext'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# memory_limit
		#------------------------------

		if ($type == 'memory_limit' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking memory limit", Logger::DEBUG);
			if (deskpro_install_check_memory_limit()) {
				$this->getLogger()->log("[OK] Memory limit is okay", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "DeskPRO needs PHP's memory_limit option to be at least 128 MB";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['memory_limit'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# upload_tmp_dir
		#------------------------------

		if ($type == 'upload_tmp_dir' || ($type == 'all' && $this->mode != 'cron')) {
			$this->getLogger()->log("[CHECK] Checking for writable upload_tmp_dir", Logger::DEBUG);
			if (\Orb\Util\Env::getUploadTempDir() && is_writable(\Orb\Util\Env::getUploadTempDir())) {
				$this->getLogger()->log("[OK] upload_tmp_dir is writable", Logger::DEBUG);
			} else {
				$msg = "Install and enable the session extension";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['upload_tmp_dir'] = array(
					'message' => $msg,
					'level' => 'recommended'
				);
			}
		}

		#------------------------------
		# data_write
		#------------------------------

		if ($type == 'data_write' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking if data directories are writable", Logger::DEBUG);
			$dir = dp_get_data_dir();
			if (deskpro_install_check_data_writable(dp_get_data_dir())) {
				$this->getLogger()->log("[OK] Data dirs are writable", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "The data directory and all sub-directories must exist and be writable (path: $dir).";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['data_write'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		#------------------------------
		# dp3_files
		#------------------------------

		if ($type == 'dp3_files' || $type == 'all') {
			$this->getLogger()->log("[CHECK] Checking to make sure DeskPRO v3 files are not present", Logger::DEBUG);
			if (!file_exists(DP_WEB_ROOT.'/newticket.php')) {
				$this->getLogger()->log("[OK] DeskPRO v3 files not here", Logger::DEBUG);
			} else {
				$this->has_fatal_server_errors = true;
				$msg = "[FATAL] It appears as though you installed the DeskPRO v4 files over a copy of DeskPRO v3. DeskPRO v4 is completely new and shares no common files with v3, and having v3 files in the same directory is a security risk. You should delete the directory and extract a fresh copy of DeskPRO v4.";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['dp3_files'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);
			}
		}

		if ($this->server_errors) {
			return false;
		}

		return true;
	}


	/**
	 * Checks the database to make sure details are correct and version etc is ok
	 *
	 * @param array $db_conf If null, will use config data and try to conect through App::getDb
	 * @return bool
	 */
	public function checkDatabase(array $db_conf = null, $should_be_empty = false)
	{
		// Dont attempt check if theres a PDO failure
		if ($this->hasErrorType('pdo_ext') || $this->hasErrorType('pdo_mysql_ext')) {
			return;
		}

		if ($db_conf) {
			$db_server = $db_conf['host'];
		} elseif (defined('DP_DATABASE_HOST')) {
			$db_server = DP_DATABASE_HOST;
		} elseif (isset($GLOBALS['DP_CONFIG']['db']['host'])) {
			$db_server = $GLOBALS['DP_CONFIG']['db']['host'];
		} else {
			$db_server = null;
		}

		#------------------------------
		# db_iis_localhost
		#------------------------------

		if ($db_server && \Orb\Util\Env::isWindows()) {
			$this->getLogger()->log("[CHECK] Checking if IIS and DB is connecing through localhost", Logger::DEBUG);
			if ($db_server != 'localhost') {
				$this->getLogger()->log("[OK] Not connecting through localhost", Logger::DEBUG);
			} else {

				// recommended for existing users to show notice in admin,
				// fatal during install
				$level = 'recommended';
				if (isset($GLOBALS['DP_IS_IMPORTING']) || (defined('DP_INTERFACE') && DP_INTERFACE == 'install')) {
					$level = 'fatal';
				}

				$this->has_fatal_server_errors = true;
				$msg = "Connecting through MySQL through 'localhost' causes very poor performance on Windows servers. Change the database server to 127.0.0.1 instead.";
				$this->getLogger()->log("$msg", Logger::INFO);
				$this->server_errors['db_win_localhost'] = array(
					'message' => $msg,
					'level' => $level
				);
			}
		}

		$this->getLogger()->log("[CHECK] Checking database connection", Logger::DEBUG);
		try {

			if ($db_conf) {
				$db_conf['driver'] = 'pdo_mysql';

				if (isset($db_conf['host']) && preg_match('#^(.*?):([0-9]+)$#', $db_conf['host'], $m)) {
					$db_conf['host'] = $m[1];
					$db_conf['port'] = $m[2];
				}

				$db = \Doctrine\DBAL\DriverManager::getConnection($db_conf);
				$db->connect();
			} else {
				$db = App::getDb();
				$db->connect();
			}

		} catch (\Exception $e) {
			$this->has_fatal_db_errors = true;
			$msg = "Connection failed: {$e->getMessage()}";
			$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
			$this->server_errors['db_connect'] = array(
				'message' => $msg,
				'level' => 'fatal'
			);

			return false;
		}

		if ($this->mode != 'cron') {
			$this->getLogger()->log("[CHECK] checking for innodb engine", Logger::DEBUG);
			$engines = App::getDb()->fetchAllKeyed("SHOW ENGINES", array(), 'Engine');
			if (!$engines || !isset($engines['InnoDB']) || $engines['InnoDB']['Support'] == 'NO') {
				$this->has_fatal_db_errors = true;
				$msg = "MySQL does not have the InnoDB engine enabled";
				$this->getLogger()->log("[FAIL] $msg", Logger::INFO);
				$this->server_errors['db_no_innodb'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);

				return false;
			} else {
				$this->getLogger()->log("[OK] innodb engine enabled", Logger::DEBUG);
			}

			$this->getLogger()->log("[CHECK] Checking mysql version is >= 5.0", Logger::DEBUG);
			$ver = $db->fetchColumn("SHOW VARIABLES LIKE 'version'", array(), 1);
			if (version_compare($ver, '5.0', '>=')) {
				$this->getLogger()->log("[OK] mysql version of $ver is okay", Logger::DEBUG);
			} else {
				$this->has_fatal_db_errors = true;
				$msg = "Install MySQL verson 5.0 or newer";
				$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
				$this->server_errors['db_version'] = array(
					'message' => $msg,
					'level' => 'fatal'
				);

				return false;
			}

			if ($should_be_empty) {
				$this->getLogger()->log("[CHECK] Checking for empty database", Logger::DEBUG);
				$tables = $db->fetchAll("SHOW TABLES");
				if ($tables) {
					$this->has_fatal_db_errors = true;
					$msg = "DeskPRO needs to be installed into an empty database";
					$this->getLogger()->log("[FATAL] $msg", Logger::INFO);
					$this->server_errors['db_not_empty'] = array(
						'message' => $msg,
						'level' => 'fatal'
					);

					return false;
				} else {
					$this->getLogger()->log("[OK] Database is empty", Logger::DEBUG);
				}
			}
		}

		return true;
	}


	/**
	 * @return bool
	 */
	public function hasDbErrors()
	{
		foreach ($this->server_errors as $k => $info) {
			if (strpos($k, 'db_') === 0) {
				return true;
			}
		}

		return false;
	}
}
