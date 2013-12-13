<?php

function deskpro_install_check_reqs()
{
	$errors = array();
	if (!deskpro_install_check_version()) {
		$errors['php_version'] = 'fatal';
	}

	if (!function_exists('json_encode')) {
		$errors['json_ext'] = 'fatal';
	}

	if (!function_exists('session_start')) {
		$errors['session_ext'] = 'fatal';
	}

	if (!extension_loaded('dom')) {
		$errors['dom_ext'] = 'fatal';
	}

	if (!deskpro_install_check_image_manip()) {
		$errors['image_manip'] = 'fatal';
	}

	if (!function_exists('ctype_alpha')) {
		$errors['ctype_ext'] = 'fatal';
	}

	if (!function_exists('token_get_all')) {
		$errors['tokenizer_ext'] = 'fatal';
	}

	if (!deskpro_install_check_pdo()) {
		$errors['pdo_ext'] = 'fatal';
	} elseif (!deskpro_install_check_pdo_mysql()) {
		$errors['pdo_mysql_ext'] = 'fatal';
	}

	if (!extension_loaded('openssl')) {
		$errors['openssl_ext'] = 'recommended';
	}

	if (!(function_exists('apc_store') && ini_get('apc.enabled'))) {
		$errors['apc_check'] = 'recommended';
	}

	if (!function_exists('get_magic_quotes_gpc')) {
		$errors['magic_quotes_gpc_check'] = 'recommended';
	}

	if (!function_exists('iconv') && !function_exists('mb_convert_encoding')) {
		$errors['iconv_ext'] = 'fatal';
	}

	if (!deskpro_install_check_memory_limit()) {
		$errors['memory_limit'] = 'fatal';
	}

	// Only do data dir ceck if we've got an environment loaded,
	// the simple check file doesnt load up anything besides this
	if (function_exists('dp_get_data_dir')) {
		if (!deskpro_install_check_data_writable(dp_get_data_dir())) {
			$errors['data_write'] = 'fatal';
		}
	}

	return $errors;
}

function deskpro_install_check_version()
{
	return version_compare(phpversion(), '5.3.2', '>=');
}

function deskpro_install_check_pcre()
{
	$backtrack_limit = (int)(@ini_get('pcre.backtrack_limit'));

	if ($backtrack_limit < 100000) {
		return false;
	}

	return true;
}

function deskpro_install_check_safemode()
{
	$v = ini_get('safe_mode');
	if (!$v || strtolower($v) == 'off' || strtolower($v) == 'false') {
		return true;
	}

	return false;// samemode on, fails test
}

function deskpro_install_check_config()
{
	if (!is_file(DP_CONFIG_FILE)) {
		return false;
	}

	return true;
}

function deskpro_install_check_mbstring()
{
	return function_exists('mb_stripos');
}

function deskpro_install_check_pdo()
{
	return class_exists('PDO', false);
}

function deskpro_install_check_pdo_mysql()
{
	if (!deskpro_install_check_pdo()) {
		return false;
	}

	$drivers = PDO::getAvailableDrivers();
	if (!$drivers) {
		return false;
	}

	if (!in_array('mysql', $drivers)) {
		return false;
	}

	return true;
}

function deskpro_install_check_image_manip()
{
	if (class_exists('Imagick', false) || class_exists('Gmagick', false) || function_exists('gd_info')) {
		return true;
	}
	return false;
}

function deskpro_install_check_memory_limit()
{
	$mem_size = @ini_get('memory_limit');
	if ($mem_size && $mem_size != '-1' && deskpro_install_check_parseinisize($mem_size) < 134217728/* 128 MB */) {
		return false;
	}

	return true;
}

