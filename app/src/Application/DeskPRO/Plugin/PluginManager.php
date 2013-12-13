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
 * @subpackage Addons
 */

namespace Application\DeskPRO\Plugin;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Plugin;
use Application\DeskPRO\Plugin\PluginPackage\AbstractPluginPackage;

use Symfony\Component\Finder\Finder;

/**
 * This finds plugins that exist in the DeskPRO file structure
 */
class PluginManager
{
	protected $em;
	protected $plugins = null;
	protected $packages = array();

	public function __construct($em)
	{
		$this->em = $em;
	}

	public function initialize()
	{
		if ($this->plugins !== null) return;

		$this->plugins = $this->em->createQuery("
			SELECT p
			FROM DeskPRO:Plugin p INDEX BY p.id
		")->execute();

		foreach ($this->plugins AS $key => $plugin) {
			$package = $this->_initializePlugin($plugin);
			if (!$package)
			{
				unset($this->plugins[$key]);
			}
		}
	}

	protected function _initializePlugin(Plugin $plugin)
	{
		$class = $plugin->package_class;

		if (!class_exists($class, false)) {
			$file = $plugin->getCanonicalPackageClassFile();
			if (!file_exists($file)) {
				return false;
			}
			require $file;
		}

		$package = new $class();
		$package->initialize();
		$this->packages[$plugin->id] = $package;

		return $package;
	}

	public function addPlugin($plugin)
	{
		$this->initialize();

		if (isset($this->plugins[$plugin['id']]) && $this->plugins[$plugin['id']] === $plugin) {
			// already added
			return $this->packages[$plugin->id];
		}

		$package = $this->_initializePlugin($plugin);
		if ($package)
		{
			$this->plugins[$plugin['id']] = $plugin;
			return $package;
		} else {
			return false;
		}
	}

	public function hasPlugin($plugin_id)
	{
		$this->initialize();

		return isset($this->plugins[$plugin_id]);
	}

	public function getPlugin($id)
	{
		return isset($this->plugins[$id]) ? $this->plugins[$id] : false;
	}

	public function getPackage($id)
	{
		return isset($this->plugins[$id]) ? $this->packages[$id] : false;
	}

	public function getBundle($plugin_id)
	{
		$this->initialize();

		if (!isset($this->plugins[$plugin_id])) {
			return null;
		}

		return new PluginBundle($this->plugins[$plugin_id]);
	}

	public function getResourcesPath($plugin_id)
	{
		$this->initialize();

		if (!isset($this->plugins[$plugin_id])) {
			return null;
		}

		return $this->plugins[$plugin_id]->getCanonicalResourcesPath();
	}
}
