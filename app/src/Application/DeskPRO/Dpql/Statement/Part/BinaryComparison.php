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
use Application\DeskPRO\Dpql\Parser;
use Application\DeskPRO\Dpql\Exception;

/**
 * Represents a binary comparison (=, >, <=, etc).
 */
class BinaryComparison extends AbstractPart
{
	/**
	 * Token ID of the operator
	 *
	 * @var integer
	 */
	public $operator;

	/**
	 * Left hand side of comparison
	 *
	 * @var \Application\DeskPRO\Dpql\Statement\Part\AbstractPart
	 */
	public $lhs;

	/**
	 * Right hand side of comparison
	 *
	 * @var \Application\DeskPRO\Dpql\Statement\Part\AbstractPart
	 */
	public $rhs;

	/**
	 * Maps from token IDs to printable/usable operators
	 *
	 * @var array
	 */
	protected static $_operatorMap = array(
		Parser::T_OP_EQ => '=',
		Parser::T_OP_NE => '<>',
		Parser::T_OP_GT => '>',
		Parser::T_OP_GTEQ => '>=',
		Parser::T_OP_LT => '<',
		Parser::T_OP_LTEQ => '<='
	);

	/**
	 * This is used when a comparison needs to be flipped (for placeholders, for example).
	 * Maps from the original operator string to the equivalent when the
	 * comparison's LHS and RHS are swapped.
	 *
	 * @var array
	 */
	protected static $_operatorOrderFlipped = array(
		'=' => '=',
		'<>' => '<>',
		'>' => '<',
		'>=' => '<=',
		'<' => '>',
		'<=' => '>='
	);

	/**
	 * @param integer $operator
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart $lhs
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart $rhs
	 *
	 * @throws \Application\DeskPRO\Dpql\Exception
	 */
	public function __construct($operator, AbstractPart $lhs, AbstractPart $rhs)
	{
		if (!isset(self::$_operatorMap[$operator])) {
			throw new Exception("Invalid comparison operator (token ID: $operator)");
		}

		$this->operator = $operator;
		$this->lhs = $lhs;
		$this->rhs = $rhs;
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
		$childStack = $this->getChildStack($stack);

		$lhs = $this->lhs;
		$rhs = $this->rhs;
		$operator = self::$_operatorMap[$this->operator];

		if ($lhs instanceof Placeholder || $lhs instanceof BinaryInterval) {
			// flip as placeholder/interval comparison expects placeholder/interval on RHS
			$temp = $lhs;
			$lhs = $rhs;
			$rhs = $temp;
			$operator = self::$_operatorOrderFlipped[$operator];
		}

		if ($rhs instanceof Placeholder || $rhs instanceof BinaryInterval) {
			$prepared = $rhs->prepareComparison(
				$lhs, $operator, $statement, $section, $childStack, $select, $result
			);
			if ($prepared) {
				return $prepared;
			}
		}

		$lhsRes = $lhs->prepare($statement, $section, $childStack, $select, $result);
		$rhsRes = $rhs->prepare($statement, $section, $childStack, $select, $result);

		$title = "{$lhsRes->name()} $operator {$rhsRes->name()}";

		if ($rhs instanceof NullValue) {
			if ($this->operator == Parser::T_OP_EQ) {
				return new Prepared("({$lhsRes->sql()} IS NULL)", $title);
			} else if ($this->operator == Parser::T_OP_NE) {
				return new Prepared("({$lhsRes->sql()} IS NOT NULL)", $title);
			}
		}

		return new Prepared("({$lhsRes->sql()} $operator {$rhsRes->sql()})", $title, false, 'boolean');
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
		return $this->lhs->toDpql($statement, $section, $stack)
			. ' ' . self::$_operatorMap[$this->operator] . ' '
			. $this->rhs->toDpql($statement, $section, $stack);
	}
}