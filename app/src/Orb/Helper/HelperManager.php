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
 * @category Util
 */

namespace Orb\Helper;

use \Orb\Util\Util;

/**
 * An object that keeps track of "helpers".
 */
class HelperManager
{
	/**
	 * An array of helpers
	 * @var array
	 */
	protected $_helpers = array();

	/**
	 * An array of short callable names
	 */
	protected $_callable_names = array();



	/**
	 * Add a helper object
	 *
	 * @param Object $object
	 * @param string $name  The name of the helper. Defaults to strtolower of the base classname
	 */
	public function addHelper($object, $name = null, $prefix_callable = false)
	{
		if (!$name) {
			$name = Util::getBaseClassname($object);
		}

		$name = strtolower($name);

		if (isset($this->_helpers[$name])) {
			throw new \InvalidArgumentException("`$name` has already been registered.");
		}

		if (method_exists($object, '__invoke')) {
			$this->_callable_names[str_replace('_', '', $name)] = array($object, '__invoke');
		}

		if ($object instanceof ShortCallableInterface) {
			foreach ($object->getShortCallableNames() as $short_name => $method) {
				if ($prefix_callable) {
					$short_name = $prefix_callable . $short_name;
				}

				$short_name = strtolower($short_name);

				$this->_callable_names[$short_name] = array($object, $method);
			}
		}

		$this->_helpers[$name] = $object;
	}



	/**
	 * Check to see if a helper of a specific type has been registerd.
	 *
	 * @param string $typename
	 * @param bool $exact Check for exact class, discount any children
	 * @return bool
	 */
	public function findHelperOfType($typename, $exact = false)
	{
		if ($exact) {
			foreach ($this->_helpers as $name => $object) {
				if (get_class($object) == $typename) {
					return $name;
				}
			}
		} else {
			foreach ($this->_helpers as $object) {
				if (is_a($object, $typename)) {
					return $name;
				}
			}
		}

		return false;
	}



	/**
	 * Is a certain helper added yet?
	 *
	 * @return bool
	 */
	public function hasHelper($name)
	{
		$name = strtolower($name);
		return isset($this->_helpers[$name]);
	}



	/**
	 * Get a helper
	 *
	 * @param <type> $name
	 * @return <type>
	 */
	public function getHelper($name)
	{
		$name = strtolower($name);
		if (!isset($this->_helpers[$name])) {
			throw new \OutOfBoundsException("No helper `$name` is registered");
		}

		return $this->_helpers[$name];
	}



	/**
	 * Remove a helper
	 *
	 * @param string $name
	 * @return void
	 */
	public function removeHelper($name)
	{
		$name = strtolower($name);
		if (!isset($this->_helpers[$name])) {
			throw new \OutOfBoundsException("No helper `$name` is registered");
		}

		unset($this->_helpers[$name]);
	}



	/**
	 * Is a certain name callable given our helpers?
	 *
	 * @param string $name
	 * @return bool
	 */
	public function isNameCallable($name)
	{
		if (isset($this->_callable_names[$name])) {
			return true;
		}

		return false;
	}



	/**
	 * Call a certain callable
	 *
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function callName($name, array $args)
	{
		return call_user_func_array($this->_callable_names[$name], $args);
	}
}
