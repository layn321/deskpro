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

use Orb\Util\Arrays;
use Orb\Util\Strings;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\Route;

class PhraseCheckCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setDefinition(array())->setName('dpdev:phrase-check');
		$this->addArgument('langfiles', InputArgument::REQUIRED, 'Which language files to scan (e.g., agent or agent-emails)');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		#------------------------------
		# Get phrases to check
		#------------------------------

		$check_phrases = array();

		$opt_files = $input->getArgument('langfiles');

		if (!preg_match('#^(admin|agent|user)#', $opt_files)) {
			$output->writeln("<error>Invalid langfiles argument.</error>");
			return 1;
		}

		if (strpos($opt_files, '-') === false) {
			$col = Finder::create()->files()->name('*.php')->in(DP_ROOT.'/languages/default/'.$opt_files);
			foreach ($col as $f) {
				/** @var \SplFileInfo $f */
				$file_phrases = include($f->getRealPath());
				$file_phrases = array_keys($file_phrases);
				$check_phrases = array_merge($check_phrases, $file_phrases);
			}
		} else {
			$opt_files = str_replace('-', DIRECTORY_SEPARATOR, $opt_files) . '.php';
			if (!is_file(DP_ROOT.'/languages/default/'.$opt_files)) {
				$output->writeln("<error>Invalid langfiles argument.</error>");
				return 1;
			}

			$check_phrases = include($opt_files);
			$check_phrases = array_keys($check_phrases);
		}

		#------------------------------
		# Now check the files
		#------------------------------

		$admin_list = iterator_to_array(Finder::create()->files()->in(array(
			DP_ROOT.'/src/Application/AdminBundle',
			DP_ROOT.'/src/Cloud/AdminBundle',
		))->getIterator());

		$agent_list = iterator_to_array(Finder::create()->files()->in(array(
			DP_ROOT.'/src/Application/AgentBundle',
		))->getIterator());

		$user_list = iterator_to_array(Finder::create()->files()->in(array(
			DP_ROOT.'/src/Application/UserBundle',
		))->getIterator());

		$other_list = iterator_to_array(Finder::create()->files()->in(array(
			DP_ROOT.'/src/Application/ReportBundle',
			DP_ROOT.'/src/Application/BillingBundle',
			DP_ROOT.'/src/Application/DeskPRO',
			DP_ROOT.'/src/Application/InstallBundle',
			DP_ROOT.'/src/Cloud/BillingBundle',
			DP_WEB_ROOT.'/plugins',
		))->getIterator());

		foreach ($check_phrases as $phrase) {
			$search_lists = array();

			if (strpos($phrase, 'admin.') === 0) {
				$search_lists[] = 'admin_list';
			} elseif (strpos($phrase, 'agent.') === 0) {
				$search_lists[] = 'agent_list';
				$search_lists[] = 'admin_list';
			} else {
				$search_lists[] = 'user_list';
			}

			$search_lists[] = 'other_list';

			$found = false;
			foreach ($search_lists as $list_name) {
				foreach ($$list_name as $f) {
					/** @var \SplFileInfo $f */
					$content = file_get_contents($f->getRealPath());

					if (strpos($content, $phrase) !== false) {
						$found = true;
						break 2;
					}
				}
			}

			if (!$found) {
				echo "* " . $phrase . "\n";
			}
		}

		echo "\n";
		return 0;
    }
}
