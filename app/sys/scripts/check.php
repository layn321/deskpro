<?php if (!defined('DP_ROOT')) exit('No access');
require DP_ROOT . '/src/Application/InstallBundle/Install/server_check_functions.php';
echo '<pre>';

#------------------------------
# Preboot checks
#------------------------------

$mem_size = @ini_get('memory_limit');
if ($mem_size && $mem_size != '-1' && deskpro_install_check_parseinisize($mem_size) < 134217728/* 128 MB */) {
	@ini_set('memory_limit', 134217728);
}
unset($mem_size);

$mem_size = @ini_get('memory_limit');
if (deskpro_install_check_parseinisize($mem_size) < 134217728) {
	echo "memory_limit\n";
}

$fail = false;

if (!deskpro_install_check_version()) {
	$fail = true;
	echo "php_version\n";
}

if (!deskpro_install_check_safemode()) {
	$fail = true;
	echo "safe_mode\n";
}

if (!deskpro_install_check_config()) {
	$fail = true;
	echo "config_file\n";
}

// Fatal errors, cant continue
if ($fail) {
	exit;
}


#------------------------------
# Do full checks now
#------------------------------

#---
# Checker requires some services so boot into install kernel
#---

require DP_ROOT . '/sys/KernelBooter.php';
\DeskPRO\Kernel\KernelBooter::bootstrapConfig();

$env = 'prod';
$debug = false;

if (isset($DP_CONFIG['debug']['dev']) && $DP_CONFIG['debug']['dev']) {
	$env = 'dev';
	$debug = true;
}

\DeskPRO\Kernel\KernelBooter::bootstrapLib($debug);

$kernel = new DeskPRO\Kernel\InstallKernel($env, $debug);
$kernel->boot();

#---
# Run through the checks
#---

$server_check = new \Application\InstallBundle\Install\ServerChecks();
$server_check->checkServer();

$is_fatal = $server_check->hasFatalErrors();

if (!$is_fatal) {
	$server_check->checkDatabase(\Application\DeskPRO\App::getConfig('db'));
}

$db_errs = false;
foreach ($server_check->getErrors() as $k => $v) {
	if (strpos($k, 'db_') === 0) $db_errs = true;
	$fail = true;
	echo "$k\n";
}

#---
# Check to make sure all tables exist
#---

if (!$is_fatal && !$db_errs && file_exists(DP_ROOT.'/src/Application/InstallBundle/Data/schema.php')) {
	try {
		$tables = \Application\DeskPRO\App::getDb()->fetchAllCol("SHOW TABLES");
		$tables = array_flip($tables);

		$schema = require DP_ROOT.'/src/Application/InstallBundle/Data/schema.php';
		$schema = implode(" ", $schema['create']);

		$m = null;
		preg_match_all('#CREATE\s+TABLE\s+`?(.*?)`?\s+#', $schema, $m);
		foreach ($m[1] as $t) {
			if (!isset($tables[$t])) {
				$fail = true;
				echo "missing table {$t}\n";
			}
		}

	} catch (\Exception $e) {
		echo "exception {$e->getCode()}\n";
		$fail = true;
	}
}

echo "\n";
if ($fail) {
	echo "failed server check\n";
} else {
	echo "everything okay\n";
}
