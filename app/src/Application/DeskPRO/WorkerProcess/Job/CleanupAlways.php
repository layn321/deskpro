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

use Application\DeskPRO\App;

class CleanupAlways extends AbstractJob
{
	const DEFAULT_INTERVAL = 1;

	public function run()
	{
		#------------------------------
		# cleanup chat pings
		#------------------------------

		$cutoff = time() - 180;

		App::getDb()->executeUpdate("
			DELETE FROM chat_conversation_pings
			WHERE ping_time < $cutoff
		");

		#------------------------------
		# client_messages
		#------------------------------

		// client messages are nearly instant, so this timesnip is very low
		$datetime = date('Y-m-d H:i:s', time() - 120);

		// Long-lived channels are still only deleted after 3 days
		$datetime2 = date('Y-m-d H:i:s', time() - 259200);

		$long_lived_channels = array(
			'agent_chat.new-message'
		);

		$long_lived_channels = "'" . implode("','", $long_lived_channels) . "'";

		$ids = App::getDb()->fetchAllCol("
			SELECT id FROM client_messages
			WHERE (
				date_created < ? AND channel NOT IN ($long_lived_channels)
			) OR (
				date_created < ? AND channel IN ($long_lived_channels)
			)
		", array($datetime, $datetime2));
		if ($ids) {
			$num = App::getDb()->executeUpdate("
				DELETE FROM client_messages
				WHERE id IN (" . implode(',', $ids) . ")
			");

			if ($num) {
				$this->logStatus("Cleaned up $num old client messages");
			}
		}

		#------------------------------
		# Try to delete old update status file
		#------------------------------

		if (file_exists(DP_WEB_ROOT.'/auto-update-status.php') && App::getSetting('core.last_auto_upgrade_time') < time()-180) {
			@unlink(DP_WEB_ROOT.'/auto-update-status.php');
		}
	}
}
