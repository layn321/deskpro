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
 * @subpackage WorkerProcess
 */

namespace Application\DeskPRO\WorkerProcess\Job;

use Application\DeskPRO\Mail\QueueProcessor\Database as DatabaseQueueProcessor;

use Application\DeskPRO\App;
use Application\DeskPRO\Log\Logger;
use Application\DeskPRO\Entity\TicketTrigger;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Tickets\TicketActions\ActionsCollection;

/**
 * Handles SLA warn/fail updates
 */
class TicketSlas extends AbstractJob
{
	const DEFAULT_INTERVAL = 60;

	public function run()
	{
		$GLOBALS['DP_ESCALATION_RUNNING'] = true;

		$em = App::getOrm();

		$count_failed = 0;
		$count_warning = 0;

		$ticket_slas = App::getEntityRepository('DeskPRO:TicketSla')->getTicketSlasPastThreshold('fail');
		foreach ($ticket_slas as $ticket_sla) {
			$ticket_sla->evaluateSlaDates();
			$em->persist($ticket_sla);
			$em->flush();

			if ($ticket_sla->sla_status == \Application\DeskPRO\Entity\TicketSla::STATUS_FAIL) {
				$count_failed++;
			}
		}

		App::getOrm()->clear('Application\\DeskPRO\\Entity\\Ticket');
		App::getOrm()->clear('Application\\DeskPRO\\Entity\\TicketSla');

		$ticket_slas = App::getEntityRepository('DeskPRO:TicketSla')->getTicketSlasPastThreshold('warning');
		foreach ($ticket_slas as $ticket_sla) {
			$ticket_sla->evaluateSlaDates();
			$em->persist($ticket_sla);
			$em->flush();

			if ($ticket_sla->sla_status == \Application\DeskPRO\Entity\TicketSla::STATUS_WARNING) {
				$count_warning++;
			}
		}

		App::getOrm()->clear('Application\\DeskPRO\\Entity\\Ticket');
		App::getOrm()->clear('Application\\DeskPRO\\Entity\\TicketSla');

		if ($count_warning || $count_failed) {
			$this->getLogger()->logInfo("SLA statuses updated. Failed: $count_failed, warning: $count_warning");
		}

		unset($GLOBALS['DP_ESCALATION_RUNNING']);
	}
}