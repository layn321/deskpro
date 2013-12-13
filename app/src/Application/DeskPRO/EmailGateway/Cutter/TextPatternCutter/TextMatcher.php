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

namespace Application\DeskPRO\EmailGateway\Cutter\TextPatternCutter;

class TextMatcher
{
	const CUT_MARK = '<___DP_EMAIL_CUT_MARK___>';

	/**
	 * @var string
	 */
	protected $body;

	/**
	 * @var \Application\DeskPRO\EmailGateway\Cutter\TextPatternCutter\TextPattern
	 */
	protected $pattern;

	/**
	 * @var array
	 */
	protected $pattern_match;

	/**
	 * @var string
	 */
	protected $marked_body;

	/**
	 * @var string
	 */
	protected $mark_id;

	/**
	 * @param string $body
	 * @param string|TextPattern $pattern
	 */
	public function __construct($body, $pattern)
	{
		$this->body = $body;

		if (is_string($pattern)) {
			$pattern = new TextPattern($pattern);
		}

		$this->pattern = $pattern;
	}


	/**
	 * Given a tokenized pattern, process it against the body to find matching results
	 */
	public function process()
	{
		if ($this->marked_body !== null) {
			return;
		}

		$this->pattern_match = false;
		$this->marked_body   = $this->body;

		if (preg_match($this->pattern->getPattern(), $this->body)) {
			$this->marked_body = preg_replace($this->pattern->getPattern(), self::CUT_MARK . '$0', $this->body);
			$this->pattern_match = true;
		}
	}


	/**
	 * Does the pattern match?
	 *
	 * @return bool
	 */
	public function isMatch()
	{
		$this->process();
		if ($this->pattern_match) {
			return true;
		}

		return false;
	}


	/**
	 * Process the pattern and if it matches, mark the beginning of the cut areas with self::CUT_MARK
	 *
	 * @return string
	 */
	public function getMarkedDocument()
	{
		$this->process();
		return $this->marked_body;
	}


	/**
	 * Cut at the first cut mark
	 *
	 * @param string $mark_string
	 * @return string
	 */
	public function getCutBody()
	{
		$body = $this->getMarkedDocument();

		$pos = strpos($body, self::CUT_MARK);
		if ($pos === false) {
			return $body;
		}

		return substr($body, 0, $pos);
	}
}