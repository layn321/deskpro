#!/usr/bin/php
<?php
/*
 * This script is called with a local directory name and then a remote SFTP server.
 * All files in the local directory are uploaded to the remote server and then deleted.
 *
 * It is meant to be used with the DeskPRO blob service option that copies all blobs
 * to a local directory. Use this script to offload those copies elsewhere for backup.
 */

$options = getopt('v', array(
	'local:',
	'remote:',
	'remote-pubkey:',
	'remote-privkey:',
	'remote-passphrase:',
	'verbose',
	'logfile:'
));

$usage = false;
if (empty($options['local']) || empty($options['remote'])) {
	$usage = true;
}

if (!empty($options['remote-pubkey']) && empty($options['remote-privkey'])) {
	$usage = true;
}

if (!extension_loaded('ssh2')) {
	$usage = true;
}

$is_verbose = (isset($options['v']) || isset($options['verbose']));

$remote_host = null;
$remote_port = 22;
$remote_user = null;
$remote_path = null;
$remote_pass = null;
if (!empty($options['remote'])) {
	$info = @parse_url('sft://' . $options['remote']);
	if ($info && !empty($info['host']) && !empty($info['user']) && !empty($info['path'])) {
		$remote_host = $info['host'];
		$remote_user = $info['user'];
		$remote_path = rtrim($info['path'], '/');

		if (!empty($info['port'])) {
			$remote_port = $info['port'];
		}
		if (!empty($info['pass'])) {
			$remote_pass = $info['pass'];
		}
	} else {
		$usage = true;
	}
}

if ($usage) {
	echo "Usage: backup-blob-copies.php --local path --remote user@remote-server.com/path/to/store [--remote-pubfile path.pub --remote-privkey key] [--remote-privkey-passphrase phrase]";
	echo "\n\n";
	echo "\tverbose:\tOutput info. Quiet by default (suitable for cron) so enable this if you want to see things.\n";
	echo "\tlocal:\tThe local path to process\n";
	echo "\tremote:\tThe remote server and path to save to\n";
	echo "\tremote-pubkey:\t(Optional) The public key to auth with\n";
	echo "\tremote-privkey:\t(Optional) The private key to auth with\n";
	echo "\tremote-passphrase:\t(Optional) The passphrase used with the above key\n";
	echo "\tlogfile:\t(Optional) Path to a logfile that all messages will be written to\n";
	echo "\n\n";
	if (!extension_loaded('ssh2')) {
		echo "THIS SCRIPT REQUIRES THE SSH2 EXTENSION:\n";
		echo "http://www.php.net/manual/en/book.ssh2.php\n\n";
	}
	exit;
}

########################################################################################################################
# Helpers
########################################################################################################################

$dp_log_messages = array();
$dp_log_level = 7;
$logfile = !empty($options['logfile']) ? $options['logfile'] : null;

function dp_log($msg)
{
	global $dp_log_messages, $dp_log_level, $logfile, $is_verbose;

	$args = func_get_args();
	array_shift($args);
	$argc = count($args);

	$last = isset($args[$argc-1]) ? $args[$argc-1] : null;
	$level = -1;
	$prefix = '';
	if ($last == 'DEBUG') {
		$level = 7;
	} elseif ($last == 'INFO') {
		$level = 6;
		$prefix = 'INFO: ';
	} elseif ($last == 'NOTICE') {
		$level = 5;
		$prefix = 'NOTICE: ';
	} elseif ($last == 'WARN') {
		$level = 4;
		$prefix = 'WARN: ';
	} elseif ($last == 'ERR') {
		$level = 3;
		$prefix = '!!! ERR: ';
	} elseif ($last == 'CRIT') {
		$level = 3;
		$prefix = '!!! CRIT: ';
	} elseif ($last == 'ALERT') {
		$level = 1;
		$prefix = '!!! ALERT: ';
	} elseif ($last == 'EMERG') {
		$level = 0;
		$prefix = '!!! EMERG: ';
	}

	if ($level != -1) {
		array_pop($args);
		if ($level < $dp_log_level) {
			$dp_log_level = $level;
		}
	} else {
		$level = 7;
	}

	$msg = vsprintf($msg, $args);
	$msg = "[" . date('Y-m-d H:i:s') . '] ' . $prefix . $msg . "\n";

	$dp_log_messages[] = $msg;

	if ($is_verbose || $level <= 5) {
		echo $msg;
	}

	if ($logfile) {
		@file_put_contents($logfile, $msg, \FILE_APPEND);
	}
}

########################################################################################################################
# Build filelist
########################################################################################################################

if (!is_dir($options['local'])) {
	dp_log("Local directory does not exist: %s", $options['local'], 'NOTICE');
	dp_log("No files need uploading.");
	exit(0);
}

$filelist = array();

$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($options['local']), RecursiveIteratorIterator::LEAVES_ONLY);
foreach($objects as $name => $x){
    $filelist[] = $name;
}

if (!$filelist) {
	dp_log("No files need uploading.");
	exit(0);
}

dp_log("Files that will be uploaded: %d", count($filelist));


########################################################################################################################
# Connect
########################################################################################################################

$t = microtime(true);
dp_log("Connecting...");

$conn = ssh2_connect($remote_host, $remote_port);
if (!$conn) {
	dp_log("Failed to connect", 'ERR');
	exit(1);
}

if (!empty($options['remote-pubkey'])) {
	if (!ssh2_auth_pubkey_file($conn, $remote_user, $options['remote-pubkey'], $options['remote-privkey'], $options['remote-passphrase'] ?: null)) {
		dp_log("Failed to authenticate with public key", 'ERR');
		exit(1);
	}
} elseif (!empty($remote_pass)) {
	if (!ssh2_auth_password($conn, $remote_user, $remote_pass)) {
		dp_log("Failed to authenticate with password", 'ERR');
		exit(1);
	}
} else {
	if (ssh2_auth_none($conn, $remote_user) !== true) {
		dp_log("Failed to authenticate with no auth", 'ERR');
		exit(1);
	}
}

$sftp_conn = ssh2_sftp($conn);

dp_log("Connected in %.3fs", microtime(true) - $t);


########################################################################################################################
# Process Files
########################################################################################################################

$t_all = microtime(true);
$checked_dirs = array();

foreach ($filelist as $file) {
	$t = microtime(true);
	dp_log("Uploading %s...", $file);

	$filename = basename($file);
	$dirname  = rtrim(dirname($file), '/');
	$dirname  = rtrim(substr($dirname, strlen($options['local'])) , '/');

	$remote_dir  = $remote_path . '/' . $dirname;
	$remote_file = $remote_dir . '/' . $filename;

	if (!isset($checked_dirs[$dirname])) {
		$dirstat = @ssh2_sftp_stat($sftp_conn, $remote_dir);
		if (!$dirstat) {
			if (!ssh2_sftp_mkdir($sftp_conn, $remote_dir, 0755, true)) {
				dp_log("Failed to create remote dir: %s", $remote_dir, 'ERR');
				continue;
			}
		}

		$checked_dirs[$dirname] = true;
	}

	// Upload the file
	if (!ssh2_scp_send($conn, $file, $remote_file, 0644)) {
		dp_log("Failed to upload remote file: %s -> %s", $file, $remote_file, 'ERR');
		continue;
	}

	if (!unlink($file)) {
		dp_log('Failed to unlink local file: %s', $file, 'ERR');
	}

	dp_log("Done in %.3fs", microtime(true) - $t);
}

dp_log("Done all in %.3fs", microtime(true) - $t_all);

if (!$is_verbose && $dp_log_level <= 3) {
	echo implode('', $dp_log_messages);
}