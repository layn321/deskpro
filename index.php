<?php

/**
 * The path to the 'app' directory.
 * If you move the app directory, you must update this path.
 */
if (!defined('DP_ROOT')) define('DP_ROOT', dirname(__FILE__) . '/app');

/**
 * The path to the config.php file.
 * You may wish to move the config file outisde of the webroot.
 * If you move the config.php file, you must update this path.
 */
if (!defined('DP_CONFIG_FILE')) define('DP_CONFIG_FILE', dirname(__FILE__) . '/config.php');


#########################################################################################################
# You should not change anything below this line
#########################################################################################################

error_reporting(E_ALL | E_STRICT);
if (!defined('DP_WEB_ROOT')) define('DP_WEB_ROOT', dirname(__FILE__));
if (!defined('DP_START_TIME')) define('DP_START_TIME', microtime(true));

if (isset($_GET['_sys'])) {
	require_once DP_ROOT.'/sys/boot_scripts.php';
	exit;
}

require DP_ROOT . '/sys/preboot.php';

if (!defined('DP_BOOT_MODE')) define('DP_BOOT_MODE', 'web');
switch (DP_BOOT_MODE) {
	case 'cron':            require DP_ROOT.'/sys/boot_cron.php';        break;
	case 'cli':             require DP_ROOT.'/sys/boot_cli.php';         break;
	case 'web':             require DP_ROOT.'/sys/boot_web.php';         break;
	case 'serve_file':      require DP_ROOT.'/sys/serve_file.php';       break;
	case 'get_messages':    require DP_ROOT.'/sys/get_messages.php';     break;
	case 'import':          require DP_ROOT.'/sys/boot_import.php';      break;
	case 'upgrade':         require DP_ROOT.'/sys/boot_upgrade.php';     break;
	case 'dp':              require DP_ROOT.'/sys/serve_dp.php';         break;
}
