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

namespace Application\DeskPRO\CacheInvalidator;

use Orb\Util\Strings;
use Application\DeskPRO\App;

class LanguageJsCache
{
	protected $_cache_dir = '';

	public function __construct($cache_dir = null)
	{
		if (!$cache_dir) {
			$cache_dir = dp_get_tmp_dir();
		}

		$this->_cache_dir = $cache_dir;
	}

	public function invalidateAll()
	{
		$cache_dir = $this->_cache_dir;
		if (is_dir($cache_dir)) {
			$res = @glob("$cache_dir/agent-lang-*.cache");
			if ($res) {
				foreach ($res AS $file) {
					@unlink($file);
				}
			}

			$res = @glob("$cache_dir/user-lang-*.cache");
			if ($res) {
				foreach ($res AS $file) {
					@unlink($file);
				}
			}
		}
	}

	public function invalidateLanguage($id)
	{
		$cache_dir = $this->_cache_dir;
		if (is_dir($cache_dir)) {
			@unlink("$cache_dir/agent-lang-$id.cache");
			@unlink("$cache_dir/user-lang-$id.cache");
		}
	}
}
