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
use Orb\Util\Arrays;
use Orb\Util\Strings;

/**
 * A log of a message sent to a person.
 *
 * This is a log of exactly 1 email message sent to exactly 1 person.
 * For example, if one email was sent to 1 person and 3 CC'd people, then there
 * would be 4 SendmailLog's.
 *
 * @package Application\DeskPRO\Entity
 */
class SendmailLog extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var Person
	 */
	protected $person;

	/**
	 * @var Ticket
	 */
	protected $ticket;

	/**
	 * @var TicketMessage
	 */
	protected $ticket_message;

	/**
	 * @var string
	 */
	protected $to_address;

	/**
	 * @var string
	 */
	protected $code;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var string
	 */
	protected $from_address;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var \DateTime
	 */
	protected $date_process;

	#### Delivered
	/**
	 * @var \DateTime
	 */
	protected $date_deliver;

	/**
	 * @var string
	 */
	protected $reason_deliver;

	#### Opened/Clicks
	/**
	 * @var \DateTime
	 */
	protected $date_open;

	/**
	 * @var \DateTime
	 */
	protected $date_click;

	/**
	 * A plaintext log with multiple lines: [date] URL
	 *
	 * @var string
	 */
	protected $clicked_urls;

	/**
	 * @var int
	 */
	protected $count_open = 0;

	/**
	 * @var int
	 */
	protected $count_click = 0;

	#### Deferred
	/**
	 * @var \DateTime
	 */
	protected $date_defer;

	/**
	 * @var string
	 */
	protected $reason_defer;

	#### Bounced/Spam
	/**
	 * @var \DateTime
	 */
	protected $date_bounce;

	/**
	 * @var string
	 */
	protected $bounce_type;

	/**
	 * @var string
	 */
	protected $bounce_code;

	/**
	 * @var string
	 */
	protected $reason_bounce;

	/**
	 * @var \DateTime
	 */
	protected $date_drop;

	/**
	 * @var string
	 */
	protected $reason_drop;

	/**
	 * @var \DateTime
	 */
	protected $date_spam;


	public function __construct()
	{
		$this->date_created = new \DateTime();
		$this->code = self::genCode();
	}

	/**
	 * @return string
	 */
	public static function genCode()
	{
		$time = \Orb\Util\Util::baseEncode(time(), 'letters');
		return $time . \Orb\Util\Strings::random(30 - strlen($time), Strings::CHARS_ALPHANUM_IU);
	}


	/**
	 * @param Ticket $ticket
	 * @param array $to_addresses
	 * @param $subject
	 * @param $from_address
	 * @return string
	 */
	public static function insertTicketLog(Ticket $ticket, array $to_addresses, $subject, $from_address)
	{
		$code = self::genCode();

		$from_address = strtolower($from_address);

		$to_addresses = Arrays::func($to_addresses, 'strtolower');
		$to_addresses = array_unique($to_addresses);

		if (!$to_addresses) {
			return null;
		}

		$tos_in = array();
		foreach ($to_addresses as $to) {
			$tos_in[] = App::getDb()->quote($to);
		}
		$tos_in = implode(',', $tos_in);

		$now = date('Y-m-d H:i:s');

		$batch = array();

		$people_ids = App::getDb()->fetchAllKeyValue("
			SELECT email, person_id
			FROM people_emails
			WHERE email IN ($tos_in)
		");

		foreach ($to_addresses as $to) {
			$batch[] = array(
				'code'              => $code,
				'to_address'        => $to,
				'person_id'         => isset($people_ids[$to]) ? $people_ids[$to] : null,
				'from_address'      => $from_address,
				'subject'           => $subject,
				'date_created'      => $now,
				'ticket_id'         => $ticket->getId(),
			);
		}

		App::getDb()->batchInsert('sendmail_logs', $batch);

		return $code;
	}


	/**
	 * @param TicketMessage $ticket_message
	 * @param array $to_addresses
	 * @param $subject
	 * @param $from_address
	 * @return string
	 */
	public static function insertTicketMessageLog(TicketMessage $ticket_message, array $to_addresses, $subject, $from_address)
	{
		$code = self::genCode();

		$from_address = strtolower($from_address);

		$to_addresses = Arrays::func($to_addresses, 'strtolower');
		$to_addresses = array_unique($to_addresses);

		if (!$to_addresses) {
			return null;
		}

		$tos_in = array();
		foreach ($to_addresses as $to) {
			$tos_in[] = App::getDb()->quote($to);
		}
		$tos_in = implode(',', $tos_in);

		$now = date('Y-m-d H:i:s');

		$batch = array();

		$people_ids = App::getDb()->fetchAllKeyValue("
			SELECT email, person_id
			FROM people_emails
			WHERE email IN ($tos_in)
		");

		foreach ($to_addresses as $to) {
			$batch[] = array(
				'code'              => $code,
				'to_address'        => $to,
				'person_id'         => isset($people_ids[$to]) ? $people_ids[$to] : null,
				'from_address'      => $from_address,
				'subject'           => $subject,
				'date_created'      => $now,
				'ticket_id'         => $ticket_message->ticket->getId(),
				'ticket_message_id' => $ticket_message->getId()
			);
		}

		App::getDb()->batchInsert('sendmail_logs', $batch);

		return $code;
	}


	/**
	 * @param array $to_addresses
	 * @param $subject
	 * @param $from_address
	 * @return string
	 */
	public static function insertLog(array $to_addresses, $subject, $from_address)
	{
		$code = self::genCode();

		$from_address = strtolower($from_address);

		$to_addresses = Arrays::func($to_addresses, 'strtolower');
		$to_addresses = array_unique($to_addresses);

		if (!$to_addresses) {
			return null;
		}

		$tos_in = array();
		foreach ($to_addresses as $to) {
			$tos_in[] = App::getDb()->quote($to);
		}

		$now = date('Y-m-d H:i:s');

		$batch = array();

		$people_ids = App::getDb()->fetchAllKeyValue("
			SELECT email, person_id
			FROM people_emails
			WHERE email IN ($tos_in)
		");

		foreach ($to_addresses as $to) {
			$batch[] = array(
				'code'         => $code,
				'to_address'   => $to,
				'person_id'    => isset($people_ids[$to]) ? $people_ids[$to] : null,
				'from_address' => $from_address,
				'subject'      => $subject,
				'date_created' => $now
			);
		}

		App::getDb()->batchInsert('sendmail_logs', $batch);

		return $code;
	}


	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array(
			'name' => 'sendmail_logs',
			'uniqueConstraints' => array(
				'code' => array('columns' => array('code', 'to_address'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'code', 'type' => 'string', 'length' => 30, 'nullable' => false, 'columnName' => 'code', ));
		$metadata->mapField(array( 'fieldName' => 'to_address', 'type' => 'string', 'length' => 255,  'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'to_address', 'uid' => true, ));
		$metadata->mapField(array( 'fieldName' => 'subject', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'subject', ));
		$metadata->mapField(array( 'fieldName' => 'from_address', 'type' => 'string', 'length' => 255, 'scale' => 0, 'nullable' => false, 'columnName' => 'from_address', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_process', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_process', ));
		$metadata->mapField(array( 'fieldName' => 'date_deliver', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_deliver', ));
		$metadata->mapField(array( 'fieldName' => 'reason_deliver', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'reason_deliver', ));
		$metadata->mapField(array( 'fieldName' => 'date_open', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_open', ));
		$metadata->mapField(array( 'fieldName' => 'date_click', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_click', ));
		$metadata->mapField(array( 'fieldName' => 'clicked_urls', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'clicked_urls', ));
		$metadata->mapField(array( 'fieldName' => 'count_open', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'count_open', ));
		$metadata->mapField(array( 'fieldName' => 'count_click', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'count_click', ));
		$metadata->mapField(array( 'fieldName' => 'date_defer', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_defer', ));
		$metadata->mapField(array( 'fieldName' => 'reason_defer', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'reason_defer', ));
		$metadata->mapField(array( 'fieldName' => 'date_bounce', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_bounce', ));
		$metadata->mapField(array( 'fieldName' => 'bounce_code', 'type' => 'string', 'length' => 10, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'bounce_code', ));
		$metadata->mapField(array( 'fieldName' => 'bounce_type', 'type' => 'string', 'length' => 10, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'bounce_type', ));
		$metadata->mapField(array( 'fieldName' => 'reason_bounce', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'reason_bounce', ));
		$metadata->mapField(array( 'fieldName' => 'date_drop', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_drop', ));
		$metadata->mapField(array( 'fieldName' => 'reason_drop', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'reason_drop', ));
		$metadata->mapField(array( 'fieldName' => 'date_spam', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_spam', ));

		$metadata->mapManyToOne(array( 'fieldName' => 'ticket', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Ticket', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'ticket_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'ticket_message', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketMessage', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'ticket_message_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
