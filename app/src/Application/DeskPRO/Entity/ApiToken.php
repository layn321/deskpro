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
 * @subpackage ApiBundle
 * @category Entities
 */

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Orb\Util\Strings;
use Orb\Util\Arrays;

class ApiToken extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var \DateTime|null
	 */
	protected $date_expires = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;


	public function __construct()
	{
		$this['token'] = Strings::random(25, Strings::CHARS_KEY);
	}

	public function regenerateToken()
	{
		$this['token'] = Strings::random(25, Strings::CHARS_KEY);
	}



	/**
	 * Get a "key string". This is a combined ID and code like id:code
	 * that is used in auth lookups.
	 *
	 * @return string
	 */
	public function getKeyString()
	{
		return $this->person->id . ':' . $this->token;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\ApiToken';
		$metadata->setPrimaryTable(array( 'name' => 'api_token', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'token', 'type' => 'string', 'length' => 25, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'token', ));
		$metadata->mapField(array( 'fieldName' => 'date_expires', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_expires', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'id' => true, 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
