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
use Orb\Util\Dates;

/**
 * Ticket trigger log
 */
class TicketTriggerLog extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var int
	 */
	protected $ticket_id;

	/**
	 * @var int
	 */
	protected $trigger_id;

	/**
	 * The date the trigger was executed on the ticket.
	 *
	 * @var \DateTime
	 */
	protected $date_ran;

	/**
	 * The date criteria that caused the trigger execute in the first place.
	 * For example, if a ticket is awaiting_user and the trigger is set to run after 1 day,
	 * then when the trigger finally runs, date_criteria = ticket.date_awaiting_user.
	 *
	 * This is used to ensure a trigger doesnt run multiple times in any escalation period. So
	 * that executes after 5 minuts doesnt again run after 10, and 15, and 20 etc.
	 *
	 * @var \DateTime
	 */
	protected $date_criteria;

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
		$metadata->setPrimaryTable(array(
			'name' => 'ticket_trigger_logs',
			'indexes' => array(
				'ticket_id_idx' => array('columns' => array('ticket_id', 'trigger_id', 'date_criteria'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'ticket_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ticket_id', ));
		$metadata->mapField(array( 'fieldName' => 'trigger_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'trigger_id', ));
		$metadata->mapField(array( 'fieldName' => 'date_ran', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_ran', ));
		$metadata->mapField(array( 'fieldName' => 'date_criteria', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_criteria', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
