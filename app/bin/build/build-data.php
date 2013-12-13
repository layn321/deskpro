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

#------------------------------
# Build cloudflare IPs data
#------------------------------

$data_path = DP_ROOT.'/sys/Resources/cloudflare-ips.php';

$lines = trim(file_get_contents('https://www.cloudflare.com/ips-v4'));
$lines .= "\n";
$lines .= trim(file_get_contents('https://www.cloudflare.com/ips-v6'));
$lines = trim($lines);
$lines = \Orb\Util\Strings::standardEol($lines);

$lines = \Orb\Util\Strings::modifyLines($lines, "\t'", "',");

file_put_contents($data_path, "<?php return array(\n\n$lines\n\n);\n");
chmod($data_path, 0644);