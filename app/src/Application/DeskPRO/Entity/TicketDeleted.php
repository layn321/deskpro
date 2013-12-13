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

/**
 * A log of deleted tickets
 *
 */
class TicketDeleted extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $ticket_id;

	/**
	 * @var string
	 */
	protected $old_ptac;

	/**
	 * @var string
	 */
	protected $old_ref = '';

	/**
	 * @var int
	 */
	protected $new_ticket_id = 0;

	/**
	 * @var int
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $by_person;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var string
	 */
	protected $reason;

	public function __construct()
	{
		$this['date_created'] = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	public function getByPersonId()
	{
		if ($this->by_person) {
			return $this->by_person['id'];
		}

		return 0;
	}

	public function setByPersonId($id)
	{
		if ($id) {
			$this['by_person'] = App::getEntityRepository('DeskPRO:Person')->find($id);
		} else {
			$this['by_person'] = null;
		}
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array(
			'name' => 'tickets_deleted',
			'indexes' => array(
				'old_ref_idx' => array('columns' => array('old_ref'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'ticket_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ticket_id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'old_ptac', 'type' => 'string', 'length' => 80, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'old_ptac', ));
		$metadata->mapField(array( 'fieldName' => 'old_ref', 'type' => 'string', 'length' => 80, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'old_ref', ));
		$metadata->mapField(array( 'fieldName' => 'new_ticket_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'new_ticket_id', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'reason', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'reason', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'by_person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'by_person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
