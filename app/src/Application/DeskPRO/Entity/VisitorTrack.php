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

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Util;

/**
 * Tracks pages a user has been on
 */
class VisitorTrack extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor;

	/**
	 * @var bool
	 */
	protected $is_new_visit = false;

	/**
	 * @var string
	 */
	protected $page_title = '';

	/**
	 * @var string
	 */
	protected $page_url = '';

	/**
	 * @var string
	 */
	protected $ref_page_url = '';

	/**
	 * The users user agent string
	 *
	 * @var string
	 */
	protected $user_agent = '';

	/**
	 * The users user agent string
	 *
	 * @var string
	 */
	protected $user_browser = '';

	/**
	 * The users user agent string
	 *
	 * @var string
	 */
	protected $user_os = '';

	/**
	 * The users IP address
	 *
	 * @var string
	 */
	protected $ip_address;

	/**
	 * @var string
	 */
	protected $geo_continent = null;

	/**
	 * @var string
	 */
	protected $geo_country = null;

	/**
	 * @var string
	 */
	protected $geo_region = null;

	/**
	 * @var string
	 */
	protected $geo_city = null;

	/**
	 * @var string
	 */
	protected $geo_long = null;

	/**
	 * @var string
	 */
	protected $geo_lat = null;

	/**
	 * @var boolean
	 */
	protected $is_soft_track = false;

	/**
	 * @var array
	 */
	protected $data = null;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function __construct()
	{
		$this->setModelField('date_created', new \DateTime());
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\VisitorTrack';
		$metadata->setPrimaryTable(array(
			'name' => 'visitor_tracks',
			'indexes' => array(
				'idx1' => array(
					'columns' => array('date_created', 'is_new_visit')
				),
			)
		));
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'is_new_visit', 'type' => 'boolean',  'nullable' => false, 'columnName' => 'is_new_visit', ));
		$metadata->mapField(array( 'fieldName' => 'page_title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'page_title', ));
		$metadata->mapField(array( 'fieldName' => 'page_url', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'page_url', ));
		$metadata->mapField(array( 'fieldName' => 'ref_page_url', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'ref_page_url', ));
		$metadata->mapField(array( 'fieldName' => 'user_agent', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'user_agent', ));
		$metadata->mapField(array( 'fieldName' => 'user_browser', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'user_browser', ));
		$metadata->mapField(array( 'fieldName' => 'user_os', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'user_os', ));
		$metadata->mapField(array( 'fieldName' => 'ip_address', 'type' => 'string', 'length' => 80, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ip_address', ));
		$metadata->mapField(array( 'fieldName' => 'geo_continent', 'type' => 'string', 'length' => 2, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'geo_continent', ));
		$metadata->mapField(array( 'fieldName' => 'geo_country', 'type' => 'string', 'length' => 2, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'geo_country', ));
		$metadata->mapField(array( 'fieldName' => 'geo_region', 'type' => 'string', 'length' => 2, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'geo_region', ));
		$metadata->mapField(array( 'fieldName' => 'geo_city', 'type' => 'string', 'length' => 2, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'geo_city', ));
		$metadata->mapField(array( 'fieldName' => 'geo_long', 'type' => 'decimal','precision' => 16, 'scale' => 8, 'nullable' => true, 'columnName' => 'geo_long', ));
		$metadata->mapField(array( 'fieldName' => 'geo_lat', 'type' => 'decimal', 'precision' => 16, 'scale' => 8, 'nullable' => true, 'columnName' => 'geo_lat', ));
		$metadata->mapField(array( 'fieldName' => 'is_soft_track', 'type' => 'boolean', 'nullable' => false, 'columnName' => 'is_soft_track', ));
		$metadata->mapField(array( 'fieldName' => 'data', 'type' => 'array', 'nullable' => true, 'columnName' => 'data', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'visitor', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Visitor', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'visitor_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
