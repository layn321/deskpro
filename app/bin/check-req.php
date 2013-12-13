<?php
define('DP_ROOT', realpath(dirname(__FILE__) . '/../'));

@ini_set('memory_limit', -1);
@ini_set('memory_limit', 268435456);
@set_time_limit(0);

// Normalise env
setlocale(LC_CTYPE, 'C');
date_default_timezone_set('UTC');
ini_set('default_charset', 'UTF-8');

require_once DP_ROOT . '/src/Application/InstallBundle/Install/server_check_functions.php';

$fatal = array();

foreach (deskpro_install_check_reqs() as $type => $level) {
	if ($level == 'fatal') {
		$fatal[] = $type;
	}
}

if ($fatal) {
	echo "Errors detected: " . implode(',', $fatal);
} else {
	echo "OKAY";
}

echo "\n";