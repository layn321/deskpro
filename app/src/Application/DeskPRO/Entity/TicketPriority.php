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

use Application\DeskPRO\App;
use Application\DeskPRO\Translate\Translate;
use Application\DeskPRO\Translate\HasPhraseName;

/**
 * Ticket priorities
 *
 */
class TicketPriority extends \Application\DeskPRO\Domain\DomainObject implements HasPhraseName
{
	/**
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return App::getTranslator()->getPhraseObject($this, 'title');
	}


	/**
	 * @return string
	 */
	public function getRealTitle()
	{
		return $this->title;
	}


	/**
	 * @param string $property
	 * @return string
	 */
	public function getPhraseName($property = null, Translate $translate)
	{
		if (!$property) {
			$property = 'title';
		}
		$phrase_name = 'obj_ticketpriority.' . $this->id . '_' . $property;

		return $phrase_name;
	}


	/**
	 * @param string $property
	 * @return string
	 */
	public function getPhraseDefault($property = null, Translate $translate)
	{
		return $this->title;
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketPriority';
		$metadata->setPrimaryTable(array( 'name' => 'ticket_priorities', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'priority', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'priority', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
