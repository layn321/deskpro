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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\ChatConversation;
use Application\DeskPRO\Entity\ChatMessage;
use Application\DeskPRO\Entity\ClientMessage;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;

class UserTrackController extends AbstractController
{
	####################################################################################################################
	# win-header-table
	####################################################################################################################

	public function winHeaderTableAction()
	{
		$cut = new \DateTime("@" . (time() - $this->settings->get('core_chat.user_online_time')));

		$visitors = $this->em->createQuery("
			SELECT v, t, ti
			FROM DeskPRO:Visitor v
			LEFT JOIN v.last_track t
			LEFT JOIN v.visit_track ti
			WHERE v.date_last > ?0 AND v.last_track IS NOT NULL AND v.hint_hidden = 0
			ORDER BY v.date_last DESC
		")->setMaxResults(100)->execute(array($cut));

		return $this->render('AgentBundle:UserTrack:header-table.html.twig', array(
			'visitors' => $visitors
		));
	}

	####################################################################################################################
	# view
	####################################################################################################################

	public function viewAction($visitor_id)
	{
		$visitor = $this->em->find('DeskPRO:Visitor', $visitor_id);

		if (!$visitor) {
			throw $this->createNotFoundException();
		}

		$tracks = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:VisitorTrack t
			WHERE t.visitor = ?0
			ORDER BY t.id DESC
		")->execute(array($visitor));

		$visit_tracks = $this->em->createQuery("
			SELECT t
			FROM DeskPRO:VisitorTrack t
			WHERE t.visitor = ?0 AND t.is_new_visit = true AND t.is_soft_track = 0
			ORDER BY t.id DESC
		")->execute(array($visitor));

		$ip_addresses  = array();
		$user_agents   = array();
		$geo_countries = array();
		foreach ($visit_tracks as $t) {
			if ($t->ip_address) {
				$ip_addresses[$t->ip_address] = $t->ip_address;
			}
			if ($t->user_agent) {
				$user_agents[$t->user_agent] = $t->user_agent;
			}
			if ($t->geo_country) {
				$geo_countries[$t->geo_country] = $t->geo_country;
			}
		}

		return $this->render('AgentBundle:UserTrack:view.html.twig', array(
			'visitor'         => $visitor,
			'tracks'          => $tracks,
			'visit_tracks'    => $visit_tracks,
			'ip_addresses'    => $ip_addresses,
			'user_agents'     => $user_agents,
			'geo_countries'   => $geo_countries,
		));
	}
}