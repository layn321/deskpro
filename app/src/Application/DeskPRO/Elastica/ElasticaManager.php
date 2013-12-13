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
 * @category Elastica
 */

namespace Application\DeskPRO\Elastica;

/**
 * Manages references to Elastica indexes and client connections
 */
class ElasticaManager
{
	/**
	 * @var \Elastica_Client[]
	 */
	protected $clients = array();

	/**
	 * The name of the default client to use when no name is given
	 * @var string
	 */
	protected $default_client = 'default';

	/**
	 * @var \Elastica_Index[]
	 */
	protected $indexes = array();

	/**
	 * The default index name
	 * @var string
	 */
	protected $default_index = 'content';

	/**
	 * @var \Application\DeskPRO\Elastica\Type\AbstractType[]
	 */
	protected $types = array();

	
	/**
	 * Get a client connection
	 *
	 * @param string $name
	 * @return \Elastica_Client
	 */
	public function getClient($name = null)
	{
		if ($name === null) $name = $this->default_client;

		if (!isset($this->clients[$name])) {
			throw new \InvalidArgumentException("Unknown client `$name`");
		}

		return $this->clients[$name];
	}


	/**
	 * Get the default client name
	 * 
	 * @return string
	 */
	public function getDefaultClientName()
	{
		return $this->default_client;
	}


	/**
	 * Set the default client name
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setDefaultClientName($name)
	{
		$this->default_client = $name;
	}


	/**
	 * Add a new client
	 * 
	 * @param string $name
	 * @param \Elastica_Client $client
	 * @return void
	 */
	public function addClient($name, \Elastica_Client $client)
	{
		if ($name === null) $name = $this->default_client;
		$this->clients[$name] = $client;
	}


	/**
	 * Create a new client object and add it to the manager
	 *
	 * @param  string $name  The client name in this manager
	 * @param  string $host  The host of the elasticsearch server
	 * @param  string $port  The port the server is listening on
	 * @return \Elastica_Client
	 */
	public function createClientObject($name = null, $host = 'localhost', $port = 9200)
	{
		$client = new \Elastica_Client($host, $port);
		$this->addClient($name, $client);

		return $client;
	}

	
	/**
	 * Has the client $name been added to this manager?
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasClient($name = null)
	{
		if ($name === null) $name = $this->default_client;
		return isset($this->clients[$name]);
	}


	/**
	 * Add a new index to the manager
	 *
	 * @param \Elastica_Index $index
	 * @param string $alias An alias name for the index
	 * @return void
	 */
	public function addIndex(\Elastica_Index $index, $alias = null)
	{
		$this->indexes[$index->getName()] = $index;

		if ($alias) {
			$this->indexes[$alias] = $index;
		}
	}


	/**
	 * Add a new index object to the manager.
	 *
	 * Note: The object isnt created until it's first fetched with get()
	 *
	 * @param string $index_name   The index name
	 * @param null   $client_name  The client thats already been added to this manager
	 * @param string $index_alis   An alt name for the index
	 */
	public function createIndexObject($index_name = null, $client_name = null, $index_alias = null)
	{
		if ($index_name === null) $index = $this->default_index;

		if (isset($this->indexes[$index_name])) {
			return $this->indexes[$index_name];
		}

		if ($client_name === null) $client_name = $this->default_client;

		$index = $this->getClient($client_name)->getIndex($index_name);
		$this->addIndex($index);

		return $index;
	}


	/**
	 * Get an index
	 *
	 * @param  $name
	 * @return \Elastica_Index
	 */
	public function getIndex($name = null)
	{
		if ($name === null) $name = $this->default_index;

		if (!isset($this->indexes[$name])) {
			throw new \InvalidArgumentException("Unknown index named `$name`");
		}

		return $this->indexes[$name];
	}


	/**
	 * Get the default index name
	 *
	 * @return string
	 */
	public function getDefaultIndexName()
	{
		return $this->default_index;
	}


	/**
	 * Set the default index name
	 *
	 * @return string
	 */
	public function setDefaultIndexName($name)
	{
		return $this->default_index = $name;
	}


	/**
	 * Has index $name been added to this manager?
	 *
	 * @param $name
	 * @return bool
	 */
	public function hasIndex($name)
	{
		if ($name === null) $name = $this->default_index;
		return isset($this->indexes[$name]);
	}
}
