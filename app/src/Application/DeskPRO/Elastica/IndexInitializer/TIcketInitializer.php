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
 * @subpackage Elastica
 */

namespace Application\DeskPRO\Elastica\IndexInitializer;

use Orb\Log\Logger;
use Application\DeskPRO\App;
use Application\DeskPRO\Elastica\Type\AbstractType;

class TicketInitializer extends AbstractInitializer
{
	public function run()
	{
		#------------------------------
		# Recreate index
		#------------------------------

		$tickets_index = $this->manager->getIndex('tickets');
		try {
			$tickets_index->delete();
			$this->logger->log('Deleted old `tickets` index', Logger::INFO);
		} catch (\Elastica_Exception_Response $e) {
			// probably means it didnt exist to begin with
			$this->logger->log('Exception while deleting old `tickets` index. Probably can be ignored. Message: ' . $e->getMessage(), Logger::NOTICE);
		}

		$tickets_active_index = $this->manager->getIndex('tickets_active');
		try {
			$tickets_active_index->delete();
			$this->logger->log('Deleted old `tickets_active` index', Logger::INFO);
		} catch (\Elastica_Exception_Response $e) {
			// probably means it didnt exist to begin with
			$this->logger->log('Exception while deleting old `tickets_active` index. Probably can be ignored. Message: ' . $e->getMessage(), Logger::NOTICE);
		}

		try {
			$tickets_index->create();
			$this->logger->log('Created `tickets` index', Logger::INFO);
		} catch (\Elastica_Exception_Response $e) {
			$this->logger->log('Could not create `tickets` index. Aborting. Error: ' . $e->getMessage(), Logger::ERR);
			return 0;
		}

		try {
			$tickets_active_index->create();
			$this->logger->log('Created `tickets_active` index', Logger::INFO);
		} catch (\Elastica_Exception_Response $e) {
			$this->logger->log('Could not create `tickets_active` index. Aborting. Error: ' . $e->getMessage(), Logger::ERR);
			return 0;
		}

		#------------------------------
		# Run through each content type
		#------------------------------

		$time_start = microtime(true);
		$total = 0;
		try {
			$total += $this->runForTickets(false);
			$total += $this->runForTicketMessages(false);
			$total += $this->runForTickets(true);
			$total += $this->runForTicketMessages(true);
		} catch (\Exception $e) {
			$this->logger->log('Exception: ' . $e->getMessage(), Logger::ERR);
			throw $e;
		}

		$time = sprintf("%.5f", microtime(true)-$time_start);
		$this->logger->log("Indexed $total items in $time seconds", Logger::INFO);

		return $total;
	}

	public function runForTickets($archive = false)
	{
		if ($archive) {
			$where = "tickets.status IN ('awaiting_agent','awaiting_user')";
			$name = "ticket (open)";
		} else {
			$where = "tickets.status NOT IN ('awaiting_agent','awaiting_user')";
			$name = "ticket (archived)";
		}

		$count = App::getDb()->fetchColumn("SELECT COUNT(*) FROM tickets WHERE $where");
		if (!$count) {
			$this->logger->log("No $name objects", Logger::INFO);
		}

		$time_start = microtime(true);
		$this->logger->log("START $name ($count objects)", Logger::INFO);

		$per_page = 25;
		$pages = ceil($count / $per_page);

		for ($i = 0; $i < $pages; $i++) {
			$documents = array();
			$offset = $i * $per_page;

			$objects = App::getOrm()->createQuery("
				SELECT o
				FROM DeskPRO:Ticket o
				ORDER BY o.id
			")->setMaxResults($per_page)->setFirstResult($offset)->execute();

			foreach ($objects as $object) {
				$doc = App::get('deskpro.elastica.types.ticket')->transformToDocument($object);
				$documents[] = $doc;
			}

			App::getOrm()->clear();

			$this->manager->getClient()->addDocuments($documents);
			$this->logger->log("--- Inserted batch $i of $pages", Logger::INFO);
		}

		$time = sprintf("%.5f", microtime(true)-$time_start);
		$this->logger->log("END $name (took $time seconds)", Logger::INFO);

		return $count;
	}

	public function runForTicketMessages($archive = false)
	{
		if ($archive) {
			$where = "tickets.status IN ('awaiting_agent','awaiting_user')";
			$name = "ticket_messages (open)";
		} else {
			$where = "tickets.status NOT IN ('awaiting_agent','awaiting_user')";
			$name = "ticket_messages (archived)";
		}

		$count = App::getDb()->fetchColumn("
			SELECT COUNT(*)
			FROM ticket_messages
			LEFT JOIN tickets ON (ticket_messages.ticket_id = tickets.id)
			WHERE $where
		");
		if (!$count) {
			$this->logger->log("No $name objects", Logger::INFO);
		}

		$time_start = microtime(true);
		$this->logger->log("START $name ($count objects)", Logger::INFO);

		$per_page = 25;
		$pages = ceil($count / $per_page);

		for ($i = 0; $i < $pages; $i++) {
			$documents = array();
			$offset = $i * $per_page;

			$objects = App::getOrm()->createQuery("
				SELECT o
				FROM DeskPRO:TicketMessage o
				ORDER BY o.id
			")->setMaxResults($per_page)->setFirstResult($offset)->execute();

			foreach ($objects as $object) {
				$doc = App::get('deskpro.elastica.types.ticket_message')->transformToDocument($object);
				$documents[] = $doc;
			}

			App::getOrm()->clear();

			$this->manager->getClient()->addDocuments($documents);
			$this->logger->log("--- Inserted batch $i of $pages", Logger::INFO);
		}

		$time = sprintf("%.5f", microtime(true)-$time_start);
		$this->logger->log("END $name (took $time seconds)", Logger::INFO);

		return $count;
	}
}
