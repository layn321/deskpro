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
 * @category Entities
 */

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * A scraper is something that fetches data from a remote resource. This is the abstract
 * scraper type, but each different type of scrape defines its own entity and may have
 * its own dramatically different processing/handling.
 *
 * Actual scrapers are also responsible for how to store any scraped data (hence there is no
 * use in an abstract ScraperData class).
 *
 */
abstract class Scraper
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 * 
	 */
	protected $id;

	/**
	 * The handler classname. A handler is created from this usersource, and is responsible for
	 * creating all the resources needed for a scraper to do its job.
	 *
	 * @var string
	 */
	protected $handler_class;

	/**
	 * Options we'll pass to the handler
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * True if this scraper is enabled
	 *
	 * @var bool
	 */
	protected $is_enabled = true;

	/**
	 * @var Application\DeskPRO\Scraper\Handler\HandlerAbstract
	 */
	protected $_handler_instance = null;



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->isMappedSuperclass = true; 
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE); 
		$metadata->setPrimaryTable(array( 'name' => 'Scraper', )); 
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
	}
}
