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

use Orb\Util\Strings;

class HtmlMatcher
{
	const CUT_MARK = '<!-- DP_EMAIL_CUT_MARK -->';

	/**
	 * @var string
	 */
	protected $body;

	/**
	 * @var \Application\DeskPRO\EmailGateway\Cutter\PatternCutter\HtmlPattern
	 */
	protected $pattern;

	/**
	 * @var \QueryPath\DOMQuery
	 */
	protected $qp;

	/**
	 * @var array
	 */
	protected $pattern_match;

	/**
	 * @var int
	 */
	protected $pattern_match_id;

	/**
	 * @var string
	 */
	protected $marked_body;

	/**
	 * @var array
	 */
	protected $root_state;


	/**
	 * @param string $body
	 * @param string|HtmlPattern $pattern
	 */
	public function __construct($body, $pattern)
	{
		$this->body = $body;

		if (is_string($pattern)) {
			$pattern = new HtmlPattern($pattern);
		}

		$this->pattern = $pattern;
	}


	/**
	 * Given a tokenized pattern, process it against the body to find matching results
	 *
	 * @return array
	 */
	public function process()
	{
		if ($this->pattern_match !== null) {
			return $this->pattern_match;
		}

		$tokens = $this->pattern->getTokens();

		$first_token = array_shift($tokens);

		// If theres only one token and its a regex check,
		// then this pattern is a simple string pattern with no dom traversal
		if ($first_token[0] == 'match' && !$tokens) {
			$m = null;

			$try = $this->body;
			if (!preg_match('/^#\^/', $first_token[1]) && !preg_match('/\$#[a-zA-Z]*$/', $first_token[1])) {
				// Get rid of new lines that may affect the cutter.
				// (Doesnt matter with HTML emails anyway)
				// But only if we arent anchoring the pattern, where newlines matter
				$try = str_replace(array("\r\n", "\n"), " ", $this->body);
			}

			if (preg_match($first_token[1], $this->body, $m)) {
				$this->body = $try;
				$this->marked_body = str_replace($m[0], self::CUT_MARK, $this->body);

				$this->pattern_match = 'SIMPLE_MATCH';
				return 'SIMPLE_MATCH';
			} else {
				return null;
			}
		}

		$roots = array();

		try {
			$this->getQpBranch()->find($first_token[1])->each(function($i, $m) use (&$roots) {
				$roots[] = \QueryPath::with($m);
			});
		} catch (\QueryPath\Exception $e) {
			// This can happen if the document has no tags
			// (eg they were all stripped out, it was plaintext without a root etc)
			// -> So obviously it's a no match if there are no tags to parse
			return null;
		}


		foreach ($roots as $id => $root) {
			$use_tokens = $tokens;

			$branch = $root->branch()->first();
			$this->root_state[$id] = array(
				'closed' => false,
				'mark_spot' => null,
				'mark_pattern' => null
			);


			while ($use_tokens) {
				$branch = $this->consumeNavigates($id, $branch, $use_tokens);
				if (!$branch) {
					break;
				}
				$branch = $this->consumeMatches($id, $branch, $use_tokens);
				if (!$branch) {
					break;
				}
			}

			if ($branch) {
				$this->pattern_match = $root;
				$this->pattern_match_id = $id;
				return $this->pattern_match;
			}
		}

		return null;
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
	 * @param string|HtmlPattern $pattern
	 * @param string $mark_string
	 * @return string
	 */
	public function getMarkedDocument()
	{
		if ($this->marked_body) {
			return $this->marked_body;
		}

		$match = $this->process();
		if (!$match) {
			$this->marked_body = $this->body;
			return $this->marked_body;
		}

		if ($match == 'SIMPLE_MATCH') {
			return $this->marked_body;
		}

		if (!$this->root_state[$this->pattern_match_id]['mark_spot']) {
			$match->before(self::CUT_MARK);
		}

		ob_start();
		$this->getQp()->writeXHTML();
		$this->marked_body = ob_get_clean();

		// We muck around adding <?xml declaration to QueryPath to force PHP's DOMDocument
		// into UTF mode, so now strip those out here
		$this->marked_body = trim($this->marked_body);
		$this->marked_body = preg_replace('#^<\?xml.*?\?>\s*#', '', $this->marked_body);
		$this->marked_body = preg_replace('#<\?xml.*?\?\?>\s*#', '', $this->marked_body);

		$wrap_pos = strpos($this->marked_body, 'DP_MARK_EL');
		if ($wrap_pos) {
			$piece1 = substr($this->marked_body, 0, $wrap_pos);
			$piece2 = substr($this->marked_body, $wrap_pos);

			if (preg_match($this->root_state[$this->pattern_match_id]['mark_pattern'], $piece1)) {
				$piece2 = preg_replace($this->root_state[$this->pattern_match_id]['mark_pattern'], self::CUT_MARK . '$0', $piece2, 1);
			} else {
				$piece2 = self::CUT_MARK . $piece2;
			}

			$this->marked_body = $piece1 . $piece2;
		}

		$this->marked_body = trim($this->marked_body);
		if (!$this->marked_body) {
			return $this->body;
		}

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


	/**
	 * Consume  all navigate finds and return a new array of branches that match
	 *
	 * @param array $results
	 * @param array $tokens
	 * @return array
	 */
	public function consumeNavigates($id, $branch, array &$tokens)
	{
		$current = $branch->branch()->first();

		while ($token = array_shift($tokens)) {

			// Next token isnt a match
			if ($token[0] != 'nav') {
				array_unshift($tokens, $token);
				break;
			}

			$sel = $token[1];
			$depth = $token[2];

			if ($sel == ':close') {
				if ($this->root_state[$id]['closed']) {
					$current->parent();
				}
				$this->root_state[$id]['closed'] = true;
			} else {
				if ($this->root_state[$id]['closed']) {
					$current->next();
					if (!$current->length || $current->get(0)->tagName != $sel) {
						return null;
					}
					$this->root_state[$id]['closed'] = false;
				} else {
					$current->find($sel)->first();
					if (!$current->length) {
						return null;
					}
				}
			}
		}

		return $current;
	}


	/**
	 * Process all match requirements on the result set and return a new array of branches that match.
	 *
	 * @param array $results
	 * @param array $tokens
	 * @return array
	 */
	public function consumeMatches($id, $branch, array &$tokens)
	{
		while ($token = array_shift($tokens)) {

			// Next token isnt a match
			if ($token[0] != 'match') {
				array_unshift($tokens, $token);
				break;
			}

			// Get rid of token type on the array
			array_shift($token);

			$text = $branch->text();

			$m = null;
			if (!preg_match($token[0], $text, $m)) {

				// Check entire contents
				$html = $branch->innerHTML();
				$text = str_replace(array('<br />', '<br/>', '<br>'), "\n", $html);
				$text = strip_tags($text);
				$text = trim($text);

				if (!preg_match($token[0], $text, $m)) {
					return null;
				}
			}

			if (!$this->root_state[$id]['mark_spot']) {
				$this->root_state[$id]['mark_spot'] = $m[0];
				$this->root_state[$id]['mark_pattern'] = $token[0];
				$branch->addClass('DP_MARK_EL');
			}
		}

		return $branch;
	}


	/**
	 * Used internally by the PatternCutter to fetch the qp and set it on the next pattern when
	 * we know a pattern didnt match and we havent mutated the collection, saves
	 * from re-creating the doc.
	 *
	 * @internal
	 * @param \QueryPath\DOMQuery $qp
	 */
	public function _setQp(\QueryPath\DOMQuery $qp)
	{
		$this->qp = $qp;
	}


	/**
	 * @return \QueryPath\DOMQuery
	 */
	public function getQp()
	{
		if ($this->qp) {
			return $this->qp;
		}

		$this->qp = \QueryPath::withHTML($this->body, 'body', array('convert_to_encoding' => null));

		return $this->qp;
	}


	/**
	 * @return \QueryPath\DOMQuery
	 */
	public function getQpBranch()
	{
		return $this->getQp()->branch();
	}
}