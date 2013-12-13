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

require DP_ROOT . '/../config.php';

if (!isset($DP_CONFIG) || !is_array($DP_CONFIG)) {
	$DP_CONFIG = array();
}

if (!isset($DP_CONFIG['db'])) $DP_CONFIG['db'] = array();
if (!isset($DP_CONFIG['db']['host']))      $DP_CONFIG['db']['host']      = DP_DATABASE_HOST;
if (!isset($DP_CONFIG['db']['user']))      $DP_CONFIG['db']['user']      = DP_DATABASE_USER;
if (!isset($DP_CONFIG['db']['password']))  $DP_CONFIG['db']['password']  = DP_DATABASE_PASSWORD;
if (!isset($DP_CONFIG['db']['dbname']))    $DP_CONFIG['db']['dbname']    = DP_DATABASE_NAME;

if (!defined('DP_BUILD_TIME')) {
	if (file_exists(DP_ROOT.'/sys/config/build-time.php')) {
		require(DP_ROOT.'/sys/config/build-time.php');
	} else {
		define('DP_BUILD_TIME', 1323444089); // would be used by someone who hasnt built yet
	}
}

require DP_ROOT . '/bin/build/inc.php';
require DP_ROOT.'/sys/system.php';

$kernel = new \DeskPRO\Kernel\CliKernel('dev', true);

$_SERVER['argv'] = array('x', 'dpdev:lang:check-phrase-ids');

$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
$application->run();
