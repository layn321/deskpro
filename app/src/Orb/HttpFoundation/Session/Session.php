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
 *
 */
class Session implements SessionInterface
{
	/**
	 * @var SessionStorageInterface
	 */
	protected $storage;

	/**
	 * Has the session been started yet?
	 * @var bool
	 */
	protected $has_started = false;

	/**
	 * Created namespaces. Namespaces are always using this objects data directly,
	 * but more efficient if we use the same namespace objects instead of creating new ones.
	 * @var array
	 */
	protected $namespaces = array();

	/**
	 * The raw data we'll save to storage. This is public for efficiencies sake in
	 * SessionNamespace -- you shouldn't use this yourself though.
	 * @var array
	 */
	public $data = array();

	/**
	 * Various metadata
	 * @var array
	 */
	public $metadata = array();


	/**
	 * @param SessionStorageInterface $storage
	 */
	public function __construct(SessionStorageInterface $storage)
	{
		$this->storage = $storage;
	}



	/**
	 * Gets a session object who is sandboxed to a specific sub-key (namespace)
	 *
	 * @param string $namespace The namespace to fetch
	 * @return SessionNamespace
	 */
	public function createNamespace($namespace)
	{
		if (isset($this->namespaces[$namespace])) {
			return $this->namespaces[$namespace];
		}

		$this->namespaces[$namespace] = new SessionNamespace($this, $namespace);
		return $this->namespaces[$namespace];
	}



	/**
	 * Starts the session storage.
	 */
	public function start()
	{
		if ($this->has_started === true) {
			return;
		}

		$this->storage->start();
		$this->data = $this->storage->read('orb_sess');
		$this->metadata = array();
		if (isset($this->data['__metadata'])) {
			$this->metadata = $this->data['__metadata'];
			unset($this->data['__metadata']);
		}
		$this->has_started = true;
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

		return isset($this->data[$name]);
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

		return isset($this->data[$name]) ? $this->data[$name] : $default;
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

		$this->data[$name] = $value;
	}



	/**
	 * Returns data.
	 *
	 * @return array
	 */
	public function getAllData()
	{
		$this->start();

		return $this->data;
	}



	/**
	 * Sets data.
	 *
	 * @param array $data Attributes
	 */
	public function setAllData($data)
	{
		$this->start();
		$this->data = $data;
	}



	/**
	 * Clear all set data
	 */
	public function removeAllData()
	{
		$this->data = array();
	}



	/**
	 * Removes a data item.
	 *
	 * @param string $name
	 */
	public function remove($name)
	{
		$this->start();
		unset($this->data[$name]);
	}



	public function __destruct()
	{
		if ($this->has_started === true) {
			if ($this->metadata) {
				$data = array_merge($this->data, array('__metadata' => $this->metadata));
			} else {
				$data = $this->data;
			}

			$this->storage->write('orb_sess', $data);
		}
	}



	public function getIterator()
	{
		return \ArrayIterator($this->data);
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
