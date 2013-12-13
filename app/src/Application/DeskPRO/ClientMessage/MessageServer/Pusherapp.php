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
 * @category Controller
 */

namespace Application\DeskPRO\ClientMessage\MessageServer;

use Symfony\Component\EventDispatcher\Event;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;

/**
 * A message server is something that listenes on the ClientMessages event
 * to handle dispatching messages through various protocols.
 */
class Pusherapp extends AbstractMessageServer
{
	protected $pusher;

	protected function init(array $options)
	{
		$this->pusher = new \Pusher(
			$options['api_key'],
			$options['secret'],
			$options['app_id']
		);
	}



	/**
	 * Handle a new message
	 *
	 * @param ClientMessage $message
	 * @return mixed
	 */
	function handleNewMessage(Entity\ClientMessage $message)
	{
		// We use dots, pusherapp doesnt allow them so lets use dahs
		$full_name = str_replace('.', '-', $message['channel']);

		list ($channel, $event_name) = Strings::rexplode('-', $full_name, 2);

		$this->pusher->trigger($channel, $event_name, $message['data']);
	}
}
