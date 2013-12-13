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

#------------------------------
# Show PHP Info
#------------------------------

header('Content-Type: text/plain');
header('Content-Disposition: inline; filename=error.log.txt');

if (isset($_GET['web'])) {

	echo "WEB ERROR LOG FILE\n\n";

	$log_file_path = @ini_get('error_log');
	if (!$log_file_path) {
		$log_file_path = dp_get_log_dir() . '/server-phperr-web.log';
	}

	if (!is_file($log_file_path)) {
		die('error_log file does not exist: ' . $log_file_path);
	}

	if (!is_readable($log_file_path)) {
		die('error_log file is not readable: ' . $log_file_path);
	}

} elseif (isset($_GET['cli'])) {

	echo "CLI ERROR LOG FILE\n\n";

	$log_file_path = dp_get_log_dir() . '/cli-phperr.log';

	if (!file_exists($log_file_path)) {
		die('No cli-phperr.log file');
	}

} else {

	echo "DESKPRO ERROR LOG FILE\n\n";

	$log_file_path = dp_get_log_dir() . '/error.log';

	if (!file_exists($log_file_path)) {
		die('No error.log file');
	}
}

$fp = fopen($log_file_path, 'r');
while (!feof($fp)) {
	echo str_replace("\r", '', fread($fp, 8192));
}
fclose($fp);
