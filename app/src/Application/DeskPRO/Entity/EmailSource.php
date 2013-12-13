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
 * Raw email sources
 */
class EmailSource extends \Application\DeskPRO\Domain\DomainObject
{
	const OBJ_TYPE_TICKET = 'ticket';
	const OBJ_TYPE_TICKET_MESSAGE = 'ticketmessage';

	const ERR_SERVER_ERROR      = 'server_error';
	const ERR_FROM_MISSING      = 'from_missing';
	const ERR_FROM_INVALID      = 'from_invalid';
	const ERR_FROM_GATEWAY      = 'from_gateway_address';
	const ERR_FROM_BANNED       = 'from_banned';
	const ERR_FROM_DISABLED     = 'from_disabled_user';
	const ERR_SUBJECT_MISSING   = 'subject_missing';
	const ERR_MESSAGE_EMPTY     = 'message_missing';
	const ERR_MESSAGE_TOO_BIG   = 'message_too_big';
	const ERR_EMPTY             = 'empty';
	const ERR_DUPE              = 'duplicate_message';
	const ERR_AUTORESPONDER     = 'autoresponder';
	const ERR_SPAM              = 'spam';
	const ERR_REQUIRE_REG       = 'require_reg';
	const ERR_OBJ_CLOSED        = 'obj_closed';
	const ERR_OBJ_DELETED       = 'obj_deleted';
	const ERR_OBJ_UNKNOWN       = 'obj_unknown';
	const ERR_AUTH_INVALID      = 'auth_invalid';
	const ERR_AUTH_MISSING      = 'auth_missing';
	const ERR_DESKPRO_EMAIL     = 'deskpro_email';
	const ERR_PERM_INSUFFICIENT = 'perm_insufficient';
	const ERR_INVALID_FWD       = 'invalid_fwd';
	const ERR_INVALID_FWD_EMAIL = 'invalid_fwd_email';
	const ERR_MISSING_MARKER    = 'missing_marker';
	const ERR_AGENT_BOUNCE      = 'agent_bounce';
	const ERR_DATE_LIMIT        = 'date_limit';

	/**
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * The message UID.
	 *
	 * @var null
	 */
	protected $uid = null;

	/**
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $blob = null;

	/**
	 * @var \Application\DeskPRO\Entity\EmailGateway
	 */
	protected $gateway = null;

	/**
	 * The type of object this is attached to (should be the table name of
	 * the super type, eg: tickets, people, organizations).
	 *
	 * This typically is not set until after the email is processed (ie
	 * the status is 'processed').
	 *
	 * @var string
	 */
	protected $object_type = '';

	/**
	 * The ID of the object this is attached to.
	 *
	 * @var int
	 */
	protected $object_id = '';

	/**
	 * Just the headers portion of the email
	 *
	 * @var string
	 */
	protected $headers;

	/**
	 * @var string
	 */
	protected $header_to = '';

	/**
	 * @var string
	 */
	protected $header_from = '';

	/**
	 * @var string
	 */
	protected $header_subject = '';

	/**
	 * The current status of the message:
	 * - inserted: Only inserted
	 * - processing: Currently processing
	 * - complete: Fully processed
	 * - error: Tried to process but there was some kind of error (see error_code)
	 *
	 * @var string
	 */
	protected $status = 'processing';

	/**
	 * @var \DateTime
	 */
	protected $date_status = null;

	/**
	 * When status is error, this is the code that describes the error.
	 *
	 * @var string
	 */
	protected $error_code = null;

	/**
	 * A string of other info/debug info about the source. For example, if there is an error then additional
	 * information might be placed here.
	 *
	 * @var string
	 */
	protected $source_info = null;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * The raw source, pieced together.
	 *
	 * This is public on purpose. The AbstractFetcher
	 * sets this property for efficiency in cases where an email
	 * may be processed immediately after being read, we dont
	 * re-fetch the data from the db.
	 *
	 * @var string
	 */
	public $_raw = null;

