<?php
if (!isset($_REQUEST['key']) || $_REQUEST['key'] != 'dp_login_check') exit;

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$result = $auth->login(
	request_var('dp_username', '', true),
	request_var('dp_password', '', true),
	0,
	0,
	0
);

if ($result['status'] == LOGIN_SUCCESS) {
	echo 'LOGIN_SUCCESS';
} else {
	echo 'LOGIN_FAILURE';
}