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
 * @category Usersources
 */

namespace Orb\HttpFoundation\Session;

/**
 * Is the same session interface, except we're sandboxed into a namespace.
 * This uses a Session directly.
 */
class SessionNamespace implements SessionInterface
{
	/**
	 * The session object being used
	 * @var Orb\HttpFoundation\Session\Session
	 */
	protected $session;

	/**
	 * The string key used in the above session to store these values
	 * @var string
	 */
	protected $namespace;

	protected function __construct(\Orb\HttpFoundation\Session\Session $session, $namespace)
	{
		$this->session = $session;
		$this->namespace = '__' . $namespace;
	}



	/**
	 * Starts the session storage.
	 */
	public function start()
	{
		$this->session->start();
	}



	/**
	 * Checks if a data item is defined.
	 *
	 * @param string $name The data item name
	 * @return boolean
	 */
	public function has($name)
	{
		$this->start();

		return isset($this->session->data[$this->namespace][$name]);
	}



	/**
	 * Returns a data item.
	 *
	 * @param string $name    The attribute name
	 * @param mixed  $default The default value
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		$this->start();

		return isset($this->session->data[$this->namespace][$name]) ? $this->session->data[$this->namespace][$name] : $default;
	}



	/**
	 * Sets a data item.
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public function set($name, $value)
	{
		$this->start();

		if (isset($this->session->data[$this->namespace])) {
			$this->session->data[$this->namespace] = array();
		}

		$this->session->data[$this->namespace][$name] = $value;
	}



	/**
	 * Returns data.
	 *
	 * @return array
	 */
	public function getAllData()
	{
		$this->start();

		if (!isset($this->session->data[$this->namespace])) {
			return array();
		}

		return $this->session->data[$this->namespace];
	}



	/**
	 * Sets data.
	 *
	 * @param array $data Data
	 */
	public function setAllData($data)
	{
		$this->start();
		$this->session->data[$this->namespace][$name] = $data;
	}



	/**
	 * Removes a data item.
	 *
	 * @param string $name
	 */
	public function remove($name)
	{
		$this->start();
		if (!isset($this->session->data[$this->namespace])) return;

		unset($this->session->data[$this->namespace][$name]);

		// If its empty, just unset this namespace now
		if (!$this->session->data[$this->namespace]) {
			unset($this->session->data[$this->namespace]);
		}
	}



	/**
	 * Removes all data
	 */
	public function removeAllData()
	{
		if (isset($this->session->data[$this->namespace])) {
			unset($this->session->data[$this->namespace]);
		}
	}



	/**
	 * Get this namespace name
	 *
	 * @return string
	 */
	public function getNamespaceName()
	{
		return substr($this->namespace, 2);
	}



	public function getIterator()
	{
		if (isset($this->session->data[$this->namespace])) {
			return \ArrayIterator($this->session->data[$this->namespace]);
		} else {
			return \ArrayIterator(array());
		}
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetExists($offset)
	{
		return $this->has($offset);
	}
}