function deskpro_install_simple_data_submit($log)
{
	$install_token_file = dp_get_log_dir() . '/install_token.dat';
	if (file_exists($install_token_file)) {
		$GLOBALS['dp_install_token'] = @file_get_contents($install_token_file);
	} elseif (isset($_COOKIE['dp_install_token'])) {
		$GLOBALS['dp_install_token'] = $_COOKIE['dp_install_token'];
	} else {
		$GLOBALS['dp_install_token'] = sha1(uniqid('', true) . mt_rand(1000,99999));
	}

	@file_put_contents($install_token_file, $GLOBALS['dp_install_token']);
	@setcookie('dp_install_token', $GLOBALS['dp_install_token'], strtotime('+4 weeks'), '/', null, false, true);

	if (!defined('DP_BUILD_TIME')) {
		if (file_exists(DP_ROOT.'/sys/config/build-time.php')) {
			require_once(DP_ROOT.'/sys/config/build-time.php');
		}
	}

	$url = 'http://';
	if (isset($_SERVER['HOST'])) $url .= $_SERVER['HOST'];
	elseif (isset($_SERVER['SERVER_NAME'])) $url .= $_SERVER['SERVER_NAME'];
	elseif (isset($_SERVER['SERVER_ADDR'])) $url .= $_SERVER['SERVER_ADDR'];
	if (isset($_SERVER['REQUEST_URI'])) $url .= $_SERVER['REQUEST_URI'];
	elseif (isset($_SERVER['PHP_SELF'])) $url .= $_SERVER['PHP_SELF'];

	$data = array(
		'source_type' => 'install.web',
		'log' => $log,
		'install_token' => isset($GLOBALS['dp_install_token']) ? $GLOBALS['dp_install_token'] : '',
		'nostats' => 0,
		'total_time' => 0,
		'error_type' => 'php',
		'error_info' => array(
			'exception_type' => 'Application\\InstallBundle\\Install\\ServerCheckException',
			'summary' => 'Pre-boot check failures',
			'errstr' => 'Pre-boot check failures',
		),
		'root'              => defined('DP_ROOT')                 ? DP_ROOT : '',
		'server_ip'         => isset($_SERVER['SERVER_ADDR'])     ? $_SERVER['SERVER_ADDR'] : '',
		'client_ip'         => isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR'] : '',
		'client_referrer'   => isset($_SERVER['HTTP_REFERER'])    ? $_SERVER['HTTP_REFERER'] : '',
		'client_user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
		'client_request'    => isset($_REQUEST)                   ? implode(', ', array_keys($_REQUEST)) : '',
		'build'             => defined('DP_BUILD_TIME') ? DP_BUILD_TIME : '0',
		'url'               => $url
	);

	$data['php_version'] = phpversion();

	if (function_exists('apc_cache_info')) {
		$data['php_has_apc'] = 1;
	} else {
		$data['php_has_apc'] = 0;
	}

	if (function_exists('wincache_ucache_info')) {
		$data['php_has_wincache'] = 1;
	} else {
		$data['php_has_wincache'] = 0;
	}

	if (function_exists('mb_get_info')) {
		$data['php_has_mbstring'] = 1;
	} else {
		$data['php_has_mbstring'] = 0;
	}

	if (function_exists('gd_info')) {
		$data['php_has_gd'] = 1;
	} else {
		$data['php_has_gd'] = 0;
	}

	if (class_exists('Imagick', false)) {
		$data['php_has_imagick'] = 1;
	} else {
		$data['php_has_imagick'] = 0;
	}

	if (class_exists('Gmagick', false)) {
		$data['php_has_gmagick'] = 1;
	} else {
		$data['php_has_gmagick'] = 0;
	}

	if (class_exists('PDO')) {
		$data['php_has_pdo'] = 1;
		if (in_array('mysql', PDO::getAvailableDrivers())) {
			$data['php_has_pdo_mysql'] = 1;
		} else {
			$data['php_has_pdo_mysql'] = 0;
		}
	} else {
		$data['php_has_pdo'] = 0;
		$data['php_has_pdo_mysql'] = 0;
	}

	if (function_exists('json_decode')) {
		$data['php_has_json'] = 1;
	} else {
		$data['php_has_json'] = 0;
	}

	if (function_exists('ctype_digit')) {
		$data['php_has_ctype'] = 1;
	} else {
		$data['php_has_ctype'] = 0;
	}

	if (function_exists('token_get_all')) {
		$data['php_has_tokenizer'] = 1;
	} else {
		$data['php_has_tokenizer'] = 0;
	}

	if (defined('DP_MA_SERVER')) {
		$ma_server = DP_MA_SERVER;
	} else {
		$ma_server = 'http://www.deskpro.com/members';
	}

	$opts = array(
		'http' => array(
			'timeout' => 10
		)
	);

	$context  = stream_context_create($opts);

	@file_get_contents($ma_server . '/api/data-submit/report-install.json?' . http_build_query($data, '', '&'), false, $context);
}

