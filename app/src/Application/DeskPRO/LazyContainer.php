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

namespace Application\DeskPRO;

/**
 * A very simple container with lazy loading with callback functions for items.
 */
class LazyContainer
{
	protected $items = array();
	protected $wait_items = array();

	public function add($id, $loader)
	{
		$this->wait_items[$id] = $loader;
	}

	public function set($id, $value)
	{
		$this->items[$id] = $value;
	}

	public function has($id)
	{
		return array_key_exists($id, $this->items) OR isset($this->wait_items[$id]);
	}

	public function get($id)
	{
		if (array_key_exists($id, $this->items)) {
			return $this->items[$id];
		}

		if (isset($this->wait_items[$id])) {
			$f = $this->wait_items[$id];
			unset($this->wait_items[$id]);

			$v = $f();

			$this->set($id, $v);
			return $this->items[$id];
		}

		return null;
	}

	public function __get($id)
	{
		return $this->get($id);
	}

	public function __isset($id)
	{
		return $this->has($id);
	}
}
