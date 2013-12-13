<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// | HEADER REPLACE
// +-------------------------------------------------------------+
// | $Id: global.php 6619 2010-03-03 05:59:14Z chroder $
// +-------------------------------------------------------------+
// | File Details:
// | - Global initialization.
// | - This is run by every file, including email gateway
// +-------------------------------------------------------------+

/*********************************
* PHP Check
*********************************/

// check we have PHP v5.2.0
if (version_compare('5.2.0', PHP_VERSION) > 1) {
	die('<strong>Fatal Error:</strong> ' . DP_NAME . ' requires PHP v5.2.0 or greater.');
}

/*********************************
* GOOGLE ACCELERATOR
*********************************/

if (strpos($_SERVER['HTTP_X_MOZ'], 'prefetch') !== false) {
	// 404
	exit();
}

/*********************************
* DEFINE SOME PATHS
*********************************/

define('INC', ROOT . '/includes/');
define('INC_3RD_PARTY', INC . '3rdparty/');

// datastore can be defined inside config.php
if (!defined('DATASTORE')) {
	if (defined('MANAGED')) {
		define('DATASTORE', ROOT . '/datastore/' . CORPHELP_USERNAME . '/');
		
		if (!is_dir(DATASTORE)) {
		    @mkdir(DATASTORE);
		    @mkdir(DATASTORE . 'mail');
		    @mkdir(DATASTORE . 'graphs');
		    @mkdir(DATASTORE . 'manuals');
		}
	} // END_MANAGED

	if (!defined('MANAGED')) {
		define('DATASTORE', ROOT . '/datastore/');
	} // END_NOT_MANAGED
}

if (!defined('WEB')) {
	define('WEB', './');
}

define('LOC_IMAGES', WEB . 'images/');
define('LOC_INCLUDES', WEB . 'includes/');
define('LOC_CSS', WEB . 'includes/css/');
define('LOC_JAVASCRIPT', WEB . 'includes/javascript/');
define('LOC_3RD_PARTY', WEB . 'includes/3rdparty/');

// Misc paths

define('JPGRAPH', INC . '3rdparty/jpgraph/');
define('PEAR', INC . '3rdparty/pear/');

// Set include path
set_include_path(INC . '3rdparty/' . PATH_SEPARATOR . PEAR . PATH_SEPARATOR . JPGRAPH . PATH_SEPARATOR . get_include_path());

// "Downgrade" locale to a basic acceptable value.
// We dont use PHP locale features
// We do our own timezone offsets etc, so we can just set a UTC timezone
@setlocale(LC_CTYPE, 'C');
date_default_timezone_set('UTC');

/*********************************
* TURN OFF REGISTER GLOBALS
*********************************/

require_once(INC . 'classes/class_Request.php');
$request = new Request();

unset($parsevars);
$parsevars = array(
	'GLOBALS',
	'_COOKIE',
	'_REQUEST',
	'_POST',
	'_GET',
	'_ENV',
	'_SERVER',
	'_FILES',
	'request',
	'parsevars',
	'LOAD_PLUGINS',
);

if (defined('MANAGED')) {
	$parsevars[] = 'managed_config';
	$parsevars[] = 'MANDB';
} // END_MANAGED

/*
	callback is used to get any data from start of script we need (e.g. for template caching).
	we only add this varialble if we explicitly have set USING_CALLBACK define function
	the variable is safe because we unset($callback) before the define
*/

if (defined('USING_CALLBACK')) {
	$parsevars[] = 'callback';
}

// unset all other variables created by register_globals on
if (is_array($GLOBALS)) {

	// functionalised due to bug in PHP
	function deglobalise($parsevars) {

		foreach ($GLOBALS AS $key => $var) {
			if (!in_array($key, $parsevars) AND $key != 'var' AND $key != 'key') {
				unset($GLOBALS[$key]);
			}
		}
	}
	deglobalise($parsevars);

} else {
	die('<strong>Fatal Error:</strong> Invalid URL');
}

unset($var, $parsevars, $key);

/*********************************
* Load Language
*********************************/

//require_once(INC . '3rdparty/gettext/gettext.php');
//require_once(INC . 'functions/locale_functions.php');

/*********************************
* Load Profiling class
*********************************/

//require_once(INC . 'classes/class_Profile.php');

/*********************************
* Auto-loader
*********************************/

//require_once(ROOT . '/includes/Orb/Loader.php');
//Orb_Loader::loadMap(ROOT . '/includes/data/classpaths.php');
//Orb_Loader::register();


/*********************************
* Get IP Address
*********************************/

$ipaddress = false;
if (defined('ENV_IP_VARIABLE')) {
	$ipaddress = $_SERVER[ENV_IP_VARIABLE];

	if (!$ipaddress) {
		$ipaddress = $_ENV[ENV_IP_VARIABLE];
	}
}

if (!$ipaddress) {
	$ipaddress = $_SERVER['REMOTE_ADDR'];
}
define('IPADDRESS', trim(preg_replace('#^([^,]+)(,.*)?#', '$1', $ipaddress)));

$alt_ip = false;

if (isset($_SERVER['HTTP_CLIENT_IP'])) {
	$alt_ip = $_SERVER['HTTP_CLIENT_IP'];
}

