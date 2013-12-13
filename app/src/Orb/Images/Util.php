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

namespace Orb\Images;
use Orb\Util\Numbers;

class Util
{
	/**
	 * Create a gradient image useful for repeating backgrounds
	 *
	 * @param $size
	 * @param $start_color
	 * @param $end_color
	 * @param string $direction
	 * @param int $step
	 * @return resource
	 */
	public static function getGradientImage($size, $start_color, $end_color, $direction = 'vertical', $step = 1, $other_size = 1)
	{
		if ($direction == 'vertical') {
			$width = $other_size;
			$height = $size;
		} else {
			$width = $size;
			$height = $other_size;
		}

		if (function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($width, $height);
		} elseif (function_exists('imagecreate')) {
			$im = imagecreate($width, $height);
		}

		if (!is_array($start_color)) {
			$start_color = Numbers::hex2rgb($start_color);
		}
		if (!is_array($end_color)) {
			$end_color = Numbers::hex2rgb($end_color);
		}

		$start_color = array_values($start_color);
		$end_color   = array_values($end_color);

		list($r1,$g1,$b1) = $start_color;
		list($r2,$g2,$b2) = $end_color;

		if ($direction == 'horizontal') {
			$line_numbers  = imagesx($im);
			$line_width    = imagesy($im);
		} else {
			$line_numbers  = imagesy($im);
			$line_width    = imagesx($im);
		}

		$r = $g = $b = '';
		$fill = null;
		for ($i = 0; $i < $line_numbers; $i= $i + $step) {
			$old_r = $r;
			$old_g = $g;
			$old_b = $b;

			$r = ($r2 - $r1 != 0) ? intval($r1 + ($r2 - $r1) * ($i / $line_numbers)): $r1;
			$g = ($g2 - $g1 != 0) ? intval($g1 + ($g2 - $g1) * ($i / $line_numbers)): $g1;
			$b = ($b2 - $b1 != 0) ? intval($b1 + ($b2 - $b1) * ($i / $line_numbers)): $b1;

			if (!$fill || "$old_r,$old_g,$old_b" != "$r,$g,$b") {
				$fill = imagecolorallocate($im, $r, $g, $b);
			}

			if ($direction == 'horizontal') {
				imagefilledrectangle( $im, $i, 0, $i+($step-1), $line_width, $fill);

			} else {
				imagefilledrectangle($im, 0, $i, $line_width, $i+($step-1), $fill);
			}
		}

		return $im;
	}
}