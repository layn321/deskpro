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
 * @subpackage Dpql
 */

namespace Application\DeskPRO\Dpql\Statement\Part;

use Application\DeskPRO\Dpql\Statement\Display;
use Application\DeskPRO\Dpql;
use Application\DeskPRO\App;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Application\DeskPRO\Dpql\Exception;
use Application\DeskPRO\Dpql\Renderer\Values\AbstractValues;
use Application\DeskPRO\Dpql\Renderer\AbstractRenderer;
use Application\DeskPRO\Dpql\Func\Link;

/**
 * Represents a reference to a column or association.
 */
class Column extends AbstractPart
{
	/**
	 * List of parts in the reference
	 *
	 * @var array
	 */
	public $parts;

	/**
	 * This is used when resolving direct association references to specific columns.
	 * Maps a table name to 2 values:
	 *  - 0: the unique ID field (usually a number)
	 *  - 1: the printable field (name, subject, etc)
	 *  - 2: the type of link (if linkable)
	 *
	 * @var array
	 */
	protected static $_tableResolver = array(
		'agent_teams' => array('id', 'name'),
		'departments' => array('id', 'title'),
		'feedback_categories' => array('id', 'title'),
		'feedback_status_categories' => array('id', 'title'),
		'labels_tickets' => array('label', 'label'),
		'languages' => array('id', 'title'),
		'organizations' => array('id', 'name', 'organization'),
		'people' => array('id', 'name', 'person'),
		'products' => array('id', 'title'),
		'slas' => array('id', 'title'),
		'tickets' => array('id', 'subject', 'ticket'),
		'ticket_categories' => array('id', 'title'),
		'ticket_priorities' => array('id', 'title'),
		'ticket_workflows' => array('id', 'title')
	);

	protected static $_autoLink = array(
		'tickets.id' => array('ticket')
	);

	protected static $_conditionResolver = array(
		'custom_data_article' => '%1$s.root_field_id = %2$s',
		'custom_data_feedback' => '%1$s.root_field_id = %2$s',
		'custom_data_organizations' => '%1$s.root_field_id = %2$s',
		'custom_data_person' => '%1$s.root_field_id = %2$s',
		'custom_data_ticket' => '%1$s.root_field_id = %2$s',
		'ticket_slas' => '%1$s.sla_id = %2$s',
	);

	/**
	 * @param array $parts
	 */
	public function __construct(array $parts)
	{
		$this->parts = $parts;
	}

