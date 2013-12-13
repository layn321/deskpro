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
 * A simple extension to the Symfony class loader that adds ability to map specific
 * classes to specific files. Useful for single-classes.
 */
class ClassLoader extends \Symfony\Component\ClassLoader\UniversalClassLoader
{
	/**
	 * An array of classname => file
	 * @var array
	 */
	protected $class_map = array();

	/**
	 * Maps a namespace to a callback that is called when it cant be loaded using
	 * a normal map.
	 *
	 * @var array
	 */
	protected $namespace_callback = array();


	/**
	 * Get the current class map.
	 *
	 * @return array
	 */
	public function getClassNameMap()
	{
		return $this->class_map;
	}


	/**
	 * Register a new namespace callback loader
	 *
	 * @param string $namespace
	 * @param callback $callback
	 * @return void
	 */
	public function registerNamespaceCallback($namespace, $callback)
	{
		$this->namespace_callback[$namespace] = $callback;
	}


	/**
	 * Register a classname to a particular path.
	 *
	 * @param string $classname The full classname
	 * @param string $path The path to the source file
	 */
	public function registerClassName($class_name, $path)
	{
		$this->class_map[$class_name] = $path;
	}



	/**
	 * Register an array of classnames.
	 *
	 * @param array $class_names An array of classname => path
	 */
	public function registerClassNames(array $class_names)
	{
		$this->class_map = array_merge($this->class_map, $class_names);
	}


	public function findFile($class_name)
	{
		if (isset($this->class_map[$class_name])) {
			$file = $this->class_map[$class_name];
			if (file_exists($file)) {
				return $file;
			}
		}

		$file = parent::findFile($class_name);

		if (!$file) {
			$m = null;
			$ns_parts = explode('\\', $class_name, 2);
			if (count($ns_parts) == 2) {
				$ns = $ns_parts[0];
				if (isset($this->namespace_callback[$ns])) {
					$callback = $this->namespace_callback[$ns];
					$file = call_user_func($callback, $class_name);
				}
			}
		}

		return $file;
	}
}
