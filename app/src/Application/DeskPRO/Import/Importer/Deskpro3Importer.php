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

use Orb\Log\Logger;
use Application\DeskPRO\DBAL\Logging\QueryLogger;

class Deskpro3Importer extends AbstractImporter
{
	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	public $db;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	public $olddb;

	/**
	 * @var int
	 */
	public $time_begin = 0;

	/**
	 * @var string
	 */
	public $run_mode;

	/**
	 * @var array
	 */
	public $table_exists = array();

	protected $steps = array(
		'Prepare',
		'Settings',
		'Banning',
		'CustomFields',
		'TicketCategories',
		'UserChatDepartments',
		'TicketPriorities',
		'TicketWorkflows',
		'KbCats',
		'DownloadCats',
		'FeedbackCats',
		'Usergroups',
		'Companies',
		'PopAccounts',
		'Techs',
		'Usersources',
		'Users',
		'UserRules',
		'TechPms',
		'KbBlobs',
		'KbImageBlobs',
		'FileBlobs',
		'UserChatBlobs',
		'TicketBlobs',
		'Kb',
		'KbRelated',
		'KbSearchLog',
		'Downloads',
		'Feedback',
		'UserNews',
		'KbSearchIndex',
		'FeedbackSearchIndex',
		'NewsSearchIndex',
		'DownloadsSearchIndex',
		'UserChats',
		'ChatSnippets',
		'Tasks',
		'TechNews',
		'Tickets',
		'TicketsRerunCache',
		'TicketsRerun',
		'TicketDeleteLog',
		'TicketMerge',
		'TicketSnippets',
		'TechTimelog',
		'UserBills',
		'SaveLanguage',
		'BackupTroubles',
		'SaveDataMisc',
		'SaveDataEmailId',
		'SaveDataFaqSubs',
		'SaveDataManualComments',
		'SaveDataManualPages',
		'SaveDataManualRevisions',
		'SaveDataManualSearchLog',
		'SaveDataNotebookPage',
		'SaveDataBillingOrder',
		'SaveDataBillingTransaction',
		'SaveDataUserPlanSubs',
		'SaveNotebookBlobs',
		'CreateIndexes',
		'CleanupDone',
	);

	public function validateOptions()
	{
		$errors = array();

		foreach (array('db_host', 'db_user', 'db_password', 'db_name') as $k) {
			if (!$this->config->has($k)) {
				$errors[] = "Missing configuration value: import.$k";
			}
		}

		if (!$this->olddb) {
			try {
				$params = array(
					'driver'        => 'pdo_mysql',
					'host'          => $this->config->db_host,
					'user'          => $this->config->db_user,
					'password'      => $this->config->db_password,
					'dbname'        => $this->config->db_name,
					'names_charset' => 'latin1'
				);

				$m = null;
				if (isset($params['host']) && preg_match('#^(.*?):([0-9]+)$#', $params['host'], $m)) {
					$params['host'] = $m[1];
					$params['port'] = $m[2];
				}

				$this->olddb = $this->container->get('doctrine.dbal.connection_factory')->createConnection($params);
			} catch (\Exception $e) {
				$this->logMessage("-- FAILED");
				$errors[] = "Failed connecting to DeskPRO v3 database: {$e->getMessage()}";
			}
		}

		return $errors;
	}

	public function isLargeDatabase()
	{
		$count_users   = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM user");
		$count_tickets = $this->getOldDb()->fetchColumn("SELECT COUNT(*) FROM ticket");

		if ($count_users > 500000 || $count_tickets > 500000) {
			return true;
		}

		return false;
	}

