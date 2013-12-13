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

namespace Orb\Util;

use Orb\Util\Arrays;

/**
 * Utility functions that work with numbers.
 */
class OptionsArray implements \ArrayAccess, \IteratorAggregate
{
	protected $options = array();

	public function __construct(array $options = array())
	{
		$this->options = $options;
	}

	public function has($name)
	{
		return isset($this->options[$name]);
	}

	public function hasAny(array $names)
	{
		return Arrays::isIn($this->options, $names, false);
	}

	public function hasAll(array $names)
	{
		return Arrays::isIn($this->options, $names, true);
	}

	public function get($name, $default = null)
	{
		return isset($this->options[$name]) ? $this->options[$name] : $default;
	}

	public function set($name, $value)
	{
		$this->options[$name] = $value;
	}

	public function remove($name)
	{
		unset($this->options[$name]);
	}

	public function setArray(array $options)
	{
		$this->options = array_merge($this->options, $options);
	}

	public function setArrayDefault(array $options)
	{
		$this->options = array_merge($options, $this->options);
	}

	public function setDefault($name, $value)
	{
		if (!$this->has($name)) {
			$this->options[$name] = $value;
		}
	}

	public function setAll(array $options)
	{
		$this->options = $options;
	}

	public function all()
	{
		return $this->options;
	}

	public function __get($name)
	{
		return $this->get($name);
	}

	public function __set($name, $value)
	{
		$this->set($name, $value);
	}

	public function __isset($name)
	{
		return $this->has($name);
	}

	public function	__unset($name)
	{
		return $this->remove($name);
	}

	public function offsetGet($k)
	{
		return $this->get($k);
	}

	public function offsetSet($k, $v)
	{
		$this->set($k, $v);
	}

	public function offsetExists($k)
	{
		return $this->has($k);
	}

	public function offsetUnset($k)
	{
		$this->remove($k);
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->options);
	}
}
