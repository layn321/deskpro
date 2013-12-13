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

use Application\DeskPRO\Translate\HasPhraseName;
use Application\DeskPRO\Translate\Translate;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Orb\Util\Util;

/**
 * Feedback status types for accepted/declined statuses
 *
 */
class FeedbackStatusCategory extends \Application\DeskPRO\Domain\DomainObject implements HasPhraseName
{
	const STATUS_ACTIVE = 'active';
	const STATUS_CLOSED = 'closed';

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $status_type;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var int
	 */
	protected $display_order = 0;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getStatusCode()
	{
		return $this->status_type . '.' . $this->id;
	}

	public function getRealTitle()
	{
		return $this->title;
	}

	/**
	 * Return a unique ID that we can use to look up translations for this object
	 *
	 * @param string $property If supplied, the property on the object we want to translate.
	 * @return string
	 */
	public function getPhraseName($property = null, Translate $translate)
	{
		if (!$property) {
			$property = 'title';
		}
		$name = strtolower(Util::getBaseClassname($this));
		$phrase_name = 'obj_'.$name.'.' . $this->id . '_' . $property;

		return $phrase_name;
	}


	/**
	 * Get the default value phrase for the object
	 *
	 * @param string $property If supplied, the property on the object we want to translate.
	 * @return string
	 */
	public function getPhraseDefault($property = null, Translate $translate)
	{
		return $this->title;
	}


	public function getSelectTitle()
	{
		return $this->title;
	}


	public function __toString()
	{
		return $this->title;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\FeedbackStatusCategory';
		$metadata->setPrimaryTable(array( 'name' => 'feedback_status_categories', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'status_type', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'status_type', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'display_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'display_order', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
