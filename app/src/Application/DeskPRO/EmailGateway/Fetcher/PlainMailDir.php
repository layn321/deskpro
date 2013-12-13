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

/**
 * Reads all files in a directory as mail.
 *
 * Note that is not to be confused with MailDir (http://en.wikipedia.org/wiki/Maildir),
 * though this fetcher could be used with the 'new' directory of MailDir.
 *
 * But the point is that this does nothing about moving files to cur or setting flags.
 * It's a simple directory scanner.
 */
class PlainMailDir extends AbstractFetcher
{
	/**
	 * @var string
	 */
	protected $maildir;

	/**
	 * @var \Directory
	 */
	protected $dir;

	/**
	 * @var array
	 */
	protected $message_list;

	/**
	 * @var int
	 */
	protected $read_count = 0;

	/**
	 * Initiates the connection
	 */
	protected function _initConnection()
	{
		$this->maildir = $this->gateway['connection_options']['dir'];
		$this->maildir = str_replace('%DP_DATA_DIR%', dp_get_data_dir(), $this->maildir);

		$this->logger->logDebug("Reading from: {$this->maildir}");

		if (is_dir($this->maildir)) {
			$this->dir = dir($this->maildir);
		} else {
			$this->logger->logDebug("Directory does not exist");

			// Dir doesnt exist, but that doesnt mean error
			// Just means no mail. Checking on dir should be a separate test at setup time
			$this->dir = false;
		}

		return $this->dir;
	}

	public function __destruct()
	{
		if ($this->storage && is_resource($this->storage->handle)) {
			try { @$this->storage->close(); } catch (\Exception $e) {}
		}

		$this->storage = null;
		$this->dir = null;
	}


	/**
	 * Read filenames from directory
	 */
	protected function _initMessageList()
	{
		if ($this->message_list !== null) {
			return $this->message_list;
		}

		$this->message_list = array();

		while (($f = $this->dir->read()) !== false) {
			if ($f != '.' && $f !== '..') {
				$this->message_list[] = $f;
			}
		}

		return $this->message_list;
	}


	/**
	 * @return \Directory
	 */
	public function getStorage($reconnect = false)
	{
		if ($this->storage === null) {
			$this->storage = $this->_initConnection();
		}

		return $this->storage;
	}

	/**
	 * Reads the next message in the inbox
	 *
	 * @return \Application\DeskPRO\EmailGateway\Fetcher\RawMessage
	 */
	protected function _readNext()
	{
		if (!$this->getStorage()) {
			return null;
		}

		$this->getStorage();
		$this->_initMessageList();

		$this->read_count++;
		$this->logger->log("Trying to read next ({$this->read_count} call)", 'debug');

		$next = array_shift($this->message_list);
		if (!$next) {
			return null;
		}

		$mailfile = $this->maildir . '/' . $next;

		if (!is_writable($mailfile)) {
			error_log("Skipping mailfile $mailfile because it is not writable so we cant delete it after");
			$this->logger->logError("Skipping mailfile $mailfile because it is not writable so we cant delete it after");
			return $this->_readNext();
		}

		if (dp_get_config('plainmaildir_track_read')) {
			$check_name = md5('plainmaildir::' . $mailfile);
			$check = App::getDb()->fetchColumn("
				SELECT data
				FROM install_data
				WHERE build = ? AND name = ?
				LIMIT 1
			", array(DP_BUILD_TIME, $check_name));

			if ($check) {
				$this->logger->logError("Skipping mailfile $mailfile because it has been marked as read");
				error_log("Skipping mailfile $mailfile because it has been marked as read");
				return $this->_readNext();
			}

			App::getDb()->insert('install_data', array(
				'build' => DP_BUILD_TIME,
				'name'  => $check_name,
				'data'  => 1
			));
		}

		$message_size = filesize($mailfile);

		$start_time = microtime(true);

		$this->logger->log("Fetching message $next", 'debug');

		$raw_message = new RawMessage();
		$raw_message->id = $next;
		$raw_message->size = $message_size;

		$raw_message->content = file_get_contents($mailfile);
		$headers = null;

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

		if ($this->max_size && $raw_message->size > $this->max_size) {
			$raw_message->too_big = true;
		}

		$this->logger->log(sprintf("Got message Took %0.2f seconds.", microtime(true) - $start_time), 'debug');

		return $raw_message;
	}

	/**
	 * Deletes the message from the server.
	 *
	 * @param  $id
	 */
	protected function _doneRead($id)
	{
		$this->logger->log("Marking message as deleted: $id", 'debug');

		if (is_file($this->maildir . '/' . $id) && !@unlink($this->maildir . '/' . $id)) {
			sleep(1);
			if (is_file($this->maildir . '/' . $id) && !unlink($this->maildir . '/' . $id)) {
				$this->logger->logError("Failed to delete source file: " . $this->maildir . '/' . $id);
			}
		}
	}


	/**
	 * Tests the connection and returns the number of messages on success
	 *
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function test()
	{
		if (!is_dir($this->maildir)) {
			throw new \InvalidArgumentException("Mail directory does not exist");
		}

		return 0;
	}
}
