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
 * @subpackage
 */

namespace Application\InstallBundle\Upgrade\Build;

class Build1363036050 extends AbstractBuild
{
	public function run()
	{
		$this->out("Set missing delete times so cleanup job can process");

		$ticket_ids = $this->container->getDb()->fetchAllCol("
			SELECT tickets.id
			FROM tickets
			LEFT JOIN tickets_deleted ON (tickets_deleted.ticket_id = tickets.id)
			WHERE tickets.status = 'hidden' AND tickets.hidden_status = 'deleted' AND tickets_deleted.ticket_id IS NULL
		");

		$ins = array();
		foreach ($ticket_ids as $tid) {
			$ins[] = array(
				'ticket_id' => $tid,
				'date_created' => '2012-01-01 00:00:00',
				'reason' => '(system marked)'
			);
		}

		if($ins) {
			$this->container->getDb()->batchInsert('tickets_deleted', $ins, true);
		}
	}
}