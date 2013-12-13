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

if (php_sapi_name() != 'cli') {
	echo "This script must only be run using the command line interface of PHP\n";
	echo "Contact support@deskpro.com if you require assistance.\n";
	exit(1);
}

if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
	echo "This script is designed for Windows only. twitter-cloud.php can be run directly on Linux.\n";
	exit(2);
}

ini_set('display_errors', true);
error_reporting(E_ALL | E_STRICT);
define('DP_ROOT', realpath(__DIR__ . '/../../../'));
setlocale(LC_CTYPE, 'C');
date_default_timezone_set('UTC');
ini_set('default_charset', 'UTF-8');
set_time_limit(0);

require_once DP_ROOT.'/sys/load_config.php';
dp_load_config();

$file = escapeshellarg(__DIR__ . '\\twitter-cloud.php');
$php_path = dp_get_php_path(true);

// this is needed as we need a fake window to hide the process
$php_path = str_replace('php-win.exe', 'php.exe', $php_path);

if (class_exists('\COM', false)) {
	$shell = new \COM("WScript.Shell");
	$shell->Run("$php_path $file", 0, false);
} else {
	pclose(popen("start \"dptwittercloud\" /MIN $php_path $file", "r"));
}