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

use DeskPRO\Kernel\KernelErrorHandler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;

class UpgradeCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:upgrade')
		     ->addOption('info', null, InputOption::VALUE_NONE, 'Set this flag to get info about your current instance')
		     ->addOption('dobuildrun', null, InputOption::VALUE_REQUIRED, 'Runs a build script. Usually used internally.')
		     ->setHelp("This command executes the upgrader to bring your database to the same version the filesystem is");
	}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		set_time_limit(0);

		// Clear caches, including doctrine query caches
		App::getDb()->exec("TRUNCATE TABLE cache");
		@unlink(dp_get_tmp_dir() . DIRECTORY_SEPARATOR . 'dql.cache');

		$manager = new \Application\InstallBundle\Upgrade\Manager($this->getContainer());

		#------------------------------
		# Info
		#------------------------------

		if ($input->getOption('info')) {

			$next_id = $manager->getNextBuildId();
			$output->writeln(sprintf("\tInstalled version:   %d (%s)", $manager->getCurrentBuild(),  $manager->formatBuildId($manager->getCurrentBuild())));
			if (!$next_id) {
				$output->writeln(sprintf("\t     Next version:   none", $manager->getNextBuildId(),  $manager->formatBuildId($manager->getNextBuildId())));
			} else {
				$output->writeln(sprintf("\t     Next version:   %d (%s)", $manager->getNextBuildId(),  $manager->formatBuildId($manager->getNextBuildId())));
			}

			$output->writeln(sprintf("\t   Latest version:   %d (%s)", $manager->getLatestBuildId(), $manager->formatBuildId($manager->getLatestBuildId())));

			echo "\n";

			if (!$next_id) {
				$output->writeln("You are all up to date!");
			} else {
				$output->writeln("Builds that need to be executed:");
					foreach ($manager->getWaitingBuildIds() as $build_id) {
						$output->writeln(sprintf("\t%d (%s)", $build_id, $manager->formatBuildId($build_id)));
					}
			}

			return 0;
		}

		if (!$manager->getNextBuildId()) {
			$output->writeln("You are all up to date!");
		}

		#------------------------------
		# Runs a build script
		#------------------------------

		if ($input->getOption('dobuildrun')) {
			$manager->runBuild($input->getOption('dobuildrun'));
			return 0;
		}

		#------------------------------
		# The main executor loop
		#------------------------------

		chdir(DP_ROOT . '/../');

		while ($next_id = $manager->getNextBuildId()) {
			$output->writeln("<info>Build #$next_id</info>");

			$cmd = dp_get_php_command('cmd.php', "dp:upgrade --dobuildrun=$next_id");
			$ret = null;
			passthru($cmd, $ret);

			if ($ret) {
				return $ret;
			}

			$manager->reset();
		}

		#------------------------------
		# Auto-crrect keys
		#------------------------------

		if (!defined('DP_UPGRADE_NO_CORRECT_KEYS') && !defined('DPC_IS_CLOUD')) {
			$output->writeln("<info>Running automatic schema correction</info>");
			$cmd = dp_get_php_command('cmd.php', 'dp:schema-correction --apply');
			$ret = null;
			passthru($cmd, $ret);
		}

		#------------------------------
		# Post Run
		#------------------------------

		$output->writeln("<info>Running post scripts</info>");
	    $manager->postUpgrade();

		if (defined('DP_BUILD_TIME')) {
			$current = App::getDb()->fetchColumn("SELECT value FROM settings WHERE name = 'core.deskpro_build'");
			if ($current < DP_BUILD_TIME) {
				App::getDb()->replace('settings', array('value' => DP_BUILD_TIME, 'name' => 'core.deskpro_build'));
				App::getDb()->replace('settings', array('value' => DP_BUILD_NUM, 'name' => 'core.deskpro_build_num'));
			}
		}

		$output->writeln("<info>Done All</info>");

		return 0;
	}
}