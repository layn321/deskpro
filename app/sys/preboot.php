<?php if (!defined('DP_ROOT')) exit('No access');

#------------------------------
# Normalize env
#------------------------------

@setlocale(LC_CTYPE, 'C');
@date_default_timezone_set('UTC');
@ini_set('default_charset', 'UTF-8');
@ini_set('zlib.output_compression', '0');

require DP_ROOT . '/src/Application/InstallBundle/Install/server_check_functions.php';
require DP_ROOT . '/sys/load_config.php';
dp_load_config();

#------------------------------
# See if we need to clear apc
#------------------------------

if (!defined('DPC_IS_CLOUD') && file_exists(dp_get_tmp_dir() . '/apc-clear.trigger')) {
	if (function_exists('apc_clear_cache')) {
		apc_clear_cache();
		apc_clear_cache('user');
	}
	if (function_exists('wincache_ucache_clear')) {
		wincache_ucache_clear();
		wincache_refresh_if_changed();
	}
	@unlink(dp_get_tmp_dir() . '/apc-clear.trigger');
}

#------------------------------
# Attempt to set min memory limit to 128 MB
#------------------------------

define('DP_REAL_MEMSIZE', deskpro_install_check_parseinisize(@ini_get('memory_limit')));
$mem_size = DP_REAL_MEMSIZE;
if ($mem_size && $mem_size != '-1') {
	if ($mem_size < 134217728/* 128 MB */) {
		define('DP_SET_MEMSIZE', 134217728);
		@ini_set('memory_limit', 134217728);
	} else {
		define('DP_SET_MEMSIZE', $mem_size);
	}
} else {
	define('DP_SET_MEMSIZE', -1);
}

if (dp_get_config('use_max_memory')) {
	define('DP_MAX_MEMSIZE', dp_get_config('use_max_memory'));
} else {
	define('DP_MAX_MEMSIZE', ($mem_size && $mem_size != -1) ? DP_SET_MEMSIZE+134217728 : -1);
}

#------------------------------
# Attempt to set max_execution_time to at least 40s
#------------------------------

define('DP_REAL_MAX_EXEC_TIME', @ini_get('max_execution_time'));

if (!isset($GLOBALS['DP_PREF_MAX_EXEC_TIME'])) {
	$GLOBALS['DP_PREF_MAX_EXEC_TIME'] = 600;
}

if (defined('DP_PREF_MAX_EXEC_TIME')) {
	$GLOBALS['DP_PREF_MAX_EXEC_TIME'] = DP_PREF_MAX_EXEC_TIME;
}

$max_time = DP_REAL_MAX_EXEC_TIME;
if (!$max_time || $max_time < $GLOBALS['DP_PREF_MAX_EXEC_TIME']) {
	@set_time_limit($GLOBALS['DP_PREF_MAX_EXEC_TIME']);
}
unset($max_time);

#------------------------------
# Attempt to set error log file if unset
#------------------------------

@ini_set('log_errors', true);

define('DP_REAL_ERROR_LOG', @ini_get('error_log'));
if (!DP_REAL_ERROR_LOG) {
	if (defined('DP_BOOT_MODE') && (DP_BOOT_MODE == 'cron' || DP_BOOT_MODE == 'cli')) {
		@ini_set('error_log', dp_get_log_dir() . '/server-phperr-cli.log');
	} else {
		@ini_set('error_log', dp_get_log_dir() . '/server-phperr-web.log');
	}
}

// If DeskPRO is not installed yet, always force-on display_errors
// so problems during an install process are not missed
if (!file_exists(dp_get_data_dir().'/is_installed.dat')) {
	@ini_set('display_errors', "1");
}

#------------------------------
# Detect if auto-update is running which
# means we should quit now.
#------------------------------

