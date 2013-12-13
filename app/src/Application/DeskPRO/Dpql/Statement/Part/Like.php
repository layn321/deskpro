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

/**
 * Represents a call to the LIKE operator.
 */
class Like extends AbstractPart
{
	/**
	 * @var \Application\DeskPRO\Dpql\Statement\Part\AbstractPart
	 */
	public $lhs;

	/**
	 * @var \Application\DeskPRO\Dpql\Statement\Part\AbstractPart
	 */
	public $rhs;

	/**
	 * True = LIKE, false = NOT LIKE
	 *
	 * @var bool
	 */
	public $positive;

	/**
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart $lhs
	 * @param \Application\DeskPRO\Dpql\Statement\Part\AbstractPart $rhs
	 * @param bool $positive
	 */
	public function __construct(AbstractPart $lhs, AbstractPart $rhs, $positive = true)
	{
		$this->lhs = $lhs;
		$this->rhs = $rhs;
		$this->positive = $positive;
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

		$lhs = $this->lhs->prepare($statement, $section, $childStack, $select, $result);
		$rhs = $this->rhs->prepare($statement, $section, $childStack, $select, $result);
		$not = ($this->positive ? '' : ' NOT');

		$sql = "{$lhs->sql()}$not LIKE {$rhs->sql()}";
		return new Prepared($sql, "{$lhs->name()}$not LIKE {$rhs->name()}", false, 'boolean');
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
		$not = ($this->positive ? '' : ' NOT');

		return $this->lhs->toDpql($statement, $section, $stack)
			. $not . ' LIKE '
			. $this->rhs->toDpql($statement, $section, $stack);
	}
}