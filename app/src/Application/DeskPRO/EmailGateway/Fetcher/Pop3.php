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
use Application\DeskPRO\Entity;
use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Util\Numbers;
use Zend\Mail\Protocol\Exception\RuntimeException;

/**
 * Fetches mail from a pop3 server
 */
class Pop3 extends AbstractFetcher
{
	protected $read_count = 0;

	/**
	 * @var array
	 */
	protected $message_list = null;

	/**
	 * @var array
	 */
	protected $message_list_ids = array();

	/**
	 * @var array
	 */
	protected $message_files = array();

	/**
	 * The size in bytes a message must be before the "memory protection"
	 * features are enabled.
	 *
	 * @var int
	 */
	protected $memory_protection_size = 0;

	/**
	 * @var bool
	 */
	protected $done_read_finished = false;

	/**
	 * Files written as part of the memory protection.
	 * These should be removed after a successful read.
	 *
	 * @var array
	 */
	protected $backup_file = array();

	public function init()
	{
		$this->memory_protection_size = 3670016;
	}

	/**
	 * Initiates the connection
	 *
	 * @return \Zend\Mail\Storage\Pop3
	 */
	protected function _initConnection()
	{
		$options = array();
		$options['host']     = isset($this->gateway['connection_options']['host'])     ? $this->gateway['connection_options']['host']     : 'localhost';
		$options['port']     = isset($this->gateway['connection_options']['port'])     ? $this->gateway['connection_options']['port']     : '110';
		$options['user']     = isset($this->gateway['connection_options']['username']) ? $this->gateway['connection_options']['username'] : '';
		$options['password'] = isset($this->gateway['connection_options']['password']) ? $this->gateway['connection_options']['password'] : '';

		$this->logger->log("Connecting with user {$options['user']} to {$options['host']}:{$options['port']}", 'debug');

		if (isset($this->gateway['connection_options']['secure']) AND $this->gateway['connection_options']['secure']) {
			$options['ssl'] = strtoupper($this->gateway['connection_options']['secure']); // 'ssl' or 'tls'
			$this->logger->log('SSL Enabled', 'debug');
		}

		$options['logger'] = $this->logger;

		$storage = new \Application\DeskPRO\EmailGateway\Storage\Pop3($options);
		return $storage;
	}

	public function close()
	{
		if ($this->storage) {
			$this->storage->close();
			$this->storage = null;
		}
	}

	public function resetConnection()
	{
		$this->logger->logDebug('Resetting connection');
		$this->getStorage(true);
		$this->_initMessageList(true);
	}

	/**
	 * Server supports uniqid?
	 *
	 * @return mixed
	 */
	protected function canUniqueId()
	{
		static $can = null;

		if ($can === null) {
			try {
				$can = $this->getStorage()->canUniqueId();
			} catch(\Exception $e) {}
		}

		return $can;
	}

	/**
	 * Get a list of message IDs
	 */
	protected function _initMessageList($reload = false)
	{
		if (!$reload && $this->message_list !== null) {
			return;
		}

		if ($this->gateway->keep_read) {
			if (!$this->canUniqueId()) {
				try {
					$capas = $this->getStorage()->getProtocolCapabilities();
					$capas = implode(', ', $capas);
				} catch (\Exception $e) {
					$capas = '<unknown>';
				}
				$this->logger->log("Gateway does not support unique but keep_read is enabled. Capabilities: $capas", 'debug');

				$e = new \InvalidArgumentException("Gateway does not support uniqueid");
				$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
				$einfo['no_send_error'] = true;
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);

				$this->message_list = array();
				return;
			}

			$id_to_num = array_flip($this->getStorage()->getUniqueId());

			$this->logger->log("Server has " . count($id_to_num) . " messages", 'debug');

			if (count($id_to_num) > 2500) {
				$this->logger->log("Server has >= 2500 messages, breaking", 'ERR');
				$this->message_list = array();

				$e = new \InvalidArgumentException("POP3 server has >= 2500 messages and 'keep read' setting is enbaled. Clean out old messages and try again.");
				$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
				$einfo['no_send_error'] = true;
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);

				return;
			}

