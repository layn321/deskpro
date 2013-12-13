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

namespace Orb\Mail;

use \Orb\Mail\QueueProcessor\QueueProcessorInterface;
use \Orb\Mail\Message;

use \Orb\Util\Strings;
use \Orb\Util\Util;

/**
 * Queue mail transport
 */
class QueueTransport extends \Orb\Mail\Transport\QueueTransport
{
	/**
	 * @param QueueProcessorInterface $queue_processor
	 */
	public function __construct(QueueProcessorInterface $queue_processor)
	{
		static $done_reg = false;
		if (!$done_reg) {
			$done_reg = true;
			Swift_DependencyContainer::getInstance()->register('transport.queue')
  				->asNewInstanceOf('Orb\\Mail\\Transport\\QueueTransport')
  				->withDependencies(array('transport.eventdispatcher'));
		}

		$arguments = \Swift_DependencyContainer::getInstance()->createDependenciesFor('transport.queue');
		array_unshift($arguments, $queue_processor);

		call_user_func_array(array($this, 'Orb\\Mail\\Transport\\QueueTransport::__construct'), $arguments);
	}

	/**
	 * @param QueueProcessorInterface $queue_processor
	 * @return QueueTransport
	 */
	public static function newInstance(QueueProcessorInterface $queue_processor)
	{
		return new self($queue_processor);
	}
}