if (!defined('DP_BOOT_MODE') || (DP_BOOT_MODE != 'cli' && DP_BOOT_MODE != 'upgrade')) {
	if (
		// The upgrade is still marked as running -- this is the point the helpdesk is actually supposed to be off
		(file_exists(DP_WEB_ROOT.'/auto-update-is-running.trigger') || (defined('DPC_SYS_DISABLED') && DPC_SYS_DISABLED == 'upgrading'))

		// But on the CLI/command, we turn off when the upgrade is actually started (while its doing backups etc could be a while)
		|| (file_exists(dp_get_tmp_dir() . '/auto-upgrade-started') && intval(trim(file_get_contents(dp_get_tmp_dir() . '/auto-upgrade-started'))) > time() - 600)
	) {
		if (php_sapi_name() == 'cli') {
			echo "Currently installing updates";
			die(0);
		} else {

			// The upgrade watcher check-started. We dont want to boot into full system to serve it from the UpgradeController::checkStartedAction
			if (strpos($_SERVER['PHP_SELF'], 'check-started.json') !== false || strpos($_SERVER['REQUEST_URI'], 'check-started.json') !== false) {
				header('Content-Type: application/json');
				echo json_encode(array('started' => true));
				exit;
			}

			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				header('HTTP/1.0 503 Service Unavailable');
				header('Content-Type: application/json');
				echo json_encode(array(
					'error' => 'update_running'
				));
				exit;
			} else {
				$page_html = file_get_contents(DP_ROOT . '/src/Application/DeskPRO/Resources/views/helpdesk-disabled.html');
				$message = 'The helpdesk is currently offline for maintenance. Please try again in a few minutes.';
				if (file_exists(dp_get_data_dir() . '/helpdesk-offline-message.txt')) {
					$message = file_get_contents(dp_get_data_dir() . '/helpdesk-offline-message.txt');
				}
				$page_html = str_replace('{{ OFFLINE_MESSAGE }}', $message, $page_html);
				echo $page_html;
				exit;
			}
		}
	}
}

#------------------------------
# Handle CLI logging of info
#------------------------------

if (!defined('DPC_IS_CLOUD') && ((defined('DP_BOOT_MODE') && DP_BOOT_MODE == 'cron') || (isset($_SERVER['argv']) && in_array('dp_write_cli_info', $_SERVER['argv'])))) {

	$do_update = false;
	$last_error_log_hash = null;
	if (file_exists(dp_get_data_dir() .'/cli-server-reqs-check.dat')) {
		$data = file_get_contents(dp_get_data_dir() .'/cli-server-reqs-check.dat');
		$data = @unserialize($data);

		// Update these files every 5 minutes on cron
		if (!$data || !isset($data['gen_time']) || $data['gen_time'] < time() - 300) {
			$do_update = true;
		}

		if (isset($data['error_log_hash'])) {
			$last_error_log_hash = $data['error_log_hash'];
		}
	} else {
		$do_update = true;
	}

	if ($do_update) {

		if (!is_writable(dp_get_data_dir() .'/cli-phpinfo.html')) {
			error_log("No permission to write data/cli-phpinfo.php file");
		}
		if (!is_writable(dp_get_data_dir() .'/cli-server-reqs-check.dat')) {
			error_log("No permission to write data/cli-server-reqs-check.dat file");
		}

		ob_start();
		@phpinfo();
		$phpinfo = ob_get_clean();
		@file_put_contents(dp_get_data_dir() .'/cli-phpinfo.html', $phpinfo);
		@chmod(dp_get_data_dir() .'/cli-phpinfo.html', 0777);

		$data = array('checks' => deskpro_install_check_reqs());
		$data['gen_time'] = time();
		$data['php_version'] = phpversion();
		$data['memory_limit'] = deskpro_install_check_parseinisize(@ini_get('memory_limit'));
		$data['memory_limit_real'] = DP_REAL_MEMSIZE;
		$data['error_log'] = @ini_get('error_log');
		$data['error_log_real'] = DP_REAL_ERROR_LOG;

		if ($data['error_log'] && file_exists($data['error_log']) && is_readable($data['error_log'])) {
			$data['error_log_hash'] = md5_file($data['error_log']);

			if ($last_error_log_hash != $data['error_log_hash']) {
				@copy($data['error_log'], dp_get_log_dir() . '/cli-phperr.log');
				@chmod(dp_get_log_dir() . '/cli-phperr.log', 0777);
			}
		}

		@file_put_contents(dp_get_data_dir() .'/cli-server-reqs-check.dat', serialize($data));
		@chmod(dp_get_data_dir() .'/cli-server-reqs-check.dat', 0777);
	}

	unset($do_update, $phpinfo, $data);

	if (isset($_SERVER['argv']) && in_array('dp_write_cli_info', $_SERVER['argv'])) {
		exit;
	}
}

#------------------------------
# Run low-level server checks
#------------------------------

$errors = array();
$errors_codes = array();

if (!deskpro_install_check_version()) {
	$errors[] = "The version of PHP you have is too old. DeskPRO requires PHP v5.3.2 or newer but <a href='?phpinfo'>you are using " . phpversion() . "</a>. You need to upgrade your version.";
	$errors_codes[] = 'php_version';
}

