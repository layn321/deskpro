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
 * A reader source that fetches data from a superglobal array.
 */
class Superglobal implements SourceInterface
{
	/**
	 * The superglobal name
	 * @var string
	 */
	protected $superglobal;

	/**
	 * Array of data
	 * @var array
	 */
	protected $array = null;

	/**
	 * Create the source.
	 *
	 * @param  $sg_name  The name of the superglobal: _POST, _GET etc.
	 */
	public function __construct($sg_name)
	{
		$this->superglobal = $sg_name;
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
		$this->_initArray();

		$parts = array();
		if (is_array($name)) {
			$parts = $name;
			$name = array_shift($parts);
		}

		if (isset($this->array[$name])) {
			$value = $this->array[$name];
		} else {
			return null;
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

	protected function _initArray()
	{
		if ($this->array !== null) return; // already done

		// We'll enforce our own request array
		if ($this->superglobal == '_REQUEST') {
			$this->array = \array_merge($_GET, $_POST);
		} else {
			$this->array = $GLOBALS[$this->superglobal];
		}
		if (!$this->array) $this->array = array();
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
	public function getSuperglobalName()
	{
		return $this->superglobal;
	}
}
