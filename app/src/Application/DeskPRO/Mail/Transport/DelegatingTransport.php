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
 * Orb
 *
 * @package Orb
 * @subpackage Mail
 */

namespace Application\DeskPRO\Mail\Transport;

use Application\DeskPRO\App;

use Application\DeskPRO\Mail\Loggers\MessageLogWriter;
use Application\DeskPRO\Mail\QueueProcessor\Database as DatabaseQueueProcessor;
use Orb\Log\Filter\SimpleLineFormatter;
use Orb\Mail\Transport\QueueTransport;
use Orb\Mail\Message;
use Orb\Util\Strings;
use Orb\Util\Util;
use Orb\Log\Logger;
use Orb\Log\Loggable;
use Orb\Log\Writer\ArrayWriter as LogArrayWriter;

/**
 * This transport takes care of initializing any other transports based on settings
 * etc, and also queuing.
 */
class DelegatingTransport implements \Swift_Transport, Loggable
{
	/**
	 * @var bool
	 */
	protected $queue_disabled = false;

	/**
	 * @var \Swift_Events_EventDispatcher
	 */
	protected $event_dispatcher;

	/**
	 * @var array
	 */
	protected $transports = array();

	/**
	 * @var \Orb\Mail\Transport\QueueTransport
	 */
	protected $queue_transport = null;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var \Application\DeskPRO\EmailGateway\AddressMatcher
	 */
	protected $gateway_address_matcher;

	/**
	 * @var \Orb\Log\Writer\ArrayWriter
	 */
	protected $message_log_writer;

	/**
	 * @param \Swift_Events_EventDispatcher $event_dispatcher
	 */
	public function __construct(\Swift_Events_EventDispatcher $event_dispatcher)
	{
		$this->event_dispatcher = $event_dispatcher;

		// If we are using a queue server,
		// dont enable the local deskpro queue that runs on cron
		if (defined('DP_SMTP_USE_DESKPRO_QUEUE')) {
			$this->disableQueue();
		}

		$this->message_log_writer = new MessageLogWriter();
		$this->message_log_writer->addFilter(new SimpleLineFormatter());
	}


	/**
	 * Disable the use of the queue, all messages are sent instantly.
	 * Note that messages are still saved in the event of a send failure.
	 */
	public function disableQueue()
	{
		$this->queue_disabled = true;
	}


	/**
	 * Enable the queue.
	 */
	public function enableQueue()
	{
		$this->queue_disabled = false;
	}


	/**
	 * Is queueing currently enabeld?
	 *
	 * @return bool
	 */
	public function isQueueEnabled()
	{
		return !$this->queue_disabled;
	}


	/**
	 * Get the queue transport with the database queue processor.
	 * The queue will be created if it has not already been.
	 *
	 * @return \Orb\Mail\Transport\QueueTransport
	 */
	public function getQueueTransport()
	{
		if ($this->queue_transport !== null) return $this->queue_transport;

		$this->queue_transport = new QueueTransport($this->event_dispatcher);

		$this->attachLoggerOnce($this->queue_transport);

		return $this->queue_transport;
	}

	/**
	 * @param \Swift_Mime_Message $message
	 * @param null $failedRecipients
	 * @return int
	 */
	public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$this->message_log_writer->clearCurrentMessage();
		$this->message_log_writer->setCurrentMessageIfValid($message);

		try {
			$ret = $this->_doSend($message, $failedRecipients);
			$this->message_log_writer->clearCurrentMessage();
		} catch (\Exception $e) {
			$this->message_log_writer->clearCurrentMessage();
			throw $e;
		}

