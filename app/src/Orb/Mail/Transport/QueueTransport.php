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

namespace Orb\Mail\Transport;

use Application\DeskPRO\App;
use \Orb\Mail\Message;

use \Orb\Util\Strings;
use \Orb\Util\Util;

/**
 * Queue mail transport
 */
class QueueTransport implements \Swift_Transport
{
	protected $_queue_processor;
	protected $_event_dispatcher;

	public function __construct(\Swift_Events_EventDispatcher $event_dispatcher)
	{
		$this->_event_dispatcher = $event_dispatcher;
	}

	public function getQueueProcessor()
	{
		return $this->_queue_processor;
	}

	public function isStarted()
	{
		return true;
	}

	public function start()
	{
		$this->_queue_processor->startQueue();
	}

	public function stop()
	{
		$this->_queue_processor->shutdownQueue();
	}

	public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
	{
		if ($evt = $this->_event_dispatcher->createSendEvent($this, $message)) {
			$this->_event_dispatcher->dispatchEvent($evt, 'beforeSendPerformed');
			if ($evt->bubbleCancelled()) {
				return 0;
			}
		}

		$log = '';
		if ($message instanceof \Application\DeskPRO\Mail\Message) {
			$log = $message->getLogMessages();
			$message->clearLogMessages();
		}

		$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromString(serialize($message), 'sendmail.obj', 'plain/text');
		$sendmail = new \Application\DeskPRO\Entity\SendmailQueue();
		$sendmail->blob = $blob;
		$sendmail->subject = $message->getSubject() ?: '';
		$sendmail->date_next_attempt = new \DateTime();
		$sendmail->priority = 10;

		if ($message instanceof \Orb\Mail\Message) {
			$sendmail->priority = $message->getQueuePriority();
		}

		$tos = array();
		foreach ($message->getTo() as $addr => $name) {
			$tos[] = $addr;
		}
		$sendmail->to_address = implode(',', $tos);
		foreach ($message->getFrom() as $addr => $name) {
			$sendmail->from_address = $addr;
		}

		if ($log) {
			$sendmail->appendLog($log);
		}

		App::getOrm()->persist($sendmail);
		App::getOrm()->flush();

		if ($evt) {
			$evt->setResult(\Swift_Events_SendEvent::RESULT_SUCCESS);
			$this->_event_dispatcher->dispatchEvent($evt, 'sendPerformed');
		}

		return 1;
	}

	public function registerPlugin(\Swift_Events_EventListener $plugin)
	{
		$this->_event_dispatcher->bindEventListener($plugin);
	}
}
