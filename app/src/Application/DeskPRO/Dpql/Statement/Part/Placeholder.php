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
use Application\DeskPRO\Dpql\Placeholder\AbstractPlaceholder;

/**
 * Represents a placeholder reference.
 */
class Placeholder extends AbstractPart
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
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
		$prepared = AbstractPlaceholder::create($this->name)->prepare(
			$statement, $section, $stack, $select, $result
		);
		$prepared->setName('%' . $this->name . '%');

		return $prepared;
	}

	/**
	 * Prepares a part for use, including validating that the usage is valid.
	 *
	 * @param \Application\DeskPRO\Dpql\Statement\Display $statement
	 * @param string $section Name of the section usage is in (select, where, split, group, order)
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart[] $stack Parent parts
	 * @param \Application\DeskPRO\Dpql\SqlSelect $select Select being built up
	 * @param \Application\DeskPRO\Dpql\ResultHandler $result
	 * @param \Application\DeskPRO\Dpql\Statement\Part\BinaryInterval[] $intervals List of intervals that affect this calculation
	 *
	 * @throws \Application\DeskPRO\Dpql\Exception
	 *
	 * @return \Application\DeskPRO\Dpql\Statement\Part\Prepared|bool Prepared results or false if there's no output
	 */
	public function prepareWithIntervals(
		Display $statement, $section, array $stack, Dpql\SqlSelect $select, Dpql\ResultHandler $result, array $intervals = array()
	)
	{
		$prepared = AbstractPlaceholder::create($this->name)->prepareWithIntervals(
			$statement, $section, $stack, $select, $result, $intervals
		);

		$append = '';
		foreach ($intervals AS $interval) {
			$operator = $interval->operator == \Application\DeskPRO\Dpql\Parser::T_OP_PLUS ? '+' : '-';
			$append .= " $operator INTERVAL $interval->amount $interval->unit";
		}

		$prepared->setName('%' . $this->name . '%' . $append);

		return $prepared;
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
		return '%' . $this->name . '%';
	}

	/**
	 * Prepares the placeholder when it's called in a binary comparison context.
	 * The placeholder is always the right hand side of the comparison. This is
	 * needed for placeholders that actually map to date ranges rather than scalars.
	 *
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart $lhs The left hand side of the comparison
	 * @param string $comparison The comparison operator
	 * @param \Application\DeskPRO\Dpql\Statement\Display $statement
	 * @param string $section Name of the section usage is in (select, where, split, group, order)
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart[] $stack Parent parts
	 * @param \Application\DeskPRO\Dpql\SqlSelect $select
	 * @param \Application\DeskPRO\Dpql\ResultHandler $result
	 * @param \Application\DeskPRO\Dpql\Statement\Part\BinaryInterval[] List of intervals that affect this calculation
	 *
	 * @throws \Application\DeskPRO\Dpql\Exception
	 *
	 * @return \Application\DeskPRO\Dpql\Statement\Part\Prepared|bool Prepared results or false the default behavior should be called
	 */
	public function prepareComparison(
		AbstractPart $lhs, $comparison, Display $statement, $section, array $stack,
		Dpql\SqlSelect $select, Dpql\ResultHandler $result, array $intervals = array()
	)
	{
		return AbstractPlaceholder::create($this->name)->prepareComparison(
			$lhs, $comparison, $statement, $section, $stack, $select, $result, $intervals
		);
	}
}