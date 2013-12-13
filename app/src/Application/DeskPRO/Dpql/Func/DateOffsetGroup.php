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

use Application\DeskPRO\App;
use Application\DeskPRO\Dpql\Statement\Display;
use Application\DeskPRO\Dpql;
use Application\DeskPRO\Dpql\Statement\Part\Prepared;
use Application\DeskPRO\Dpql\Exception;
use Application\DeskPRO\Dpql\Renderer\AbstractRenderer;
use Application\DeskPRO\Dpql\Renderer\Values\AbstractValues;

/**
 * Gets a human readable value for a date offset grouping (0-15 mins, 15-30 mins, etc).
 */
class DateOffsetGroup extends AbstractFunc
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
		$argCount = count($this->_arguments);

		if ($argCount != 1 && $argCount != 2) {
			throw new Exception('DATE_OFFSET_GROUP() can only accept 1 or 2 arguments');
		}

		if ($argCount == 1) {
			$value = reset($this->_arguments);
			$prepped = $value->prepare($statement, $section, $stack, $select, $result);

			$name = 'DATE_OFFSET_GROUP(' . $prepped->name() . ')';
			$ifSql = $prepped->sql();
		} else {
			$valueTo = reset($this->_arguments);
			$valueFrom = next($this->_arguments);

			$toPrepped = $valueTo->prepare($statement, $section, $stack, $select, $result);
			$fromPrepped = $valueFrom->prepare($statement, $section, $stack, $select, $result);

			$name = 'DATE_OFFSET_GROUP(' . $toPrepped->name() . ', ' . $fromPrepped->name() . ')';
			$ifSql = 'UNIX_TIMESTAMP(' . $toPrepped->sql() . ') - UNIX_TIMESTAMP(' . $fromPrepped->sql() . ')';
		}

		$groups = array(
			900 => '0-15 minutes',
			1800 => '15-30 minutes',
			3600 => '30-60 minutes',
			7200 => '1-2 hours',
			14400 => '2-4 hours',
			43200 => '4-12 hours',
			86400 => '12-24 hours',
			172800 => '1-2 days',
			345600 => '2-4 days',
			604800 => '4-7 days',
			1209600 => '1-2 weeks',
			2419200 => '2-4 weeks',
			5270400 => '1-2 months', // actually 61 days
			7862400 => '2-3 months', // 91 days
			15724800 => '3-6 months', // 181 days
			31536000 => '6-12 months', // 365 days
			63072000 => '1-2 years', // 365*2 days
		);
		krsort($groups);

		$maxSentinel = 630720000;

		$sql = $maxSentinel; // this value must be higher than all the group values
		foreach ($groups AS $max => $value) {
			$sql = "IF($ifSql < $max, $max, $sql)";
		}
		$sql = "IF($ifSql IS NULL, 0, $sql)";

		$renderer = function(AbstractValues $valueRenderer, $value, array $row, AbstractRenderer $renderer) use ($groups, $maxSentinel)
		{
			if ($value == $maxSentinel) {
				return '2+ years';
			} else if (isset($groups[$value])) {
				return $groups[$value];
			} else {
				return $valueRenderer->renderValue(null, 'string');
			}
		};

		$return = new Prepared($sql, $name, false, $renderer);

		$return->setGroupFill(function($min, $max) use ($groups, $maxSentinel) {
			$fills = array();
			if ($max >= $maxSentinel) {
				$fills[] = array($maxSentinel, $maxSentinel, $maxSentinel);
			}
			foreach ($groups AS $groupMax => $null) {
				if ($groupMax <= $max) {
					$fills[] = array($groupMax, $groupMax, $groupMax);
				}
			}

			return array_reverse($fills);
		});

		return $return;
	}
}