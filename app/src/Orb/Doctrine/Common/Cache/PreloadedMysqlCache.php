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
 * @subpackage Doctrine
 */

namespace Orb\Doctrine\Common\Cache;

use Doctrine\DBAL\Connection;
use Orb\Util\Arrays;

/**
 * The MySQL cache stores data in a mysql table and you preload groups
 * of keys before they are actually used.
 */
class PreloadedMysqlCache implements \Doctrine\Common\Cache\Cache
{
	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $id_prefix = 'd.';

	/**
	 * Pre-loaded data
	 *
	 * @var array
	 */
	protected $loaded = array();

	/**
	 * Array of prefixes we've prelaoded
	 *
	 * @var array
	 */
	protected $loaded_prefixes = array();

	/**
	 * @var bool
	 */
	protected $auto_unserialize = true;

	/**
	 * True to catch and silently discard exceptions
	 * for fetch/save's
	 *
	 * @var bool
	 */
	protected $silence_exceptions = true;

	/**
	 * @param \Doctrine\DBAL\Connection $db
	 */
	public function __construct(Connection $db)
	{
		$this->db = $db;
	}


	/**
	 * @param bool $on_or_off
	 */
	public function setAutoUnserialize($on_or_off)
	{
		$this->auto_unserialize = (bool)$on_or_off;
	}


