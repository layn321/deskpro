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

use Orb\Util\Arrays;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DevLangCheckVarsCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:lang:check-vars');
		$this->addOption('with-plural', null, InputOption::VALUE_NONE, 'Also check for plurals');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$do_plural_check = $input->getOption('with-plural');

		#------------------------------
		# Get default phrases and lang dirs
		#------------------------------

		$default_phrases = $this->_readLang(DP_ROOT . '/languages/default');

		$lang_dirs = array();

		$dir = dir(DP_ROOT . "/languages");
		while (($f = $dir->read()) !== false) {
			if ($f == '.' || $f == '..' || $f == 'default') continue;
			$fpath = DP_ROOT . "/languages/$f";

			if (is_dir($fpath)) {
				$lang_dirs[] = $f;
			}
		}
		$dir->close();

		#------------------------------
		# Process langs to detect vars
		#------------------------------

		$bad_count = 0;

		foreach ($lang_dirs as $dirname) {
			$done_one = false;
			$lang_phrases = $this->_readLang(DP_ROOT . "/languages/$dirname");

			foreach ($lang_phrases as $phrase => $phrasetext) {
				if (!isset($default_phrases[$phrase])) {
					$output->writeln("<error>$phrase</error> should not exist:");
					continue;
				}

				$default_phrasetext = $default_phrases[$phrase];
				$default_vars = $this->_getVars($default_phrasetext);
				$lang_vars = $this->_getVars($phrasetext);

				$is_bad = false;
				if (count($default_vars) != count($lang_vars)) {
					$is_bad = true;
				} else if ($default_vars || $lang_vars) {
					if (!Arrays::isIn($default_vars, $lang_vars, true, true)) {
						$is_bad = true;
					}
				}

				$is_bad_html = false;
				if (!$is_bad) {
					$default_html_vars = $this->_getHtmlVars($default_phrasetext);
					$lang_html_vars = $this->_getHtmlVars($phrasetext);

					if (count($default_html_vars) != count($lang_html_vars)) {
						$is_bad_html = true;
					} else if ($default_html_vars || $lang_html_vars) {
						if (!Arrays::isIn($default_html_vars, $lang_html_vars, true, true)) {
							$is_bad_html = true;
						}
					}
				}

				if ($is_bad) {

					if (!$done_one) {
						$output->writeln("\n\n<info>####################\n# $dirname\n####################\n</info>");
						$done_one = true;
					}

					$output->writeln("<error>$phrase</error> has bad vars:");
					echo "\tDefault: $default_phrasetext\n";
					echo "\tLang: $phrasetext\n\n";

					$bad_count++;
				} else if ($is_bad_html) {

					if (!$done_one) {
						$output->writeln("\n\n<info>####################\n# $dirname\n####################\n</info>");
						$done_one = true;
					}

					$output->writeln("<error>$phrase</error> has bad HTML vars:");
					echo "\tDefault: $default_phrasetext\n";
					echo "\tLang: $phrasetext\n\n";

					$bad_count++;

				} else if ($do_plural_check) {
					$default_is_plural = (bool)strpos($default_phrasetext, '|');
					$lang_is_plural = (bool)strpos($phrasetext, '|');

					if ($default_is_plural != $lang_is_plural) {
						if (!$done_one) {
							$output->writeln("\n\n<info>####################\n# $dirname\n####################\n</info>");
							$done_one = true;
						}

						$output->writeln("<error>$phrase</error> plurality does not match:");
						echo "\tDefault: $default_phrasetext\n";
						echo "\tLang: $phrasetext\n\n";

						$bad_count++;
					}
				}
			}
		}

		if ($bad_count) {
			$output->writeln("<error>Found $bad_count phrases with bad varnames</error>\n");
			return 1;
		} else {
			$output->writeln("<info>All phrases check out fine</info>\n");
		}

		return 0;
	}

	private function _getVars($phrasetext)
	{
		$matches = 0;
		if (!preg_match_all('#\{\{\s*(.*?)\s*\}\}#', $phrasetext, $matches, \PREG_PATTERN_ORDER)) {
			return array();
		}

		$vars = array();

		foreach ($matches[1] as $m) {
			$vars[$m] = $m;
		}

		return $vars;
	}

	private function _getHtmlVars($phrasetext)
	{
		$matches = 0;
		if (!preg_match_all('#(<[^>]+>)#', $phrasetext, $matches, \PREG_PATTERN_ORDER)) {
			return array();
		}

		$vars = array();

		foreach ($matches[1] as $m) {
			$vars[$m] = $m;
		}

		return $vars;
	}

	private function _readLang($dir_path)
	{
		$phrases = array();

		foreach (array('admin', 'agent', 'user') as $dirname) {
			$dirpath = $dir_path . "/$dirname";
			if (!is_dir($dirpath)) {
				continue;
			}

			$dir = dir($dirpath);
			while (($f = $dir->read()) !== false) {
				if ($f == '.' || $f == '..' || !preg_match('#\.php$#', $f)) continue;
				$filepath = $dirpath . "/$f";

				if (is_file($filepath)) {
					$l = require($filepath);
					if ($l && is_array($l)) {
						$phrases = array_merge($phrases, $l);
					} else {
						echo "Bad file: $filepath\n";
					}
				}
			}
			$dir->close();
		}

		return $phrases;
	}
}