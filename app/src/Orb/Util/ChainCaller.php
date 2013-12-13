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

/**
 * A simple class that tracks a collection of objects, and calls a method on all
 * of them whenever a method is invoked.
 */
class ChainCaller
{
	protected $_objects = array();

	/**
	 * Add a new object to the chain.
	 * 
	 * @param array $object
	 */
	public function addObject($object)
	{
		$this->_objects[] = $object;
	}

	
	
	/**
	 * Get all objects in the chain.
	 *
	 * @return array
	 */
	public function getObjects()
	{
		return $this->_objects;
	}

	

	/**
	 * Magic method calls each object in the chain.
	 */
	public function __call($name, $arguments)
	{
		foreach ($this->_objects as $obj) {
			$value = call_user_func_array(array($obj, $name), $arguments);
		}

		return $value;
	}
}
