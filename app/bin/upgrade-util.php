<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
|                                                                          |
| The license agreement under which this software is released              |
| can be found at http://www.deskpro.com/license                           |
|                                                                          |
| By using this software, you acknowledge having read the license          |
| and agree to be bound thereby.                                           |
|                                                                          |
| Please note that DeskPRO is not free software. We release the full       |
| source code for our software because we trust our users to pay us for    |
| the huge investment in time and energy that has gone into both creating  |
| this software and supporting our customers. By providing the source code |
| we preserve our customers' ability to modify, audit and learn from our   |
| work. We have been developing DeskPRO since 2001, please help us make it |
| another decade.                                                          |
|                                                                          |
| Like the work you see? Think you could make it better? We are always     |
| looking for great developers to join us: http://www.deskpro.com/jobs/    |
|                                                                          |
| ~ Thanks, Everyone at Team DeskPRO                                       |
\**************************************************************************/

/**
 * DeskPRO
 *
 * @package DeskPRO
 * @subpackage Tools
 */

namespace DeskPRO\Tools;

########################################################################################################################
# Boot up
########################################################################################################################

if (php_sapi_name() != 'cli') {
	echo "This script must only be run from the CLI.\n";
	echo "Contact support@deskpro.com if you require assistance.\n";
	exit(1);
}

define('DP_START_DIR', getcwd());

if (!defined('DP_ROOT')) {
	define('DP_ROOT', realpath(dirname(__FILE__) . '/../'));
}

if (!defined('DP_WEB_ROOT')) {
	define('DP_WEB_ROOT', realpath(dirname(__FILE__) . '/../../'));
}

if (!defined('DP_CONFIG_FILE')) {
	define('DP_CONFIG_FILE', DP_WEB_ROOT.'/config.php');
}

@ini_set('memory_limit', -1);
@ini_set('memory_limit', 268435456);
@set_time_limit(0);

// Normalise env
setlocale(LC_CTYPE, 'C');
date_default_timezone_set('UTC');
ini_set('default_charset', 'UTF-8');

require_once DP_ROOT . '/src/Application/InstallBundle/Install/server_check_functions.php';
require_once DP_ROOT . '/sys/load_config.php';

require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/HttpKernel/Util/Filesystem.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Process/ExecutableFinder.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Finder/Finder.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Finder/Glob.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Finder/SplFileInfo.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Finder/Iterator/RecursiveDirectoryIterator.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Finder/Iterator/ExcludeDirectoryFilterIterator.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Finder/Iterator/FileTypeFilterIterator.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Finder/Iterator/FilenameFilterIterator.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Output/OutputInterface.php';
require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Formatter/OutputFormatterInterface.php';
require_once DP_ROOT.'/src/Orb/Util/Numbers.php';
require_once DP_ROOT.'/src/Orb/Util/Env.php';
require_once DP_ROOT.'/src/Application/DeskPRO/LowUtil/RemoteRequest.php';

dp_load_config();

if (!@ini_get('error_log')) {
	@ini_set('error_log', dp_get_log_dir() . '/server-phperr-cli.log');
}

if (!defined('DP_MA_SERVER')) {
	define('DP_MA_SERVER', 'http://www.deskpro.com/members');
}

// Current build time
if (file_exists(DP_ROOT.'/sys/config/build-time.php')) {
	$build_file = @file_get_contents(DP_ROOT.'/sys/config/build-time.php');
	if ($build_file) {
		$m = null;
		if (preg_match('#([0-9]{10})#', $build_file, $m)) {
			define('DP_ORIG_BUILD_TIME', $m[1]);
		}
	}
}

define('DP_UPGRADE_STARTTIME', microtime(true));

########################################################################################################################
# Basic requirement checks
########################################################################################################################

$errors = array();

if (!deskpro_install_check_version()) {
	$errors[] = "The version of PHP you have is too old. DeskPRO requires PHP v5.3.2 or newer. You need to upgrade your version.";
}

if (!deskpro_install_check_pcre()) {
	$errors[] = "PHP is configured with a `pcre.backtrack_limit` value that is too low. Edit your php.ini configuration and change it to at least 100000.";
}

if (!deskpro_install_check_safemode()) {
	$errors[] = "PHP currently has <code>safe_mode</code> enabled. DeskPRO requires safe_mode to be set to \"Off\". You need to edit your PHP configuration to make this change.";
}

if (deskpro_install_check_pdo()) {
	if (deskpro_install_check_pdo_mysql()) {
		// ok
	} else {
		$errors[] = "PDO (http://php.net/manual/en/book.pdo.php) is installed, but the MySQL driver is not. You need to install pdo_mysql into your php.ini file.";
	}
} else {
	$errors[] = "PDO (http://php.net/manual/en/book.pdo.php) is not installed. You need to install PDO into your php.ini file.";
}

if ($errors) {
	echo "There are problems with your server or PHP configuration that prevents this tool from running:\n";
	foreach ($errors as $e) {
		echo "- " . $e;
		echo "\n";
	}
	echo "\n";
	echo "We have automatically detected the path to your php.ini file at:\n";
	echo \Orb\Util\Env::getPhpIniPath();
	echo "\n\n";
	echo "If you require assistance, email support@deskpro.com\n\n";
	exit(1);
}
unset($errors);

########################################################################################################################
# Upgrade class
########################################################################################################################

class Upgrade
{
	const MIN_FILESIZE_CHECK = 36700160;

	/**
	 * @var array
	 */
	protected $argv;

	/**
	 * @var array
	 * @see getLatestVersion()
	 */
	protected $latest_version = null;

	/**
	 * @var resource
	 * @see log
	 */
	protected $log_fh;

	/**
	 * @var string
	 */
	protected $file_backup;

	/**
	 * @var string
	 */
	protected $db_backup;

	/**
	 * 'files' to revert files
	 * 'db' to revert files and db
	 * @var string
	 */
	protected $revert_checkpoint;

	/**
	 * @var ZipStrategy
	 */
	protected $zip;

	public function run(array $argv)
	{
		$this->argv = $argv;

		if (in_array('--auto', $argv)) {
			register_shutdown_function('DeskPRO\\Tools\\Upgrade_Shutdown_Function');
			$this->runAction_auto();
		}

		$this->checkEnv();
		register_shutdown_function('DeskPRO\\Tools\\Upgrade_Shutdown_Function');

		if (in_array('--help', $argv)) {
			$this->runAction_help();
		} elseif (in_array('--check-version', $argv)) {
			$this->runAction_checkVersion();
		} elseif (in_array('--download-latest', $argv)) {
			$this->runAction_downloadLatest();
		} elseif (in_array('--backup-db', $argv)) {
			$this->runAction_backupDatabase();
		} elseif (in_array('--restore-db', $argv)) {
			$this->runAction_restoreDatabase();
		} elseif (in_array('--backup-files', $argv)) {
			$this->runAction_backupFiles();
		} elseif (in_array('--restore-files', $argv)) {
			$this->runAction_restoreFiles();
		} elseif (in_array('--install-latest-files', $argv)) {
			$this->runAction_installLatestFiles();
		} elseif (in_array('--run-db-upgrade', $argv)) {
			$this->runAction_dbUpgrade();
		} elseif (in_array('--dev-test-log', $argv)) {
			$this->sendLog();
		} elseif (in_array('--dev-test-log-e', $argv)) {
			$this->sendLog(new \Exception("Testing error"));
		} else {
			$this->runAction_interactive();
		}
	}

	protected function checkEnv()
	{
		try {
			if (!is_dir($this->getBackupDir()) || !is_writable($this->getBackupDir())) {
				$this->outAndLog("Backup directory does not exist or is not writable: " . $this->getBackupDir());
				exit(1);
			}

			if (!is_dir($this->getLogDir()) || !is_writable($this->getLogDir())) {
				$this->outAndLog("Log directory does not exist or is not writable: " . $this->getLogDir());
				exit(1);
			}

			if (!is_dir($this->getTmpDir()) || !is_writable($this->getTmpDir())) {
				$this->outAndLog("Tmp directory does not exist or is not writable: " . $this->getTmpDir());
				exit(1);
			}
		} catch (\Exception $e) {} // to catch error about log

		try {
			$this->zip = new ZipStrategy($this);
		} catch (\Exception $e) {
			$this->outAndLog("To use this tool, the zlib or Zip PHP extensions must be enabled.");
			exit(1);
		}

		try {
			// Empty the db first
			$pdo = $this->newDb();
		} catch (\Exception $e) {
			$this->outAndLog("There was a problem connecting to the database: " . $e->getMessage());
			exit(1);
		}

		try {
			$tables = $pdo->query("SHOW TABLES")->fetchColumn(0);
			if (!$tables) {
				$this->outAndLog("Your database appears to be empty. Did you mean to run the import.php command?");
				exit(1);
			}
		} catch (\Exception $e) { }
	}


	/**
	 * @param $string
	 */
	public function out($string, $nl = true)
	{
		echo $string;
		if ($nl) {
			echo "\n";
		}
	}


	/**
	 * @param $string
	 */
	public function outAndLog($string, $nl = true)
	{
		$this->out($string, $nl);
		$this->log($string);
	}


