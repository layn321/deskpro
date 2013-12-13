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
 * A log of views
 */
class PageViewLog extends \Application\DeskPRO\Domain\DomainObject
{
	const TYPE_ARTICLE   = 1;
	const TYPE_DOWNLOAD  = 2;
	const TYPE_NEWS      = 3;
	const TYPE_FEEDBACK  = 4;

	const ACTION_VIEW = 1;
	const ACTION_DOWNLOAD = 2;

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var int
	 */
	protected $object_type;

	/**
	 * @var int
	 */
	protected $object_id;

	/**
	 * @var int
	 */
	protected $view_action = 1;

	/**
	 * @var int
	 */
	protected $person_id = null;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	public function __construct()
	{
		$this->setModelField('date_created', new \DateTime());
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setPerson(Person $person)
	{
		$id = null;
		if ($person->getId()) {
			$id = $person->getId();
		}

		$this->setModelField('person_id', $id);
	}

	public function getObjectType()
	{
		return self::getObjectTypeFromTypeId($this->object_type);
	}

	/**
	 * @param string|int $type
	 */
	public function setObjectType($type)
	{
		if (!is_numeric($type)) {
			switch ($type) {
				case 'article':
					$type = self::TYPE_ARTICLE;
					break;
				case 'download':
					$type = self::TYPE_DOWNLOAD;
					break;
				case 'news':
					$type = self::TYPE_NEWS;
					break;
				case 'feedback':
					$type = self::TYPE_FEEDBACK;
					break;
			}
		}

		$this->setModelField('object_type', $type);
	}

	public static function getObjectTypeFromTypeId($type)
	{
		switch ($type) {
			case self::TYPE_ARTICLE: return 'article';
			case self::TYPE_DOWNLOAD: return 'download';
			case self::TYPE_NEWS: return 'news';
			case self::TYPE_FEEDBACK: return 'feedback';
		}

		throw new \InvalidArgumentException("Invalid type id. Got:`$type`");
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Basic';
		$metadata->setPrimaryTable(array(
			'name' => 'page_view_log',
			'indexes' => array(
				'object_idx' => array('columns' => array('object_type', 'object_id')),
				'date_created_idx' => array('columns' => array('date_created')),
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'object_type', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_type', ));
		$metadata->mapField(array( 'fieldName' => 'object_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_id', ));
		$metadata->mapField(array( 'fieldName' => 'view_action', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'view_action', ));
		$metadata->mapField(array( 'fieldName' => 'person_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'person_id', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
