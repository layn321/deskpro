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
 * A queue processor is something that knows how to enqueue a message
 * and later process it.
 */
interface QueueProcessorInterface
{
	const PROCESS_SUCCESS = 1;
	const PROCESS_FAILURE = 2;
	const PROCESS_STOP = 4;

	/**
	 * Start the queue system
	 */
	public function startQueue();

	/**
	 * Shutdown the queue system
	 */
	public function shutdownQueue();

	/**
	 * Add a message to the queue
	 *
	 * @param Orb\Mail\Message $message
	 */
	public function addQueuedMessage(\Orb\Mail\Message $message);

	/**
	 * Process each member in a queue with the given callback
	 *
	 * @param  $callback
	 * @return void
	 */
	public function processQueue($callback);
}
