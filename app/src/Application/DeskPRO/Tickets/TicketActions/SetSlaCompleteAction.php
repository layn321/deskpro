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

use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\App;

use Orb\Util\Arrays;

/**
 * Set SLA complete
 */
class SetSlaCompleteAction extends AbstractAction
{
	protected $actions = array();

	public function __construct($sla_complete, $sla_id)
	{
		$this->actions = array($sla_complete => array($sla_id));
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		foreach ($this->actions AS $complete => $sla_ids) {
			if (in_array('0', $sla_ids)) {
				// take action for all
				$sla_ids = array('0');
			}
			foreach ($sla_ids AS $sla_id) {
				if ($sla_id) {
					$sla = App::getEntityRepository('DeskPRO:Sla')->find($sla_id);
					if (!$sla) {
						continue;
					}

					$ticket_sla = $ticket->hasSla($sla);
					if (!$ticket_sla) {
						continue;
					}

					$ticket_slas = array($ticket_sla);
				} else {
					$ticket_slas = $ticket->ticket_slas;
				}

				foreach ($ticket_slas AS $ticket_sla) {
					$ticket_sla->setIsCompletedSet($complete);
				}
			}
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
			array('action' => 'set_sla_complete', 'actions' => $this->actions)
		);
	}


	/**
	 * @return array
	 */
	public function getSlaActions()
	{
		return $this->actions;
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		$actions = $other_action->getSlaActions();
		foreach ($actions AS $complete => $sla_ids) {
			if (isset($this->actions[$complete])) {
				$this->actions[$complete] = array_merge($this->actions[$complete], $sla_ids);
				$this->actions[$complete] = array_unique($this->actions[$complete]);
			} else {
				$this->actions[$complete] = $sla_ids;
			}
		}

		return $this;
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		$parts = array();
		foreach ($this->actions AS $complete => $sla_ids) {
			if (in_array('0', $sla_ids)) {
				// take action for all
				$titles = null;
			} else {
				$slas = App::getEntityRepository('DeskPRO:Sla')->getByIds($sla_ids);
				$titles = array();
				foreach ($slas as $s) {
					$titles[$s->id] = $as_html ? htmlspecialchars($s->title) : $s->title;
				}

				foreach ($sla_ids as $id) {
					if (!isset($titles[$id])) {
						$titles[$id] = "<error>Unknown #$id</error>";
					}
				}
			}

			if ($complete) {
				if ($titles !== null) {
					$parts[] = 'Set SLA requirements to complete for SLA ' . ($titles ? implode($titles, ', ') : '[unknown]');
				} else {
					$parts[] = 'Set SLA requirements to complete';
				}
			} else {
				if ($titles !== null) {
					$parts[] = 'Set SLA requirements to incomplete for SLA ' . ($titles ? implode($titles, ', ') : '[unknown]');
				} else {
					$parts[] = 'Set SLA requirements to incomplete';
				}
			}
		}

		return implode('; ', $parts);
	}
}