	/**
	 * Log a message
	 *
	 * @param $string
	 * @throws \Exception
	 */
	public function log($string)
	{
		static $has_opened = false;

		if (!$has_opened) {
			// Reset log
			@file_put_contents($this->getLogDir() . '/upgrade.log', '');
		}
		$has_opened = true;

		if (!$this->log_fh) {
			$this->log_fh = fopen($this->getLogDir() . '/upgrade.log', 'a');
			if (!$this->log_fh) {
				throw new \Exception("Could not open log file: " . $this->getLogDir() . '/upgrade.log');
			}
			@chmod($this->getLogDir() . '/upgrade.log', 0777);

			$this->registerCleanupParam('close_log_fh', $this->log_fh);

			$this->log("(Command: " . implode(' ', $this->argv) . ")");
		}

		$string = trim($string);
		fwrite($this->log_fh, sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $string));
	}

	/**
	 * Log an exception
	 *
	 * @param \Exception $e
	 */
	public function logException(\Exception $e)
	{
		$this->log("-> {$e->getCode()} {$e->getMessage()}");

		$lines = $e->getTraceAsString();
		$lines = str_replace(DP_ROOT, '', $lines);
		$lines = explode("\n", $lines);

		foreach ($lines as $l) {
			$this->log("-> $l");
		}
	}


	####################################################################################################################
	# help
	####################################################################################################################

	public function runAction_help()
	{
		$this->out("Usage: php upgrade-util.php <action>");
		$this->out('');
		$this->out("Possible actions:");

		$this->out("\t--auto [--quiet]");
		$this->out("\t\tAutomatically checks for a newer version, and if one exists, will attempt to ");
		$this->out("\t\tdownload it, extract it and install it. Backups will be made to the backups directory.");
		$this->out('');
		$this->out("\t\t--quiet suppresses output. Ideal for automation. The log file will contain any");
		$this->out("\t\trelevant information.");
		$this->out('');

		$this->out("\t--check-version");
		$this->out("\t\tOutputs information about your current version and the latest version of DeskPRO available");
		$this->out('');

		$this->out("\t--run-db-upgrade");
		$this->out("\t\tIf the database is out of date with the files on the filesystem, then any required database");
		$this->out("\t\tupdates will be executed.");
		$this->out('');

		$this->out("\t--backup-db");
		$this->out("\t\tExecutes a mysqldump of your database into the data/backups directory");
		$this->out('');

		$this->out("\t--backup-files");
		$this->out("\t\tBacks up all DeskPRO files. Note: This will NOT back up the data/backups directory.");
		$this->out('');

		$this->out("\t--download-latest [--path <path>]");
		$this->out("\t\tDownloads the latest version of DeskPRO and saves it into the data/backups directory,");
		$this->out("\t\tunless you specify a path with --path.");
		$this->out('');

		$this->out("\t--install-latest-files [--path <zip-path>] --dry-run");
		$this->out("\t\tExtracts a ZIP and replaces current files with the ones from the ZIP. This does NOT upgrade");
		$this->out("\t\tthe database scheme. You still need to run upgrade.php after this to update the database.");
		$this->out('');
		$this->out("\t\tIf --path is supplied, the ZIP from --path will be used as the source. Otherwise, the latest");
		$this->out("\t\tsource is downloaded (same as running --download-latest).");
		$this->out('');
		$this->out("\t\tIf --dry-run is specified, no actual files will be overwritten or created. Your console will");
		$this->out("\t\tfill up with a log of files that will be copied.");
		$this->out('');
		$this->out("\t\tIt is recommended to run --backup-files before running this.");
		$this->out('');
		$this->out("\t\tNote that files are copied and overwritten, but old files remain. Any custom files you have");
		$this->out("\t\tuploaded will not be removed.");
		$this->out('');
	}

	####################################################################################################################
	# interactive
	####################################################################################################################

	public function runAction_interactive()
	{
		$interactive = new UpgradeInteractive($this);
	}


	####################################################################################################################
	# auto
	####################################################################################################################

	public function runAction_auto()
	{


		$time_start = microtime(true);

		$is_quiet        = in_array('--quiet', $this->argv);
		$is_error_halt = true;
		$is_status_write = in_array('--write-status-file', $this->argv);

		$skip_file_backup = in_array('--skip-backup-file', $this->argv);
		$skip_db_backup   = in_array('--skip-backup-db', $this->argv);

		if ($is_status_write) {

			if (file_exists(DP_WEB_ROOT . '/auto-update-status.php') && !unlink(DP_WEB_ROOT . '/auto-update-status.php')) {
				$this->outAndLog("Could not delete previous auto-update-status.log file");
				exit(1);
			}

			$that = $this;
			$write_status = function($code, $message = '') use ($that) {
				$fp = fopen(DP_WEB_ROOT . '/auto-update-status.php', 'a');
				$time = microtime(true);

				if (is_array($message)) {
					$message = json_encode($message);
				}

				// Wont ever happen, but best be sure
				$message = str_replace('<?', '< ?', $message);

				$status = "STATUS(" . $code . ")@$time#$message\n";

				fwrite($fp, $status);
				fclose($fp);

				$that->log($status);

				@file_put_contents(DP_WEB_ROOT . '/auto-update-is-running.trigger', 'This file indicates that the system is performing an upgrade. Helpdesk requests will be disabled until the upgrade finishes.');
				@file_put_contents(dp_get_tmp_dir() . '/auto-upgrade-started', time());
			};

			if (!($fp = fopen(DP_WEB_ROOT . '/auto-update-status.php', 'w'))) {
				$this->outAndLog("Could not write update status file");
				exit(1);
			}

			fclose($fp);

		} else {
			$write_status = function($code, $message = '') {
				// null
			};
		}

		$write_status("start");
		@chmod(DP_WEB_ROOT . '/auto-update-status.php', 0777);

		#----------------------------------------
		# Requirement Checks
		#----------------------------------------

		$write_status("basic_checks_start");

		$checks_fail = false;

		#---
		# Binary Paths
		#---

		$php_path        = dp_get_php_path(true);
		$this->log("php: $php_path\n");

		if (!$skip_db_backup) {
			$mysql_dump_path = dp_get_mysqldump_path(true);
			$this->log("mysqldump: $mysql_dump_path\n");
		} else {
			$mysql_dump_path = null;
		}

		if ($is_status_write) {
			if ($php_path) $write_status('php_path_okay'); else $write_status('error_php_path');

			if (!$skip_db_backup) {
				if ($mysql_dump_path) $write_status('mysqldump_path_okay'); else $write_status('error_mysqldump_path');
			}
		}

		if (!$php_path || (!$skip_db_backup && !$mysql_dump_path)) {
			$unknown_binary_paths = array();
			if (!$php_path)        $this->outAndLog("Cannot find path to `php` CLI");

			if (!$skip_db_backup) {
				if (!$mysql_dump_path) $this->outAndLog("Cannot find path to `mysqldump` binary");
			}

			$write_status("error_unknown_binary", $unknown_binary_paths);
			$checks_fail = true;
		}

		#---
		# Requirements check
		#---

		try {
			if (!$skip_file_backup || !$skip_db_backup) {
				if (!is_dir($this->getBackupDir()) || !is_writable($this->getBackupDir())) {
					$write_status('error_backup_dir', $this->getBackupDir());
					$this->outAndLog("Backup directory does not exist or is not writable: " . $this->getBackupDir());
					$checks_fail = true;
				} else {
					$write_status('backup_dir_okay');
				}
			}

			if (!is_dir($this->getLogDir()) || !is_writable($this->getLogDir())) {
				$write_status('error_log_dir', $this->getLogDir());
				$this->outAndLog("Log directory does not exist or is not writable: " . $this->getLogDir());
				$checks_fail = true;
			} else {
				$write_status('log_dir_okay');
			}

			if (!is_dir($this->getTmpDir()) || !is_writable($this->getTmpDir())) {
				$write_status('error_tmp_dir', $this->getTmpDir());
				$this->outAndLog("Tmp directory does not exist or is not writable: " . $this->getTmpDir());
				$checks_fail = true;
			} else {
				$write_status('tmp_dir_okay');
			}
		} catch (\Exception $e) {} // to catch error about log

		try {
			$this->zip = new ZipStrategy($this);
			$write_status('zip_ext_okay');
		} catch (\Exception $e) {
			$write_status('error_zip_ext', \Orb\Util\Env::getPhpIniPath());
			$this->outAndLog("To use this tool, the zlib or Zip PHP extensions must be enabled.");
			$checks_fail = true;
		}

		$req_strategy = \DeskPRO_LowUtil_RemoteRequester::detectStrategy();
		if ($req_strategy) {
			$write_status('remoterequester_okay', $req_strategy);
		} else {
			$write_status('error_remoterequester', \Orb\Util\Env::getPhpIniPath());
			$this->outAndLog("CURL not enabled and allow_url_fopen disabled, there is no way to download files");
			$checks_fail = true;
		}

		// Check for disabled functions
		$disabled_f = array();
		foreach (array(
			'escapeshellarg',
			'exec',
			'passthru',
			'chdir',
			'proc_open'
		) as $f) {
			if (\Orb\Util\Env::isFunctionDisabled($f)) {
				$disabled_f[] = $f;
			}
		}

		if ($disabled_f) {
			$write_status('error_disabled_functions', implode(', ', $disabled_f));
			$this->outAndLog("These functions are disabled: " . implode($disabled_f));
			$checks_fail = true;
		}

		#---
		# File permissions: CHeck a few dirs/files to make sure we can write them all
		#---

		$check = array(
			DP_ROOT,
			DP_ROOT.'/src',
			DP_ROOT.'/sys',
			DP_ROOT.'/sys/cache',
			DP_ROOT.'/sys/cache',
			DP_ROOT.'/sys/cache/prod',
			DP_ROOT.'/sys/system.php',
			DP_ROOT.'/sys/vendor',
		);

		$write_fail = false;
		foreach ($check as $f) {
			if (file_exists($f) && !is_writable($f)) {
				$write_fail = $f;
				break;
			}
		}
		if ($write_fail) {

			$this->log("Failed write check on: $write_fail");

			$guess_user = 'unknown';
			if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
				$uinfo = @posix_getpwuid(@posix_geteuid());
				if (isset($uinfo['name'])) {
					$guess_user = $uinfo['name'];
				}
			} elseif (function_exists('get_current_user')) {
				if (@get_current_user()) {
					$guess_user = @get_current_user();
				}
			}

			$owner_user = 'unknown';
			if (function_exists('posix_geteuid') && function_exists('fileowner')) {
				$uinfo = @posix_getpwuid(@fileowner($write_fail));
				if (isset($uinfo['name'])) {
					$owner_user = $uinfo['name'];
				}
			}

			$checks_fail = true;
			$write_status("error_permissions", sprintf("User who is running the utility: %s, User who owns the files: %s", $guess_user, $owner_user));
			$this->outAndLog("Found insufficient write permissions to DeskPRO files. Does this user own them or have permission to write?");
			$this->outAndLog(sprintf("Current user: %s, File owner: %s", $guess_user, $owner_user));
		} else {
			$write_status("permissions_okay");
		}

		if ($checks_fail) {
			$write_status("error_basic_checks_fail");
			$this->outAndLog("Failed basic checks");

			@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
			@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');

			$e = new \RuntimeException("Failed basic checks");
			$this->sendLog($e);
			exit(10);
		}

		$write_status("basic_checks_done");

		#----------------------------------------
		# Make sure we can comm with DeskPRO server
		#----------------------------------------

		try {
			$this->getLatestVersion();
		} catch (ServiceCallException $e) {
			@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
			@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');

			$write_status("error_server_comm", $e->getMessage());
			$this->outAndLog("Error communicating with server: " . $e->getMessage());
			$this->sendLog($e);
			exit(13);
		}

		#----------------------------------------
		# Check if we need an upgrade at all
		#----------------------------------------

		if (!$this->isInstanceOutdated()) {
			$write_status("done");
			if (!$is_quiet) {
				$this->out("You are all up to date.");
			}

			@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
			@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');
			exit(0);
		}

		#----------------------------------------
		# Download newest version
		#----------------------------------------

		try {
			$write_status("downloading_update_start");
			if (!$is_quiet) $this->out("Downloading latest source ...");
			$new_source_zip = $this->downloadLatest();
			if (!$is_quiet) $this->out("-> Done");
			if (!$is_quiet) $this->out('-> Filesize: ' . filesize($new_source_zip));

			$this->log('Downloaded ZIP filesize: ' . filesize($new_source_zip));

			if (filesize($new_source_zip) < Upgrade::MIN_FILESIZE_CHECK) {
				throw new \InvalidArgumentException("Downloaded zip is smaller than expected, it probably failed to fully download");
			}

			$write_status("downloading_update_done");
		} catch (\Exception $e) {
			@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
			@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');

			$write_status("error_downloading_update", $e->getMessage());
			$this->out($e->getCode() . ' ' . $e->getMessage());
			$this->logException($e);
			$this->sendLog($e);
			exit(20);
		}

		#----------------------------------------
		# Do upgrade
		#----------------------------------------

		// Shutdown helpdesk
		$fileutil = new FilesystemUtil();

		try {
			$write_status("file_backup_start");
			if (!$skip_file_backup) {
				if (!$is_quiet) $this->out("Doing file backup ...");
				$this->file_backup = $this->backupFiles(function($status) use ($write_status) {
					$write_status('file_backup_' . $status);
				});
				if (!$is_quiet) $this->out("-> Done");
			} else {
				$this->log('file backup skipped');
			}
			$write_status("file_backup_done");

			if ($this->file_backup) {
				$write_status("file_backup_loc", $this->file_backup . ' (' . Upgrade::getFilesizeDisplay(filesize($this->file_backup)) . ')');
			}

		} catch (\Exception $e) {
			$write_status("error_backup_files", $e->getMessage());
			$fileutil->remove(DP_WEB_ROOT.'/auto-update-is-running.trigger');
			$fileutil->remove(dp_get_tmp_dir() . '/auto-upgrade-started');
			$this->out($e->getCode() . ' ' . $e->getMessage());
			$this->logException($e);
			$this->sendLog($e);
			exit(14);
		}

		if (!$is_quiet) $this->out("Turning helpdesk off");
		$fileutil->touch(DP_WEB_ROOT.'/auto-update-is-running.trigger');
		$write_status('helpdesk_offline');

		try {
			$write_status("database_backup_start");
			if (!$skip_db_backup) {
				if (!$is_quiet) $this->out("Doing database backup ... ");
				$this->db_backup = $this->backupDatabase(true);
				if (!$is_quiet) $this->out("-> Done");
			} else {
				$this->log('database backup skipped');
			}
			$write_status("database_backup_end");

			if ($this->db_backup) {
				$write_status("database_backup_loc", $this->db_backup . ' (' . Upgrade::getFilesizeDisplay(filesize($this->db_backup)) . ')');
			}

		} catch (\Exception $e) {
			$write_status("error_backup_db", $e->getMessage());
			$fileutil->remove(DP_WEB_ROOT.'/auto-update-is-running.trigger');
			$fileutil->remove(dp_get_tmp_dir() . '/auto-upgrade-started');
			$this->out($e->getCode() . ' ' . $e->getMessage());
			$this->logException($e);
			$this->sendLog($e);
			exit(15);
		}

		try {
			$this->revert_checkpoint = 'files';

			$write_status("installing_files_start");
			if (!$is_quiet) $this->out("Installing latest source files ...");
			$failures = array();
			$this->installFilesFromZip($new_source_zip, false, $failures);
			if (!$is_quiet) $this->out("-> Done");

			if ($failures) {
				$write_status("error_installing_files", sprintf("%d files failed to install due to file permissions", count($failures)));
				$this->out(sprintf("%d files failed to install due to file permissions", count($failures)));

				$e = new \Exception(sprintf("%d files failed to install due to file permissions", count($failures)));
				$e->_dp_failures = $failures;

				$this->logException($e);
				$this->sendLog($e);
				exit(25);
			}

			$write_status("installing_files_done");

			// Remove downloaded zip
			@unlink($new_source_zip);
		} catch (\Exception $e) {
			$write_status("error_installing_files", $e->getMessage());
			$this->out($e->getCode() . ' ' . $e->getMessage());
			$this->logException($e);
			$this->sendLog($e);
			exit(25);
		}

		$this->revert_checkpoint = 'db';

		if (!$is_quiet) $this->out("Performing database upgrades ...");

		$write_status("updating_db_start");
		chdir(DP_ROOT . '/../');
		$cmd = "$php_path cmd.php dp:upgrade 2>&1";
		exec($cmd, $out, $ret);
		$write_status("updating_db_end");

		if ($ret) {
			$write_status("error_updating_db");
			$this->outAndLog("Upgrade returned erorr status $ret");
			if ($out) {
				foreach ($out as $l) {
					$this->outAndLog("-> $l");
				}
			}

			if (!$is_error_halt) {
				$write_status("reverting_files");
				$fileutil->touch(DP_WEB_ROOT.'/auto-update-is-running.trigger');
			}

			$e = new \RuntimeException("Error during upgrade");
			$this->sendLog($e);

			exit(30);
		}

		if (!$is_quiet) $this->out("-> Done");
		$fileutil->remove(DP_WEB_ROOT.'/auto-update-is-running.trigger');
		$fileutil->remove(dp_get_tmp_dir() . '/auto-upgrade-started');

		if (!$is_quiet) $this->out("Helpdesk turned on");
		$write_status('helpdesk_online');

		$write_status("done");

		$this->revert_checkpoint = null;
		$str = sprintf("Upgrade done in %.4f seconds", microtime(true) - $time_start);

		if ($is_quiet) {
			$this->log($str);
		} else {
			$this->outAndLog($str);
		}

		$this->sendLog();

		$this->postUpgrade();

		exit(0);
	}

	public function revertAutoUpgrade()
	{
		if ($this->revert_checkpoint == 'files' || $this->revert_checkpoint == 'db') {
			$this->installFilesFromZip($this->file_backup);
		}

		if ($this->revert_checkpoint == 'db'){
			$this->restoreDbFromZip($this->db_backup);
		}

		unlink(DP_WEB_ROOT.'/auto-update-is-running.trigger');
		@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');

		$this->revert_checkpoint = null;
	}

	####################################################################################################################
	# run-db-upgrade
	####################################################################################################################

	public function runAction_dbUpgrade()
	{
		$php_path = $this->getPhpBinaryPath();
		if (!$php_path) {
			$this->outAndLog("Cannot find path to the `php` CLI");
		}

		chdir(DP_ROOT . '/../');
		$cmd = dp_get_php_command('cmd.php', 'dp:upgrade');
		echo "> $cmd\n";
		passthru($cmd, $ret);

		return $ret;
	}

	####################################################################################################################
	# upgrade-files
	####################################################################################################################

	public function runAction_installLatestFiles()
	{
		$zip_path = null;
		$zip_specified = false;
		if (($key = array_search('--path', $this->argv)) !== false && isset($this->argv[$key+1])) {
			$zip_path = @realpath($this->argv[$key+1]);
			if (!file_exists($zip_path)) {
				$this->out("Invalid --path");
				exit(1);
			}

			$zip_specified = true;
		}

		if (!$zip_path) {
			$this->out("Downloading latest source ... ");
			$zip_path = $this->downloadLatest();
			$this->out("-> Done");

			$this->registerCleanupParam('unlink_zip_path', $zip_path);
		}

		$dry_run = in_array('--dry-run', $this->argv);

		$this->out("Installing files ... ");
		$this->installFilesFromZip($zip_path, $dry_run);
		$this->out("-> Done");

		if (!$zip_specified) {
			unlink($zip_path);
			$this->registerCleanupParam('unlink_zip_path', null);
		}
	}

	/**
	 * Replaces current source files with ones from $zip_path.
	 *
	 * Note: Only files that are in the source will be actually replaces. That means
	 * custom files remain (e.g., config.php).
	 *
	 * @param string $zip_path
	 */
	public function installFilesFromZip($zip_path, $dry_run = false, array &$failures = null)
	{
		if (!is_file($zip_path)) {
			throw new UpgradeFilesException("Zip path does not exist: $zip_path", UpgradeFilesException::BAD_ZIP);
		}

		if ($failures === null) {
			$failures = null;
		}

		$time_start = microtime(true);

		$this->log("installFilesFromZip: From $zip_path");

		#------------------------------
		# Extract the zip into the dir
		#------------------------------

		$e = false;
		$tmp_dir = $this->zip->decompressZip($zip_path, null, $e);

		if (!$tmp_dir) {
			throw new UpgradeFilesException("Failed to extract zip: $e", UpgradeFilesException::EXTRACT_ERROR);
		}

		$this->log("installFilesFromZip: Extracted to $tmp_dir");

		#------------------------------
		# Now copy everything over
		#------------------------------

		$fileutil = new FilesystemUtil();
		if ($dry_run) {
			$fileutil->enableDryRun();
			$this->log("installFilesFromZip: (dry run)");
		}

		$this->log("installFilesFromZip: Copying to " . DP_WEB_ROOT);

		// Delete old cache dir
		$fileutil->remove(DP_ROOT.'/sys/cache/dev');
		$fileutil->remove(DP_ROOT.'/sys/cache/prod');
		$fileutil->remove(DP_ROOT.'/sys/cache/doctrine-proxies');
		$fileutil->remove(DP_ROOT.'/sys/cache/twig-compiled');

		// Copy all files over
		$fileutil->mirror($tmp_dir, DP_WEB_ROOT, null, array(
			'override'        => true,
			'copy_on_windows' => true,
			'exclude'         => array('/web.config', '/.htaccess')
		));

		$this->registerCleanupParam('unlink_scratch_dir', null);

		// New build time
		$build_file = @file_get_contents(DP_ROOT.'/sys/config/build-time.php');
		if ($build_file) {
			$m = null;
			if (preg_match('#([0-9]{10})#', $build_file, $m)) {
				define('DP_NEW_BUILD_TIME', $m[1]);
			}
		}

		$this->log(sprintf('installFilesFromZip: time(%.4f)', microtime(true) - $time_start));
	}

	####################################################################################################################
	# backup-database
	####################################################################################################################

	public function runAction_backupDatabase()
	{
		try {
			$this->backupDatabase();
		} catch (MysqlBackupException $e) {
			$this->outAndLog($e->getMessage());
			exit(1);
		}
	}

	public function backupDatabase($overwrite = false)
	{
		global $DP_CONFIG;

		$time_start = microtime(true);

		$mysql_dump_path = $this->getMysqldumpBinaryPath();
		if (!$mysql_dump_path) {
			throw new MysqlBackupException("Could not find path to `mysqldump` command", MysqlBackupException::NO_MYSQLDUMP);
		}

		$f = date('Y-m-d-His') . '-database.sql';
		$f_full = $this->getBackupDir() . DIRECTORY_SEPARATOR . $f;

		if (file_exists($f_full)) {
			if ($overwrite) {
				unlink($f_full);
			}
			if (file_exists($f_full)) {
				throw new MysqlBackupException("Target backup file already exists: $f_full", MysqlBackupException::FILE_EXISTS);
			}
		}

		$pass = '';
		$log_pass = '';
		if ($DP_CONFIG['db']['password']) {
			$pass = "--password=" . escapeshellarg($DP_CONFIG['db']['password']);
			$log_pass = '--password=...';
		}

		$host = $DP_CONFIG['db']['host'];
		$port = null;
		if (preg_match('#^(.*?):([0-9]+)$#', $host, $m)) {
			$host = $m[1];
			$port = $m[2];
		}

		if ($port) {
			$cmd = sprintf(
				"%s --opt -Q -h%s --port=%s -u%s %s %s > %s",
				$mysql_dump_path,
				escapeshellarg($host),
				escapeshellarg($port),
				escapeshellarg($DP_CONFIG['db']['user']),
				$pass,
				escapeshellarg($DP_CONFIG['db']['dbname']),
				escapeshellarg($f)
			);

			$log_cmd = sprintf(
				"%s --opt -Q -h%s --port=%s -u%s %s %s > %s",
				$mysql_dump_path,
				escapeshellarg($host),
				escapeshellarg($port),
				escapeshellarg($DP_CONFIG['db']['user']),
				$log_pass,
				escapeshellarg($DP_CONFIG['db']['dbname']),
				escapeshellarg($f)
			);
		} else {
			$cmd = sprintf(
				"%s --opt -Q -h%s -u%s %s %s > %s",
				$mysql_dump_path,
				escapeshellarg($host),
				escapeshellarg($DP_CONFIG['db']['user']),
				$pass,
				escapeshellarg($DP_CONFIG['db']['dbname']),
				escapeshellarg($f)
			);

			$log_cmd = sprintf(
				"%s --opt -Q -h%s -u%s %s %s > %s",
				$mysql_dump_path,
				escapeshellarg($host),
				escapeshellarg($DP_CONFIG['db']['user']),
				$log_pass,
				escapeshellarg($DP_CONFIG['db']['dbname']),
				escapeshellarg($f)
			);
		}

		$this->log("Backup directory:  {$this->getBackupDir()}");
		$this->log("Backup command:    $log_cmd");

		$out = null;
		$ret = $this->execCommand($cmd, $this->getBackupDir(), $out);

		if ($ret) {
			$this->out("Backup error: Command exited with error status: $ret");

			foreach ($out as $l) {
				$this->out("-> " . $l);
			}

			throw new MysqlBackupException("Command exited with error status: $ret", MysqlBackupException::DUMP_ERROR);
		}

		// Try to verify that the complete dump is there
		$fh = fopen($f_full, 'r');
		fseek($fh, -256000, \SEEK_END);
		$code = fread($fh, 256000);

		if (strpos($code, 'CREATE TABLE `worker_jobs`') === false) {
			$this->log("Database dump seems invalid.");
			throw new MysqlBackupException("Database dump seems invalid", MysqlBackupException::DUMP_ERROR);
		}
		fclose($fh);
		unset($code);

		$this->log(sprintf("backupDatabase: time(%.4f)   dump_size(%d)", microtime(true) - $time_start, filesize($f_full)));
		$f_full = $this->compressFile($f_full);

		if (!$f_full) {
			return false;
		}

		$backup_path = $this->getBackupDir() . DIRECTORY_SEPARATOR . str_replace('.sql', '', $f) . '.zip';
		rename($f_full, $backup_path);

		return $backup_path;
	}

	public function getMysqldumpBinaryPath()
	{
		return dp_get_mysqldump_path();
	}

	public function getMysqlBinaryPath()
	{
		return dp_get_mysql_path();
	}

	public function getPhpBinaryPath()
	{
		return dp_get_php_path();
	}

	public function postUpgrade()
	{
		$touch_trigger = false;

		if (function_exists('apc_clear_cache')) {
			apc_clear_cache();
			apc_clear_cache('user');
			$touch_trigger = true;
		}
		if (function_exists('wincache_ucache_clear')) {
			wincache_ucache_clear();
			wincache_refresh_if_changed();
			$touch_trigger = true;
		}

		if ($touch_trigger) {
			// We need to trigger this on the web too, we do that
			// by touching this trigger file that the web kernel uses
			@touch(dp_get_tmp_dir() . '/apc-clear.trigger');
			@chmod(dp_get_tmp_dir() . '/apc-clear.trigger', 0777);
		}

		foreach (array('error.log', 'cli-phperr.log', 'server-phperr-cli.log', 'server-phperr-web.log') as $f) {
			$path = dp_get_log_dir() . DIRECTORY_SEPARATOR . $f;
			if (file_exists($path)) {
				@file_put_contents('', $path);
			}
		}

		@unlink(dp_get_tmp_dir() . DIRECTORY_SEPARATOR . 'dql.cache');
	}

	####################################################################################################################
	# restore-db
	####################################################################################################################

	public function runAction_restoreDatabase()
	{
		$fileutil = new FilesystemUtil();

		$zip_path = false;
		if (($key = array_search('--path', $this->argv)) !== false && isset($this->argv[$key+1])) {
			if ($fileutil->isAbsolutePath($this->argv[$key+1])) {
				$zip_path = @realpath($this->argv[$key+1]);
			} else {
				$zip_path = $this->getBackupDir() . DIRECTORY_SEPARATOR . $this->argv[$key+1];
			}
		}

		if (!$zip_path || !file_exists($zip_path)) {
			$this->out("Invalid --path. File does not exist: " . $zip_path);
			exit(1);
		}

		$this->out("Restoreing datbase ... ");
		$this->restoreDbFromZip($zip_path);
		$this->out("-> Done");
	}

	/**
	 * Drops everything from the current database, then installs dump
	 *
	 * @param string $zip_path
	 */
	public function restoreDbFromZip($zip_path)
	{
		if (!is_file($zip_path)) {
			throw new MysqlRestoreException("Zip path does not exist: $zip_path", MysqlRestoreException::BAD_ZIP);
		}

		$mysql_path = $this->getMysqlBinaryPath();
		if (!$mysql_path) {
			throw new MysqlBackupException("Could not find path to `mysql` command", MysqlRestoreException::NO_MYSQL);
		}

		$time_start = microtime(true);

		#------------------------------
		# Extract the zip into the dir
		#------------------------------

		$e = false;
		$tmp_dir = $this->zip->decompressZip($zip_path, null, $e);

		if (!$tmp_dir) {
			throw new UpgradeFilesException("Failed to extract zip: $e", MysqlRestoreException::EXTRACT_ERROR);
		}

		// Find the SQL file
		$finder = new \Symfony\Component\Finder\Finder();
		$finder->in($tmp_dir)->files()->name('*.sql');

		$file = null;
		foreach ($finder as $file) break;

		if ($file === null) {
			throw new UpgradeFilesException("No sql file in the zip", MysqlRestoreException::EXTRACT_ERROR);
		}

		/** @var $sql_filename \SplFileInfo */
		$sql_filename = $file->getFilename();

		#------------------------------
		# Drop everything from the database first
		#------------------------------

		global $DP_CONFIG;

		// Empty the db first
		$pdo = $this->newDb();
		$tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_NUM);

		$pdo->exec("SET foreign_key_checks = 0");
		foreach ($tables as $t) {
			$t = $t[0];
			$pdo->exec("DROP TABLE `$t`");
		}
		$pdo->exec("SET foreign_key_checks = 1");

		#------------------------------
		# Restore dump
		#------------------------------

		$pass = '';
		if ($DP_CONFIG['db']['password']) {
			$pass = "--password=".escapeshellarg($DP_CONFIG['db']['password']);
		}
		$cmd = sprintf(
			'%s -h%s -u%s %s %s < %s',
			$mysql_path,
			escapeshellarg($DP_CONFIG['db']['host']),
			escapeshellarg($DP_CONFIG['db']['user']),
			$pass,
			escapeshellarg($DP_CONFIG['db']['dbname']),
			escapeshellarg($sql_filename)
		);

		$ret = $this->execCommand($cmd, $tmp_dir);
		if ($ret) {
			throw new UpgradeFilesException("Error importing database backup", MysqlRestoreException::RESTORE_ERROR);
		}

		$this->log(sprintf("backupDatabase: time(%.4f)", microtime(true) - $time_start));
	}

	####################################################################################################################
	# backup-files
	####################################################################################################################

	public function runAction_backupFiles()
	{
		try {
			$this->backupFiles();
		} catch (FileBackupException $e) {
			$this->outAndLog($e->getMessage());
			exit(1);
		}
	}

	public function backupFiles($status_callback = null)
	{
		if (!$status_callback) {
			$status_callback = function($code) {};
		}

		$time_start = microtime(true);

		$f = DIRECTORY_SEPARATOR . date('Y-m-d-His') . '-files';
		$backup_dir = $this->getTmpDir() . $f;
		if (is_dir($backup_dir)) {
			throw new FileBackupException("Backup directory already exists: $backup_dir", FileBackupException::FILE_EXISTS);
		}

		if (!mkdir($backup_dir, 0755, true)) {
			throw new FileBackupException("Could not create backup directory: $backup_dir", FileBackupException::PERM_ERROR);
		}

		$status_callback('copy_start');

		$finder = new \Symfony\Component\Finder\Finder();
		$finder->in(DP_WEB_ROOT)->exclude(dp_get_data_dir())->files();

		$count_file = 0;
		$count_dir = 0;
		foreach ($finder as $file) {
			$file_rel_dir = str_replace(DP_WEB_ROOT, '', dirname($file->getRealPath()));
			$file_backup_dir = $backup_dir . $file_rel_dir;

			// Ignore data dir
			if (strpos($file->getRealPath(), dp_get_data_dir()) === 0) {
				continue;
			}

			if (!is_dir($file_backup_dir)) {
				$count_dir++;
				if (!mkdir($file_backup_dir, 0755, true)) {
					throw new FileBackupException("Could not create backup directory: $backup_dir", FileBackupException::PERM_ERROR);
				}
			}

			if (!copy($file->getRealPath(), $file_backup_dir . DIRECTORY_SEPARATOR . $file->getFilename())) {
				throw new FileBackupException("Could not copy file to backup directory: {$file->getRealPath()} to {$file_backup_dir}{$file->getFilename()}", FileBackupException::PERM_ERROR);
			}

			$count_file++;
		}

		$status_callback('copy_done');

		$this->log(sprintf("backupFiles: time(%.4f)   file_count(%d)    dir_count(%d)", microtime(true) - $time_start, $count_file, $count_dir));

		$status_callback('zip_start');

		$f_path = $this->compressFile($backup_dir);

		$status_callback('zip_done');

		if (!$f_path) {
			return false;
		}

		$status_callback('cleanup_start');

		$backup_file = $this->getBackupDir() . DIRECTORY_SEPARATOR . $f . '.zip';
		if (is_file($backup_file)) {
			unlink($backup_file);
		}
		rename($f_path, $backup_file);

		$status_callback('cleanup_done');

		return $backup_file;
	}


	####################################################################################################################
	# restore-files
	####################################################################################################################

	public function runAction_restoreFiles()
	{
		$fileutil = new FilesystemUtil();

		$zip_path = false;
		if (($key = array_search('--path', $this->argv)) !== false && isset($this->argv[$key+1])) {
			if ($fileutil->isAbsolutePath($this->argv[$key+1])) {
				$zip_path = @realpath($this->argv[$key+1]);
			} else {
				$zip_path = $this->getBackupDir() . DIRECTORY_SEPARATOR . $this->argv[$key+1];
			}
		}

		if (!$zip_path || !file_exists($zip_path)) {
			$this->out("Invalid --path. File does not exist: " . $zip_path);
			exit(1);
		}

		$dry_run = in_array('--dry-run', $this->argv);

		$this->out("Restoreing files ... ");
		$this->installFilesFromZip($zip_path, $dry_run);
		$this->out("-> Done");
	}


	####################################################################################################################
	# download-latest
	####################################################################################################################

	public function runAction_downloadLatest()
	{
		$save_path = null;
		if (($key = array_search('--path', $this->argv)) !== false || isset($this->argv[$key+1])) {
			$save_path = @realpath($this->argv[$key+1]);
			if (!$save_path) {
				$this->out("Invalid --path");
				exit(1);
			}
		}

		try {
			$this->downloadLatest($save_path);
		} catch (DownloadException $e) {
			$this->outAndLog($e->getMessage());
			exit(1);
		}
	}


	/**
	 * Download the latest copy of DeskPRO into $save_path. If $save_path is not defined, then it will be put
	 * into the backups directory.
	 *
	 * @param $save_path
	 * @return string The path it was saved to
	 */
	public function downloadLatest($save_path = null, $_attempt = 0)
	{
		$save_path_orig = $save_path;
		$version_info = $this->getLatestVersion();

		$time_start = microtime(true);

		if (!$save_path) {
			$save_path = $this->getTmpDir();
		}

		if (is_dir($save_path)) {
			$save_path .= DIRECTORY_SEPARATOR . basename(dirname($version_info['download'])) . '-' . basename($version_info['download']);
		}

		$save_dir = dirname($save_path);

		if (!is_dir($save_dir) && !@mkdir($save_dir, 0777, true)) {
			throw new DownloadException("Save directory does not exist: " . $save_dir, DownloadException::NO_DIR);
		}

		if (!is_writable($save_dir)) {
			throw new DownloadException("Save directory is not writable: " . $save_dir, DownloadException::PERM_ERROR);
		}

		// It already exists, just return it
		if (file_exists($save_path) && filesize($save_path) >= Upgrade::MIN_FILESIZE_CHECK) {
			return $save_path;
		}

		$this->log("downloadLatest: Downloading from " . $version_info['download']);
		$this->log("downloadLatest: Saving to " . $save_path);

		try {
			\DeskPRO_LowUtil_RemoteRequester::create()->download($version_info['download'], $save_path);
		} catch (\Exception $e) {
			if (file_exists($save_path)) {
				unlink($save_path);
			}

			if ($_attempt < 2) {
				return $this->downloadLatest($save_path_orig, $_attempt+1);
			}

			throw new DownloadException("Download failed: " . $e->getMessage());
		}

		$this->log(sprintf("downloadLatest: time(%.4f)  file_size(%d)", microtime(true) - $time_start, filesize($save_path)));

		if (filesize($save_path) < Upgrade::MIN_FILESIZE_CHECK) {
			if (file_exists($save_path)) {
				unlink($save_path);
			}

			if ($_attempt < 2) {
				return $this->downloadLatest($save_path_orig, $_attempt+1);
			}

			$size = filesize($save_path);
			throw new DownloadException(sprintf("Saved file seems too small: $save_path is %d bytes", $size), DownloadException::BAD_FILE);
		}

		return $save_path;
	}


	####################################################################################################################
	# check-version
	####################################################################################################################

	public function runAction_checkVersion()
	{
		$version_info = $this->getLatestVersion();

		$this->out(sprintf("Your build:      %s (built %s)", DP_BUILD_NUM, $this->formatBuild(DP_BUILD_TIME)));
		$this->out(sprintf("Latest build:    %s (build %s)", $version_info['build_num'], $this->formatBuild($version_info['build'])));
		$this->out(sprintf("                 %s", $version_info['download']));
		$this->out(str_repeat('-', 70));

		$this->log(sprintf("runCheckVersion: current(%s)   latest(%s)", DP_BUILD_TIME, $version_info['build']));

		if ($this->isInstanceOutdated()) {
			$this->out("Your instance is outdated. You should upgrade.");
		} else {
			$this->out("Your instance is up to date.");
		}

		echo "\n";
	}


	/**
	 * Is the currently installed instance outdated?
	 *
	 * @return bool
	 */
	public function isInstanceOutdated()
	{
		$version_info = $this->getLatestVersion();

		if (DP_BUILD_TIME < $version_info['build']) {
			return true;
		}

		return false;
	}


	/**
	 * @return array
	 */
	public function getLatestVersion()
	{
		if ($this->latest_version !== null) {
			return $this->latest_version;
		}

		$this->latest_version = $this->callService('check-latest-version.json');

		return $this->latest_version;
	}


	####################################################################################################################

	public function sendLog($e = null)
	{
		static $has_sent = false;
		if ($has_sent) {
			return;
		}
		$has_sent = true;

		$stats = array();

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
		$stats['php_version'] = phpversion();

		$info = array(
			'root'              => defined('DP_ROOT')                 ? DP_ROOT : '',
			'os'                => isset($stats['server_os'])         ? $stats['server_os'] : '',
			'php_version'       => isset($stats['php_version'])       ? $stats['php_version'] : '',
			'server_ip'         => isset($_SERVER['SERVER_ADDR'])     ? $_SERVER['SERVER_ADDR'] : '',
			'build'             => DP_BUILD_TIME,
			'total_time'        => microtime(true) - DP_UPGRADE_STARTTIME,
		);
		$info['hostname'] = @gethostname();

		if (defined('DP_NEW_BUILD_TIME')) {
			$info['build'] = DP_NEW_BUILD_TIME;
		}

		if ($e) {
			$errinfo = self::getExceptionInfo($e);
			$info['error_info'] = $errinfo;
		}

		try {
			$pdo = $this->newDb();

			$q = $pdo->query("SELECT name, value FROM settings");
			$settings = $q->fetchAll(\PDO::FETCH_KEY_PAIR);

			$info['url'] = isset($settings['core.deskpro_url']) ? $settings['core.deskpro_url'] : '';
			$info['build'] = isset($settings['core.deskpro_build']) ? $settings['core.deskpro_build'] : 0;

			$info['license_id'] = 0;
			if (isset($settings['core.license'])) {
				$license_code = str_replace(array("\n", "\r", " ", "\t"), "", trim($settings['core.license']));
				$license_code = @base64_decode($license_code);
				$info['license_id'] = substr($license_code, 0, 14);
				$info['license_id'] = rtrim($info['license_id'], '-');
			}

		} catch (\Exception $e) {}

		$info['log'] = @file_get_contents(dp_get_log_dir() . '/upgrade.log');
		$info['old_build'] = defined('DP_ORIG_BUILD_TIME') ? DP_ORIG_BUILD_TIME : '0';
		$info['new_build'] = defined('DP_NEW_BUILD_TIME') ? DP_NEW_BUILD_TIME : '0';

		try {
			$url = $this->callService('get-service-url.json');
			if ($url && !empty($url['url'])) {
				$this->callService('/data-submit/report-upgrade.json', $info, $url['url']);
			}
		} catch (\Exception $e) {}
	}

	/**
	 * Copy of KernelErrorHandler::getExceptionInfo
	 */
	public static function getExceptionInfo(\Exception $exception)
	{
		$errno   = $exception->getCode();
		$errstr  = $exception->getMessage();
		$errfile = $exception->getFile();
		$errline = $exception->getLine();

		$backtrace = $exception->getTrace();
		$trace = self::formatBacktrace($backtrace);
		$context_data = '';

		if (isset($exception->_dp_query)) {
			$errstr .= ' -- Query: ' . substr($exception->_dp_query, 0, 2000);

			if (!empty($exception->_dp_query_params)) {
				$context_data = self::varToString($exception->_dp_query_params);
			}
		}

		$type = get_class($exception);
		$summary = "[EXCEPTION] $type:$errno $errstr ($errfile:$errline)";

		$display = true;
		if (!(error_reporting() & E_ERROR)) {
			$display = false;
		}

		$prev = $exception->getPrevious();
		if ($prev) {
			$previnfo = self::getExceptionInfo($prev);
			$summary .= ", " . $previnfo['summary'];
			$trace .= "\n\n(Alt Exception)\n" . $previnfo['trace'];
		}

		$errinfo = array(
			'type'           => 'exception',
			'session_name'   => isset($exception->_dp_sn) ? $exception->_dp_sn : '',
			'exception'      => $exception,
			'exception_type' => get_class($exception),
			'die'            => true,
			'pri'            => 'ERR',
			'trace'          => $trace,
			'summary'        => $summary,
			'errstr'         => $errstr,
			'errname'        => 'EXCEPTION',
			'errno'          => $errno,
			'errfile'        => $errfile,
			'errline'        => $errline,
			'display'        => $display,
			'build'          => defined('DP_ORIG_BUILD_TIME') ? DP_ORIG_BUILD_TIME : 0,
			'process_log'    => '',
			'context_data'   => $context_data
		);

		return $errinfo;
	}

	/**
	 * Copy of KernelErrorHandler::formatBacktrace
	 */
	public static function formatBacktrace(array $backtrace)
	{
		$trace = '';

		foreach($backtrace as $k=>$v){

			$prefix = "#$k ";
			$line = '';

			if (!empty($v['file'])) {
				$v['file'] = $v['file'];
				$prefix .= "[{$v['file']}:{$v['line']}] ";
			}

			if (isset($v['object'])) {
				$line .= get_class($v['object']) . "::";
			} elseif (isset($v['class'])) {
				$line .= $v['class'] . "::";
			}

			$line .= "{$v['function']}(";

			if (!empty($v['args'])) {
				$line .= self::varToString($v['args']);
			}

			$line .= ")";

			$trace .= $prefix . ' ' . trim($line) . "\n";
		}

		$trace = preg_replace('#PDO::__construct(.*?)$#m', 'PDO::__construct(...)', $trace);

		return trim($trace);
	}


	/**
	 * Copy of KernelErrorHandler::varToString
	 */
	public static function varToString($var, $_depth = 0)
    {
        if (is_object($var)) {
            return sprintf('[object](%s)', get_class($var));
        }
        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
				if ($_depth > 8) {
					$a[] = sprintf('%s => %s', $k, '(string)');
				} else {
					$a[] = sprintf('%s => %s', $k, self::varToString($v, $_depth+1));
				}
            }
            return sprintf("[array](%s)", implode(', ', $a));
        }
        if (is_resource($var)) {
            return '[resource]';
        }
		$str = (string)$var;
		if (strlen($str) > 1000) {
			$str = substr($str, 0, 1000) . "...(clipped)";
		}
        return str_replace("\n", '', var_export($str, true));
    }

	/**
	 * Compress a file or directory with ZIP.
	 *
	 * @param $path
	 */
	public function compressFile($path)
	{
		$time_start = microtime(true);

		$out_filepath = $this->zip->compressFile($path);
		if (!$out_filepath) {
			return false;
		}

		// Double check the out file too
		$success = true;
		if ($out_filepath) {
			if (!file_exists($out_filepath) || filesize($out_filepath) < 10) {
				$this->log("compressFile: reported success but file looks bad: $out_filepath");
				$success = false;
			}
		}

		// If we're a success, then we can remove the original
		if ($success) {
			$fileutil = new FilesystemUtil();
			$fileutil->remove($path);
		}

		$this->log(sprintf("compressFile: time(%.4f)   file_size(%d)", microtime(true) - $time_start, filesize($out_filepath)));

		return $out_filepath;
	}


	/**
	 * Executes a $command in $dir, puts the output in $out, and returns the status of the command.
	 *
	 * @param string $command
	 * @param string $dir
	 * @param array  $out
	 * @return int
	 */
	public function execCommand($command, $dir = null, &$out = null)
	{
		$command_log = $command;
		$command_log = preg_replace('#password=.*? #', 'password=xxx ', $command_log);
		$this->log(sprintf("execCommand: dir(%s)  cmd(%s)", $dir, $command_log));

		if ($dir) {
			chdir($dir);
		}

		$command .= ' 2>&1';
		$ret = 0;
		exec($command, $out, $ret);

		$this->log(sprintf("execCommand: -> result: %d", $ret));
		if (strpos($command, '--help') === false) {
			foreach ($out as $l) {
				$l = trim($l);
				if ($l !== '') {
					// Remove pass
					global $DP_CONFIG;
					$l = str_replace($DP_CONFIG['db']['password'], '***', $l);

					$this->log(sprintf("execCommand: -> %s", $l));
				}
			}
		}

		return $ret;
	}


	/**
	 * @return string
	 */
	public function getBackupDir()
	{
		return dp_get_backup_dir();
	}


	/**
	 * @return string
	 */
	public function getTmpDir()
	{
		return dp_get_tmp_dir();
	}


	/**
	 * @return string
	 */
	public function getLogDir()
	{
		return dp_get_log_dir();
	}


	/**
	 * Formats a build as a time
	 *
	 * @param $build
	 * @return string
	 */
	public function formatBuild($build)
	{
		return date('Y-m-d H:i:s', $build);
	}


	/**
	 * Call a DeskPRO service
	 *
	 * @param string $endpoint
	 * @param array $post_data
	 * @return array
	 */
	public function callService($endpoint, array $post_data = array(), $url = null)
	{
		if ($url === null) {
			$url = DP_MA_SERVER;
		}

		$url = rtrim($url, '/') . '/api/' . ltrim($endpoint, '/');
		return $this->fetchServiceResult($url, $post_data);
	}


	/**
	 * @param string $url
	 * @param array $post_data
	 * @return array
	 */
	public function fetchServiceResult($url, array $post_data = array())
	{
		$this->log('Calling: ' . $url);
		try {
			$result = \DeskPRO_LowUtil_RemoteRequester::create()->request($url, $post_data, 'POST');
		} catch (\Exception $e) {
			throw new ServiceCallException("Failed contacting server: " . $e->getMessage(), ServiceCallException::NO_RESPONSE);
		}
		$this->log('-> ' . $result);

		if (!$result) {
			throw new ServiceCallException("No response from server: $url $result", ServiceCallException::INVALID_RESPONSE);
		}

		$res_data = @json_decode($result, true);
		if (!is_array($res_data)) {
			throw new ServiceCallException("Invalid JSON response from server: $url $result", ServiceCallException::INVALID_RESPONSE);
		}

		return $res_data;
	}


	/**
	 * Register a cleanup param
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function registerCleanupParam($name, $value)
	{
		global $UPGRADE_CLEANUP;
		if (!$UPGRADE_CLEANUP) {
			$UPGRADE_CLEANUP = array();
		}

		if ($value === null) {
			unset($UPGRADE_CLEANUP[$name]);
		} else {
			$UPGRADE_CLEANUP[$name] = $value;
		}
	}

	/**
	 * @static
	 * @param int $bytes
	 * @return string
	 */
	public static function getFilesizeDisplay($bytes)
	{
		if (!$bytes OR $bytes < 1) {
			return array('number' => 0, 'symbol' => 'B');
	    }

	    $all_symbols = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $exp = floor(log($bytes)/log(1024));
        $val = $bytes/pow(1024, floor($exp));

        $sym = '';
        if (isset($all_symbols[$exp])) {
            $sym = $all_symbols[$exp];
        }

		$parts=  array(
			'number' => $val,
			'symbol' => $sym
		);

		return sprintf('%.2f %s', $parts['number'], $parts['symbol']);
	}

	/**
	 * @return \PDO
	 */
	public function newDb()
	{
		global $DP_CONFIG;

		if (isset($DP_CONFIG['db']['host']) && preg_match('#^(.*?):([0-9]+)$#', $DP_CONFIG['db']['host'], $m)) {
			$host = $m[1];
			$port = ";port={$m[2]};";
		} else {
			$host = $DP_CONFIG['db']['host'];
			$port = '';
		}

		return new \PDO("mysql:host={$host};dbname={$DP_CONFIG['db']['dbname']}$port", $DP_CONFIG['db']['user'], $DP_CONFIG['db']['password']);
	}
}

