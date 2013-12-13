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

class Build1358499996 extends AbstractBuild
{
	public function run()
	{
		$this->out("Populate tickets.count data");

		$agent_ids = $this->container->getDb()->fetchAllCol("
			SELECT id FROM people
			WHERE is_agent = 1
		");
		$agent_ids_in = implode(',', $agent_ids);

		$this->out('Populating tickets.count_agent_replies');
		$t = microtime(true);
		$x = $this->container->getDb()->executeUpdate("
			UPDATE tickets
			LEFT JOIN (
				SELECT COUNT(*) AS count, ticket_id
				FROM tickets_messages
				WHERE person_id IN ($agent_ids_in) AND is_agent_note = 0
				GROUP BY ticket_id
			) AS t ON tickets.id = t.ticket_id
			SET tickets.count_agent_replies = COALESCE(t.count, 0);
		");
		$this->out(sprintf("-- Updated $x rows in %.4f s", microtime(true) - $t));

		$this->out('Populating tickets.count_user_replies');
		$t = microtime(true);
		$x = $this->container->getDb()->executeUpdate("
			UPDATE tickets
			LEFT JOIN (
				SELECT COUNT(*) AS count, ticket_id
				FROM tickets_messages
				WHERE person_id NOT IN ($agent_ids_in)
				GROUP BY ticket_id
			) AS t ON tickets.id = t.ticket_id
			SET tickets.count_user_replies = COALESCE(t.count, 0);
		");
		$this->out(sprintf("-- Updated $x rows in %.4f s", microtime(true) - $t));
	}
}