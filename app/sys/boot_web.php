<?php if (!defined('DP_ROOT')) exit('No access');
require DP_ROOT . '/sys/KernelBooter.php';
\DeskPRO\Kernel\KernelBooter::bootWeb();
\DeskPRO\Kernel\KernelBooter::DeskPRO_Done();
