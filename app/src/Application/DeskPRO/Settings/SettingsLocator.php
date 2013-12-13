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
 * This class locates a settings file based on the group name
 */
class SettingsLocator implements \ArrayAccess
{
	protected $settings_paths = array();
	protected $failed_paths = array();

	/**
	 * @param array $settings_paths Initial array of known paths
	 */
	public function __construct(array $settings_paths = array())
	{
		$this->settings_paths = $settings_paths;
	}


	/**
	 * When a path is not found, we can try to look it up here. Allows for lazy-mapping.
	 *
	 * @param  $key
	 * @return void
	 */
	public function initPath($key)
	{
		// Already tried to locate
		if (isset($this->settings_paths[$key]) OR isset($this->failed_paths[$key])) {
			return;
		}

		$plugin_manager = App::get('deskpro.plugin_manager');
		if ($plugin_manager->hasPlugin($key)) {
			$this->settings_paths[$key] = $plugin_manager->getResourcesPath($key) . '/settings';
		} else {
			$this->failed_paths[$key] = true;
		}
	}


	public function offsetExists($offset)
	{
		$this->initPath($offset);
		return isset($this->settings_paths);
	}

	public function offsetGet($offset)
	{
		$this->initPath($offset);
		return $this->settings_paths[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->settings_paths[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->settings_paths[$offset]);
	}
}
