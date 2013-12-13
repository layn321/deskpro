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
*/

namespace Cloud\AdminBundle\Controller;

use Application\DeskPRO\App;
use DeskPRO\Kernel\License;

use Application\AdminBundle\Controller\MainController as BaseMainController;

class MainController extends BaseMainController
{
	public function indexAction()
	{
		$agents_online_ids = $this->em->getRepository('DeskPRO:Session')->getAvailableAgentIds();
		$online_agents = array();
		foreach ($agents_online_ids as $aid) {
			$online_agents[$aid] = $this->em->getRepository('DeskPRO:Person')->getAgent($aid);
		}

		$count_online_users = $this->db->fetchColumn("
			SELECT COUNT(*) FROM sessions
			WHERE date_last > ? AND is_helpdesk = 1
		", array(date('Y-m-d H:i:s', time() - App::getSetting('core.sessions_lifetime'))));
		$count_online_users -= max(0, count($online_agents));

		$stats = array();
		$today = $this->person->getDateTime();
		$today->setTime(0,0,0);
		$today = \Orb\Util\Dates::convertToUtcDateTime($today);
		$today = $today->format('Y-m-d H:i:s');

		$stats['created_today']  = $this->db->fetchColumn("SELECT COUNT(*) FROM tickets WHERE date_created > ?", array($today));
		$stats['resolved_today'] = $this->db->fetchColumn("SELECT COUNT(*) FROM tickets WHERE date_resolved > ?", array($today));
		$stats['awaiting_agent'] = $this->db->fetchColumn("SELECT COUNT(*) FROM tickets WHERE status = 'awaiting_agent'");

		$last_login = $this->em->getRepository('DeskPRO:LoginLog')->getLast($this->person);

		$onboard = new \Application\AdminBundle\OnboardNotices();

		if (!$this->container->getSetting('core.cloud.has_first_login')) {
			$this->container->getSettingsHandler()->setSetting('core.cloud.has_first_login', 1);

			$data_init = new \Application\InstallBundle\Data\DataInitializer($this->container);
			$data_init->newDefaultTicket($this->person);
		}

		$sendmail_error_count = $this->db->fetchColumn("SELECT COUNT(*) FROM sendmail_queue WHERE date_next_attempt IS NULL");

		return $this->render('@index.html.twig', array(
			'lic'                => License::getLicense(),
			'online_agents'      => $online_agents,
			'count_online_users' => $count_online_users,
			'stats'              => $stats,
			'last_login'         => $last_login,
			'onboard'            => $onboard,
			'sendmail_error_count' => $sendmail_error_count,
		));
	}
}