########################################################################################################################
# Shutdown handler
########################################################################################################################

/**
 * When things quit unexpectedly, try to clean up anything that might be left over.
 */
function Upgrade_Shutdown_Function()
{
	@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
	@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');

	global $UPGRADE_CLEANUP;
	if (!$UPGRADE_CLEANUP) {
		return;
	}

	$fileutil = new FilesystemUtil();

	if (isset($UPGRADE_CLEANUP['close_log_fh'])) {
		@fclose($UPGRADE_CLEANUP['close_log_fh']);
	}
	if (isset($UPGRADE_CLEANUP['unlink_zip_path'])) {
		try {
			$fileutil->remove($UPGRADE_CLEANUP['unlink_zip_path']);
		} catch (\Exception $e) {}
	}
	if (isset($UPGRADE_CLEANUP['unlink_scratch_dir'])) {
		try {
			$fileutil->remove($UPGRADE_CLEANUP['unlink_scratch_dir']);
		} catch (\Exception $e) {}
	}

	try {
		$fileutil->remove(DP_WEB_ROOT.'/auto-update-is-running.trigger');
		$fileutil->remove(dp_get_tmp_dir() . '/auto-upgrade-started');
	} catch (\Exception $e) {}

	$UPGRADE_CLEANUP = null;
}

