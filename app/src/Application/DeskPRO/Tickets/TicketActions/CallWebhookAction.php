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
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;
use DeskPRO\Kernel\KernelErrorHandler;

class CallWebHookAction extends AbstractAction
{
	protected $webhook_id;

	public function __construct($webhook_id)
	{
		$this->webhook_id = $webhook_id;
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		$hook = App::getEntityRepository('DeskPRO:WebHook')->find($this->webhook_id);
		if (!$hook) {
			return;
		}

		$data = array();
		$data['ticket'] = $ticket->toApiData();
		unset($data['ticket']['person'], $data['ticket']['organization']);
		if ($ticket->person) {
			$data['person'] = $ticket->person->toApiData();
		}
		if ($ticket->organization) {
			$data['organization'] = $ticket->organization->toApiData();
		}

		try {
			$hook->trigger($data);
		} catch (\Exception $e) {
			KernelErrorHandler::logException($e, false);
		}
	}


	/**
	 * Get an array of actions that would be performed on the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function getApplyActions(Ticket $ticket)
	{
		return array(
			array('action' => 'call_webhook', 'webhook_id' => $this->webhook_id)
		);
	}


	/**
	 * Get the web hook id
	 *
	 * @return int
	 */
	public function getWebHookId()
	{
		return $this->webhook_id;
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
		$tr = App::getTranslator();

		if ($this->webhook_id == 0) {
			return '';
		} else {
			$titles = App::getEntityRepository('DeskPRO:WebHook')->getHookTitles();

			$name = isset($titles[$this->webhook_id]) ? $titles[$this->webhook_id] : null;
			if ($name !== null && $as_html) {
				$name = htmlspecialchars($name);
			}
			if ($name === null) $name = "<error>Unknown #{$this->agent_id}</error>";

			return $tr->phrase('agent.tickets.call_webhook_action', array('hook' => $name));
		}
	}
}
