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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

/**
 * Displays cron jobs
 */
class CronController extends AbstractController
{
	public function listAction()
	{
		$jobs = $this->em->createQuery("
			SELECT j
			FROM DeskPRO:WorkerJob j
			ORDER BY j.interval ASC
		")->execute();

		$last_start = $this->container->getSetting('core.last_cron_start');
		if (!$last_start) $last_start = 0;
		$time_since_start = time() - $last_start;

		$last_run = $this->container->getSetting('core.last_cron_run');
		if (!$last_run) $last_run = 0;
		$time_since_run = time() - $last_run;

		return $this->render("AdminBundle:Cron:list.html.twig", array(
			'jobs' => $jobs,
			'last_run' => $last_run,
			'time_since_run' => $time_since_run,
			'last_start' => $last_start,
			'time_since_start' => $time_since_start,
		));
	}

	public function logsAction()
	{
		$jobs = $this->em->createQuery("
			SELECT j
			FROM DeskPRO:WorkerJob j
			ORDER BY j.interval ASC
		")->execute();

		$job_id = $this->in->getString('job_id');
		if ($job_id) {
			$search_job = 'worker_job.' . $job_id;
		} else {
			$search_job = 'worker_job.%';
		}

		$search_pri = 10;
		if ($this->in->getUint('priority')) {
			$search_pri = $this->in->getUint('priority');
		}

		$logs = $this->db->fetchAll("
			SELECT log_name, session_name, message, priority, UNIX_TIMESTAMP(date_created) AS date_created
			FROM log_items
			WHERE log_name LIKE ? AND priority <= ?
			ORDER BY id DESC
			LIMIT 2000
		", array($search_job, $search_pri));

		return $this->render('AdminBundle:Cron:logs.html.twig', array(
			'job_id' => $job_id,
			'priority' => $search_pri,
			'logs' => $logs,
			'jobs' => $jobs,
		));
	}

	public function clearLogsAction()
	{
		$this->ensureRequestToken('clear_cron_logs', 'x');
		$this->db->exec("DELETE FROM log_items WHERE log_name LIKE 'worker_job.%'");

		return $this->redirectRoute('admin_server_cron');
	}
}
