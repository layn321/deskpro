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
*/

namespace Application\DeskPRO\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Log\Logger;

use Orb\Util\Util;
use Orb\Util\Numbers;
use Orb\Util\Strings;

class ImportCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	/**
	 * @var \Application\DeskPRO\Log\Logger
	 */
	protected $logger;

	/**
	 * @var float
	 */
	protected $cmd_start_time;

	protected $total_time;

	protected function configure()
	{
		$this->setName('dp:import');
		$this->addOption('info', null, InputOption::VALUE_NONE, 'Show information about the importer and config');
		$this->addOption('run', null, InputOption::VALUE_NONE, 'Run the importer from start to finish');
		$this->addOption('rerun', null, InputOption::VALUE_NONE, 'Set this as a re-run to import new data only');
		$this->addOption('step', null, InputOption::VALUE_REQUIRED, 'With --run, Start from this step');
		$this->addOption('exec-step', null, InputOption::VALUE_REQUIRED, 'Execute only this step');
		$this->addOption('exec-step-page', null, InputOption::VALUE_REQUIRED, 'With --exec-step, runs a page of the step. If not specified, page 1 is run.');
		$this->setHelp("This imports data from another platform into the currently installed helpdesk. Please read http://support.deskpro.com/ for more information.");
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->cmd_start_time = microtime(true);

		$output->setFormatter(new \Orb\Console\Formatter\MaxLineLengthFormatter(80));

		$GLOBALS['DP_NOSQL_LOG'] = true;
		$GLOBALS['DP_INDEX_NOINDEX'] = true;
		$GLOBALS['DP_ERR_NOSHOWTRACE'] = true;
		$GLOBALS['DP_IS_IMPORTING'] = true;

		#----------------------------------------
		# Figure out our run mode
		#----------------------------------------

		$mode = null;
		if ($input->getOption('exec-step') !== null) $mode = 'exec-step';
		elseif ($input->getOption('info')) $mode = 'info';
		elseif ($input->getOption('rerun')) $mode = 'rerun';
		elseif ($input->getOption('run')) $mode = 'run';

		$page = 0;
		if ($input->getOption('exec-step-page') !== null) $page = (int)$input->getOption('exec-step-page');
		if (!$page) {
			$page = 1;
		}

		if (!$mode) {
			echo "Choose one of the run modes: --info, --run, --step or --exec-step\n";
			return 1;
		}

		$start_step = $input->getOption('step');

		#----------------------------------------
		# Set environment
		#----------------------------------------

		error_reporting(E_ALL | E_STRICT);
		@ini_set('display_errors', true);
		@ini_set('memory_limit', -1);
		@set_time_limit(0);

		$logger = new Logger();
		$wr = new \Orb\Log\Writer\ConsoleOutputWriter($output);
		if ($output->getVerbosity() > 1) {
			$wr->addFilter(new \Orb\Log\Filter\PriorityFilter(Logger::DEBUG));
		} else {
			$wr->addFilter(new \Orb\Log\Filter\PriorityFilter(Logger::NOTICE));
		}
		$wr->addFilter(new \Orb\Log\Filter\CallbackFormatter(function($log_item) {
			if (isset($log_item['errinfo'])) {
				return null;
			}
			return $log_item;
		}));
		$logger->addWriter($wr);

		// Special callback for submitting error logs when there is one
		$wr = new \Orb\Log\Writer\Callback(function ($log_item) {
			static $count = 0;
			if (!isset($log_item['errinfo'])) {
				return;
			}
			if ($count++ > 3) return;

			// Dont send log immediately unless its a critial error,
			// notices etc will be sent in normal log at the end of a process,
			// these instant-report-sendings are meant for fatal type errors.
			if (isset($log_item['errinfo']) && isset($log_item['errinfo']['die']) && !$log_item['errinfo']['die']) {
				$GLOBALS['DP_HAS_NONFATAL_ERROR'] = $log_item['errinfo']['summary'];
				return;
			}

			\Application\DeskPRO\Command\ImportCommand::sendLogFile($log_item['errinfo']);
		});
		$logger->addWriter($wr);

		$log_file_path = $this->getContainer()->getKernel()->getUserLogDir() . '/import.log';
		if ($mode == 'run' && !$start_step) {
			@unlink($log_file_path);
		}
		try {
			if (!(isset($DP_CONFIG['import']['nolog']) && $DP_CONFIG['import']['nolog'])) {
				$wr = new \Orb\Log\Writer\Stream($log_file_path, 'a');
				$wr->enableNewStreamPerWrite();
				$wr->getStream();
			} else {
				$logger->disabled = true;
			}
			$logger->addWriter($wr);
		} catch (\Exception $e) {
			$output->writeln("Log file not writable: $log_file_path");
			$output->writeln("Make the logs directory ({$this->getContainer()->getKernel()->getUserLogDir()} is writable and try again.");
			return 1;
		}

		// Override default error logger so it logs to the file
		$GLOBALS['DP_ERR_LOGGER'] = $logger;
		$this->logger = $logger;

		$DP_CONFIG = $this->getContainer()->getSysConfig('*');

		if (!isset($DP_CONFIG['import']['db_host'])) {
			$logger->log('You need to fill in the "import" section of config.php before running this tool.', Logger::ERR);
			return 1;
		}

		#----------------------------------------
		# Check for database
		#----------------------------------------

		/** @var $db \Application\DeskPRO\DBAL\Connection */
		$db = $this->getContainer()->getDb();

		try {
			// Some PHP's, PDO's PDO::ATTR_ERRMODE to throw exceptions instead of issue warnings
			// doesnt work on connect(). Instead it throws the exception, but also issues the warning.
			// So temporarily disable warnings so we can gracefully handle these events

			$e = error_reporting(E_ALL ^ E_WARNING);
			$db->connect();
			error_reporting($e);
		} catch (\PDOException $e) {
			error_reporting($e);
			if ($e->getCode() == '1049') {

				$logger->log("We have detected that the database {$DP_CONFIG['db']['dbname']} does not exist. We will try to create it now.\n", Logger::DEBUG);

				// Attempt to create an empty database
				try {
					$dbh = new \PDO("mysql:host={$DP_CONFIG['db']['host']}", $DP_CONFIG['db']['user'], $DP_CONFIG['db']['password']);
					$dbh->exec("CREATE DATABASE `{$DP_CONFIG['db']['dbname']}`");
					$success = true;
				} catch (\Exception $e) {
					$success = false;
				}

				if (!$success) {
					$logger->log('The database name you have set in config.php does not exist and we could not create it.'  . PHP_EOL, Logger::ERR);
					return 21;
				} else {
					$logger->log('The database was created successfully.', Logger::DEBUG);
				}
			} elseif ($e->getCode() == '1044' || $e->getCode() == '1045') {

				$logger->log('Invalid user credentials set in config.php. Please check that the username and password set in config.php are correct and that the user has permission to access the database: ' . $e->getCode() . ' ' . $e->getMessage() . ''  . PHP_EOL, Logger::ERR);
				return 21;
			} else {
				$logger->log('There was a problem while trying to connect to your database: ' . $e->getCode() . ' ' . $e->getMessage() . ''  . PHP_EOL, Logger::ERR);
				return 21;
			}
		}

		// Check thei mport db too
		try {
			$e = error_reporting(E_ALL ^ E_WARNING);
			$params = array(
				'driver'        => 'pdo_mysql',
				'host'          => $DP_CONFIG['import']['db_host'],
				'user'          => $DP_CONFIG['import']['db_user'],
				'password'      => $DP_CONFIG['import']['db_password'],
				'dbname'        => $DP_CONFIG['import']['db_name'],
				'names_charset' => 'latin1'
			);

			$m = null;
			if (isset($params['host']) && preg_match('#^(.*?):([0-9]+)$#', $params['host'], $m)) {
				$params['host'] = $m[1];
				$params['port'] = $m[2];
			}

			$old_db = $this->getContainer()->get('doctrine.dbal.connection_factory')->createConnection($params);
			$old_db->connect();
			error_reporting($e);

			try {
				$techs = $old_db->fetchColumn("SELECT COUNT(*) FROM tech");
			} catch (\Exception $e) {
				$logger->log("The database details you entered for your DeskPRO v3 database appear to be incorrect. Check config.php to make sure you entered the correct details.\n", Logger::ERR);
				return 1;
			}
		} catch (\Exception $e) {
			error_reporting($e);
			$logger->log('There was a problem while trying to connect to your DeskPRO v3 database. Check config.php to make sure you entered the correct details. ' . PHP_EOL . $e->getMessage() . ''  . PHP_EOL, Logger::ERR);
			return 1;
		}

		#----------------------------------------
		# Check requirements
		#----------------------------------------

		if ($mode == 'run') {
			$install_token_file = $this->getContainer()->getLogDir() . '/install_token.dat';
			if (file_exists($install_token_file)) {
				$GLOBALS['dp_install_token'] = @file_get_contents($install_token_file);
			} else {
				$GLOBALS['dp_install_token'] = Strings::random(40, Strings::CHARS_ALPHANUM_IU) . time();
			}

			@file_put_contents($install_token_file, $GLOBALS['dp_install_token']);
		}

		if ($mode == 'run' && !$start_step) {

			$new_download = null;
			$this_build = date('Y-m-d', DP_BUILD_TIME);
			$new_build = 0;
			try {
				$latest_version = \Application\DeskPRO\Service\LicenseService::getLatestVersion();
				$new_build = date('Y-m-d', $latest_version['build']);
				if ($latest_version['build'] > DP_BUILD_TIME) {
					$new_download = $latest_version['download'];
				}
			} catch (\Exception $e) {}

			if ($new_download) {
				$output->writeln(sprintf("A newer version of DeskPRO is available. You have version %s but version %s is available for download.", $this_build, $new_build));
				$output->writeln(sprintf("You can download the new version from: %s", $new_download));

				try {
					$yes = $this->getHelper('dialog')->askConfirmation($output, 'Do you want to abort? [Y/n]> ');
				} catch (\Exception $e) {
					$yes = false;
				}
				if ($yes) {
					echo "Aborted. You can re-run this command again at any time.\n";
					echo "\n";
					return 0;
				}
			}

			try {
				$stat_db = $this->getContainer()->getDb();
			} catch (\Exception $e) {
				$stat_db = null;
			}
			$stats_fetcher = new \Application\InstallBundle\Data\ServerStats($stat_db);
			$stats = $stats_fetcher->getStats();

			$logger->log("Server Stats:\n" . \Orb\Util\Arrays::implodeTemplate($stats, "\t{KEY}: {VAL}\n"), Logger::DEBUG);

			$server_check = new \Application\InstallBundle\Install\ServerChecks();
			$server_check->setLogger($logger);
			$server_check->checkServer();

			$is_fatal = $server_check->hasFatalErrors();
			$has_config = false;
			$has_db_checks = false;

			if (!$is_fatal) {
				$has_db_checks = true;
				if (file_exists(DP_CONFIG_FILE)) {
					$has_config = true;
					$server_check->checkDatabase($DP_CONFIG['db']);
				}
			}

			if ($server_check->hasFatalErrors()) {
				$str = "There are problems with your server setup that prevents DeskPRO v4 from installing:\n";
				foreach ($server_check->getFatalErrors() as $err) {
					$str .= "- {$err['message']}\n";
				}
				echo "Fix these problems and try again.\n";
				$logger->log($str, Logger::ERR);

				$e = new \Application\InstallBundle\Install\ServerCheckException("Server requirements failed: " . implode(', ', array_keys($server_check->getFatalErrors())));
				self::sendLogFile(\DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e));

				return 1;
			}
		}

		#----------------------------------------
		# Get importer config
		#----------------------------------------

		$config = App::getConfig('import');
		if (!$config) {
			$logger->log("There is no `import` configuration.\n", Logger::ERR);
			return 1;
		}

		if (!isset($config['importer'])) {
			$config['importer'] = 'Deskpro3';
		}

		$importer_class = 'Application\\DeskPRO\\Import\\Importer\\' . $config['importer'] . 'Importer';
		if (!class_exists($importer_class)) {
			$logger->log("The `import.importer` class of {$config['importer']} does not exist.\n", Logger::ERR);
			return 3;
		}

		if (isset($config['store_attachment_files']) && $config['store_attachment_files']) {
			global $DP_CONFIG;
			$DP_CONFIG['core.filestorage_method'] = 'fs';
		}

		if (isset($DP_CONFIG['import']['existing_attachment_files'])) {
			$DP_CONFIG['import']['existing_attachment_files'] = rtrim($DP_CONFIG['import']['existing_attachment_files'], '/');
		}

		#----------------------------------------
		# Figure out PHP path
		#----------------------------------------

		if ($mode == 'run') {
			// First (main) runner, make sure php CLI is actually right
			$php_path = dp_get_php_path(true);
		} else {
			// Other calls dont need to do the binary check
			$php_path = dp_get_php_path(false);
		}

		if (!$php_path) {
			$logger->log("Unknown path to PHP executable. Edit your /config.php file and specify a value for php_path.\n", Logger::ERR);
			return 1;
		} else {
			if ($mode == 'run' && !$start_step) {
				$logger->log("Path to PHP executable found at " . $php_path, Logger::INFO);
			}
		}

		#----------------------------------------
		# Confirm path is right by checking reqs
		#----------------------------------------

		if ($mode == 'run') {

			$cli_check_file = dp_get_data_dir() .'/cli-server-reqs-check.dat';
			@unlink($cli_check_file);

			$cmd = $php_path . ' cmd.php dp_write_cli_info';
			$proc = new \Symfony\Component\Process\Process($cmd, DP_ROOT . '/../');
			$proc->setTimeout(360000);
			$proc->run(function ($type, $buffer) {
				if ('err' === $type) {
					echo '[ERR] '.$buffer;
				} else {
					echo $buffer;
				}
			});

			if (!$proc->isSuccessful()) {
				$logger->log("Error detected, stopping.", 'ERROR');
				return 1;
			}

			// Now check for the file that should've been written
			$okay = false;
			if (file_exists($cli_check_file)) {
				$cli_check = file_get_contents($cli_check_file);
				$cli_check = @unserialize($cli_check);

				$logger->log("cli check file: $cli_check_file: " . print_r($cli_check,1), Logger::DEBUG);

				if (is_array($cli_check) && isset($cli_check['checks'])) {
					$okay = true;
					if (in_array('fatal', $cli_check['checks'])) {
						$okay = false;
					}
				}
			} else {
				$logger->log("cli check file does not exist: $cli_check_file", Logger::DEBUG);
			}

			// Means they are different: Import command (the wrapper) works fine or else we would have quit already,
			// but the sub-command failed that generated the checks above failed.
			if (!$okay) {
				$logger->log("The automatically detected path to PHP is incorrect.\n", Logger::ERR);
				$logger->log("Edit your /config.php file and specify the path to your PHP binary under the php_path setting.\n", Logger::ERR);
				return 1;
			}
		}

		#----------------------------------------
		# Execute DP3 upgrade
		#----------------------------------------

		/** @var $importer \Application\DeskPRO\Import\Importer\Deskpro3Importer */
		$config['log_dir'] = App::getKernel()->getLogDir();
		$config['enable_query_log'] = false;
		$importer = new $importer_class($this->getContainer(), $config, $logger);
		$importer->validateOptions();

		if ($mode == 'run') {
			if ($importer instanceof \Application\DeskPRO\Import\Importer\Deskpro3Importer) {

				if ($importer->isLargeDatabase()) {
					$output->writeln("\n<info>Your database is quite large. Before you continue, we recommend reading our knowledgebase article on importing large databases:\nhttps://support.deskpro.com/kb/articles/115\n</info>");
					try {
						$yes = $this->getHelper('dialog')->askConfirmation($output, 'Do you want to continue with the import now? [Y/n]> ');
					} catch (\Exception $e) {
						$yes = false;
					}
					if (!$yes) {
						echo "\n";
						return 0;
					}
					echo "\n";
				}

				#----------------------------------------
				# Verify attachment paths
				#----------------------------------------

				if (!dp_get_config('import.dev_ignore_attachments')) {
					try {
						$has_filepath = $importer->getOldDb()->fetchColumn("
							SELECT filepath
							FROM blobs
							WHERE filepath IS NOT NULL
							ORDER BY id DESC
							LIMIT 1
						");
					} catch (\Exception $e) {
						$has_filepath = false;
						// it could fail if using an old version of deskpro,
						// so just catch it and it means not using file system (obviously)
					}

					if ($has_filepath) {
						if (!isset($DP_CONFIG['import']['existing_attachment_files']) || !$DP_CONFIG['import']['existing_attachment_files']) {
							$output->writeln("Your DeskPRO v3 installation is set to store attachments as files on the filesystem. You need to specify the path to these files in import options in config.php. Look for the `existing_attachment_files` option.");
							return 1;
						}

						$check_path = $DP_CONFIG['import']['existing_attachment_files'] . '/' . $has_filepath;
						if (!file_exists($check_path)) {
							$output->writeln("The path you entered for `existing_attachment_files` appears to be invalid. We checked for a file attachment but it does not exist: " . $check_path);
							return 1;
						}
					}
				}

				#----------------------------------------
				# Check version
				#----------------------------------------

				$other_version = $importer->getOldDb()->fetchColumn("SELECT value FROM settings WHERE name = ?", array('deskpro_version_internal'));
				if ($other_version < 3030001) {
					$output->writeln('Your DeskPRO v3 installation is outdated. Before we can import your helpdesk into the system, you must run the upgrader.');
					$output->writeln("<warn>\nWARNING: We will perform the upgrade directly on the database you specified ({$DP_CONFIG['import']['db_user']}@{$DP_CONFIG['import']['db_host']}/{$DP_CONFIG['import']['db_name']}). The database will be changed permanantly! You should not perform this upgrade on your live database. We recommend upgrading on a clone or backup.\n</warn>");
					$output->writeln('Do you want to upgrade your helpdesk database now?');

					$yes = $this->getHelper('dialog')->askConfirmation($output, '[y/N]> ', false);
					if (!$yes) {
						$output->writeln('Aborting. You can re-run this command when you are ready to proceed.');
						return 24;
					}

					#------------------------------
					# Backup
					#------------------------------

					$output->writeln('Do you want to backup your database to '.$this->getContainer()->getBackupDir().' first? This is HIGHLY recommended.');
					$yes = $this->getHelper('dialog')->askConfirmation($output, '[Y/n]> ', true);

					if ($yes) {
						$mysqldump_path = $this->getContainer()->getMysqldumpBinaryPath();
						if (!$mysqldump_path) {
							$output->writeln('We could not locate the path to the MySQL backup utility "mysqldump". You can edit /config.php to specify this path in the "mysqldump_path" setting.');
							return 25;
						}

						$f = "{$config['db_name']}-" . date('Y-m-d-H-i-s') . '.sql';
						$pass = '';
						if ($config['db_password']) {
							$pass = "--password=".escapeshellarg($config['db_password']);
						}
						$cmd = sprintf(
							"%s --opt -Q -h%s -u%s %s %s > %s",
							$mysqldump_path,
							escapeshellarg($config['db_host']),
							escapeshellarg($config['db_user']),
							$pass,
							escapeshellarg($config['db_name']),
							escapeshellarg($f)
						);

						@set_time_limit(0);
						chdir($this->getContainer()->getBackupDir());
						$ret = 0;
						passthru($cmd, $ret);

						if ($ret) {
							$output->writeln('<warn>We detected an error while trying to back up your DeskPRO v3 database. Do you want to continue anyway?</warn>');
							try {
								$yes = $this->getHelper('dialog')->askConfirmation($output, '[y/N]> ', false);
							} catch (\Exception $e) {
								$yes = false;
							}
							if (!$yes) {
								$output->writeln("Aborting. You can re-run this tool once you are ready to proceed.");
								return 25;
							}
						}
					}

					#------------------------------
					# Run the upgrader command
					#------------------------------

					$output->writeln("We are now running through the DeskPRO v3 upgrader. This may take some time.");

					$cmd = $this->getContainer()->getPhpBinaryPath() . ' index.php';
					$dir = DP_ROOT . '/sys/legacy/upgrader';

					chdir($dir);

					$cmd .= ' 2>&1';
					$ret = 0;
					passthru($cmd, $ret);

					if ($ret) {
						$output->writeln(PHP_EOL . 'We detected an error while executing the DeskPRO v3 upgrade. You should contact support@deskpro.com.');
						return 26;
					}
				}

				$old_helpdesk_url = $importer->getOldDb()->fetchColumn("SELECT value FROM settings WHERE name = ?", array('helpdesk_url'));
				$logger->log('dp_old_url(' . $old_helpdesk_url.')', \Orb\Log\Logger::DEBUG);
			}

			$output->writeln(
				"\n" .
				"The import process is about to begin.\n" .
				"The process is automatic and you will not need to do anything.\n" .
				"It is safe to leave this tool running unattended.\n"
			);

			if ($config['store_attachment_files']) {
				$output->writeln(
					"\n" .
					"Note that attachments will be copied to the filesystem (under /data/files).\n" .
					"You can disable this option in config.php.\n"
				);
			}

			try {
				$yes = $this->getHelper('dialog')->askConfirmation($output, 'Are ready to continue? [Y/n]> ');
			} catch (\Exception $e) {
				$yes = false;
			}
			if (!$yes) {
				echo "Aborted. You can re-run this command again at any time.\n";
				echo "\n";
				return 0;
			}
		}

		#----------------------------------------
		# Execute an install
		#----------------------------------------

		/** @var $sm \Doctrine\DBAL\Schema\AbstractSchemaManager */
		$sm = $db->getSchemaManager();

		$tables = $sm->listTableNames();
		if ($tables && $mode == 'run' && !$start_step) {

			try {
				$is_dp3              = $db->fetchColumn("SELECT `value` FROM settings WHERE name = 'deskpro_version'");
				$is_installed        = $db->fetchColumn("SELECT `value` FROM settings WHERE name = 'core.install_timestamp'");
				$is_imported         = $db->fetchColumn("SELECT `value` FROM settings WHERE name = 'core.imported_timestamp'");
				$is_imported_started = $db->fetchColumn("SELECT `value` FROM settings WHERE name = 'core.imported_timestamp_start'");

				if ($is_dp3) {
					$logger->log(
						"You have inserted database details for an existing DeskPRO v3 database into your config.php. "
						."You should put your DeskPRO v3 configuration into the 'import' section of config.php file instead. "
						."The database section at the top of the file should be used for a NEW database that DeskPRO v4 will use. "
						."Refer to the README.txt file for more information.\n"
					, Logger::ERR);
					return 22;
				} elseif ($is_imported_started) {
					if (!$is_imported) {
						$logger->log(
							"It appears as though you have attempted an import before but it did not finish. Unfortunately, there is "
							."no  method to resume an incomplete import. To try again, delete and re-create the DeskPRO v4 database "
							."and then execute this command to begin the process from the start."
						, Logger::ERR);
					} else {
						$logger->log("The import has already been processed. You should now try logging in to the admin interface at /admin/."  . PHP_EOL, Logger::ERR);
					}
					return 23;
				} elseif ($is_installed) {
					$logger->log(
						"It appears as though you have already installed DeskPRO into this database. "
						."The import tool needs to work on an empty database. You should create a new "
						."database, update your config.php file, and then you can re-run this tool.\n"
					, Logger::ERR);
					return 24;
				}
			} catch (\Exception $e) {}

			$logger->log('Your database already contains tables. DeskPRO requires a new, empty database to import into.' . PHP_EOL, Logger::ERR);
			$logger->log('Create a new empty database and edit /config.php with the new details, then try again.'  . PHP_EOL, Logger::ERR);
			return 22;
		}

		if (!$tables) {
			$logger->log(PHP_EOL . 'Welcome to the DeskPRO importer. Our first step is to install the DeskPRO v4 tables in your new database. This may take a minute.'  . PHP_EOL, Logger::INFO, array('ignore_pri_filter' => true));

			try {
				$db->exec("
					CREATE TABLE IF NOT EXISTS `install_data` (
					  `build` varchar(30) NOT NULL,
					  `name` varchar(75) NOT NULL DEFAULT '',
					  `data` blob NOT NULL,
					  PRIMARY KEY (`build`,`name`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8
				");
			} catch (\Exception $e) {
				$logger->log('There was a problem trying to create the first database table `install_data`: ' . $e->getCode() . ' ' . $e->getMessage(), Logger::ERR);

				if (strpos($e->getMessage(), 'access violation') !== false) {
					echo "\n\n";
					echo 'This probably means you need to grant privileges to your MySQL user on your database with a command similar to this: ';
					echo "\n";
					echo 'GRANT ALL PRIVILEGES ON `' . DP_DATABASE_NAME . '`.* TO \''.DP_DATABASE_USER.'\'@\'localhost\'';
					echo "\n";
				}
				return 1;
			}

			$tableinfo = $db->fetchColumn("SHOW CREATE TABLE `install_data`", array(), 1);
			if (stripos($tableinfo, 'innodb') === false) {
				$logger->log('Your database server created a new table, but it ignored the instruction to use the InnoDB engine. Please refer to our helpdesk for information on how to resolve this error: http://support.deskpro.com/', Logger::ERR);
				return 1;
			}

			if (!defined('DP_BUILD_TIME')) {
				$build_file = DP_ROOT.'/sys/config/build-time.php';
				if (is_file($build_file)) {
					require $build_file;
				} else {
					define('DP_BUILD_TIME', time());
				}
			}

			$schema = null;
			if (file_exists(DP_ROOT.'/src/Application/InstallBundle/Data/schema.php')) {
				$schema = require DP_ROOT.'/src/Application/InstallBundle/Data/schema.php';
			}
			$install_schema = new \Application\InstallBundle\Install\InstallSchema($db, $schema, DP_BUILD_TIME);

			$logger->log("Installing schema.", Logger::DEBUG);
			$time_start = microtime(true);

			$errors = array();

			$total = $install_schema->countQueries();
			$count = 0;
			$self = $this;
			$fn = function($section, $status, $sql, $x, $e = null) use (&$errors, &$count, $total, $self, $output, $logger) {
				$count++;
				$self->updateStatus($output, '1. Installing Database', $count, $total);
				if ($status == 'error') {
					$errors[] = $e . " (SQL: $sql)";
				}
			};

			$install_schema->run(false, 100000000, 0, $fn);

			$logger->log(sprintf("Done installing schema. Took %.5f seconds.", microtime(true)-$time_start), Logger::DEBUG);

			if ($errors) {
				$logger->log("There were errors while trying to install the database: " . implode("\n", $errors) . "\n", Logger::ERR);
				$logger->log("Re-create the database and try again.\n", Logger::INFO);
				return 23;
			}

			// Install default data
			$AGENTGROUP_ALL = null; // should be defined by the time we finish processing data.php
			$USERGROUP_EVERYONE = null; // should be defined by the time we finish processing data.php
			$WEB_INSTALL = false;
			$IMPORT_INSTALL = true;

			$this->getContainer()->getEm()->beginTransaction();

			try {
				$install_data = new \Application\InstallBundle\Install\InstallDataReader(DP_ROOT.'/src/Application/InstallBundle/Data/data.php');
				$em = $this->getContainer()->getEm();
				$translate = $this->getContainer()->get('deskpro.core.translate');

				$total = $install_data->count();
				$count = 0;
				foreach ($install_data as $php) {
					$count++;
					$self->updateStatus($output, '2. Installing Initial Records', $count, $total);
					eval($php);
				}

				$this->getContainer()->getEm()->flush();

				\Application\DeskPRO\DataSync\AbstractDataSync::syncAllBaseToLive();

				// For the all agent group, fetch permissions from the template
				if ($AGENTGROUP_ALL) {
					$scanner = new \Application\InstallBundle\Data\AgentGroupPermScanner();
					foreach ($scanner->getNames() as $p_name) {
						$p = new \Application\DeskPRO\Entity\Permission();
						$p->usergroup = $AGENTGROUP_ALL;
						$p->name = $p_name;
						$p->value = 1;
						$this->getContainer()->getEm()->persist($p);
					}
					$this->getContainer()->getEm()->flush();
				}
				if ($USERGROUP_EVERYONE) {
					$scanner = new \Application\InstallBundle\Data\UserGroupPermScanner();
					foreach ($scanner->getNames() as $p_name) {
						$p = new \Application\DeskPRO\Entity\Permission();
						$p->usergroup = $USERGROUP_EVERYONE;
						$p->name = $p_name;
						$p->value = 1;
						$this->getContainer()->getEm()->persist($p);
					}
					$this->getContainer()->getEm()->flush();
				}

				$this->getContainer()->getEm()->getConnection()->commit();
			} catch (\Exception $e) {
				$this->getContainer()->getEm()->getConnection()->rollback();
				throw $e;
			}

			App::getDb()->replace('settings', array(
				'name' => 'core.deskpro_build',
				'value' => DP_BUILD_TIME,
			));
			App::getDb()->replace('settings', array(
				'name' => 'core.deskpro_build_num',
				'value' => DP_BUILD_NUM,
			));
			App::getDb()->replace('settings', array(
				'name' => 'core.deskpro_version',
				'value' => date('YmdHis'),
			));
			App::getDb()->replace('settings', array(
				'name' => 'core.install_key',
				'value' => \Orb\Util\Strings::random(20, \Orb\Util\Strings::CHARS_KEY),
			));
			$db->replace('settings', array(
				'name' => 'core.install_token',
				'value' => isset($GLOBALS['dp_install_token']) ? $GLOBALS['dp_install_token'] : '',
			));

			echo "\n";
			echo "Proceeding with the import.";
			echo "\n";
		}

		if (isset($DP_CONFIG['core.filestorage_method']) && $DP_CONFIG['core.filestorage_method'] == 'fs') {
			$this->getContainer()->getDb()->replace('settings', array(
				'name' => 'core.filestorage_method',
				'value' => 'fs',
			));
		}

		#----------------------------------------
		# Execute import
		#----------------------------------------

		return $this->executeImport($importer, $mode, $page, $input, $output);
	}


	protected function executeImport($importer, $mode, $page, InputInterface $input, OutputInterface $output)
	{
		$logger = $this->logger;
		$php_path = dp_get_php_path(false);

		App::getDb()->replace('settings', array(
			'name' => 'core.imported_timestamp_start',
			'value' => time(),
		));

		$start_time = microtime(true);

		#----------------------------------------
		# Run the --info command
		#----------------------------------------

		if ($input->getOption('info')) {
			$importer_class = get_class($importer);
			$output->writeln("Importer: {$importer_class}");
			$output->writeln("Importer ID: {$importer->getId()}");
			$output->writeln("Number of steps: {$importer->countSteps()}");

			for ($i = 1; $i <= $importer->countSteps(); $i++) {
				$output->writeln(sprintf("\t%2s. %s", $i, $importer->getStepTitle($i)));
			}

			$output->writeln('');

			if ($errors = $importer->validateOptions()) {
				$output->writeln("Config errors:");
				foreach ($errors as $e) {
					$output->writeln("\t{$e}");
				}
			}

			return 0;
		}

		#----------------------------------------
		# Execute a single step and page
		#----------------------------------------

		if ($mode == 'exec-step') {
			$importer->validateOptions();
			$importer->setupImport();

			$step_num = $input->getOption('exec-step');
			$importer->preRunStep($step_num);
			$step = $importer->getStep($step_num);
			$step->run($page);
			$importer->postRunStep($step_num);

			$importer->cleanupImport();

			return 0;
		}

		#----------------------------------------
		# Run full importer
		#----------------------------------------

		if ($mode == 'run' || $mode == 'rerun') {

			$logger->log(sprintf("Starting importer %s", $importer->getId()), 'INFO');

			if ($errors = $importer->validateOptions()) {
				$logger->log(sprintf("There were %i errors detected before importing could begin", count($errors)), 'INFO');
				foreach ($errors as $e) {
					$logger->log($e, 'ERROR');
				}
				return 4;
			}

			$importer->setupImport($mode);
			$logger->log(sprintf("There are %d import steps.", $importer->countSteps()), 'INFO');
			echo "\n";

			$i = 1;
			$num = $importer->countSteps();

			if ($input->getOption('step')) {
				$i = $input->getOption('step');
			}

			if ($i < 1 || $i > $importer->countSteps()) {
				$output->writeln("`step` must be between 1 and {$importer->countSteps()}");
				return 1;
			}

			for (; $i <= $num; $i++) {

				$start_step_time = microtime(true);

				$step = $importer->getStep($i);
				$logger->log(sprintf("### Step %d: %s ###", $i, $step::getTitle(), $start_step_time), 'INFO');

				$skip = false;
				if ($mode == 'run' && !$step->on_run) {
					$skip = true;
				} elseif ($mode == 'rerun' && !$step->on_rerun) {
					$skip = true;
				}

				if ($importer->getConfig('fast_import') && !$step->on_fast) {
					$skip = true;
				}

				if ($skip) {
					$logger->log(sprintf("Skipping step (mode: %s, fast: %s)", $mode, $importer->getConfig('fast_import')), 'INFO');
					$this->updateStatus($output, sprintf('%2d.', $i) .' '.$step::getTitle(), 1, 1);
					continue;
				}

				$num_pages = $step->countPages();
				for ($p = 1; $p <= $num_pages; $p++) {

					$this->updateStatus($output, sprintf('%2d.', $i) .' '.$step::getTitle(), $p-1, $num_pages);

					if ($num_pages > 1) {
						$logger->log(sprintf("Part %d of %d", $p, $num_pages), 'INFO');
					}

					$cmd = $php_path . ' cmd.php '.$this->getName().' --exec-step=' . $i . ' --exec-step-page=' . $p;
					$proc = new \Symfony\Component\Process\Process($cmd, DP_ROOT . '/../');
					$proc->setTimeout(360000);
					$proc->run(function ($type, $buffer) {
						if ('err' === $type) {
							echo '[ERR] '.$buffer;
						} else {
							echo $buffer;
						}
					});

					if (!$proc->isSuccessful()) {
						$logger->log("Error detected, stopping.", 'ERROR');
						return 1;
					}

					if ($p == 1 && $num_pages == 1) {
						$this->updateStatus($output, sprintf('%2d.', $i) .' '.$step::getTitle(), 1, 2);
						usleep(500000);
					}
					$this->updateStatus($output, sprintf('%2d.', $i) .' '.$step::getTitle(), $p, $num_pages);
				}

				$end_step_time = microtime(true);
				$logger->log(sprintf("Step #%d complete: Took %0.3f seconds.\n", $i, $end_step_time-$start_step_time), 'INFO');
			}

			$importer->cleanupImport();

			// Clear caches like kb/news/ideas/files category caches
			App::getDb()->executeUpdate('TRUNCATE TABLE cache');

			App::getDb()->replace('settings', array(
				'name' => 'core.imported_timestamp',
				'value' => time(),
			));

			App::getDb()->replace('settings', array(
				'name' => 'core.install_timestamp',
				'value' => time(),
			));
			App::getDb()->replace('settings', array(
				'name' => 'core.install_build',
				'value' => defined('DP_BUILD_TIME') ? DP_BUILD_TIME : time(),
			));
			App::getDb()->replace('settings', array(
				'name' => 'core.tickets.use_ref',
				'value' => 1,
			));

			// Mark that we've done this import
			App::getDb()->replace('settings', array(
				'name' => 'core.' . strtolower(\Orb\Util\Util::getBaseClassname($importer)),
				'value' => '1',
			));

			$DP_CONFIG = $this->getContainer()->getSysConfig('*');
			foreach (array('db_host', 'db_user', 'db_name') as $k) {
				App::getDb()->replace('settings', array(
					'name' => 'core.imported_' . $k,
					'value' => $DP_CONFIG['import'][$k],
				));
			}

			$end_time = microtime(true);
			$logger->log(sprintf("Importer complete. Took %0.3f seconds.", $end_time-$start_time), 'INFO');

			$total_time = sprintf("%.03f", microtime(true) - $this->cmd_start_time);
			$GLOBALS['import_total_time'] = $total_time;

			$logger->log("All Done ($total_time seconds)", 'INFO');

			// Cleanup install token
			@unlink($this->getContainer()->getLogDir() . '/install_token.dat');

			if (!dp_get_config('debug.no_install_dat_file')) {
				@file_put_contents(dp_get_data_dir() . '/is_installed.dat', "Do not remove this file. It tells DeskPRO that the software has been installed and turns off access to /install/.");
			}

			\Application\DeskPRO\Command\ImportCommand::sendLogFile(false);

			return 0;
		}

		return 0;
	}

	public function updateStatus($output, $title, $cur, $max)
	{
		if ($output->getVerbosity() > 1) {
			return;
		}

		// Erase previous line
		echo "\r";
		echo str_repeat(' ', 40+25+2+6);
		echo "\r";

		$perc  = ceil(($cur / $max) * 100);
		$width = 25;
		$pips  = floor($perc / 4);

		printf("%-40s", $title);

		if ($cur >= $max) {
			$pips = $width;
			$perc = 100;
		}

		echo "[";
		echo str_repeat('=',$pips);
		if ($width-$pips > 0) {
			echo ">";
		} else {
			echo "=";
		}
		echo str_repeat(' ',$width-$pips);
		echo "] ";
		echo sprintf("%3d", $perc) . "%";

		if ($cur >= $max) {
			usleep(750000);

			// Erase previous line
			echo "\r";
			echo str_repeat(' ', 40+25+2+6);
			echo "\r";
			printf("%-40s", $title);
			echo "DONE\n";
		}
	}

	public static function sendLogFile($errinfo = null)
	{
		global $DP_CONFIG;
		if (isset($DP_CONFIG['debug']['no_report_errors']) AND $DP_CONFIG['debug']['no_report_errors']) {
			return;
		}

		$import_log_path = App::getKernel()->getUserLogDir() . '/import.log';
		if (!file_exists($import_log_path)) {
			return;
		}

		$data = array(
			'source_type' => 'import.dp3',
			'total_time' => isset($GLOBALS['import_total_time']) ? $GLOBALS['import_total_time'] : 'na',
			'log' => @file_get_contents($import_log_path),
			'errinfo' => $errinfo ? $errinfo : 0,
			'install_token' => isset($GLOBALS['dp_install_token']) ? $GLOBALS['dp_install_token'] : '',
			'nonfatal_error' => isset($GLOBALS['DP_HAS_NONFATAL_ERROR']) ? $GLOBALS['DP_HAS_NONFATAL_ERROR'] : 0
		);

		try {
			$stats_fetcher = new \Application\InstallBundle\Data\ServerStats(App::getDb());
			$data = array_merge($data, $stats_fetcher->getStats());
		} catch (\Exception $e) {}

		\Application\DeskPRO\Service\ErrorReporter::sendInstallReport($data);
	}
}
