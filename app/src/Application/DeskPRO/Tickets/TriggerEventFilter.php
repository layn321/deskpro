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
 * @subpackage Tickets 
 */

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketTrigger;
use Application\DeskPRO\Tickets\TicketChangeTracker;

class TriggerEventFilter
{
	/**
	 * @var \Application\DeskPRO\Entity\TicketTrigger[]
	 */
	protected $triggers;
	
	public function __construct($triggers)
	{
		$this->triggers = $triggers;
	}

	/**
	 * Filter all the triggers through a trackr to get a final array of actions
	 * that sholud be executed.
	 *
	 * @param array               $event_types  An array of event types
	 * @param TicketChangeTracker $tracker
	 * @return array
	 */
	public function getActions(array $event_types, TicketChangeTracker $tracker)
	{
		$ticket = $tracker->getTicket();

		#------------------------------
		# Fetch all triggers that apply to this type
		#------------------------------

		$event_triggers = array();

		foreach ($this->triggers as $trigger) {
			if (in_array($tracker['event_trigger'], $event_types)) {
				$event_triggers[] = $tracker;
			}
		}

		#------------------------------
		# Now go through and fetch all that actually match the changes
		#------------------------------

		$actions = array();
	}
}