if (!deskpro_install_check_pcre()) {
	$errors[] = "PHP is configured with a `pcre.backtrack_limit` value that is too low. Edit your php.ini configuration and change it to at least 100000.";
	$errors_codes[] = 'pcre_backtrack_limit';
}

if (!deskpro_install_check_safemode()) {
	$errors[] = "PHP currently has safe_mode enabled. DeskPRO requires safe_mode to be set to \"Off\". You need to edit your PHP configuration to make this change.";
	$errors_codes[] = 'safe_mode';
}

if (php_sapi_name() == 'cli') {
	if (!deskpro_install_check_pdo()) {
		$errors[] = "PHP on the command-line does not have PDO installed. It is possible you have to install 'pdo' into a separate php.ini file (noted below) for command-line usage.";
		$errors_codes[] = 'pdo_ext';
	} elseif (!deskpro_install_check_pdo()) {
		$errors[] = "PHP on the command-line has PDO installed, but not the MySQL driver. It is possible you have to install 'pdo_mysql' into a separate php.ini file (noted below) for command-line usage.";
		$errors_codes[] = 'pdo_mysql_ext';
	}
}

if (defined('DP_BOOT_MODE') && DP_BOOT_MODE == 'cron' && php_sapi_name() != 'cli') {
	$errors[] = "You are using a PHP binary that is not meant for use on the command-line. You should re-compile PHP. (Using: " . php_sapi_name() . ")";
	$errors_codes[] = 'php_not_cli';
}

if ($errors) {

	if (!(defined('DP_BOOT_MODE') && DP_BOOT_MODE == 'cron')) {
		deskpro_install_simple_data_submit(implode("\n", $errors));
	}

	if (php_sapi_name() == 'cli' || (defined('DP_BOOT_MODE') && DP_BOOT_MODE == 'cron')) {
		$msg = "There are problems with your server that prevent DeskPRO from executing this command:\n\n";
		$msg .= '- ' . implode("\n- ", $errors);
		$msg .= "\n\n";

		$ini_path = deskpro_install_guess_phpini_path();

		$msg .= "The path to php.ini that is being use on the command-line:\n" . $ini_path . "\n\n";

		if (defined('DP_BOOT_MODE') && DP_BOOT_MODE == 'cron') {
			$msg_codes = array();
			foreach ($errors_codes as $e) {
				$msg_codes[] = 'error: ' . $e;
			}

			if ($ini_path) {
				$msg_codes[] = "ini_path: $ini_path";
			}
			@file_put_contents(dp_get_log_dir().'/cron-boot-errors.log', $msg . "###\n\n" . implode("\n", $msg_codes));
		}

		echo $msg;
	} else {

		$is_installed = file_exists(dp_get_data_dir().'/is_installed.dat');
		if (!$is_installed && isset($_GET['phpinfo'])) {
			phpinfo();
			exit;
		}

		if (!deskpro_install_check_version() && version_compare(phpversion(), '5.3', '<')) {
			$v = phpversion();
			$errors = 'DeskPRO requires PHP version v5.3.2 (or v5.4.x) to function. Your server currently has <a href="?phpinfo">PHP v'.$v.'</a> installed. Support for the version of PHP you have installed was ended by <a href="http://php.net/archive/2010.php">The PHP Group in 2010</a> and it is strongly recommended you upgrade.';

			if (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') {
				if (!empty($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'IIS') !== false) {
					$errors .= '<br/><br />To continue you should install the latest version of PHP for IIS. You can do this directly <a href="http://php.iis.net/">Microsoft\'s IIS & PHP website</a>.';
				} else {
					$errors .= '<br/><br />To continue you should install a more recent version of PHP. You can download PHP directly from the <a href="http://windows.php.net/">PHP.net website</a>, or you might wish to install one of the following distributions which include PHP along with other server software such as MySQL and Apache: <a href="http://www.wampserver.com/en/">WampServer</a>, <a href="http://php.iis.net/">PHP on IIS</a>, <a href="http://www.easyphp.org/">EasyPHP</a> or <a href="http://www.apachefriends.org/en/xampp-windows.html">XAMPP</a>.';
				}
			} else {
				$errors .= '<br /><br />To continue you should install a more recent version of PHP. You can download the PHP sources from the <a href="http://php.net/">PHP.net website</a>.';
			}
		} else {
			$errors = '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
		}


		echo deskpro_install_basic_error($errors);
	}
	exit;
}
unset($errors);