<?php if (!defined('DP_ROOT')) exit('No access');
/**
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage SystemScripts
 * @copyright Copyright (c) 2010 DeskPRO (http://www.deskpro.com/)
 * @license http://www.deskpro.com/license-agreement DeskPRO License
 */

/**
 * This handles writing the special chat_is_available.trigger file based.
 * It is used when multiple front-end web servers are in use. The normal
 * chat_ping_timeout cron job posts to this script so the local server has the proper trigger.
 *
 * Required GET params:
 * - auth: Must be the defined DP_CHATSTATUS_AUTH
 * - is_chat_available: Either 1 or 0
 */

require DP_ROOT.'/sys/load_config.php';
dp_load_config();

#------------------------------
# Verify auth
#------------------------------

if (!defined('DP_CHATSTATUS_AUTH')) {
	echo "DP_CHATSTATUS_AUTH_UNDEFINED";
	exit(1);
}

if (!isset($_GET['auth']) || $_GET['auth'] != DP_CHATSTATUS_AUTH) {
	echo "DP_CHATSTATUS_AUTH_INVALID";
	exit(1);
}

#------------------------------
# Write file
#------------------------------

$trigger_file = dp_get_data_dir() . '/chat_is_available.trigger';
if (isset($_GET['is_chat_available']) && $_GET['is_chat_available']) {
	if (!file_put_contents($trigger_file, time())) {
		echo "DP_CHATSTATUS_FAIL_AVAILABLE";
		exit;
	}
	@chmod($trigger_File, 0777);
	echo "DP_CHATSTATUS_WROTE_AVAILABLE";
} else {
	if (file_exists($trigger_file) && !unlink($trigger_file)) {
		echo "DP_CHATSTATUS_FAILED_UNAVAILABLE";
		exit;
	}

	echo "DP_CHATSTATUS_WROTE_UNAVAILABLE";
}