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
 * This writer just echos out the messages
 */
class Output extends AbstractWriter
{
	protected $html = false;
	
	/**
	 * @param  streamOrUrl     Stream or URL to open as a stream
	 * @param  mode            Mode, only applicable if a URL is given
	 */
	public function __construct($html = false)
	{
		$this->html = $html;

		$this->addFilter(new \Orb\Log\Filter\SimpleLineFormatter());
	}

	/**
	 * Write a message to the log.
	 */
	public function _write(LogItem $log_item)
	{
		$msg = $log_item[LogItem::MESSAGE_LINE];
		if ($this->html) {
			$msg = '<pre style="margin:0;padding:0;">' . htmlspecialchars(trim($msg)) . '</pre>';
		} else {
			$msg .= "\n";
		}

		echo $msg;
	}
}
