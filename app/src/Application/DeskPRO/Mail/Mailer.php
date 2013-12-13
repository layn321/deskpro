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
 * @category Mail
 */

namespace Application\DeskPRO\Mail;

use Application\DeskPRO\App;

use Orb\Log\Logger;
use Orb\Log\Loggable;
use Orb\Util\Strings;
use Orb\Util\Util;

require_once(DP_ROOT . '/vendor/swiftmailer/lib/swift_required.php');

/**
 * This transport takes care of initializing any other transports based on settings
 * etc, and also queuing.
 */
class Mailer extends \Swift_Mailer implements Loggable
{
	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
	 */
	protected $templating;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var array
	 */
	protected $queued = array();

	/**
	 * @var \Orb\Log\Writer\ArrayWriter
	 */
	protected $messagesLog;

	/**
	 * @var bool
	 */
	protected $default_queue_hint = true;

	/**
	 * @var bool
	 */
	protected $is_sending_queue = false;

	public function __construct(\Swift_Transport $transport, \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating, Logger $logger = null)
	{
		$tmpdir = dp_get_tmp_dir() . '/swiftmailer-cache';
		if (!is_dir(dp_get_tmp_dir() . '/swiftmailer-cache')) {
			if (!@mkdir($tmpdir, 0777, true)) {
				$tmpdir = sys_get_temp_dir() . '/dp-swiftmailer-cache';
				if (!is_dir($tmpdir)) {
					@mkdir($tmpdir, 0777, true);
				}
			}
		}

		if (!is_dir($tmpdir) || !is_writable($tmpdir)) {
			// Fall back on system tmp dir
			$tmpdir = sys_get_temp_dir();
		}

		\Swift_Preferences::getInstance()->setTempDir($tmpdir);
		$GLOBALS['DP_SWIFTMAIL_TMPDIR'] = $tmpdir;

		$this->messagesLog = new \Orb\Log\Writer\ArrayWriter();

		if ($logger) {
			$this->setLogger($logger);
		}

		$this->templating = $templating;

		$this->getLogger()->logInfo(sprintf("Setting transport: %s", get_class($transport)));
		if ($transport instanceof \Orb\Log\Loggable) {
			$transport->setLogger($this->getLogger());
		}

		parent::__construct($transport);

		if (App::getConfig('debug.mail.force_to')) {
			$this->getLogger()->logInfo(sprintf("debug.mail.force_to on: %s", App::getConfig('debug.mail.force_to')));
			$this->registerPlugin(new \Orb\Mail\Plugins\ForceToAddress(App::getConfig('debug.mail.force_to')));
		}

		if (App::getConfig('debug.mail.save_to_file')) {
			$filepath = App::getConfig('debug.mail.save_to_file');
			if ($filepath === true || is_numeric($filepath)) {
				$filepath = '%log_dir%/emails';
			}

			$filepath = str_replace('%log_dir%', App::getLogDir(), $filepath);
			if (!is_dir($filepath)) {
				@mkdir($filepath, 0777);
			}

			$this->getLogger()->logInfo(sprintf("debug.mail.save_to_file on: %s", $filepath));

			$plugin = new \Orb\Mail\Plugins\DebugToFile($filepath, App::getConfig('debug.mail.disable_send', false));

			if ($info_path = App::getConfig('debug.mail.save_to_file_info')) {
				if ($info_path === true || is_numeric($info_path)) {
					$info_path = '%log_dir%/emails-info';
				}

				$info_path = str_replace('%log_dir%', App::getLogDir(), $info_path);
				if (!is_dir($info_path)) {
					@mkdir($info_path, 0777);
				}

				$plugin->setInfoFilePath($info_path);
			}

			$this->registerPlugin($plugin);

		} else if (App::getConfig('debug.mail.disable_send')) {
			// As an elseif becaue the DebugToFile can also disable send
			// If CancelSend is registered first, then the DebugToFile wont fire either
			// and we'll just have nothing

			$this->getLogger()->logInfo("debug.mail.disable_send");

			$this->registerPlugin(new \Orb\Mail\Plugins\CancelSend());
		}

		try {
			$default = App::getSetting('core.default_from_email');
			$name    = App::getSetting('core.deskpro_name');

			if (!$default) {
				if (!empty($_SERVER['HOST_NAME'])) {
					$default = 'deskpro@' . $_SERVER['HOST_NAME'];
				} elseif (@php_uname('n')) {
					$default = 'deskpro@' . php_uname('n');
				} else {
					$default = 'deskpro@localhost';
				}
			}

			$this->getLogger()->logInfo(sprintf("Default from: %s <%s>", $name, $default));

			if ($default) {
				$this->registerPlugin(new \Orb\Mail\Plugins\DefaultFromAddress($default, $name));
			}
		} catch (\Exception $e) {}

		// After successful runs, send queued messages
		\DpShutdown::add(array($this, 'sendQueuedSilent'), null, 'db_done_trans_commit');
		\DpShutdown::add(array($this, 'sendQueuedSilent'), null, 'shutdown', 1000);

		// If there was an error and db is being rolled back,
		// clear any queued messages made during the transaction
		\DpShutdown::add(array($this, 'clearQueuedMessages'), null, 'db_done_trans_rollback');
	}


