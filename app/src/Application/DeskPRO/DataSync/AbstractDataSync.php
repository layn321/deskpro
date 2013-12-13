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
 */

namespace Application\DeskPRO\DataSync;

/**
 * Abstract base for syncing data during install/upgrade.
 *
 * Data is stored in app/src/Application/InstallBundle/Data/Sync/ in .json files
 * that are named after the handler classes in this directory.
 */
abstract class AbstractDataSync
{
	/**
	 * Name of the table in the DB this refers to
	 *
	 * @var string
	 */
	protected $_table;

	/**
	 * Name of the unique identifying key column in the DB.
	 *
	 * @var string
	 */
	protected $_keyField;

	/**
	 * A list of columns in the DB that should be synchronized.
	 *
	 * @var array
	 */
	protected $_syncFields = array();

	/**
	 * A list of key-value pairs that represent default values for
	 * non-synchronized fields on insert.
	 *
	 * @var array
	 */
	protected $_defaultFields = array();

	/**
	 * Path to the base data file.
	 *
	 * @var string
	 */
	protected $_baseFile;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $_db;

	/**
	 * Gets the name of the DB table that data is synced to/from.
	 *
	 * @return string
	 */
	abstract public function getTableName();

	/**
	 * Gets the name of the DB column that is used as a unique identifier
	 * in the DB. Any non-null value for this field can be synced.
	 *
	 * @return string
	 */
	abstract public function getKeyField();

	/**
	 * Gets the list of fields that will be synced to/from the base data file.
	 *
	 * @return array
	 */
	abstract public function getSyncFields();

	/**
	 * Constructor. Default file path is in src/Application/InstallBundle/Data/Sync/<this file name>.json.
	 *
	 * @param string|null $baseFile Path to base data file or null for the default path.
	 */
	public function __construct($baseFile = null)
	{
		$this->_table = $this->getTableName();
		$this->_keyField = $this->getKeyField();
		$this->_syncFields = $this->getSyncFields();
		$this->_defaultFields = $this->getDefaultInsertValues();

		$fileName = basename(str_replace('\\', DIRECTORY_SEPARATOR, get_class($this)));
		$this->_baseFile = $baseFile ?: DP_ROOT . '/src/Application/InstallBundle/Data/Sync/' . $fileName . '.json';

		$this->_db = \Application\DeskPRO\App::getDb();
	}

	/**
	 * Gets key-value pairs of default values for non-synced columns.
	 * This is only used when inserting a new row.
	 *
	 * @return array
	 */
	public function getDefaultInsertValues()
	{
		return array();
	}

	/**
	 * Syncs the base data to the live data.
	 *
	 * @return array Array with counts of manipulation types: install, update, delete
	 *
	 * @throws \Exception
	 */
	public function syncBaseToLive()
	{
		$live = $this->getLiveSyncableRows();
		$base = $this->getBaseSyncableData();

		$insert = 0;
		$update = 0;
		$delete = 0;

		$this->_db->beginTransaction();

		try {
			foreach ($live AS $key => $row) {
				if (!isset($base[$key])) {
					$this->delete($key, $row);
					$delete++;
				}
			}

			foreach ($base AS $key => $data) {
				if (!isset($live[$key])) {
					$this->insert($key, $data);
					$insert++;
				} else {
					$this->update($key, $data, $live[$key]);
					$update++;
				}
			}

			$this->_db->commit();
		} catch (\Exception $e) {
			$this->_db->rollback();
			throw $e;
		}

		return array(
			'insert' => $insert,
			'update' => $update,
			'delete' => $delete
		);
	}

	public function deleteLiveData()
	{
		$live = $this->getLiveSyncableRows();
		if (!$live) {
			return 0;
		}

		$delete = 0;

		$this->_db->beginTransaction();

		try {
			foreach ($live AS $key => $row) {
				$this->delete($key, $row);
				$delete++;
			}

			$this->_db->commit();
		} catch (\Exception $e) {
			$this->_db->rollback();
			throw $e;
		}

		return $delete;
	}

	/**
	 * Writes the given data (or current live data) to the base file.
	 *
	 * @param array|null $data If null, gets the live data
	 *
	 * @return bool
	 */
	public function writeToBase(array $data = null)
	{
		if ($data === null) {
			$data = $this->getLiveSyncableData();
		}

		return file_put_contents($this->_baseFile, $this->_encodeBaseData($data)) !== false;
	}

