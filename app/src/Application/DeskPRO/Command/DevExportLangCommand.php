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

use Application\DeskPRO\Languages\Build\OneSkyBuild;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;
use Application\DeskPRO\Languages\Build\TransifexBuild;

class DevExportLangCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:export-lang');
		$this->addOption('po', null, InputOption::VALUE_NONE, 'Export PO files');
		$this->addOption('transifex', null, InputOption::VALUE_NONE, 'Export to transifex');
		$this->addOption('onesky', null, InputOption::VALUE_NONE, 'Export to onesky');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if ($input->getOption('transifex')) {
			if (
				!dp_get_config('transifex.url')
				|| !dp_get_config('transifex.username')
				|| !dp_get_config('transifex.password')
			) {
				$output->writeln("Missing transifex configuration");
				return 1;
			}
		}

		if ($input->getOption('onesky')) {
			if (
				!dp_get_config('onesky.api_key')
				|| !dp_get_config('onesky.secret_key')
			) {
				$output->writeln("Missing onesky configuration");
				return 1;
			}
		}

		$done_any = false;

		if ($input->getOption('po')) {
			$ret = $this->exportPOs($input, $output);
			if ($ret) {
				return $ret;
			}
			$done_any = true;
		}

		if ($input->getOption('transifex')) {
			$ret = $this->exportTransifex($input, $output);
			if ($ret) {
				return $ret;
			}
			$done_any = true;
		}

		if ($input->getOption('onesky')) {
			$ret = $this->exportOneSky($input, $output);
			if ($ret) {
				return $ret;
			}
			$done_any = true;
		}

		if (!$done_any) {
			$output->writeln("<error>Choose an export option. See --help for options.");
			return 1;
		}

		return 0;
	}

	protected function exportPOs(InputInterface $input, OutputInterface $output)
	{
		$fileutil = new \Symfony\Component\HttpKernel\Util\Filesystem();
		$langpack = new \Application\DeskPRO\Languages\LangPackInfo();

		foreach ($langpack->getDefaultSections() as $section) {
			$section_dir = DP_ROOT.'/languages/default/' . $section;
			$export_dir = $section_dir . '/export';

			if (is_dir($export_dir)) {
				$fileutil->remove($export_dir);
			}

			if (!mkdir($export_dir, 0777, true)) {
				die("Failed to create $export_dir");
			}

			foreach ($langpack->getDefaultCategories($section) as $category) {
				$cat_path = $section_dir . '/' . $category . '.php';
				if (!is_file($cat_path)) {
					die('MISSING: ' . $cat_path);
				}

				$phrases = include($cat_path);

				$outfile = $export_dir . '/' . $category . '.po';
				$fs = fopen($outfile, 'w');

				fwrite($fs, 'msgid ""' . "\n");
				fwrite($fs, 'msgstr ""' . "\n");
				fwrite($fs, '"MIME-Version: 1.0\n"' . "\n");
				fwrite($fs, '"Content-Type: text/plain; charset=UTF-8\n"' . "\n");
				fwrite($fs, '"Content-Transfer-Encoding: 8bit\n"' . "\n");

				foreach($phrases as $source => $target) {

					fwrite($fs, "\nmsgid \"{$source}\"\n");
					fwrite($fs, "msgstr ");

					$parts = explode("\n", $target);
					foreach($parts as $i=>$part) {

						// escape " for PO format
						fwrite($fs, '"'. str_replace('"', '\\"', $part));

						if($i != count($parts) -1) {
							fwrite($fs, '\n');
						}

						fwrite($fs, "\"\n");
					}
				}

				fclose($fs);

				echo "Wrote: $outfile\n";
			}
		}

		return 0;
	}

	protected function exportTransifex(InputInterface $input, OutputInterface $output)
	{
		$build = new TransifexBuild(
			dp_get_config('transifex.url'),
			dp_get_config('transifex.username'),
			dp_get_config('transifex.password')
		);

		$wr = new \Orb\Log\Writer\ConsoleOutputWriter($output);
		$build->getLogger()->addWriter($wr);

		$build->updateAllSources();

		return 0;
	}

	public function exportOneSky(InputInterface $input, OutputInterface $output)
	{
		$build = new OneSkyBuild(dp_get_config('onesky.api_key'), dp_get_config('onesky.secret_key'));

		$wr = new \Orb\Log\Writer\ConsoleOutputWriter($output);
		$build->getLogger()->addWriter($wr);

		$build->updateAllSources();

		return 0;
	}
}
