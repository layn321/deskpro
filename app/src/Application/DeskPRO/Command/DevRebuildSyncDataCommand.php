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

use Application\DeskPRO\DataSync\AbstractDataSync;

class DevRebuildSyncDataCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:rebuild-sync-data');
		$this->addOption('rebuild', null, InputOption::VALUE_REQUIRED, 'Rebuilds the specified sync data type');
		$this->addOption('rebuild-all', null, InputOption::VALUE_NONE, 'Rebuilds all sync data');
		$this->addOption('list', null, InputOption::VALUE_NONE, 'Lists the available sync data');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if (!dp_get_config('debug.dev')) {
			$output->write("Dev mode is not enabled");
			return 1;
		}

		if ($input->getOption('rebuild-all')) {
			$output->writeln("Rebuilding all sync data...");
			$start = microtime(true);

			$classes = AbstractDataSync::getAvailableSyncClasses();
			foreach ($classes AS $name => $class) {
				/* @var $sync \Application\DeskPRO\DataSync\AbstractDataSync */
				$sync = new $class();
				$sync->writeToBase();
				$output->writeln(sprintf("\tRebuilt %s data.", $name));
			}

			$end = microtime(true);
			$output->writeln(sprintf("Done (%.4fs)", $end - $start));
			return 0;
		} else if ($input->getOption('rebuild')) {
			$name = $input->getOption('rebuild');
			$classes = AbstractDataSync::getAvailableSyncClasses();

			if (isset($classes[$name])) {
				$class = $classes[$name];

				/* @var $sync \Application\DeskPRO\DataSync\AbstractDataSync */
				$sync = new $class();
				$sync->writeToBase();
				$output->writeln(sprintf("Rebuilt %s data.", $name));
				return 0;
			} else {
				$output->writeln(sprintf("Could not find %s data.", $name));
				return 1;
			}
		} else if ($input->getOption('list')) {
			$names = array_keys(AbstractDataSync::getAvailableSyncClasses());

			$output->writeln(sprintf("Available sync data options:\n\t%s", implode("\n\t", $names)));
			return 0;
		} else {
			$output->writeln("Use --help to see available commands");
			return 0;
		}
	}
}
