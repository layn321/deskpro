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

namespace DeskproPlugins\Highrise;

use Application\DeskPRO\Entity\Plugin;
use Application\DeskPRO\Plugin\PluginPackage\AbstractPluginPackage;
use DeskproPlugins\Highrise\PluginPackage\Installer;
use DeskproPlugins\Highrise\PluginPackage\Uninstaller;

class PluginPackage extends AbstractPluginPackage
{
	/**
	 * Called the first time the plugin is installed.
	 *
	 * @return InstallerAbstract
	 */
	public static function getInstaller($install_controller, Plugin $plugin)
	{
		$installer = new Installer($plugin, $install_controller);
		return $installer;
	}

	/**
	 * Called whent he plugin exists in the database, but the source
	 * is a newer version.
	 *
	 * @param Plugin $plugin The existing plugin (ie use this to get version)
	 * @return void
	 */
	public static function getUpgrader($upgrade_controller, Plugin $plugin)
	{

	}

	/**
	 * Called when the plugin is removed.
	 *
	 * @param Plugin $plugin The existing plugin (ie use this to get version)
	 * @return UninstallerAbstract
	 */
	public static function getUninstaller($uninstall_controller, Plugin $plugin)
	{
		$installer = new Installer('DeskproPlugins\\Highrise\\PluginPackage', $install_controller);
		return $install_controller;
	}

	public static function isAvailable()
	{
		return false;
	}
	
	/**
	 * Ge tthe version
	 *
	 * @return mixed
	 */
	public static function getVersion()
	{
		return '1';
	}

	
	/**
	 * Get the unique name for the plugin
	 *
	 * @return string
	 */
	public static function getName()
	{
		return 'dp_highrise';
	}


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	public static function getTitle()
	{
		return "Highrise Integration";
	}


	/**
	 * Get the readable description for this plugin
	 *
	 * @return string
	 */
	public static function getDescription()
	{
		return "Look up user profile data in <a href=\"http://highrisehq.com/\">highrise</a> and display this information in fields on ticket and profile views.";
	}
}