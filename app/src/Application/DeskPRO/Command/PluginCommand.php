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

class PluginCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:plugin');
		$this->addOption('plugin', 'p', InputOption::VALUE_REQUIRED, 'ID of the plugin to handle data for');
		$this->addArgument('action', InputOption::VALUE_REQUIRED);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$pluginId = $input->getOption('plugin');
		if (!$pluginId) {
			$output->writeln("--plugin option must be specified");
			return 1;
		}

		/** @var $plugin \Application\DeskPRO\Entity\Plugin */
		$plugin = App::getEntityRepository('DeskPRO:Plugin')->findOneById($pluginId);
		if (!$plugin) {
			$output->writeln("Plugin '$pluginId' could not be found");
			return 1;
		}

		$action = $input->getArgument('action');
		switch ($action) {
			case 'export-sync-data':
				$plugin->exportSyncData();
				$output->writeln("$plugin->title sync data exported");
				break;

			case 'import-sync-data':
				$plugin->importSyncData();
				$output->writeln("$plugin->title sync data imported");
				break;

			default:
				$output->writeln("Unknown action '$action'");
				return 1;
		}

		return 0;
	}
}