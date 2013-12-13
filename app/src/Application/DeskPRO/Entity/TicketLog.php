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

use Application\DeskPRO\App;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Ticket log items
 *
 */
class TicketLog extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\TicketLog
	 */
	protected $parent = null;

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var string
	 */
	protected $action_type;

	/**
	 * If the log involves a specific thing in a ticket (eg a message that was moved),
	 * then that is this id.
	 *
	 * @var int
	 *
	 */
	protected $id_object = null;

	/**
	 * The ID of the previous entity changed, or any other numeric value.
	 * @var int
	 *
	 */
	protected $id_before = null;

	/**
	 * The ID of the new entity, or any other numeric value.
	 * @var int
	 *
	 */
	protected $id_after = null;

	/**
	 * If the change was caused by a trigger, the trigger id
	 * @var int
	 */
	protected $trigger_id = null;

	/**
	 * If the change was caused by an SLA, the SLA
	 *
	 * @var Sla|null
	 */
	protected $sla = null;

	/**
	 * @var string|null
	 */
	protected $sla_status = null;

	/**
	 * @var string
	 */
	protected $details = array();

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * Used in TicketController to hold sub-logs
	 *
	 * @var array
	 */
	public $grouped = array();

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

	public function getPersonId()
	{
		return $this->person['id'];
	}

	public function setPersonId($id)
	{
		if ($id) {
			$person = App::getOrm()->getRepository('DeskPRO:Person')->find($id);
			$this['person'] = $person;
		} else {
			$this['person'] = null;
		}
	}

	public function getTicketId()
	{
		return $this->ticket['id'];
	}

	public function setTicketId($id)
	{
		$ticket = App::getOrm()->getRepository('DeskPRO:Ticket')->find($id);
		$this['ticket'] = $ticket;
	}

	public function setDetails(array $details)
	{
		if (isset($details['id_before'])) {
			$this['id_before'] = $details['id_before'];
			unset($details['id_before']);
		}
		if (isset($details['id_after'])) {
			$this['id_after'] = $details['id_after'];
			unset($details['id_after']);
		}
		if (isset($details['id_object'])) {
			$this['id_object'] = $details['id_object'];
			unset($details['id_object']);
		}

		$this->setModelField('details', $details);
	}

	public function setSlaId($sla_id)
	{
		if ($sla_id) {
			$this['sla'] = App::getOrm()->getRepository('DeskPRO:Sla')->find($sla_id);
		} else {
			$this['sla'] = null;
		}
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketLog';
		$metadata->setPrimaryTable(array( 'name' => 'tickets_logs', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'action_type', 'type' => 'string', 'length' => 40, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'action_type', ));
		$metadata->mapField(array( 'fieldName' => 'id_object', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'id_object', ));
		$metadata->mapField(array( 'fieldName' => 'id_before', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'id_before', ));
		$metadata->mapField(array( 'fieldName' => 'id_after', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'id_after', ));
		$metadata->mapField(array( 'fieldName' => 'trigger_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'trigger_id', ));
		$metadata->mapField(array( 'fieldName' => 'sla_status', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'sla_status', ));
		$metadata->mapField(array( 'fieldName' => 'details', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'details', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'parent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketLog', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'parent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'ticket', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Ticket', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'ticket_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'sla', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Sla', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'sla_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true,  ));
	}
}
