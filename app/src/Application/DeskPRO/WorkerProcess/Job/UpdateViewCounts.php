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
use Application\DeskPRO\Entity\PageViewLog;

/**
 * Updates viewcounts on articles
 */
class UpdateViewCounts extends AbstractJob
{
	const DEFAULT_INTERVAL = 600; // 10 minutes

	public function run()
	{
		// VIEW_COUNTER
		return;
		$time = time();
		$last_time = App::getSetting('core.last_viewcount_update');
		if (!$last_time) {
			$last_time = time() - 600;
		}

		$update_objects = App::getDb()->fetchAll("
			SELECT object_type, object_id, COUNT(*) AS count
			FROM page_view_log
			WHERE date_created > ?
			GROUP BY object_type, object_id
		", array(date('Y-m-d H:i:s', $last_time)));

		App::getDb()->beginTransaction();
		try {
			foreach ($update_objects as $obj) {
				switch ($obj['object_type']) {
					case PageViewLog::TYPE_ARTICLE:  $table = 'articles';  break;
					case PageViewLog::TYPE_DOWNLOAD: $table = 'downloads'; break;
					case PageViewLog::TYPE_FEEDBACK: $table = 'feedback';  break;
					case PageViewLog::TYPE_NEWS:     $table = 'news';      break;
					default: $table = null;
				}

				if (!$table) {
					continue;
				}

				App::getDb()->executeUpdate("
					UPDATE $table
					SET view_count = view_count + ?
					WHERE id = ?
				", array($obj['count'], $obj['object_id']));
			}

			App::getDb()->commit();
		} catch (\Exception $e) {
			App::getDb()->rollback();
			throw $e;
		}

		if ($update_objects) {
			$this->logStatus("Updated " . count($update_objects) . " view counts");
		}

		App::get('deskpro.core.settings')->setSetting('core.last_viewcount_update', $time);
	}
}