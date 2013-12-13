<?php

require_once dirname(__FILE__) .'/../../src/Application/DeskPRO/LowUtil/RemoteRequest.php';

if (!defined('DP_BUILD_TIME') && file_exists(dirname(__FILE__) .'/../../sys/config/build-time.php')) {
	require_once dirname(__FILE__) .'/../../sys/config/build-time.php';
}

if (!defined('DP_BUILD_NUM') && file_exists(dirname(__FILE__) .'/../../sys/config/build-num.php')) {
	require_once dirname(__FILE__) .'/../../sys/config/build-num.php';
}

#----------------------------------------
# Gather stats
#----------------------------------------

$stats = array(
	'server_ip'         => isset($_SERVER['SERVER_ADDR'])     ? $_SERVER['SERVER_ADDR'] : '',
	'client_ip'         => isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR'] : '',
	'client_referrer'   => isset($_SERVER['HTTP_REFERER'])    ? $_SERVER['HTTP_REFERER'] : '',
	'client_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
	'client_request'    => isset($_REQUEST)                   ? implode(', ', array_keys($_REQUEST)) : '',
	'build'             => defined('DP_BUILD_TIME') ? DP_BUILD_TIME : 0,
	'build_num'         => defined('DP_BUILD_NUM')  ? DP_BUILD_NUM : 0,
);

$url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
if (!$url) {
	$url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
}
if (!$url) {
	$url = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
}

if (php_sapi_name() == 'cli') {
	$url = 'Command: ' . implode(' ', $_SERVER['argv']);
}

$stats['url'] = $url;

$stats['php_version'] = phpversion();
$stats['php_memory_limit'] = \Orb\Util\Env::getMemoryLimit();

if (function_exists('apc_cache_info')) {
	$stats['php_has_apc'] = 1;
	$stats['apc_version'] = phpversion('apc');
} else {
	$stats['php_has_apc'] = 0;
	$stats['apc_version'] = 0;
}

if (function_exists('wincache_ucache_info')) {
	$stats['php_has_wincache'] = 1;
	$stats['wincache_version'] = phpversion('wincache');
} else {
	$stats['php_has_wincache'] = 0;
	$stats['wincache_version'] = 0;
}

if (function_exists('mb_get_info')) {
	$stats['php_has_mbstring'] = 1;
} else {
	$stats['php_has_mbstring'] = 0;
}

if (function_exists('gd_info')) {
	$stats['php_has_gd'] = 1;
} else {
	$stats['php_has_gd'] = 0;
}

if (class_exists('Imagick', false)) {
	$stats['php_has_imagick'] = 1;
} else {
	$stats['php_has_imagick'] = 0;
}

if (class_exists('Gmagick', false)) {
	$stats['php_has_gmagick'] = 1;
} else {
	$stats['php_has_gmagick'] = 0;
}

if (class_exists('PDO')) {
	$stats['php_has_pdo'] = 1;
	if (in_array('mysql', \PDO::getAvailableDrivers())) {
		$stats['php_has_pdo_mysql'] = 1;
	} else {
		$stats['php_has_pdo_mysql'] = 0;
	}
} else {
	$stats['php_has_pdo'] = 0;
	$stats['php_has_pdo_mysql'] = 0;
}

if (function_exists('json_decode')) {
	$stats['php_has_json'] = 1;
} else {
	$stats['php_has_json'] = 0;
}

if (function_exists('ctype_digit')) {
	$stats['php_has_ctype'] = 1;
} else {
	$stats['php_has_ctype'] = 0;
}

if (function_exists('token_get_all')) {
	$stats['php_has_tokenizer'] = 1;
} else {
	$stats['php_has_tokenizer'] = 0;
}

#------------------------------
# OS / Server info
#------------------------------

if (strpos(strtoupper(PHP_OS), 'WIN') === 0) {
	$stats['server_os'] = 'win';
} elseif (strpos(strtoupper(PHP_OS), 'DARWIN') === 0) {
	$stats['server_os'] = 'mac';
} elseif (strpos(strtoupper(PHP_OS), 'FREEBSD') === 0) {
	$stats['server_os'] = 'freebsd';
} elseif (strpos(strtoupper(PHP_OS), 'LINUX') === 0) {
	$stats['server_os'] = 'linux';
} else {
	$stats['server_os'] = PHP_OS;
}

$stats['server_uname'] = php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('v') . ' ' . php_uname('m');

if (isset($_SERVER['SERVER_SOFTWARE'])) {
	if (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'APACHE') !== false) {
		$stats['web_server'] = 'apache';
	} elseif (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'IIS') !== false) {
		$stats['web_server'] = 'iis';
	} elseif (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'NGINX') !== false) {
		$stats['web_server'] = 'nginx';
	} elseif (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'CHEROKEE') !== false) {
		$stats['web_server'] = 'cherokee';
	} elseif (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'LIGHTTPD') !== false) {
		$stats['web_server'] = 'lighttpd';
	} else {
		$stats['web_server'] = $_SERVER['SERVER_SOFTWARE'];
	}

	$stats['server_software'] = $_SERVER['SERVER_SOFTWARE'];

	if (function_exists('apache_get_modules')) {
		$stats['has_mod_rewrite'] = in_array('mod_rewrite', apache_get_modules());
	}
}

#----------------------------------------
# Submit error report
#----------------------------------------

$stats['error_info'] = array(
	'summary' => $__fail_message
);

if (isset($__license_code)) {
	$stats['error_info']['license_code'] = $__license_code;
	$stats['license_id'] = 'nolic';
	$stats['local_hash'] = md5($__license_code);
}
if (isset($__install_key)) {
	$stats['error_info']['install_key'] = $__install_key;
}

$stats['error_type'] = 'php';
$stats['local_hash'] = sha1($__fail_message . __FILE__ . php_uname());

error_log($__fail_message);

try {
	DeskPRO_LowUtil_RemoteRequester::create()->request(
		'https://www.deskpro.com/members/api/data-submit/report-error.json',
		$stats,
		'POST',
		15
	);
} catch (\Exception $e) {}