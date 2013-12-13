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
 * A custom field definition
 *
 */
class CustomDefChat extends CustomDefAbstract
{
	/**
	 * @var CustomDefChat
	 */
	protected $parent = null;

	/**
	 * Field children
	 *
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $children = null;



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\CustomDefChat';
		$metadata->setPrimaryTable(array( 'name' => 'custom_def_chat', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'js_class', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'js_class', ));
		$metadata->mapField(array( 'fieldName' => 'has_form_template', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'has_form_template', ));
		$metadata->mapField(array( 'fieldName' => 'has_display_template', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'has_display_template', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'description', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'description', ));
		$metadata->mapField(array( 'fieldName' => 'handler_class', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'handler_class', ));
		$metadata->mapField(array( 'fieldName' => 'options', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'options', ));
		$metadata->mapField(array( 'fieldName' => 'is_user_enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_user_enabled', ));
		$metadata->mapField(array( 'fieldName' => 'is_enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_enabled', ));
		$metadata->mapField(array( 'fieldName' => 'display_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'display_order', ));
		$metadata->mapField(array( 'fieldName' => 'default_value', 'type' => 'string', 'length' => 500, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'default_value' ));
		$metadata->mapField(array( 'fieldName' => 'is_agent_field', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_agent_field', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'parent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDefChat', 'mappedBy' => NULL, 'inversedBy' => 'children', 'joinColumns' => array( 0 => array( 'name' => 'parent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'children', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDefChat', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'parent',  'orderBy' => array( 'display_order' => 'ASC', ), ));
		$metadata->mapManyToOne(array( 'fieldName' => 'plugin', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Plugin', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'plugin_id', 'referencedColumnName' => 'id', 'unique' => false, 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
