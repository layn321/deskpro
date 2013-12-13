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

namespace Application\DeskPRO\AgentAlert;

use Application\DeskPRO\Domain\DomainObject;
use Application\DeskPRO\Entity\AgentAlert;
use Application\DeskPRO\ORM\EntityManager;
use Application\DeskPRO\DBAL\Connection;
use Application\DeskPRO\Entity\ClientMessage;
use Application\DeskPRO\Tickets\TicketActions\AgentAlertNotificationAction;

class AlertSender
{
	/**
	 * @var \Application\DeskPRO\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->db = $em->getConnection();
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person $agent
	 * @param string $type
	 * @param array $data
	 */
	public function send($agent, $type, array $data)
	{
		$tpl_line = null;

		$alert = new AgentAlert();
		$alert->person   = $agent;
		$alert->typename = $type;
		$alert->data     = $data;

		if (isset($data['browser_rendered'])) {
			$alert->addTargetMap(AgentAlert::TARGET_BROWSER, array('browser_rendered'));
		}
		$this->em->persist($alert);
		$this->em->flush($alert);

		if (isset($data['browser_rendered'])) {
			$tpl_line = $data['browser_rendered'];

			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => 'agent-notify.tickets',
				'data' => array(
					'type'       => $type,
					'alert_id'   => $alert->getId(),
					'row'        => $tpl_line
				),
				'for_person'        => $agent,
				'created_by_client' => 'sys'
			));
			$this->em->persist($cm);
			$this->em->flush($cm);
		}

		return $alert;
	}


	/**
	 * @param AgentAlert $alert
	 * @return array
	 */
	public function getDataArray(AgentAlert $alert, $target = null)
	{
		$data = $alert->getData($target);

		if (isset($data['@fetch_types'])) {
			$fetch_types = $data['@fetch_types'];
			unset($data['@fetch_types']);

			foreach ($fetch_types as $k => $type) {
				if (!isset($data[$k]) || !$data[$k]) {
					$data[$k] = null;
					continue;
				}

				$val = $data[$k];
				if (is_array($val)) {
					$data[$k] = $this->em->getRepository($type)->getByIds($val, true);

					foreach ($data[$k] as &$sub) {
						$sub = $sub->toApiData();
					}
					unset($sub);
				} else {
					$data[$k] = $this->em->getRepository($type)->find($val);
					if ($data[$k] && $data[$k] instanceof DomainObject) {
						$data[$k] = $data[$k]->toApiData();
					} else {
						unset($data[$k]);
					}
				}
			}
		}

		return $data;
	}
}