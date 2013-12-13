<?php if (!defined('DP_ROOT')) exit('No access');
/**
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage SystemScripts
 * @copyright Copyright (c) 2010 DeskPRO (http://www.deskpro.com/)
 * @license http://www.deskpro.com/license-agreement DeskPRO License
 */

/*
 * Enable this script by adding a config.php line:
 *
 * $DP_CONFIG['sys_licinfo'] = 'abc';
 *
 * Where 'abc' is any auth code you want to use. Then call this page like:
 *
 * http://example.com/deskpro/index.php?_sys=licinfo&abc
 *
 * (Where the abc part is your auth code).
 */

#------------------------------
# Load config
#------------------------------

require_once DP_ROOT.'/sys/load_config.php';
dp_load_config();

$open = dp_get_config('sys_licinfo');
if (!$open) {
	exit;
} elseif ($open !== true) {
	// if its not a boolean true, then its an authcode
	if (!isset($_GET[$open])) {
		exit;
	}
}

$env = 'prod';
$debug = false;

if (isset($DP_CONFIG['debug']['dev']) && $DP_CONFIG['debug']['dev']) {
	$env = 'dev';
	$debug = true;
}

require DP_ROOT . '/sys/KernelBooter.php';
\DeskPRO\Kernel\KernelBooter::bootstrapLib(true);

$kernel_class = 'DeskPRO\\Kernel\\UserKernel';
define('DP_INTERFACE', 'sys');

$kernel = new $kernel_class($env, $debug);
$kernel->boot();

/** @var $container \Application\DeskPRO\DependencyInjection\DeskproContainer */
$container = $kernel->getContainer();

#------------------------------
# Get license details
#------------------------------

header('Content-Type: text/plain');
$lic = \DeskPRO\Kernel\License::getLicense();

echo "License ID  : " . $lic->getLicenseId();
echo "\n";
echo "Expires     : " . $lic->getExpireDate()->format('Y-m-d H:i:s');
echo "\n";
echo "Expire Days : " . $lic->getExpireDays();
echo "\n";