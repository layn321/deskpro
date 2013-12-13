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
 * Email sources that we need to send
 *
 */
class SendmailQueue extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Blob
	 */
	protected $blob = null;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var string
	 */
	protected $from_address;

	/**
	 * @var string
	 */
	protected $to_address;

	/**
	 * @var int
	 */
	protected $attempts = 0;

	/**
	 * @var \DateTime
	 */
	protected $date_next_attempt = null;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var \DateTime
	 */
	protected $date_sent = null;

	/**
	 * @var bool
	 */
	protected $has_sent = false;

	/**
	 * @var string
	 */
	protected $log = '';

	/**
	 * @var int
	 */
	protected $priority = 0;

	public function __construct()
	{
		$this->date_created = new \DateTime();
	}


	/**
	 * @param string $addr
	 */
	public function setFromAddress($addr)
	{
		if (is_array($addr)) {
			$addr = array_shift($addr);
		}

		$this->setModelField('from_address', $addr);
	}


	/**
	 * @param string|array $addr
	 */
	public function setToAddress($addr)
	{
		if (is_array($addr)) {
			$addr = implode(',', $addr);
		}

		$this->setModelField('to_address', $addr);
	}


	/**
	 * Get the message blob as a raw email string
	 *
	 * @return string
	 */
	public function getMessageAsString()
	{
		if (!$this->blob) {
			return '';
		}

		$raw_source = App::getContainer()->getBlobStorage()->copyBlobRecordToString($this->blob);

		// A DeskPRO queue job means we should parse out the headers
		if ($this->blob->filename == 'sendmail.job') {
			$pos = strpos($raw_source, "\n");
			if ($pos !== false) {
				$pos2 = strpos($raw_source, "\n", $pos+2);
				if ($pos2 !== false) {
					$pos = $pos2;
				}
			}

			if ($pos !== false) {
				$raw_source = substr($raw_source, $pos+2);
			}

		// Its an object, we can unserialise and get the value
		} else {
			$message = @unserialize($raw_source);
			$raw_source = '';

			if ($message) {
				$raw_source = (string)$message;
				$message = null;
			}
		}

		return $raw_source;
	}

	/**
	 * @param string $log
	 */
	public function appendLog($log)
	{
		$this->log .= "\n" . $log;

		$len = strlen($this->log);
		if ($len > 25000) {
			$trim = $len - 25000;
			if ($trim > 1000) {
				$this->log = "(Truncated)\n\n" . substr($this->log, -25000);
			}
		}

		$this->log = trim($this->log);
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array(
			'name' => 'sendmail_queue',
			'indexes' => array(
				'has_sent_idx' => array('columns' => array('has_sent', 'date_next_attempt'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'subject', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'subject', ));
		$metadata->mapField(array( 'fieldName' => 'to_address', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'to_address', ));
		$metadata->mapField(array( 'fieldName' => 'from_address', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'from_address', ));
		$metadata->mapField(array( 'fieldName' => 'attempts', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'attempts', ));
		$metadata->mapField(array( 'fieldName' => 'date_next_attempt', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_next_attempt', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_sent', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_sent', ));
		$metadata->mapField(array( 'fieldName' => 'has_sent', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'has_sent', ));
		$metadata->mapField(array( 'fieldName' => 'log', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'log', ));
		$metadata->mapField(array( 'fieldName' => 'priority', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'priority'));
		$metadata->mapManyToOne(array( 'fieldName' => 'blob', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Blob', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'blob_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
