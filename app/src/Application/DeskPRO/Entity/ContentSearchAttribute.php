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
 * Attributes or various other fields that are searchable on some type
 *
 */
class ContentSearchAttribute extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var string
	 */
	protected $object_type;

	/**
	 * @var int
	 */
	protected $object_id = null;

	/**
	 * The name of the attribute like "somefield"
	 *
	 */
	protected $attribute_id;

	/**
	 * The searchable content of the attribuet
	 *
	 */
	protected $content;



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array( 'name' => 'content_search_attribute', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'object_type', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_type', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'object_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'attribute_id', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'attribute_id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'content', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'content', 'id' => true, ));
	}
}
