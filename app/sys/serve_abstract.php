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
 */

namespace DeskPRO\Kernel;

abstract class LoaderAbstract
{
	/**
	 * @var string
	 */
	protected $base_url;

	/**
	 * @var string
	 */
	protected $path_info;

	/**
	 * @var string
	 */
	protected $request_uri;

	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * @var array
	 */
	protected $settings;

	public function run()
	{
		#------------------------------
		# Normalize env
		#------------------------------

		setlocale(LC_CTYPE, 'C');
		date_default_timezone_set('UTC');
		ini_set('default_charset', 'UTF-8');

		if (!defined('ORB_STRINGS_UTF8_DIR')) {
			define('ORB_STRINGS_UTF8_DIR', DP_ROOT.'/vendor/php-utf8');
		}

		if (!defined('GEOIP_API_INC_PATH')) {
			define('GEOIP_API_INC_PATH', DP_ROOT.'/vendor/geoip-api');
		}

		spl_autoload_register(function($class) {
			switch ($class) {
				case 'Orb\\Util\\Util':
					require(DP_ROOT . '/src/Orb/Util/Util.php');
					return;
				case 'Orb\\Util\\Strings':
					require(DP_ROOT . '/src/Orb/Util/Strings.php');
					return;
			}

			if (strpos($class, 'Orb\\') === 0) {
				$path = DP_ROOT . '/src/' . str_replace('\\', '/', $class) . '.php';
				require($path);
			}
		});

		#------------------------------
		# Undo magic quotes
		#------------------------------

		// Check exists since its gone in PHP 5.4
		if (function_exists('get_magic_quotes_gpc')) {
			ini_set('magic_quotes_runtime', 0);

			if (get_magic_quotes_gpc()) {
				$clean_fn = function(&$v) {
					$v = stripslashes($v);
				};

				array_walk_recursive($_GET,     $clean_fn);
				array_walk_recursive($_POST,    $clean_fn);
				array_walk_recursive($_COOKIE,  $clean_fn);
				array_walk_recursive($_REQUEST, $clean_fn);
			}
		}

		#------------------------------
		# Load config
		#------------------------------

		global $DP_CONFIG;
		require_once DP_ROOT.'/sys/load_config.php';
		dp_load_config();

		#------------------------------
		# Serve 503 if helpdesk is offline
		#------------------------------

		if (is_file(dp_get_data_dir() . '/helpdesk-offline.trigger') || is_file(DP_WEB_ROOT.'/auto-update-is-running.trigger')) {
			header('HTTP/1.1 503 Service Unavailable');
			echo "Helpdesk is offline.";
			exit(1);
		}

		#------------------------------
		# Run appropriate action
		#------------------------------

		$this->runAction();

		if (session_id() != '') {
			session_write_close();
		}
	}


	/**
	 * @return mixed
	 */
	abstract protected function runAction();


	/**
	 * Handle a fatal exception
	 *
	 * @param \Exception $e
	 */
	protected function handleException(\Exception $e)
	{
		try {
			$container = $this->bootFullSystem();
		} catch (\Exception $e) {
			error_log("Error handling error: {$e->getMessage()}");
			echo "Error while processing error";
			exit(1);
		}

		KernelErrorHandler::handleException($e);

		header("HTTP/1.1 500 Internal Server Error");
		echo "There was an error while processing your request.";
		exit(1);
	}