function deskpro_install_check_data_writable($data_dir = null)
{

	$failed = false;

	// data directory
	$data_dir = dp_get_data_dir();
	if (!is_dir($data_dir) OR (!is_writable($data_dir))) {
		$failed = true;
	}

	// debug directory
	$debug_dir = dp_get_debug_dir();
	if (!is_dir($debug_dir) OR (!is_writable($debug_dir))) {
		$failed = true;
	}

	// log directory
	$log_dir = dp_get_data_dir();
	if (!is_dir($log_dir) OR (!is_writable($log_dir))) {
		$failed = true;
	}

	// backup directory
	$backup_dir = dp_get_data_dir();
	if (!is_dir($backup_dir) OR (!is_writable($backup_dir))) {
		$failed = true;
	}

	// blob directory
	$blob_dir = dp_get_blob_dir();
	if (!is_dir($blob_dir) OR (!is_writable($blob_dir))) {
		$failed = true;
	}

	// tmp directory
	$tmp_dir = dp_get_tmp_dir();
	if (!is_dir($tmp_dir) OR (!is_writable($tmp_dir))) {
		$failed = true;
	}

	if ($failed) {
		return false;
	}

	return true;
}

if (deskpro_install_check_version()) {
	require_once DP_ROOT.'/src/Application/InstallBundle/Install/server_check_php53_stub.php';
} else {
	require_once DP_ROOT.'/src/Application/InstallBundle/Install/server_check_phpold_stub.php';
}

function deskpro_install_basic_error($message, $title = 'DeskPRO Installation')
{
	$page_html = file_get_contents(DP_ROOT . '/src/Application/DeskPRO/Resources/views/preboot-error.html');
	$page_html = str_replace('{{ TITLE }}', $title, $page_html);
	$page_html = str_replace('{{ MESSAGE }}', $message, $page_html);

	return $page_html;
}


/**
 * This is a copy of Orb\Util\Numbers::parseIniSize() because that class isn't included at the time preboot is called
 */
function deskpro_install_check_parseinisize($val)
{
	if ($val == -1 || !$val) {
		return $val;
	}

	$val = trim($val);
	$last = strtoupper($val[strlen($val)-1]);

	// Already in bytes
	if (ctype_digit($last)) {
		return (int)$val;
	}

	$val = (int)$val;

	if ($last != 'G' && $last != 'M' && $last != 'K') {
		return 0;
	}

	switch($last) {
		case 'G':
			$val *= 1024;
		case 'M':
			$val *= 1024;
		case 'K':
			$val *= 1024;
	}

	return $val;
}

/**
 * @return string
 */
function deskpro_install_guess_phpini_path()
{
	ob_start();
	phpinfo();
	$phpinfo = ob_get_clean();
	$phpinfo = html_entity_decode(strip_tags($phpinfo), ENT_QUOTES);

	if (preg_match('#^Loaded Configuration File (.*?)$#m', $phpinfo, $m)) {
		$path = $m[1];
		$path = str_replace('=>', '', $path);
		$path = trim($path);
		return $path;
	} else {
		return false;
	}
}