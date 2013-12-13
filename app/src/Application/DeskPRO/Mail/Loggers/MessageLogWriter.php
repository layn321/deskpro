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
 * @subpackage Log
 */

namespace Application\DeskPRO\Mail\Loggers;
use \Orb\Log\LogItem;

class MessageLogWriter extends \Orb\Log\Writer\AbstractWriter
{
	/**
	 * @var int
	 */
	protected $max_line_length = 10000;

	/**
	 * @var \Application\DeskPRO\Mail\Message
	 */
	protected $on_message = null;

	public function setMaxMessageLength($max_line_length = 5000)
	{
		$this->max_line_length = $max_line_length;
	}

	public function clearCurrentMessage()
	{
		$this->on_message = null;
	}

	public function setCurrentMessageIfValid($on_message)
	{
		$this->clearCurrentMessage();

		if ($on_message instanceof \Application\DeskPRO\Mail\Message) {
			$this->setCurrentMessage($on_message);
		}
	}

	public function setCurrentMessage(\Application\DeskPRO\Mail\Message $on_message)
	{
		$this->on_message = $on_message;
	}

	public function _write(LogItem $log_item)
	{
		if (!$this->on_message) {
			return;
		}

		$msg = trim($log_item[LogItem::MESSAGE_LINE]);

		if (strlen($msg) > $this->max_line_length) {
			$msg = substr($msg, 0, $this->max_line_length);
		}

		$this->on_message->addLogMessage($msg);
	}
}
