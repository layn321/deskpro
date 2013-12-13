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

require DP_ROOT . '/bin/build/inc.php';
require DP_ROOT . '/bin/build/php-path.php';
require DP_ROOT.'/sys/system.php';

$proc_kernel = null;
if (($k = array_search('--knum', $_SERVER['argv'])) !== false) {
	if (isset($_SERVER['argv'][$k+1])) {
		$proc_kernel = $_SERVER['argv'][$k+1];
	}
}

$kernel_classes = array(
	'DeskPRO\\Kernel\\AdminKernel',
	'DeskPRO\\Kernel\\AgentKernel',
	'DeskPRO\\Kernel\\ApiKernel',
	'DeskPRO\\Kernel\\CliKernel',
	'DeskPRO\\Kernel\\ReportKernel',
	'DeskPRO\\Kernel\\UserKernel',
	'DeskPRO\\Kernel\\InstallKernel',
	'DeskPRO\\Kernel\\BillingKernel',
);

if ($proc_kernel === null) {
	foreach ($kernel_classes as $k => $kernel_class) {
		echo "Warming {$kernel_class} ... ";
		$time = microtime(true);

		$cmd = DP_PHP_PATH . ' ./build-caches.php --knum ' . $k;
		$proc = new Symfony\Component\Process\Process($cmd, DP_ROOT.'/bin/build');
		$proc->setTimeout(600);
		$proc->run(function($type, $buffer) {
			if ($type === 'err') {
				echo 'ERR: '.$buffer;
			} else {
				echo $buffer;
			}
		});

		if (!$proc->isSuccessful()) {
			exit($proc->getExitCode());
		}

		echo $proc->getOutput();

		printf(" Done %.fs\n", (microtime(true) - $time));
	}

	echo "\nDone";

	exit(0);
} else {

	@ini_set('memory_limit', '524288000');

	$class = $kernel_classes[$proc_kernel];
	$kernel = new $class('prod', false);

	$_SERVER['argv'] = array('xx', 'cache:warmup', '--verbose');

	$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
	$application->run();
}
