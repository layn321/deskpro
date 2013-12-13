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

namespace Application\DeskPRO\EmailGateway\Cutter;

use Application\DeskPRO\EmailGateway\Cutter\TextPatternCutter\TextPattern;
use Application\DeskPRO\EmailGateway\Cutter\TextPatternCutter\TextMatcher;
use Application\DeskPRO\EmailGateway\Cutter\Def\QuoteDef;

class TextPatternCutter implements QuoteDef
{
	/**
	 * @var \Application\DeskPRO\EmailGateway\Cutter\TextPatternCutter\TextPattern[]
	 */
	protected $patterns = array();

	/**
	 * @var TextPatternCutter\TextPattern[]
	 */
	protected $matched_patterns;

	/**
	 * @var array
	 */
	protected $translate_map;


	/**
	 * @param array $translate_map
	 */
	public function setTranslateMap(array $translate_map)
	{
		$this->translate_map = $translate_map;
	}


	/**
	 * @return array
	 */
	public function getTranslateMap()
	{
		if (!$this->translate_map) {
			$this->translate_map = new \Application\DeskPRO\Config\UserFileConfig('cut-patterns-translate');
			$this->translate_map = $this->translate_map->all();
		}

		return $this->translate_map;
	}


	/**
	 * @param \Application\DeskPRO\EmailGateway\Cutter\TextPatternCutter\TextPattern|string $pattern
	 */
	public function addPattern($pattern)
	{
		if (is_string($pattern)) {
			if (strpos($pattern, 'lang:') === 0) {
				$translate_map = $this->getTranslateMap();

				$orig_pattern = preg_replace('#^lang:\s*#', '', $pattern);

				foreach ($translate_map as $set) {
					$pattern = $orig_pattern;
					foreach ($set as $f => $r) {
						$pattern = str_replace($f, $r, $pattern);
					}

					$pattern = new TextPattern($pattern);
					$this->patterns[] = $pattern;
				}
			} else {
				$pattern = new TextPattern($pattern);
				$this->patterns[] = $pattern;
			}
		} else {
			$this->patterns[] = $pattern;
		}
	}


	/**
	 * Add an array of patterns
	 *
	 * @param array $patterns
	 */
	public function addPatterns(array $patterns)
	{
		foreach ($patterns as $pattern) {
			$this->addPattern($pattern);
		}
	}


	/**
	 * Cut out the quote block
	 *
	 * @param string $body
	 * @param bool $is_html
	 * @return string
	 */
	public function cutQuoteBlock($body, $is_html = false)
	{
		if ($is_html) {
			return $body;
		}

		foreach ($this->patterns as $pattern) {
			$matcher = new TextMatcher($body, $pattern);
			if ($matcher->isMatch()) {
				$this->matched_patterns[] = $pattern;
				$body = $matcher->getMarkedDocument();
			}
		}

		$pos = strpos($body, TextMatcher::CUT_MARK);
		if ($pos !== false) {
			$body = trim(substr($body, 0, $pos));
		}

		return $body;
	}


	/**
	 * @param $body
	 * @return TextPatternCutter\TextPattern|null
	 */
	public function findMatchingMatcher($body)
	{
		$last_qp = null;
		foreach ($this->patterns as $pattern) {
			$matcher = new TextMatcher($body, $pattern);
			if ($matcher->isMatch()) {
				$this->matched_patterns[] = $pattern;
				return $matcher;
			}
		}

		return null;
	}


	/**
	 * @return TextPatternCutter\TextPattern|null
	 */
	public function getMatchedPatterns()
	{
		return $this->matched_patterns;
	}
}