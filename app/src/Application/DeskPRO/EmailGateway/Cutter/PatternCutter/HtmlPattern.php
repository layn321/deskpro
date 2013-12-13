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

namespace Application\DeskPRO\EmailGateway\Cutter\PatternCutter;

class HtmlPattern
{
	/**
	 * @var string
	 */
	protected $pattern;

	/**
	 * @var array
	 */
	protected $tokens;

	/**
	 * Example pattern: div p ?a b span #from:#i /span /b span #.*# br /br b #sent:#i /b #.*# br /br b #to:#i /b #.*# br /br /span /p /div
	 *
	 * @param $pattern
	 */
	public function __construct($pattern)
	{
		$this->pattern = $pattern;
	}


	/**
	 * @return string
	 */
	public function getPattern()
	{
		return $this->pattern;
	}


	/**
	 * Get tokens for the pattern
	 *
	 * @return array
	 */
	public function getTokens()
	{
		if ($this->tokens !== null) {
			return $this->tokens;
		}

		$pattern = " {$this->pattern} ";
		$pattern = str_replace('\\#', '__dp_esc_hash__', $pattern);

		$segments = preg_split('/ (#(?:.*?)#(?:[imsxADUu]*)) /', $pattern, NULL, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);

		$depth = 0;
		foreach ($segments as $segment) {

			$segment = str_replace('__dp_esc_hash__', '\\#', $segment);
			$segment = trim($segment);

			// Match token is a regex string
			if ($segment[0] == '#') {
				$this->tokens[] = array('match', trim($segment));

			// Tag token
			} else {

				// Space on each side for easy anchoring
				$segment = " $segment ";

				// Split up tags into groups of tags, optional tags and closing tags
				$tag_segments = preg_split('/ (\??\\/?(?:[a-zA-Z:]+)) /', $segment, NULL, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);

				$token_bunch = array();
				foreach ($tag_segments as $tag) {
					$tag = trim($tag);
					if (!$tag) {
						continue;
					}

					// Closing tag: This just means :parent for us,
					// its just telling the matcher to go up the tree again
					if ($tag[0] == '/') {
						$this->tokens[] = array('nav', ':close', $depth--);

					// Normal tag, add it to the current tag bunch
					} else {
						$this->tokens[] = array('nav', $tag, $depth++);
					}
				}
			}
		}

		return $this->tokens;
	}
}