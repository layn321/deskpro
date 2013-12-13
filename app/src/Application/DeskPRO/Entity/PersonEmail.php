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

use Application\DeskPRO\App;

/**
 * Email addresses attached to a person. This is a separate entity because emails are
 * roughly tied to identity (ie local login uses email as identity), and are integral
 * in many cases (notifications etc).
 */
class PersonEmail extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
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
	 * The email address domain
	 *
	 * @var string
	 */
	protected $email_domain;

	/**
	 * @var bool
	 */
	protected $is_validated = true;

	/**
	 * A comment or description of the email address. For example, "work" or "home."
	 *
	 * @var string
	 */
	protected $comment = '';

	/**
	 * The original time the email was created. If validation is requried, this will be the time
	 * that PersonEmailValidating record was created before this one.
	 *
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * The time this email became valid. If validation is requried, then this is when
	 * a PersonEmailValidating becomes a a PersonEmail. If its not required, then
	 * this and date_created will be the same.
	 *
	 * @var \DateTime
	 */
	protected $date_validated = null;

	public function __construct()
	{
		$this->setModelField('date_created', new \DateTime());
		$this->setModelField('date_validated', new \DateTime());
		$this->setModelField('is_validated', true);
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
		if ($this->email_domain) {
			return $this->email_domain;
		}

		return Strings::extractRegexMatch('#@(.*?)$#', $this->email, 1);
	}


	/**
	 * Gets the gravatar URL for this email
	 *
	 * @param bool $secure Use secure url? null to detect automatically based on current request
	 * @return string
	 */
	public function getGravatarUrl($secure = null)
	{
		// Null means detect
		if ($secure === null AND App::isWebRequest()) {
			$request = App::getRequest();
			if ($request->isSecure()) {
				$secure = true;
			}
		}

		$hash = strtolower(md5($this->email));
		if ($secure) {
			$url = 'https://secure.gravatar.com/avatar/' . $hash . '?';
		} else {
			$url = 'http://www.gravatar.com/avatar/' . $hash . '?';
		}

		return $url;
	}



	/**
	 * Checks to see if the gravatar for this email address is actually a real avatar (not a default).
	 *
	 * @return bool
	 */
	public function hasGravatar()
	{
		static $is_real = null;

		if ($is_real === null) {
			$is_real = false;

			$hash = strtolower(md5($this->email));
			$check_url = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';

			$headers = @get_headers($check_url);
			if ($headers AND !empty($headers[0])) {
				if (strpos($headers[0], '200') !== false) {
					$is_real = true;
				}
			}
		}

		return $is_real;
	}



	/**
	 * Set email
	 *
	 * @param string $email
	 */
	public function setEmail($email)
	{
		if ($email) {

			if (!strpos($email, '@')) {
				throw new \InvalidArgumentException("Email address is invalid");
			}

			$this->setModelField('email', strtolower($email));
			list (, $email_domain) = explode('@', $email, 2);
		} else {
			$email = null;
		}

		$this->setModelField('email_domain', $email_domain);
	}



	public function setIsValidated($yesno)
	{
		$this->setModelField('is_validated', $yesno);

		if ($yesno) {
			$this->setModelField('date_validated', new \DateTime());
		} else {
			$this->setModelField('date_validated', null);
		}
	}


	/**
	 * @param Person $person
	 */
	public function setPerson(Person $person)
	{
		$this->setModelField('person', $person);
		if ($person->is_agent) {
			$this->setIsValidated(true);
		}
	}


	public function _postPersist()
	{
		if (!$this->person) {
			return;
		}

		$user_rule_proc = new \Application\DeskPRO\People\UserRuleProcessor(App::getOrm());
		$user_rule_proc->newEmail($this->person, $this);
	}

	public function _verifyEmailAddress()
	{
		// Email address should be validated by the time we get here,
		// this is a failsafe check
		if (App::getSystemService('gateway_address_matcher')->isManagedAddress($this->email)) {
			throw new \RuntimeException("`{$this->email}`` is an a gateway account address");
		}
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\PersonEmail';

		$metadata->setPrimaryTable(array(
			'name' => 'people_emails',
			'indexes' => array(
				'email_domain_idx' => array('columns' => array('email_domain')),
			),
			'uniqueConstraints' => array(
				'email_idx' => array('columns' => array('email'))
			),
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->addLifecycleCallback('_postPersist', 'postPersist');
		$metadata->addLifecycleCallback('_verifyEmailAddress', 'prePersist');
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'email', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email', ));
		$metadata->mapField(array( 'fieldName' => 'email_domain', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_domain', ));
		$metadata->mapField(array( 'fieldName' => 'is_validated', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_validated', ));
		$metadata->mapField(array( 'fieldName' => 'comment', 'type' => 'text', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'comment', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_validated', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_validated', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => 'emails', 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
