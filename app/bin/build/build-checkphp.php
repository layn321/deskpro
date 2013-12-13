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
require DP_ROOT . '/bin/build/php-path.php';
require DP_ROOT.'/sys/system.php';

$paths = array(
	DP_ROOT.'/languages',
	DP_ROOT.'/bin',
	DP_ROOT.'/src',
	DP_ROOT.'/sys'
);

echo "Checking files for PHP errors\n";
$x = 0;

$check_files = array();

if (in_array('--only-changed', $_SERVER['argv']) && file_exists(DP_ROOT.'/sys/config/changed-files.php')) {
	echo "Using changerd-files file\n";
	$tmp = include(DP_ROOT.'/sys/config/changed-files.php');
	foreach ($tmp as $file) {
		if ($file && preg_match('#\.php$#', $file) && file_exists(DP_WEB_ROOT . '/' . $file)) {
			$check_files[] = DP_WEB_ROOT . '/' . $file;
		}
	}
} else {
	foreach ($paths as $dir) {
		$finder = new \Symfony\Component\Finder\Finder();
		$finder->files()->name('*.php')->in($dir);

		foreach ($finder as $file) {
			/** @var \Symfony\Component\Finder\SplFileinfo $file */
			$check_files[] = $file->getRealPath();
		}
	}
}

echo "Checking " . count($check_files) . " files ...\n";

$has_failed = array();
$bad_size = array();
foreach ($check_files as $filepath) {
	if (strpos($filepath, '/src/vendor/') === false) {
		$cmd = DP_PHP_PATH . " -l \"" . $filepath . "\"";

		$out = null;
		exec($cmd, $out, $ret);
	} else {
		$ret = false;
		$out = array();
	}

	if ($ret) {
		echo "\n";
		echo implode("\n", $out);
		echo "\n";
		$has_failed[] = str_replace(DP_ROOT, '', $filepath);
	} elseif (filesize($filepath) % 4096 == 0 && filesize($filepath) != 0) {
		$bad_size[] = str_replace(DP_ROOT, '', $filepath);
	} else {
		$x++;
		if ($x % 10 === 0) {
			echo ".";
		}
		if ($x % 100 == 0) {
			echo $x;
		}
	}
}

echo "\n";

if ($has_failed) {
	echo "There were syntax errors detected in the following files:\n";
	echo "- " . implode("\n- ", $has_failed);
	echo "\n";
	exit(1);
}

if ($bad_size) {
	echo "The following files are susceptible to the magic 4096 bug (https://bugs.php.net/bug.php?id=60998):\n";
	echo "- " . implode("\n- ", $bad_size);
	echo "\n";
	exit(1);
}

if (!$has_failed && !$bad_size) {
	echo "No errors detected.\n";
}
exit(0);