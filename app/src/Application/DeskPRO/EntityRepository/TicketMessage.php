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
use Doctrine\ORM\EntityRepository;

use Orb\Util\Numbers;

class TicketMessage extends AbstractEntityRepository
{
	public function getLastAgentReply($ticket)
	{
		if (!($ticket instanceof Entity\Ticket)) {
			$ticket = App::getEntityRepository('DeskPRO:Ticket')->find($ticket);
		}

		return $this->getEntityManager()->createQuery("
			SELECT m
			FROM DeskPRO:TicketMessage m
			LEFT JOIN m.person p
			WHERE
				m.ticket = ?1
				AND p.is_agent = 1
				AND m.is_agent_note = 0
			ORDER BY m.id DESC
		")->setMaxResults(1)->setParameters(array(1=> $ticket))->getOneOrNullResult();
	}

	/**
	 * Fetch the first message of a ticket.
	 *
	 * @throws NoResultException If there is no message. This shouldn't happen
	 *                           because a ticket should always have a message. So it's quite exceptional indeed!
	 * @param int|Ticket $ticket A ticket ID or the ID of a ticket
	 * @return TicketMessage
	 */
	public function getFirstTicketMessage($ticket)
	{
		if (!($ticket instanceof Entity\Ticket)) {
			$ticket = App::getEntityRepository('DeskPRO:Ticket')->find($ticket);
		}

		try {
			$message = $this->getEntityManager()->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessage m
				WHERE m.ticket = ?1
				ORDER BY m.id ASC
			")->setParameter(1, $ticket)->setMaxResults(1)->getSingleResult();

			return $message;
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}


	/**
	 * Get all messages in a ticket
	 *
	 * @param  $ticket
	 * @return array
	 */
	public function getTicketMessages($ticket, array $set_options = array())
	{
		$options = array_merge(array(
			'order' => 'ASC',
			'limit' => null,
			'with_notes' => false,
			'since_id' => 0
		), $set_options);

		$order = strtoupper($options['order']);
		if (!in_array($order, array('ASC', 'DESC'))) {
			$order = 'ASC';
		}

		$q = $this->getEntityManager()->createQueryBuilder();
		$q->from('DeskPRO:TicketMessage', 'm');
		$q->select('m');
		$q->leftJoin('m.person', 'p');
		$q->where('m.ticket = :ticket');
		$q->addOrderBy('m.date_created', $order);

		$params = array();
		$params['ticket'] = $ticket;

		if (isset($options['since_id']) && $options['since_id']) {
			$q->andWhere('m.id > :since_id');
			$params['since_id'] = $options['since_id'];
		}

		if (!$options['with_notes']) {
			$q->andWhere('m.is_agent_note = false');
		}

		if ($options['limit']) {
			$q->setMaxResults($options['limit']);
		}

		$q = $q->getQuery();

		$messages = $q->execute($params);

		return $messages;
	}


	/**
	 * Checks the database for a duplicate message.
	 *
	 * Returns the TicketMessage if there was one found, or false if none found.
	 *
	 * @param \Application\DeskPRO\Entity\TicketMessage $message
	 * @param int $secs_ago
	 * @return bool|mixed
	 */
	public function checkDupeMessage(Entity\TicketMessage $message, $ticket = null, $secs_ago = 10800 /* 3 hours */, \Orb\Log\Logger $logger = null)
	{
		if (App::getConfig('debug.disable_dupe_check')) {
			if ($logger) $logger->logDebug("debug.disable_dupe_check is enabled");
			return false;
		}

		$timesnip = date_create('-' . $secs_ago . ' seconds');

		if ($ticket) {
			if ($logger) $logger->logDebug("[EntityRepository:TicketMessage] Checking {$message['id']} for dupe in ticket {$ticket['id']} (-$secs_ago s)");
			$check_matches = $this->_em->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessage m
				LEFT JOIN m.ticket t
				WHERE m.message_hash = ?0 AND m.date_created > ?1 AND m.ticket = ?2
			")->setParameters(array($message['message_hash'], $timesnip, $ticket))->getResult();
		} else {
			if ($logger) $logger->logDebug("[EntityRepository:TicketMessage] Checking {$message['id']} for dupes in any previous ticket (-$secs_ago s)");
			$check_matches = $this->_em->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessage m
				LEFT JOIN m.ticket AS t
				WHERE m.message_hash = ?0 AND m.date_created > ?1 AND t.subject = ?2
			")->setParameters(array($message['message_hash'], $timesnip, $message->withNewSubject))->getResult();
		}

		$ids = array();
		foreach ($check_matches as $t) $ids[] = $t->getId();
		$ids = implode(', ', $ids);

		if ($logger) $logger->logDebug("[EntityRepository:TicketMessage] Found " . count($check_matches) . " possibles: $ids");

		if (!$check_matches || !count($check_matches)) {
			return false;
		}

		foreach ($check_matches as $check) {
			$prev_message = $this->_em->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessage m
				WHERE m.ticket = ?0 AND m.id < ?1
				ORDER BY m.id DESC
			")->setMaxResults(1)->setParameters(array($check->ticket->getId(), $check->getId()))->getOneOrNullResult();

			// There is no previous message, so it is a dupe
			if (!$prev_message) {
				if ($logger) $logger->logDebug("[EntityRepository:TicketMessage] {$check['id']} is a match because no prev message, dupe yes");
				return $check;
			}

			// The previous message is also by us, so it is a dupe
			if ($prev_message->person->getId() == $message->person->getId()) {
				if ($logger) $logger->logDebug("[EntityRepository:TicketMessage] {$check['id']} is a match because prev message is by us");
				return $check;
			}

			if ($logger) $logger->logDebug("[EntityRepository:TicketMessage] {$check['id']} is not a match");
		}

		return false;
	}
}
