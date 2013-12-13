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
 * Handles time-based triggers
 */
class TicketTriggers extends AbstractJob
{
	const DEFAULT_INTERVAL = 60;

	protected $count_success;
	protected $count_failed;

	public function run()
	{
		$escalations = App::getEntityRepository('DeskPRO:TicketTrigger')->getTimeTriggers();

		if (!count($escalations)) {
			return;
		}

		$GLOBALS['DP_ESCALATION_RUNNING'] = true;
		foreach ($escalations as $esc) {
			$this->runEscalation($esc);
		}
		unset($GLOBALS['DP_ESCALATION_RUNNING']);
	}

	protected function runEscalation(TicketTrigger $trigger)
	{
		$searcher = $trigger->getSearcher();
		$searcher->addTerm('escalation_eliminator', 'is', $trigger);

		// Add this limit for imported installs
		if (App::getSetting('core.imported_timestamp')) {
			$instal_timestamp = App::getSetting('core.install_timestamp') ?: time();
			$install_date = new \DateTime('@' . $instal_timestamp);
			$install_date = $install_date->format('Y-m-d H:i:s');

			$searcher->addRawWhere("tickets.date_created >= '$install_date'");
		}

		$ticket_ids = $searcher->getMatches(array('offset' => 0, 'limit' => 100));

		$this->logger->log("Trigger {$trigger->id}: Found " . count($ticket_ids) . " matching", 'INFO');

		if (!$ticket_ids) {
			return;
		}

		$tickets = App::getOrm()->getRepository('DeskPRO:Ticket')->getByIds($ticket_ids);

		foreach ($tickets as $ticket) {
			$tracker = $ticket->getTicketLogger();

			$field = $trigger->getTicketTimeField();
			if (!$field) {
				continue;
			}

			$d = !empty($ticket[$field]) ? $ticket[$field] : null;
			if (!$d) {
				continue;
			}

			// Always insert log first so same trigger isnt applied over and over
			// in worst-case of an error
			$trigger_log = array(
				'ticket_id'     => $ticket->id,
				'trigger_id'    => $trigger->id,
				'date_ran'      => date('Y-m-d H:i:s'),
				'date_criteria' => $d->format('Y-m-d H:i:s')
			);
			App::getDb()->insert('ticket_trigger_logs', $trigger_log);

			$factory = new \Application\DeskPRO\Tickets\TicketActions\ActionsFactory();
			$factory->addGlobalOption('tracker', $tracker);
			$factory->addGlobalOption('ticket', $ticket);

			$actions_collection = new ActionsCollection();
			foreach ($trigger->actions as $action_info) {
				$action = $factory->createFromInfo($action_info);
				if ($action) {

					if ($action instanceof \Application\DeskPRO\Tickets\TicketActions\ExecutionContextAware) {
						$action->setExecutionContext('trigger');
					}

					$actions_collection->add($action, array('trigger' => $trigger));
					$tracker->recordExtraMulti('trigger', $trigger);
				}
			}

			App::getDb()->beginTransaction();
			try {
				$actions_collection->apply($ticket->getTicketLogger(), $ticket, null);
				App::getOrm()->persist($ticket);
				App::getOrm()->flush();
				App::getDb()->commit();

				// Need to call this explicitly or logs wont be applied when there are only actions
				// that dont directly modify the ticket (e.g., emails with no prop changes)
				// wont fire as part of the usual post-commit hooks
				$ticket->_saveTicketLogs();
			} catch (\Exception $e) {
				App::getDb()->rollback();

				// Log the error but continue with execution
				$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);
			}
		}
	}
}