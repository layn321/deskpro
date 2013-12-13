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

namespace Application\DeskPRO\Languages;

use Application\DeskPRO\DependencyInjection\SystemServices\LanguageDataService;
use \Text_LanguageDetect;

class Detect
{
	/**
	 * @var \Application\DeskPRO\DependencyInjection\SystemServices\LanguageDataService
	 */
	protected $lang_data;

	/**
	 * @var \Text_LanguageDetect
	 */
	protected $lang_detect;

	/**
	 * @var array
	 */
	protected $detectable_langs;

	/**
	 * @var bool
	 */
	protected $has_jpn = false;

	/**
	 * @var array
	 */
	protected $jpn_data;

	/**
	 * @var int
	 */
	protected $jpn_common_word_threshold = 1;

	/**
	 * @param \Application\DeskPRO\DependencyInjection\SystemServices\LanguageDataService $lang_data
	 */
	public function __construct(LanguageDataService $lang_data)
	{
		$this->lang_data = $lang_data;
	}


	/**
	 * @param string $string
	 * @return string
	 */
	public function detectLanguageCode($string)
	{
		$d = $this->getLanguageDetect();

		if (!$this->detectable_langs) {
			return null;
		}

		$string = strip_tags($string);
		$detected_code = null;

		if (!$detected_code && $this->has_jpn) {
			$count = 0;
			foreach ($this->getCommonJapaneseWords() as $word) {
				if (strpos($string, $word) !== false) {
					$count++;

					if ($count >= $this->jpn_common_word_threshold) {
						break;
					}
				}
			}

			if ($count >= $this->jpn_common_word_threshold) {
				$detected_code = 'jpn';
			}
		}

		if (!$detected_code) {
			$detected_code = $d->detectSimple($string);
		}

		return $detected_code ? $detected_code : null;
	}


	/**
	 * @param string $string
	 * @return \Application\DeskPRO\Entity\Language
	 */
	public function detectLanguage($string)
	{
		$code = $this->detectLanguageCode($string);
		if (!$code) {
			return null;
		}

		$lang = $this->lang_data->findLangCode($code);

		return $lang;
	}


	/**
	 * @return \Text_LanguageDetect
	 */
	public function getLanguageDetect()
	{
		if ($this->lang_detect !== null) {
			return $this->lang_detect;
		}

		$this->lang_detect = new \Text_LanguageDetect();
		$this->lang_detect->setNameMode(3);

		$this->detectable_langs = array();
		foreach ($this->lang_data->getLangCodes() as $code) {
			if ($this->lang_detect->languageExists($code)) {
				$this->detectable_langs[] = $code;
			} elseif ($code == 'jpn') {
				$this->has_jpn = true;
			}
		}

		$this->lang_detect->omitLanguages($this->detectable_langs, true);

		return $this->lang_detect;
	}


	/**
	 * @return string[]
	 */
	public function getDetectableLanguages()
	{
		$this->getLanguageDetect();

		if ($this->has_jpn) {
			return array_merge($this->detectable_langs, array('jpn'));
		}

		return $this->detectable_langs;
	}


	/**
	 * @return array
	 */
	public function getCommonJapaneseWords()
	{
		if ($this->jpn_data) {
			return $this->jpn_data;
		}

		$this->jpn_data = require(__DIR__.'/data/japanese-common-words.php');
		return $this->jpn_data;
	}


	/**
	 * @param int $threshold
	 */
	public function setCommonJapaneseWordThreshold($threshold = 3)
	{
		$this->jpn_common_word_threshold = $threshold;
	}
}