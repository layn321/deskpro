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

class Build1359109263 extends AbstractBuild
{
	public function run()
	{
		$this->out("Update worker jobs");
		$this->execMutateSql("DELETE FROM `worker_jobs` WHERE `id` IN ('cleanup_client_messages','cleanup_sendmail', 'cleanup_sessions', 'cleanup_ticket_locks', 'cleanup_tmp_attach', 'cleanup_tmp_data', 'cleanup_twitter', 'cleanup_drafts')");

		$install_data = new \Application\InstallBundle\Install\InstallDataReader(DP_ROOT.'/src/Application/InstallBundle/Data/data.php');
		$em = $this->container->getEm();

		eval($install_data->get('create_jobs.cleanup_always'));
		eval($install_data->get('create_jobs.cleanup_quarter_hourly'));
		eval($install_data->get('create_jobs.cleanup_hourly'));
		eval($install_data->get('create_jobs.cleanup_daily'));
		eval($install_data->get('create_jobs.cleanup_weekly'));
	}
}