	public function setupImport($mode = 'run')
	{
		gc_enable();
		$this->db = $this->container->getDb();
		$this->run_mode = $mode;
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
		$class = 'Application\\DeskPRO\\Import\\Importer\\Step\\Deskpro3\\' . $this->steps[$step-1] . 'Step';
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
		$formatter = new \Orb\Log\Filter\CallbackFormatter(function ($log_item) {
			/** @var $log_item \Orb\Log\LogItem */
			$log_item = $log_item;

			$extra = $log_item->getExtra();
			$queryinfo = $extra['queryinfo'];

			$mem = @memory_get_usage();
			$mem = \Orb\Util\Numbers::filesizeDisplay($mem);

			$log_item[\Orb\Log\LogItem::MESSAGE_LINE] = sprintf(
				"[%s time:%0.2fs mem:%s]\n\t%s\n\n\t%s\n\n\n",
				$queryinfo['query_typename'],
				$queryinfo['time_taken'],
				$mem,
				\DeskPRO\Kernel\KernelErrorHandler::varToString($queryinfo['params']),
				$queryinfo['sql']
			);

			return $log_item;
		});

		// For current database connection
		$qlog = new QueryLogger();
		$qlog->ignore_triggers[] = 'DP_QLOG_NOLOG';
		$qlog->tag = 'new_db';
		if ($this->config->get('enable_query_log')) {
			$logger = new \Orb\Log\Logger();
			$logger->addFilter($formatter);
			$logger->addWriter(new \Orb\Log\Writer\Stream($this->config->get('log_dir') . '/importer-db-sql.log', null, false));

			$qlog->setLogger($logger);

			if (!(isset($DP_CONFIG['import']['nolog']) && $DP_CONFIG['import']['nolog'])) {
				$qlog->addSlowLogRule(QueryLogger::TYPE_ALL, 0);
			}
		} else {
			$this->logger->addFilter(new \Application\InstallBundle\Logger\Filter\InstallQueryLogFormatter());
			$qlog->setLogger($this->logger);

			if (!(isset($DP_CONFIG['import']['nolog']) && $DP_CONFIG['import']['nolog'])) {
				$qlog->addSlowLogRule(QueryLogger::TYPE_ALL, 0);
			}
		}

		$this->qlog_db = $qlog;
		$this->db->getConfiguration()->setSQLLogger($qlog);

		// For olddb too
		$qlog = new QueryLogger();
		$qlog->ignore_triggers[] = 'DP_QLOG_NOLOG';
		$qlog->tag = 'olddb';
		if ($this->config->get('enable_query_log')) {
			$logger = new \Orb\Log\Logger();
			$logger->addFilter($formatter);
			$logger->addWriter(new \Orb\Log\Writer\Stream($this->config->get('log_dir') . '/importer-olddb-sql.log', null, false));

			$qlog->setLogger($logger);

			if (!(isset($DP_CONFIG['import']['nolog']) && $DP_CONFIG['import']['nolog'])) {
				$qlog->addSlowLogRule(QueryLogger::TYPE_ALL, 0);
			}
		} else {
			$this->logger->addFilter(new \Application\InstallBundle\Logger\Filter\InstallQueryLogFormatter());
			$qlog->setLogger($this->logger);

			if (!(isset($DP_CONFIG['import']['nolog']) && $DP_CONFIG['import']['nolog'])) {
				$qlog->addSlowLogRule(QueryLogger::TYPE_ALL, 0);
			}
		}

		$this->qlog_olddb = $qlog;
		$this->getOldDb()->getConfiguration()->setSQLLogger($qlog);

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
		$time_olddb = $this->qlog_olddb->total_time;

		$time_db_total  = $time_db + $time_olddb;
		$time_php_total = $time_total - $time_db_total;

		$total_queries = $this->qlog_db->query_count + $this->qlog_olddb->query_count;

		$mem = @memory_get_peak_usage();
		if (!$mem) {
			$mem = 0;
		}
		$mem = \Orb\Util\Numbers::filesizeDisplay($mem);

		$this->logMessage(sprintf("Time: %0.2f   PHP: %0.2f   DB: %0.2f (db %0.3f, olddb %0.3f)  Queries: %d,  Peak Mem: %s", $time_total, $time_php_total, $time_db_total, $time_db, $time_olddb, $total_queries, $mem));
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
		$class = 'Application\\DeskPRO\\Import\\Importer\\Step\\Deskpro3\\' . $this->steps[$step-1] . 'Step';
		return $class::getTitle();
	}

	/**
	 * @return \Application\DeskPRO\DBAL\Connection
	 */
	public function getOldDb()
	{
		return $this->olddb;
	}


	/**
	 * Fixes big 64bit ints that appear in serialized strings, but wont work when going
	 * back to 32bit systems. This just turns the ints into quoted strings.
	 */
	public static function unserialize_fix_32b_ints($str)
	{
		$matches = array();
		if (!preg_match_all('#i:(\d{10,});#', $str, $matches, PREG_SET_ORDER)) {
			return $str;
		}

		foreach ($matches as $match) {
			$str = str_replace($match[0], 's:' . strlen($match[1]) . ':"' . $match[1] . '";', $str);
		}

		return $str;
	}
}