	public function __construct()
	{
		$this->setModelField('date_created', new \DateTime());
		$this->setModelField('date_status', new \DateTime());
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Get the full raw source of the email
	 *
	 * @return string
	 */
	public function getRawSource()
	{
		if ($this->_raw !== null) return $this->_raw;

		$this->_raw = App::getContainer()->getBlobStorage()->copyBlobRecordToString($this->blob);

		return $this->_raw;
	}


	/**
	 * Clears local cache of raw source
	 */
	public function clearRawSource()
	{
		$this->_raw = null;
	}


	/**
	 * @return string
	 */
	public function getErrorCodeTitle()
	{
		if (!$this->error_code) {
			return '';
		}

		switch ($this->error_code) {
			case self::ERR_SERVER_ERROR:        return 'Server Error';
			case self::ERR_FROM_MISSING:        return 'Missing From Address';
			case self::ERR_FROM_INVALID:        return 'Invalid From Address';
			case self::ERR_FROM_GATEWAY:        return 'From Gateway Address';
			case self::ERR_FROM_BANNED:         return 'Banned From Addres';
			case self::ERR_FROM_DISABLED:       return 'From Disabled User';
			case self::ERR_SUBJECT_MISSING:     return 'Subject Missing';
			case self::ERR_MESSAGE_EMPTY:       return 'Empty message';
			case self::ERR_MESSAGE_TOO_BIG:     return 'Message Too Big';
			case self::ERR_EMPTY:               return 'Empty Source';
			case self::ERR_DUPE:                return 'Duplicate';
			case self::ERR_AUTORESPONDER:       return 'Auto-repsonse';
			case self::ERR_SPAM:                return 'Spam';
			case self::ERR_REQUIRE_REG:         return 'User Requires Registration';
			case self::ERR_OBJ_CLOSED:          return 'Ticket Closed';
			case self::ERR_OBJ_DELETED:         return 'Ticket Deleted';
			case self::ERR_OBJ_UNKNOWN:         return 'Unknown Ticket';
			case self::ERR_AUTH_INVALID:        return 'Invalid Auth Code';
			case self::ERR_AUTH_MISSING:        return 'Missing Auth Code';
			case self::ERR_DESKPRO_EMAIL:       return 'DeskPRO Address';
			case self::ERR_PERM_INSUFFICIENT:   return 'Insufficient Permissions';
			case self::ERR_INVALID_FWD:         return 'Invalid Forward: Could not parse';
			case self::ERR_INVALID_FWD_EMAIL:   return 'Invalid Forward: Invalid user email address';
			case self::ERR_MISSING_MARKER:      return 'Missing Marker';
		}

		return $this->error_code;
	}


	/**
	 * @return string
	 */
	public function getSourceInfoAsString()
	{
		if (!$this->source_info) {
			return '';
		}

		if (isset($this->source_info[0])) {
			return implode("\n", $this->source_info);
		} else {
			return print_r($this->source_info, true);
		}
	}


	/**
	 * @param string $status
	 */
	public function setStatus($status)
	{
		$this->setModelField('status', $status);
		$this->setModelField('date_status', new \DateTime());
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\\DeskPRO\\EntityRepository\\EmailSource';
		$metadata->setPrimaryTable(array(
			'name' => 'email_sources',
			'indexes' => array(
				'date_created' => array('columns' => array('date_created')),
				'object_idx'   => array('columns' => array('object_type', 'object_id')),
				'status_idx'   => array('columns' => array('status')),
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'uid', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'uid' ));
		$metadata->mapField(array( 'fieldName' => 'object_type', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_type', ));
		$metadata->mapField(array( 'fieldName' => 'object_id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'object_id', ));
		$metadata->mapField(array( 'fieldName' => 'headers', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'headers', ));
		$metadata->mapField(array( 'fieldName' => 'header_to', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'header_to', ));
		$metadata->mapField(array( 'fieldName' => 'header_from', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'header_from', ));
		$metadata->mapField(array( 'fieldName' => 'header_subject', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'header_subject', ));
		$metadata->mapField(array( 'fieldName' => 'status', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'status', ));
		$metadata->mapField(array( 'fieldName' => 'error_code', 'type' => 'string', 'length' => 80, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'error_code', ));
		$metadata->mapField(array( 'fieldName' => 'source_info', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'source_info', ));
		$metadata->mapField(array( 'fieldName' => 'date_status', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_status', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'blob', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'blob_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'gateway', 'targetEntity' => 'Application\\DeskPRO\\Entity\\EmailGateway', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'gateway_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
