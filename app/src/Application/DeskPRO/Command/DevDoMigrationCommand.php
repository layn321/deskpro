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

use Symfony\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Symfony\Bundle\DoctrineMigrationsBundle\Command\DoctrineCommand;

use Doctrine\DBAL\Migrations\Configuration\Configuration;

use Doctrine\ORM\Tools\SchemaTool;

use Orb\Util\Util;
use Orb\Util\Numbers;

use Application\DeskPRO\App;

class DevDoMigrationCommand extends \Symfony\Bundle\DoctrineMigrationsBundle\Command\MigrationsMigrateDoctrineCommand
{
	protected function configure()
	{
		parent::configure();
		$this->setName('dpdev:do-migration');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		if (!dp_get_config('debug.dev')) {
			$output->write("Dev mode is not enabled. Did you mean to use the upgrade.php command?");
			return 0;
		}

        set_time_limit(0);
		$check = App::getDb()->fetchColumn("SHOW TABLES LIKE 'dev_migration_versions'");
		if (!$check) {
			App::getDb()->exec("
				CREATE TABLE `dev_migration_versions` (
				  `version` varchar(255) NOT NULL,
				  PRIMARY KEY (`version`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;
			");
		}

		$version = App::getDb()->fetchColumn("SELECT version FROM dev_migration_versions ORDER BY version DESC LIMIT 1");
		$setting_version = App::getDb()->fetchColumn("SELECT value FROM settings WHERE name = 'core.deskpro_version'");

		if ($setting_version && (!$version || $setting_version > $version)) {
			$finder = new \Symfony\Component\Finder\Finder();
			$finder->files()->name('Version*.php')->in(DP_ROOT.'/sys/Resources/DoctrineMigrations');
			foreach ($finder as $f) {
				$version = \Orb\Util\Strings::extractRegexMatch('#Version([0-9]+)\.php#', $f->getFilename(), 1);
				if ($version) {
					$version = (int)$version;
					if ($version <= $setting_version) {
						App::getDb()->replace('dev_migration_versions', array('version' => $version));
					}
				}
			}
		}

		parent::execute($input, $output);

		$version = App::getDb()->fetchColumn("SELECT version FROM dev_migration_versions ORDER BY version DESC LIMIT 1");
		if (!$version) {
			$version = date('YmdHis');
		}

		// Update version setting
		App::getDb()->replace('settings', array(
			'name' => 'core.deskpro_version',
			'value' => $version
		));
	}

}