<?php
if (!isset($__fail_code)) {
	return;
}

$__fail_code = (int)$__fail_code;

$__fail_message = "Invalid /app/sys/system.php file ($__fail_code)";
include dirname(__FILE__) . '/system-fail-func.php';