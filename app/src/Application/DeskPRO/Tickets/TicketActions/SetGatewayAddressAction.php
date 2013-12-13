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

namespace Application\DeskPRO\Tickets\TicketActions;

use Application\DeskPRO\App;
use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\Entity\Ticket;

/**
 * A hidden action used with the SetFromAddress modifier that actual modified the ticket email address
 */
class SetGatewayAddressAction extends AbstractAction
{
	/**
	 * @var int
	 */
	protected $gateway_address_id;

	/**
	 * @var \Application\DeskPRO\Entity\EmailGatewayAddress
	 */
	protected $gateway_address;

	public function __construct($gateway_address_id)
	{
		$this->gateway_address_id = $gateway_address_id;
		if ($gateway_address_id && $this->gateway_address_id != 'department') {
			$this->gateway_address = App::getOrm()->find('DeskPRO:EmailGatewayAddress', $gateway_address_id);
		}
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$gateway_address = $this->gateway_address;

		if ($this->gateway_address_id == 'department') {
			if ($ticket->department && $ticket->department->email_gateway && $ticket->department->email_gateway->getPrimaryEmailAddress(true)) {
				$gateway_address = $ticket->department->email_gateway->getPrimaryEmailAddress(true);
			}
		}

		if (!$gateway_address) {
			return;
		}

		$ticket->notify_email = '';
		$ticket->email_gateway_address = $gateway_address;
		$ticket->email_gateway = $gateway_address->gateway;
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		return array();
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		return $other_action;
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		if ($this->gateway_address_id == 'department') {
			return 'Set the email account that matches the department';
		}

		if (!$this->gateway_address) {
			return "<error>Unknown #{$this->gateway_address}</error>";
		}
		return 'Set gateway address to ' . $this->gateway_address->match_pattern;
	}

	/**
	 * @return bool
	 */
	public function doPrepend()
	{
		return true;
	}
}
