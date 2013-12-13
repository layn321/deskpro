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

namespace Application\DeskPRO\Plugin;

class PluginRepository
{
	/**
	 * @var array
	 */
	protected $plugins;

	/**
	 * @param \Application\DeskPRO\Entity\Plugin[] $plugins
	 */
	public function __construct(array $plugins)
	{
		$this->plugins = array();

		foreach ($plugins as $plugin) {
			if ($plugin->enabled) {
				$this->plugins[strtolower($plugin->getId())] = $plugin;
			}
		}
	}


	/**
	 * @param string $id
	 * @return bool
	 */
	public function isPluginInstalled($plugin_id)
	{
		$plugin_id = strtolower($plugin_id);
		return isset($this->plugins[$plugin_id]);
	}


	/**
	 * @param $id
	 * @return \Application\DeskPRO\Entity\Plugin
	 * @throws \InvalidArgumentException
	 */
	public function getPlugin($plugin_id)
	{
		$plugin_id = strtolower($plugin_id);

		if (!isset($this->plugins[$plugin_id])) {
			throw new \InvalidArgumentException("Unknown plugin $plugin_id");
		}

		return $this->plugins[$plugin_id];
	}


	/**
	 * Get a plugin service. Prefix the id with the plugin name and it'll be looked up on that plugin.
	 *
	 * @param string $id
	 */
	public function getPluginService($id)
	{
		list ($plugin_id, $id) = explode('.', $id, 2);

		return $this->getPlugin($plugin_id)->getPluginService($id);
	}
}