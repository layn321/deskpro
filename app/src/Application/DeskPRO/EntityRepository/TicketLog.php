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
use Application\DeskPRO\Entity;

use \Doctrine\ORM\EntityRepository;
use Orb\Util\Arrays;

class TicketLog extends AbstractEntityRepository
{
	/**
	 * @param Entity\Ticket $ticket
	 * @param array $options
	 * @return array
	 */
	public function getLogsForTicket(Entity\Ticket $ticket, array $options = array())
	{
		#------------------------------
		# Get logs
		#------------------------------

		if (!isset($options['order_dir'])) {
			$options['order_dir'] = 'DESC';
		}

		$params = array('ticket_id' => $ticket->getId());

		$qb = $this->getEntityManager()->createQueryBuilder();
		$qb->select('log, p')
			->from('DeskPRO:TicketLog', 'log')
			->leftJoin('log.person', 'p')
			->andWhere('log.ticket = :ticket_id');

		if ($options['order_dir'] == 'ASC') {
			$qb->orderBy('log.date_created', 'ASC');
		} else {
			$qb->orderBy('log.date_created', 'DESC');
		}

		if (!empty($options['since_id'])) {
			$qb->andWhere('log.id > :since_id');
			$params['since_id'] = $options['since_id'];
		}

		$query = $qb->getQuery();
		$raw_ticket_logs = $query->execute($params);

		$ticket_logs = array();
		foreach ($raw_ticket_logs as $l) {
			$ticket_logs[$l->getId()] = $l;
		}
		unset($raw_ticket_logs);

		return $ticket_logs;
	}


	/**
	 * @param $ticket_logs
	 * @return array
	 */
	public function groupTicketLogs($ticket_logs)
	{
		#------------------------------
		# Group them
		#------------------------------

		// Need another one to make sure all of the children are here as well
		$pids = array(0);
		$cids = array(0);
		foreach ($ticket_logs as $l) {
			if (!$l->parent) {
				$pids[] = $l->getId();
			} else {
				$cids[] = $l->getId();
			}
		}

		// And group children under their parent row
		$return = array();

		foreach ($ticket_logs as $log) {
			if ($log->parent) {
				$pid = $log->parent->getId();

				if (!isset($ticket_logs[$pid])) {
					continue;
				}

				if (!isset($return[$pid])) {
					$return[$pid] = $ticket_logs[$pid];
				}

				$return[$pid]->grouped[] = $log;
			} else {
				$return[$log->getId()] = $log;
			}
		}

		return $return;
	}


	/**
	 * @param $ticket_logs
	 * @param $filter_type
	 * @return array
	 */
	public function filterTicketLogs($ticket_logs, $filter_type)
	{
		$type_map = array(
			'message'  => array('message_removed', 'message_edit', 'message_created'),
			'note'     => array('message_note_created'),
			'notif'    => array('agent_notify', 'user_notify'),
			'assign'   => array('changed_agent', 'changed_agent_team', 'changed_person', 'participant_added', 'participant_removed'),
			'slas'     => array('ticket_sla_added', 'ticket_sla_removed', 'ticket_sla_updated'),
			'triggers' => array('executed_triggers'),
			'status'   => array('status'),
		);

		$types = $type_map[$filter_type];

		$return = array();

		foreach ($ticket_logs as $log) {
			if (in_array($log->action_type, $types)) {
				$return[$log->id] = $log;
			}
		}

		return $return;
	}


	/**
	 * @param $ticket_logs
	 * @return array
	 */
	public function countTicketLogTypes($ticket_logs)
	{
		$counts = array('all' => 0);

		$type_map = array(
			'message'  => array('message_removed', 'message_edit', 'message_created'),
			'note'     => array('message_note_created'),
			'notif'    => array('agent_notify', 'user_notify'),
			'assign'   => array('changed_agent', 'changed_agent_team', 'changed_person', 'participant_added', 'participant_removed'),
			'slas'     => array('ticket_sla_added', 'ticket_sla_removed', 'ticket_sla_updated'),
			'triggers' => array('executed_triggers'),
			'status'   => array('status'),
		);

		foreach ($ticket_logs as $log) {
			$counts['all']++;

			foreach ($type_map as $t => $types) {
				if (in_array($log->action_type, $types)) {
					if (!isset($counts[$t])) $counts[$t] = 0;
					$counts[$t]++;
				}
			}
		}

		return $counts;
	}


    public function getLogsForAgent(Entity\Person $agent, array $options = array())
    {
        if(isset($options['date_range'])) {
            $query = $this->_em->createQuery("
				SELECT log
				FROM DeskPRO:TicketLog log INDEX BY log.id
				WHERE log.person = ?1
				AND log.date_created BETWEEN ?2 AND ?3
				ORDER BY log.date_created ASC
			")
            ->setParameter(1, $agent)
            ->setParameter(2, $options['date_range']['start'])
            ->setParameter(3, $options['date_range']['end'])
            ;
        } else {
            $query = $this->_em->createQuery("
				SELECT log
				FROM DeskPRO:TicketLog log INDEX BY log.id
				WHERE log.person = ?1
				ORDER BY log.date_created ASC
			")
            ->setParameter(1, $agent);
        }

        return $query->execute();
    }
}
