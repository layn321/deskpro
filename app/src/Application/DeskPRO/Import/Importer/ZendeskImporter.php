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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer;

use Doctrine\ORM\Query;
use Orb\Log\Logger;
use Application\DeskPRO\DBAL\Logging\QueryLogger;
use Orb\Service\Zendesk\Zendesk;

class ZendeskImporter extends AbstractImporter
{
	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	public $db;

	/**
	 * @var \Application\DeskPRO\Import\Importer\ZendeskApi
	 */
	public $zd;

	/**
	 * @var int
	 */
	public $time_begin = 0;

	/**
	 * @var array
	 */
	public $table_exists = array();

	/**
	 * @var array
	 */
	public $rerun_data = array();

	/**
	 * @var string
	 */
	public $run_mode;

	protected $cache_map_types = array(
		'zd_org_id' => true,
		'zd_group_id' => true,
		'zd_ticket_field_id' => true,
	);

	protected $steps = array(
		'PreRun',
		'Orgs',
		'UsersCache',
		'Users',
		'Groups',
		'TicketFields',
		'TicketsCache',
		'TicketsDataCache',
		'Tickets',
		'TicketsRerunList',
		'TicketsRerunCache',
		'TicketsRerun',
		'UserPictures',
		'TicketAttachments',
		'RecountLabels',
	);

	public function validateOptions()
	{
		$errors = array();
		return $errors;
	}


	public function setupImport($mode = 'run')
	{
		$this->run_mode = $mode;

		gc_enable();
		$this->db = $this->container->getDb();
		$this->zd = new ZendeskApi(
			$this->config->zendesk_domain,
			$this->config->zendesk_user_id,
			$this->config->zendesk_api_token
		);
		$this->zd->importer = $this;
	}


	public function cleanupImport()
	{

	}


	public function countSteps()
	{
		return count($this->steps);
	}


	public function getStep($step)
	{
		$class = 'Application\\DeskPRO\\Import\\Importer\\Step\\Zendesk\\' . $this->steps[$step-1] . 'Step';
		$step = new $class($this);

		return $step;
	}

	/**
	 * Called before a step is run
	 *
	 * @param $step
	 */
	public function preRunStep($step)
	{
		global $DP_CONFIG;

		#------------------------------
		# logger for db connection
		#------------------------------

		$qlog = new QueryLogger();
		$qlog->ignore_triggers[] = 'DP_QLOG_NOLOG';
		$qlog->tag = 'new_db';

		$this->logger->addFilter(new \Application\InstallBundle\Logger\Filter\InstallQueryLogFormatter());
		$qlog->setLogger($this->logger);

		if (!(isset($DP_CONFIG['import']['nolog']) && $DP_CONFIG['import']['nolog'])) {
			$qlog->addSlowLogRule(QueryLogger::TYPE_ALL, 0);
		}

		$this->qlog_db = $qlog;
		$this->db->getConfiguration()->setSQLLogger($qlog);

		#------------------------------
		# logger for ZD
		#------------------------------

		$zdlog = new ZendeskApiLogger();
		$this->zdlog = $zdlog;
		$this->zd->addListener(array($zdlog, 'callback'));

		$this->zd->setLogger($this->logger);

		$this->time_begin = microtime(true);
	}


	/**
	 * Called after a step is run
	 *
	 * @param $step
	 */
	public function postRunStep($step)
	{
		gc_collect_cycles();

		$time_end   = microtime(true);
		$time_total = $time_end - $this->time_begin;

		$time_db    = $this->qlog_db->total_time;
		$time_zd    = $this->zdlog->time;
		$time_php_total = $time_total - $time_db - $time_zd;

		$total_queries  = $this->qlog_db->query_count;
		$total_apicalls = $this->zdlog->count;

		$mem = @memory_get_peak_usage();
		if (!$mem) {
			$mem = 0;
		}
		$mem = \Orb\Util\Numbers::filesizeDisplay($mem);

		$this->logMessage(sprintf("Time: %0.2f   PHP: %0.2f   ZD: %.02f   DB: %0.2f   ZD Calls: %d   Queries: %d,  Peak Mem: %s", $time_total, $time_php_total, $time_zd, $time_db, $total_apicalls, $total_queries, $mem));
	}


