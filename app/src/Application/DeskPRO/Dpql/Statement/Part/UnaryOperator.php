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

/**
 * Represents a unary operator (-, NOT, !)
 */
class UnaryOperator extends AbstractPart
{
	/**
	 * Token ID of the operator
	 *
	 * @var integer
	 */
	public $operator;

	/**
	 * @var \Application\DeskPRO\Dpql\Statement\Part\AbstractPart
	 */
	public $value;

	/**
	 * Maps from token IDs to printable/usable operators
	 *
	 * @var array
	 */
	protected static $_operatorMap = array(
		Parser::T_OP_BANG => '!',
		Parser::T_OP_U_MINUS => '-',
		Parser::T_OP_MINUS => '-',
		Parser::T_OP_NOT => 'NOT ', // space after is important
	);

	protected static $_operatorTypeMap = array(
		Parser::T_OP_BANG => 'boolean',
		Parser::T_OP_U_MINUS => 'number',
		Parser::T_OP_MINUS => 'number',
		Parser::T_OP_NOT => 'boolean',
	);

	/**
	 * @param integer $operator
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart $value
	 */
	public function __construct($operator, AbstractPart $value)
	{
		$this->operator = $operator;
		$this->value = $value;
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

		$value = $this->value->prepare($statement, $section, $childStack, $select, $result);
		$operator = self::$_operatorMap[$this->operator];
		$operatorType = self::$_operatorTypeMap[$this->operator];

		return new Prepared("($operator{$value->sql()})", "$operator{$value->name()}", false, $operatorType);
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
		return self::$_operatorMap[$this->operator]
			. $this->value->toDpql($statement, $section, $stack);
	}
}