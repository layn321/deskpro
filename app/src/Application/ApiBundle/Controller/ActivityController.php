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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Searcher\ChatConversationSearch;
use Application\DeskPRO\Entity\ChatConversation;
use Orb\Util\Arrays;

class ActivityController extends AbstractController
{
	/**
	 * @param int $since
	 */
	public function getActivityAction($since)
	{
		if (!$since) {
			$alert_recs = $this->em->createQuery("
				SELECT a
				FROM DeskPRO:AgentAlert a
				WHERE a.person = ?0 AND a.is_dismissed = 0
				ORDER BY a.id DESC
			")->setParameters(array($this->person))->setMaxResults(200)->execute();
		} else {
			$alert_recs = $this->em->createQuery("
				SELECT a
				FROM DeskPRO:AgentAlert a
				WHERE a.person = ?0 AND a.id >= ?1 AND a.is_dismissed = 0
				ORDER BY a.id DESC
			")->setParameters(array($this->person, $since))->execute();
		}

		$alerts = array();
		foreach ($alert_recs as $alert) {
			$alerts[] = array(
				'id'                 => $alert->getId(),
				'type'               => $alert->typename,
				'date_created'       => $alert->date_created->format('Y-m-d H:i:s'),
				'date_created_ts'    => $alert->date_created->getTimestamp(),
				'date_created_ts_ms' => $alert->date_created->getTimestamp() * 1000,
				'data'               => $this->container->getAgentAlertSender()->getDataArray($alert)
			);
		}

		$last_id = $this->db->fetchColumn("SELECT id FROM agent_alerts ORDER BY id DESC LIMIT 1");

		return $this->createApiResponse(array('last_id' => $last_id, 'alerts' => $alerts));
	}

	public function dismissAction()
	{
		// Could be a json encoded array
		if (isset($_REQUEST['dismiss_ids']) && !is_array($_REQUEST['dismiss_ids'])) {
			$alert_ids = $this->in->getString('dismiss_ids');
			$alert_ids = @json_decode($alert_ids, true);

			if ($alert_ids) {
				$alert_ids = Arrays::castToType($alert_ids, 'int', 'discard');
				$alert_ids = array_unique($alert_ids);
			}

		// or a regular posted array
		} else {
			$alert_ids = $this->in->getCleanValueArray('dismiss_ids', 'int', 'discard');
			$alert_ids = Arrays::removeFalsey($alert_ids);
			$alert_ids = array_unique($alert_ids);
		}

		if ($alert_ids) {
			if (in_array(-1, $alert_ids)) {
				$this->db->executeUpdate("
					UPDATE agent_alerts
					SET is_dismissed = 1
					WHERE person_id = ?
				", array($this->person->getId()));
			} else {
				$ids_in = implode(',', $alert_ids);
				$this->db->executeUpdate("
					UPDATE agent_alerts
					SET is_dismissed = 1
					WHERE person_id = ? AND id IN ($ids_in)
				", array($this->person->getId()));
			}
		}

		return $this->createApiResponse(array('success' => true));
	}
}