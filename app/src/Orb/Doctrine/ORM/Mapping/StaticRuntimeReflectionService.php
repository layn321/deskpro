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
 * @subpackage Doctrine
 */

namespace Orb\Doctrine\ORM\Mapping;

use Doctrine\Common\Persistence\Mapping\ReflectionService;

class StaticRuntimeReflectionService implements ReflectionService
{
	/**
	 * Return an array of the parent classes (not interfaces) for the given class.
	 *
	 * @param string $class
	 * @return array
	 */
	public function getParentClasses($class)
	{
		return class_parents($class);
	}

	/**
	 * Return the shortname of a class.
	 *
	 * @param string $className
	 * @return string
	 */
	public function getClassShortName($className)
	{
		if (($p = strrpos($className, '\\')) !== false) {
			$className = substr($className, $p);
		}
		return $className;
	}

	/**
	 * Return the namespace of a class.
	 *
	 * @param string $className
	 * @return string
	 */
	public function getClassNamespace($className)
	{
		$namespace = '';
		if (($p = strrpos($className, '\\')) !== false) {
			$namespace = substr($className, 0, $p);
		}
		return $namespace;
	}

	/**
	 * Return a reflection class instance or null
	 *
	 * @param string $class
	 * @return \ReflectionClass|null
	 */
	public function getClass($class)
	{
		return new \ReflectionClass($class);
	}

	/**
	 * Return an accessible property (setAccessible(true)) or null.
	 *
	 * @param string $class
	 * @param string $property
	 * @return \ReflectionProperty|null
	 */
	public function getAccessibleProperty($class, $property)
	{
		$property = new StaticReflectionProperty($class, $property);
		$property->setAccessible(true);
		return $property;
	}

	/**
	 * Check if the class have a public method with the given name.
	 *
	 * @param mixed $class
	 * @param mixed $method
	 * @return bool
	 */
	public function hasPublicMethod($class, $method)
	{
		return method_exists($class, $method) && is_callable(array($class, $method));
	}
}