########################################################################################################################
# Custom filesystem util class
########################################################################################################################

class FilesystemUtil extends \Symfony\Component\HttpKernel\Util\Filesystem
{
	protected $dry_run = false;

	public function enableDryRun()
	{
		$this->dry_run = true;
	}

	public function mirror($originDir, $targetDir, \Traversable $iterator = null, $options = array(), array &$failures = null)
	{
		if ($failures === null) {
			$failures = array();
		}

		$copyOnWindows = false;
		if (isset($options['copy_on_windows']) && !function_exists('symlink')) {
			$copyOnWindows = $options['copy_on_windows'];
		}

		if (null === $iterator) {
			$flags = $copyOnWindows ? \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS : \FilesystemIterator::SKIP_DOTS;
			$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($originDir, $flags), \RecursiveIteratorIterator::SELF_FIRST);
		}

		if ('/' === substr($targetDir, -1) || '\\' === substr($targetDir, -1)) {
			$targetDir = substr($targetDir, 0, -1);
		}

		if ('/' === substr($originDir, -1) || '\\' === substr($originDir, -1)) {
			$originDir = substr($originDir, 0, -1);
		}

		foreach ($iterator as $file) {

			$file_rel_path = DIRECTORY_SEPARATOR . str_replace($originDir.DIRECTORY_SEPARATOR, '', $file->getPathname());
			if (!empty($options['exclude']) && in_array($file_rel_path, $options['exclude'])) {
				continue;
			}

			$target = $targetDir.DIRECTORY_SEPARATOR.str_replace($originDir.DIRECTORY_SEPARATOR, '', $file->getPathname());

			if (is_link($file)) {
				$this->symlink($file, $target);
			} elseif (is_dir($file)) {
				$this->mkdir($target, 0777, $failures);
			} elseif (is_file($file) || ($copyOnWindows && is_link($file))) {
				$this->copy($file, $target, isset($options['override']) ? $options['override'] : false, $failures);
			} else {
				throw new \RuntimeException(sprintf('Unable to guess "%s" file type.', $file));
			}
		}
	}

	public function copy($originFile, $targetFile, $override = false, array &$failures = null)
	{
		if ($failures === null) {
			$failures = array();
		}

		if ($this->dry_run) {
			echo "[copy] $originFile => $targetFile\n";
			return;
		}

		parent::copy($originFile, $targetFile, $override);
	}

	public function mkdir($dirs, $mode = 0777, array &$failures = null)
	{
		if ($failures === null) {
			$failures = array();
		}

		if ($this->dry_run) {
			foreach ($this->toIterator($dirs) as $dir) {
				if (is_dir($dir)) {
					continue;
				}

				echo "[mkdir] $dir\n";
			}

			return true;
		}

		return parent::mkdir($dirs, $mode);
	}

	public function touch($files)
	{
		if ($this->dry_run) {
			foreach ($this->toIterator($files) as $file) {
				echo "[touch] $file\n";
			}
			return;
		}

		parent::touch($files);
	}

	public function remove($files)
	{
		if ($this->dry_run) {
			$files = iterator_to_array($this->toIterator($files));
			$files = array_reverse($files);
			foreach ($files as $file) {
				if (!file_exists($file)) {
					continue;
				}

				if (is_dir($file) && !is_link($file)) {
					echo "[rmdir] $file\n";
				} else {
					echo "[rm] $file\n";
				}
			}
			return;
		}

		$files = iterator_to_array($this->toIterator($files));
		$files = array_reverse($files);
		foreach ($files as $file) {
			if (!file_exists($file)) {
				continue;
			}

			if (is_dir($file) && !is_link($file)) {

				$dir_arg = escapeshellarg($file);
				if (dp_get_os() == 'win') {
					$cmd = 'RD /S /Q ' . $dir_arg;
				} else {
					$cmd = 'rm -rf ' . $dir_arg;
				}

				$out = null;
				$ret = null;
				exec($cmd, $out, $ret);

				if ($ret) {
					$out = implode("\n", $out);
					echo $out;
				}

			} else {
				unlink($file);
			}
		}
	}

	public function chmod($files, $mode, $umask = 0000)
	{
		if ($this->dry_run) {
			foreach ($this->toIterator($files) as $file) {
				printf("[chmod] %o %s\n", $file, $mode);
			}
			return;
		}

		parent::chmod($files, $mode, $umask);
	}

	public function rename($origin, $target)
	{
		if ($this->dry_run) {
			echo "[rename] $origin => $target\n";
			return;
		}

		parent::rename($origin, $target);
	}

	public function symlink($originDir, $targetDir, $copyOnWindows = false)
	{
		if ($this->dry_run) {
			echo "[symlink] $originDir => $targetDir\n";
			return;
		}

		parent::symlink($originDir, $targetDir);
	}

	private function toIterator($files)
	{
		if (!$files instanceof \Traversable) {
			$files = new \ArrayObject(is_array($files) ? $files : array($files));
		}

		return $files;
	}
}

