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

namespace Application\DeskPRO\Log\ErrorLog;

use Orb\Util\Dates;

class ErrorLogReader implements \Countable, \Iterator, \ArrayAccess
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var array
	 */
	protected $items;

	/**
	 * @var bool
	 */
	protected $store_raw = false;

	/**
	 * @var null
	 */
	protected $filter = null;

	/**
	 * @var int
	 */
	protected $count = 0;

	/**
	 * @var \DateTimeZone
	 */
	protected $timezone;

	/**
	 * @var bool
	 */
	protected $count_mode = false;

	public function __construct($path)
	{
		$this->path = $path;
	}


	/**
	 * @param \DateTimeZone $tz
	 */
	public function setDateTimezone(\DateTimeZone $tz)
	{
		$this->timezone = $tz;
	}


	/**
	 * Filter the items to read
	 *
	 * The filter must accept two parameters:
	 * - string $mode Either 'parsed' (array) or 'raw' (string)
	 * - string $id The log ID
	 * - array $data An array of lines or array of data depending on $mode
	 *
	 * @param callback $filter
	 */
	public function setFilter($filter)
	{
		$this->filter = $filter;
	}


	/**
	 * This sets a filter so reading only finds a specific log entry
	 *
	 * @param string $id
	 */
	public function setIdFilter($find_id)
	{
		$filter = function($mode, $id, $data) use ($find_id) {
			return $id == $find_id;
		};

		$this->setFilter($filter);

		return $filter;
	}


	/**
	 * Saves the raw log in the data array so it can be displayed
	 */
	public function enableRawLog()
	{
		$this->store_raw = true;
	}


	/**
	 * Only keeps track of a count, no data is parsed
	 */
	public function enableCountMode()
	{
		$this->count_mode = true;
	}

	/**
	 * Loads the log file and does the parsing
	 */
	protected function _initItems()
	{
		if ($this->items !==  null) {
			return;
		}

		$this->items = array();

		$fp = @fopen($this->path, 'r');
		if (!$fp) {
			return;
		}

		$last_id = null;
		$log_lines = array();

		while (($l = fgets($fp)) !== false) {
			$m = null;
			$l = trim($l);

			if (!preg_match('#^<DP_LOG:([0-9A-Z]+)>\s*(.*?)$#', $l, $m)) {
				if ($log_lines && $last_id) {
					$this->_initItem($last_id, $log_lines);
				}
				$log_lines = array();
				continue;
			}

			$id  = $m[1];
			$txt = $m[2];

			if ($log_lines && $last_id != $id) {
				$this->_initItem($last_id, $log_lines);
				$log_lines = array();
			}

			$log_lines[] = $txt;
			$last_id = $id;
		}

		if ($log_lines && isset($last_id)) {
			$this->_initItem($last_id, $log_lines);
		}

		fclose($fp);
	}


	/**
	 * Handles lines of a single log entry and parses data out of it
	 *
	 * @param string $id
	 * @param array $log_lines
	 * @return void
	 */
	protected function _initItem($id, array $log_lines)
	{
		$this->count++;
		if ($this->count_mode) {
			return;
		}

		if ($this->filter && !call_user_func($this->filter, 'raw', $id, $log_lines)) {
			return;
		}

		$log_lines = implode("\n", $log_lines);

		$item = array(
			'id'      => $id,
			'summary' => \Orb\Util\Strings::extractRegexMatch('#^(Error|Exception): (.*?)$#m', $log_lines, 2),
			'date'    => \Orb\Util\Strings::extractRegexMatch('#^Date: (.*?)$#m', $log_lines, 1),
			'type'    => \Orb\Util\Strings::extractRegexMatch('#^Type: (.*?)$#m', $log_lines, 1),
			'build'   => \Orb\Util\Strings::extractRegexMatch('#^Build: (.*?)$#m', $log_lines, 1),
			'log'     => $this->store_raw ? $log_lines : null,
		);

		if ($this->timezone) {
			$date = \DateTime::createFromFormat('Y-m-d H:i:s', $item['date'], new \DateTimeZone('UTC'));
			if ($date) {
				$date->setTimezone($this->timezone);
				$item['date'] = $date->format('Y-m-d H:i:s');
			}
		}

		if ($this->filter && !call_user_func($this->filter, 'parsed', $id, $item)) {
			return;
		}

		$this->items[$id] = $item;
	}


	/**
	 * @return array
	 */
	public function getAll()
	{
		$this->_initItems();
		return $this->items;
	}


	/**
	 * @return array
	 */
	public function getKeys()
	{
		$this->_initItems();
		return array_keys($this->items);
	}


	/**#@+ ArrayAccess Interface **/
	public function offsetExists($offset)
	{
		$this->_initItems();
		return isset($this->items[$offset]);
	}

	public function offsetGet($offset)
	{
		$this->_initItems();
		return $this->items[$offset];
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException();
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException();
	}
	/**#@-*/


	/**
	 * @return int
	 */
	public function count()
	{
		$this->_initItems();
		return $this->count;
	}


	/**#@+ Iterator Interface **/
	public function rewind()
	{
		$this->_initItems();
		return reset($this->items);
	}

	public function current()
	{
		$this->_initItems();
		$key = key($this->items);
		if (!$key) {
			return null;
		}
		return $this[$key];
	}

	public function key()
	{
		$this->_initItems();
		$key = key($this->items);
		return $key;
	}

	public function next()
	{
		$this->_initItems();
		return next($this->items);
	}

	public function valid()
	{
		$this->_initItems();
		return key($this->items) !== null;
	}
	/**#@-*/
}