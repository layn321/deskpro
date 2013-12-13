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
 */

namespace Application\DeskPRO\EmailGateway\Fetcher;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\EmailGateway;
use Application\DeskPRO\Entity\EmailSource;
use Orb\Log\Logger;
use Orb\Util\Strings;

/**
 * A fetcher takes makes a conenction to a resource described in
 * an EmailGateway record, and reads messages into the database for storage.
 */
abstract class AbstractFetcher
{
	/**
	 * \Application\DeskPRO\EmailGateway
	 */
	protected $gateway;

	/**
	 * @var \Zend\Mail\Storage\AbstractStorage
	 */
	protected $storage;

	/**
	 * @var \Application\DeskPRO\Log\Logger
	 */
	protected $logger;

	/**
	 * The max size in byes to read
	 * @var int
	 */
	protected $max_size = 0;

	/**
	 * @param \Application\DeskPRO\Entity\EmailGateway $gateway
	 * @param int $max_size  The max size in bytes to read. 0 to disable.
	 */
	public function __construct(EmailGateway $gateway, $max_size = 0)
	{
		$this->gateway = $gateway;
		$this->logger = new Logger();
		$this->setMaxSize($max_size);
		$this->init();
	}

	protected function init() {}

	public function __destruct()
	{
		if ($this->storage) {
			try { $this->storage->close(); } catch (\Exception $e) {}
		}
	}

	/**
	 * Closes the connection
	 */
	protected function _closeConnection()
	{
		if ($this->storage) {
			try {
				$this->storage->close();
			} catch (\Exception $e) {}
		}
	}

	/**
	 * Set the max size to read
	 *
	 * @param int $max_size The max size in bytes
	 */
	public function setMaxSize($max_size)
	{
		$this->max_size = (int)$max_size;
		if ($this->max_size < 0) $this->max_size = 0;
	}


	/**
	 * Get the max size
	 *
	 * @return int
	 */
	public function getMaxSize()
	{
		return $this->max_size;
	}


	/**
	 * @return mixed
	 */
	public function getStorage($reconnect = false)
	{
		if ($reconnect && $this->storage) {
			$this->_closeConnection();
			$this->storage = null;
		}

		if (!$this->storage) {
			$this->storage = $this->_initConnection();
		}

		return $this->storage;
	}


	/**
	 * Closes the fetcher.
	 */
	public function close()
	{

	}


	/**
	 * @param $logger \Application\DeskPRO\Log\Logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Initiates the connection
	 * @return \Zend\Mail\Storage\AbstractStorage
	 */
	abstract protected function _initConnection();

	/**
	 * Reads the next message in the inbox. Must return
	 * a RawMessage.
	 *
	 * Return null if there are no more messages.
	 *
	 * @return \Application\DeskPRO\EmailGateway\Fetcher\RawMessage
	 */
	abstract protected function _readNext();

	/**
	 * Marks the email as 'read' in any way that'll prevent the system from
	 * reading it again. For example, deleting it, moving it to a new folder, etc.
	 *
	 * @param  $id
	 */
	abstract protected function _doneRead($id);


	/**
	 * Reads the next message from the resource and saves it into the datbaase,
	 * then removes the email so its not read again.
	 *
	 * Returns null if there are no more messages.
	 *
	 * @param string $object_type
	 *
	 * @return \Application\DeskPRO\Entity\EmailSource
	 */
	public function readNext($object_type = 'ticket')
	{
		try {
			$raw_message = $this->_readNext();
		} catch (\Exception $e) {
			$this->logger->log(sprintf("_readNext exception: %s", $e->getMessage()), 'debug');
			if ($this->storage) {
				try { $this->storage->close(); } catch (\Exception $e) {}
			}
			throw $e;
		}

		if (!$raw_message) {
			return null;
		}

		App::getOrm()->beginTransaction();

		try {
			#------------------------------
			# Store the message
			#------------------------------

			$source = new EmailSource();
			$source->fromArray(array(
				'gateway' => $this->gateway,
				'headers' => $raw_message->headers,
				'status' => 'inserted'
			));

			// Rough matching, just for info purposes when browsing a list
			$source->header_to      = Strings::extractRegexMatch('#^To:\s*(.*?)$#m', $raw_message->headers) ?: '';
			$source->header_from    = Strings::extractRegexMatch('#^From:\s*(.*?)$#m', $raw_message->headers) ?: '';
			$source->header_subject = Strings::extractRegexMatch('#^Subject:\s*(.*?)$#m', $raw_message->headers) ?: '';
			$source->object_type    = $object_type;

			if ($raw_message->uid) {
				$source->uid = $raw_message->uid;

				App::getDb()->executeUpdate("
					INSERT IGNORE INTO email_uids
					SET id = ?, gateway_id = ?, date_created = ?
				", array($raw_message->uid, $this->gateway->getId(), date('Y-m-d H:i:s')));

				$this->logger->log(sprintf("Saved UID: %s", $raw_message->uid), 'debug');
			}

			if ($raw_message->too_big) {
				$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromString(
					$raw_message->content,
					'email.eml',
					'message/rfc822'
				);
				$blob_id = $blob->getId();

				// Unset the content now, its not used from here on out
				$raw_message->content = '';

				$source->status = 'error';
				$source->error_code = EmailSource::ERR_MESSAGE_TOO_BIG;
				$source->source_info = array(
					'size' => $raw_message->size,
					'max_size' => $this->max_size
				);
			} else {
				$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromString(
					$raw_message->content,
					'email.eml',
					'message/rfc822'
				);
				$blob_id = $blob->getId();
			}

			$source->blob = $blob;

			App::getOrm()->persist($source);
			App::getOrm()->flush();

			App::getOrm()->commit();

			$this->logger->log(sprintf("Committed message source: %s", $source->getId()), 'debug');

			#------------------------------
			# Delete message on the server
			#------------------------------

			$this->_doneRead($raw_message->id);

		} catch (\Exception $e) {
			$this->logger->log(sprintf("Save source error: %s", $e->getMessage()), 'debug');
			App::getOrm()->rollback();
			if ($this->storage) {
				try { $this->storage->close(); } catch (\Exception $e) {}
			}
			throw $e;
		}

		$source->_raw = $raw_message->content;

		return $source;
	}


	/**
	 * Test the resource to see if configuration is correct and/or that the service
	 * supports the required features.
	 *
	 * @return bool
	 */
	public function test()
	{
		return true;
	}
}
