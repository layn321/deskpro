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

class CleanupHourly extends AbstractJob
{
	const DEFAULT_INTERVAL = 3600;

	public function run()
	{
		#------------------------------
		# drafts
		#------------------------------

		$datetime = date('Y-m-d H:i:s', time() - App::getSetting('core.drafts_lifetime'));
		$num = App::getDb()->executeUpdate("DELETE FROM drafts WHERE date_created < ?", array($datetime));

		if ($num) {
			$this->logStatus("Cleaned up $num drafts");
		}

		$datetime = date('Y-m-d H:i:s', time() - 28800);
		$num = App::getDb()->executeUpdate("DELETE FROM article_comments WHERE status = 'temp' AND date_created < ?", array($datetime));
		if ($num) {
			$this->logStatus("Cleaned up $num temp article comments");
		}

		$num = App::getDb()->executeUpdate("DELETE FROM download_comments WHERE status = 'temp' AND date_created < ?", array($datetime));
		if ($num) {
			$this->logStatus("Cleaned up $num temp download comments");
		}

		$num = App::getDb()->executeUpdate("DELETE FROM feedback_comments WHERE status = 'temp' AND date_created < ?", array($datetime));
		if ($num) {
			$this->logStatus("Cleaned up $num temp feedback comments");
		}

		$num = App::getDb()->executeUpdate("DELETE FROM news_comments WHERE status = 'temp' AND date_created < ?", array($datetime));
		if ($num) {
			$this->logStatus("Cleaned up $num temp news comments");
		}

		#------------------------------
		# sessions
		#------------------------------

		$datetime = date('Y-m-d H:i:s', time() - App::getSetting('core.sessions_lifetime'));
		$num = App::getDb()->executeUpdate("DELETE FROM sessions WHERE date_last < ?", array($datetime));

		if ($num) {
			$this->logStatus("Cleaned up $num stale sessions");
		}

		#------------------------------
		# Old visitors
		#------------------------------

		$datesnip = date('Y-m-d H:i:s', time() - App::getSetting('core.visitor_cleanup_time'));
		$num = App::getDb()->executeUpdate("
			DELETE FROM visitors
			WHERE date_last < ?
		", array($datesnip));

		if ($num) {
			$this->logStatus("Cleaned up $num stale visitors");
		}

		#------------------------------
		# Bogus visitors
		#------------------------------

		$datesnip = date('Y-m-d H:i:s', time() - App::getSetting('core.visitor_cleanup_bogus_time'));
		$num = App::getDb()->executeUpdate("
			DELETE FROM visitors
			WHERE
				date_last < ?
				AND (
					visitors.hint_hidden = 1
					OR visitors.last_track_id IS NULL
				)
		", array($datesnip));

		if ($num) {
			$this->logStatus("Cleaned up $num bogus visitors");
		}

		#------------------------------
		# chat blocks
		#------------------------------

		// Clean up chat blocks
		$num = App::getOrm()->getRepository('DeskPRO:ChatBlock')->cleanupBlocks();

		if ($num) {
			$this->logStatus("Cleaned up $num stale chat blocks");
		}

		#------------------------------
		# temp attachments
		#------------------------------

		$now = date('Y-m-d H:i:s');
		$datetime = date('Y-m-d H:i:s', strtotime('-6 hours'));

		$blob_ids = App::getDb()->fetchAllCol("
			SELECT id
			FROM blobs
			WHERE (is_temp = 1 AND date_created < ?) OR date_cleanup < ?
		", array($datetime, $now));

		$num = 0;
		foreach ($blob_ids as $blob_id) {
			try {
				$blob = App::getOrm()->find('DeskPRO:Blob', $blob_id);
				if ($blob) {
					App::getContainer()->getBlobStorage()->deleteBlobRecord($blob);
				}
			} catch (\Exception $e) {}
			$num++;
		}

		if ($num) {
			$this->logStatus("Cleaned up $num temporary attachments");
		}

		#------------------------------
		# Temp data
		#------------------------------

		$datetime = date('Y-m-d H:i:s', time());

		$num = App::getDb()->executeUpdate("
			DELETE FROM tmp_data
			WHERE date_expire < ?
		", array($datetime));

		if ($num) {
			$this->logStatus("Cleaned up $num stale user temp data entries");
		}

		#------------------------------
		# Prefs
		#------------------------------

		$datetime = date('Y-m-d H:i:s', time());

		$num = App::getDb()->executeUpdate("
			DELETE FROM people_prefs
			WHERE date_expire < ?
		", array($datetime));

		if ($num) {
			$this->logStatus("Cleaned up $num stale user preference entries");
		}

		#------------------------------
		# Twitter
		#------------------------------

		App::getContainer()->getSettingsHandler()->setSetting('core.twitter_last_cleanup', time());

		$db = App::getDb();
		$cutoff = gmdate('Y-m-d H:i:s', time() - App::getSetting('core.twitter_auto_remove_time'));

		$db->executeUpdate("
			DELETE IGNORE FROM twitter_accounts_statuses
			WHERE (status_type = 'timeline' OR status_type IS NULL)
				AND is_favorited = 0
				AND agent_id IS NULL
				AND agent_team_id IS NULL
				AND retweeted_id IS NULL
				AND action_agent_id IS NULL
				AND date_created < ?
		", array($cutoff));

		$deleted = $db->executeUpdate("
			DELETE IGNORE s FROM twitter_statuses AS s
			LEFT JOIN twitter_accounts_statuses AS accs ON (s.id = accs.status_id)
			WHERE s.date_created < ?
				AND accs.id IS NULL
		", array($cutoff));

		$ids = $db->fetchAllCol("
			SELECT id
			FROM twitter_users
			WHERE last_follow_update < ? AND last_follow_update IS NOT NULL
		", array($cutoff));
		if ($ids) {
			$db->executeUpdate("DELETE FROM twitter_users_followers WHERE user_id IN (" . implode(',', $ids) . ')');
			$db->executeUpdate("DELETE FROM twitter_users_friends WHERE user_id IN (" . implode(',', $ids) . ')');
			$db->executeUpdate("UPDATE twitter_users SET last_follow_update = NULL WHERE id IN (" . implode(',', $ids) . ')');
		}

		if ($deleted) {
			$this->logStatus("Cleaned up $deleted statuses");
		}

		App::getContainer()->getSettingsHandler()->setSetting('core.twitter_last_cleanup', time());
	}
}
