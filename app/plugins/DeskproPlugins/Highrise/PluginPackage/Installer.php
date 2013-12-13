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

namespace DeskproPlugins\Highrise\PluginPackage;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Plugin;
use Orb\Util\Util;

use Application\DeskPRO\Plugin\PluginPackage\InstallerAbstract;

/**
 * A generic installer/uninstaller class that you can extend and use from the PluginPackage
 * to help install the plugin.
 */
class Installer extends InstallerAbstract
{
	protected $in;

	protected $inserted_display_field;

	protected function init()
	{
		$this->setSteps(1);
		$this->in = $this->controller->in;
	}

	public function step1()
	{
		if ($this->in->getBool('process')) {
			$this->insertUserSetting('dp_highrise.api_auth_key', $this->in->getBool('api_auth_key'));
			$this->insertUserSetting('dp_highrise.highrise_url', $this->in->getBool('highrise_url'));
			return $this->controller->redirectRoute('admin_plugins_install_step', array('plugin_id' => 'dp_highrise', 'step' => 99));
		}

		return $this->controller->render('dp_highrise:Install:install_step_1.html.twig');
	}

	public function stepInstall($plugin)
	{
		return $this->controller->render('dp_highrise:Install:install_done.html.twig');
	}

	public function postInstall($plugin)
	{
		// This is the special display field we'll use to inject the results into the viewticket form
		$this->inserted_display_field = new \Application\DeskPRO\Entity\CustomDefTicket();
		$this->inserted_display_field->fromArray(array(
			'plugin' => $plugin,
			'title' => 'Highrise User Information',
			'handler_class' => 'Application\\DeskPRO\\CustomFields\\Handler\\Display',
		));

		App::getOrm()->persist($this->inserted_display_field);
		App::getOrm()->flush();
	}

	/**
	 * Get an array of plugin listeners
	 *
	 * @var array
	 */
	public function getPluginListeners()
	{
		return array(
			array(
				'event_name' => 'DeskPRO_onDisplayFieldRenderHtml',
				'event_options' => array(
					'field_table' => 'custom_def_tickets',
					'field_id'    => $this->inserted_display_field['id']
				),
				'listener_class' => 'DeskproPlugins\\Highrise\\ListenerHandler\\FetchHighriseData'
			)
		);
	}
}