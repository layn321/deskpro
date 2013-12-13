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

use Application\DeskPRO\Languages\AllPhrases;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class DevLangCheckPhraseIdsCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:lang:check-phrase-ids');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$phrase_loader = new AllPhrases(DP_ROOT.'/languages/default');
		$phrase_ids = $phrase_loader->getPhraseIds();
		unset($phrase_loader);

		$phrase_ids = array_combine($phrase_ids,$phrase_ids);

		#------------------------------
		# Read all DeskPRO files to try and find
		# phrase tags that reference unknown phrase IDs
		#------------------------------

		$missing_count = 0;

		$search_dirs = array(
			DP_ROOT.'/src/Application',
			DP_ROOT.'/src/Cloud',
		);

		$finder = Finder::create()->files()->name('*.php')->name('*.twig')->in($search_dirs);
		foreach ($finder as $file) {
			/** @var $file \SplFileInfo */
			$path = $file->getRealPath();
			$file_content = file_get_contents($path);

			$missing = array();

			switch ($file->getExtension()) {
				case 'php':
					$matches = null;
					if (preg_match_all('#phrase\((\'|")([a-zA-Z0-9\._\-]+)\1\)#', $file_content, $matches, \PREG_PATTERN_ORDER)) {
						foreach ($matches[2] as $pid) {
							if (!isset($phrase_ids[$pid])) {
								$missing[$pid] = $pid;
							}
						}
					}
					break;

				case 'twig':
					$matches = null;
					if (preg_match_all('#phrase\((\'|")([a-zA-Z0-9\._\-]+)\1\)#', $file_content, $matches, \PREG_PATTERN_ORDER)) {
						foreach ($matches[2] as $pid) {
							if (!isset($phrase_ids[$pid])) {
								$missing[$pid] = $pid;
							}
						}
					}
					break;
			}

			if ($missing) {
				$output->writeln("\n\n<info>####################\n# $path\n####################\n</info>");
				echo "Bad phrase IDs:";
				foreach ($missing as $m) {
					echo "\t$m\n";
				}

				$missing_count += count($missing);
			}
		}

		echo "\n";

		if ($missing_count) {
			$output->writeln("<error>Found $missing_count bad phrase IDs</error>\n");
			return 1;
		} else {
			$output->writeln("<info>All phrase IDs check out fine</info>\n");
		}

		return 0;
	}
}