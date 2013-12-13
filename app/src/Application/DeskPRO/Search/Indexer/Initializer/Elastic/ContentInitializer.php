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
 * @subpackage Search
 */

namespace Application\DeskPRO\Search\IndexInitializer\Elastic;

use Application\DeskPRO\App;
use Application\DeskPRO\Search\IndexInitializer\ContentInitializer as BaseContentInitializer;

class ContentInitializer extends BaseContentInitializer
{
	public function preRun()
	{
		#------------------------------
		# Recreate index
		#------------------------------

		$index = $this->adapter->getClient()->getIndex('content');
		try {
			$index->delete();
			$this->logger->log('Deleted old index', Logger::INFO);
		} catch (\Elastica_Exception_Response $e) {
			// probably means it didnt exist to begin with
			$this->logger->log('Exception while deleting old index. Probably can be ignored. Message: ' . $e->getMessage(), Logger::NOTICE);
		}

		try {
			$index->create();
			$this->logger->log('Created index', Logger::INFO);
		} catch (\Elastica_Exception_Response $e) {
			$this->logger->log('Could not create index. Aborting. Error: ' . $e->getMessage(), Logger::ERR);
			throw $e;
		}
	}

}
