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
use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * Log of agent and admin logins
 */
class LoginLog extends \Application\DeskPRO\Domain\DomainObject
{
	const AREA_ADMIN = 'admin';
	const AREA_AGENT = 'agent';

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * Who the login log is for
	 *
	 * @var string
	 */
	protected $person;

	/**
	 * Where they logged into
	 *
	 * @var string
	 */
	protected $area;

	/**
	 * If the login was successful or failure
	 *
	 * @var bool
	 */
	protected $is_success = true;

	/**
	 * The IP address of the user
	 *
	 * @var string
	 */
	protected $ip_address;

	/**
	 * The traced hostname of the IP address
	 *
	 * @var string
	 */
	protected $hostname;

	/**
	 * The user agent of the user
	 *
	 * @var string
	 */
	protected $user_agent;

	/**
	 * @var string
	 */
	protected $note = '';

	/**
	 * @var bool
	 */
	protected $via_cookie = false;

	/**
	 * The date the login was attempted
	 *
	 * @var \DateTime
	 */
	protected $date_created;

	public function __construct()
	{
		$this->date_created = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\\DeskPRO\\EntityRepository\\LoginLog';
		$metadata->setPrimaryTable(array('name' => 'login_log', 'indexes' => array('person_id_idx' => array('columns' => array('person_id')))));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'area', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'area', ));
		$metadata->mapField(array( 'fieldName' => 'is_success', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_success', ));
		$metadata->mapField(array( 'fieldName' => 'ip_address', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ip_address', ));
		$metadata->mapField(array( 'fieldName' => 'hostname', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'hostname', ));
		$metadata->mapField(array( 'fieldName' => 'user_agent', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'user_agent', ));
		$metadata->mapField(array( 'fieldName' => 'note', 'type' => 'string', 'length' => 1000, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'note', ));
		$metadata->mapField(array( 'fieldName' => 'via_cookie', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'via_cookie', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
