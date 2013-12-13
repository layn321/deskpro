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
 * @subpackage AdminBundle
 */

namespace Application\InstallBundle\Data;

use Application\DeskPRO\DBAL\Connection;
use Orb\Util\Strings;

class ServerStats
{
	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	public function __construct(Connection $db = null)
	{
		$this->db = $db;
	}

	public function getStats()
	{
		$stats = array();

		#------------------------------
		# PHP info
		#------------------------------

		$stats['php_version'] = phpversion();
		$stats['php_memory_limit'] = \Orb\Util\Env::getMemoryLimit();

		if (function_exists('apc_cache_info')) {
			$stats['php_has_apc'] = 1;
			$stats['apc_version'] = phpversion('apc');
		} else {
			$stats['php_has_apc'] = 0;
			$stats['apc_version'] = 0;
		}

		if (function_exists('wincache_ucache_info')) {
			$stats['php_has_wincache'] = 1;
			$stats['wincache_version'] = phpversion('wincache');
		} else {
			$stats['php_has_wincache'] = 0;
			$stats['wincache_version'] = 0;
		}

		if (function_exists('mb_get_info')) {
			$stats['php_has_mbstring'] = 1;
		} else {
			$stats['php_has_mbstring'] = 0;
		}

		if (function_exists('gd_info')) {
			$stats['php_has_gd'] = 1;
		} else {
			$stats['php_has_gd'] = 0;
		}

		if (class_exists('Imagick', false)) {
			$stats['php_has_imagick'] = 1;
		} else {
			$stats['php_has_imagick'] = 0;
		}

		if (class_exists('Gmagick', false)) {
			$stats['php_has_gmagick'] = 1;
		} else {
			$stats['php_has_gmagick'] = 0;
		}

		if (class_exists('PDO')) {
			$stats['php_has_pdo'] = 1;
			if (in_array('mysql', \PDO::getAvailableDrivers())) {
				$stats['php_has_pdo_mysql'] = 1;
			} else {
				$stats['php_has_pdo_mysql'] = 0;
			}
		} else {
			$stats['php_has_pdo'] = 0;
			$stats['php_has_pdo_mysql'] = 0;
		}

		if (function_exists('json_decode')) {
			$stats['php_has_json'] = 1;
		} else {
			$stats['php_has_json'] = 0;
		}

		if (function_exists('ctype_digit')) {
			$stats['php_has_ctype'] = 1;
		} else {
			$stats['php_has_ctype'] = 0;
		}

		if (function_exists('token_get_all')) {
			$stats['php_has_tokenizer'] = 1;
		} else {
			$stats['php_has_tokenizer'] = 0;
		}

		#------------------------------
		# MySQL info
		#------------------------------

		if ($this->db) {
			try {
				$stats['mysql_version'] = $this->db->fetchColumn("SHOW VARIABLES LIKE 'version'", array(), 1);
				$stats['mysql_read_buffer_size'] = $this->db->fetchColumn("SHOW VARIABLES LIKE 'read_buffer_size'", array(), 1);
				$stats['mysql_default_storage_engine'] = $this->db->fetchColumn("SHOW VARIABLES LIKE 'default_storage_engine'", array(), 1);
				$stats['mysql_join_buffer_size'] = $this->db->fetchColumn("SHOW VARIABLES LIKE 'join_buffer_size'", array(), 1);
				$stats['mysql_key_buffer_size'] = $this->db->fetchColumn("SHOW VARIABLES LIKE 'key_buffer_size'", array(), 1);
				$stats['mysql_max_allowed_packet'] = $this->db->fetchColumn("SHOW VARIABLES LIKE 'max_allowed_packet'", array(), 1);
				$stats['mysql_max_tmp_tables'] = $this->db->fetchColumn("SHOW VARIABLES LIKE 'max_tmp_tables'", array(), 1);
				$stats['mysql_max_user_connections'] = $this->db->fetchColumn("SHOW VARIABLES LIKE 'max_user_connections'", array(), 1);
				foreach ($this->db->fetchAllKeyValue("SHOW VARIABLES LIKE '%innodb%'") as $k => $v) {
					$stats["mysql_$k"] = $v;
				}
			} catch (\Exception $e) {}
		}

		#------------------------------
		# OS / Server info
		#------------------------------

		if (strpos(strtoupper(PHP_OS), 'WIN') === 0) {
			$stats['server_os'] = 'win';
		} elseif (strpos(strtoupper(PHP_OS), 'DARWIN') === 0) {
			$stats['server_os'] = 'mac';
		} elseif (strpos(strtoupper(PHP_OS), 'FREEBSD') === 0) {
			$stats['server_os'] = 'freebsd';
		} elseif (strpos(strtoupper(PHP_OS), 'LINUX') === 0) {
			$stats['server_os'] = 'linux';
		} else {
			$stats['server_os'] = PHP_OS;
		}

		$stats['server_uname'] = php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('v') . ' ' . php_uname('m');

		#------------------------------
		# Web server
		#------------------------------

		if (isset($_SERVER['SERVER_SOFTWARE'])) {
			if (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'APACHE') !== false) {
				$stats['web_server'] = 'apache';
			} elseif (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'IIS') !== false) {
				$stats['web_server'] = 'iis';
			} elseif (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'NGINX') !== false) {
				$stats['web_server'] = 'nginx';
			} elseif (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'CHEROKEE') !== false) {
				$stats['web_server'] = 'cherokee';
			} elseif (strpos(strtoupper($_SERVER['SERVER_SOFTWARE']), 'LIGHTTPD') !== false) {
				$stats['web_server'] = 'lighttpd';
			} else {
				$stats['web_server'] = $_SERVER['SERVER_SOFTWARE'];
			}

			$stats['server_software'] = $_SERVER['SERVER_SOFTWARE'];

			if (function_exists('apache_get_modules')) {
				$stats['has_mod_rewrite'] = in_array('mod_rewrite', apache_get_modules());
			}
		}

		return $stats;
	}
}
