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
use Application\DeskPRO\Entity;

use Orb\Util\Strings;
use Orb\Util\Util;

/**
 * Tickets that were in the process of being created but were never finished,
 * or that were solved with auto-search.
 *
 */
class PreticketContent extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor = null;

	/**
	 * @var string
	 */
	protected $ip_address = '';

	/**
	 * @var string
	 */
	protected $email = null;

	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * The department ID the ticket was in
	 *
	 */
	protected $department_id = 0;

	/**
	 * The subject
	 *
	 * @var string
	 */
	protected $subject = '';

	/**
	 * The message
	 *
	 * @var string
	 */
	protected $message = '';

	/**
	 * Other raw form data
	 *
	 * @var string
	 */
	protected $data = array();

	/**
	 * If the person marked the ticket as solved after reading some content.
	 *
	 * @var bool
	 */
	protected $is_solved = false;

	/**
	 * Array of array(type,id) that the user said didnt answer their article.
	 *
	 * @var string
	 */
	protected $unsolved_content = array();

	/**
	 * @var string
	 */
	protected $object_type = null;

	/**
	 * @var int
	 */
	protected $object_id = null;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

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

	/**
	 * @static
	 * @param Person $person
	 * @param bool $use_request Use the current request to set visitor (and thus ip etc)
	 * @return \Application\DeskPRO\Entity\CommentAbstract
	 */
	public static function newForPerson(Person $person, $use_request = true)
	{
		$preticket = new static();

		if (!$person->isGuest()) {
			$preticket->person = $person;
		}

		if ($use_request) {
			$preticket->visitor = App::getSession()->getVisitor();
		}

		return $preticket;
	}

	public function setContentObject($obj)
	{
		$this['object_type'] = $obj->getContentType();
		$this['object_id']   = $obj->getId();
	}

	public function setVisitor(Visitor $visitor = null)
	{
		$this->_onPropertyChanged('visitor', $this->visitor, $visitor);
		$this['visitor'] = $visitor;

		if ($visitor === null) return;

		$this['ip_address'] = $visitor['ip_address'];

		if (!$this->name AND $visitor['name']) {
			$this['name'] = $visitor['name'];
		}
		if (!$this->email AND $visitor['email']) {
			$this['email'] = $visitor['email'];
		}
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array(
			'name' => 'pretickets_content',
			'indexes' => array(
				'email_idx' => array('columns' => array('email')),
				'object_idx' => array('columns' => array('object_type', 'object_id'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'ip_address', 'type' => 'string', 'length' => 30, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ip_address', ));
		$metadata->mapField(array( 'fieldName' => 'email', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'email', ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'department_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'department_id', ));
		$metadata->mapField(array( 'fieldName' => 'subject', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'subject', ));
		$metadata->mapField(array( 'fieldName' => 'message', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'message', ));
		$metadata->mapField(array( 'fieldName' => 'data', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'data', ));
		$metadata->mapField(array( 'fieldName' => 'is_solved', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_solved', ));
		$metadata->mapField(array( 'fieldName' => 'unsolved_content', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'unsolved_content', ));
		$metadata->mapField(array( 'fieldName' => 'object_type', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'object_type', ));
		$metadata->mapField(array( 'fieldName' => 'object_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'object_id', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'visitor', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Visitor', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'visitor_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