	/**
	 * Remove indexes on a table for bulk inserting
	 *
	 * We are a bit clever and combine the alter queries into one so they execute faster,
	 * rather than trying to do them one at a time as Doctrine does by default
	 */
	public function removeTableIndexes($table, $keep_indexes = array())
	{
		/** @var $sm \Doctrine\DBAL\Schema\AbstractSchemaManager */
		$sm = $this->getDb()->getSchemaManager();

		$indexes = $sm->listTableIndexes($table);
		$fkeys = $sm->listTableForeignKeys($table);

		$drop_parts = array();
		$restore_parts = array();

		foreach ($indexes as $x) {
			if ($x->isPrimary()) continue;

			$cols = $x->getColumns();
			$skip = false;

			foreach ($keep_indexes as $keep) {
				$count = count($keep);
				$found_count = 0;
				if (count($cols) == $count) {
					foreach ($cols as $idx_col) {
						if (in_array($idx_col, $keep)) {

							$found_count++;
						}
					}
				}

				if ($found_count == $count) {
					$skip = true;
				}
			}

			if ($skip) {
				continue;
			}

			$p = $sm->getDatabasePlatform()->getDropIndexSQL($x, $table);
			$p = preg_replace('#^ALTER TABLE (.*?) #', '', trim($p));
			$p = preg_replace("# ON (.*?)$#", '', trim($p));
			$drop_parts[] = $p;

			$p = $sm->getDatabasePlatform()->getCreateIndexSQL($x, $table);
			$p = preg_replace('#^ALTER TABLE (.*?) #', '', trim($p));
			$p = preg_replace('#^CREATE #', 'ADD ', trim($p));
			$p = preg_replace('# ON (.*?) \((.*?)\)$#', ' ($2)', trim($p));
			$restore_parts[] = $p;
		}
		foreach ($fkeys as $x) {

			$cols = $x->getColumns();
			$skip = false;

			foreach ($keep_indexes as $keep) {
				$count = count($keep);
				$found_count = 0;
				if (count($cols) == $count) {
					foreach ($cols as $idx_col) {
						if (in_array($idx_col, $keep)) {

							$found_count++;
						}
					}
				}

				if ($found_count == $count) {
					$skip = true;
				}
			}

			if ($skip) {
				continue;
			}

			$p = $sm->getDatabasePlatform()->getDropForeignKeySQL($x, $table);
			$p = preg_replace('#^ALTER TABLE (.*?) #', '', trim($p));
			$drop_parts[] = $p;

			$name = $x->getQuotedName($sm->getDatabasePlatform());

			if (isset($this->schema_file['fk'][$table][$name])) {
				$p = $this->schema_file['fk'][$table][$name];
			} else {
				$p = $sm->getDatabasePlatform()->getCreateForeignKeySQL($x, $table);
			}

			$p = preg_replace('#^ALTER TABLE (.*?) #', '', trim($p));
			$restore_parts[] = $p;
		}

		if (!$drop_parts) {
			return;
		}

		$drop_sql      = "ALTER TABLE `$table` " . implode(', ', $drop_parts);
		$restore_sql   = "ALTER TABLE `$table` " . implode(', ', $restore_parts);

		$this->getDb()->replace('import_datastore', array(
			'typename' => 'tableindexes.' . $table,
			'data' => serialize(array('sql' => $restore_sql))
		));

		$this->getDb()->exec($drop_sql);
	}


	/**
	 * Restores indexes that were previously deleted
	 *
	 * @param string $table
	 */
	public function restoreTableIndexes($table)
	{
		$data = $this->getDb()->fetchColumn("SELECT data FROM import_datastore WHERE typename = ?", array('tableindexes.' . $table));
		$data = @unserialize($data);

		if (!$data || empty($data['sql'])) {
			return;
		}

		try {
			$this->getDb()->exec($data['sql']);
		} catch (\Exception $e) {
			$this->logger->log(sprintf("FK constraint failed so reexecuting with checks off: %s", $data['sql']), 'ERR');

			$this->getDb()->exec("SET FOREIGN_KEY_CHECKS = 0");
			$this->getDb()->exec($data['sql']);
			$this->getDb()->exec("SET FOREIGN_KEY_CHECKS = 1");
		}
	}


	/**
	 * @param $table
	 * @return bool
	 */
	public function doesOldTableExist($table)
	{
		if (isset($this->table_exists[$table])) {
			return $this->table_exists[$table];
		}

		$x = $this->getOldDb()->fetchColumn("show tables like '{$table}'");
		if ($x) {
			$this->table_exists[$table] = true;
		} else {
			$this->table_exists[$table] = false;
		}

		return $this->table_exists[$table];
	}


	public function getStepTitle($step)
	{
		$class = 'Application\\DeskPRO\\Import\\Importer\\Step\\Zendesk\\' . $this->steps[$step-1] . 'Step';
		return $class::getTitle();
	}

	/**
	 * @return \Orb\Service\Zendesk\Zendesk
	 */
	public function getZd()
	{
		return $this->zd;
	}
}
