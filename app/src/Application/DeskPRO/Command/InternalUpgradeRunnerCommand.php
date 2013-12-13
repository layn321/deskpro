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
 * @subpackage
 */

namespace Application\DeskPRO\Command;

namespace Application\DeskPRO\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;

class InternalUpgradeRunnerCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:internal-upgrade-runner');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->setVerbosity(0);
		if ($input->getOption('verbose')) {
			$output->setVerbosity(2);
		}

		$check = App::getDb()->fetchColumn("SELECT value FROM settings WHERE name = ?", array('core.croncheck.dp-cron'));
		if ($check) {
			$date = new \DateTime('@'.$check);
			$date_cut = new \DateTime('-15 minutes');

			if ($date_cut < $date) {
				// Giving it more time to run
				return 0;
			}
			// Otherwise assume crashed and continue
		}

		@file_put_contents(dp_get_tmp_dir() . '/auto-upgrade-started', time());

		if (file_exists(DP_WEB_ROOT . '/auto-update-status.php')) {
			@unlink(DP_WEB_ROOT . '/auto-update-status.php');
		}
		$write_status = function($code, $message = '') {
			$fp = @fopen(DP_WEB_ROOT . '/auto-update-status.php', 'a');
			if (!$fp) {
				return false;
			}
			$time = time();

			if (is_array($message)) {
				$message = json_encode($message);
			}

			// Wont ever happen, but best be sure
			$message = str_replace('<?', '< ?', $message);

			if (!@fwrite($fp, "STATUS(" . $code . ")@$time#$message\n")) {
				return false;
			}
			@fclose($fp);

			@file_put_contents(DP_WEB_ROOT . '/auto-update-is-running.trigger', 'This file indicates that the system is performing an upgrade. Helpdesk requests will be disabled until the upgrade finishes.');

			return true;
		};

		$skip_seg = '';
		if (!$this->getContainer()->getSetting('core.upgrade_backup_files')) {
			$skip_seg .= ' --skip-backup-file';
		}
		if (!$this->getContainer()->getSetting('core.upgrade_backup_db')) {
			$skip_seg .= ' --skip-backup-db ';
		}

		$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_started', null);
		$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_error_writeperm', null);
		$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_time', null);
		$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_set_at', null);
		$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_backup_files', null);
		$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_backup_db', null);

		if (!$write_status('runner_start')) {
			$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_error_writeperm', 1);
			$output->write('<error>Could not write upgrade status file to root dir: ' . DP_WEB_ROOT . '</error>');
			@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
			@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');
			return 1;
		}

		@chmod(DP_WEB_ROOT . '/auto-update-status.php', 0777);

		$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_started', 1);

		if (!dp_get_php_path(true)) {
			$write_status('error_php_path');
			$write_status("error_unknown_binary", array('php'));
			$write_status("error_basic_checks_fail");
			$output->write('<error>Could not find path to PHP</error>');
			@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
			@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');
			return 1;
		}

		#-------------------------
		# Check PHP infos
		#-------------------------

		if (dp_is_php_path_guessed()) {
			$cmd = sprintf(
				"%s %s",
				dp_get_php_path(),
				escapeshellarg(DP_ROOT.'/bin/phpinfo.php')
			);

			$ret = null;
			$out = null;
			exec($cmd, $out, $ret);

			$fail = true;
			if ($out) {
				$check_phpinfo = implode("\n", $out);
				$fail = !\Orb\Util\Env::isSamePhpInfo(
					\Orb\Util\Env::getPhpInfo(),
					$check_phpinfo
				);
			}

			if ($fail) {
				$write_status('error_php_path');
				$write_status("error_unknown_binary", array('php'));
				$write_status("error_basic_checks_fail");
				$output->write('<error>Could not find path to PHP (Detected PHP appears different than running PHP)</error>');
				@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
				@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');
				return 1;
			}
		}

		#-------------------------
		# Make sure PHP we have passes requirements
		#-------------------------

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
			$write_status("error_php_binary_failcheck");
			$write_status("error_basic_checks_fail", str_replace("\n", ' ', trim($out)));
			$output->write('<error>PHP sub-command binary fails server checks: ' . $out . '</error>');
			$output->write('<error>Check your config.php file to make sure $DP_CONFIG[\'php_path\'] is set to the correct PHP path.</error>');

			// Failed before we could actually do anything, dont keep helpdesk offline
			$this->getContainer()->getSettingsHandler()->setSetting('core.last_auto_upgrade_time', time());
			$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_started', null);
			@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
			@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');

			return 1;
		}

		#-------------------------
		# Exec upgrade command
		#-------------------------

		$cmd = sprintf(
			"%s %s --auto --quiet --write-status-file %s",
			dp_get_php_path(),
			escapeshellarg(DP_ROOT.'/bin/upgrade-util.php'),
			$skip_seg
		);

		$write_status('exec_cmd', $cmd);

		set_time_limit(0);
		$ret = null;
		$out = null;
		exec($cmd, $out, $ret);

		if (!$out) {
			$out = array();
		}

		$write_status('exec_result', $ret);
		$str_collapsed = implode(' ', $out);
		$write_status('exec_output', $str_collapsed);

		$str = implode("\n", $out);
		if ($str) {
			echo $str;
		}

		// Report a error status
		if ($ret) {
			$write_status('error_command', $str_collapsed);
		}

		$this->getContainer()->getSettingsHandler()->setSetting('core.last_auto_upgrade_time', time());
		$this->getContainer()->getSettingsHandler()->setSetting('core.upgrade_started', null);

		@unlink(DP_WEB_ROOT . '/auto-update-is-running.trigger');
		@unlink(dp_get_tmp_dir() . '/auto-upgrade-started');

		return $ret;
	}
}