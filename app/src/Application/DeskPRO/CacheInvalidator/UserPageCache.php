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

class UserPageCache
{
	protected $_cache_dir = '';

	public function __construct($cache_dir = null)
	{
		if (!$cache_dir) {
			$cache_dir = dp_get_tmp_dir() . '/page-cache';
		}

		$this->_cache_dir = $cache_dir;
	}

	public function invalidateAll()
	{
		$cache_dir = $this->_cache_dir;
		if (is_dir($cache_dir)) {
			$dir = opendir($cache_dir);
			while (($file = readdir($dir)) !== false) {
				if ($file == 'index.html') {
					continue;
				}

				$path = "$cache_dir/$file";
				if (is_file($path) && is_readable($path)) {
					@unlink($path);
				}
			}
			closedir($dir);
		}
	}

	public function invalidateRegex($regex)
	{
		$cache_dir = $this->_cache_dir;
		if (is_dir($cache_dir) && is_readable($cache_dir)) {
			$dir = opendir($cache_dir);
			while (($file = readdir($dir)) !== false) {
				if ($file == 'index.html') {
					continue;
				}

				$path = "$cache_dir/$file";
				if (preg_match($regex, $file) && is_file($path) && is_readable($path)) {
					@unlink($path);
				}
			}
			closedir($dir);
		}
	}

	public function cleanup($ttl = null, $max_size = null)
	{
		global $DP_CONFIG;
		if ($ttl === null) {
			$ttl = isset($DP_CONFIG['cache']['page_cache']['ttl']) ? $DP_CONFIG['cache']['page_cache']['ttl'] : 900;
		}
		if ($max_size === null) {
			$max_size = isset($DP_CONFIG['cache']['page_cache']['max_size']) ? $DP_CONFIG['cache']['page_cache']['max_size'] : 10000000;
		}

		$cache_dir = $this->_cache_dir;
		if (is_dir($cache_dir)) {
			$files = array();
			$sizes = array();
			$total_size = 0;
			$dir = opendir($cache_dir);
			while (($file = readdir($dir)) !== false) {
				if ($file == 'index.html') {
					continue;
				}

				$path = "$cache_dir/$file";
				if (is_file($path) && is_readable($path)) {
					$files[$path] = filemtime($path);
					$sizes[$path] = filesize($path);
					$total_size += $sizes[$path];
				}
			}
			closedir($dir);

			$cutoff = time() - $ttl;

			asort($files);

			foreach ($files AS $path => $mtime) {
				if ($mtime < $cutoff || $total_size >= $max_size) {
					@unlink($path);
					$size = $sizes[$path];
					$total_size -= $size;
				}

				if ($mtime >= $cutoff && $total_size < $max_size) {
					break;
				}
			}
		}
	}

	public function invalidateLanguageCache()
	{
		@unlink(dp_get_data_dir() . '/languages.cache');
	}
}
