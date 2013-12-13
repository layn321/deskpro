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

use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Orb\Log\Logger;
use Orb\Util\Arrays;

/**
 * We have importers for each platform, and each Importer has a number of Steps.
 *
 * - Importer: A specific platform that we're importing stuff from. For example, DeskPRO v3.
 * - Steps: To import objects from the platform into the local DeskPRO is done through one or more steps.
 */
abstract class AbstractImporter
{
	/**
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	public $container;

	/**
	 * @var \Orb\Util\OptionsArray
	 */
	public $config;

	/**
	 * @var \Orb\Log\Logger
	 */
	public $logger;

	/**
	 * @var array
	 */
	public $cached_maps = null;

	/**
	 * @var callback
	 */
	public $update_status_fn;

	/**
	 * @var \Application\DeskPRO\DBAL\Connection
	 */
	public $db;

	/**
	 * @var array
	 */
	public $schema_file;

	/**
	 * @var array
	 */
	public $buffered_save_mapped_ids = array();

	/**
	 * @var int
	 */
	public $archive_days = null;

	/**
	 * Which maps to cache totally
	 *
	 * @var array
	 */
	protected $cache_map_types = array(
		'ticket_category'    => true,
		'ticket_workflow'    => true,
		'ticket_priority'    => true,
		'company'            => true,
		'tech'               => true,
		'tech_email'         => true,
		'ticket_def_choice'  => true,
		'people_def_choice'  => true,
		'usergroup'          => true,
		'usergroup_sys'      => true,
		'chat_dep'           => true,
		'people_def'         => true,
		'ticket_def'         => true,
	);

	public function __construct(DeskproContainer $container, $config, Logger $logger = null)
	{
		if (is_array($config)) {
			$config = new \Orb\Util\OptionsArray($config);
		}

		$this->container = $container;
		$this->config = $config;

		if (!$logger) {
			$logger = new \Application\DeskPRO\Log\Logger();
			$logger->addWriter(new \Orb\Log\Writer\Output());
		}

		$this->logger = $logger;

		$this->db = $this->db;

		if (file_exists(DP_ROOT.'/src/Application/InstallBundle/Data/schema.php')) {
			$this->schema_file = include(DP_ROOT.'/src/Application/InstallBundle/Data/schema.php');
		}
	}


	/**
	 * Returns true when we can detect a large database and can link to the kb article
	 *
	 * @return bool
	 */
	public function isLargeDatabase()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function isArchiveEnabled()
	{
		if ($this->archive_days === null) {
			$settings = $this->db->fetchAllKeyValue("SELECT name, value FROM settings WHERE name IN ('core_tickets.use_archive', 'core_tickets.auto_archive_time')");
			if (
				!empty($settings['core_tickets.use_archive'])
				&& $settings['core_tickets.use_archive']
				&& !empty($settings['core_tickets.auto_archive_time'])
				&& $settings['core_tickets.auto_archive_time']
			) {
				$this->archive_days = $settings['core_tickets.auto_archive_time'];
			} else {
				$this->archive_days = 0;
			}
		}

		return (bool)$this->archive_days;
	}

	/**
	 * @return int
	 */
	public function getArchiveTime()
	{
		if (!$this->isArchiveEnabled()) {
			return 0;
		}

		return $this->archive_days;
	}


	/**
	 * Called before initalizing. Use this to check $config to ensure
	 * everything is alright.
	 *
	 * @return array An array of error messages, if any
	 */
	abstract public function validateOptions();


	/**
	 * Called before the first step to initialize anything.
	 */
	abstract public function setupImport($mode = 'run');


	/**
	 * Called after all steps are finished to cleanup anything.
	 */
	abstract public function cleanupImport();


	/**
	 * The number of steps this importer has.
	 *
	 * @return int
	 */
	abstract public function countSteps();


	/**
	 * Get a step
	 *
	 * @param int $step
	 * @return \Application\DeskPRO\Import\Importer\Step\AbstractStep
	 */
	abstract public function getStep($step);

	/**
	 * Get a step title
	 *
	 * @param int $step
	 * @return string
	 */
	abstract public function getStepTitle($step);


	/**
	 * Called before a step is run
	 *
	 * @param $step
	 */
	public function preRunStep($step)
	{

	}


	/**
	 * Called after a step is run
	 *
	 * @param $step
	 */
	public function postRunStep($step)
	{

	}


