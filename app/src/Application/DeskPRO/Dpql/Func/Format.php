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
use Application\DeskPRO\Dpql\Renderer\AbstractRenderer;
use Application\DeskPRO\Dpql\Renderer\Values\AbstractValues;
use Application\DeskPRO\App;

/**
 * Formats output using the given type and options.
 */
class Format extends AbstractFunc
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
		if (count($this->_arguments) < 2) {
			throw new Exception('FORMAT() requires at least 2 arguments.');
		}

		$arguments = $this->_arguments;
		$value = array_shift($arguments);
		$type = array_shift($arguments);
		$typeLiteral = $this->_toLiteral($type);

		$argNames = array();
		$argLiterals = array();
		foreach ($arguments AS $argument) {
			$prepped = $argument->prepare($statement, $section, $stack, $select, $result);
			$argNames[] = $prepped->name();
			$argLiterals[] = $this->_toLiteral($argument);
		}

		$preppedValue = $value->prepare($statement, $section, $stack, $select, $result);
		$preppedType = $type->prepare($statement, $section, $stack, $select, $result);

		if ($argNames) {
			$argNameOutput = ', ' . implode(', ', $argNames);
		} else {
			$argNameOutput = '';
		}

		$name = 'FORMAT(' . $preppedValue->name() . ', ' . $preppedType->name() . $argNameOutput . ')';

		$renderer = function(AbstractValues $valueRenderer, $value, array $row, AbstractRenderer $renderer)
			use ($typeLiteral, $argLiterals)
		{
			if ($value === null) {
				return $valueRenderer->renderValue(null, 'string');
			}

			switch (strtolower($typeLiteral)) {
				case 'number':
					if ($argLiterals) {
						return $valueRenderer->escapeValue(number_format($value, $argLiterals[0]));
					}
					break;

				case 'date':
					if ($argLiterals) {
						$tz = App::getCurrentPerson()->getTimezone();
						try {
							$date = new \DateTime($value, new \DateTimeZone($tz));
							return $valueRenderer->escapeValue($date->format($argLiterals[0]));
						} catch (\Exception $e) {
							return $valueRenderer->escapeValue($value);
						}
					}
					break;

				case 'percent':
					$decimals = isset($argLiterals[0]) ? $argLiterals[0] : 2;
					return $valueRenderer->escapeValue(number_format($value * 100, $decimals) . '%');
			}

			return $valueRenderer->renderValue($value, $typeLiteral);
		};

		return new Prepared($preppedValue->sql(), $name, false, $renderer);
	}
}