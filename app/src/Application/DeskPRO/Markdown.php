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
 * @subpackage Util
 */

namespace Application\DeskPRO;
use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

/**
 * Transform markdown-formatted text into html
 */
class Markdown extends \Markdown_Parser
{
	protected $attach_tokens = array();

	/**
	 * Format the supplied markdown string to HTML
	 * 
	 * @static
	 * @param  $string
	 * @return string
	 */
	public static function format($string)
	{
		$tr = new self();
		return $tr->transform($string);
	}

	public function transform($text)
	{
		$this->attach_tokens = array();

		// Get rid of more tokens, they'd've been handled elsewhere
		$text = str_replace('![more]', '', $text);

		$m = null;
		if (preg_match_all('#!\[attach(.*?)\]#', $text, $m)) {
			foreach ($m[0] as $match) {
				$token = ":attach-token-" . md5(microtime() . Util::requestUniqueId()) . ":";
				$this->attach_tokens[$token] = $match;
			}
		}

		$text = parent::transform($text);

		if ($this->attach_tokens) {
			$text = $this->processAttachTokens($text, $this->attach_tokens);
		}

		$this->attach_tokens = array();

		return $text;
	}

	public function processAttachTokens($text, array $attach_tokens)
	{
		foreach ($attach_tokens as $token => $attach_code) {
			$attach_html = $this->getAttachHtml($attach_code);
			$text = str_replace($token, $attach_html, $text);
		}

		return $text;
	}

	public function getAttachHtml($attach_code)
	{
		$blob_id = Strings::extractRegexMatch('#!\[attach:(\d+)#', $attach_code, 1);
		if (!$blob_id) {
			return '';
		}

		/** @var $blob \Application\DeskPRO\Entity\Blob */
		$blob = App::findEntity('DeskPRO:Blob', $blob_id);
		if (!$blob) {
			return '';
		}

		if (strpos('url]', $attach_code) !== null) {
			return $blob->getDownloadUrl();
		} elseif (strpos('image]', $attach_code) !== null) {
			return '<img src="'.$blob->getDownloadUrl().'" alt="" class="blob blob-'.$blob['id'].'" border="0" />';
		}

		return '';
	}
}
