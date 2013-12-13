<?php if (!defined('DP_ROOT')) exit('No access');
/**
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage SystemScripts
 * @copyright Copyright (c) 2010 DeskPRO (http://www.deskpro.com/)
 * @license http://www.deskpro.com/license-agreement DeskPRO License
 */

#------------------------------
# Load config
#------------------------------

require_once DP_ROOT.'/sys/load_config.php';
dp_load_config();

// If not authed, the only way we render phpinfo is if not installed or we have the auth
if (!isset($is_authed) || !$is_authed) {
	$is_authed = false;

	$auth = isset($_GET['auth']) ? $_GET['auth'] : false;
	if ($auth && dp_get_config('phpinfo_auth') && dp_get_config('phpinfo_auth') == $auth) {
		$is_authed = true;
	} elseif (!file_exists(dp_get_data_dir() . '/is_installed.dat')) {
		$is_authed = true;
	}
}

if (!$is_authed) die('Invalid auth code.');

#------------------------------
# Show PHP Info
#------------------------------

if (isset($_GET['cli'])) {
	if (!file_exists(dp_get_data_dir() . '/cli-phpinfo.html')) {
		die('CLI phpinfo has not been generated yet');
	}

	$phpinfo = file_get_contents(dp_get_data_dir() . '/cli-phpinfo.html');
	if (strpos($phpinfo, '<body') === false) {
		header('Content-Type: text/plain');
		header('Content-Disposition: inline; filename=error.log.txt');
	}
	echo $phpinfo;
} else {
	phpinfo();
}