########################################################################################################################
# Interactive Upgrader
########################################################################################################################

class UpgradeInteractive implements \Symfony\Component\Console\Output\OutputInterface
{
	/**
	 * @var \DeskPRO\Tools\Upgrade
	 */
	protected $upgrade;

	/**
	 * @var \Symfony\Component\Console\Formatter\OutputFormatter
	 */
	protected $outputFormatter;

	/**
	 * @var \Symfony\Component\Console\Helper\DialogHelper
	 */
	protected $dialogHelper;

	/**
	 * @var int
	 */
	protected $spinner_state = 0;

	/**
	 * @var string
	 */
	protected $dl_distro = null;
	protected $file_backup = null;
	protected $db_backup = null;
	protected $revert_checkpoint = null;

	protected $answer_backup_files = null;
	protected $answer_backup_db = null;

	public function askConfirmation($x, $prompt, $default = false) {

		if (!$default) {
			$default = 'n';
		} else {
			$default = 'y';
		}

		$val = null;
		while (true) {
			$val = $this->dialogHelper->ask($this, $prompt, '');
			$val = trim(strtolower($val));
			if ($val == 'y' || $val == 'yes') {
				$val = 'y';
				break;
			} elseif ($val == 'n' || $val == 'no') {
				$val = 'n';
				break;
			} elseif ($val === '') {
				$val = null;
				break;
			}
		}

		if ($val === null) {
			$val = $default;
		}

		if ($val == 'y') {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @param \DeskPRO\Tools\Upgrade $upgrade
	 */
	public function __construct(Upgrade $upgrade)
	{
		$this->upgrade = $upgrade;

		#------------------------------
		# Load the required Symfony libs
		#------------------------------

		require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Formatter/OutputFormatterStyleInterface.php';
		require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Formatter/OutputFormatterStyle.php';
		require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Formatter/OutputFormatter.php';
		require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Helper/HelperInterface.php';
		require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Helper/Helper.php';
		require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Helper/DialogHelper.php';
		require_once DP_ROOT.'/vendor/symfony/src/Symfony/Component/Console/Helper/FormatterHelper.php';

		#------------------------------
		# Create helpers
		#------------------------------

		$decorated = true;
		if (strpos(strtoupper(PHP_OS), 'WIN') === 0) {
			$decorated = false;
		}
		$this->outputFormatter = new \Symfony\Component\Console\Formatter\OutputFormatter($decorated, array(
			'title' => new \Symfony\Component\Console\Formatter\OutputFormatterStyle('white', 'blue', array('bold')),
			'note' => new \Symfony\Component\Console\Formatter\OutputFormatterStyle('yellow', null),
			'prompt' => new \Symfony\Component\Console\Formatter\OutputFormatterStyle('cyan', 'black')
		));

		$this->dialogHelper    = new \Symfony\Component\Console\Helper\DialogHelper();

		#------------------------------
		# Check requirements
		#------------------------------

		$php_path        = dp_get_php_path(true);
		$mysql_dump_path = dp_get_mysqldump_path(true);
		$mysql_path      = dp_get_mysql_path(true);

		if (!$php_path || !$mysql_path || !$mysql_dump_path) {

			$this->out("<error>Error: We could not find the path to an important utility</error>");

			$this->out("The upgrader could not locate the paths to the following utilitie(s):");
			if (!$php_path) {
				$this->upgrade->log("Cannot find path to the `php` CLI");
				$this->out("\t- Could not find the path to php");
			}
			if (!$mysql_dump_path) {
				$this->upgrade->log("Cannot find path to `mysqldump` binary");
				$this->out("\t- Could not find the path to mysqldump");
			}
			if (!$mysql_path) {
				$this->upgrade->log("Cannot find path to `mysql` binary");
				$this->out("\t- Could not find the path to mysql");
			}

			$this->out();
			$this->out("Edit your config.php file to learn more about locating these utilities and setting their paths.");

			exit(10);
		}

		// Check currently executing requirements
		$fatal = array();

		foreach (deskpro_install_check_reqs() as $type => $level) {
			if ($level == 'fatal') {
				$fatal[] = $type;
			}
		}

		if ($fatal) {
			$this->out("<error>Error: The PHP binary you are using does not meet the server requirements.</error>");
			$this->out("The following server checks failed:");
			$out = '- ' . implode("\n- ", $fatal);
			$this->out($out);
			$this->out("\nUse a different PHP binary or correct the problem, and then try again.");

			exit(13);
		}

		// Now check that the php in the php path also passes
		$cmd = sprintf(
			"%s %s",
			dp_get_php_path(),
			escapeshellarg(DP_ROOT.'/bin/check-req.php')
		);

		$ret = null;
		$out = null;
		exec($cmd, $out, $ret);

		if (!$out) $out = array();

		$out = implode("\n", $out);

		if ($ret || strpos($out, 'OKAY') === false) {
			$this->out("<error>Error: We could not verify the path to your PHP binary</error>");
			$this->out("You have configured DeskPRO to use the PHP binary at " . $php_path . " but it does not meet server requirements.\n\nSince you are running this command fine, that means you have a PHP binary that is suitable but you need to edit config.php and to correct the `php_path` value.\n");
			$this->out($out);

			exit(11);
		}

		#------------------------------
		# GO
		#------------------------------

		$this->outHeader('DeskPRO Upgrader', true);
		$this->out();

		$this->out(
			"<info>Welcome to the DeskPRO interactive upgrader.\n"
			."For help please visit: http://support.deskpro.com</info>"
		);

		$this->out();

		#------------------------------
		# Check req
		#------------------------------

		$continue_anyway = false;
		$inipath = \Orb\Util\Env::getPhpIniPath();

		$req_strategy = \DeskPRO_LowUtil_RemoteRequester::detectStrategy();
		if (!$req_strategy) {
			$this->out(
				"<error>Your server is unable to make outbound connection to the DeskPRO servers to check for version status or to download updates.\n"
				."The upgrade utility requires one of the two: "
				."    - allow_url_fopen enabled in php.ini"
				."    - Or the cURL extension enabled"
				.($inipath ? "We have detected the path to php.ini you will need to edit: $inipath\n" : '')
				."\n"
				."If you require assistance, you can contact us at support@deskpro.com</error>"
			);
			$this->out();

			$this->out(
				"<prompt>Would you like to continue? If you have manually updated DeskPRO files, or if you wish to"
				." check the version of your database, you can still run this tools.</prompt>"
			);
			$ret = $this->askConfirmation($this, "Continue? [y/N]> ", false);

			if (!$ret) {
				$this->out("\n");
				exit(0);
			}

			$continue_anyway = true;
		}

		#------------------------------
		# Menu
		#------------------------------

		try {
			$version_info = $this->upgrade->getLatestVersion();
		} catch (\Exception $e) {
			$this->upgrade->log("getLatestVersion error: {$e->getMessage()}");
			$version_info = null;
		}

		#-----
		# We have version info
		#-----

		if ($version_info) {
			$this->upgrade->log("(Interactive Upgrader)");
			$this->out(sprintf("Your build:      %s (%s)", DP_BUILD_NUM, $this->upgrade->formatBuild(DP_BUILD_TIME)));
			$this->out(sprintf("Latest build:    %s (%s)", $version_info['build_num'], $this->upgrade->formatBuild($version_info['build'])));
			$this->upgrade->log(sprintf("runCheckVersion: current(%s)   latest(%s)", DP_BUILD_TIME, $version_info['build']));

			$this->out();

			if ($this->upgrade->isInstanceOutdated()) {
				$this->out("<prompt>Your current instance is outdated. Would you like to download updates now?</prompt>");

				$ret = $this->askConfirmation($this, '[Y/n]> ', true);

				if ($ret) {
					$this->runAction_downloadAndInstallChoice();
				} else {
					$this->runAction_checkAndUpgrade();
				}
			} else {
				$this->runAction_checkAndUpgrade();
			}

		#-----
		# We don't know about the version
		#-----

		// $continue_anyway is set when we didnt have a requester strategy,
		// so they might've already stated they want to continue anyway

		} elseif (!$continue_anyway) {

			$this->out(
				"<error>We could not fetch version information from our web server. There are a number of possible causes:\n"
				."    - Your server is behind a firewall\n"
				."    - There is a network problem between your server and ours\n"
				."    - Our version server may be having difficulties. Check http://www.deskpro.com/status/\n"
				."\n"
				."You can try again but if you continue to experience trouble, you can contact us at support@deskpro.com</error>"
			);
			$this->out();

			$this->out(
				"<prompt>Would you like to continue? If you have manually updated DeskPRO files, or if you wish to"
				." check the version of your database, you can still run this tools.</prompt>"
			);
			$ret = $this->askConfirmation($this, "Continue? [y/N]> ", false);

			if (!$ret) {
				$this->out("\n");
				exit(0);
			}

			$this->runAction_checkAndUpgrade();
		}
	}

	/**
	 * Download and install updates
	 */
	public function	runAction_downloadAndInstallChoice()
	{
		$this->out();

		#---
		# File permissions: CHeck a few dirs/files to make sure we can write them all
		#---

		$check = array(
			DP_ROOT,
			DP_ROOT.'/src',
			DP_ROOT.'/sys',
			DP_ROOT.'/sys/cache',
			DP_ROOT.'/sys/cache',
			DP_ROOT.'/sys/cache/prod',
			DP_ROOT.'/sys/system.php',
			DP_ROOT.'/sys/vendor',
		);

		$write_fail = false;
		foreach ($check as $f) {
			if (file_exists($f) && !is_writable($f)) {
				$write_fail = $f;
				break;
			}
		}

		if ($write_fail) {
			$this->upgrade->log("Failed write permission check on: $write_fail");
			$this->errorExit(
				"This command requires permission to delete and write all files in the DeskPRO directory. We detected a permission error that would prevent the "
				."upgrade from completing successfully. Check to make sure this user has permission on all DeskPRO files, and then try again."
			);
		}

		#------------------------------
		# Download
		#------------------------------

		$this->spinner("Downloading latest version ...");

		try {
			$this->dl_distro = $this->upgrade->downloadLatest();
		} catch (DownloadException $e) {
			$this->clearSpinner();
			$this->upgrade->outAndLog($e->getMessage());
			$this->errorExit("There was a problem trying to download the latest version. Try again later.");
		}

		$this->clearSpinner();

		$this->out("<info>Download was successful. Package saved to:\n{$this->dl_distro}\n</info>");
		$this->out();

		$this->out("<prompt>Before we install the updates, you should generate a back up first. You can back up both your files and your database.\n</prompt>");

		$this->upgrade->log("(Gathering input)");
		while(true) {
			$this->out("Do you want to back up your current source files? ", false);
			$this->answer_backup_files = $this->askConfirmation($this, "[Y/n]> ", true);

			$this->out("Do you want to back up your database? ", false);
			$this->answer_backup_db = $this->askConfirmation($this, "[Y/n]> ", true);

			$this->out();
			$this->out("<comment>Backup files: " . ($this->answer_backup_files ? "YES" : "NO") . "</comment>");
			$this->out("<comment>Backup database: " . ($this->answer_backup_db ? "YES" : "NO") . "</comment>");

			$this->out();
			$this->out("<prompt>Are you ready to continue?\nAnswer 'n' to re-input backup options.</prompt>");
			$this->out("Continue with the upgrade? ", false);

			$ret = $this->askConfirmation($this, "[Y/n]> ", true);
			if ($ret) {
				break;
			}
			$this->out();
		}
		$this->upgrade->log(sprintf("(Done gathering input: answer_backup_files=%d, answer_backup_db=%d)", $this->answer_backup_files, $this->answer_backup_db));

		$fileutil = new FilesystemUtil();
		$fileutil->touch(DP_WEB_ROOT.'/auto-update-is-running.trigger');

		#------------------------------
		# Backup files
		#------------------------------

		$this->outHeader("Installing Updates");
		$this->out();

		if ($this->answer_backup_files) {
			$this->out(sprintf("%-40s", "<info>[*] Backing up files ...</info>"), false);

			try {
				$this->file_backup = $this->upgrade->backupFiles();
			} catch (\Exception $e) {
				$this->upgrade->outAndLog($e->getMessage());
				$this->errorExit("There was a problem backing up your files.");
			}

			$this->revert_checkpoint = 'files';
			$this->out("<info>DONE</info>");

			$this->out(sprintf("    File: %s :: %s", Upgrade::getFilesizeDisplay(filesize($this->file_backup)), $this->file_backup));
		}

		#------------------------------
		# Install files
		#------------------------------

		$this->out(sprintf("%-55s", "<info>[*] Installing files ...</info>"), false);

		try {
			$this->upgrade->installFilesFromZip($this->dl_distro, false);
		} catch (\Exception $e) {
			$this->upgrade->outAndLog($e->getMessage());
			$this->errorExit("There was a problem installing the new files.");
		}

		$this->out("<info>DONE</info>");

		// Remove downloaded zip
		@unlink($this->dl_distro);

		#------------------------------
		# Backup database
		#------------------------------

		if ($this->answer_backup_db) {
			$this->out(sprintf("%-55s", "<info>[*] Backing up database ...</info>"), false);

			try {
				$this->db_backup = $this->upgrade->backupDatabase();
			} catch (\Exception $e) {
				$this->upgrade->outAndLog($e->getMessage());
				$this->errorExit("There was a problem backing up your database.");
			}

			$this->out(sprintf("    File: %s :: %s", Upgrade::getFilesizeDisplay(filesize($this->db_backup)), $this->db_backup));

			$this->revert_checkpoint = 'db';
			$this->out("<info>DONE</info>");
		}

		#------------------------------
		# Run upgrader
		#------------------------------

		$this->out(sprintf("%-55s", "<info>[*] Installing database updates</info>"));

		$php_path = $this->upgrade->getPhpBinaryPath();

		chdir(DP_ROOT . '/../');
		$cmd = "$php_path cmd.php dp:upgrade 2>&1";
		passthru($cmd, $ret);

		if ($ret) {
			$this->upgrade->outAndLog("Upgrade returned erorr status $ret");
			$this->errorExit("There was a problem installing the database updates");
		}

		$fileutil->remove(DP_WEB_ROOT.'/auto-update-is-running.trigger');
		$fileutil->remove(dp_get_tmp_dir() . '/auto-upgrade-started');

		$this->outHeader("DONE");
		$this->out();

		$this->out("<info>DeskPRO has been upgraded successfully.</info>");
		$this->out();
		$this->out('');

		if (extension_loaded('wincache')) {
			$this->out("<error>RESTART REQUIRED</error>");
			$this->out("Your server has WinCache installed. Due to a limitation of WinCache, updates to files are not always immediately recognised.");
			$this->out("You must restart IIS (or your computer) to ensure the updates were fully installed.");
		}

		$this->upgrade->postUpgrade();

		$this->upgrade->sendLog();
	}


	/**
	 * Running just the upgrade against currently file sources
	 */
	public function runAction_checkAndUpgrade()
	{
		global $DP_CONFIG;

		#------------------------------
		# Check versions
		#------------------------------

		if (isset($DP_CONFIG['db']['host']) && preg_match('#^(.*?):([0-9]+)$#', $DP_CONFIG['db']['host'], $m)) {
			$host = $m[1];
			$port = ";port={$m[2]};";
		} else {
			$host = $DP_CONFIG['db']['host'];
			$port = '';
		}
		$pdo = new \PDO("mysql:host={$host};dbname={$DP_CONFIG['db']['dbname']}$port", $DP_CONFIG['db']['user'], $DP_CONFIG['db']['password']);
		$version = $pdo->query("SELECT value FROM settings WHERE name = 'core.deskpro_build'")->fetch(\PDO::FETCH_NUM);

		if (!$version) {
			$this->errorExit("We could not find your currently installed version.");
		}

		$version = $version[0];

		$this->out(sprintf("File build time:      %s", $this->upgrade->formatBuild(DP_BUILD_TIME)));
		$this->out(sprintf("Database build time:  %s", $this->upgrade->formatBuild($version)));

		$this->out();

		if ($version >= DP_BUILD_TIME) {
			$this->out("<info>Your database and source file builds correspond. No database upgrades need to be run.</info>");
			$this->out();
			$this->out("");
			exit(0);
		}

		#------------------------------
		# Gather input
		#------------------------------

		$this->out("<info>Your database is out of date. Would you like to perform an upgrade now?</info>");
		$this->out("Upgrade now? ", false);

		$ret = $this->askConfirmation($this, "[Y/n]> ", true);
		if (!$ret) {
			$this->out();
			$this->out("");
			exit(0);
		}

		$this->out("<prompt>Before we install the updates, you should generate back up first.</prompt>");

		while(true) {
			$this->out("Do you want to back up your database? ", false);
			$this->answer_backup_db = $this->askConfirmation($this, "[Y/n]> ", true);

			$this->out();
			$this->out("<comment>Backup database: " . ($this->answer_backup_db ? "YES" : "NO") . "</comment>");

			$this->out();
			$this->out("<prompt>Are you ready to continue? Answer 'n' to re-input backup options.</prompt>");
			$this->out("Continue with the upgrade? ", false);

			$ret = $this->askConfirmation($this, "[Y/n]> ", true);
			if ($ret) {
				break;
			}
			$this->out();
		}

		$fileutil = new FilesystemUtil();
		$fileutil->touch(DP_WEB_ROOT.'/auto-update-is-running.trigger');

		#------------------------------
		# Backup database
		#------------------------------

		if ($this->answer_backup_db) {
			$this->out(sprintf("%-40s", "<info>[*] Backing up database ...</info>"), false);

			try {
				$this->db_backup = $this->upgrade->backupDatabase();
			} catch (\Exception $e) {
				$this->upgrade->outAndLog($e->getMessage());
				$this->errorExit("There was a problem backing up your database.");
			}

			$this->revert_checkpoint = 'db';
			$this->out("<info>DONE</info>");
		}

		#------------------------------
		# Run upgrader
		#------------------------------

		$this->out(sprintf("%-40s", "<info>[*] Installing database updates</info>"));

		$php_path = $this->upgrade->getPhpBinaryPath();

		chdir(DP_ROOT . '/../');
		$cmd = "$php_path cmd.php dp:upgrade 2>&1";
		ob_start();
		passthru($cmd, $ret);
		$out = ob_get_contents();

		if ($ret) {
			$this->upgrade->outAndLog("Upgrade returned erorr status $ret");
			$this->upgrade->outAndLog("Upgrade output: $out");
			$this->errorExit("There was a problem installing the database updates");
		}


		$this->outHeader("DONE");

		$this->out("<info>DeskPRO has been upgraded successfully.</info>");
		$this->out();
		$this->out('');

		$this->upgrade->postUpgrade();

		// New build time
		$build_file = @file_get_contents(DP_ROOT.'/sys/config/build-time.php');
		if ($build_file) {
			$m = null;
			if (preg_match('#([0-9]{10})#', $build_file, $m)) {
				define('DP_NEW_BUILD_TIME', $m[1]);
			}
		}

		$this->upgrade->sendLog();
	}


	/**
	 * @param string $message
	 */
	public function errorExit($message = '')
	{
		if ($message) {
			$this->out("<error>$message</error>");
			$this->out();
		}

		/*
		if ($this->file_backup && $this->revert_checkpoint == 'files' || $this->revert_checkpoint == 'db') {
			$this->out(sprintf("%-40s", "<info>[*] Restoring files from backup ...</info>"), false);
			$this->upgrade->installFilesFromZip($this->file_backup);
			$this->out("<info>Done</info>");
		}

		if ($this->revert_checkpoint == 'db'){
			$this->out(sprintf("%-40s", "<info>[*] Restoring database from backup ...</info>"), false);
			$this->upgrade->restoreDbFromZip($this->db_backup);
			$this->out("<info>Done</info>");
		}
		*/

		$fileutil = new FilesystemUtil();
		$fileutil->remove(DP_WEB_ROOT.'/auto-update-is-running.trigger');
		$fileutil->remove(dp_get_tmp_dir() . '/auto-upgrade-started');

		$e = new \Exception($message);
		$this->upgrade->sendLog($e);

		exit(1);
	}


	/**
	 * Infinite spinner
	 */
	public function spinner($message = '')
	{
		echo "\r";
		echo str_repeat(' ', 70);
		echo "\r";

		echo "(";
		switch ($this->spinner_state) {
			case 0:
				echo "-";
				break;

			case 1:
				echo "\\";
				break;

			case 2:
				echo "|";
				break;

			case 3:
				echo "/";
				break;
		}

		$this->spinner_state++;
		if ($this->spinner_state > 3) {
			$this->spinner_state = 0;
		}

		echo ")";
		if ($message) {
			echo " $message";
		}
	}


	/**
	 * Clear the spinner form the line
	 */
	public function clearSpinner()
	{
		$this->spinner_state = 0;
		echo "\r";
		echo str_repeat(' ', 72);
		echo "\r";
	}


	/**
	 * @param string $string
	 * @param bool $nl
	 */
	public function out($string = '', $nl = true)
	{
		$tagged = null;
		if (preg_match('#^<(.*?)>(.*?)</$1>$#', $string, $m)) {
			$tagged = $m[1];
			$string = $m[2];
		}

		$string = wordwrap($string, 72, "\n", true);

		if ($tagged) {
			$string = "<$tagged>$string</$tagged>";
		}

		$string = $this->outputFormatter->format($string);
		$this->upgrade->out($string, $nl);
	}


	/**
	 * @param string $title
	 * @param bool $big
	 */
	public function outHeader($title, $big = false)
	{
		$string = '';
		if ($big) {
			$string .= str_repeat(' ', 72) . "\n";
		}

		$len = strlen($title);
		$remain = 72-$len;
		$left  = floor($remain/2);
		$right = 72 - $len - $left;

		$string .= str_repeat(' ', $left) . $title . str_repeat(' ', $right);

		if ($big) {
			$string .= "\n" . str_repeat(' ', 72);
		}

		$string = $this->outputFormatter->format("<title>$string</title>");

		echo $string;
	}

	/**
	 * @param $note
	 */
	public function outNote($note)
	{
		$note = wordwrap($note, 65, "\n", true);

		$lines = explode("\n", $note);
		foreach ($lines as &$l) $l = '    > ' . $l;
		$note = implode("\n", $lines);

		$string = $this->outputFormatter->format("<note>$note</note>");

		echo $string;
	}

	function write($messages, $newline = false, $type = 0)
	{
		$this->out($messages, $newline);
	}

	function writeln($messages, $type = 0)
	{
		$this->out($messages, true);
	}

	function setVerbosity($level)
	{

	}

	function getVerbosity()
	{
		return 1;
	}

	function setDecorated($decorated)
	{

	}

	function isDecorated()
	{
		return true;
	}

	function setFormatter(\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter)
	{

	}

	function getFormatter()
	{
		return $this->outputFormatter;
	}
}

########################################################################################################################
# ZIP Classes
########################################################################################################################

class ZipStrategy implements DpZip
{
	protected $zip;

	public function __construct(Upgrade $upgrade, $force_strategy = null)
	{
		if ($force_strategy !== null) {
			switch ($force_strategy) {
				case 'Zip_PHP':     $this->zip = new Zip_PHP($upgrade);     return;
				case 'Zip_PclZip':  $this->zip = new Zip_PclZip($upgrade);  return;
			}
		}

		if (extension_loaded('Zip')) {
			$upgrade->log("ZipStrategy: Zip_PHP");
			$this->zip = new Zip_PHP();
		} elseif (extension_loaded('zlib')) {
			$upgrade->log("ZipStrategy: Zip_PclZip");
			$this->zip = new Zip_PclZip();
		} else {
			throw new ZipException("Zip and zlib extensions not installed, no way to zip");
		}
	}

	public function compressFile($path)
	{
		return $this->zip->compressFile($path);
	}

	public function decompressZip($path, $to = null, &$error = null)
	{
		return $this->zip->decompressZip($path, $to, $error);
	}
}

interface DpZip
{
	/**
	 * Compress a file or directory of files.
	 * If a directory, the ZIP should be created at the root. E.g., extracting
	 * should extract into the cwd.
	 *
	 * @param string $path
	 * @return string
	 */
	public function compressFile($path);

	/**
	 * Decompress a zip file
	 *
	 * @param string $path
	 * @return string
	 */
	public function decompressZip($path, $to = null);
}

class Zip_PHP implements DpZip
{
	public function compressFile($path)
	{
		$path = str_replace('\\', '/', $path);
		$path = rtrim($path, '/');

		$filename     = basename($path);
		$out_filename = $filename . '-' . time() . '-' . mt_rand(1000,9999) . '.zip';
		$out_filepath = dp_get_tmp_dir() . DIRECTORY_SEPARATOR . $out_filename;

		$zip = new \ZipArchive();
		if ($zip->open($out_filepath, \ZipArchive::CREATE) !== true) {
			return false;
		}

		if (is_dir($path)) {
			$basedir = "/dp_zip";

			$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $file) {
				$file = str_replace('\\', '/', realpath($file));

				if (is_dir($file) === true) {
					$local = $basedir . str_replace($path. '/', '', '/' . $file . '/');
					$zip->addEmptyDir($local);
				} elseif (is_file($file) === true && realpath($file) != $out_filepath) {
					$local = $basedir . str_replace($path . '/', '', '/' . $file);
					$zip->addFile(realpath($file), $local);
				}
			}
		} else {
			$zip->addFile($path, '/dp/' . $filename);
		}

		if (!$zip->close()) {
			return false;
		}

		return $out_filepath;
	}

	public function decompressZip($path, $to = null, &$error = null)
	{
		$zip = new \ZipArchive();
		if (!is_file($path)) {
			$error = 'No file: ' . $path;
			return false;
		}

		if (($code = $zip->open($path)) !== true) {
			$error = sprintf("[%s/%s] %s %s", $zip->status, $zip->statusSys, $code, $zip->getStatusString());
			return false;
		}

		$tmpdir = dp_get_tmp_dir() . DIRECTORY_SEPARATOR . time() . '-' . mt_rand(1000,9999);
		if (!mkdir($tmpdir)) {
			$error = 'Unable to make tmpdir: ' . $tmpdir;
			return false;
		}

		if (!$zip->extractTo($tmpdir)) {
			$error = sprintf("[%s/%s] %s", $zip->status, $zip->statusSys, $zip->getStatusString());
			return false;
		}

		$realpath = $tmpdir;
		if (is_dir($tmpdir . '/dp_zip')) {
			$realpath = $tmpdir . '/dp_zip';
		}

		if ($to) {
			$fileutil = new FilesystemUtil();
			$fileutil->mirror($realpath, $to, null, array('override' => true));
			$fileutil->remove($tmpdir);
			return $to;
		}

		return $realpath;
	}
}

class Zip_PclZip implements DpZip
{
	public function __construct()
	{
		require_once(DP_ROOT . '/vendor/pclzip/pclzip.lib.php');
	}