	/**
	 * Encodes the base data to a string
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	protected function _encodeBaseData(array $data)
	{
		$output = array();
		foreach ($data AS $uniqueKey => $row) {
			$output[] = "\"$uniqueKey\":" . json_encode($row);
		}

		$eol = PHP_EOL;

		return '{' . $eol . implode(",$eol", $output) . $eol . '}';
	}

	/**
	 * Decodes string from the base data file to an array
	 *
	 * @param string $data
	 *
	 * @return array
	 */
	protected function _decodeBaseData($data)
	{
		$output = json_decode($data, true);
		return is_array($output) ? $output : array();
	}

	/**
	 * Gets the rows and columns that can be synced from the live data.
	 *
	 * @return array
	 */
	public function getLiveSyncableData()
	{
		return $this->filterSyncableData($this->getLiveSyncableRows());
	}

	/**
	 * Gets any rows that contain syncable data. This data may include
	 * data that shouldn't be synced.
	 *
	 * @return array
	 */
	public function getLiveSyncableRows()
	{
		return $this->_db->fetchAllKeyed("
			SELECT *
			FROM `$this->_table`
			WHERE `$this->_keyField` IS NOT NULL
			ORDER BY `$this->_keyField`
		", array(), $this->_keyField);
	}

	/**
	 * Gets the syncable data from the base source.
	 *
	 * @return array
	 */
	public function getBaseSyncableData()
	{
		if (file_exists($this->_baseFile)) {
			return $this->_decodeBaseData(file_get_contents($this->_baseFile), true);
		} else {
			return array();
		}
	}

	/**
	 * Filters a syncable data set (multiple rows) to only include fields that can be
	 * in each row.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function filterSyncableData(array $data)
	{
		$output = array();
		foreach ($data AS $key => $row) {
			$output[$key] = $this->filterSyncableRow($row);
		}

		return $output;
	}

	/**
	 * Filters a row of data to only include fields that a syncable.
	 *
	 * @param array $row
	 *
	 * @return array
	 */
	public function filterSyncableRow(array $row)
	{
		$output = array();
		foreach ($this->_syncFields AS $field)
		{
			if (array_key_exists($field, $row)) {
				$output[$field] = $row[$field];
			} else {
				$output[$field] = null;
			}
		}

		ksort($output);

		return $output;
	}

	/**
	 * Inserts a row with the given key.
	 *
	 * @param string $key Unique key
	 * @param array $data Syncable data
	 */
	public function insert($key, array $data)
	{
		$row = array_merge($this->_defaultFields, $data);
		$row[$this->_keyField] = $key;

		$this->_db->insert($this->_table, $row);
	}

	/**
	 * Updates the syncable data for a given key.
	 *
	 * @param string $key
	 * @param array $data Syncable data (new data)
	 * @param array $row Full existing "live" row
	 */
	public function update($key, array $data, array $row)
	{
		$this->_db->update($this->_table, $data, array($this->_keyField => $key));
	}

	/**
	 * Deletes the live data for the given key.
	 *
	 * @param string $key
	 * @param array $row Full existing "live" row
	 */
	public function delete($key, array $row)
	{
		$this->_db->delete($this->_table, array($this->_keyField => $key));
	}

	/**
	 * Gets a list of syncable classes.
	 *
	 * @return array Keyed by a unique name, value is a canonical class name
	 */
	public static function getAvailableSyncClasses()
	{
		$classes = array();
		$baseFile = basename(__FILE__);

		foreach (glob(__DIR__ . '/*.php') AS $file)
		{
			$file = basename($file);
			if ($file != $baseFile) {
				$class = substr($file, 0, -4);
				$classes[$class] = '\\' . __NAMESPACE__ . '\\' . $class;
			}
		}

		return $classes;
	}

	/**
	 * Syncs all available base data files to the live system. This is mostly
	 * targeted at install/upgrade processes.
	 *
	 * @return array
	 */
	public static function syncAllBaseToLive()
	{
		$output = array();
		foreach (static::getAvailableSyncClasses() AS $key => $class)
		{
			/* @var $sync \Application\DeskPRO\DataSync\AbstractDataSync */
			$sync = new $class();
			$output[$key] = $sync->syncBaseToLive();
		}

		return $output;
	}
}