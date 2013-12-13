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
use Orb\Util\Dates;

/**
 * Will send daily task due reminders
 */
class TaskReminders extends AbstractJob
{
	const DEFAULT_INTERVAL = 900;

	public function run()
	{
		#------------------------------
		# The time of day reminders are sent
		#------------------------------

		$send_time = App::getSetting('core.task_reminder_time');
		if (!$send_time || strpos($send_time, ':') === false) {
			$send_time = '09:00';
		}

		list ($hour, $min) = explode(':', $send_time);
		$hour = (int)$hour;
		$min  = (int)$min;

		#------------------------------
		# Figure out which agents the current time is for
		#------------------------------

		$for_agents = array();
		foreach (App::getDataService('Agent')->getAgents() as $agent) {
			/** @var $agent \Application\DeskPRO\Entity\Person */

			$t = $agent->getDateTime();
			$hour_now = (int)$t->format('G');
			$min_now  = (int)$t->format('i');

			if ($hour_now == $hour && $min_now >= $min) {
				$this->getLogger()->logInfo(sprintf(
					"Running for agent %d %s (Local time %s is in range of %s)",
					$agent->getId(),
					$agent->getDisplayName(),
					$t->format('H:i'),
					$send_time
				));
				$for_agents[$agent->getId()] = $agent;
			}
		}

		if (!$for_agents) {
			return;
		}

		$online_ids = App::getEntityRepository('DeskPRO:Session')->getAvailableAgentIds();
		$emails = 0;
		$alerts = 0;

		foreach ($for_agents as $agent) {

			$agent->loadHelper('Agent');
			$team_ids = $agent->Agent->getTeamIds();
			if (!$team_ids) {
				$team_ids = array(0);
			}

			$today = $agent->getDateTime();
			$today->setTime(0,0,0);

			$today_end = $agent->getDateTime();
			$today_end->setTime(23, 59, 59);

			$today_utc = Dates::convertToUtcDateTime($today);
			$today_end_utc = Dates::convertToUtcDateTime($today_end);

			$task_ids = App::getDb()->fetchAllCol("
				SELECT tasks.id
				FROM tasks
				LEFT JOIN task_reminder_logs ON (task_reminder_logs.task_id = tasks.id)
				WHERE
					tasks.is_completed = 0
					AND (tasks.assigned_agent_id = {$agent->getId()} OR tasks.assigned_agent_team_id IN (".implode(',', $team_ids) . ") OR (tasks.assigned_agent_id IS NULL AND tasks.person_id = {$agent->getId()}))
					AND tasks.date_due >= '{$today_utc->format('Y-m-d H:i:s')}' AND tasks.date_due <= '{$today_end_utc->format('Y-m-d H:i:s')}'
					AND task_reminder_logs.id IS NULL
			");

			if (!$task_ids) {
				continue;
			}

			$tasks = App::getEntityRepository('DeskPRO:Task')->getByIds($task_ids);

			foreach ($tasks as $task) {
				if (in_array($agent->id, $online_ids) && $agent->getPref('agent_notif.task_due.alert')) {
					$tpl_line = App::getTemplating()->render('AgentBundle:Task:notify-row-reminder.html.twig', array(
						'task' => $task,
						'person' => $agent
					));

					$cm = new \Application\DeskPRO\Entity\ClientMessage();
					$cm->fromArray(array(
						'channel' => 'agent-notify.tasks',
						'data' => array('row' => $tpl_line),
						'for_person'        => $agent,
						'created_by_client' => 'sys'
					));
					App::getOrm()->persist($cm);
					App::getOrm()->flush();

					$alerts++;
				}

				if ($agent->getPref('agent_notif.task_due.email')) {
					$message = App::getMailer()->createMessage();
					$message->setTemplate('DeskPRO:emails_agent:task-due-reminder.html.twig', array(
						'task' => $task,
						'person' => $agent
					));
					$message->setToPerson($agent);
					$message->setFrom(App::getSetting('core.default_from_email'), App::getSetting('core.deskpro_name'));
					App::getMailer()->send($message);

					$emails++;
				}

				App::getDb()->insert('task_reminder_logs', array(
					'task_id'   => $task->getId(),
					'person_id' => $agent->getId(),
					'date_sent' => date('Y-m-d H:i:s')
				));
			}
		}

		if ($emails || $alerts) {
			$this->getLogger()->logInfo("Reminders sent (alerts: $alerts, emails: $emails)");
		}
	}
}