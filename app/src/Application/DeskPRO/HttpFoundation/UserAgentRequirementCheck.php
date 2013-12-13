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
 * @subpackage
 */

namespace Application\DeskPRO\HttpFoundation;

use \Browser;

class UserAgentRequirementCheck
{
	public static function passAgentInterface(Browser $browser = null)
	{
		if (!$browser) {
			$browser = new \Browser();
		}

		// Check for known browsers
		if ($browser->getBrowser() == \Browser::BROWSER_FIREFOX && $browser->getVersion() < 4) {
			return false;
		} elseif ($browser->getBrowser() == \Browser::BROWSER_CHROME && $browser->getVersion() < 14) {
			return false;
		} elseif ($browser->getBrowser() == \Browser::BROWSER_SAFARI && $browser->getVersion() < 5) {
			return false;
		} elseif ($browser->getBrowser() == \Browser::BROWSER_OPERA && $browser->getVersion() < 11) {
			return false;
		} elseif ($browser->getBrowser() == \Browser::BROWSER_IE && $browser->getVersion() < 8) {
			if (!$browser->isChromeFrame()) {
				return false;
			}
		}

		// Unknown browsers we'll err on the lenient side and assume
		// they work, or that the users know better
		return true;
	}

	public static function getInterfaceWarnings(Browser $browser = null, $interface = null)
	{
		if (!$browser) {
			$browser = new \Browser();
		}

		if ($interface === null) {
			$interface = DP_INTERFACE;
		}

		$warnings = array();

		return $warnings;
	}

	public static function passAdminInterface()
	{
		return self::passAgentInterface();
	}
}