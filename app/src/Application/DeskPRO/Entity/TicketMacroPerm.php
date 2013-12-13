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
 * Ticket macro permissions
 */
class TicketMacroPerm extends \Application\DeskPRO\Domain\DomainObject
{
	const TYPE_DEPARTMENT = 'department';
	const TYPE_USERGROUP = 'usergroup';
	const TYPE_PERSON = 'person';

	/**
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\TicketMacro
	 */
	protected $macro = null;

	/**
	 * The type of object this is attached to (should be the table name of
	 * the super type, eg: tickets, people, organizations).
	 *
	 * @var string
	 */
	protected $object_type;

	/**
	 * The ID of the object this is attached to.
	 *
	 * @var int
	 */
	protected $object_id;

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
		$metadata->setPrimaryTable(array( 'name' => 'ticket_macros_perms', 'indexes' => array( 'object_idx' => array( 'columns' => array( 0 => 'object_type', 1 => 'object_id', ), ), ), ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'object_type', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_type', ));
		$metadata->mapField(array( 'fieldName' => 'object_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_id', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'macro', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketMacro', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'macro_id', 'referencedColumnName' => 'id', 'unique' => false, 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
