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
 * @subpackage HttpFoundation
 */

namespace Orb\HttpFoundation\Session;

use \Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;

/**
 * A session interface
 */
interface SessionInterface extends \ArrayAccess, \IteratorAggregate
{
	/**
	 * Checks if a data item is defined.
	 *
	 * @param string $name The data item name
	 * @return boolean
	 */
	public function has($name);

	/**
	 * Returns a data item.
	 *
	 * @param string $name    The attribute name
	 * @param mixed  $default The default value
	 * @return mixed
	 */
	public function get($name, $default = null);

	/**
	 * Sets a data item.
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public function set($name, $value);

	/**
	 * Returns data.
	 *
	 * @return array
	 */
	public function getAllData();

	/**
	 * Sets data.
	 *
	 * @param array $data Attributes
	 */
	public function setAllData($data);

	/**
	 * Removes a data item.
	 *
	 * @param string $name
	 */
	public function remove($name);

	/**
	 * Removes all set data
	 */
	public function removeAllData();
}
