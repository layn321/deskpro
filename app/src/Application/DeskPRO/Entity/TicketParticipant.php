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

use Application\DeskPRO\App;

/**
 * Links participants to tickets
 *
 */
class TicketParticipant extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \Application\DeskPRO\Entity\TicketAccessCode
	 */
	protected $access_code = null;

	/**
	 * @var \Application\DeskPRO\Entity\PersonEmail
	 */
	protected $person_email = null;

	/**
	 * Default checkbox status of the user
	 *
	 * @var bool
	 */
	protected $default_on = true;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setPerson(Person $person)
	{
		if ($this->person == $person) {
			return;
		}

		$this->_onPropertyChanged('person', $this->person, $person);
		$this->person = $person;

		if (!$this->person_email && $this->person->primary_email) {
			$this->setPersonEmail($this->person->primary_email);
		}
	}

	public function setPersonId($id)
	{
		$person = App::findEntity('DeskPRO:Person', $id);
		$this->setPerson($person);
	}

	public function setPersonEmailId($id)
	{
		$person_email = App::findEntity('DeskPRO:PersonEmail', $id);
		$this['person_email'] = $person_email;
	}

	public function getEmailAddress()
	{
		return $this->person_email['email'];
	}

	/**
	 */
	public function _setAccessCode()
	{
		if (!$this->access_code) {

			// try to find an existing TAC for this person and ticket,
			// ie agents may already have one from them getting notifications

			$access_code = App::getEntityRepository('DeskPRO:TicketAccessCode')->findByTicketAndPerson($this->ticket, $this->person);
			if (!$access_code) {
				$access_code = new TicketAccessCode();
			}

			$this['access_code'] = $access_code;
		}

		$this->access_code->person = $this->person;
		$this->access_code->ticket = $this->ticket;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Basic';
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array( 'name' => 'tickets_participants', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->addLifecycleCallback('_setAccessCode', 'prePersist');
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'default_on', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'default_on', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'ticket', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Ticket', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'ticket_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'access_code', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketAccessCode', 'cascade' => array('persist', 'merge', ), 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'access_code_id', 'referencedColumnName' => 'id', 'unique' => false, 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person_email', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonEmail', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_email_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
