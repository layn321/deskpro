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
use Application\DeskPRO\Mail\Transport\DelegatingTransport;
use Application\DeskPRO\Entity\Ticket;

/**
 * When an agent enters vacation mode or is deleted, we have to re-assign their awaiting_agent tickets
 * to unassigned. This does them in batches.
 *
 * We cant just do it in mysql because we need the proper logs to be generated.
 */
class AgentModeTicketReassign extends AbstractJob
{
	const DEFAULT_INTERVAL = 300;

	public function run()
	{
		$max = 1000;

		#------------------------------
		# Deleted
		#------------------------------

		$agent_ids = App::getDb()->fetchAllCol("SELECT id FROM people WHERE is_agent = 1 AND is_deleted = 1");
		$agent_ids_c = implode(',', $agent_ids);

		if ($max && $agent_ids) {

			$ticket_ids = App::getDb()->fetchAllCol("
				SELECT id
				FROM tickets
				WHERE status IN ('awaiting_agent', 'awaiting_user') AND agent_id IN ($agent_ids_c)
			");

			foreach ($ticket_ids as $t) {
				App::getDb()->update(
					'tickets',
					array('agent_id' => null),
					array('id' => $t)
				);
				App::getDb()->insert('tickets_logs', array(
					'ticket_id'    => $t,
					'action_type'  => 'free',
					'details'      => serialize(array('message' => 'Unassigning deactivated agent')),
					'date_created' => date('Y-m-d H:i:s')
				));
			}
		}
	}
}
