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

namespace Application\DeskPRO\StaticLoader;

use Application\DeskPRO\App;

/**
 * This static loader is used with the DI to get a database connection using run-time values
 * defined in the user config.php file.
 *
 * In sys/config/config.yml, the dbal connection must look something like this:
 * <code>
 * doctrine.dbal:
 *   dp_from_user_config: db
 * </code>
 *
 * Where 'db' is the key of the config we'll fetch for connection params.
 *
 * @see \Application\DeskPRO\DependencyInjection\DoctrineExtension
 */
class DatabaseConnection
{
	static function getConnection()
	{
		$args = func_get_args();

		$key = isset($args[0]['dp_from_user_config']) ? isset($args[0]['dp_from_user_config']) : 'db';

		$args[0] = array_merge($args[0], \Application\DeskPRO\App::getConfig($key));

		$conn = call_user_func_array(array('Doctrine\DBAL\DriverManager', 'getConnection'), $args);

		if (App::has('doctrine.dbal.logger')) {
			
		}
	}
}