	/**
	 * @param int $step
	 * @return bool
	 */
	public function hasStep($step)
	{
		return ($step <= $this->countSteps());
	}


	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}


	/**
	 * @param $message
	 */
	public function logMessage($message)
	{
		$this->logger->log($message, 'INFO');
	}


	/**
	 * @return string $name Get a value for $name fom config, otherwise get the config object itself
	 * @return \Orb\Util\OptionsArray
	 */
	public function getConfig($name = null)
	{
		if ($name !== null) {
			return $this->config->get($name);
		}

		return $this->config;
	}


	/**
	 * @return \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	public function getContainer()
	{
		return $this->container;
	}


	/**
	 * The ID of the importer
	 *
	 * @return string
	 */
	public function getId()
	{
		$basename = \Orb\Util\Util::getBaseClassname($this);
		return strtolower(str_replace('Importer', '', $basename));
	}


	/**
	 * @return \Application\DeskPRO\DBAL\Connection
	 */
	public function getDb()
	{
		return $this->db;
	}


	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEm()
	{
		return $this->container->getEm();
	}


	/**
	 * Save an ID mapping
	 *
	 * @param $type
	 * @param $old_id
	 * @param $new_id
	 */
	public function saveMappedId($type, $old_id, $new_id, $buffer = false)
	{
		$values = array(
			'typename' => $type,
			'old_id' => $old_id,
			'new_id' => $new_id
		);

		if ($buffer) {
			$this->buffered_save_mapped_ids[] = $values;

			if (count($this->buffered_save_mapped_ids) > 2000) {
				$this->flushSaveMappedIdBuffer();
			}
		} else {
			$id = $this->db->replace('import_map', $values);
		}

		if (!isset($this->cached_maps[$type])) {
			$this->cached_maps[$type] = array();
		}
		$this->cached_maps[$type][$old_id] = $new_id;
	}


	/**
	 * Sends all of the mapped ids to the import_map table
	 */
	public function flushSaveMappedIdBuffer()
	{
		if (!$this->buffered_save_mapped_ids) {
			return;
		}

		$sql = 'INSERT INTO import_map (typename, old_id, new_id) VALUES ';
		$sql_parts = array();

		// Make sure theres no blanks
		$this->buffered_save_mapped_ids = Arrays::removeEmptyArray($this->buffered_save_mapped_ids);

		foreach ($this->buffered_save_mapped_ids as $vals) {
			$quoted = array();

			foreach ($vals as $v) {
				if (is_null($v)) {
					$quoted[] = 'NULL';
				} elseif (\Orb\Util\Numbers::isInteger($v)) {
					$quoted[] = $v;
				} else {
					$quoted[] = $this->db->quote($v);
				}
			}

			$sql_parts[] = '(' . implode(',', $quoted) . ')';
		}

		$sql .= implode(',', $sql_parts);
		$sql .= " ON DUPLICATE KEY UPDATE new_id = VALUES(new_id) ";
		$sql .= '/*DP_QLOG_NOLOG*/';

		$this->db->exec($sql);

		$this->buffered_save_mapped_ids = array();
	}


	/**
	 * Get the new Id by looking up the old one
	 *
	 * @param $type
	 * @param $old_id
	 * @return mixed
	 */
	public function getMappedNewId($type, $old_id)
	{
		$cache = false;
		if (isset($this->cache_map_types[$type])) {
			if (!$this->cached_maps) {
				$data = $this->db->fetchAll("
					SELECT typename, old_id, new_id
					FROM import_map
					WHERE typename IN ('" . implode("','", array_keys($this->cache_map_types)) . "')
					/*DP_QLOG_NOLOG*/
				");
				$this->cached_maps = array();
				foreach ($data as $d) {
					if (!isset($this->cached_maps[$d['typename']])) {
						$this->cached_maps[$d['typename']] = array();
					}
					$this->cached_maps[$d['typename']][$d['old_id']] = $d['new_id'];
				}
			}
			$cache = true;
			if (isset($this->cached_maps[$type]) && array_key_exists($old_id, $this->cached_maps[$type])) {
				return $this->cached_maps[$type][$old_id];
			}
		}

		$id = $this->db->fetchColumn("
			SELECT new_id
			FROM import_map
			WHERE typename = ? AND old_id = ?
			/*DP_QLOG_NOLOG*/
		", array($type, $old_id));

		if (!isset($this->cached_maps[$type])) {
			$this->cached_maps[$type] = array();
		}
		$this->cached_maps[$type][$old_id] = $id;

		return $id;
	}


	/**
	 * @param string $type
	 * @param array $old_ids
	 * @return array
	 */
	public function getMappedNewIdsArray($type, array $old_ids)
	{
		if (!$old_ids) {
			return array();
		}

		if (!isset($this->cached_maps[$type])) {
			$this->cached_maps[$type] = array();
		}

		$old_ids_str = implode(',', $old_ids);
		$q = $this->db->query("
			SELECT typename, old_id, new_id
			FROM import_map
			WHERE typename = '$type'
			AND old_id IN ($old_ids_str)
			/*DP_QLOG_NOLOG*/
		");
		$q->execute();

		$ret = array();
		while ($rec = $q->fetch(\PDO::FETCH_ASSOC)) {
			$old_id = $rec['old_id'];
			$id     = $rec['new_id'];

			$this->cached_maps[$type][$old_id] = $id;

			$ret[$old_id] = $id;
		}

		// Loop over all the ids again, unset ones are null
		foreach ($old_ids as $oid) {
			if (!isset($this->cached_maps[$type][$oid])) {
				$this->cached_maps[$type][$oid] = null;
			}
		}

		return $ret;
	}


	/**
	 * Get the old Id by looking up the new one
	 *
	 * @param $type
	 * @param $old_id
	 * @return mixed
	 */
	public function getMappedOldId($type, $new_id)
	{
		return $this->db->fetchColumn("
			SELECT old_id
			FROM import_map
			WHERE typename = ? AND new_id = ?
		", array($type, $new_id));
	}
}
