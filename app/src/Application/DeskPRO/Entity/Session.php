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

use Orb\Util\Strings;
use Orb\Util\Util;

/**
 * Active user sessions
 */
class Session extends \Application\DeskPRO\Domain\DomainObject
{
	const STATUS_AVAILABLE = 'available';
	const STATUS_AWAY = 'away';

	/**
	 * The unique ID.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * The authcode for the session to verify an id
	 *
	 * @var string
	 */
	protected $auth;

	/**
	 * The Interface the session is for
	 *
	 * @var string
	 */
	protected $interface = '';

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor = null;

	/**
	 * The users user agent string
	 *
	 * @var string
	 */
	protected $user_agent = null;

	/**
	 * The users IP address
	 *
	 * @var string
	 */
	protected $ip_address = null;

	/**
	 * @var string
	 */
	protected $data = '';

	/**
	 * @var bool
	 */
	protected $is_person = false;

	/**
	 * @var bool
	 */
	protected $is_bot = false;

	/**
	 * @var bool
	 */
	protected $is_helpdesk = false;

	/**
	 * (Agents) Status (available or away)
	 * @var string
	 */
	protected $active_status = 'available';

	/**
	 * (Agents) Wehn status is available, if they are available for chat
	 * @var bool
	 */
	protected $is_chat_available = true;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var \DateTime
	 */
	protected $date_last;

	/**
	 * @var bool
	 */
	protected $_is_new = false;

	public function __construct()
	{
		$this->setModelField('auth', Strings::random(15, Strings::CHARS_KEY));
		$this->setModelField('date_created', new \DateTime());
		$this->setModelField('date_last', new \DateTime());
		$this->_is_new = true;
	}


	/**
	 * @return bool
	 */
	public function getIsNew()
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



	/**
	 * Gets the session ID for this session. It's an encoded ID and an authcode.
	 *
	 * @return string
	 */
	public function getSessionCode()
	{
		$id_enc = Util::baseEncode($this->id, Util::BASE36_ALPHABET);
		return $id_enc . '-' . $this->auth;
	}



	/**
	 * Check a session code against some kind o finput to see
	 * if they match.
	 *
	 * @return bool
	 */
	public function checkSessionCode($session_code)
	{
		return ($this->getSessionCode() === $session_code);
	}



	public function setPerson(Person $person = null)
	{
		if (!$person) {
			$this->setPersonId(0);
		} else {
			$this->setPersonId($person['id']);
		}
	}

	public function setPersonId($person_id)
	{
		if ($person_id) {
			$this->setModelField('is_person', true);
			$this->setModelField('person', App::getEntityRepository('DeskPRO:Person')->find($person_id));
		} else {
			$this->setModelField('is_person', false);
			$this->setModelField('person', null);
		}
	}

	public function getPersonId()
	{
		if ($this->person) {
			return $this->person['id'];
		}
		return 0;
	}


	/**
	 * A secret hash of this session key with the app secret.
	 *
	 * Most notably used as the "proxy key"
	 *
	 * @param  string $secret Another component to add to the hash
	 * @param bool $not_vis True for do not use visitor secret. Default is to use visitor if it exists.
	 * @return string
	 */
	public function getSessionSecret($name = '', $not_vis = false)
	{
		if (!$not_vis && $this->visitor) {
			return $this->visitor->getVisitorSecret($name);
		}

		return md5($this->id . $this->auth . App::getAppSecret() . $name);
	}


	/**
	 * Generate a security token based off of this session
	 *
	 * @param $name
	 * @param int $timeout
	 * @return string
	 */
	public function generateSecurityToken($name = '', $timeout = 43200)
	{
		if ($this->visitor) {
			return Util::generateStaticSecurityToken($this->visitor->getVisitorSecret($name), $timeout);
		}

		return Util::generateStaticSecurityToken($this->getSessionSecret($name, true), $timeout);
	}


	/**
	 * Check a security token to see if its valid
	 *
	 * @param $name
	 * @return bool
	 */
	public function checkSecurityToken($name = '', $token)
	{
		if ($this->visitor && $this->visitor->checkSecurityToken($name, $token)) {
			return true;
		}
		return Util::checkStaticSecurityToken($token, $this->getSessionSecret($name, true));
	}


	public function updateLastTime()
	{
		$this->setModelField('date_last', new \DateTime());
	}

	public static function getIdFromCode($sess_code)
	{
		if (!strpos($sess_code, '-')) return null;

		list ($session_id, ) = explode('-', $sess_code, 2);

		$session_id = Util::baseDecode($session_id, Util::BASE36_ALPHABET);

		return $session_id;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Session';
		$metadata->setPrimaryTable(array( 'name' => 'sessions', 'indexes' => array( 'date_last_idx' => array( 'columns' => array( 0 => 'date_last', 1 => 'is_person', ), ), ), ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'auth', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'auth', ));
		$metadata->mapField(array( 'fieldName' => 'interface', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'interface', ));
		$metadata->mapField(array( 'fieldName' => 'user_agent', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'user_agent', ));
		$metadata->mapField(array( 'fieldName' => 'ip_address', 'type' => 'string', 'length' => 80, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'ip_address', ));
		$metadata->mapField(array( 'fieldName' => 'data', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'data', ));
		$metadata->mapField(array( 'fieldName' => 'is_person', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_person', ));
		$metadata->mapField(array( 'fieldName' => 'is_bot', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_bot', ));
		$metadata->mapField(array( 'fieldName' => 'is_helpdesk', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_helpdesk', ));
		$metadata->mapField(array( 'fieldName' => 'active_status', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'active_status', ));
		$metadata->mapField(array( 'fieldName' => 'is_chat_available', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_chat_available', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_last', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_last', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'visitor', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Visitor', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'visitor_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
