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
 * Orb
 *
 * @package Orb
 * @category Input
 */

namespace Orb\Input\Cleaner\CleanerPlugin;

use Orb\Input\Cleaner\Cleaner;
use Orb\Util\Strings;

/**
 * This re-defines the basic string types in Basic.php to filter out xss
 * from all input.
 *
 * This basic cleaner is based on CI_Security::xss_clean() in CodeIgniter
 * @link http://codeigniter.com/
 */
class BasicXss implements CleanerPlugin
{
	protected $_never_allowed_str = array(
		'document.cookie'	=> '[removed]',
		'document.write'	=> '[removed]',
		'.parentNode'		=> '[removed]',
		'.innerHTML'		=> '[removed]',
		'window.location'	=> '[removed]',
		'-moz-binding'		=> '[removed]',
		'<!--'				=> '&lt;!--',
		'-->'				=> '--&gt;',
		'<![CDATA['			=> '&lt;![CDATA[',
		'<comment>'			=> '&lt;comment&gt;'
	);

	protected $_never_allowed_regex = array(
		"javascript\s*:"			=> '[removed]',
		"expression\s*(\(|&\#40;)"	=> '[removed]',
		"vbscript\s*:"				=> '[removed]',
		"Redirect\s+302"			=> '[removed]'
	);

	public function getCleanerId()
	{
		return 'basic_xss';
	}

	public function getCleanerTypes()
	{
		// These override the basic ones
		return array(
			'str',
			'string',
			'str_notrim',
		);
	}

	public function cleanValue($value, $type, array $options, Cleaner $cleaner)
	{
		$value = $cleaner->getCleaner('basic')->cleanValue($value, $type, $options, $cleaner);

		if (isset($options['noclean']) && $options['noclean']) {
			return $value;
		}

		if (!$value) {
			return $value;
		}

		if (strpos($value, '<') !== false || strpos($value, '"') !== false || strpos($value, "'") !== false) {
			$self = $this;

			$value = str_replace("\t", '    ', $value);

			$words = array(
				'javascript', 'expression', 'vbscript', 'script',
				'applet', 'alert', 'document', 'write', 'cookie', 'window'
			);

			foreach ($words as $word) {
				$temp = '';

				for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++) {
					$temp .= substr($word, $i, 1)."\s*";
				}

				$value = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', function ($m) {
					return preg_replace('/\s+/s', '', $m[1]).$m[2];
				}, $value);
			}

			do {
				$original = $value;

				if (preg_match("/<a/i", $value)) {
					$value = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", function ($match) use ($self) {
						$attributes = $self->filterAttributes(str_replace(array('<', '>'), '', $match[1]));
						return str_replace($match[1], preg_replace("#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
					}, $value);
				}

				if (preg_match("/<img/i", $value)) {
					$value = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", function ($match) use ($self) {
						$attributes = $self->filterAttributes(str_replace(array('<', '>'), '', $match[1]));
						return str_replace($match[1], preg_replace("#src=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
					}, $value);
				}

				if (preg_match("/script/i", $value) OR preg_match("/xss/i", $value)) {
					$value = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '', $value);
				}
			} while($original != $value);
			unset($original);

			$naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
			$value = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', function($matches) {
				// encode opening brace
				$value = '&lt;'.$matches[1].$matches[2].$matches[3];

				// encode captured opening or closing brace to prevent recursive vectors
				$value .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);

				return $value;
			}, $value);

			// All javascript event handlers (e.g. onload, onclick, onmouseover), style, and xmlns
			$evil_attributes = array('on\w*', 'style', 'xmlns', 'formaction');

			do {
				$count = 0;
				$attribs = array();

				// find occurrences of illegal attribute strings without quotes
				preg_match_all("/(".implode('|', $evil_attributes).")\s*=\s*([^\s]*)/is",  $value, $matches, PREG_SET_ORDER);

				foreach ($matches as $attr)
				{
					$attribs[] = preg_quote($attr[0], '/');
				}

				// find occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
				preg_match_all("/(".implode('|', $evil_attributes).")\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is",  $value, $matches, PREG_SET_ORDER);

				foreach ($matches as $attr)
				{
					$attribs[] = preg_quote($attr[0], '/');
				}

				// replace illegal attribute strings that are inside an html tag
				if (count($attribs) > 0)
				{
					$attribs_chunks = array_chunk($attribs, 2);
					foreach ($attribs_chunks as $attribs_chunk) {
						$value = preg_replace("/<(\/?[^><]+?)([^A-Za-z\-])(".implode('|', $attribs_chunk).")([\s><])([><]*)/i", '<$1$2$4$5', $value, -1, $count);
					}
				}

			} while ($count);

			// Last resort
			foreach ($this->_never_allowed_str as $key => $val) {
				$value = str_replace($key, $val, $value);
			}

			foreach ($this->_never_allowed_regex as $key => $val) {
				$value = preg_replace("#".$key."#i", $val, $value);
			}
		}

		return $value;
	}

	public function filterAttributes($value)
	{
		$out = '';

		if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $value, $matches))
		{
			foreach ($matches[0] as $match)
			{
				$out .= preg_replace("#/\*.*?\*/#s", '', $match);
			}
		}

		return $out;
	}
}