	public function compressFile($path)
	{
		$path = str_replace('\\', '/', $path);

		$filename     = basename($path);
		$out_filename = $filename . '-' . time() . '-' . mt_rand(1000,9999) . '.zip';
		$out_filepath = dp_get_tmp_dir() . DIRECTORY_SEPARATOR . $out_filename;

		$zip = new \PclZip($out_filepath);
		$zip->add(
			$path,
			\PCLZIP_OPT_REMOVE_PATH, dirname($path),
			\PCLZIP_OPT_ADD_PATH, 'dp_zip'
		);

		return $out_filepath;

	}

	public function decompressZip($path, $to = null, &$error = null)
	{
		$zip = new \PclZip($path);

		$tmpdir = dp_get_tmp_dir() . DIRECTORY_SEPARATOR . time() . '-' . mt_rand(1000,9999);
		if (!mkdir($tmpdir)) {
			$error = 'Unable to make tmpdir: ' . $tmpdir;
			return false;
		}

		if (!is_array($zip->extract(
			\PCLZIP_OPT_PATH, $tmpdir,
			\PCLZIP_OPT_ADD_TEMP_FILE_ON,
			\PCLZIP_OPT_STOP_ON_ERROR
		))) {
			$error = $zip->errorInfo(true);
			return false;
		}

		// Ones we make have dp_zip as the container folder
		$realpath = $tmpdir;
		if (is_dir($tmpdir . '/dp_zip')) {
			$realpath = $tmpdir . '/dp_zip';
		}

		if ($to) {
			$fileutil = new FilesystemUtil();
			$fileutil->mirror($realpath, $to, null, array('override' => true));
			$fileutil->remove($tmpdir);
			return $to;
		}

		return $realpath;
	}
}

########################################################################################################################
# Exception Classes
########################################################################################################################

/**
 * Exception thrown when trying to request a DeskPRO service
 */
class ServiceCallException extends \Exception
{
	/**
	 * An empty response from the server
	 */
	const NO_RESPONSE      = 100;

