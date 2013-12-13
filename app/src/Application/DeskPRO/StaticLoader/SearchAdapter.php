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
 * This static loader is used with the DI to create a search adapter.
 *
 * @see \Application\DeskPRO\DependencyInjection\SearchExtension
 */
class SearchAdapter
{
	public static function getSearchAdapter()
	{
		$adapter_name = strtolower(App::getConfig('search.adapter'));
		$config = App::getConfig('search.options');

		switch ($adapter_name) {
			case 'elastic':
				$adapter = \Application\DeskPRO\Search\Adapter\ElasticAdapter::create($config['host'], $config['port']);
				break;

			default:
				$adapter = new \Application\DeskPRO\Search\Adapter\MysqlAdapter();
				break;
		}

		if (!$adapter) {
			throw new \Exception('No search adapter');
		}

		return $adapter;
	}
}
