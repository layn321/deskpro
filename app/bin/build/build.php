#!/usr/bin/env php
<?php
if (php_sapi_name() != 'cli') {
	echo "This script must only be run from the CLI.\n";
	echo "Contact support@deskpro.com if you require assistance.\n";
	exit(1);
}

chdir(__DIR__);

define('DP_BUILDING', true);
define('DP_ROOT', realpath(__DIR__ . '/../../'));
define('DP_WEB_ROOT', realpath(__DIR__ . '/../../../'));
define('DP_CONFIG_FILE', DP_WEB_ROOT . '/config.php');
require DP_ROOT . '/bin/build/php-path.php';

require DP_ROOT . '/vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\ClassLoader\ClassCollectionLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array('Symfony' => DP_ROOT.'/vendor/symfony/src'));
$loader->register();

$output_realtime = function($type, $buffer) {
	if ($type === 'err') {
		echo 'ERR: '.$buffer;
	} else {
		echo $buffer;
	}
};

$quick = false;
if (in_array('--quick', $_SERVER['argv'])) {
	$quick = true;
}

#####################################################################

$time = microtime(true);
echo "build-boostrap ... ";

$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-bootstrap.php', DP_ROOT.'/bin/build');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-kernels ... ";

$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-kernels.php', DP_ROOT.'/bin/build');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-caches ... ";

$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-caches.php', DP_ROOT.'/bin/build');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-assetic ... ";

if (in_array('--skip-assetic', $_SERVER['argv'])) {
	echo " SKIPPED (--skip-assetic) ";
} else {
	$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-assetic.php', DP_ROOT.'/bin/build');
	$proc->setTimeout(600);
	$proc->run($output_realtime);

	if (!$proc->isSuccessful()) {
		echo ("\nDetected error. Quitting.\n");
		exit($proc->getExitCode());
	}
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-compiled ... ";

$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-compiled.php', DP_ROOT.'/bin/build');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-schema-file ... ";

$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-schema-file.php', DP_ROOT.'/bin/build');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-template-map ... ";

if ($quick) {
	$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-template-map.php --bogus', DP_ROOT.'/bin/build');
} else {
	$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-template-map.php', DP_ROOT.'/bin/build');
}
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-class-map ... ";

$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-class-map.php', DP_ROOT.'/bin/build');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-data ... ";

$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-data.php', DP_ROOT.'/bin/build');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

// must do before checksum file is built

$build_time = time();
echo "echoing build time of ... $build_time ";

$proc = new \Symfony\Component\Process\Process("echo '<?php define(\"DP_BUILD_TIME\", $build_time); ' > build-time.php", DP_ROOT.'/sys/config');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE ";
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-cleanup ... ";

$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-cleanup.php', DP_ROOT.'/bin/build');
$proc->setTimeout(600);
$proc->run($output_realtime);

if (!$proc->isSuccessful()) {
	echo ("\nDetected error. Quitting.\n");
	exit($proc->getExitCode());
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-checkphp ... ";

if ($quick) {
	echo "SKIPPED (--quick)";
} else {
	$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-checkphp.php --only-changed', DP_ROOT.'/bin/build');
	$proc->setTimeout(600);
	$proc->run($output_realtime);

	if (!$proc->isSuccessful()) {
		echo ("\nDetected error. Quitting.\n");
		exit($proc->getExitCode());
	}
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-checkphrases ... ";

if ($quick) {
	echo "SKIPPED (--quick)";
} else {
	$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-checkphrases.php', DP_ROOT.'/bin/build');
	$proc->setTimeout(600);
	$proc->run($output_realtime);

	if (!$proc->isSuccessful()) {
		echo ("\nDetected error. Quitting.\n");
		exit($proc->getExitCode());
	}
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-checkphrases-vars ... ";

if ($quick) {
	echo "SKIPPED (--quick)";
} else {
	$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-checkphrases-vars.php', DP_ROOT.'/bin/build');
	$proc->setTimeout(600);
	$proc->run($output_realtime);

	if (!$proc->isSuccessful()) {
		echo ("\nDetected error. Quitting.\n");
		exit($proc->getExitCode());
	}
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";

#####################################################################

$time = microtime(true);
echo "build-checksum-file ... ";

if ($quick) {
	echo "SKIPPED (--quick)";
	@unlink(DP_ROOT.'/sys/Resources/distro-checksums.php');
} else {
	$proc = new \Symfony\Component\Process\Process(DP_PHP_PATH . ' ./build-checksum-file.php', DP_ROOT.'/bin/build');
	$proc->setTimeout(600);
	$proc->run($output_realtime);

	if (!$proc->isSuccessful()) {
		echo ("\nDetected error. Quitting.\n");
		exit($proc->getExitCode());
	}
}

echo " DONE " . sprintf("%.f", microtime(true)-$time);
echo "\n";
