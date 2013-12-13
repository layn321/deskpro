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

use Application\DeskPRO\Languages\Build\OneSkyBuild;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\Languages\Build\TransifexBuild;

class DevBuildLangCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:dev-build-lang');
		$this->addOption('lang-id', 'l', InputOption::VALUE_REQUIRED, 'Only build a specific language instead of all');
		$this->addOption('transifex', null, InputOption::VALUE_NONE, 'Build from transifex');
		$this->addOption('onesky', null, InputOption::VALUE_NONE, 'Build form onesky');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$build_transifex = $input->getOption('transifex');
		$build_onesky = $input->getOption('onesky');
		$done_any = false;

		if ($build_transifex) {
			$done_any = true;
			if (
				!dp_get_config('transifex.url')
				|| !dp_get_config('transifex.username')
				|| !dp_get_config('transifex.password')
			) {
				$output->writeln("Missing transifex configuration");
				return 1;
			}

			$build = new TransifexBuild(
				dp_get_config('transifex.url'),
				dp_get_config('transifex.username'),
				dp_get_config('transifex.password')
			);

			$wr = new \Orb\Log\Writer\ConsoleOutputWriter($output);
			$build->getLogger()->addWriter($wr);

			if ($input->getOption('lang-id')) {

				if (!$build->getLangPackInfo()->hasLang($input->getOption('lang-id'))) {
					$output->writeln("Invalid language ID");
					return 2;
				}

				$diff = $build->buildLanguage($input->getOption('lang-id'));

				$lang_title = $build->getLangPackInfo()->getLangInfo($input->getOption('lang-id'), 'title');
				echo sprintf(">> Built %-30s Changed: %-4s Added: -%4s Removed: %-s4s\n", $lang_title, count($diff['changed']), count($diff['added']), count($diff['removed']));
			} else {
				$diffs = $build->buildAll();

				foreach ($diffs as $id => $diff) {
					$lang_title = $build->getLangPackInfo()->getLangInfo($id, 'title');
					echo sprintf(">> Built %-18s Changed: %-4s Added: %-4s Removed: %-4s\n", $lang_title, count($diff['changed']), count($diff['added']), count($diff['removed']));
				}
			}
		}

		if ($build_onesky) {
			$done_any = true;
			if (
				!dp_get_config('onesky.api_key')
				|| !dp_get_config('onesky.secret_key')
			) {
				$output->writeln("Missing onesky configuration");
				return 1;
			}

			$build = new OneSkyBuild(dp_get_config('onesky.api_key'), dp_get_config('onesky.secret_key'));

			$wr = new \Orb\Log\Writer\ConsoleOutputWriter($output);
			$build->getLogger()->addWriter($wr);

			if ($input->getOption('lang-id')) {

				if (!$build->getLangPackInfo()->hasLang($input->getOption('lang-id'))) {
					$output->writeln("Invalid language ID");
					return 2;
				}

				$diff = $build->buildLanguage($input->getOption('lang-id'));

				$lang_title = $build->getLangPackInfo()->getLangInfo($input->getOption('lang-id'), 'title');
				echo sprintf(">> Built %-30s Changed: %-4s Added: -%4s Removed: %-s4s\n", $lang_title, count($diff['changed']), count($diff['added']), count($diff['removed']));
			} else {
				$diffs = $build->buildAll();

				foreach ($diffs as $id => $diff) {
					$lang_title = $build->getLangPackInfo()->getLangInfo($id, 'title');
					echo sprintf(">> Built %-18s Changed: %-4s Added: %-4s Removed: %-4s\n", $lang_title, count($diff['changed']), count($diff['added']), count($diff['removed']));
				}
			}
		}

		if (!$done_any) {
			$output->writeln("<error>Choose an build option. See --help for options.</error>");
			return 1;
		}

		return 0;
	}
}