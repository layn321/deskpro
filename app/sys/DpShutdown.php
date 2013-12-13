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
 */

class DpShutdown
{
	/**
	 * @var \DpShutdown
	 */
	private static $inst;

	/**
	 * @var \SplPriorityQueue[]
	 */
	private static $stack = null;

	/**
	 * @var array
	 */
	private static $params = array();

	/**
	 * @var array
	 */
	private static $callbacks;

	/**
	 * Inits the queue
	 */
	private static function _init()
	{
		if (self::$stack !== null) {
			return;
		}

		static $has_init_shutdown = false;
		if (!$has_init_shutdown) {
			$has_init_shutdown = true;
			register_shutdown_function(array('DpShutdown', 'run'));
		}

		self::$stack = array();
		self::$callbacks = array();
	}


	/**
	 *	Register a new shutdown function
	 *
	 * @param callback $callback
	 * @param int $priority
	 * @param string $tag
	 */
	public static function add($callback, array $params = null, $tag = null, $priority = 0)
	{
		self::_init();
		if ($tag === null) $tag	= 'shutdown';

		static $gen_id = 0;
		$gen_id++;

		if (!isset(self::$stack[$tag])) {
			self::$stack[$tag] = new \SplPriorityQueue();
		}

		self::$stack[$tag]->insert('cb'.$gen_id, $priority);
		self::$callbacks['cb'.$gen_id] = array($callback, $params, $priority, $tag);
	}


	/**
	 * @param string $tag
	 * @return bool
	 */
	public static function hasTag($tag = null)
	{
		if ($tag === null) $tag	= 'shutdown';
		return isset(self::$stack[$tag]);
	}


	/**
	 * @param string $name
	 * @param mixed $value
	 */
	public static function setGlobalParam($name, $value, $overwrite = false)
	{
		if (isset(self::$params) && !$overwrite) {
			throw new \InvalidArgumentException("$name is already set");
		}

		self::$params[$name] = $value;
	}


	/**
	 * Run all shutdown functions
	 */
	public static function run($tag = null)
	{
		if ($tag === null) $tag	= 'shutdown';

		if (!isset(self::$stack[$tag])) {
			return;
		}

		$proc_stack = self::$stack[$tag];
		unset(self::$stack[$tag]);

		foreach ($proc_stack as $id) {
			if (!isset(self::$callbacks[$id])) {
				continue;
			}

			$info = self::$callbacks[$id];
			unset(self::$callbacks[$id]);
			$callback = $info[0];

			$pass_params = self::$params;
			if ($info[1]) {
				$pass_params = array_merge($pass_params, $info[1]);
			}

			call_user_func($callback, $pass_params);
		}
	}
}