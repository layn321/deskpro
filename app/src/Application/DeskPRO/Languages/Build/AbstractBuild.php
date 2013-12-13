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

namespace Application\DeskPRO\Languages\Build;

use Application\DeskPRO\Languages\LangPackInfo;
use Orb\Log\Logger;
use Symfony\Component\HttpKernel\Util\Filesystem as FilesystemUtil;

abstract class AbstractBuild
{
	/**
	 * @var \Application\DeskPRO\Languages\LangPackInfo
	 */
	private $langinfo;

	/**
	 * @var \Orb\Log\Logger
	 */
	private $logger;

	/**
	 * @var array
	 */
	private $diff_track = array();

	/**
	 * @param string $id
	 * @return array
	 */
	abstract public function getCategoryWords($id, $section, $category);

	/**
	 * @param string $section
	 * @param string $category
	 * @param string $source_file
	 * @return array
	 */
	abstract public function updateSourcePhrases($section, $category, $source_file = null);


	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		if (!$this->logger) {
			$this->logger = new \Orb\Log\Logger();
		}

		return $this->logger;
	}


	/**
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @return LangPackInfo
	 */
	public function getLangPackInfo()
	{
		if (!$this->langinfo) {
			$this->langinfo = new LangPackInfo();
		}

		return $this->langinfo;
	}


	/**
	 * @param LangPackInfo $langinfo
	 */
	public function setLangPackInfo(LangPackInfo $langinfo)
	{
		$this->langinfo = $langinfo;
	}


	/**
	 * Returns a diff of changed, added and removed phrase IDs
	 *
	 * @param string $id
	 * @param string $section
	 * @param string $category
	 * @return array
	 */
	public function writeLangFile($id, $section, $category, array $phrases)
	{
		$dir = $this->getLangPackInfo()->getLangDir() . '/' . $id . '/' . $section;
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}

		$file = $dir . '/' . $category . '.php';

		if (file_exists($file)) {
			$exist = include($file);
			unlink($file);
		} else {
			$exist = array();
		}

		$diff = array(
			'changed' => array(),
			'added'   => array(),
			'removed' => array()
		);

		foreach ($phrases as $phrase_id => $string) {

			if (!isset($exist[$phrase_id])) {
				$diff['added'][] = $phrase_id;
			} elseif (trim($exist[$phrase_id]) != trim($string)) {
				$diff['changed'][] = $phrase_id;
			}
		}

		foreach ($exist as $phrase_id => $string) {
			if (!isset($phrases[$phrase_id])) {
				$diff['removed'][] = $phrase_id;
			}
		}

		ksort($phrases, \SORT_STRING);

		$php = array("<?php return array(\n");
		foreach ($phrases as $phrase_id => $string) {
			$php[] = sprintf("\t%-70s => %s,\n", "'$phrase_id'", var_export($string, true));
		}

		$php[] = ");\n";

		$php = implode('', $php);

		file_put_contents($file, $php);
		chmod($file, 0644);

		$this->getLogger()->logInfo('Wrote file: ' . $file);

		return $diff;
	}


	/**
	 * Build all languages
	 *
	 * @return array The diff of every lang
	 */
	public function buildAll()
	{
		$diff = array();

		foreach ($this->getLangPackInfo()->getLangIds() as $id) {
			if (!$this->getLangPackInfo()->getLangInfo($id, 'is_managed')) {
				continue;
			}

			$diff[$id] = $this->buildLanguage($id);
		}

		return $diff;
	}


	/**
	 * Build a language
	 *
	 * @param string $id The standard DeskPRO ID for the language
	 * @return array The diff
	 */
	public function buildLanguage($id)
	{
		$diff = array(
			'changed' => array(),
			'added'   => array(),
			'removed' => array()
		);

		foreach ($this->getLangPackInfo()->getDefaultSections() as $section) {
			foreach ($this->getLangPackInfo()->getDefaultCategories($section) as $category) {
				$words = $this->getCategoryWords($id, $section, $category);
				if ($words) {
					$cat_diff = $this->writeLangFile($id, $section, $category, $words);
					$diff['changed'] = array_merge($diff['changed'], $cat_diff['changed']);
					$diff['added']   = array_merge($diff['added'],   $cat_diff['added']);
					$diff['removed'] = array_merge($diff['removed'], $cat_diff['removed']);
				}
			}
		}

		return $diff;
	}


	/**
	 * Updates all sources for all sections and categories
	 */
	public function updateAllSources()
	{
		$sections = $this->getLangPackInfo()->getDefaultSections();

		foreach ($sections as $section) {
			foreach ($this->getLangPackInfo()->getDefaultCategories($section) as $category) {
				$this->updateSourcePhrases($section, $category);
			}
		}
	}


	/**
	 * Clears out a lang from the filesystem.
	 *
	 * @param string $id
	 */
	public function clearLang($id)
	{
		$dir = $this->getLangPackInfo()->getLangDir() . '/' . $id;

		// Nothing to do
		if (!is_dir($dir)) {
			return;
		}

		$fileutil = new FilesystemUtil();
		$fileutil->remove($dir);
	}
}