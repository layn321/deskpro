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
 * @subpackage
 */

namespace Application\DeskPRO\Tree;

/**
 * A wrapper around any object that has 'children' that allows you to define the structure
 * on-the-fly with a callback filter.
 *
 * This is ideal in templates. For example, you have a category hierarchy but the user only has permission
 * to view a subset of them. From the controller you can just pass the normal hierarchy wrapped in this class
 * with a filter.
 */
class TreeProxy implements \ArrayAccess
{
	protected $__obj;
	protected $__filter;
	protected $__child_cache;

	/**
	 * Go over each item in an array and wrap it with this tree proxy and $filter
	 *
	 * @param array $array
	 * @param callback  $filter
	 * @return array
	 */
	public static function makeTreeProxyArray($array, $filter)
	{
		$ret = array();

		foreach ($array as $k => $c) {
			if (!call_user_func($filter, $c, $k)) {
				continue;
			}

			$c_obj = new static($c, $filter);
			$ret[$k] = $c_obj;
		}

		return $ret;
	}


	/**
	 * @param mixed $obj
	 * @param callback $filter Any callback that adheres to function($obj, $key)
	 */
	public function __construct($obj, $filter)
	{
		$this->__obj = $obj;
		$this->__filter = $filter;
	}


	/**
	 * @return mixed
	 */
	public function getObject()
	{
		return $this->__obj;
	}


	/**
	 * Get the children that pas the filter, with each child itself being wrapped with the same filter.
	 *
	 * @return array
	 */
	public function getChildren()
	{
		if ($this->__child_cache !== null) {
			return $this->__child_cache;
		}

		$children = $this->__obj->getChildren();
		if (!$children || !count($children)) {
			$this->__child_cache = array();
			return array();
		}
		$children = $children->toArray();

		uasort($children, function($a, $b) {
			if (!isset($a->display_order)) {
				return 0;
			}

			if ($a->display_order == $b->display_order) {
				return 0;
			}

			return ($a->display_order < $b->display_order) ? -1 : 1;
		});

		$this->__child_cache = self::makeTreeProxyArray($children, $this->__filter);
		return $this->__child_cache;
	}


	####################################################################################################################
	# Implementations of magic methods
	####################################################################################################################

	public function offsetExists($offset)
	{
		if ($offset == 'children' || $offset == 'children_ordered') {
			return true;
		}

		return isset($this->__obj[$offset]);
	}

	public function offsetGet($offset)
	{
		if ($offset == 'children' || $offset == 'children_ordered') {
			return $this->getChildren();
		}

		return $this->__obj[$offset];
	}

	public function offsetSet($offset, $value)
	{
		if ($offset == 'children' || $offset == 'children_ordered') {
			throw new \RuntimeException();
		}

		$this->__obj[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		if ($offset == 'children' || $offset == 'children_ordered') {
			throw new \RuntimeException();
		}

		unset($this->__obj[$offset]);
	}

	function __call($name, $arguments)
	{
		return call_user_func_array(array($name, $this->__obj), $arguments);
	}

	function __get($name)
	{
		if ($name == 'children' || $name == 'children_ordered') {
			return $this->getChildren();
		}

		return $this->__obj->$name;
	}

	function __set($name, $value)
	{
		if ($name == 'children' || $name == 'children_ordered') {
			throw new \RuntimeException();
		}

		$this->__obj->$name = $value;
	}
}