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

require DP_ROOT . '/bin/build/inc.php';
require DP_ROOT.'/sys/system.php';

$kernel = new \DeskPRO\Kernel\CliKernel('dev', true);

if (in_array('--js', $_SERVER['argv'])) {
	$_SERVER['argv'] = array('x', 'dp:assetic', '-r', '--not', '--verbose', '-p', '_css');
} elseif (in_array('--css', $_SERVER['argv'])) {
	$_SERVER['argv'] = array('x', 'dp:assetic', '-r', '--verbose', '-p', 'css');
} else {
	$_SERVER['argv'] = array('x', 'dp:assetic', '-r', 'ALL', '--verbose');
}

$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
$application->run();