	/**
	 * An invalid response from the server (invalid JSON).
	 */
	const INVALID_RESPONSE = 200;
}


/**
 * Exception thrown when trying to perform a MySQL backup
 */
class MysqlBackupException extends \Exception
{
	/**
	 * We dont know where mysqldump is
	 */
	const NO_MYSQLDUMP = 100;

	/**
	 * The dump target file already exists
	 */
	const FILE_EXISTS  = 200;

	/**
	 * mysqldump exited with an error status
	 */
	const DUMP_ERROR   = 300;
}


/**
 * Exception thrown when trying to perform a MySQL backup
 */
class MysqlRestoreException extends \Exception
{
	/**
	 * We dont know where mysql is
	 */
	const NO_MYSQL = 100;

	/**
	 * The dump file doesnt exist
	 */
	const BAD_ZIP = 200;

	/**
	 * mysqldump exited with an error status
	 */
	const RESTORE_ERROR   = 300;

	/**
	 * There was a problem trying to extract the zip
	 */
	const EXTRACT_ERROR = 400;
}


/**
 * Exception thrown when trying to perform a file backup
 */
class FileBackupException extends \Exception
{
	/**
	 * The target backup dir exists
	 */
	const FILE_EXISTS = 100;

	/**
	 * There was an error to do with permissions
	 */
	const PERM_ERROR  = 200;
}


/**
 * Exception thrown when trying to download latest distro
 */
class DownloadException extends \Exception
{
	/**
	 * The target file already exists
	 */
	const FILE_EXISTS = 100;

	/**
	 * The target directory to put the distro into doesnt exist
	 */
	const NO_DIR      = 200;

	/**
	 * Couldnt write the distro to the target
	 */
	const PERM_ERROR  = 300;

	/**
	 * The distro appears to be corrupted
	 */
	const BAD_FILE    = 400;
}


/**
 * Exception thrown when trying to upgrade files on the filesystem from a zip
 */
class UpgradeFilesException extends \Exception
{
	/**
	 * The zip doesnt exist or appears to be invalid
	 */
	const BAD_ZIP       = 100;

	/**
	 * There was a problem trying to extract the zip
	 */
	const EXTRACT_ERROR = 200;

	/**
	 * There was a probelm while trying to put the new files in place.
	 * This is a bad error because it means there might be a half-upgraded filesystem.
	 */
	const COPY_ERROR    = 300;
}

/**
 * Exception thrown when trying to upgrade files on the filesystem from a zip
 */
class ZipException extends \Exception
{
	/**
	 * No way to create zips
	 */
	const NO_STRATEGY       = 100;
}

########################################################################################################################
# RUN
########################################################################################################################

$upgrade = new Upgrade();
$upgrade->run($_SERVER['argv']);
