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
 * Orb
 *
 * @package Orb
 * @category Input
 */

namespace Orb\Input\Reader\Source;

/**
 * A reader source that reads data from a normal array or array-like object
 */
class ArrayVal implements SourceInterface
{
	/**
	 * The array set.
	 * @var array
	 */
	protected $array;



	/**
	 * Create the source.
	 *
	 * @param  $array The array
	 */
	public function __construct($array)
	{
		$this->array = $array;
	}



	/**
	 * Get the value of some variable
	 *
	 * @param   string|array  $name     The name of the variable
	 * @param   mixed         $options  Any options there may be
	 * @return  mixed
	 */
	public function getValue($name, $options = null)
	{
		$parts = array();
		if (is_array($name)) {
			$parts = $name;
			$name = array_shift($parts);
		}

		if (isset($this->array[$name])) {
			$value = $this->array[$name];
		} else {
			$value = null;
		}

		if ($parts) {
			foreach ($parts as $part) {

				if (!is_array($value) OR !isset($value[$part])) {
					$value = null;
					break;
				}

				$value = $value[$part];
			}
		}

		return $value;
	}



	/**
	 * Check if a value of some variable is set.
	 *
	 * @param   string|array  $name     The name of the variable
	 * @param   mixed         $options  Any options there may be
	 * @return  bool
	 */
	public function checkIsset($name, $options = null)
	{
		return ($this->getValue($name, $options) === null ? false : true);
	}



	/**
	 * Get the superglobal name.
	 *
	 * @return string
	 */
	public function getArray()
	{
		return $this->array;
	}



	/**
	 * Set the array value.
	 *
	 * @param array $array
	 * @return void
	 */
	public function setArray($array)
	{
		$this->array = $array;
	}
}
