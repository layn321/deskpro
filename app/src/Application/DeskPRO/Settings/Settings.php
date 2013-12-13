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
 * @category Settings
 */

namespace Application\DeskPRO\Settings;

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * This class fethces settings
 */
class Settings implements \ArrayAccess
{
	/**
	 * An array of group=>paths
	 * @var array
	 */
	protected $settings_paths = array();

	/**
	 * Array of array(group => array(settings)) for default settings read in with getDefault()
	 * @var array
	 */
	protected $default_settings = array();

	/**
	 * Plain database connection for raw queries
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;


	/**
	 * Settings we've loaded so far
	 * @var array
	 */
	protected $settings = array();

	/**
	 * @var \DateTimeZone
	 */
	protected $default_timezone;


	/**
	 * An array of groups that we need to load in the next batch
	 * @var array
	 */
	protected $_pending_groups = array();


	/**
	 * An array of groups we've already loaded
	 * @var array
	 */
	protected $_loaded_groups = array();

	/**
	 * Have loaded custom settings yet?
	 * @var bool
	 */
	protected $_has_loaded_db = false;

	/**
	 * Virtual settings are not real settings, but depend on other states. For example,
	 * 'core.interact_require_login' isn't a real setting, it is true depending on the registration mode.
	 *
	 * This is a map of varname => callback
	 *
	 * @var array
	 */
	protected $virtual_settings = array();



	public function __construct(array $settings_paths, \Application\DeskPRO\DBAL\Connection $db = null)
	{
		$this->settings_paths = new SettingsLocator($settings_paths);
		$this->db = $db;

		$this->virtual_settings['core.interact_require_login'] = function($settings) {
			return in_array($settings->get('core.user_mode'), array('require_reg', 'require_reg_agent_validation', 'closed'));
		};

		$this->virtual_settings['default_timezone'] = function($settings) {
			return $settings->getDefaultTimezone();
		};

		$this->virtual_settings['tickets_enable_like_search'] = function($settings) {
			if ($settings['core_tickets.enable_like_search_mode'] == 'auto') {
				if ($settings['core_tickets.enable_like_search_auto']) {
					return true;
				} else {
					return false;
				}
			} else {
				if ($settings['core_tickets.enable_like_search_mode'] && $settings['core_tickets.enable_like_search_mode'] != 'off') {
					return true;
				} else {
					return false;
				}
			}
		};
	}


	/**
	 * @return \Application\DeskPRO\Settings\SettingsLocator
	 */
	public function getSettingsLocator()
	{
		return $this->settings_paths;
	}


	/**
	 * Get the value of a setting
	 *
	 * @param  string $name The name of the setting
	 * @return mixed
	 */
	public function get($name)
	{
		if (!$name) return '';

		if (!isset($this->settings[$name])) {

			if (isset($this->virtual_settings[$name])) {
				return call_user_func($this->virtual_settings[$name], $this, $name);
			}

			$check_group = $this->getGroupFromName($name);

			if (!in_array($check_group, $this->_loaded_groups)) {
				$this->_pending_groups[] = $check_group;
				$this->_loadPendingGroups();
				if (!isset($this->settings[$name])) {
					return null;
				}
				return $this->settings[$name];
			}

			return null;
		}

		return $this->settings[$name];
	}


	/**
	 * This loads the default for a value as defined in the setting file
	 *
	 * @param $name
	 */
	public function getDefault($name)
	{
		$group = $this->getGroupFromName($name);
		$this->getDefaultGroup($group);

		if (isset($this->default_settings[$group][$name])) {
			return $this->default_settings[$group][$name];
		}

		return null;
	}


	/**
	 * Get the default values for an entire group
	 *
	 * @param $group
	 */
	public function getDefaultGroup($group)
	{
		if (!isset($this->default_settings[$group])) {
			$group_file = $this->getGroupFile($group);

			if ($group_file) {
				$group_settings = require($group_file);
			} else {
				$group_settings = array();
			}

			$this->default_settings[$group] = (array)$group_settings;
		}

		return $this->default_settings[$group];
	}


	/**
	 * Get all settings in a group
	 *
	 * @param string $group
	 * @return array
	 */
	public function getGroup($group)
	{
		if (!in_array($group, $this->_loaded_groups)) {
			$this->_pending_groups[] = $group;
			$this->_loadPendingGroups();
		}

		$ret = array();

		$off = strlen($group) + 1;
		foreach ($this->settings as $k => $v) {
			if (strpos($k, $group) === 0) {
				$new_k = substr($k, $off);
				$ret[$new_k] = $v;
			}
		}

		return $ret;
	}



