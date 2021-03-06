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
 * @subpackage Search
 */

namespace Application\DeskPRO\Search\IndexInitializer;

use Application\DeskPRO\App;

abstract class ContentInitializer extends AbstractInitializer
{
	abstract public function preRun();
	
	public function run()
	{
		$this->preRun();

		#------------------------------
		# Run through each content type
		#------------------------------

		$time_start = microtime(true);
		$total = 0;
		try {
			$total += $this->runForType('article',  'DeskPRO:Article');
			$total += $this->runForType('download', 'DeskPRO:Download');
			$total += $this->runForType('feedback',     'DeskPRO:Feedback');
			$total += $this->runForType('news',     'DeskPRO:News');
		} catch (\Exception $e) {
			$this->logger->log('Exception: ' . $e->getMessage(), Logger::ERR);
			throw $e;
		}

		$time = sprintf("%.5f", microtime(true)-$time_start);
		$this->logger->log("Indexed $total items in $time seconds", Logger::INFO);

		return $total;
	}

	public function runForType($type_name, $entity_name)
	{
		$table_name = App::getOrm()->getClassMetadata($entity_name)->getTableName();

		$count = App::getDb()->fetchColumn("SELECT COUNT(*) FROM $table_name");
		if (!$count) {
			$this->logger->log("No $entity_name objects", Logger::INFO);
		}

		$time_start = microtime(true);
		$this->logger->log("START $entity_name ($count objects)", Logger::INFO);

		$per_page = 25;
		$pages = ceil($count / $per_page);

		for ($i = 0; $i < $pages; $i++) {
			$offset = $i * $per_page;

			$objects = App::getOrm()->createQuery("
				SELECT o
				FROM $entity_name o
				ORDER BY o.id
			")->setMaxResults($per_page)->setFirstResult($offset)->execute();

			$this->adapter->updateObjectsInIndex($objects);
			$this->logger->log("--- Inserted batch $i of $pages", Logger::INFO);

			App::getOrm()->clear();
		}

		$time = sprintf("%.5f", microtime(true)-$time_start);
		$this->logger->log("END $entity_name (took $time seconds)", Logger::INFO);

		return $count;
	}
}
