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

/**
 * A simple record that just holds filter subscriptions for agents.
 *
 */
class TicketFilterSubscription extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var \Application\DeskPRO\Entity\TicketFilter
	 */
	protected $filter;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var bool
	 */
	protected $email_created = false;

	/**
	 * @var bool
	 */
	protected $email_new = false;

	/**
	 * @var bool
	 */
	protected $email_leave = false;

	/**
	 * @var bool
	 */
	protected $email_user_activity = false;

	/**
	 * @var bool
	 */
	protected $email_agent_activity = false;

	/**
	 * @var bool
	 */
	protected $email_agent_note = false;

	/**
	 * @var bool
	 */
	protected $email_property_change = false;

	/**
	 * @var bool
	 */
	protected $alert_created = false;

	/**
	 * @var bool
	 */
	protected $alert_new = false;

	/**
	 * @var bool
	 */
	protected $alert_leave = false;

	/**
	 * @var bool
	 */
	protected $alert_user_activity = false;

	/**
	 * @var bool
	 */
	protected $alert_agent_activity = false;

	/**
	 * @var bool
	 */
	protected $alert_agent_note = false;

	/**
	 * @var bool
	 */
	protected $alert_property_change = false;

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
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketFilterSubscription';
		$metadata->setPrimaryTable(array( 'name' => 'ticket_filter_subscriptions', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapField(array( 'fieldName' => 'email_created', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_created', ));
		$metadata->mapField(array( 'fieldName' => 'email_new', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_new', ));
		$metadata->mapField(array( 'fieldName' => 'email_leave', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_leave', ));
		$metadata->mapField(array( 'fieldName' => 'email_user_activity', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_user_activity', ));
		$metadata->mapField(array( 'fieldName' => 'email_agent_activity', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_agent_activity', ));
		$metadata->mapField(array( 'fieldName' => 'email_agent_note', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_agent_note', ));
		$metadata->mapField(array( 'fieldName' => 'email_property_change', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_property_change', ));
		$metadata->mapField(array( 'fieldName' => 'alert_created', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'alert_created', ));
		$metadata->mapField(array( 'fieldName' => 'alert_new', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'alert_new', ));
		$metadata->mapField(array( 'fieldName' => 'alert_leave', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'alert_leave', ));
		$metadata->mapField(array( 'fieldName' => 'alert_user_activity', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'alert_user_activity', ));
		$metadata->mapField(array( 'fieldName' => 'alert_agent_activity', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'alert_agent_activity', ));
		$metadata->mapField(array( 'fieldName' => 'alert_agent_note', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'alert_agent_note', ));
		$metadata->mapField(array( 'fieldName' => 'alert_property_change', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'alert_property_change', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'filter', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketFilter', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'filter_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
