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

use Application\DeskPRO\App;

/**
 * These are pre-defined labels that are allowed to be used.
 *
 */
class LabelDef extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var string
	 */
	protected $label_type;

	/**
	 * @var string
	 */
	protected $label;

	protected $total = 0;

	/**
	 * Get the name of the entity used to store label associations for this type.
	 *
	 * @return string
	 */
	public function getLabelEntityName()
	{
		return App::getEntityRepository('DeskPRO:LabelDef')->getLabelEntityFromType($this->label_type);
	}

	/**
	 * Get the table name used to store label associations for this type.
	 *
	 * @return string
	 */
	public function getLabelTable()
	{
		$ent = App::getEntityRepository('DeskPRO:LabelDef')->getLabelEntityFromType($this->label_type);
		$class = App::getEntityClass($ent);
		$table = $class::getTableName();

		return $table;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\LabelDef';
		$metadata->setPrimaryTable(array(
			'name' => 'label_defs',
			'indexes' => array(
				'type_total_idx' => array('columns' => array('label_type', 'total'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'label_type', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'label_type', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'label', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'label', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'total', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'total' ));
	}
}