if (!$alt_ip AND isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip_arr = array();

	if (preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $ip_arr)) {
		foreach($ip_arr[0] AS $ip) {
			if (!preg_match("#^(10|172\.16|192\.168)\.#", $ip)) {
				$alt_ip = $ip;
				break;
			}
		}
	}
}

if (!$alt_ip AND isset($_SERVER['HTTP_FROM'])) {
	$alt_ip = $_SERVER['HTTP_FROM'];
}

if (!$alt_ip) {
	$alt_ip = $_SERVER['REMOTE_ADDR'];
}

define('ALTIPADDRESS', $alt_ip);

/*********************************
* Get script path, filename, filepath
*********************************/

if ($_SERVER['REQUEST_URI'] OR $_ENV['REQUEST_URI']) {
	$script_path = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_ENV['REQUEST_URI'];
} else	{
	if ($_SERVER['PATH_INFO'] OR $_ENV['PATH_INFO']) {
		$script_path = $_SERVER['PATH_INFO'] ? $_SERVER['PATH_INFO'] : $_ENV['PATH_INFO'];
	} elseif ($_SERVER['REDIRECT_URL'] OR $_ENV['REDIRECT_URL']) {
		$script_path = $_SERVER['REDIRECT_URL'] ? $_SERVER['REDIRECT_URL'] : $_ENV['REDIRECT_URL'];
	} else {
		$script_path = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_ENV['PHP_SELF'];
	}

	if ($_SERVER['QUERY_STRING'] OR $_ENV['QUERY_STRING']) {
		$script_path .= '?' . ($_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : $_ENV['QUERY_STRING']);
	}
}

$quest_pos = strpos($script_path, '?');
if ($quest_pos !== false) {
	$script = urldecode(substr($script_path, 0, $quest_pos));
	$script_path = $script . substr($script_path, $quest_pos);
}
else {
	$script_path = urldecode($script_path);
}

// full url path
define('PATH', preg_replace('/s=[a-z0-9]{32}?&?/', '', $script_path));
$path = htmlspecialchars(PATH);

// server location of file
define('FILEPATH', realpath($_SERVER['SCRIPT_FILENAME'] ? $_SERVER['SCRIPT_FILENAME'] : $_ENV['SCRIPT_FILENAME']));

// Path to the root of all DeskPRO file
define('FILEPATH_ROOT', realpath(dirname(__FILE__) . '/../'));

// just the php file name
if (preg_match('/([A-Z0-9a-z_-]*.php)/', PATH, $matches)) {
	define('FILENAME', $matches['1']);
} else {
	define('FILENAME', 'index.php');
}

// request protocol
if (!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != 'off') {
	define('REQUEST_PROTOCOL', 'HTTPS');
} else {
	define('REQUEST_PROTOCOL', 'HTTP');
}

// Request method
define('REQUEST_METHOD', strtoupper($_SERVER['REQUEST_METHOD']));

/*********************************
* OS
*********************************/

if (substr(php_uname(), 0, 7) == "Windows") {
	define('IS_WIN', 1);
}

/***************
* Hooks
***************/

if (!defined('DISABLE_HOOKS')) {
//	require_once(ROOT . '/includes/classes/class_DpHooks.php');
} else {
//	require_once(ROOT . '/includes/classes/class_DpHooksEmpty.php');
}

/*********************************
* Some key files
*********************************/


//require_once(INC . 'debug.php');

if (defined('MANAGED')) {
//require_once(INC . 'license_managed.php');
} // END_MANAGED

if (!defined('MANAGED')) {
//require_once(INC . 'license.php');
} // END_NOT_MANAGED


/*********************************
* See if we want to load plugins
*********************************/

if (is_array($LOAD_PLUGINS) AND !defined('DISABLE_HOOKS')) {
	foreach ($LOAD_PLUGINS as $plugin_name) {
		//$plugin_path = INC . "plugins/$plugin_name/load_plugins.php";

		if (is_file($plugin_path)) {
			@include_once($plugin_path);
		}
	}
}

/*********************************
* Shutdown func to handle errors
*********************************/

if (!defined('NOSHUTDOWNFUNCTIONS')) {

	function handle_last_error() {

		if (!function_exists('error_get_last')) {
			return false;
		}

		if (!function_exists('log_error')) {
			return false;
		}

		$error = error_get_last();

		$save_which = array(
			E_ERROR, E_PARSE, E_CORE_ERROR,
			E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING
		);

		if (!$error OR !in_array($error['type'], $save_which)) {
			return false;
		}

		if (defined('GATEWAYZONE')) {
			@gatewayError($error['type'], $error['message'], $error['file'], $error['line']);
		} else {
			@log_error('php', $error['message'] . "\nLine {$error['line']} :: {$error['file']}");
		}
	}

	register_shutdown_function('handle_last_error');
}


/*********************************
* Assetion options
* - On during debug mode, off during prod
*********************************/

if (defined('DESKPRO_DEBUG_DEVELOPERMODE') AND DESKPRO_DEBUG_DEVELOPERMODE) {
	assert_options(ASSERT_ACTIVE, 1);
	assert_options(ASSERT_WARNING, 1);
} else {
	assert_options(ASSERT_ACTIVE, 0);
	assert_options(ASSERT_WARNING, 0);
}


