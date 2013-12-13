<?php if (!defined('DP_ROOT')) exit('No access');

$is_authed = false;
if ((isset($_GET['_']) || isset($_COOKIE['dp_sysscript_'.$_GET['_sys']])) && file_exists(DP_CONFIG_FILE)) {
	$check_fn = function ($token, $secret) {
		// Check to make sure its a valid format
		if (substr_count($token, '-') != 2) {
			return false;
		}

		list($expire_time_enc, $rand_str, $hash) = explode('-', $token, 3);

		// Check the hash first
		$check_hash = sha1($secret . $expire_time_enc . $rand_str);

		if ($check_hash != $hash) {
			return false;
		}

		// Check the time now
		if ($expire_time_enc != '0') {
			$expire_time = base_convert($expire_time_enc, 36, 10);
			if (time() > $expire_time) {
				return false;
			}
		}

		return true;
	};

	if (isset($_GET['_'])) {
		$is_authed = $check_fn($_GET['_'], md5_file(DP_CONFIG_FILE) . $_GET['_sys']);
		if ($is_authed) {
			setcookie('dp_sysscript_' . $_GET['_sys'], $_GET['_'], time() + 18000, '/');
		}
	} elseif (isset($_COOKIE['dp_sysscript_'.$_GET['_sys']])) {
		$is_authed = $check_fn($_COOKIE['dp_sysscript_'.$_GET['_sys']], md5_file(DP_CONFIG_FILE) . $_GET['_sys']);
	}
}

switch ($_GET['_sys']) {
	case 'blank':
		break;

	case 'memtest':
		if (defined('DPC_IS_CLOUD')) exit;
		if (!$is_authed) die('Invalid auth code.');
		require DP_ROOT . '/sys/scripts/memtest.php';
		break;

	case 'errorlog':
		if (defined('DPC_IS_CLOUD')) exit;
		if (!$is_authed) die('Invalid auth code.');
		require DP_ROOT . '/sys/scripts/errorlog.php';
		break;

	case 'check':
		if (defined('DPC_IS_CLOUD')) exit;
		require DP_ROOT . '/sys/scripts/check.php';
		break;

	case 'phpinfo':
		if (defined('DPC_IS_CLOUD')) exit;
		require DP_ROOT . '/sys/scripts/phpinfo.php';
		break;

	case 'apc':
		if (defined('DPC_IS_CLOUD')) exit;
		if (!$is_authed) die('Invalid auth code.');
		require DP_ROOT . '/sys/scripts/apc.php';
		break;

	case 'apcclear':
		if (defined('DPC_IS_CLOUD')) exit;
		if (!$is_authed) die('Invalid auth code.');
		require DP_ROOT . '/sys/scripts/apcclear.php';
		break;

	case 'wincache':
		if (defined('DPC_IS_CLOUD')) exit;
		if (!$is_authed) die('Invalid auth code.');
		require DP_ROOT . '/sys/scripts/wincache.php';
		break;

	case 'checkurl':
		if (defined('DPC_IS_CLOUD')) exit;
		require DP_ROOT . '/sys/scripts/checkurl.php';
		break;

	case 'checkurlpath':
		if (defined('DPC_IS_CLOUD')) exit;
		require DP_ROOT . '/sys/scripts/checkurlpath.php';
		break;

	case 'dev_run_migrations':
		if (defined('DPC_IS_CLOUD')) exit;
		if (!$is_authed) die('Invalid auth code.');
		require DP_ROOT . '/sys/scripts/dev_run_migrations.php';
		break;

	case 'savemail':
		require DP_ROOT . '/sys/scripts/savemail.php';
		break;

	case 'save_failed_sendmail':
		require DP_ROOT . '/sys/scripts/failed_sendmail_job.php';
		break;

	case 'chat_status':
		require DP_ROOT . '/sys/scripts/chat_status.php';
		break;

	case 'ping':
		require DP_ROOT . '/sys/scripts/ping.php';
		break;

	case 'licinfo':
		require DP_ROOT . '/sys/scripts/licinfo.php';
		break;

	case 'smtp_event':
		require DP_ROOT.'/sys/scripts/smtp_event.php';
		break;

	case 'stats':
		require DP_ROOT.'/sys/scripts/stats.php';
		break;
}
