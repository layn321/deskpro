#!/usr/bin/env php
<?php
if (php_sapi_name() != 'cli') {
	echo "This script must only be run from the CLI.\n";
	echo "Contact support@deskpro.com if you require assistance.\n";
	exit(1);
}

define('DP_BUILDING', true);
define('DP_ROOT', realpath(__DIR__ . '/../../'));
define('DP_WEB_ROOT', realpath(__DIR__ . '/../../../'));
define('DP_CONFIG_FILE', DP_WEB_ROOT . '/config.php');

// Remove log stuff
$rm_paths = array(
	DP_ROOT.'/sys/cache/dev',
	DP_ROOT.'/sys/cache/prod/classes.map'
);

foreach ($rm_paths as $p) {
	system('rm -rf ' . $p);
}
