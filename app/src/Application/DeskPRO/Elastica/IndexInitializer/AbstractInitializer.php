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
 * @subpackage Elastica
 */

namespace Application\DeskPRO\Elastica\IndexInitializer;

use Application\DeskPRO\Elastica\ElasticaManager;
use Orb\Log\Logger;

/**
 * An initializer goes through and resets an index, and indexes all existing content.
 *
 * Type's are responsible for doing transformations as well as inserting into the index,
 * so these initializers are basically fetchers that also delete/create the actual ES index as well.
 */
abstract class AbstractInitializer
{
	/**
	 * @var \Application\DeskPRO\Elastica\ElasticaManager
	 */
	protected $manager;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger = null;

	public function __construct(ElasticaManager $manager, Logger $log = null)
	{
		$this->manager = $manager;

		// Empty logger if none provided
		if (!$log) {
			$log = new \Orb\Log\Logger();
		}

		$this->logger = $log;
	}


	/**
	 * Index all content
	 */
	abstract public function run();
}
