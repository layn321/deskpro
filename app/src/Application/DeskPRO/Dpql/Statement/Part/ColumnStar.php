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
 * Represents a reference to all data in a table
 */
class ColumnStar extends AbstractPart
{
	/**
	 * List of parts in the reference
	 *
	 * @var array
	 */
	public $parts;

	/**
	 * @param array $parts
	 */
	public function __construct(array $parts)
	{
		array_pop($parts); // pop off the ".*"
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

		end($parts);
		$lastPartKey = key($parts);

		// represents the repository of what we're joining from
		$repository = $statement->getFromEntityRepository();

		$partsSoFar = array($table);
		$partsString = $table;

		foreach ($parts AS $partKey => $part) {
			$partsSoFar[] = $part;
			$partsString = implode('.', $partsSoFar);

			if (preg_match('/\[(.+)\]$/', $part, $match)) {
				$part = substr($part, 0, -strlen($match[0]));
			}

			// are we referencing a field?
			foreach ($repository->getFieldMappings() AS $key => $field) {
				if (strtolower($key) == $part) {
					throw new Exception("Select star conditions may only include references to tables or associations (did not expect $partsString).");
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
						throw new Exception("Select star conditions may only include references to tables or associations (did not expect $partsString).");
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

					$repository = $childRepository;
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

					if (!empty($association['joinColumns'])) {
						// join can be resolved directly
						$joinColumns = $association['joinColumns'];
					} else {
						$childAssociations = $childRepository->getAssociationMappings();
						if (!empty($childAssociations[$association['mappedBy']]['joinColumns'])) {
							// join details are on the other table
							$joinColumns = $childAssociations[$association['mappedBy']]['joinColumns'];
						} else {
							$joinColumns = array();
						}
					}

					if (!$joinColumns) {
						throw new Exception("$partsString cannot be accessed via DPQL.");
					}

					$repository = $childRepository; // now references come from this table
					continue 2; // continue $parts loop
				}
			}

			throw new Exception("Unknown column reference $partsString");
		}

		if ($parts && $partKey !== $lastPartKey) {
			throw new Exception('Did not get to end of column references');
		}

		foreach ($repository->getFieldMappings() AS $key => $field) {
			if (isset($field['dpqlAccess']) && !$field['dpqlAccess']) {
				continue;
			}

			$column = new Column(array_merge($partsSoFar, array($key)));
			$statement->addPreparedSelectField($column->prepare($statement, $section, $stack, $select, $result));
		}
		foreach ($repository->getAssociationMappings() AS $association) {
			if ((isset($association['dpqlAccess']) && !$association['dpqlAccess'])
				|| $association['type'] == ClassMetadataInfo::MANY_TO_MANY
			) {
				continue;
			}

			if ($association['type'] & ClassMetadataInfo::TO_ONE) {
				try {
					$column = new Column(array_merge($partsSoFar, array($association['fieldName'])));
					$statement->addPreparedSelectField($column->prepare($statement, $section, $stack, $select, $result));
				} catch (Exception $e) {}
			} else if (preg_match('/CustomData([a-zA-Z]+)$/', $association['targetEntity'], $match)) {
				switch ($match[1]) {
					case 'Article': $type = 'articles'; break;
					case 'Feedback': $type = 'feedback'; break;
					case 'Organization': $type = 'organizations'; break;
					case 'Person': $type = 'people'; break;
					case 'Ticket': $type = 'tickets'; break;
					default: $type = ''; break;
				}

				if ($type) {
					foreach (App::getApi('custom_fields.' . $type)->getFields() AS $field) {
						$column = new Column(array_merge($partsSoFar, array($association['fieldName'] . "[$field->id]")));
						$statement->addPreparedSelectField(
							$column->prepare($statement, $section, $stack, $select, $result), $field->title
						);
					}
				}
			}
		}

		return new Prepared(false);
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
		return implode('.', $this->parts) . '.*';
	}
}