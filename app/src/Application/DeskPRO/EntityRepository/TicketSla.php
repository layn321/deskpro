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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;
use \Doctrine\ORM\EntityRepository;
use Application\DeskPRO\Entity;

use Orb\Util\Numbers;

class TicketSla extends AbstractEntityRepository
{
	public function getTicketSlaCountsForAgentInterface(array $slas, $filter = 'all', Entity\Person $person_context = null)
	{
		if (!$slas) {
			return array();
		}

		if (!$person_context) {
			$person_context = App::getCurrentPerson();
		}

		if (!$person_context->is_agent) {
			throw new \InvalidArgumentException('Person must be an agent');
		}

		$where_perm = array();

		if ($person_context->getDisallowedDepartments()) {
			$where_perm[] = "tickets.department_id NOT IN (" . implode(',', $person_context->getDisallowedDepartments()) . ")";
		}

		if (!$person_context->hasPerm('agent_tickets.view_unassigned')) {
			$where_perm[] = 'tickets.agent_id IS NOT NULL';
		}

		if (!$person_context->hasPerm('agent_tickets.view_others')) {
			$part = array();
			$part[] = "tickets.agent_id = {$person_context['id']}";
			if ($person_context->getAgentTeamIds()) {
				$part[] = "tickets.agent_team_id IN (" . implode(',', $person_context->getAgentTeamIds()) . ")";
			}

			$where_perm[] = '(' . implode(' OR ', $part) . ')';
		}

		if (!$where_perm) {
			$where_perm[] = '1';
		}

		$where = '((' . implode(' AND ', $where_perm) . ') OR (';

		$where .= "tickets.agent_id = {$person_context['id']} OR ";
		if ($person_context->getAgentTeamIds()) {
			$where .= "tickets.agent_team_id IN (" . implode(',', $person_context->getAgentTeamIds()) . ") OR ";
		}

		$where .= "tickets_participants_perm.person_id IS NOT NULL))";

		switch ($filter) {
			case 'agent':
				$where .= " AND tickets.agent_id = {$person_context['id']}";
				break;

			case 'team':
				if ($person_context->getAgentTeamIds()) {
					$where .= " AND tickets.agent_team_id IN (" . implode(',', $person_context->getAgentTeamIds()) . ")";
				} else {
					$where .= " AND 0";
				}
				break;
		}

		$where .= " AND ticket_slas.is_completed = 0";
		$where .= " AND ((slas.sla_type = 'waiting_time' AND tickets.status = 'awaiting_agent') OR (slas.sla_type = 'first_response' AND tickets.status = 'awaiting_agent') OR (slas.sla_type = 'resolution' AND tickets.status IN ('awaiting_agent', 'awaiting_user')))";

		$ids = array();
		foreach ($slas AS $sla) {
			$ids[] = $sla->id;
		}

		$where .= " AND ticket_slas.sla_id IN (" . implode(',', $ids) . ')';

		$results = $this->getEntityManager()->getConnection()->fetchAll("
			SELECT ticket_slas.sla_id, ticket_slas.sla_status, COUNT(*) AS count
			FROM ticket_slas
			INNER JOIN slas ON (ticket_slas.sla_id = slas.id)
			INNER JOIN tickets ON (ticket_slas.ticket_id = tickets.id)
			LEFT JOIN tickets_participants AS tickets_participants_perm ON (tickets_participants_perm.ticket_id = tickets.id AND tickets_participants_perm.person_id = {$person_context->id})
			WHERE $where
			GROUP BY  ticket_slas.sla_id, ticket_slas.sla_status
		");

		$output = array();
		foreach ($ids AS $id) {
			$output[$id] = array('ok' => 0, 'warning' => 0, 'fail' => 0);
		}
		foreach ($results AS $result) {
			$output[$result['sla_id']][$result['sla_status']] = $result['count'];
		}

		return $output;
	}

	public function getTicketSlasPastThreshold($type)
	{
		switch ($type) {
			case 'warning': $date_field = 'warn_date'; $statuses = "'ok'"; break;
			case 'fail': $date_field = 'fail_date'; $statuses = "'ok','warning'"; break;
			default: throw new \InvalidArgumentException("Unknown SLA status $type");
		}

		return $this->getEntityManager()->createQuery("
			SELECT ts
			FROM DeskPRO:TicketSla ts
			WHERE ts.is_completed = 0
				AND ts.sla_status IN ($statuses)
				AND ts.$date_field < ?0
		")->setMaxResults(250)->execute(array(new \DateTime('now', new \DateTimeZone('UTC'))));
	}

	public function getTicketSlaAdminGraphData()
	{
		$dt = App::getCurrentPerson()->getDateTime();

		$today = $dt->setTime(0, 0, 0)->getTimestamp();
		$yesterday = $dt->modify('-1 day')->getTimestamp();

		$dt->modify('+1 day');

		$currentDayOfWeek = $dt->format('N');
		$startAdjust = $currentDayOfWeek - App::getCurrentPerson()->getStartOfWeek();

		if ($startAdjust) {
			if ($startAdjust > 0) {
				$dt->modify('-' . $startAdjust . ' days');
			} else {
				$dt->modify('-' . (7 + $startAdjust) . ' days');
			}
		}

		$week_start = $dt->getTimestamp();

		$dt = App::getCurrentPerson()->getDateTime();

		$month = $dt->format('n');
		$year = $dt->format('Y');

		$graphs = array(
			'today' => $today,
			'yesterday' => array($yesterday, $today - 1),
			'this_week' => $week_start,
			'this_month' => gmmktime(0, 0, 0, $month, 1, $year),
			'this_year' => gmmktime(0, 0, 0, 1, 1, $year)
		);

		$output = array();
		foreach ($graphs AS $title => $start) {
			if (is_array($start)) {
				list($start, $end) = $start;
			} else {
				$end = null;
			}
			$data = $this->getTicketSlaStatusData($start, $end);
			if ($data) {
				$output[$title] = array(
					'ok' => array('title' => 'OK', 'count' => 0, 'id' => 'ok', 'color' => '#abf3ae'),
					'warning' => array('title' => 'Warning', 'count' => 0, 'id' => 'warning', 'color' => '#F7BC1F'),
					'fail' => array('title' => 'Failed', 'count' => 0, 'id' => 'count', 'color' => '#de5949'),
				);
				foreach ($data AS $status => $count) {
					$output[$title][$status]['count'] = $count;
				}

				$output[$title] = array_values($output[$title]);
			}
		}

		return $output;
	}

	public function getTicketSlaStatusData($start, $end = null)
	{
		if (!$end) {
			$end = time();
		}

		return App::getDb()->fetchAllKeyValue("
			SELECT ticket_slas.sla_status, COUNT(*)
			FROM ticket_slas
			INNER JOIN tickets ON (ticket_slas.ticket_id = tickets.id)
			WHERE tickets.date_created >= ? AND tickets.date_created <= ?
			GROUP BY ticket_slas.sla_status
		", array(gmdate('Y-m-d H:i:s', $start), gmdate('Y-m-d H:i:s', $end)));
	}
}
