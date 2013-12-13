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

namespace Application\DeskPRO\Dpql\Func;

use Application\DeskPRO\Dpql\Statement\Display;
use Application\DeskPRO\Dpql;
use Application\DeskPRO\Dpql\Statement\Part\Prepared;
use Application\DeskPRO\Dpql\Exception;

/**
 * Used in the GROUP BY clause to specify columns that will be used
 * in the X direction to create a matrix table.
 */
class X extends AbstractFunc
{
	/**
	 * Prepares the function for use, including validating that the usage is valid.
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
		if ($section != 'group') {
			throw new Exception('X() may only be used in GROUP BY.');
		}
		if (count($stack) > 1) {
			// note: the top of the stack is this function
			throw new Exception('X() may only be used at the top-level.');
		}

		$childStack = $stack;
		array_shift($childStack); // pop this off the stack - it doesn't exist to the children

		foreach ($this->_arguments AS $arg) {
			if ($arg instanceof \Application\DeskPRO\Dpql\Statement\Part\NullValue) {
				continue;
			}

			$groupBy = $arg->prepare($statement, $section, $childStack, $select, $result);
			if ($groupBy->hasValue()) {
				$printId = $select->addSelectField($groupBy->printed());
				$select->addGroupBy($groupBy->sql());
				$defaultOrder = $statement->addDefaultOrder($groupBy->printed());

				if ($groupBy->printed() === $groupBy->sql()) {
					$groupId = $printId;
				} else {
					$groupId = $select->addSelectField($groupBy->sql());
				}

				if ($groupBy->ordered() == $groupBy->printed()) {
					$orderId = $printId;
				} else {
					$orderId = $select->addSelectField($groupBy->ordered());
				}

				if ($defaultOrder && $groupBy->groupFill()) {
					$statement->addGroupFill($groupBy->groupFill(), $printId, $groupId, $orderId);
				}

				$result->addGroupXColumn($groupBy->name(), $groupId, $printId, $groupBy->renderer());
			}
		}

		return new Prepared(false);
	}
}