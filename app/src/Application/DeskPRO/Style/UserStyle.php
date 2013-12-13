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
 * @subpackage Templating
 */

namespace Application\DeskPRO\Style;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Style;

class UserStyle
{
	protected $raw;

	public function __construct($raw_css)
	{
		$this->raw = $raw_css;
	}


	/**
	 * Read CSS to get embedded vars and their default values as k=>v
	 *
	 * @return array
	 */
	public function getVars()
	{
		// @my_var(default)
		// @my_var
		preg_match_all('#@([A-Za-z0-9_\-]+)(\[(.*?)\])?#', $this->raw, $matches, PREG_SET_ORDER);

		$vars = array();

		foreach ($matches as $m) {

			if ($m[1] == 'HEX_TO_RGB') continue;

			// Set value (may overwrite existing if it appeared later in the file
			if (isset($m[3]) && $m[3]) {
				$vars[$m[1]] = $m[3];
			} else {
				// Overwrite a blank if it has (parenthesis)
				if (isset($m[2]) && $m[2]) {
					$vars[$m[1]] = '';
				// And finally set it to blank if it appears without parens and doesnt exist
				} elseif (!isset($vars[$m[1]])) {
					$vars[$m[1]] = '';
				}
			}
		}

		return $vars;
	}


	/**
	 * @param array $vars
	 */
	public function compileCss(array $vars = array())
	{
		$vars = array_merge($this->getVars(), $vars);


		$css = str_replace('@HEX_TO_RGB(', '__DP_HEX_TO_RGB(', $this->raw);

		$css = preg_replace_callback('#@([A-Za-z0-9_\-]+)(\[(.*?)\])?#', function($m) use ($vars) {

			if (!isset($vars[$m[1]])) {
				return '';
			}

			return $vars[$m[1]];
		}, $css);

		$self = $this;
		$css = preg_replace_callback('#__DP_HEX_TO_RGB\((.*?)\)#', function($m) use ($vars, $self) {
			$color = rtrim($m[1], '#');
			return $self->hex2RGB($color, ',');
		}, $css);

		// Fix url to static
		$css = str_replace('url(../../', 'url(../../web/', $css);

		// Strip comments
		$css = preg_replace('#/\*[^*]*.*?\*/#s', '', $css);

		// Superflous whitespace
		$css = preg_replace("#\n{2,}#", "\n", $css);
		$css = preg_replace("#\s*\{\s*#", "{", $css);
		$css = preg_replace("#\s*\;\s*#", ";", $css);
		$css = preg_replace("#\s*\:\s*#", ":", $css);

		return $css;
	}

	public function hex2RGB($hex, $return_string = ',')
	{
		$hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex);
		$rgb = array();
		if (strlen($hex) == 6) {
			$color_val = hexdec($hex);
			$rgb['red'] = 0xFF & ($color_val >> 0x10);
			$rgb['green'] = 0xFF & ($color_val >> 0x8);
			$rgb['blue'] = 0xFF & $color_val;
		} elseif (strlen($hex) == 3) {
			$rgb['red'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
			$rgb['green'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
			$rgb['blue'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
		} else {
			return false;
		}

		return $return_string ? implode($return_string, $rgb) : $rgb; // returns the rgb string or the associative array
	}

	public function lightenHex($orig_color, $fraction_denom = 2)
	{
		$highest_val = hexdec('FF');
		$r = hexdec(substr($orig_color,0,2));
		$r = ($highest_val-$r)/$fraction_denom + $r;

		$g = hexdec(substr($orig_color,2,2));
		$g = ($highest_val-$g)/$fraction_denom + $g;

		$b = hexdec(substr($orig_color,4,2));
		$b = ($highest_val-$b)/$fraction_denom + $b;

		return dechex($r) . dechex($g) . dechex($b);
	}
}
