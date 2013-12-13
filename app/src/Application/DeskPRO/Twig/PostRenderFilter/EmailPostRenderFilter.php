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

namespace Application\DeskPRO\Twig\PostRenderFilter;

use Orb\Util\Strings;

class EmailPostRenderFilter extends AbstractPostRenderFilter
{
	public function process($name, $code)
	{
		$m = null;
		if (!preg_match_all('#<style[^>]*>(.*?)</style>#s', $code, $m, \PREG_PATTERN_ORDER)) {
			return $code;
		}

		$orig_code = $code;

		// Separate out subject
		$parts = explode('___DP___SUBJECT___SEP___', $code, 2);
		$subj = null;
		if (count($parts) == 2) {
			$subj = trim($parts[0]);
			$code = trim($parts[1]);
		}

		$css = implode("\n", $m[1]);
		foreach ($m[0] as $find) {
			$code = str_replace($find, '', $code);
		}

		$code = Strings::preDomDocument($code);
		$emog = new \Emogrifier($code, $css);
		$code = $emog->emogrify();
		$code = Strings::postDomDocument($code);

		if (!$code) {
			return $orig_code;
		}

		if ($subj) {
			$code = $subj . '___DP___SUBJECT___SEP___' . $code;
		}

		if (strpos($name,'DeskPRO:emails_user:') === 0) {
			$code = str_replace('DP_TOP_MARK', 'DP_TOP_MARK DP_USER_EMAIL', $code);
		}

		return $code;
	}
}
