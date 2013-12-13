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

namespace Application\DeskPRO\Mail;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Blob;
use Application\DeskPRO\Entity\SendmailQueue;
use Application\DeskPRO\Mail\Transport\DeskproQueueTransport;
use Cloud\Mail\Transport\DelegatingTransport;
use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Log\Loggable;
use Orb\Log\Logger;
use Orb\Util\Strings;

class SendmailQueueRunner implements Loggable
{
	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @var \Application\DeskPRO\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\Mail\Mailer
	 */
	protected $mailer;

	/**
	 * @var \Application\DeskPRO\BlobStorage\DeskproBlobStorage
	 */
	protected $blob_storage;

	public function __construct()
	{
		$this->db           = App::getDb();
		$this->em           = App::getOrm();
		$this->mailer       = App::getMailer();
		$this->blob_storage = App::getContainer()->getBlobStorage();
		$this->logger       = new Logger();
	}

	/**
	 * Set the logger
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}


	/**
	 * @param int $limit
	 * @return int
	 */
	public function run($limit = 0, $time_limit = 0)
	{
		App::getContainer()->getSettingsHandler()->setTemporarySettingValues(array('core.use_mail_queue' => 'never'));
		if ($this->mailer->getTransport() instanceof DelegatingTransport) {
			$this->mailer->getTransport()->disableQueue();
		}

		$count = 0;
		$mtime = microtime(true);
		$time_start = time();

		$this->logger->logInfo("Run queue with limit($limit) and time_limit($time_limit)");

		while ($sendmail = $this->getNext()) {
			$count++;

			if ($limit && $count >= $limit) {
				$this->logger->logDebug("Hit limit ($limit), breaking");
				break;
			}

			if ($time_limit && time() - $time_start > $time_limit) {
				$this->logger->logDebug("Hit time limit ($time_limit), breaking");
				break;
			}

			$blob = $this->em->find('DeskPRO:Blob', $sendmail['blob_id']);
			$sendmail['blob'] = $blob;

			try {
				$this->sendQueuedMessage($sendmail);
			} catch (\Exception $e) {
				$this->logger->logInfo(sprintf("Error processing message %d: %s %s", $sendmail['id'], $e->getCode(), $e->getMessage()));
				KernelErrorHandler::logException($e, true);
			}
		}

		$this->logger->logInfo(sprintf("Processed $count messages in %.4fs", microtime(true) - $mtime));

		return $count;
	}


	/**
	 * Send a single message
	 *
	 * @param SendmailQueue $sendmail
	 */
	public function sendQueuedMessage($sendmail)
	{
		$mtime = microtime(true);
		$this->logger->logInfo("Start #{$sendmail['id']}: " . $sendmail['subject']);

		// Update next time so if we happen to crash, it doesnt constantly rerun
		$this->db->update(
			'sendmail_queue',
			array('date_next_attempt' => null),
			array('id' => $sendmail['id'])
		);

		$message = null;

		$type = Strings::getExtension($sendmail['blob']->filename);
		try {
			if ($type == 'obj') {
				$success = $this->sendObjectBlob($sendmail['blob'], $message);
			} elseif ($type == 'job') {
				$success = $this->sendJobBlob($sendmail['blob']);
			} else {
				$this->logger->logError("Unknown message type for message {$sendmail['id']}");
				return;
			}
		} catch (\Exception $e) {
			$this->logger->logError("Sending message failed for #{$sendmail['id']}: {$e->getMessage()}");
			KernelErrorHandler::logException($e, false);
			$success = false;
		}

		if ($success) {
			$this->blob_storage->deleteBlobRecord($sendmail['blob']);
			$this->db->delete('sendmail_queue', array('id' => $sendmail['id']));
		} else {

			switch ($sendmail['attempts']) {
				case 0:
				case 1:
					$next_attempt = strtotime('+1 minutes');
					break;

				case 2:
					$next_attempt = strtotime('+5 minutes');
					break;

				case 3:
					$next_attempt = strtotime('+10 minutes');
					break;

				default:
					$next_attempt = null;
					break;
			}

			if ($message && $message instanceof \Application\DeskPRO\Mail\Message) {
				$sendmail['log'] = $sendmail['log'] . "\n" . $message->getLogMessages();

				$len = strlen($sendmail['log']);
				if ($len > 25000) {
					$trim = $len - 25000;
					if ($trim > 1000) {
						$sendmail['log'] = "(Truncated)\n\n" . substr($sendmail['log'], -25000);
					}
				}

				$sendmail['log'] = trim($sendmail['log']);
			}

			$this->db->update(
				'sendmail_queue',
				array('date_next_attempt' => $next_attempt, 'attempts' => $sendmail['attempts']+1, 'log' => $sendmail['log']),
				array('id' => $sendmail['id'])
			);
		}

		$this->logger->logInfo(sprintf("Done in %.4fs", microtime(true) - $mtime));
	}


