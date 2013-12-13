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
 * Remove SLA
 */
class RemoveSlaAction extends AbstractAction
{
	protected $sla_ids = array();
	protected $remove_all = false;

	public function __construct($sla_id)
	{
		if (!$sla_id) {
			$this->remove_all = true;
		} else {
			$this->sla_ids = (array)$sla_id;
		}
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		if ($this->remove_all) {
			$ticket->removeAllSlas();
		} else {
			foreach ($this->sla_ids AS $sla_id) {
				$sla = App::getEntityRepository('DeskPRO:Sla')->find($sla_id);
				if ($sla) {
					$ticket->removeSla($sla);
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
			array('action' => 'remove_sla', 'sla_ids' => $this->sla_ids, 'remove_all' => $this->remove_all)
		);
	}


	/**
	 * @return array
	 */
	public function getSlaIds()
	{
		return $this->sla_ids;
	}

	/**
	 * @return bool
	 */
	public function getRemoveAll()
	{
		return $this->remove_all;
	}


	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		if ($other_action->getRemoveAll()) {
			$this->remove_all = true;
		} else {
			$this->sla_ids = array_merge($this->sla_ids, $other_action->getSlaIds());
			$this->sla_ids = array_unique($this->sla_ids);
		}
		return $this;
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
        $tr = App::getTranslator();
		if ($this->remove_all) {
			return $tr->phrase('agent.tickets.remove_all_slas_action');
		} else {
			$slas = App::getEntityRepository('DeskPRO:Sla')->getByIds($this->sla_ids);
			$titles = array();
			foreach ($slas AS $sla) {
				$titles[$sla->id] = $as_html ? htmlspecialchars($sla->title) : $sla->title;
			}

			foreach ($this->sla_ids as $id) {
				if (!isset($titles[$id])) {
					$titles[$id] = "<error>Unknown #$id</error>";
				}
			}

			return $tr->phrase('agent.tickets.remove_sla_action', array('sla' => $titles ? implode(', ', $titles) : '[unknown]'));
		}
	}

	public function doPrepend()
	{
		return true;
	}
}
