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
 * Templates used in the system
 *
 */
class Phrase extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 */
	protected $id = null;

	/**
	 * The language this phrase belongs to
	 *
	 * @var Language
	 */
	protected $language;

	/**
	 * The name of the phrase
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * Phrases can belong to groups. The group is the string
	 * before the first dot in the name. deskpro.profile, the group is 'deskpro'
	 *
	 * @var string
	 */
	protected $groupname;

	/**
	 * @var string
	 */
	protected $phrase;

	/**
	 * @var string
	 */
	protected $original_phrase;

	/**
	 * @var string
	 */
	protected $original_hash;

	/**
	 * Is this phrase marked as outdated?
	 *
	 * This happens when we detect the original hash stored is different from what
	 * is on the filesystem.
	 *
	 * @var bool
	 */
	protected $is_outdated = false;

	/**
	 * @var \DateTime
	 */
	protected $created_at;

	/**
	 * @var \DateTime
	 */
	protected $updated_at;

	public function __construct()
	{
		$this->setModelField('created_at', $this->updated_at = new \DateTime());
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setName($name)
	{
		$this->setModelField('name', $name);

		$groupname = Strings::rexplode('.', $name, 2);
		$groupname = array_shift($groupname);

		if ($groupname) {
			$this->setModelField('groupname', $groupname);
		}
	}

	public function incUpdatedAt()
	{
		$this->setModelField('updated_at', new \DateTime());
	}

	public function __toString()
	{
		return $this->phrase;
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Phrase';
		$metadata->setPrimaryTable(array(
			'name' => 'phrases',
			'indexes' => array(
				'name_idx' => array('columns' => array('groupname', 'name'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->addLifecycleCallback('incUpdatedAt', 'preUpdate');
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'groupname', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'groupname', ));
		$metadata->mapField(array( 'fieldName' => 'phrase', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'phrase', ));
		$metadata->mapField(array( 'fieldName' => 'original_phrase', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'original_phrase', ));
		$metadata->mapField(array( 'fieldName' => 'original_hash', 'type' => 'string', 'length' => 40, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'original_hash', ));
		$metadata->mapField(array( 'fieldName' => 'is_outdated', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_outdated', ));
		$metadata->mapField(array( 'fieldName' => 'created_at', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'created_at', ));
		$metadata->mapField(array( 'fieldName' => 'updated_at', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'updated_at', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'language', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Language', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'language_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
