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

namespace Application\DeskPRO\Dpql\Renderer\Values;

use Application\DeskPRO\App;

/**
 * Handlers formatting values to a specific type (text, html, etc) for the
 * DPQL renderer. This allows escaping/output preparation to be abstracted
 * from the actual renderer type.
 */
abstract class AbstractValues
{
	/**
	 * Renders a null value.
	 *
	 * @return string
	 */
	abstract protected function _renderNull();

	/**
	 * Renders a boolean value.
	 *
	 * @param boolean $value
	 *
	 * @return string
	 */
	abstract protected function _renderBoolean($value);

	/**
	 * Escapes the value for direct output.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	abstract public function escapeValue($value);

	/**
	 * Renders a value, ready to be output. Note that the value may still needed
	 * to be wrapped to be valid (quotes, td html tag, etc).
	 *
	 * @param string $value
	 * @param string|\Closure $format
	 *
	 * @return string
	 */
	public function renderValue($value, $format)
	{
		if ($value === null) {
			return $this->_renderNull();
		}

		$format = strtolower($format);

		switch ($format) {
			case 'year':
				return $this->escapeValue($value);

			case 'number':
			case 'numberraw';
			case 'id':
				if (preg_match('/^(\d*)\.(\d+)$/', $value, $match)) {
					// float
					$decimals = min(1, strlen($match[2]));
				} else {
					// integer
					$decimals = 0;
				}

				if (in_array($format, array('numberraw', 'id'))) {
					$thousands = '';
				} else {
					$thousands = ',';
				}

				$value = round($value, $decimals);

				return $this->escapeValue(number_format($value, $decimals, '.', $thousands));

			case 'boolean':
				return $this->_renderBoolean($value);

			case 'datetime':
			case 'date':
			case 'time':
				$settingMap = array(
					'datetime' => 'core.date_fulltime',
					'date' => 'core.date_full',
					'time' => 'core.date_time'
				);

				$tz = App::getCurrentPerson()->getTimezone();
				try {
					if ($value instanceof \DateTime) {
						$date = clone $value;
						$date->setTimezone(new \DateTimeZone($tz));
					} else {
						$date = new \DateTime($value, new \DateTimeZone($tz));
					}
					return $this->escapeValue($date->format(App::getSetting($settingMap[$format])));
				} catch (\Exception $e) {
					return $this->escapeValue($value);
				}

			case 'percent':
				return $this->escapeValue(number_format($value * 100, 2) . '%');
				break;

			case 'string':
			default:
				return $this->escapeValue($value);
		}
	}
}