			$read_ids = App::getDb()->fetchAllCol("
				SELECT id
				FROM email_uids
				WHERE gateway_id = ?
			", array($this->gateway->getId()));

			$this->logger->log("System has " . count($read_ids) . " tracked IDs", 'debug');

			foreach ($read_ids as $id) {
				if (isset($id_to_num[$id])) {
					$this->logger->log(sprintf("Skipping message #%s because UID %s", $id_to_num[$id], $id), 'debug');
					unset($id_to_num[$id]);
				}
			}

			$this->message_list_ids  = array_flip($id_to_num);

			$list = $this->getStorage()->getSize();

			$this->message_list = array();
			foreach ($list as $num => $size) {
				if (isset($this->message_list_ids[$num])) {
					$this->message_list[] = array('num' => $num, 'size' => $size, 'uid' => $this->message_list_ids[$num]);
				}
			}

			$this->logger->log("Message list contains " . count($this->message_list) . " new messages", 'debug');

		} else {
			$list = $this->getStorage()->getSize();

			$this->message_list = array();
			foreach ($list as $num => $size) {
				$this->message_list[] = array('num' => $num, 'size' => $size, 'uid' => null);
			}

			$this->logger->log("Message list contains " . count($this->message_list) . " messages", 'debug');
		}
	}

	/**
	 * Reads the next message in the inbox
	 *
	 * @return \Application\DeskPRO\EmailGateway\Fetcher\RawMessage
	 */
	protected function _readNext()
	{
		$this->done_read_finished = null;

		$this->getStorage();
		$this->_initMessageList();

		$this->read_count++;
		$this->logger->log("Trying to read next ({$this->read_count} call)", 'debug');

		$next = array_shift($this->message_list);
		if (!$next) {
			return null;
		}

		$message_size = $next['size'];
		$message_num  = $next['num'];
		$message_id   = $next['uid'];

		// Memory protection enables writing the email to a file and then immediately
		// deleting it from the server (which causes a server disconnect/reconnect).
		// So if theres a crash, the same message wont be attempted next run and hold
		// up all other messages.
		$memory_protection = false;
		if ($this->memory_protection_size && $message_size >= $this->memory_protection_size) {
			$memory_protection = true;
		}

		if (!$message_id && $this->canUniqueId()) {
			try {
				$message_id = $this->getStorage()->getProtocol()->uniqueid($message_num);
			} catch (\Exception $e) {}
		}

		$start_time = microtime(true);

		$this->logger->log("Fetching message #$message_num", 'debug');

		$raw_message = new RawMessage();
		$raw_message->id   = $message_num;
		$raw_message->uid  = $message_id;
		$raw_message->size = $message_size;

		if ($this->max_size && $raw_message->size && $raw_message->size > $this->max_size) {
			$raw_message->content = $this->getStorage()->getProtocol()->top($message_num) . "\n\n";
		} else {
			if ($memory_protection) {
				$this->logger->logInfo('Memory protected enabled');
				$content_file = dp_get_backup_dir() . '/eml-' . uniqid('', true) . '.eml';
				$fp = fopen($content_file, 'w');
				if ($fp) {
					$this->getStorage()->getProtocol()->retrieveToStream($message_num, $fp);
					fclose($fp);

					$this->_doneRead($message_num);
					$this->done_read_finished = $message_num;

					// Disconnect from server so message is deleted now
					$this->resetConnection();
				} else {
					$memory_protection = false;
					$e = new \RuntimeException("Could not save email backup file to {$raw_message->content_file}");
					KernelErrorHandler::logException($e, false);
				}

				$this->logger->logInfo('Message source saved to: ' . $content_file);
				$raw_message->content = file_get_contents($content_file);
				$this->backup_file = $content_file;
			}

			if (!$memory_protection) {
				$raw_message->content = $this->getStorage()->getProtocol()->retrieve($message_num);
			}
		}
		$headers = null;

		$this->logger->log(sprintf("Message size: %s bytes", $message_size), 'debug');

		if ($raw_message->uid) {
			$this->logger->log(sprintf("Message UID: %s", $raw_message->uid), 'debug');
		}

		$EOL = "\n";
		if (strpos($raw_message->content, $EOL . $EOL)) {
			list($headers, ) = explode($EOL . $EOL, $raw_message->content, 2);
		} else if ($EOL != "\r\n" && strpos($raw_message->content, "\r\n\r\n")) {
			list($headers, ) = explode("\r\n\r\n", $raw_message->content, 2);
		} else if ($EOL != "\n" && strpos($raw_message->content, "\n\n")) {
			list($headers, ) = explode("\n\n", $raw_message->content, 2);
		} else {
			@list($headers, ) = @preg_split("%([\r\n]+)\\1%U", $raw_message->content, 2);
		}

		$raw_message->headers = $headers;

		if (!$raw_message->size) {
			$raw_message->size = strlen($raw_message->content);
		}

		if ($this->max_size && $raw_message->size > $this->max_size) {
			$raw_message->too_big = true;
			$this->logger->log("Setting too_big flag", 'debug');
		}

		$this->logger->log(sprintf("Got message %d %s. Took %0.2f seconds.", $message_num, $message_id, microtime(true) - $start_time), 'debug');

		return $raw_message;
	}

	/**
	 * Deletes the message from the server.
	 *
	 * @param  $id
	 */
	protected function _doneRead($id)
	{
		if ($this->backup_file) {
			unlink($this->backup_file);
			$this->backup_file = null;
		}

		if ($this->done_read_finished !== null && $this->done_read_finished == $id) {
			return;
		}

		if ($this->gateway->keep_read) {
			$this->logger->log(sprintf("Done read, but keep_read is enabled"), 'debug');
			return;
		}

		$this->logger->log("Marking message as deleted: $id", 'debug');
		try {
			$this->getStorage()->removeMessage($id);
		} catch (\Exception $e) {
			$this->logger->log("Exception: {$e->getMessage()} {$e->getTraceAsString()}", 'crit');
			throw $e;
		}
	}


	/**
	 * Tests the connection and returns the number of messages on success
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function test()
	{
		try {
			$x = $this->getStorage()->countMessages();
		} catch (\Exception $e) {
			throw $e;
		}

		return $x;
	}
}
