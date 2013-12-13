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

namespace Application\InstallBundle\Data;

use Doctrine\ORM\EntityManager;

class GenerateSchema
{
	/**
	 * @var array
	 */
	protected $creates;

	/**
	 * @var array
	 */
	protected $alters;

	/**
	 * @var array
	 */
	protected $triggers;

	/**
	 * @var array
	 */
	protected $indexes;

	/**
	 * @var array
	 */
	protected $fks;

	/**
	 * @var string
	 */
	protected $php_file;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}


	/**
	 * @return array
	 */
	public function getCreates()
	{
		$this->load();
		return $this->creates;
	}


	/**
	 * @return array
	 */
	public function getAlters()
	{
		$this->load();
		return $this->alters;
	}


	/**
	 * @return array
	 */
	public function getTriggers()
	{
		$this->load();
		return $this->triggers;
	}


	/**
	 * @return string
	 */
	public function getPhpFile()
	{
		$this->load();
		return $this->php_file;
	}


	/**
	 * Loads the schema
	 */
	protected function load()
	{
		if ($this->creates !== null) {
			return;
		}

		$this->creates = array();
		$this->alters = array();

		#------------------------------
		# Load SQL
		#------------------------------

		$em = $this->em;
		/** @var $metadata \Doctrine\ORM\Mapping\ClassMetadata[] */
		$metadata = $em->getMetadataFactory()->getAllMetadata();
		$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
		$sm = $this->em->getConnection()->getSchemaManager();
		$all_sql = $tool->getCreateSchemaSql($metadata);

		#------------------------------
		# Non-entity tables
		#------------------------------

		$all_sql[] = <<<SQL
CREATE TABLE `ref_reserve` (
  `obj_type` varchar(50) NOT NULL,
  `ref` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`obj_type`,`ref`)
) ENGINE=MyISAM
SQL;

		$all_sql[] = <<<SQL
