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
 * @subpackage Plugins
 */

namespace Application\DeskPRO\Plugin\PluginPackage;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Plugin;
use Orb\Util\Util;

/**
 * A generic installer/uninstaller class that you can extend and use from the PluginPackage
 * to help install the plugin.
 */
abstract class UninstallerAbstract
{
	/**
	 * @var string
	 */
	protected $plugin_package_name;

	/**
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * The install controller
	 */
	protected $controller;

	/**
	 * @var int
	 */
	protected $steps = null;


	public final function __construct($plugin, $controller)
	{
		$this->plugin_package_name = $plugin['package_class'];
		$this->plugin = $plugin;
		$this->controller = $controller;
		$this->init();
	}

	/**
	 * Override ths method to run your own init code
	 */
	protected function init() {}


	/**
	 * This executes stepX methods on this object, and must return a response as if
	 * this were a real controller. Use $controller to do controller things.
	 *
	 * Route: admin_plugins_uninstall
	 * Params: id: this_plugins_id, step: the_step
	 *
	 * Templates should extend: AdminBundle:Plugins:uninstall_step.html.twig
	 * And overwrite the block 'step_content'
	 */
	public function runStep($step)
	{
		$session = $this->controller->session;

		$name = $this->plugin->id;
		$session_key = $name . '_uninstall';
		if (isset($session[$session_key])) {
			$session_data = $session[$session_key];
			if (!empty($session_data['installer_data'])) {
				$this->installer_data = $session_data['installer_data'];
			}
		}

		if (!$this->steps OR $step > $this->steps) {

			App::getOrm()->beginTransaction();
			$this->preUninstall();
			$this->doUninstall();

			$step_method = 'stepUninstall';
			unset($session[$session_key]);

			$ret = $this->$step_method();

			App::getOrm()->commit();
		} else {
			$step_method = 'step' . $step;
			$ret = $this->$step_method();
			$session[$session_key] = array(
				'insert_settings' => $this->insert_settings,
				'installer_data' => $this->installer_data
			);
		}

		return $ret;
	}


	/**
	 * Empty hook to cleanup any non-standard items
	 */
	protected function preUninstall() { }


	/**
	 * Performs the install by inserting the plugin record, settings,
	 * and listeners.
	 */
	protected function doUninstall()
	{
		$plugin = $this->plugin;
		$db = App::getDb();

		$db->executeUpdate("
			DELETE FROM settings WHERE name LIKE '".$this->plugin['id'].".%'
		");

		/*$db->executeUpdate("
			DELETE FROM templates WHERE name LIKE '".$this->plugin['id'].":%'
		");

		$db->executeUpdate("
			DELETE FROM plugin_listeners WHERE plugin_id = " . $db->quote($this->plugin['id']) . "
		");*/

		$finder = new \Symfony\Component\Finder\Finder();
		$finder->name('*.php')->notName('*Abstract*')->in(DP_ROOT . '/src/Application/DeskPRO/DataSync/Plugin');

		foreach ($finder AS $file) {
			/** @var $file \SplFileInfo */
			$handler = $file->getBasename('.php');
			$class = '\Application\DeskPRO\DataSync\Plugin\\' . $handler;

			/** @var $sync \Application\DeskPRO\DataSync\Plugin\AbstractPlugin */
			$sync = new $class('', $plugin);
			$sync->deleteLiveData();
		}

		App::getOrm()->remove($plugin);
		App::getOrm()->flush();
	}



	/**
	 * After the plugin is uninstalled
	 */
	public function stepUninstall()
	{
		return $this->controller->redirectRoute('admin_plugins');
	}


	/**
	 * Insert an installer preference that is saved between page loads.
	 *
	 * @param  $name
	 * @param  $value
	 * @return void
	 */
	public function setInstallerPref($name, $value)
	{
		$this->installer_data[$name] = $value;
	}


	/**
	 * Get the value of an installer pref
	 *
	 * @param  $name
	 * @param $default
	 * @return array|null
	 */
	public function getInstallerPref($name, $default = null)
	{
		return isset($this->installer_data[$name]) ? $this->installer_data[$name] : $default;
	}
}
