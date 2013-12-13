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
 * A persons contact data
 *
 */
class PersonTwitterUser extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var bigint
	 */
	protected $twitter_user_id;

	protected $screen_name;

	protected $is_verified = false;

	protected $oauth_token = null;
	protected $oauth_token_secret = null;



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\PersonTwitterUser';
		$metadata->setPrimaryTable(array(
			'name' => 'people_twitter_users',
			'uniqueConstraints' => array(
				'unique_key_idx' => array('columns' => array('person_id', 'screen_name'))
			),
			'indexes' => array(
				'screen_name_idx' => array('columns' => array('screen_name')),
				'twitter_user_id_idx' => array('columns' => array('twitter_user_id')),
			),
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'screen_name', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'screen_name', ));
		$metadata->mapField(array( 'fieldName' => 'is_verified', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_verified', ));
		$metadata->mapField(array( 'fieldName' => 'oauth_token', 'type' => 'string', 'length' => 4000, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'oauth_token', 'dpApi' => false, 'dpqlAccess' => false ));
		$metadata->mapField(array( 'fieldName' => 'oauth_token_secret', 'type' => 'string', 'length' => 4000, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'oauth_token_secret', 'dpApi' => false, 'dpqlAccess' => false ));
		$metadata->mapField(array( 'fieldName' => 'twitter_user_id', 'type' => 'bigint', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'twitter_user_id', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
