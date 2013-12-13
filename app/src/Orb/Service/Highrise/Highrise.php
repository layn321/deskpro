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
 * @subpackage Service
 * @category Highrise
 */

namespace Orb\Service\Highrise;

/**
 * The interface to all Highrise API usage. Specific actions are delegated to Resource
 * classes.
 *
 * @see http://developer.37signals.com/highrise/
 * @property \Orb\Service\Highrise\Resource\Person person
 */
class Highrise
{
	/**
	 * The company highrise URL without trailing slash. Example: http://mycompany.highrisehq.com
	 * @var string
	 */
	protected $highrise_url;

	/**
	 * The auth token for the user.
	 * @var string
	 */
	protected $auth_token;

	/**
	 * Instantiated resources
	 * @see __get
	 * @var array
	 */
	protected $resources = array();

	/**
	 * HTTP Client we'll use
	 * @var \Zend\Http\Client
	 */
	protected $http;

	public function __construct($highrise_url, $auth_token)
	{
		$this->highrise_url = $highrise_url;
		$this->auth_token = $auth_token;
	}



	/**
	 * Send a GET request.
	 *
	 * @param string $resource The resource to fetch with leading slash. Will be prepended with highrise url.
	 * @param array $params Any GET params to specify
	 * @return \Zend\Http\Response
	 */
	public function sendReadRequest($resource, array $params = array())
	{
		$resource_url = $this->highrise_url . $resource;

		$http = $this->getHttpClient();
		$http->setUri($resource_url);
		if ($params) {
			$http->setParameterGet($params);
		}
		$http->setMethod('GET');

		return $http->send();
	}



	/**
	 * Send a POST request.
	 *
	 * @param string $resource The resource to fetch with leading slash. Will be prepended with highrise url.
	 * @param string $postdata The POST data to submit. Highrise expects an XML string.
	 * @param array $get_params Any GET params to specify
	 * @return \Zend\Http\Response
	 */
	public function sendWriteRequest($resource, $postdata, array $get_params = array(), $use_put = false)
	{
		$resource_url = $this->highrise_url . $resource;

		$http = $this->getHttpClient();
		$http->setUri($resource_url);
		if ($get_params) {
			$http->setParameterGet($get_params);
		}
		$http->setEncType('application/xml');
		$http->setRawBody($postdata);

		if ($use_put) {
			$http->setMethod('PUT');
			return $http->send();
		} else {
			$http->setMethod('POST');
			return $http->send();
		}
	}



	/**
	 * Send a PUT request.
	 *
	 * @param string $resource The resource to fetch with leading slash. Will be prepended with highrise url.
	 * @param string $postdata The POST data to submit. Highrise expects an XML string.
	 * @param array $get_params Any GET params to specify
	 * @return \Zend\Http\Response
	 */
	public function sendPutRequest($resource, $postdata, array $get_params = array(), $put = false)
	{
		return $this->sendWriteRequest($resource, $postdata, $get_params, true);
	}



	/**
	 * Send a DELETE request.
	 *
	 * @param string $resource The resource to fetch with leading slash. Will be prepended with highrise url.
	 * @param array $params Any GET params to specify
	 * @return \Zend\Http\Response
	 */
	public function sendDeleteRequest($resource, array $params = array())
	{
		$resource_url = $this->highrise_url . $resource;

		$http = $this->getHttpClient();
		$http->setUri($resource_url);
		if ($params) {
			$http->setParameterGet($params);
		}
		$http->setMethod('DELETE');

		return $http->send();
	}



	/**
	 * Set the HTTPclient to use. If null, a default client will be set.
	 *
	 * @param \Zend\Http\Client|null $http
	 */
	public function setHttpClient(\Zend\Http\Client $http = null)
	{
		if ($http === null) {
			$http = new \Zend\Http\Client();
		}

		$this->http = $http;
	}



	/**
	 * Get the HTTP client to use
	 *
	 * @return \Zend\Http\Client
	 */
	public function getHttpClient()
	{
		if ($this->http === null) $this->setHttpClient();

		$this->http->resetParameters();
		$this->http->setAuth($this->auth_token, 'X');

		return $this->http;
	}



	/**
	 * Read in values from XML into a native PHP array.
	 *
	 * @param string $xml XML doc as a string, or SimpleXmlElement
	 * @return array
	 */
	public function xmlToArray($xml)
	{
		if (is_string($xml)) {
			$xml = new \SimpleXMLElement($xml);
		} elseif (!($xml instanceof \SimpleXMLElement)) {
			throw new \InvalidArgumentException('$xml must be a XML string or SimpleXMLElement');
		}

		$array = array();

		$complex_types = array(
			'contact-data',
		);
		$collection_types = array(
			'email-addresses',
			'phone-numbers',
			'addresses',
			'instant-messengers',
			'web-addresses',
		);

		foreach ($xml->children() as $nodename => $node) {
			if (in_array($nodename, $complex_types)) {
				$array[$nodename] = $this->xmlToArray($node);
			} elseif (in_array($nodename, $collection_types)) {
				$array[$nodename] = array();
				foreach ($node->children() as $subnode) {
					$array[$nodename][] = $this->xmlToArray($subnode);
				}
			} else {
				$text = trim((string)$node);
				if ($text !== '') {
					$array[$nodename] = $text;
				}
			}
		}

		return $array;
	}


	/**
	 * Export the values to XML.
	 *
	 * @return string
	 */
	public function arrayToXml(array $array, $base_nodename)
	{
		$xml = array();
		$xml[] = "<{$base_nodename}>";

		foreach ($array as $nodename => $node) {
			if (strpos('-id', $nodename) !== false) {
				$xml[] = "<{$nodename} type=\"integer\">" . ((int)$node) . "</{$nodename}>";
			} elseif (strpos('-at', $nodename) !== false) {
				$xml[] = "<{$nodename} type=\"datetime\">" . ((string)$node) . "</{$nodename}>";
			} elseif (strpos('-on', $nodename) !== false) {
				$xml[] = "<{$nodename} type=\"date\">" . ((string)$node) . "</{$nodename}>";
			} elseif (is_array($node)) {
				$xml[] = $this->arrayToXml($node, $nodename);
			} else {
				$xml[] = "<{$nodename}>" . ((string)$node) . "</{$nodename}>";
			}
		}

		$xml[] = "</{$base_nodename}>";

		return implode("\n", $xml);
	}



	/**
	 * Dynamically get resource objects.
	 *
	 * @param string $name
	 * @return \Orb\Service\Highrise\Resource\AbstractResource
	 */
	public function __get($name)
	{
		if (isset($this->resources[$name])) {
			return $this->resources[$name];
		}

		$classname = str_replace($name, '_', '-');
		$classname = \Orb\Util\Strings::dashToCamelCase($classname);
		$classname = 'Orb\\Service\\Highrise\\Resource\\' . ucfirst($classname);

		$obj = new $classname($this);
		$this->resources[$name] = $obj;

		return $obj;
	}
}
