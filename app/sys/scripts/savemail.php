<?php if (!defined('DP_ROOT')) exit('No access');
/**
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage SystemScripts
 * @copyright Copyright (c) 2010 DeskPRO (http://www.deskpro.com/)
 * @license http://www.deskpro.com/license-agreement DeskPRO License
 */

/**
 * This handles a raw email submitted via a PUT request.
 */

require DP_ROOT.'/sys/load_config.php';
dp_load_config();

#------------------------------
# Verify auth
#------------------------------

if (!defined('DP_SAVEMAIL_AUTH')) {
	echo "DP_SAVEMAIL_AUTH_UNDEFINED";
	exit(1);
}

if (!isset($_GET['auth']) || $_GET['auth'] != DP_SAVEMAIL_AUTH) {
	echo "DP_SAVEMAIL_AUTH_INVALID";
	exit(1);
}

if (!isset($_FILES['mailfile']) || !empty($_FILES['mailfile']['error']) || empty($_FILES['mailfile']['tmp_name'])) {
	echo "DP_MAILFILE_INVALID";
	exit(1);
}

#------------------------------
# Verify address
#------------------------------

if (!dp_get_config('savemail_accept_all')) {
	$invalid_mail = false;

	if (isset($_GET['cat'])) {
		try {
			$pdo = new \PDO("mysql:host={$DP_CONFIG['db']['host']};dbname={$DP_CONFIG['db']['dbname']}", $DP_CONFIG['db']['user'], $DP_CONFIG['db']['password']);
			$st = $pdo->prepare("SELECT id FROM email_gateway_addresses WHERE match_pattern = ?");
			$st->execute(array($_GET['cat']));

			if (!$st->fetchColumn()) {
				$invalid_mail = true;
			}
		} catch (\Exception $e) {
			$invalid_mail = true;
		}
	} else {
		$invalid_mail = true;
	}

	if ($invalid_mail) {
		echo "DP_UNKNOWN_CAT";
		exit(1);
	}
}

#------------------------------
# Verify save directories
#------------------------------

if (!defined('DP_SAVEMAIL_DIR')) {
	define('DP_SAVEMAIL_DIR', dp_get_data_dir() . '/emailstore');
}

if (!is_dir(DP_SAVEMAIL_DIR)) {
	if (!mkdir(DP_SAVEMAIL_DIR, 0777, true)) {
		echo "DP_SAVEMAIL_DIR_INVALID";
		exit(1);
	}

	$current_umask = umask();
	umask(0000);
	@chmod(DP_SAVEMAIL_DIR, 0777);
	umask($current_umask);
}

$cat = isset($_GET['cat']) ? $_GET['cat'] : 'default';

if (!preg_match('#^[a-zA-Z0-9\-_][a-zA-Z0-9\-_.@]*$#', $cat)) {
	echo "DP_INVALID_CAT";
	exit(1);
}

$cat = strtolower($cat);
$cat_dir = DP_SAVEMAIL_DIR . '/' . $cat;

if (!is_dir($cat_dir)) {
	if (!mkdir($cat_dir, 0777, true)) {
		echo "DP_SAVEMAIL_DIR_MAKE_ERROR";
		exit(1);
	}

	$current_umask = umask();
	umask(0000);
	@chmod($cat_dir, 0777);
	umask($current_umask);
}

#------------------------------
# Save input
#------------------------------

$name = date('Y-m-d.H-i-s') . '-' . mt_rand(100000000,999999999) . '.eml';

$tmp_path = dp_get_tmp_dir() . '/tmp_eml_' . $name;
if (!is_dir(dp_get_tmp_dir())) {
	if (!mkdir(dp_get_tmp_dir(), 0777, true)) {
		echo "DP_SAVEMAIL_TMPDIR_MAKE_ERROR";
		exit(1);
	}

	$current_umask = umask();
	umask(0000);
	@chmod(dp_get_tmp_dir(), 0777);
	umask($current_umask);
}

move_uploaded_file($_FILES['mailfile']['tmp_name'], $tmp_path);

$current_umask = umask();
umask(0000);
chmod($tmp_path, 0777);
umask($current_umask);

rename($tmp_path, $cat_dir . '/' . $name);

echo "DP_MAIL_ACCEPT";