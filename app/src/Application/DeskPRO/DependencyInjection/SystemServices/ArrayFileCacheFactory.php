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
 * @category DependencyInjection
 */

namespace Application\DeskPRO\DependencyInjection\SystemServices;

use Application\DeskPRO\DependencyInjection\DeskproContainer;

class ArrayFileCacheFactory
{
	public static function create($cache_name)
	{
		if (!function_exists('dp_get_tmp_dir') || !is_writable(dp_get_tmp_dir())) {
			return self::createNull();
		}

		if (isset($GLOBALS['DP_CONFIG']['disable_caches']) && in_array($cache_name, $GLOBALS['DP_CONFIG']['disable_caches'])) {
			return self::createNull();
		}

		$cache_name = preg_replace('#[^a-zA-Z0-9\-_\.]#', '_', $cache_name);

		if ($cache_name == 'dql' && defined('DPC_IS_CLOUD') && DPC_IS_CLOUD) {
			$path = DP_ROOT.'/sys/cache/' . $cache_name . '.cache';
		} else {
			$path = dp_get_tmp_dir() . DIRECTORY_SEPARATOR . $cache_name . '.cache';
		}

		$cache = new \Orb\Doctrine\Common\Cache\ArrayFileCache($path);

		if ($cache_name == 'dql') {
			// Filters out queries with 'IN' components that can pollute the cache
			$cache->setFilter(function($data) {
				/** @var $data \Doctrine\ORM\Query\ParserResult */
				$s = $data->getSqlExecutor()->getSqlStatements();
				if (is_string($s)) {
					// Hard-coded IDs
					if (preg_match('#IN \(\d#', $s)) {
						return false;
					// More than 10 segments
					} elseif (preg_match('#IN \([?, ]{10,}#', $s)) {
						return false;
					}
				}

				return true;
			});

			// Makes sure it doesnt get too big
			$cache->setLimit(350);
		}

		return $cache;
	}

	public static function createNull()
	{
		static $null_cache;

		if (!$null_cache) {
			$null_cache = new \Orb\Doctrine\Common\Cache\ArrayFileCache('/dev/null');
			$null_cache->disable();
		}

		return $null_cache;
	}
}
