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
 * @copyright Copyright (c) 2011 DeskPRO (http://www.deskpro.com/)
 */
namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Deal entity definition
 *
 */

abstract class DealMapper extends \Application\DeskPRO\Domain\DomainObject
{
    /**
     * The unique ID
     *
     * @var int
     *
     */
    protected $id = null;

    /**
     * @var \Application\DeskPRO\Entity\Deal
     */
    protected $deal;

    /**
     * @var string
     */
    protected $linktype;

    /**
     * @var int
     */
    protected $typeid;

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


	public static function x_loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\DealStage';
		$metadata->setPrimaryTable(array(
			'name' => 'deals_mapper',
			'indexes' => array(
				'link_idx' => array('columns' => array('linktype', 'typeid')),
			),
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'linktype', 'type' => 'string', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'type', ));
		$metadata->mapField(array( 'fieldName' => 'typeid', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'typeid', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'deal', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Deal', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'dealid', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
