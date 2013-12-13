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

use Application\DeskPRO\Entity\Plugin;
use Orb\Util\Util;
use Application\DeskPRO\Controller\AbstractController;
use Application\DeskPRO\App;

abstract class AbstractPluginPackage implements \ArrayAccess
{
	public function initialize()
	{

	}

	/**
	 * Called the first time the plugin is installed.
	 *
	 * @return InstallerAbstract
	 */
	public function getInstaller($install_controller, Plugin $plugin)
	{
		return new InstallerSimple($plugin, $install_controller);
	}

	/**
	 * Called when the plugin is removed.
	 *
	 * @param Plugin $plugin The existing plugin (ie use this to get version)
	 *
	 * @return UninstallerAbstract
	 */
	public function getUninstaller($uninstall_controller, Plugin $plugin)
	{
		return new UninstallerSimple($plugin, $uninstall_controller);
	}

	public function renderConfig(AbstractController $controller, Plugin $plugin, array $errors)
	{
		return $controller->render($this->getName() . ':Admin:config.html.twig', array(
			'plugin' => $plugin,
			'info' => $this,
			'errors' => $errors
		));
	}

	public function processConfig(AbstractController $controller, Plugin $plugin, array &$errors)
	{
		$settings = App::get(App::SERVICE_SETTINGS);
		$prefix = $plugin->id . '.';

		$setting_input = $controller->in->getArray('settings');
		$set_settings = $controller->in->getCleanValueArray('set_settings', 'str_simple', 'discard');

		foreach ($set_settings AS $key) {
			if (!isset($setting_input[$key])) {
				$setting_input[$key] = 0;
			}
		}

		foreach ($setting_input AS $setting => $value) {
			$settings->setSetting("$prefix$setting", $value);
		}

		return true;
	}

	public function runAdminAction(AbstractController $controller, $action, Plugin $plugin)
	{
		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Unknown admin plugin action $action");
	}

	public function runAgentAction(AbstractController $controller, $action, Plugin $plugin)
	{
		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Unknown agent plugin action $action");
	}

	public function runUserAction(AbstractController $controller, $action, Plugin $plugin)
	{
		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Unknown user plugin action $action");
	}

	public function isAvailable()
	{
		return true;
	}
	
	/**
	 * Ge the version
	 *
	 * @return mixed
	 */
	public function getVersion()
	{
		return '1';
	}

	public static function getBasePluginPath()
	{
		return str_replace('/', DIRECTORY_SEPARATOR, DP_WEB_ROOT) . DIRECTORY_SEPARATOR . 'plugins';
	}

	public static function getRelativePluginPath($plugin_path)
	{
		return str_replace(self::getBasePluginPath(), '%PLUGINS%', $plugin_path);
	}

	public function getFile()
	{
		$plugin_path = Util::getClassFilename($this);
		return self::getRelativePluginPath($plugin_path);
	}
	
	/**
	 * Get the path to the Resources directory
	 *
	 * @return string
	 */
	public function getResourcesPath()
	{
		$plugin_path = dirname(Util::getClassFilename($this));
		return self::getRelativePluginPath($plugin_path) . '/Resources';
	}
	
	/**
	 * Get the unique name for the plugin. Use a-zA-Z0-9 only (do not use underscores or settings will not be accessible).
	 * 
	 * @return string
	 */
	abstract public function getName();


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	abstract public function getTitle();


	/**
	 * Get the readable description for this plugin
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return '';
	}

	/**
	 * Get the readable developer name for this plugin
	 *
	 * @return string
	 */
	public function getDeveloper()
	{
		return '';
	}

	/**
	 * Get the  developer's URL
	 *
	 * @return string
	 */
	public function getDeveloperUrl()
	{
		return '';
	}

	public function offsetGet($offset)
	{
		switch ($offset)
		{
			case 'name': return $this->getName();
			case 'title': return $this->getTitle();
			case 'version': return $this->getVersion();
			case 'description': return $this->getDescription();
			case 'developer': return $this->getDeveloper();
			case 'developer_url': return $this->getDeveloperUrl();
			case 'is_available': return $this->isAvailable();
			default: return null;
		}
	}

	public function offsetExists($offset)
	{
		return $this->offsetGet($offset) !== null;
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException('Not supported');
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException('Not supported');
	}
}
