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

namespace Orb\Log\Writer;
use \Orb\Log\LogItem;

/**
 * This writer just saves messages to an array
 */
class ArrayWriter extends AbstractWriter
{
	protected $messages = array();
	protected $max_size = 10000;
	protected $max_line_length = 10000;

	public function setMaxMessageLength($max_line_length = 10000)
	{
		$this->max_line_length = $max_line_length;
	}

	public function setMaxSize($max_size)
	{
		$this->max_size = $max_size;
	}

	public function getMessages()
	{
		return $this->messages;
	}

	public function getMessagesAsString()
	{
		return implode("\n", $this->getMessages());
	}

	public function _write(LogItem $log_item)
	{
		$msg = trim($log_item[LogItem::MESSAGE_LINE]);

		if (strlen($msg) > $this->max_line_length) {
			$msg = substr($msg, 0, $this->max_line_length);
		}

		$this->messages[] = $msg;

		while(count($this->messages) > $this->max_size) {
			array_shift($this->messages);
		}
	}

	public function clear()
	{
		$this->messages = array();
	}
}
