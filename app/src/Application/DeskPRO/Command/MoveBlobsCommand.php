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

use Application\DeskPRO\BlobStorage\MoveBlobsUtil;
use Orb\Log\Logger;
use Orb\Log\Writer\ConsoleOutputWriter;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;

use Orb\Util\Arrays;
use Orb\Util\Strings;


class MoveBlobsCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dp:move-blobs');
		$this->setHelp("Attempts to move all blobs that are not in their preferred storage location");
		$this->addOption('ignore-error', null, InputOption::VALUE_NONE, 'Do not exit on errors. The blob will not be lost, but the process will continue even if one blob fails for whatever reason.');
		$this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Only run this many blobs at once');
		$this->addOption('run', null, InputOption::VALUE_NONE, 'Actually run the move now (default is to show info)');
		$this->addOption('set-storage-loc', null, InputOption::VALUE_REQUIRED, 'Update every blob and set the preferred storage loc');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$bs = App::getContainer()->getBlobStorage();
		$ignore_error = $input->getOption('ignore-error');
		$limit = $input->getOption('limit');

		if ($input->getOption('set-storage-loc')) {
			$set_aid = $input->getOption('set-storage-loc');
			$aids = $bs->getAdapterIds();
			if (!in_array($set_aid, $aids)) {
				$output->writeln("<error>Adapter is not installed: $set_aid</error>");
				return 1;
			}

			$output->writeln("Updating preferred storage location to use adapter: $set_aid");
			$t = microtime(true);
			$c = App::getDb()->executeUpdate("
				UPDATE blobs
				SET storage_loc_pref = ? WHERE storage_loc != ?
			", array($set_aid, $set_aid));
			$output->writeln(sprintf("<info>$c records updated in %.3fs</info>", microtime(true)-$t));
		}

		if (!$input->getOption('run')) {
			$t = array('Adapter', 'Count', 'Waiting Count', 'Is Installed');
			$counts = App::getDb()->fetchAllKeyValue("SELECT storage_loc, COUNT(*) AS count FROM blobs GROUP BY storage_loc");
			$counts2 = App::getDb()->fetchAllKeyValue("SELECT storage_loc_pref, COUNT(*) AS count FROM blobs WHERE storage_loc_pref IS NOT NULL GROUP BY storage_loc_pref");

			$aids = $bs->getAdapterIds();
			$aids = array_merge(array_keys($counts), array_keys($counts2), $aids);
			$aids = array_unique($aids);

			$rows = array();
			foreach ($aids as $aid) {
				$r = array();
				$r[] = $aid;
				$r[] = isset($counts[$aid]) ? $counts[$aid] : 0;
				$r[] = isset($counts2[$aid]) ? $counts2[$aid] : 0;

				if (in_array($aid, $bs->getAdapterIds())) {
					$r[] = 'Yes' . ($aid == $bs->getPreferredAdapterId() ? ' *' : '');
				} else {
					$r[] = 'No';
				}

				$rows[] = $r;
			}

			echo Strings::asciiTable($rows, $t, false);
			echo "\n";
			echo "\n";

			$output->writeln("Run this command again with the --run switch to start the process. Run --help to get an overview of the options.");
			return 0;
		}

		$mover = new MoveBlobsUtil(App::getOrm(), App::getContainer()->getBlobStorage());
		$logger = new Logger();
		$wr = new ConsoleOutputWriter($output);
		$logger->addWriter($wr);

		$mover->setLogger($logger);
		if ($ignore_error) {
			$mover->setIgnoreErrors();
		}
		if ($limit) {
			$mover->setLimit($limit);
		}

		$count = $mover->getCount();

		$output->writeln("Enabled adapters: " . implode(', ', $bs->getAdapterIds()));
		$output->writeln("<info>Blobs waiting to be moved: $count</info>");
		if (!$count) {
			$output->writeln("Nothing to do.");
			return 0;
		}

		App::getOrm()->getRepository('DeskPRO:Setting')->updateSetting('core.filesystem_move_from_id', null);

		$mover->run();

		return 0;
	}
}