CREATE TABLE `content_search` (
  `object_type` varchar(15) NOT NULL DEFAULT '',
  `object_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`object_type`,`object_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM
SQL;

		$all_sql[] = <<<SQL
CREATE TABLE `tickets_search_active` (
  `id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `priority_id` int(11) DEFAULT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `person_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `agent_team_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `email_gateway_id` int(11) DEFAULT NULL,
  `creation_system` varchar(20) NOT NULL,
  `status` varchar(30) NOT NULL,
  `urgency` int(11) NOT NULL,
  `is_hold` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_resolved` datetime DEFAULT NULL,
  `date_first_agent_reply` datetime DEFAULT NULL,
  `date_last_agent_reply` datetime DEFAULT NULL,
  `date_last_user_reply` datetime DEFAULT NULL,
  `date_agent_waiting` datetime DEFAULT NULL,
  `date_user_waiting` datetime DEFAULT NULL,
  `total_user_waiting` int(11) NOT NULL,
  `total_to_first_reply` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `person_id` (`person_id`)
) ENGINE=MyISAM
SQL;

		$all_sql[] = <<<SQL
CREATE TABLE `tickets_search_subject` (
  `id` int(11) NOT NULL,
  `subject` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM
SQL;

		$all_sql[] = <<<SQL
CREATE TABLE `tickets_search_message` (
  `id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `priority_id` int(11) DEFAULT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `person_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `agent_team_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `email_gateway_id` int(11) DEFAULT NULL,
  `creation_system` varchar(20) NOT NULL,
  `status` varchar(30) NOT NULL,
  `urgency` int(11) NOT NULL,
  `is_hold` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_resolved` datetime DEFAULT NULL,
  `date_first_agent_reply` datetime DEFAULT NULL,
  `date_last_agent_reply` datetime DEFAULT NULL,
  `date_last_user_reply` datetime DEFAULT NULL,
  `date_agent_waiting` datetime DEFAULT NULL,
  `date_user_waiting` datetime DEFAULT NULL,
  `total_user_waiting` int(11) NOT NULL,
  `total_to_first_reply` int(11) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `person_id` (`person_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM
SQL;

		$all_sql[] = <<<SQL
CREATE TABLE `tickets_search_message_active` (
  `id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `priority_id` int(11) DEFAULT NULL,
  `workflow_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `person_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `agent_team_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `email_gateway_id` int(11) DEFAULT NULL,
  `creation_system` varchar(20) NOT NULL,
  `status` varchar(30) NOT NULL,
  `urgency` int(11) NOT NULL,
  `is_hold` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_resolved` datetime DEFAULT NULL,
  `date_first_agent_reply` datetime DEFAULT NULL,
  `date_last_agent_reply` datetime DEFAULT NULL,
  `date_last_user_reply` datetime DEFAULT NULL,
  `date_agent_waiting` datetime DEFAULT NULL,
  `date_user_waiting` datetime DEFAULT NULL,
  `total_user_waiting` int(11) NOT NULL,
  `total_to_first_reply` int(11) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `person_id` (`person_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM
SQL;

		#------------------------------
		# Organise it
		#------------------------------

		$xa = 0;
		$xc = 0;
		$xt = 0;

		$tables = array();
		$php_creates  = array();
		$php_alters   = array();
		$php_triggers = array();



		foreach ($all_sql as $s) {
			$s = trim($s);

			// Trigger
			if (preg_match('#^CREATE TRIGGER#', $s)) {

				$s_ex = var_export($s, true);

				$this->triggers[] = $s;
				$php_triggers[] = "\$queries['trigger'][$xt] = $s_ex;";
				$xt++;

			// Alter
			} elseif (preg_match('#^ALTER#', $s)) {
				$s = str_replace(array("\r\n", "\n"), ' ', $s);
				$this->alters[] = $s;

			// Create
			} else {

				$s = str_replace(array("\r\n", "\n"), ' ', $s);
				$s .= ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

				if (strpos($s, 'CREATE TABLE person2usergroups') !== false) {
					$s = str_replace('INDEX IDX_356C969E217BBB47 (person_id), ', '', $s);
				}

				$s_ex = var_export($s, true);

				$this->creates[] = $s;
				$php_creates[] = "\$queries['create'][$xc] = $s_ex;";
				$xc++;
			}
		}

		$this->alters = self::combineAlters($this->alters);

		foreach ($this->alters as $s) {
			$s_ex = var_export($s, true);
			$php_alters[] = "\$queries['alter'][$xa] = $s_ex;";
			$xa++;
		}

		#------------------------------
		# Indexes and keys
		#------------------------------

		$this->indexes = array();
		$this->fks = array();
		$php_indexes = array();
		$php_fks = array();

		$schema = $tool->getSchemaFromMetadata($metadata);
		/** @var $tables \Doctrine\DBAL\Schema\Table[] */
		$tables = $schema->getTables();
		foreach ($tables as $table) {
			$t = $table->getName();

			$this->indexes[$t] = array();
			$this->fks[$t] = array();

			$indexes = $table->getIndexes();
			$fkeys = $table->getForeignKeys();

			if (count($indexes) > 0) {
				$php_indexes[] = "\$queries['index']['$t'] = array(";
			}
			if (count($fkeys) > 0) {
				$php_fks[] = "\$queries['fk']['$t'] = array(";
			}

			foreach ($indexes as $idx) {
				$sql = $sm->getDatabasePlatform()->getCreateIndexSQL($idx, $t);
				$this->indexes[$t][$idx->getName()] = $sql;

				$php_indexes[] = "\t'{$idx->getName()}' => '" . addslashes($sql) . "',";
			}
			foreach ($fkeys as $fk) {
				$sql = $sm->getDatabasePlatform()->getCreateForeignKeySQL($fk, $t);
				$this->fks[$t][$fk->getName()] = $sql;

				$php_fks[] = "\t'{$fk->getName()}' => '" . addslashes($sql) . "',";
			}

			if (count($indexes) > 0) {
				$php_indexes[] = ");";
			}
			if (count($fkeys) > 0) {
				$php_fks[] = ");";
			}
		}


		#------------------------------
		# Create the PHP file
		#------------------------------

		$php = "<?php\n\n\$queries = array('create' => array(), 'alter' => array(), 'index' => array(), 'fk' => array(), 'trigger' => array());\n\n";
		$php .= implode("\n", $php_creates);
		$php .= "\n\n\n\n\n";
		$php .= implode("\n", $php_alters);
		$php .= "\n\n\n\n\n";
		$php .= implode("\n", $php_triggers);
		$php .= "\n\n\n\n\n";
		$php .= implode("\n", $php_indexes);
		$php .= "\n\n\n\n\n";
		$php .= implode("\n", $php_fks);
		$php .= "\n\n\n\n\nreturn \$queries;\n";

		$pos = strpos($php, 'CREATE TABLE client_messages');
		if ($pos) {
			$pos = strpos($php, 'ENGINE = InnoDB', $pos);
			if ($pos) {
				$php = str_split($php, $pos);
				$php[0] .= ' AUTO_INCREMENT=2 ';
				$php = implode('', $php);
			}
		}

		$this->php_file = $php;
	}


	/**
	 * Takes an array of ALTER queries and combines any alters that alter the same table.
	 * For example, instead of 10 separate ALTER TABLE queries that add 10 separate FK's, there's only one.
	 *
	 * @param array $alters
	 */
	public static function combineAlters(array $alters)
	{
		$segments = array();

		foreach ($alters as $sql) {
			$sql = str_replace(array("\r\n", "\n", ' '), ' ', $sql);
			$sql = trim($sql, ' ;');

			$m = null;
			if (!preg_match('#^ALTER +TABLE +`?(.*?)`? (.*?)$#', $sql, $m)) {
				throw new \InvalidArgumentException("Invalid ALTER query: $sql");
			}

			$table = $m[1];
			$alter_seg = trim($m[2], ' ,');

			if (!isset($segments[$table])) {
				$segments[$table] = array();
			}

			$segments[$table][] = $alter_seg;
		}

		$return = array();
		foreach ($segments as $table => $segs) {
			$return[] = "ALTER TABLE " . $table . " " . implode(', ', $segs);
		}

		return $return;
	}
}
