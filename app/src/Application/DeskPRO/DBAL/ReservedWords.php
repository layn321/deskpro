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
 * @subpackage
 */

namespace Application\DeskPRO\DBAL;

use Doctrine\DBAL\Schema\AbstractSchemaManager;

/**
 * Checks a database for tables that use fields named after reserved words
 */
class ReservedWords
{
	public static $reserved_words = array(
		'ACCESSIBLE' => 1, 'ADD' => 1, 'ALL' => 1, 'ALTER' => 1, 'ANALYZE' => 1, 'AND' => 1, 'AS' => 1, 'ASC' => 1, 'ASENSITIVE' => 1, 'BEFORE' => 1, 'BETWEEN' => 1, 'BIGINT' => 1,
		'BINARY' => 1, 'BLOB' => 1, 'BOTH' => 1, 'BY' => 1, 'CALL' => 1, 'CASCADE' => 1, 'CASE' => 1, 'CHANGE' => 1, 'CHAR' => 1, 'CHARACTER' => 1, 'CHECK' => 1, 'COLLATE' => 1,
		'COLUMN' => 1, 'CONDITION' => 1, 'CONSTRAINT' => 1, 'CONTINUE' => 1, 'CONVERT' => 1, 'CREATE' => 1, 'CROSS' => 1, 'CURRENT_DATE' => 1, 'CURRENT_TIME' => 1,
		'CURRENT_TIMESTAMP' => 1, 'CURRENT_USER' => 1, 'CURSOR' => 1, 'DATABASE' => 1, 'DATABASES' => 1, 'DAY_HOUR' => 1, 'DAY_MICROSECOND' => 1,
		'DAY_MINUTE' => 1, 'DAY_SECOND' => 1, 'DEC' => 1, 'DECIMAL' => 1, 'DECLARE' => 1, 'DEFAULT' => 1, 'DELAYED' => 1, 'DELETE' => 1, 'DESC' => 1, 'DESCRIBE' => 1,
		'DETERMINISTIC' => 1, 'DISTINCT' => 1, 'DISTINCTROW' => 1, 'DIV' => 1, 'DOUBLE' => 1, 'DROP' => 1, 'DUAL' => 1, 'EACH' => 1, 'ELSE' => 1, 'ELSEIF' => 1,
		'ENCLOSED' => 1, 'ESCAPED' => 1, 'EXISTS' => 1, 'EXIT' => 1, 'EXPLAIN' => 1, 'FALSE' => 1, 'FETCH' => 1, 'FLOAT' => 1, 'FLOAT4' => 1, 'FLOAT8' => 1, 'FOR' => 1,
		'FORCE' => 1, 'FOREIGN' => 1, 'FROM' => 1, 'FULLTEXT' => 1, 'GRANT' => 1, 'GROUP' => 1, 'HAVING' => 1, 'HIGH_PRIORITY' => 1, 'HOUR_MICROSECOND' => 1,
		'HOUR_MINUTE' => 1, 'HOUR_SECOND' => 1, 'IF' => 1, 'IGNORE' => 1, 'IN' => 1, 'INDEX' => 1, 'INFILE' => 1, 'INNER' => 1, 'INOUT' => 1, 'INSENSITIVE' => 1,
		'INSERT' => 1, 'INT' => 1, 'INT1' => 1, 'INT2' => 1, 'INT3' => 1, 'INT4' => 1, 'INT8' => 1, 'INTEGER' => 1, 'INTERVAL' => 1, 'INTO' => 1, 'IS' => 1, 'ITERATE' => 1, 'JOIN' => 1,
		'KEY' => 1, 'KEYS' => 1, 'KILL' => 1, 'LEADING' => 1, 'LEAVE' => 1, 'LEFT' => 1, 'LIKE' => 1, 'LIMIT' => 1, 'LINEAR' => 1, 'LINES' => 1, 'LOAD' => 1, 'LOCALTIME' => 1,
		'LOCALTIMESTAMP' => 1, 'LOCK' => 1, 'LONG' => 1, 'LONGBLOB' => 1, 'LONGTEXT' => 1, 'LOOP' => 1, 'LOW_PRIORITY' => 1,
		'MASTER_SSL_VERIFY_SERVER_CERT' => 1, 'MATCH' => 1, 'MAXVALUE' => 1, 'MEDIUMBLOB' => 1, 'MEDIUMINT' => 1, 'MEDIUMTEXT' => 1, 'MIDDLEINT' => 1,
		'MINUTE_MICROSECOND' => 1, 'MINUTE_SECOND' => 1, 'MOD' => 1, 'MODIFIES' => 1, 'NATURAL' => 1, 'NOT' => 1, 'NO_WRITE_TO_BINLOG' => 1, 'NULL' => 1,
		'NUMERIC' => 1, 'ON' => 1, 'OPTIMIZE' => 1, 'OPTION' => 1, 'OPTIONALLY' => 1, 'OR' => 1, 'ORDER' => 1, 'OUT' => 1, 'OUTER' => 1, 'OUTFILE' => 1, 'PRECISION' => 1,
		'PRIMARY' => 1, 'PROCEDURE' => 1, 'PURGE' => 1, 'RANGE' => 1, 'READ' => 1, 'READS' => 1, 'READ_WRITE' => 1, 'REAL' => 1, 'REFERENCES' => 1, 'REGEXP' => 1,
		'RELEASE' => 1, 'RENAME' => 1, 'REPEAT' => 1, 'REPLACE' => 1, 'REQUIRE' => 1, 'RESIGNAL' => 1, 'RESTRICT' => 1, 'RETURN' => 1, 'REVOKE' => 1, 'RIGHT' => 1,
		'RLIKE' => 1, 'SCHEMA' => 1, 'SCHEMAS' => 1, 'SECOND_MICROSECOND' => 1, 'SELECT' => 1, 'SENSITIVE' => 1, 'SEPARATOR' => 1, 'SET' => 1, 'SHOW' => 1, 'SIGNAL' => 1,
		'SMALLINT' => 1, 'SPATIAL' => 1, 'SPECIFIC' => 1, 'SQL' => 1, 'SQLEXCEPTION' => 1, 'SQLSTATE' => 1, 'SQLWARNING' => 1, 'SQL_BIG_RESULT' => 1,
		'SQL_CALC_FOUND_ROWS' => 1, 'SQL_SMALL_RESULT' => 1, 'SSL' => 1, 'STARTING' => 1, 'STRAIGHT_JOIN' => 1, 'TABLE' => 1, 'TERMINATED' => 1, 'THEN' => 1,
		'TINYBLOB' => 1, 'TINYINT' => 1, 'TINYTEXT' => 1, 'TO' => 1, 'TRAILING' => 1, 'TRIGGER' => 1, 'TRUE' => 1, 'UNDO' => 1, 'UNION' => 1, 'UNIQUE' => 1, 'UNLOCK' => 1,
		'UNSIGNED' => 1, 'UPDATE' => 1, 'USAGE' => 1, 'USE' => 1, 'USING' => 1, 'UTC_DATE' => 1, 'UTC_TIME' => 1, 'UTC_TIMESTAMP' => 1, 'VALUES' => 1, 'VARBINARY' => 1,
		'VARCHAR' => 1, 'VARCHARACTER' => 1, 'VARYING' => 1, 'WHEN' => 1, 'WHERE' => 1, 'WHILE' => 1, 'WITH' => 1, 'WRITE' => 1, 'XOR' => 1, 'YEAR_MONTH' => 1,
		'ZEROFILL' => 1, 'GENERAL' => 1, 'IGNORE_SERVER_IDS' => 1, 'MASTER_HEARTBEAT_PERIOD' => 1, 'SLOW' => 1
	);

	/**
	 * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	protected $schema;

	/**
	 * @var array
	 */
	protected $bad_tables = null;


	/**
	 * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $schema
	 */
	public function __construct(AbstractSchemaManager $schema)
	{
		$this->schema = $schema;
	}


	protected function _load()
	{
		if ($this->bad_tables !== null) {
			return;
		}

		$this->bad_tables = array();

		$tables = $this->schema->listTableNames();

		foreach ($tables as $t) {
			$cols = $this->schema->listTableColumns($t);

			foreach ($cols as $c) {
				$c = $c->getName();
				$check = strtoupper($c);
				if (isset(self::$reserved_words[$check])) {
					if (!isset($this->bad_tables[$t])) {
						$this->bad_tables[$t] = array();
					}
					$this->bad_tables[$t][] = $c;
				}
			}
		}
	}


	/**
	 * Gets an array of tables that have fields named after reserved words.
	 *
	 * @return array
	 */
	public function getBadTables()
	{
		$this->_load();
		return $this->bad_tables;
	}
}