	/**
	 * Manually set the value for one or more settings. Note that these values are
	 * temporary, they are NOT persisted. This is mainly useful for code overrides
	 * or the like.
	 *
	 * @param array $settings
	 */
	public function setTemporarySettingValues(array $settings)
	{
		$this->settings = array_merge($this->settings, $settings);
	}


	/**
	 * Persist a new value for a setting, and update this as well
	 *
	 * @param string $setting
	 * @param string $value
	 */
	public function setSetting($setting, $value)
	{
		$this->db->beginTransaction();
		try {

			if ($value !== null) {
				$this->db->executeUpdate("
					INSERT INTO settings
						(name, value)
					VALUES
						(?, ?)
					ON DUPLICATE KEY UPDATE
						value = VALUES(value)
				", array($setting, $value));
			} else {
				$this->db->delete('settings', array('name' => $setting));
			}

			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		$this->settings[$setting] = $value;
	}



	/**
	 * Add a group of settings we want to load.
	 *
	 * @param  $group
	 */
	public function loadGroups($group)
	{
		for ($i = 0, $max = func_num_args(); $i < $max; $i++) {
			$group = func_get_arg($i);
			if (!in_array($group, $this->_loaded_groups)) {
				$this->pending_load[] = $group;
			}
		}
	}


	/**
	 * Get the file a setting group is in
	 *
	 * @param $group
	 * @return string
	 */
	public function getGroupFile($group)
	{
		if (strpos($group, '_') !== false) {
			list($key, $name) = Strings::rexplode('_', $group, 2);
		} else {
			$key = $group;
			$name = $group;
		}

		// We dont know about these settings?
		if (!isset($this->settings_paths[$key])) {
			trigger_error("Unknown settings group `$group`", \E_USER_WARNING);
			return null;
		}

		$path = $this->settings_paths[$key] . '/' . $name . '.php';

		return $path;
	}


	/**
	 * When an unknown setting is encountered in a group we haven't loaded yet,
	 * we'll load all pending groups.
	 */
	protected function _loadPendingGroups()
	{
		if (!$this->_pending_groups) {
			return;
		}

		$this->_pending_groups = array_unique($this->_pending_groups);
		$this->_pending_groups = Arrays::removeFalsey($this->_pending_groups);

		#------------------------------
		# Load from filesystem first
		#------------------------------

		foreach ($this->_pending_groups as $group) {
			$path = $this->getGroupFile($group);
			if (!$path || !file_exists($path)) continue;

			$group_settings = require($path);
			$this->settings = array_merge($group_settings, $this->settings);
		}

		unset($group_settings);

		#------------------------------
		# Load from db (user-specified overrides)
		#------------------------------

		if (!$this->_has_loaded_db) {

			$this->_has_loaded_db = true;

			$db_settings = $this->db->fetchAllKeyValue("
				SELECT name, value
				FROM settings
			");

			$this->settings = array_merge($this->settings, $db_settings);

			if (!empty($GLOBALS['DP_CONFIG']['SETTINGS']) && is_array($GLOBALS['DP_CONFIG']['SETTINGS'])) {
				$this->settings = array_merge($this->settings, $GLOBALS['DP_CONFIG']['SETTINGS']);
			}
		}

		$this->_loaded_groups = array_merge($this->_loaded_groups, $this->_pending_groups);
		$this->_pending_groups = array();
	}



	/**
	 * Get the group from the name of a setting.
	 *
	 * @param  string $name
	 * @return string
	 */
	public function getGroupFromName($name)
	{
		$pos = strpos($name, '.');
		if ($pos === false) {
			return false;
		}

		return substr($name, 0, $pos);
	}


	/**
	 * @return \DateTimeZone
	 */
	public function getDefaultTimezone()
	{
		if ($this->default_timezone !== null) {
			return $this->default_timezone;
		}

		try {
			$this->default_timezone = new \DateTimeZone($this->get('core.default_timezone'));
		} catch (\Exception $e) {
			$this->default_timezone = new \DateTimeZone('UTC');
		}

		return $this->default_timezone;
	}


	public function offsetExists($offset)
	{
		return $this->get($offset) !== null;
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException('You cannot set settings');
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException('You cannot unset settings');
	}
}