	/**
	 * @param bool $on_or_off
	 */
	public function setDefaultQueueHint($on_or_off)
	{
		$this->default_queue_hint = $on_or_off;
	}


	/**
	 * @return array
	 */
	public function getLogMessages()
	{
		return $this->messagesLog->getMessages();
	}

	/**
	 * Get the logger
	 *
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		if (!$this->logger) {
			$this->logger = new Logger();
		}

		return $this->logger;
	}


	/**
	 * Set the logger used
	 *
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
		$this->logger->addWriter($this->messagesLog);
	}


	/**
	 * @static
	 * @param \Swift_Transport $transport
	 * @return \Application\DeskPRO\Mail\Mailer
	 */
	public static function newInstance(\Swift_Transport $transport)
	{
		$templating = App::get('templating');
		$inst = self($transport, $templating);
		$inst->setLogger(App::getSystemService('mail_logger'));

		return $inst;
	}


	/**
	 * @return \Application\DeskPRO\Mail\Message
	 */
	public function createMessage($service = 'message')
	{
		if ($service == 'message') {
			$message = \Application\DeskPRO\Mail\Message::newInstance();
			$message->setEncoder(\Swift_Encoding::get8BitEncoding());
			$message->setTemplateEngine($this->templating);
			$message->enableQueueHint();
			return $message;
		}

		return parent::createMessage($service);
	}


	/**
	 * @param string $id
	 * @return bool
	 */
	public function hasQueuedMessage($id)
	{
		return isset($this->queued[$id]);
	}


	/**
	 * @param string $id
	 * @return \Application\DeskPRO\Mail\Message
	 */
	public function getQueuedMessage($id)
	{
		return isset($this->queued[$id]) ? $this->queued[$id] : null;
	}


	/**
	 * @param string $id
	 */
	public function removeQueuedMessage($id)
	{
		unset($this->queued[$id]);
	}


	/**
	 * Remove all queued messages so they wont sent (eg database error so dont send messages)
	 */
	public function clearQueuedMessages()
	{
		$this->queued = array();
	}


	/**
	 * @return array
	 */
	public function getAllQueuedMessages()
	{
		return $this->queued;
	}


	/**
	 * Queues a message to send. Usually they are sent during shutdown, or any time
	 * sendQueued or sendQueuedSilent is called.
	 *
	 * If you want to send now, use sendNow.
	 *
	 * Returns a truthy ID of the queued message.
	 * $failedRecipients will always stay null becaue messages arent attempted to send yet.
	 *
	 * @param \Swift_Mime_Message $message
	 * @param null $failedRecipients
	 * @return string
	 */
	public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$id = str_replace('.', '_', uniqid('eml', true));

		if ($message instanceof \Application\DeskPRO\Mail\Message) {
			// Need to prepare right away so any context-sensitive changes affect
			// the template. E.g., language context might be temporarily changed.
			$message->prepare();
		}
		$this->queued[$id] = $message;

		return $id;
	}


	/**
	 * Send all of the queued messages and return the status of each.
	 * Exceptions are not caught, so if an error happens then possible
	 * only part of the queue is sent.
	 *
	 * @return array
	 */
	public function sendQueued()
	{
		if ($this->is_sending_queue) return array();
		$this->is_sending_queue = true;

		$status = array();

		$keys = array_keys($this->queued);
		foreach ($keys as $k) {
			if (!isset($this->queued[$k])) continue;

			$message = $this->queued[$k];
			unset($this->queued[$k]);

			try {
				$status[$k] = $this->sendNow($message);
			} catch (\Exception $e) {
				$this->is_sending_queue = false;
				throw $e;
			}
		}

		$this->is_sending_queue = false;

		return $status;
	}


	/**
	 * Send all of the queued messages and dont die on errors (theyre still logged though).
	 * This is usually called from shutdown.
	 *
	 * @return array
	 */
	public function sendQueuedSilent()
	{
		if ($this->is_sending_queue) return array();
		$this->is_sending_queue = true;

		$status = array();

		$keys = array_keys($this->queued);

		foreach ($keys as $k) {
			if (!isset($this->queued[$k])) continue;

			$message = $this->queued[$k];
			unset($this->queued[$k]);

			try {
				$status[$k] = $this->sendNow($message);
			} catch (\Exception $e) {
				$status[$k] = false;

				$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);
			}
		}

		$this->is_sending_queue = false;

		return $status;
	}


	/**
	 * Count the number of queued messages
	 *
	 * @return int
	 */
	public function countQueued()
	{
		if (!$this->queued) {
			return 0;
		}

		return count($this->queued);
	}


	/**
	 * Send a message now (dont queue for shutdown func)
	 *
	 * @param \Swift_Mime_Message $message
	 * @param null $failedRecipients
	 * @return int
	 */
	public function sendNow(\Swift_Mime_Message $message, &$failedRecipients = null)
	{
		return parent::send($message, $failedRecipients);
	}
}