	/**
	 * Re-sends the job through the normal system
	 *
	 * @param Blob $blob
	 */
	public function sendObjectBlob(Blob $blob, &$message = null)
	{
		$this->logger->logDebug("Sending object message");

		$message_raw = $this->blob_storage->copyBlobRecordToString($blob);
		$message = unserialize($message_raw);

		// A bug in previous versions has the data being a serialised string of the
		// serialised string, so we need to double-unserialize it to get the actual obj.
		if ($message && is_string($message)) {
			$message = unserialize($message);
		}

		if (!$message || !is_object($message)) {
			$e = new \Exception("SendmailQueue: Failed to unserialize message blob. Blob #{$blob['id']}");
			$einfo = KernelErrorHandler::getExceptionInfo($e);
			$einfo['context_data'] = substr($message_raw, 0, 512000);
			KernelErrorHandler::logToFile($einfo);

			$this->logger->logError("Failed to unserialize message blob #{$blob['id']}");
			return false;
		}

		if ($message instanceof \Orb\Mail\Message) {
			$message->disableQueueHint();
		}
		if ($message instanceof \Application\DeskPRO\Mail\Message) {
			$message->setIsRetrying();
		}

		try {
			$success = $this->mailer->sendNow($message);
		} catch (\Exception $e) {
			KernelErrorHandler::logException($e, false);
			$success = false;
		}

		if ($success) {
			$this->logger->logDebug("Send success");
		} else {
			$this->logger->logDebug("Send failed");
		}

		return $success;
	}


	/**
	 * Re-sends the job to the sendmail queue server
	 *
	 * @param Blob $blob
	 */
	public function sendJobBlob(Blob $blob)
	{
		if (!defined('DP_SMTP_DESKPRO_QUEUE_HOST') || !DP_SMTP_DESKPRO_QUEUE_HOST || !defined('DP_SMTP_DESKPRO_QUEUE_PORT') || !DP_SMTP_DESKPRO_QUEUE_PORT) {
			throw new \RuntimeException("The DeskPRO queue server is not being used");
		}

		$tmppath = tempnam(sys_get_temp_dir(), 'dpe');
		$this->blob_storage->copyBlobRecordToFile($tmppath, $blob);

		$tr = DeskproQueueTransport::newInstance(DP_SMTP_DESKPRO_QUEUE_HOST, DP_SMTP_DESKPRO_QUEUE_PORT);
		$tr->sendJobFile($tmppath);

		return true;
	}


	/**
	 * @return \Application\DeskPRO\Entity\SendmailQueue
	 */
	public function getNext()
	{
		$date = date('Y-m-d H:i:s');
		$this->logger->logDebug("Getting next send with date < $date");

		$next = $this->db->fetchAssoc("
			SELECT * FROM sendmail_queue
			WHERE date_next_attempt < ? AND blob_id IS NOT NULL
			ORDER BY priority DESC, date_next_attempt ASC
			LIMIT 1
		", array($date));

		if ($next) {
			$this->logger->logDebug("Got next: {$next['id']}");
			return $next;
		}

		$this->logger->logDebug("There is no next");
		return null;
	}
}