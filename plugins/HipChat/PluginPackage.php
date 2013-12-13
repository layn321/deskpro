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

namespace HipChat;

use Application\DeskPRO\Entity\Plugin;
use Application\DeskPRO\Plugin\PluginPackage as CorePluginPackage;
use Application\DeskPRO\Controller\AbstractController;
use Application\DeskPRO\App;

class PluginPackage extends CorePluginPackage\AbstractPluginPackage
{
	/**
	 * Get the version
	 *
	 * @return mixed
	 */
	public function getVersion()
	{
		return '1.0';
	}

	
	/**
	 * Get the unique name for the plugin. Use a-zA-Z0-9 only (do not use underscores or settings will not be accessible).
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'HipChat';
	}


	/**
	 * Get the readable title for this plugin
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return "HipChat";
	}

	public function getDescription()
	{
		return 'Allows you to add a ticket trigger that inserts a message into a HipChat room when matched.';
	}

	public function getDeveloper()
	{
		return 'DeskPRO';
	}

	public function getDeveloperUrl()
	{
		return 'https://www.deskpro.com/integrations/hipchat/';
	}
}