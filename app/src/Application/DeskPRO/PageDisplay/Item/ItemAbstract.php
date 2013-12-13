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
 * @subpackage PageDisplay
 */

namespace Application\DeskPRO\PageDisplay\Item;

abstract class ItemAbstract implements ItemInterface
{
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * Get the item ID
	 * 
	 * @return int
	 */
	public function getId()
	{
		if (isset($this->data['item_id'])) {
			return $this->data['item_id'];
		}

		return null;
	}

	/**
	 * Sets item data
	 *
	 * @return void
	 */
	public function setData(array $data)
	{
		$this->setData = $data;
	}

	/**
	 * Set a speciifc value
	 *
	 * @param string $k
	 * @param mixed $v
	 */
	public function setDataValue($k, $v)
	{
		$this->data[$k] = $v;
	}

	public function getType()
	{
		return get_class($this);
	}

	/**
	 * Returns item data
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	public function __isset($prop)
	{
		return isset($this->data[$prop]);
	}

	public function __get($prop)
	{
		return $this->data[$prop];
	}
}