		return $ret;
	}

	/**
	 * @param \Swift_Mime_Message $message
	 * @param null $failedRecipients
	 * @return int
	 */
	public function _doSend(\Swift_Mime_Message $message, &$failedRecipients = null)
	{
		// Reset max exec time when sending a message
		if (isset($GLOBALS['DP_PREF_MAX_EXEC_TIME'])) {
			set_time_limit($GLOBALS['DP_PREF_MAX_EXEC_TIME']);
		}

		$time_top = microtime(true);
		$this->getLogger()->logDebug(sprintf("[DelegatingTransport] Begin message :: %s %s", implode(',', (array)$message->getTo()), $message->getSubject()));

		if ($message instanceof Message) {
			$time = microtime(true);
			try {
				$message->prepare();
			} catch (\Exception $e) {
				$this->getLogger()->logError(sprintf("[DelegatingTransport] ERROR preparing: %s %s %s", $e->getCode(), get_class($e), $e->getMessage()));
				throw $e;
			}
			$this->getLogger()->logDebug(sprintf("[DelegatingTransport] Preparing message took %.4f seconds", microtime(true)-$time));
		}

		if ($message->isQueueHinted()) {
			$this->getLogger()->logInfo(sprintf("[DelegatingTransport] Message is queue hinted"));
		}

		$queue_pref = App::getSetting('core.use_mail_queue');
		$use_queue = false;
		if ($queue_pref == 'always' OR ($queue_pref == 'hint' AND $message->isQueueHinted())) {
			$this->getLogger()->logInfo(sprintf("[DelegatingTransport] Message is set for queue"));
			$use_queue = true;
		}

		if ($use_queue && !$this->isQueueEnabled()) {
			$this->getLogger()->logInfo(sprintf("[DelegatingTransport] Queueing is disabled"));
			$use_queue = false;
		}

		$is_retrying = false;
		if ($message instanceof \Application\DeskPRO\Mail\Message) {
			$is_retrying = $message->getIsRetrying();
		}

		if ($is_retrying && $use_queue) {
			$this->getLogger()->logInfo(sprintf("[DelegatingTransport] Message is retrying (no queue will happen)"));
			$use_queue = false;
		}

		$bcc_list = App::getSetting('core.bcc_all_emails');
		if ($bcc_list) {
			$this->getLogger()->logInfo(sprintf("[DelegatingTransport] core.bcc_all_emails on: ", implode(',', $bcc_list)));
			foreach (explode(',',$bcc_list) as $bcc_e) {
				$message->addBcc(trim($bcc_e));
			}
		}

		$success = false;

		if ($message->getSpecificTransport()) {
			$tr = $message->getSpecificTransport();

			$this->getLogger()->logInfo(sprintf("[DelegatingTransport] Specific transport requested: %s", get_class($tr)));
			$this->attachLoggerOnce($tr);

			if (!$tr->isStarted()) $tr->start();

			if ($message instanceof \Orb\Mail\Message) {
				$message->preSend();
			}

			if ($evt = $this->event_dispatcher->createSendEvent($this, $message)) {
				try {
					$this->event_dispatcher->dispatchEvent($evt, 'beforeSendPerformed');
				} catch (\Exception $e) {
					$this->getLogger()->logError(sprintf("[DelegatingTransport] ERROR executing beforeSendPerformed: %s %s %s", $e->getCode(), get_class($e), $e->getMessage()));
					throw $e;
				}
				if ($evt->bubbleCancelled()) {
					$this->getLogger()->logInfo("[DelegatingTransport] beforeSendPerformed cancelled message");
					return 0;
				}
			}

			$success = $tr->send($message, $failedRecipients);
		} elseif ($use_queue) {
			$tr = $this->getQueueTransport();
			if (!$tr->isStarted()) $tr->start();

			$this->attachLoggerOnce($tr);

			$this->getLogger()->logInfo(sprintf("[DelegatingTransport] Sending to queue transport: %s", get_class($tr)));

			if ($message instanceof \Orb\Mail\Message) {
				$message->preSend();
			}

			if ($evt = $this->event_dispatcher->createSendEvent($this, $message)) {
				try {
					$this->event_dispatcher->dispatchEvent($evt, 'beforeSendPerformed');
				} catch (\Exception $e) {
					$this->getLogger()->logError(sprintf("[DelegatingTransport] ERROR executing beforeSendPerformed: %s %s %s", $e->getCode(), get_class($e), $e->getMessage()));
					throw $e;
				}
				if ($evt->bubbleCancelled()) {
					$this->getLogger()->logInfo("[DelegatingTransport] beforeSendPerformed cancelled message");
					return 0;
				}
			}

			$success = $tr->send($message);
		} else {
			try {
				$tr = $this->getTransportForMessage($message);
				if (!$tr->isStarted()) $tr->start();

				$this->attachLoggerOnce($tr);

				$this->getLogger()->logInfo(sprintf("[DelegatingTransport] Using detected transport: %s", get_class($tr)));

				if ($message instanceof \Orb\Mail\Message) {
					$message->preSend();
				}

				if ($evt = $this->event_dispatcher->createSendEvent($this, $message)) {
					try {
						$this->event_dispatcher->dispatchEvent($evt, 'beforeSendPerformed');
					} catch (\Exception $e) {
						$this->getLogger()->logError(sprintf("[DelegatingTransport] ERROR executing beforeSendPerformed: %s %s %s", $e->getCode(), get_class($e), $e->getMessage()));
						throw $e;
					}
					if ($evt->bubbleCancelled()) {
						$this->getLogger()->logInfo("[DelegatingTransport] beforeSendPerformed cancelled message");
						return 0;
					}
				}

				$success = $tr->send($message, $failedRecipients);
			} catch (\Swift_TransportException $e) {
				$this->getLogger()->logInfo(sprintf("[DelegatingTransport] Send failed: %s %s %s", $e->getCode(), get_class($e), $e->getMessage()));
				$success = false;
			}

			if (!$success) {
				$this->getLogger()->logInfo("[DelegatingTransport] Send failed");
				if (!$is_retrying && $this->isQueueEnabled()) {
					$this->getLogger()->logInfo("[DelegatingTransport] Saving to queue to retry later");

					if ($message instanceof \Orb\Mail\Message) {
						$message->preSend();
					}

					if ($evt = $this->event_dispatcher->createSendEvent($this, $message)) {
						try {
							$this->event_dispatcher->dispatchEvent($evt, 'beforeSendPerformed');
						} catch (\Exception $e) {
							$this->getLogger()->logError(sprintf("[DelegatingTransport] ERROR executing beforeSendPerformed: %s %s %s", $e->getCode(), get_class($e), $e->getMessage()));
							throw $e;
						}
						if ($evt->bubbleCancelled()) {
							$this->getLogger()->logInfo("[DelegatingTransport] beforeSendPerformed cancelled message");
							return 0;
						}
					}

					$success = $this->getQueueTransport()->send($message);
				} else {
					$success = false;
				}
			} else {
				$this->getLogger()->logInfo("[DelegatingTransport] Send success");

				// Save a logged copy too
				$queue_proc = $this->getQueueTransport()->getQueueProcessor();
				if (!$is_retrying && $queue_proc instanceof DatabaseQueueProcessor) {
					$this->getLogger()->logInfo("[DelegatingTransport] Saving logged message");
					$queue_proc->addLoggedMessage($message);
				}
			}
		}

		if (isset($evt) && $evt) {
			$evt->setResult($success ? \Swift_Events_SendEvent::RESULT_SUCCESS : \Swift_Events_SendEvent::RESULT_FAILED);
			$this->event_dispatcher->dispatchEvent($evt, 'sendPerformed');
		}

		$this->getLogger()->logDebug(sprintf("[DelegatingTransport] DONE in %.4f seconds", microtime(true)-$time_top));

		return $success;
	}


	/**
	 * Given a message, inspect the 'From' address to see which transport we sholud use to send it.
	 *
	 * @param \Swift_Mime_Message $message
	 * @return \Swift_MailTransport
	 */
	public function getTransportForMessage(\Swift_Mime_Message $message)
	{
		$from_address_model = $message->getFrom();
		$from_address = array_keys($from_address_model);
		$from_name = array_values($from_address_model);

		if (!$from_address) $from_address = '';
		else $from_address = $from_address[0];

		if (!$from_name) $from_name = '';
		else $from_name = $from_name[0];

		if ($message instanceof \Application\DeskPRO\Mail\Message) {
			$this->getLogger()->logDebug(sprintf("[DelegatingTransport] Message context: %s", $message->getContextId()));
		}

		$matcher = $this->getGatewayAddressMatcher();
		$address = $matcher->getMatchingAddress($from_address);

		// See if it matches a gateway account which can be linked to transport
		if ($gateway_address = $matcher->getMatchingAddress($from_address)) {
			$gateway = $gateway_address->gateway;
			if ($gateway && $gateway->linked_transport) {
				$this->getLogger()->logDebug("[DelegatingTransport] Matched gateway account {$gateway->id} with linked transport {$gateway->linked_transport->id}");

				$new_address = $gateway->getPrimaryEmailAddress();
				if ($gateway->getAliasEmailAddress()) {
					$new_address = $gateway->getAliasEmailAddress();
				}
				$from = array($new_address => $from_name);
				$message->setFrom($from);

				$this->getLogger()->logDebug("[DelegatingTransport] From set to $new_address");

				return $gateway->linked_transport->getTransport();
			}
		}

		if (!App::getSetting('core.allow_arbitrary_gateway_address') && $message instanceof \Application\DeskPRO\Mail\Message && $message->getContextId() == 'ticket_gateway') {

			$this->getLogger()->logDebug(sprintf("[DelegatingTransport] ticket_gateway context, checking gateway address for %s", $from_address));

			if ($address) {

				$this->getLogger()->logDebug(sprintf("[DelegatingTransport] Gateway address found, confirming transport"));

				// We have an address, but that address might not have a transport. So we do this here
				// to decide if we need to revert back to a default gateway address which is figured out next
				$tr = $this->getTransportForFromAddress($from_address, true);
				if ($tr) {
					$this->getLogger()->logDebug(sprintf("[DelegatingTransport] Got transport"));
					return $tr;
				} else {
					$this->getLogger()->logDebug(sprintf("[DelegatingTransport] Gateway address valid, but has no transport"));
					$address = false;
				}
			}

			// If theres no address match, then we need to choose one
			if (!$address) {
				$this->getLogger()->logDebug(sprintf("[DelegatingTransport] Gateway address invalid. Choosing default."));
				$new_address = $matcher->getDefaultTicketAccountFrom();
				if ($new_address) {
					$from = array($new_address => $from_name);
					$message->setFrom($from);

					$from_address = $new_address;

					$this->getLogger()->logDebug("[DelegatingTransport] From set to $new_address");
				}
			}
		}

		$this->getLogger()->logDebug("[DelegatingTransport] From address is $from_address");

		return $this->getTransportForFromAddress($from_address);
	}


	/**
	 * @param string $from_address
	 * @return null|\Swift_MailTransport
	 */
	public function getTransportForFromAddress($from_address, $no_default = false)
	{
		$this->getLogger()->logDebug(sprintf("[DelegatingTransport] getTransportForMessage finding address: %s", $from_address));

		$from_account = App::getEntityRepository('DeskPRO:EmailTransport')->findTransportForAddress($from_address);
		if ($from_account) {

			$this->getLogger()->logDebug(sprintf("[DelegatingTransport] getTransportForMessage found transport %s", $from_account->getId()));

			$tr = $from_account->getTransport();
		} else {
			$this->getLogger()->logDebug(sprintf("[DelegatingTransport] getTransportForMessage NO ACCOUNT FOUND"));

			if ($no_default) {
				return null;
			}

			$email_trans = App::getEntityRepository('DeskPRO:EmailTransport')->getDefaultTransport();
			if ($email_trans) {
				$tr = $email_trans->getTransport();
			} else {
				$tr = new \Application\DeskPRO\Entity\EmailTransport();
				$tr->match_type = 'all';
				$tr->title = '';
				$tr->transport_type = 'mail';

				$e = new \RuntimeException("No default transport found");
				\DeskPRO\Kernel\KernelErrorHandler::logException($e);

				$tr = $tr->getTransport();
			}
		}

		if ($tr) {
			$this->attachLoggerOnce($tr);
		}

		return $tr;
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\AddressMatcher
	 */
	public function getGatewayAddressMatcher()
	{
		if ($this->gateway_address_matcher) {
			return $this->gateway_address_matcher;
		}

		$this->gateway_address_matcher = new \Application\DeskPRO\EmailGateway\AddressMatcher(App::getContainer()->getEm());

		return $this->gateway_address_matcher;
	}


	public function registerPlugin(\Swift_Events_EventListener $plugin)
	{
		$this->event_dispatcher->bindEventListener($plugin);
	}

	public function isStarted()
	{
		return true;
	}

	public function start()
	{

	}

	public function stop()
	{

	}

	/**
	 * Get the logger
	 *
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		if (!$this->logger) {
			$logger = new Logger();
			$this->setLogger($logger);
		}

		return $this->logger;
	}


	/**
	 * Set the logger used. NOTE: An array writer is added automatically,
	 * and the array writer is used to log mail info to failed messages.
	 *
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
		$logger->addWriter($this->message_log_writer);
	}


	/**
	 * Get the log lines for the last attempt.
	 *
	 * @return string[]
	 */
	public function getLastLogLines()
	{
		return $this->message_log_writer->getMessages();
	}


	/**
	 * Attaches the logger plugin once
	 *
	 * @param \Swift_Transport $tr
	 */
	protected function attachLoggerOnce(\Swift_Transport $tr)
	{
		static $done_trs = array();

		$hash = spl_object_hash($tr);
		if (!isset($done_trs[$hash])) {
			$tr->registerPlugin(new \Swift_Plugins_LoggerPlugin(new \Application\DeskPRO\Mail\Loggers\OrbLogger($this->getLogger())));
		}

		$done_trs[$hash] = true;
	}
}
