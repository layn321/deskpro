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

namespace Orb\Mail\QueueProcessor;

use Orb\Util\Strings;
use Orb\Util\Util;

/**
 * Stores messages in the filesystem
 */
class Filesystem implements QueueProcessorInterface
{
	protected $filepath;

	public function __construct($path)
	{
		$path = rtrim($path, '/\\');

		if (!is_dir($path) OR !is_writable($path)) {
			throw new \InvalidArgumentException('$path is not writable');
		}

		$this->filepath = $path;
	}



	/**
	 * Process queues
	 *
	 * @return Message
	 */
	public function processQueue($callback)
	{
		$files = scandir($this->filepath);

		foreach ($files as $file) {

			if (!($message = unserialize(file_get_contents($this->filepath . DIRECTORY_SEPARATOR . $file)))) {
				throw new \RuntimeException('Failed to read or unserialize message');
			}

			$ret = call_user_func($callback, $message);
			if ($ret & self::PROCESS_SUCCESS) {
				if (!unlink($this->filepath . DIRECTORY_SEPARATOR . $file)) {
					throw new \RuntimeException('Failed to remove old message file');
				}
			}

			if ($ret & self::PROCESS_STOP) {
				return;
			}
		}
	}



	/**
	 * Add a message to the queue
	 *
	 * @param Orb\Mail\Message $message
	 */
	public function addQueuedMessage(\Orb\Mail\Message $message)
	{
		$name = time() . '_' . preg_replace('#[^a-zA-Z0-9]#', '-', $message->getSubject()) . '.dat';
		$name = preg_replace('#-{,2}#', '-', $name);

		$path = $this->filepath . DIRECTORY_SEPARATOR . $name;

		if (!file_put_contents($path, serialize($message))) {
			return false;
		}

		return true;
	}



	/**
	 * Start the queue system
	 */
	public function startQueue() {}

	/**
	 * Shutdown the queue system
	 */
	public function shutdownQueue() {}
}
