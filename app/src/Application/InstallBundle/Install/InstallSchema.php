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
 * @subpackage InstallBundle
 */

namespace Application\InstallBundle\Install;

use Application\DeskPRO\DBAL\Connection;
use Application\DeskPRO\App;
use Orb\Log\Logger;

class InstallSchema
{
	/**
	 * Plain database connection for raw queries
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	protected $db;

	/**
	 * @param array $schema
	 */
	protected $schema;

	/**
	 * @var \Application\DeskPRO\Log\Logger
	 */
	protected $logger = null;

	/**
	 * @var string
	 */
	protected $build = 'default';

	protected $done_steps = null;

	/**
	 * @param \Application\DeskPRO\DBAL\Connection $db
	 * @param array $schema
	 */
	public function __construct($db, array $schema = null, $build = 'default')
	{
		$this->db = $db;

		// Generate now dynamically (dev tool)
		if ($schema === null) {
			$sc = new \Application\InstallBundle\Data\GenerateSchema(App::getOrm());
			$schema = array(
				'create' => $sc->getCreates(),
				'alter' => $sc->getAlters(),
				'trigger' => $sc->getTriggers()
			);
		}

		$this->schema = $schema;
		$this->build = $build;
	}


	/**
	 * @param \Application\DeskPRO\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}

	protected function getLogger()
	{
		if ($this->logger === null) {
			$this->logger = new \Orb\Log\Logger();
		}

		return $this->logger;
	}

	public function countQueries()
	{
		return count($this->schema['create']) + count($this->schema['alter']) + count($this->schema['trigger']);
	}

	public function hasDoneStep($id)
	{

		if ($this->done_steps === null) {
			$this->done_steps = $this->db->fetchAllKeyValue("SELECT name, data FROM install_data WHERE build = ? AND name LIKE 'buildstep_%'", array($this->build));
		}

		if (isset($this->done_steps['buildstep_' . $id])) {
			return true;
		}

		return false;
	}

	public function markStepDone($id)
	{
		$this->db->insert('install_data', array('build' => $this->build, 'name' => 'buildstep_' . $id, 'data' => 1));
		if (!is_array($this->done_steps)) {
			$this->hasDoneStep($id);
		}
		$this->done_steps['buildstep_' . $id] = 1;
	}

	/**
	 * Run through all the queries
	 *
	 * @param bool $halt_on_error True to stop and throw an exception when an error is encountered.
	 * @return bool True on success, false on error
	 */
	public function run($halt_on_error = true, $limit = 1000000, $skip = 0, $callback = null)
	{
		$has_error = false;

		$s_time = microtime(true);
		$this->getLogger()->log("InstallSchema::run started " . sprintf("%.f", $s_time), Logger::DEBUG);

		if (!$this->schema['create']) $this->schema['create'] = array();
		if (!$this->schema['alter']) $this->schema['alter'] = array();

		if ($limit) {
			foreach ($this->schema['create'] as $k => $sql) {

				if ($skip) {
					$skip--;
					continue;
				}

				$step_id = "query_table_$k";
				if ($this->hasDoneStep($step_id)) {
					$this->getLogger()->log("[QUERY:TABLE:$k] SKIPPED $sql", Logger::DEBUG, array('skipped' => true));
					if ($callback) $callback('table', 'skip', $sql, $k);
					$limit--;
					if (!$limit) {
						break;
					}
					continue;
				}

				$this->getLogger()->log("[QUERY:TABLE:$k] $sql", Logger::DEBUG, array('sql' => $sql));

				try {
					$time_start = microtime(true);
					$this->db->exec($sql);
					$time_end = microtime(true);

					$this->markStepDone($step_id);
					if ($callback) $callback('table', 'done', $sql, $k, $time_end-$time_start);
				} catch (\Exception $e) {
					$has_error = true;
					if (strlen($sql) > 30) {
						$sub = substr($sql, 0, 30) . '...';
					} else {
						$sub = $sql;
					}
					$this->getLogger()->log("[QUERY:TABLE:$k] FAILED: {$e->getMessage()} in query: $sub", Logger::CRIT, array('type' => 'alter', 'sql' => $sql, 'exception' => $e));
					if ($callback) $callback('table', 'error', $sql, $k, $e->getCode() . ' ' . $e->getMessage());
					if ($halt_on_error) {
						throw $e;
					}
				}

				$limit--;
				if (!$limit) {
					break;
				}
			}
		}

		if ($limit) {
			foreach ($this->schema['alter'] as $k => $sql) {
				if ($skip) {
					$skip--;
					continue;
				}

				$step_id = "query_alter_$k";
				if ($this->hasDoneStep($step_id)) {
					$this->getLogger()->log("[QUERY:ALTER:$k] SKIPPED $sql", Logger::DEBUG, array('skipped' => true));
					if ($callback) $callback('alter', 'skip', $sql, $k);
					$limit--;
					if (!$limit) {
						break;
					}
					continue;
				}

				$this->getLogger()->log("[QUERY:ALTER:$k] $sql", Logger::DEBUG, array('sql' => $sql));

				try {
					$time_start = microtime(true);
					$this->db->exec($sql);
					$time_end = microtime(true);

					$this->markStepDone($step_id);
					if ($callback) $callback('alter', 'done', $sql, $k, $time_end-$time_start);
				} catch (\Exception $e) {
					$has_error = true;
					if (strlen($sql) > 30) {
						$sub = substr($sql, 0, 30) . '...';
					} else {
						$sub = $sql;
					}
					$this->getLogger()->log("[QUERY:ALTER:$k] FAILED: {$e->getMessage()} in query: $sub", Logger::CRIT, array('type' => 'alter', 'sql' => $sql, 'exception' => $e));
					if ($callback) $callback('alter', 'fail', $sql, $k, $e->getCode() . ' ' . $e->getMessage());
					if ($halt_on_error) {
						throw $e;
					}
				}

				$limit--;
				if (!$limit) {
					break;
				}
			}
		}

		if ($limit && isset($this->schema['trigger'])) {
			foreach ($this->schema['trigger'] as $k => $sql) {
				if ($skip) {
					$skip--;
					continue;
				}

				$step_id = "query_triger_$k";
				if ($this->hasDoneStep($step_id)) {
					$this->getLogger()->log("[QUERY:TRIGGER:$k] SKIPPED $sql", Logger::DEBUG, array('skipped' => true));
					if ($callback) $callback('trigger', 'skip', $sql, $k);
					$limit--;
					if (!$limit) {
						break;
					}
					continue;
				}

				$this->getLogger()->log("[QUERY:TRIGGER:$k] $sql", Logger::DEBUG, array('sql' => $sql));

				try {
					$this->db->exec($sql);
					$this->markStepDone($step_id);
					if ($callback) $callback('trigger', 'done', $sql, $k);
				} catch (\Exception $e) {
					$has_error = true;
					if (strlen($sql) > 30) {
						$sub = substr($sql, 0, 30) . '...';
					} else {
						$sub = $sql;
					}
					$this->getLogger()->log("[QUERY:TRIGGER:$k] FAILED: {$e->getMessage()} in query: $sub", Logger::CRIT, array('type' => 'alter', 'sql' => $sql, 'exception' => $e));
					if ($callback) $callback('trigger', 'error', $sql, $k, $e->getCode() . ' ' . $e->getMessage());
					if ($halt_on_error) {
						throw $e;
					}
				}

				$limit--;
				if (!$limit) {
					break;
				}
			}
		}

		$e_time = microtime(true);
		$this->getLogger()->log("InstallSchema::run finished " . sprintf("%.f (took %.fs)", $e_time, $e_time - $s_time), Logger::DEBUG);

		if ($has_error) {
			return false;
		}

		return true;
	}
}
