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
 * The array file cache is a cache that writes k=>v to a file on the filesystem.
 * This is very ineffecient for writes, but can work well if writes are very rare (ie part of a build system).
 */
class ArrayFileCache extends \Doctrine\Common\Cache\CacheProvider
{
	/**
	 * Data is array(key => array(time => timestamp, data => data, deleted => true, updated => true)
	 *
     * @var array $data
     */
    protected $data = null;

	/**
	 * Do we have unwritten changes?
	 *
	 * @var bool
	 */
	protected $dirty = false;

	/**
	 * @var string
	 */
	protected $cache_file;

	/**
	 * Automatically write after each update
	 *
	 * @var bool
	 */
	protected $auto_write = false;

	/**
	 * @var bool
	 */
	protected $disabled = false;

	/**
	 * @var int
	 */
	protected $umask = 0111;

	/**
	 * @var callable
	 */
	protected $filter;

	/**
	 * @var int
	 */
	protected $limit = 0;

	/**
	 * @var int
	 */
	protected $slam_timeout = 10;

	/**
	 * @var resource
	 */
	protected $slam_fp;

	/**
	 * @param string $cache_file
	 */
	public function __construct($cache_file)
	{
		$this->cache_file = $cache_file;
	}

	/**
	 * Function to be called when a new item is being added. Return true
	 * to allow the add, or false to discard the add.
	 *
	 * Called with: $data, $lifeTime, $id
	 *
	 * @param callable $fn
	 */
	public function setFilter($fn)
	{
		$this->filter = $fn;
	}


	/**
	 * Maximum number of entries to add
	 *
	 * @param $limit
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
	}


	/**
	 * Dont load anything new and dont commit
	 */
	public function disable()
	{
		$this->disabled	 = true;
	}

	/**
	 * Disable caching and updating
	 */
	public function enable()
	{
		$this->disabled	 = false;
	}

	/**
	 * Register a shutdown function to save the cache on exit if there are changes
	 *
	 * @return mixed
	 */
	public function registerShutdownCommit()
	{
		static $has_reg = false;
		if ($has_reg) {
			return;
		}

		$has_reg = true;

		register_shutdown_function(array($this, 'commitIfDirty'), true);
	}


	/**
	 * Reload all data
	 */
	public function reloadData()
	{
		if ($this->data === null) {
			$this->data = array();
		}

		if ($this->disabled) {
			return;
		}

		if (file_exists($this->cache_file)) {
			$load_data = @file_get_contents($this->cache_file);
			if ($load_data) {
				$load_data = @unserialize($load_data);
			}

			if (!$load_data || !is_array($load_data)) {
				// If we got here, it means we read the file but it appears to be invalid
				// So it probably means we read during the file being written, so just disable cache this time
				$this->disabled = true;
				return;
			}

			$time = time();

			foreach ($load_data as $k => $info) {
				if ($info['die'] > $time) {
					continue;
				}

				// We dont have the item, add it
				if (!isset($this->data[$k])) {
					$this->data[$k] = $info;

				// The item in the file is newer
				} elseif ($info['time'] > $this->data[$k]['time']) {
					$this->data[$k] = $info;
				}
			}
		}
	}


    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
		if ($this->data === null) $this->reloadData();

		if (isset($this->data[$id]) && !isset($this->data[$id]['deleted']) && (!$this->data[$id]['die'] || $this->data[$id]['die'] < time())) {
			if (isset($this->data[$id]['serialized'])) {
				if (isset($this->data[$id]['data_u'])) {
					return $this->data[$id]['data_u'];
				}

				$this->data[$id]['data_u'] = unserialize($this->data[$id]['data']);
				return $this->data[$id]['data_u'];
			} else {
				return $this->data[$id]['data'];
			}
		}

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
		if ($this->data === null) $this->reloadData();

        if (isset($this->data[$id]) && !isset($this->data[$id]['deleted']) && (!$this->data['die'] || $this->data['die'] < time())) {
			return true;
		}

