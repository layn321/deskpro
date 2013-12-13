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

use Application\DeskPRO\ClientMessage\Event;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;


/**
 * A message server is something that listenes on the ClientMessages event
 * to handle dispatching messages through various protocols.
 */
abstract class AbstractMessageServer
{
	public function __construct(array $options)
	{
		$event_dispatcher = App::get('event_dispatcher');
		$event_dispatcher->addListener('DeskPRO_onNewClientMessage', $this);

		$this->init($options);
	}

	protected function init(array $options)
	{
		// hook for children
	}



	public function DeskPRO_onNewClientMessage(Event $event)
	{
		$this->handleNewMessage($event->getClientMessage());
	}



	/**
	 * Handle a new message
	 *
	 * @param ClientMessage $message
	 * @return mixed
	 */
	abstract function handleNewMessage(Entity\ClientMessage $message);
}
