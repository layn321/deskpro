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
abstract class InstallerAbstract
{
	/**
	 * @var Plugin
	 */
	protected $plugin;
	
	/**
	 * @var string
	 */
	protected $plugin_package_name;
	
	/**
	 * @var array
	 */
	protected $insert_settings = array();

	/**
	 * @var array
	 */
	protected $installer_data = array();

	/**
	 * The install controller
	 */
	protected $controller;

	/**
	 * @var int
	 */
	protected $steps = null;

	
	public final function __construct(Plugin $plugin, $controller)
	{
		$this->plugin = $plugin;
		$this->plugin_package_name = $plugin['package_class'];
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
	 * Route: admin_plugins_install
	 * Params: id: this_plugins_id, step: the_step
	 *
	 * Templates should extend: AdminBundle:Plugins:install_step.html.twig
	 * And overwrite the block 'step_content'
	 */
	public function runStep($step)
	{
		$session = $this->controller->session;

		$session_key = $this->plugin->id . '_install';
		if (isset($session[$session_key])) {
			$session_data = $session[$session_key];
			if (!empty($session_data['insert_settings'])) {
				$this->insert_settings = $session_data['insert_settings'];
			}
			if (!empty($session_data['installer_data'])) {
				$this->installer_data = $session_data['installer_data'];
			}
		}

		if (!$this->steps OR $step > $this->steps) {

			App::getOrm()->beginTransaction();
			$plugin = $this->doInstall();

			$step_method = 'stepInstall';
			unset($session[$session_key]);

			$ret = $this->$step_method($plugin);

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
	 * Performs the install by inserting the plugin record, settings,
	 * and listeners.
	 */
	protected function doInstall()
	{
		$plugin = $this->plugin;
		$orm = App::getOrm();

		$orm->beginTransaction();

		$orm->persist($plugin);
		$orm->flush();

		foreach ($this->insert_settings as $k => $v) {
			App::getEntityRepository('DeskPRO:Setting')->updateSetting($k, $v);
		}

		$plugin->importSyncData();

		$plugin_listeners = $this->getPluginListeners();
		foreach ($plugin_listeners as $plugin_listener_info) {
			if (is_array($plugin_listener_info)) {
				$plugin_listener = new \Application\DeskPRO\Entity\PluginListener();
				$plugin_listener->fromArray($plugin_listener_info);
			}
			$plugin->addPluginListener($plugin_listener);
		}

		$orm->flush();

		$this->postInstall($plugin);

		$orm->commit();

		App::get('deskpro.plugin_manager')->addPlugin($plugin);

		return $plugin;
	}

	public function postInstall($plugin) { }



	/**
	 * After the plugin is installed, this method is finally run with the inserted
	 * $plugin. You can do more work here, just return a response (ie confirmation/success page)
	 */
	public function stepInstall(Plugin $plugin)
	{
		return $this->controller->redirectRoute('admin_plugins_plugin', array('plugin_id' => $plugin->id));
	}


	/**
	 * Get an array of plugin listeners
	 *
	 * @var array
	 */
	protected function getPluginListeners()
	{
		return array();
	}


	
	/**
	 * Should be called from init() to set the number of steps the installer has.
	 * 0 or the default (null) steps means jumping right to the end.
	 */
	protected function setSteps($steps)
	{
		if ($this->steps !== null) {
			throw new \BadMethodCallException("Steps has already been set");
		}

		$this->steps = (int)$steps;
	}

	
	/**
	 * Insert a user-defind setting
	 * 
	 * @param  $name
	 * @param  $value
	 */
	public function insertUserSetting($name, $value)
	{
		$this->insert_settings[$name] = $value;
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
