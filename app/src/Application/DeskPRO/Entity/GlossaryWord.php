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

/**
 * Glossary
 *
 */
class GlossaryWord extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $word;

	/**
	 * @var GlossaryWordDefinition
	 */
	protected $definition;

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
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\GlossaryWord';
		$metadata->setPrimaryTable(array( 'name' => 'glossary_words', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'word', 'type' => 'string', 'length' => 255, 'unique' => true, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'word', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'definition', 'targetEntity' => 'Application\\DeskPRO\\Entity\\GlossaryWordDefinition', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'definition_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
