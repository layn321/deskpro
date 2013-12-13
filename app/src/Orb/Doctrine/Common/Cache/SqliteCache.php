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

use \Orb\Util\Util;

/**
 * The SQLite cache driver stores cache info in an sqlite db, either in the
 * filesystem or memory.
 *
 * You can re-use one connection for multiple caches if you specify a $connection_name
 * with setDbFile(), just cahnge $cache_name. This essentially creates a new table
 * for each cache, so you can reuse one db.
 */
class SqliteCache extends \Doctrine\Common\Cache\CacheProvider
{
	/**
	 * An array of saved connections. Allows us to reuse connections.
	 * @var Doctrine\DBAL\Connection[]
	 */
	protected static $named_connections = array();

	/**
	 * The path to the DB file, or 'MEMORY' if its memory.
	 */
	protected $_dbfile;

	/**
	 * The cache name, aka the table in the database
	 * @var string
	 */
	protected $_cache_name = 'doctrine_cache';

	/**
	 * Connection name which might have been created before
	 * @var string
	 */
	protected $_connection_name = null;

	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $_db;

	/**
	 * No expiry mode
	 *
	 * @var bool
	 */
	protected $_no_expire = false;

	/**
	 * Fetch a full list of IDs the first time an ID containment check is made
	 * @var bool
	 */
	protected $_cached_ids_mode = false;

	/**
	 * @var array
	 */
	protected $_cached_ids = null;

	/**
	 * Sets no expiry mode. This means all cache entries are deleted manually, no time comparions or cleanup is done.
	 */
	public function enableNoExpiryMode()
	{
		$this->_no_expire = true;
	}

	/**
	 * This enables an ID cache so contains() doesnt result in a new query every time.
	 * This will keep a record of all IDs with one query, and refer to that instead.
	 */
	public function enableCachedIdsCheckMode()
	{
		$this->_cached_ids_mode = true;
	}

	/**
	 * Sets the SQLite database connection to use for the cache. This will automatically
	 * initialize the database/tables if it doesn't exist.
	 *
	 * If $filepath is 'MEMORY', then the SQLite database will be created in memory. (Note that
	 * this is mainly a debug tool; memory tables are destroyed as soon as the connection dies.)
	 *
	 * $cache_name is the name of the cache table. $connection_name is the named connection for this
	 * sqlite database. You can reuse connections for multiple caches, just change the $cache_name
	 * so they use different tables.
	 *
	 * @param string $filepath
	 * @param string $cache_name
	 * @param string|null $connection_name
	 */
	public function __construct($filepath, $cache_name = 'doctrine_cache', $connection_name = null)
	{
		$this->_dbfile = $filepath;
		$this->_cache_name = $cache_name;
		$this->_connection_name = $connection_name;
	}


	/**
	 * Get the database connection
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getDbConnection()
	{
		if ($this->_db !== null) {
			return $this->_db;
		}

		if ($this->_connection_name !== null) {
			if (isset(self::$named_connections[$this->_connection_name])) {
				$this->_db = self::$named_connections[$this->_connection_name];
			}
		}
		if (!$this->_db) {
			$params = array(
				'driver' => 'pdo_sqlite'
			);

			if ($this->_dbfile == 'MEMORY') {
				$params['memory'] = true;
			} else {
				$params['path'] = $this->_dbfile;
			}

			$this->_db = \Doctrine\DBAL\DriverManager::getConnection($params);

			if ($this->_connection_name) {
				self::$named_connections[$this->_connection_name] = $this->_db;
			}
		}

		$exists = $this->_db->fetchColumn("SELECT name FROM sqlite_master WHERE type='table' AND name='{$this->_cache_name}'");
		if (!$exists) {
			if (!$this->_no_expire) {
				$this->_db->exec("CREATE TABLE {$this->_cache_name} (id TEXT PRIMARY KEY, data BLOB, expire INTEGER)");
			} else {
				$this->_db->exec("CREATE TABLE {$this->_cache_name} (id TEXT PRIMARY KEY, data BLOB)");
			}
		} else {
			if (!$this->_no_expire && mt_rand(1,10) <= 3) {
				$this->_db->executeUpdate("DELETE FROM {$this->_cache_name} WHERE expire < ? AND expire != 0", array(time()));
			}
		}

		return $this->_db;
	}


	public function getIds()
	{
		$keys = array();

		if ($this->_no_expire) {
			$sql = "SELECT id FROM {$this->_cache_name}";
		} else {
			$sql = "SELECT id FROM {$this->_cache_name} WHERE (expire = 0 OR expire > ?)";
		}

		foreach ($this->getDbConnection()->fetchAll($sql, array(time())) as $x) {
			$keys[] = $x['id'];
		}

		if ($this->_cached_ids_mode) {
			$this->_cached_ids = array_combine($keys, array_fill(0,count($keys), true));
		}

		return $keys;
	}

	protected function doFetch($id)
    {
		if ($this->_no_expire) {
			$sql = "SELECT data FROM {$this->_cache_name} WHERE id = ?";
			$params = array($id);
		} else {
			$sql = "SELECT data FROM {$this->_cache_name} WHERE id = ? AND (expire = 0 OR expire > ?)";
			$params = array($id, time());
		}

        $data = $this->getDbConnection()->fetchColumn($sql, $params);
		$data = unserialize($data);

		return $data;
    }

    protected function doContains($id)
    {
		if ($this->_cached_ids_mode) {
			if ($this->_cached_ids === null) {
				$this->getIds();
			}
			return isset($this->_cached_ids[$id]);
		}

		if ($this->_no_expire) {
			$sql = "SELECT data FROM {$this->_cache_name} WHERE id = ?";
			$params = array($id);
		} else {
			$sql = "SELECT data FROM {$this->_cache_name} WHERE id = ? AND (expire = 0 OR expire > ?)";
			$params = array($id, time());
		}

        $exists = $this->getDbConnection()->fetchColumn("SELECT id FROM {$this->_cache_name} WHERE id = ? AND (expire = 0 OR expire > ?)", array($id, time()));

		return (bool)$exists;
    }

    protected function doSave($id, $data, $lifeTime = 0)
    {
		$data = serialize($data);

		if ($this->_no_expire) {
			$expire = 0;
			if ($lifeTime) {
				$expire = time() + $lifeTime;
			}

			$this->getDbConnection()->executeUpdate("INSERT OR REPLACE INTO {$this->_cache_name} (id, data, expire) VALUES (?, ?, ?)", array($id, $data, $expire));
		} else {
			$this->getDbConnection()->executeUpdate("INSERT OR REPLACE INTO {$this->_cache_name} (id, data) VALUES (?, ?)", array($id, $data));
		}

		return true;
    }

    protected function doDelete($id)
    {
		$this->getDbConnection()->executeUpdate("DELETE FROM {$this->_cache_name} WHERE id = ?", array($id));

		if ($this->_cached_ids !== null) {
			unset($this->_cached_ids[$id]);
		}

		return true;
    }

	protected function doFlush()
	{
		$this->getDbConnection()->executeUpdate("DELETE FROM {$this->_cache_name}");
	}

	protected function doGetStats()
	{
		return null;
	}
}
