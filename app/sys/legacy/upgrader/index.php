<?php
define('DP_ROOT', realpath(__DIR__ . '/../../../'));
define('DP_WEB_ROOT', realpath(__DIR__ . '/../../../../'));
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);


##################################### START UP THE SYSTEM #####################################

/***************
* Define Upgrade Type
***************/

define('INSTALLER', 1);
define('UPGRADE_DEBUG', true);
define('UPGRADE_TYPE', 'shell');

/***************
* Check if we should be using this
***************/

if (!in_array(php_sapi_name(), array('cgi-fcgi', 'cgi', 'cli'))) {
	die('You should only run this file from shell');
}

/*******************************
* Try and extend length of time script runs, make sure gzip is off
*******************************/

@ini_set('zlib.output_compression', '0');
@set_time_limit(0);
@ignore_user_abort(1);
@ini_set('max_execution_time', 0);
@ini_set('xdebug.max_nesting_level', 10000);
@ini_set('display_errors', true);

$mem_limit = @ini_get('memory_limit');

if ($mem_limit == -1 || !$mem_limit) {
	$mem_limit = 0;
}

if ($mem_limit) {
	$mem_limit = trim($mem_limit);
	$last = strtoupper($mem_limit[strlen($mem_limit)-1]);

	// Already in bytes
	if (ctype_digit($last)) {
		return (int)$mem_limit;
	}

	$mem_limit = (int)$mem_limit;

	if ($last != 'G' && $last != 'M' && $last != 'K') {
		return 0;
	}

	switch($last) {
		case 'G':
			$mem_limit *= 1024;
		case 'M':
			$mem_limit *= 1024;
		case 'K':
			$mem_limit *= 1024;
	}
}

if ($mem_limit && $mem_limit != '-1' && $mem_limit < 134217728/* 128 MB */) {
	@ini_set('memory_limit', 134217728);
}

/***********
* Initiate System
***********/

define('CWD_DESKPRO', __DIR__ . '/');
define('ROOT', CWD_DESKPRO);

// web location
define('WEB', './../');

// no log errors
define('NO_LOG_ERROR', true);

// load global
require_once(CWD_DESKPRO . 'includes/global.php');

// set install path
define('INSTALL', ROOT);

/*******************************
* Include Files
*******************************/

require_once(INC . 'classes/database/mysql.php');
require_once(INC . 'classes/class_XMLDecode.php');
require_once(INC . 'functions/general_functions.php');
require_once(INC . 'functions/phpcompat_functions.php');
require_once(INC . 'functions/html_functions.php');
require_once(INC . 'functions/admin_tech_functions.php');
require_once(INC . 'functions/form_functions.php');
require_once(INC . 'classes/class_Content.php');
require_once(INC . 'functions/import_functions.php');
require_once(INC . 'classes/database/database_factory.php');
require_once(INC . 'functions/email_functions.php');
require_once(INC . 'functions/date_functions.php');

require_once(INSTALL . 'includes/functions.php');
require_once(INSTALL . 'includes/functions_legacy.php');

// the class that handles how to do the upgrade
require_once(INSTALL . 'includes/upgrade_abstract.php');
require_once(INSTALL . 'includes/upgrade_shell.php');

require_once(DP_WEB_ROOT . '/config.php');
require_once(INSTALL . 'includes/config.php');

/*******************************
* Security Check (2)
*******************************/

if (!defined('IGNORE_NOINSTALL_DAT') AND file_exists(INSTALL . 'noinstall.dat')) {
	echo "The file noinstall.dat exists inside the /install/ directory. This file must be removed to continue with the installation";
	exit();
}

/*******************************
* Setup header
*******************************/

$header = new Header();

/*******************************
* Create database connection
*******************************/

$db =& database_factory(DATABASE_TYPE);
$db2 =& database_object_factory(DATABASE_TYPE, TRUE);

/*******************************
* Get Tables
*******************************/

$query = $db->query_quiet("SHOW TABLES FROM `" . DATABASE_NAME . '`');
while ($result = $db->row_array($query, DB_RETURN_NUM)) {
	$tables[] = $result['0'];
}

/*******************************
* No Tables
*******************************/

if (!is_array($tables)) {
	die('No tables found');
}

if (($key = array_search('--reset-internal-version', $_SERVER['argv'])) !== false) {

	$key++; // next key is the version to set to

	require_once(INC . 'classes/class_DpBuilds.php');
	$dpbuilds = new DpBuilds();

	if (isset($_SERVER['argv'][$key]) AND $dpbuilds->isBuild($_SERVER['argv'][$key])) {

		$internal = $_SERVER['argv'][$key];
		$version = $dpbuilds->getVersionName($internal);

		$db->query("UPDATE settings SET value = '" . $db->escape($version) . "' WHERE name = 'deskpro_version'");
		$db->query("UPDATE settings SET value = " . intval($internal) . " WHERE name = 'deskpro_version_internal'");

		echo "Reset version to $internal :: $version\n";
	} else {
		die('Unknown build: ' . $_SERVER['argv'][$key]);
	}
}

/*******************************
* DeskPRO v1
*******************************/

if (in_array('categories', $tables)) {

	require_once(INSTALL . 'upgrade/v1_v2/index.php');

	$query = $db->query_quiet("SHOW TABLES FROM `" . DATABASE_NAME . '`');
	while ($result = $db->row_array($query, DB_RETURN_NUM)) {
		$tables[] = $result['0'];
	}

}

/*******************************
* Upgrade v2
*******************************/

if (in_array('admin_help_cat', $tables) AND in_array('pm_relations', $tables)) {
	require_once(INSTALL . 'upgrade/v2_v3/index.php');
}

/*******************************
* Upgrade v3
*******************************/

require_once(INSTALL . 'upgrade/v3/shell.php');

/*******************************
* Turn helpdesk back on
*******************************/

if ($helpdesk_was_on) {
	legacy_update_setting('helpdesk_enabled', 1);
}

?>