		return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
		if ($this->filter) {
			if (!call_user_func($this->filter, $data, $lifeTime, $id)) {
				return true;
			}
		}
		if ($this->data === null) $this->reloadData();

		if ($this->limit && count($this->data) >= $this->limit) {
			return true;
		}

		if (is_scalar($data)) {
			$this->data[$id] = array(
				'time' => time(),
				'die' => ($lifeTime ? time() + $lifeTime : 0),
				'data' => $data,
				'updated' => true
			);
		} else {
			$this->data[$id] = array(
				'time' => time(),
				'die' => ($lifeTime ? time() + $lifeTime : 0),
				'data' => serialize($data),
				'data_u' => $data,
				'serialized' => true,
				'updated' => true
			);
		}

		$this->dirty = true;

		if ($this->auto_write) {
			$this->commit();
		}

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
		if ($this->data === null) $this->reloadData();

        $this->data[$id] = array(
			'time' => time(),
			'die' => 0,
			'data' => 0,
			'deleted' => true
		);

		$this->dirty = true;

		if ($this->auto_write) {
			$this->commit();
		}

        return true;
    }

	/**
	 * Save the current cache to disk
	 *
	 * @return int
	 */
	public function commit()
	{
		if ($this->disabled || $this->hasSlam()) {
			return;
		}

		if (!$this->obtainSlam()) {
			return;
		}

        $result = array();
		$time = time();
		foreach ($this->data as $k => $v) {
			if (!isset($v['deleted']) && (!$v['die'] || $v['die'] < $time)) {
				unset($v['deleted'], $v['updated'], $v['data_u']);
				$result[$k] = $v;
			}
		}

		$this->data = $result;

		$contents = serialize($result);
		$size = strlen($contents);

		$this->dirty = false;

		$changed_umask = null;
		if (!file_exists($this->cache_file)) {
			$changed_umask = umask($this->umask);
		}

		if (file_put_contents($this->cache_file, $contents, \LOCK_EX) != $size) {

			if ($changed_umask !== null) {
				umask($changed_umask);
			}

			// The file is probably invalid now, delete it
			@unlink($this->cache_file);
			$this->releaseSlam();

			throw new \RuntimeException("Failed to write $size bytes");
		}

		if ($changed_umask !== null) {
			umask($changed_umask);
		}

		$this->releaseSlam();
	}


	/**
	 * Commit if there have been changes to the cache
	 */
	public function commitIfDirty($quiet = false)
	{
		if ($this->dirty) {
			try {
				$this->commit();
			} catch (\Exception $e) {
				if (!$quiet) {
					throw $e;
				}
			}
		}
	}


    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
		$this->dirty = false;

		$this->data = array();
		$php = "<?php return array(); ";
		return file_put_contents($this->cache_file, $php);
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return null;
    }


	/**
	 * @return bool
	 */
	public function hasSlam()
	{
		if (file_exists($this->cache_file . '.slam') && (@filemtime($this->cache_file . '.slam') ?: 0) < time() - $this->slam_timeout) {
			return true;
		}

		return false;
	}


	/**
	 * @return resource
	 */
	public function obtainSlam()
	{
		if ($this->hasSlam()) {
			return false;
		}

		if ($this->slam_fp) {
			return $this->slam_fp;
		}

		$this->slam_fp = @fopen($this->cache_file . '.slam', 'w');
		if (!$this->slam_fp) {
			$this->slam_fp = null;
			return false;
		}

		if (!@flock($this->slam_fp, \LOCK_EX)) {
			@fclose($this->slam_fp);
			return false;
		}

		register_shutdown_function(array($this, 'releaseSlam'));

		return $this->slam_fp;
	}


	/**
	 * @return void
	 */
	public function releaseSlam()
	{
		if ($this->slam_fp) {
			@flock($this->slam_fp, \LOCK_UN);
			@fclose($this->slam_fp);
			@unlink($this->cache_file . '.slam');
			$this->slam_fp = null;
		}
	}
}