	/**
	 * @return \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected function bootFullSystem($kernel_class = null)
	{
		static $container;

		if (!$container) {
			global $DP_CONFIG;
			$env = 'prod';
			$debug = false;

			if (isset($DP_CONFIG['debug']['dev']) && $DP_CONFIG['debug']['dev']) {
				$env = 'dev';
				$debug = true;
			}

			require DP_ROOT . '/sys/KernelBooter.php';
			\DeskPRO\Kernel\KernelBooter::bootstrapLib(true);

			if (!$kernel_class) {
				$kernel_class = 'DeskPRO\\Kernel\\UserKernel';
			}
			define('DP_INTERFACE', 'sys');

			$kernel = new $kernel_class($env, $debug);
			$kernel->boot();

			/** @var $container \Application\DeskPRO\DependencyInjection\DeskproContainer */
			$container = $kernel->getContainer();
		}

		return $container;
	}


	/**
	 * @return \PDO
	 */
	public function getPdo()
	{
		if ($this->pdo) {
			return $this->pdo;
		}

		global $DP_CONFIG;

		$port = '';
		if (isset($DP_CONFIG['db']['host']) && preg_match('#^(.*?):([0-9]+)$#', $DP_CONFIG['db']['host'], $m)) {
			$DP_CONFIG['db']['host'] = $m[1];
			$port = ";port={$m[2]};";
		}

		$this->pdo = new \PDO("mysql:dbname={$DP_CONFIG['db']['dbname']};host={$DP_CONFIG['db']['host']}$port", $DP_CONFIG['db']['user'], $DP_CONFIG['db']['password']);
		$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		$this->pdo->exec("SET sql_mode=''");
		$this->pdo->exec("SET NAMES 'UTF8'");

		return $this->pdo;
	}


	/**
	 * @return array
	 */
	public function getAllSettings()
	{
		if ($this->settings !== null) {
			return $this->settings;
		}

		$this->settings = array();

		$q = $this->getPdo()->prepare("
			SELECT name, value
			FROM settings
		");
		$q->execute();
		while ($row = $q->fetch(\PDO::FETCH_NUM)) {
			$this->settings[$row[0]] = $row[1];
		}

		return $this->settings;
	}


	/**
	 * @param string $name
	 * @param null $default
	 * @return null
	 */
	public function getSetting($name, $default = null)
	{
		if ($this->settings === null) {
			$this->getAllSettings();
		}

		return isset($this->settings[$name]) ? $this->settings[$name] : $default;
	}


	####################################################################################################################
	# Request Helpers
	####################################################################################################################

	/**
	 * @see \Symfony\Component\HttpFoundation\Request
	 */
	public function getPathInfo()
	{
		if ($this->path_info !== null) {
			return $this->path_info;
		}

		$baseUrl = $this->getBaseUrl();

		if (null === ($requestUri = $this->getRequestUri())) {
			return '/';
		}

		$pathInfo = '/';

		// Remove the query string from REQUEST_URI
		if ($pos = strpos($requestUri, '?')) {
			$requestUri = substr($requestUri, 0, $pos);
		}

		if ((null !== $baseUrl) && (false === ($pathInfo = substr(urldecode($requestUri), strlen(urldecode($baseUrl)))))) {
			// If substr() returns false then PATH_INFO is set to an empty string
			return '/';
		} elseif (null === $baseUrl) {
			return $requestUri;
		}

		$this->path_info = (string)$pathInfo;
		return $this->path_info;
	}


	/**
	 * @see \Symfony\Component\HttpFoundation\Request
	 */
	public function getBaseUrl()
	{
		if ($this->base_url !== null) {
			return $this->base_url;
		}

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$strpos = 'stripos';
			$filter = 'strtolower';
		} else {
			$strpos = 'strpos';
			$filter = function($in) { return $in; };
		}

		$filename = $filter(basename((isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : null)));

		if ($filter(basename((isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null))) === $filename) {
			$baseUrl = (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null);
		} elseif ($filter(basename((isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : null))) === $filename) {
			$baseUrl = (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : null);
		} elseif ($filter(basename((isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : null))) === $filename) {
			$baseUrl = (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : null); // 1and1 shared hosting compatibility
		} else {
			// Backtrack up the script_filename to find the portion matching
			// php_self
			$path    = (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '');
			$file    = (isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '');
			$segs    = explode('/', trim($file, '/'));
			$segs    = array_reverse($segs);
			$index   = 0;
			$last    = count($segs);
			$baseUrl = '';
			do {
				$seg     = $segs[$index];
				$baseUrl = '/'.$seg.$baseUrl;
				++$index;
			} while (($last > $index) && (false !== ($pos = $strpos($path, $baseUrl))) && (0 != $pos));
		}

		// Does the baseUrl have anything in common with the request_uri?
		$requestUri = $this->getRequestUri();

		if ($baseUrl && 0 === strpos($requestUri, $baseUrl)) {
			// full $baseUrl matches
			return $baseUrl;
		}

		if ($baseUrl && 0 === $strpos($requestUri, dirname($baseUrl))) {
			// directory portion of $baseUrl matches
			return rtrim(dirname($baseUrl), '/');
		}

		$truncatedRequestUri = $requestUri;
		if (($pos = strpos($requestUri, '?')) !== false) {
			$truncatedRequestUri = substr($requestUri, 0, $pos);
		}

		$basename = basename($baseUrl);
		if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
			// no match whatsoever; set it blank
			return '';
		}

		// If using mod_rewrite or ISAPI_Rewrite strip the script filename
		// out of baseUrl. $pos !== 0 makes sure it is not matching a value
		// from PATH_INFO or QUERY_STRING
		if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
			$baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
		}

		$this->base_url = rtrim($baseUrl, '/');
		return $this->base_url;
	}


	/**
	 * @see \Symfony\Component\HttpFoundation\Request
	 */
	public function getRequestUri()
    {
		if ($this->request_uri !== null) {
			return $this->request_uri;
		}

        $requestUri = '';

        if ((isset($_SERVER['X_REWRITE_URL']) ? $_SERVER['X_REWRITE_URL'] : null) && false !== stripos(PHP_OS, 'WIN')) {
            // check this first so IIS will catch
            $requestUri = (isset($_SERVER['X_REWRITE_URL']) ? $_SERVER['X_REWRITE_URL'] : null);
        } elseif ((isset($_SERVER['IIS_WasUrlRewritten']) ? $_SERVER['IIS_WasUrlRewritten'] : null) == '1' && (isset($_SERVER['UNENCODED_URL']) ? $_SERVER['UNENCODED_URL'] : null) != '') {
            // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
            $requestUri = (isset($_SERVER['UNENCODED_URL']) ? $_SERVER['UNENCODED_URL'] : null);
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null);
            // HTTP proxy reqs setup request uri with scheme and host [and port] + the url path, only use url path
            $schemeAndHttpHost = $this->getScheme().'://'.$this->getHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            // IIS 5.0, PHP as CGI
            $requestUri = (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : null);
            if ((isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null)) {
                $requestUri .= '?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null);
            }
        }

        $this->request_uri = $requestUri;
		return $this->request_uri;
    }

	/**
	 * @see \Symfony\Component\HttpFoundation\Request
	 */
	public function getScheme()
	{
		return $this->isSecure() ? 'https' : 'http';
	}

	/**
	 * @see \Symfony\Component\HttpFoundation\Request
	 */
	public function isSecure()
	{
		return (
			(strtolower((isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : null)) == 'on' || (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : null) == 1)
			||
			((isset($_SERVER['SSL_HTTPS']) ? $_SERVER['SSL_HTTPS'] : null) == 1)
		);
	}

	/**
	 * @see \Symfony\Component\HttpFoundation\Request
	 */
	public function getHttpHost()
	{
		$scheme = $this->getScheme();
		$port   = $this->getPort();

		if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
			return $this->getHost();
		}

		return $this->getHost().':'.$port;
	}

	/**
	 * @see \Symfony\Component\HttpFoundation\Request
	 */
	public function getPort()
	{
		return (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null);
	}

	/**
	 * @see \Symfony\Component\HttpFoundation\Request
	 */
	public function getHost()
	{
		if (!$host = (isset($_SERVER['HOST']) ? $_SERVER['HOST'] : null)) {
			if (!$host = (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null)) {
				$host = (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '');
			}
		}

		// Remove port number from host
		$host = preg_replace('/:\d+$/', '', $host);

		return trim($host);
	}

	public static function formatBacktrace(array $backtrace)
	{
		$trace = '';
		foreach($backtrace as $k=>$v){

			$line = "#$k ";

			if (isset($v['object'])) {
				$line .= get_class($v['object']) . "::";
			} elseif (isset($v['class'])) {
				$line .= $v['class'] . "::";
			}

			$line .= "{$v['function']}(";

			if (!empty($v['args'])) {
				$line .= self::varToString($v['args']);
			}

			$line .= ")";

			if (!empty($v['file'])) {
				$line .= " called at [{$v['file']}:{$v['line']}]";
			}

			$line .= "\n";

			$trace .= $line;
		}

		return $trace;
	}

	public static function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('[object](%s)', get_class($var));
        }
        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, self::varToString($v));
            }
            return sprintf("[array](%s)", implode(', ', $a));
        }
        if (is_resource($var)) {
            return '[resource]';
        }
        return str_replace("\n", '', var_export((string) $var, true));
    }
}