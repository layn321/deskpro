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

class CleanupQuarterHourly extends AbstractJob
{
	const DEFAULT_INTERVAL = 900;

	public function run()
	{
		#------------------------------
		# Page cache
		#------------------------------

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->cleanup();

		#------------------------------
		# sessions
		#------------------------------

		$datetime = date('Y-m-d H:i:s', time() - App::getSetting('core.sessions_lifetime'));
		$num = App::getDb()->executeUpdate("DELETE FROM sessions WHERE date_last < ?", array($datetime));

		if ($num) {
			$this->logStatus("Cleaned up $num stale sessions");
		}

		#------------------------------
		# ticket locks
		#------------------------------

		$datetime = date('Y-m-d H:i:s', time() - App::getSetting('core_tickets.lock_lifetime'));
		$num = App::getDb()->executeUpdate("UPDATE tickets SET date_locked = null, locked_by_agent = null  WHERE date_locked < ?", array($datetime));

		if ($num) {
			$this->logStatus("Cleaned up $num ticket locks");
		}

		#------------------------------
		# Agent alerts
		#------------------------------

		if ($maxage = App::getSetting('agent.alerts_cleanup_time')) {
			$datetime = date('Y-m-d H:i:s', time() - $maxage);
			$num = App::getDb()->executeUpdate("
				DELETE FROM agent_alerts
				WHERE date_created < ? OR is_dismissed = 1
			", array($datetime));

			if ($num) {
				$this->logStatus("Cleaned up $num agent alerts");
			}
		}

		#------------------------------
		# Update table counts
		#------------------------------

		$counts = array();
		$counts['tickets']                    = App::getDb()->fetchColumn("SELECT COUNT(*) FROM `tickets`");
		$counts['tickets.resolved']           = App::getDb()->fetchColumn("SELECT COUNT(*) FROM `tickets_search_active` WHERE `status` = 'resolved'");
		$counts['tickets.archive_validating'] = App::getDb()->fetchColumn("SELECT COUNT(*) FROM `tickets` WHERE `status` = 'hidden' AND `hidden_status` = 'validating'");
		$counts['tickets.archive_spam']       = App::getDb()->fetchColumn("SELECT COUNT(*) FROM `tickets` WHERE `status` = 'hidden' AND `hidden_status` = 'spam'");
		$counts['tickets.archive_deleted']    = App::getDb()->fetchColumn("SELECT COUNT(*) FROM `tickets` WHERE `status` = 'hidden' AND `hidden_status` = 'deleted'");
		$counts['tickets.archive_closed']     = App::getDb()->fetchColumn("SELECT COUNT(*) FROM `tickets` WHERE `status` = 'closed'");
		$counts['people']                     = App::getDb()->fetchColumn("SELECT COUNT(*) FROM `people`");

		foreach ($counts as $k => $v) {
			App::getDb()->replace('settings', array(
				'name'  => "core_tablecounts.$k",
				'value' => (int)$v
			));
		}

		// Fetch in agent context
		$filters = App::getOrm()->createQuery("
			SELECT f
			FROM DeskPRO:TicketFilter f
			WHERE f.sys_name LIKE 'archive_%' AND f.sys_name != 'archive_resolved' AND f.sys_name != 'archive_awaiting_user'
		")->execute();

		$inserts = array();

		foreach (App::getContainer()->getAgentData()->getAgents() as $agent) {
			$agent->loadHelper('Agent');
			$agent->loadHelper('AgentTeam');
			$agent->loadHelper('AgentPermissions');
			$agent->loadHelper('PermissionsManager');
			$agent->loadHelper('HelpMessages');
			$agent->loadHelper('AgentPrefs');

			foreach ($filters as $filter) {
				/** @var \Application\DeskPRO\Entity\TicketFilter $filter*/
				$searcher = $filter->getSearcher();
				$searcher->setPersonContext($agent);

				$count = $searcher->getCount();

				$inserts[] = array(
					'person_id'   => $agent->id,
					'name'        => "ticket_counts.{$filter->sys_name}",
					'value_str'   => $count,
					'value_array' => null,
					'date_expire' => null
				);
			}
		}

		if ($inserts) {
			App::getDb()->executeUpdate("DELETE FROM people_prefs WHERE name LIKE 'ticket_counts.%'");
			App::getDb()->batchInsert('people_prefs', $inserts, true);
		}
	}
}
