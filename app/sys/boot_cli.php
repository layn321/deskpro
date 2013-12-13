<?php if (!defined('DP_ROOT')) exit('No access');
require DP_ROOT . '/sys/KernelBooter.php';
$return = \DeskPRO\Kernel\KernelBooter::bootCli('prod', false);
\DeskPRO\Kernel\KernelBooter::DeskPRO_Done();
exit($return);