	/**
	 * Create the cache database table
	 */
	public function initTable()
	{
		$this->db->exec("
			CREATE TABLE `cache` (
			  `id` varbinary(255) NOT NULL,
			  `data` longblob NOT NULL,
			  `date_expire` datetime DEFAULT NULL,
			  INDEX date_expire_idx (date_expire),
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}


	/**
	 * Set the prefix used on all IDs
	 *
	 * @param $prefix
	 */
	public function setPrefix($prefix)
	{
		if (func_num_args() == 1) {
			$this->id_prefix = $prefix;
		} else {
			$args = func_get_args();
			$args = Arrays::castToType($args, 'string');
			$this->id_prefix = implode('.', $args);
		}

		if (substr($this->id_prefix, -1, 1) != '.') {
			$this->id_prefix .= '.';
		}
	}


	/**
	 * Preload one or more keys
	 *
	 * If exactly one array param, expected to be an array of keys.
	 * Otherwise you can pass any number of key args
	 *
	 * @param string $id...
	 */
	public function preloadIds()
	{
		if (func_num_args() == 1) {
			$keys = (array)func_get_arg(1);
		} else {
			$keys = func_get_args();
		}

		foreach ($keys as &$k) {
			if (!array_key_exists($k, $this->loaded)) {
				$k = $this->id_prefix . $k;
			} else {
				$k = 0;
			}
		}
		unset($k);

		$keys = Arrays::removeFalsey($keys);
		if (!$keys) {
			return;
		}

		$keys_in = "'" . implode("','", $keys) . "'";

		$date = date('Y-m-d H:i:s');

		try {
			$records = $this->db->fetchAll("
				SELECT id, data
				FROM cache
				WHERE id IN ($keys_in) AND (date_expire IS NULL OR date_expire > ?)
			", array($date));
		} catch (\Exception $e) {
			$records = array();
			if (!$this->silence_exceptions) {
				throw $e;
			}
		}

		$got_keys = array();
		foreach ($records as $rec) {
			$got_keys[$rec['id']];
			$this->loaded[$rec['id']] = $rec['data'];
		}

		// Fill loaded with nulls for keys we know are missing
		if (count($got_keys) != count($keys)) {
			$missing_keys = array_diff($keys, $got_keys);
			foreach ($missing_keys as $k) {
				$this->loaded[$k] = null;
			}
		}
	}


	/**
	 * Preload a number of items given a prefix
	 *
	 * @param $prefix
	 */
	public function preloadPrefix($add_prefix = null)
	{
		if (!$add_prefix) $add_prefix = '';
		$prefix = $this->id_prefix . $add_prefix;

		if (in_array($prefix, $this->loaded_prefixes)) {
			return;
		}

		$this->loaded_prefixes[] = $prefix;
		$prefix_like = "$prefix%";

		$date = date('Y-m-d H:i:s');
		try {
			$records = $this->db->fetchAll("
				SELECT id, data
				FROM cache
				WHERE id LIKE ? AND (date_expire IS NULL OR date_expire > ?)
			", array($prefix_like, $date));
		} catch (\Exception $e) {
			$records = array();
			if (!$this->silence_exceptions) {
				throw $e;
			}
		}

		foreach ($records as $rec) {
			$this->loaded[$rec['id']] = $rec['data'];
		}
	}


	/**
	 * Delete all caches in the database with a key
	 *
	 * @param $prefix
	 */
	public function flushPrefix($prefix)
	{
		$prefix = $this->id_prefix . $prefix;
		$prefix_like = "$prefix%";

		try {
			$this->db->fetchAll("
				DELETE FROM cache
				WHERE id LIKE ?
			", array($prefix_like));
		} catch (\Exception $e) {
			if (!$this->silence_exceptions) {
				throw $e;
			}
		}

		foreach (array_keys($this->loaded) as $key) {
			if (strpos($key, $prefix) === 0) {
				unset($this->loaded[$key]);
			}
		}
	}


	/**
	 * Fetches an entry from the cache.
	 *
	 * @param string $id cache id The id of the cache entry to fetch.
	 * @return string The cached data or FALSE, if no cache entry exists for the given id.
	 */
	public function fetch($id)
	{
		$prefix_id = $this->id_prefix . $id;

		$data = false;

		$is_loaded = false;

		if (array_key_exists($prefix_id, $this->loaded)) {
			$is_loaded = true;
		} else {
			foreach ($this->loaded_prefixes as $check_prefix) {
				if (strpos($prefix_id, $check_prefix) === 0) {
					$is_loaded = true;
					break;
				}
			}
		}

		if ($is_loaded) {
			if (!isset($this->loaded[$prefix_id]) || $this->loaded[$prefix_id] === null) {
				return false;
			}

			$data = $this->loaded[$prefix_id];

		} else {
			$date = date('Y-m-d H:i:s');

			try {
				$record = $this->db->fetchAssoc("
					SELECT data
					FROM cache
					WHERE id = ? AND (date_expire IS NULL OR date_expire > ?)
				", array($prefix_id, $date));
			} catch (\Exception $e) {
				$record = null;
				if (!$this->silence_exceptions) {
					throw $e;
				}
			}

			if ($record) {
				$this->loaded[$prefix_id] = $record['data'];
				$data = $this->loaded[$prefix_id];
			} else {
				$this->loaded[$prefix_id] = null;
				return false;
			}
		}

		if (is_scalar($data) && $this->auto_unserialize && !is_numeric($data) && preg_match('#^(a|O):[0-9]+:#', $data)) {
			$this->loaded[$prefix_id] = $data = unserialize($data);
		}

		return $data;
	}


	/**
	 * Test if an entry exists in the cache.
	 *
	 * @param string $id cache id The cache id of the entry to check for.
	 * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
	 */
	public function contains($id)
	{
		$prefix_id = $this->id_prefix . $id;

		// If it exists in the array we know for sure if its set or not
		if (array_key_exists($prefix_id, $this->loaded)) {
			return ($this->loaded[$prefix_id] !== null);

		// Otherwise we have to do a query to fetch it
		} else {

			// Try to see if it matches one of the laoded prefixes
			foreach ($this->loaded_prefixes as $prefix) {
				// If its found but it didnt match above, then we know it wasnt loaded
				if (strpos($prefix_id, $prefix) === 0) {
					$this->loaded[$prefix_id] = null;
					return false;
				}
			}

			// Fetch it into local storage now with fetch()
			return ($this->fetch($id) !== false);
		}
	}




	/**
	 * Puts data into the cache.
	 *
	 * @param string $id The cache id.
	 * @param string $data The cache entry/data.
	 * @param int $lifeTime The lifetime. If != 0, sets a specific lifetime for this cache entry (0 => infinite lifeTime).
	 * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
	 */
	public function save($id, $data, $lifeTime = 0)
	{
		$prefix_id = $this->id_prefix . $id;

		$date = null;
		if ($lifeTime) {
			$date = date('Y-m-d H:i:s', time()+$lifeTime);
		}

		if (!is_scalar($data)) {
			$data = serialize($data);
		}

		try {
			$this->db->executeUpdate("
				REPLACE INTO cache SET id = ?, data = ?, date_expire = ?
			", array($prefix_id, $data, $date));
		} catch (\Exception $e) {
			if (!$this->silence_exceptions) {
				throw $e;
			}
		}

		$this->loaded[$prefix_id] = $data;

		return true;
	}


	/**
	 * Deletes a cache entry.
	 *
	 * @param string $id cache id
	 * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
	 */
	public function delete($id)
	{
		$prefix_id = $this->id_prefix . $id;

		try {
			$this->db->executeUpdate("
				DELETE FROM cache
				WHERE id = ?
			", array($prefix_id));
		} catch (\Exception $e) {
			if (!$this->silence_exceptions) {
				throw $e;
			}
		}

		unset($this->loaded[$prefix_id]);

		return true;
	}


	/**
	 * Clear the cache
	 */
	public function flush()
	{
		$prefix_like = $this->id_prefix . '%';

		try {
			$this->db->executeUpdate("
				DELETE FROM cache
				WHERE id LIKE ?
			", array($prefix_like));
		} catch (\Exception $e) {
			if (!$this->silence_exceptions) {
				throw $e;
			}
		}

		foreach ($this->loaded as &$v) {
			$v = null;
		}
	}


	/**
	 * @return null
	 */
	public function getStats()
	{
		return null;
	}
}
