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
 * @category Commands
 */

namespace Application\DeskPRO\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;


/**
 * dpdev:compile-js
 *
 * Compiles and minifies JS source files.
 *
 * NOTE: This command assumes default file structure, where assets are stored in
 * /static
 */
class AsseticCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setDefinition(array(
			new InputArgument('pack', InputArgument::REQUIRED, 'The packs to compile separated by comma. Example: agent_vendors. Or ALL for everything'),
			new InputOption('regex', 'p', InputOption::VALUE_NONE, 'Pack name is interpretted as a regex'),
			new InputOption('not', null, InputOption::VALUE_NONE, 'Pack name is excluded'),
			new InputOption('reload', 'r', InputOption::VALUE_NONE, 'Files are regenerated even if they arent stale'),
		))->setName('dp:assetic');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$packs = $input->getArgument('pack');
		$assetic_manager = $this->getContainer()->getSystemService('assetic_manager');

		$bundles = array();

		if ($packs == 'ALL' || $input->getOption('regex')) {
			if ($input->getOption('regex')) {
				foreach ($assetic_manager->getAllBundleNames() as $k) {
					$match = preg_match('#' . $packs . '#', $k);
					if ($input->getOption('not') && !$match) {
						$bundles[] = $k;
					} elseif (!$input->getOption('not') && $match) {
						$bundles[] = $k;
					}
				}
			} else {
				$bundles = $assetic_manager->getAllBundleNames();
			}
		} else {
			foreach (explode(',', $packs) as $p) {
				$p = trim($p);
				$bundles[] = $p;
			}
		}

		$reload = $input->getOption('reload');

		foreach ($bundles as $name) {
			echo "[PROCESSING] $name ... ";
			if ($reload) {
				echo 'reload ';
				$assetic_manager->writeBuildFile($name);
			} else {
				$assetic_manager->writeBuildFileIfStale($name);
			}
			echo " Done\n";
		}
	}
}
