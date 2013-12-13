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

use Application\DeskPRO\BlobStorage\MoveBlobsUtil;
use Application\DeskPRO\App;

/**
 * Goes through blobs that need to be moved from one storage mechanism to another
 */
class MoveBlobs extends AbstractJob
{
	const DEFAULT_INTERVAL = 60;

	public function run()
	{
		$mover = new MoveBlobsUtil(App::getOrm(), App::getContainer()->getBlobStorage());
		$mover->setLogger($this->getLogger());
		$mover->setIgnoreErrors();
		$mover->setLimit(10);
		$mover->setLimitTime(60);

		$count = $mover->getCount();
		if (!$count) {
			App::getOrm()->getRepository('DeskPRO:Setting')->updateSetting('core.filesystem_move_from_id', null);

			// Nothing to do
			return;
		}

		$this->logStatus("$count blobs moved");

		$mover->run();

		$next_id = App::getDb()->fetchColumn("SELECT id FROM blobs WHERE storage_loc_pref IS NOT NULL ORDER BY id ASC LIMIT 1");
		if ($next_id) {
			App::getOrm()->getRepository('DeskPRO:Setting')->updateSetting('core.filesystem_move_from_id', $next_id);
		} else {
			App::getOrm()->getRepository('DeskPRO:Setting')->updateSetting('core.filesystem_move_from_id', null);
		}
	}
}