	/**
	 * Prepares a part for use, including validating that the usage is valid.
	 *
	 * @param \Application\DeskPRO\Dpql\Statement\Display $statement
	 * @param string $section Name of the section usage is in (select, where, split, group, order)
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart[] $stack Parent parts
	 * @param \Application\DeskPRO\Dpql\SqlSelect $select Select being built up
	 * @param \Application\DeskPRO\Dpql\ResultHandler $result
	 *
	 * @throws \Application\DeskPRO\Dpql\Exception
	 *
	 * @return \Application\DeskPRO\Dpql\Statement\Part\Prepared|bool Prepared results or false if there's no output
	 */
	public function prepare(
		Display $statement, $section, array $stack, Dpql\SqlSelect $select, Dpql\ResultHandler $result
	)
	{
		$parts = $this->parts;
		$table = array_shift($parts);

		if (strtolower($table) != strtolower($statement->getFrom())) {
			throw new Exception("Invalid table name in column reference (received $table, expected {$statement->getFrom()}).");
		}

		if (!$parts) {
			throw new Exception('Missing column/join name in column reference.');
		}

		$sql = false;
		$printedSql = false;
		$name = false;
		$renderer = null;

		end($parts);
		$lastPartKey = key($parts);

		// represents the repository of what we're joining from
		$repository = $statement->getFromEntityRepository();
		$sqlTable = $repository->getTableName();

		$partsSoFar = array($table);
		$extraConditionValue = false;

		foreach ($parts AS $partKey => $part) {
			$partsSoFar[] = $part;
			$partsString = implode('.', $partsSoFar);

			if (preg_match('/\[(.+)\]$/', $part, $match)) {
				$extraConditionValue = $match[1];
				$part = substr($part, 0, -strlen($match[0]));
			} else {
				$extraConditionValue = false;
			}

			// are we referencing a field?
			foreach ($repository->getFieldMappings() AS $key => $field) {
				if (strtolower($key) == $part) {
					if (isset($field['dpqlAccess']) && !$field['dpqlAccess']) {
						throw new Exception("$partsString cannot be accessed via DPQL.");
					}

					if ($extraConditionValue !== false) {
						throw new Exception("$partsString contains an unexpected extra condition");
					}

					$sql = '`' . $sqlTable . '`.`' . $field['columnName'] . '`';

					if ($repository->getTableName() == 'tickets' && $field['columnName'] == 'total_user_waiting') {
						$sql = "($sql + IF(`$sqlTable`.date_user_waiting AND `$sqlTable`.status = 'awaiting_agent', UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`$sqlTable`.date_user_waiting), 0))";
					}

					switch ($field['type']) {
						case 'datetime':
							$tzOffsetSeconds = $statement->getTimezoneOffsetForFunction($stack);
							if ($tzOffsetSeconds) {
								$sql = "($sql + INTERVAL $tzOffsetSeconds SECOND)";
							}

							$renderer = 'datetime';
							break;

						case 'integer':
						case 'smallint':
						case 'bigint':
						case 'decimal':
						case 'float':
							$renderer = 'number';
							break;

						case 'date':
							$renderer = 'date';
							break;

						case 'time':
							$renderer = 'time';
							break;

						case 'boolean':
							$renderer = 'boolean';
							break;

						case 'string':
						case 'text':
							$renderer = 'string';
							break;
					}

					if ($renderer == 'number' && !empty($field['id'])) {
						$renderer = 'id';
					}

					$linkLookup = $repository->getTableName() . '.' . $part;

					if (isset(self::$_autoLink[$linkLookup])) {
						$lookup = self::$_autoLink[$linkLookup];

						if ($section == 'split') {
							$argSelect = array($statement->getSplitSql()->addSelectField($sql));
						} else {
							$argSelect = array($select->addSelectField($sql));
						}

						$renderer = function(AbstractValues $valueRenderer, $value, array $row, AbstractRenderer $renderer)
							use ($lookup, $argSelect)
						{
							return Link::formatLink($value, $lookup[0], $argSelect, $row, $valueRenderer, $renderer);
						};
					}

					$name = $part;
					break 2; // break $parts loop
				}
			}

			foreach ($repository->getAssociationMappings() AS $association) {
				if (empty($association['joinColumns'])) {
					// need to know how to make the join; ignore this
					continue;
				}

				foreach ($association['joinColumns'] AS $joinColumn) {
					// are we referencing a field that is only listed in an association?
					if (strtolower($joinColumn['name']) == $part) {
						if ($extraConditionValue !== false) {
							throw new Exception("$partsString contains an unexpected extra condition");
						}

						$sql = '`' . $sqlTable . '`.`' . $joinColumn['name'] . '`';
						$name = $part;
						break 3; // break $parts loop
					}
				}
			}

			foreach ($repository->getReportAssociations() AS $name => $association) {
				if (strtolower($name) == $part) {
					$target = $association['targetEntity'];
					$childRepository = $target::getRepository();

					if (!($childRepository instanceof \Application\DeskPRO\EntityRepository\AbstractEntityRepository)) {
						throw new Exception("$partsString cannot be accessed via DPQL.");
					}

					$childSqlTable = $childRepository->getTableName();
					$joinAlias = "{$sqlTable}_{$name}";
					$joinConditions = sprintf($association['conditions'], $joinAlias, $sqlTable);

					$select->addJoin(
						"$joinAlias",
						"LEFT JOIN `$childSqlTable` AS `$joinAlias` ON ($joinConditions)"
					);

					$repository = $childRepository; // now references come from this table
					$sqlTable = $joinAlias;

					continue 2; // continue $parts loop
				}
			}

			foreach ($repository->getAssociationMappings() AS $association) {
				// are we referencing an association?
				if (strtolower($association['fieldName']) == $part) {
					$target = $association['targetEntity'];
					$childRepository = $target::getRepository();

					if ((isset($association['dpqlAccess']) && !$association['dpqlAccess'])
						|| !($childRepository instanceof \Application\DeskPRO\EntityRepository\AbstractEntityRepository)
						|| $association['type'] == ClassMetadataInfo::MANY_TO_MANY
					) {
						throw new Exception("$partsString cannot be accessed via DPQL.");
					}

					$childSqlTable = $childRepository->getTableName();
					$joinAlias = "{$sqlTable}_{$association['fieldName']}";

					if ($extraConditionValue !== false) {
						if (!isset(self::$_conditionResolver[$childSqlTable])) {
							throw new Exception("$partsString contains an unexpected extra condition");
						}

						$joinAlias .= '_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $extraConditionValue);
					}

					if (!empty($association['joinColumns'])) {
						// join can be resolved directly
						$joinColumns = $association['joinColumns'];
						$sourceTable = $sqlTable;
						$joinTable = $joinAlias;
					} else {
						$childAssociations = $childRepository->getAssociationMappings();
						if (!empty($childAssociations[$association['mappedBy']]['joinColumns'])) {
							// join details are on the other table
							$joinColumns = $childAssociations[$association['mappedBy']]['joinColumns'];
							$sourceTable = $joinAlias;
							$joinTable = $sqlTable;
						} else {
							$joinColumns = array();
						}
					}

					if (!$joinColumns) {
						throw new Exception("$partsString cannot be accessed via DPQL.");
					}

					$joinConditions = array();
					foreach ($joinColumns AS $joinColumn) {
						$joinConditions[] =
							"`$sourceTable`.`$joinColumn[name]` = "
							. "`$joinTable`.`$joinColumn[referencedColumnName]`";
					}

					if ($extraConditionValue !== false) {
						$joinConditions[] = sprintf(
							self::$_conditionResolver[$childSqlTable], $joinAlias, App::getDb()->quote($extraConditionValue)
						);
					}

					$select->addJoin(
						"$joinAlias",
						"LEFT JOIN `$childSqlTable` AS `$joinAlias` ON (" . implode(' AND ', $joinConditions) . ")"
					);

					$repository = $childRepository; // now references come from this table
					$sqlTable = $joinAlias;

					continue 2; // continue $parts loop
				}
			}

			throw new Exception("Unknown column reference $partsString");
		}

		if ($partKey !== $lastPartKey) {
			throw new Exception('Did not get to end of column references');
		}

		if ($sql === false) {
			$assocTable = $repository->getTableName();
			$name = $part;
			if ($assocTable == 'departments') {
				$call = new FunctionCall('if', array(
					new Column(array_merge($this->parts, array('parent', 'id'))),
					new FunctionCall('concat', array(
						new Column(array_merge($this->parts, array('parent', 'title'))),
						new String(' > '),
						new Column(array_merge($this->parts, array('title'))),
					)),
					new Column(array_merge($this->parts, array('title')))
				));
				$prepped = $call->prepare($statement, $section, $stack, $select, $result);

				return new Prepared("`$sqlTable`.`id`", $this->_prettifyColumnName($name), $prepped->sql());
			} else if ($assocTable == 'ticket_slas') {
				$call = new Column(array_merge($this->parts, array('sla')));
				$prepped = $call->prepare($statement, $section, $stack, $select, $result);

				return new Prepared($prepped->sql(), $this->_prettifyColumnName($name), $prepped->printed());
			} else if (preg_match('/^custom_data_/', $assocTable)) {

				$custom_def_table = str_replace('_data_', '_def_', $assocTable);
				switch ($custom_def_table) {
					case 'custom_def_ticket': $manager = App::getContainer()->getSystemService('TicketFieldsManager'); break;
					case 'custom_def_people': $manager = App::getContainer()->getSystemService('PersonFieldsManager'); break;
					case 'custom_def_organizations': $manager = App::getContainer()->getSystemService('OrgFieldsManager'); break;
					default: $manager = null; break;
				}

				$field = null;
				if ($manager) {
					$field = $manager->getFieldFromId($extraConditionValue);
				}

				$renderer = null;
				if ($field && $field->getTypeName() == 'date') {
					$call = new Column(array_merge($this->parts, array('value')));
					$prepped = $call->prepare($statement, $section, $stack, $select, $result);

					$renderer = function(AbstractValues $valueRenderer, $value, array $row, AbstractRenderer $renderer) {
						if (!$value) {
							return $valueRenderer->renderValue(null, 'date');
						}

						$date = new \DateTime('@' . $value);
						if (!$date) {
							return $valueRenderer->renderValue(null, 'date');
						}

						return $valueRenderer->renderValue($date, 'date');
					};
				} else {
					$call = new FunctionCall('if', array(
						new Column(array_merge($this->parts, array('value'))),
						new Column(array_merge($this->parts, array('field', 'title'))),
						new Column(array_merge($this->parts, array('input')))
					));
					$prepped = $call->prepare($statement, $section, $stack, $select, $result);
				}

				return new Prepared($prepped->sql(), $this->_prettifyColumnName($name), false, $renderer);
			} else if (preg_match('/^custom_def_/', $assocTable)) {
				$call = new FunctionCall('if', array(
					new Column(array_merge($this->parts, array('parent', 'id'))),
					new Column(array_merge($this->parts, array('parent', 'title'))),
					new Column(array_merge($this->parts, array('title')))
				));
				$prepped = $call->prepare($statement, $section, $stack, $select, $result);

				return new Prepared($prepped->sql(), $this->_prettifyColumnName($name));
			} else if (isset(self::$_tableResolver[$assocTable])) {
				$resolver = self::$_tableResolver[$assocTable];

				$parent = reset($stack);
				if ($stack || in_array($section, array('order'))) {
					// if we have a parent of any sort, act on the printed value
					$sql = "`$sqlTable`.`$resolver[1]`";
				} else {
					$sql = "`$sqlTable`.`$resolver[0]`";
				}

				$printedSql = "`$sqlTable`.`$resolver[1]`";

				if (isset($resolver[2])) {
					if ($section == 'split') {
						$argSelect = array($statement->getSplitSql()->addSelectField("`$sqlTable`.`$resolver[0]`"));
					} else {
						$argSelect = array($select->addSelectField("`$sqlTable`.`$resolver[0]`"));
					}

					$renderer = function(AbstractValues $valueRenderer, $value, array $row, AbstractRenderer $renderer)
						use ($resolver, $argSelect)
					{
						return Link::formatLink($value, $resolver[2], $argSelect, $row, $valueRenderer, $renderer);
					};
				}
			} else {
				throw new Exception("$partsString cannot be referenced directly. Please reference a specific column.");
			}
		}

		return new Prepared($sql, $this->_prettifyColumnName($name), $printedSql, $renderer);
	}

	/**
	 * Renders a part back to DPQL.
	 *
	 * @param \Application\DeskPRO\Dpql\Statement\Display $statement
	 * @param string $section
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart[] $stack
	 *
	 * @return string
	 */
	public function toDpql(Display $statement, $section, array $stack)
	{
		return implode('.', $this->parts);
	}

	/**
	 * Turns a column reference (such as ticket_id) into a nicer looking,
	 * printable version (Ticket ID).
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	protected function _prettifyColumnName($name)
	{
		$name = str_replace('_', ' ', $name);
		$name = ucwords($name);
		$name = str_replace('Id', 'ID', $name);

		return $name;
	}
}