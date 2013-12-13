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

namespace Application\DeskPRO;

/**
 * Helper that just checks for the current installs capabilities.
 */
class InstanceAbility
{
	public function canUseSsl()
	{
		static $has_ssl;

		if ($has_ssl === null) {
			$has_ssl = extension_loaded('openssl');
		}

		return $has_ssl;
	}

	public function canUseFacebookAuth()
	{
		return $this->canUseSsl();
	}

	public function canUseTwitterAuth()
	{
		return $this->canUseSsl();
	}

	public function canUseGoogleAuth()
	{
		return $this->canUseSsl();
	}

	public function canUseSecurePop3()
	{
		return $this->canUseSsl();
	}

	public function canUseSecureSmtp()
	{
		return $this->canUseSsl();
	}

	public function canUseGoogleApps()
	{
		return $this->canUseSsl();
	}

	public function isWindows()
	{
		if (strpos(strtoupper(PHP_OS), 'WIN') === 0) {
			return true;
		}

		return false;
	}

	public function isIis()
	{
		if ($this->isWindows() && strpos(strtolower(@$_SERVER['SERVER_SOFTWARE'] ?: ''), "iis") !== false) {
			return true;
		}

		return false;
	}

	public function __call($method, array $args = array())
	{
		return false;
	}
}