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
 * Email addresses that are still waiting to be validated.
 *
 * These are multipurpose:
 * - New email addresses on an account can be be added and validated
 * - Content submissions from logged-out users that use existing email addresses
 *   will create these records and will be validated through the usual controller.
 *
 * A user should always have an email address on their account. If a user is a new record,
 * then they shold have a normal PersonEmail record with is_validating instead.
 *
 * These PersonEmailValidating records are just a way to add an email address to an account
 * without actually reserving the address. For example, a malicious user cant add a new address
 * on his account such that the real user can't register.
 *
 * It's important to look-up the email address first to see if it's in use by a person. Since
 * new content generally creates new Person records, we want to make sure each validating content
 * is attached to a single person and not many records.
 *
 */
class PersonEmailValidating extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * The email address
	 *
	 * @var string
	 */
	protected $email;

	/**
	 * @var int
	 */
	protected $auth;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * An array of array('entityname', 'id')
	 * of content that is validating based on this email address.
	 *
	 * @var string
	 */
	protected $validating_content = array();

	/**
	 * @var bool
	 */
	protected $_is_new = false;

	public function __construct()
	{
		$this->_is_new = true;
		$this->setModelField('date_created', new \DateTime());
		$this->setModelField('auth', Strings::random(8, Strings::CHARS_KEY));
	}

	public function isNewEntity()
	{
		return $this->_is_new;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getEmailDomain()
	{
		return Strings::extractRegexMatch('#@(.*?)$#', $this->email, 1);
	}

	public function addValidatingContent($entity_name, $id)
	{
		$old = $this->validating_content;

		$this->validating_content[] = array($entity_name, $id);

		$this->_onPropertyChanged('validating_content', $old, $this->validating_content);
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\PersonEmailValidating';
		$metadata->setPrimaryTable(array(
			'name' => 'people_emails_validating',
			'uniqueConstraints' => array(
				'email_idx' => array('columns' => array('email'))
			),
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'email', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email', ));
		$metadata->mapField(array( 'fieldName' => 'auth', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'auth', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'validating_content', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'validating_content', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
