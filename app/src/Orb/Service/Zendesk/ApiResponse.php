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

namespace Orb\Service\Zendesk;

use Orb\Util\Arrays;
use Orb\Util\NullValue;

class ApiResponse implements \ArrayAccess
{
	/**
	 * @var int
	 */
	protected $http_code;

	/**
	 * @var string
	 */
	protected $raw;

	/**
	 * @var array
	 */
	protected $data;

	public function __construct($http_code, $raw)
	{
		$this->http_code = $http_code;
		$this->raw       = $raw;
		$this->data      = json_decode($this->raw, true);

		if (!$this->data) {
			throw new ApiException("Could not decode response", ApiException::INVALID_RESPONSE, $http_code ?: null, $this->raw);
		}
	}


	/**
	 * @return string
	 */
	public function getRaw()
	{
		return $this->raw;
	}


	/**
	 * @return int
	 */
	public function getHttpStatusCode()
	{
		return $this->http_code;
	}


	/**
	 * @return bool
	 */
	public function isSuccess()
	{
		return !$this->isError();
	}


	/**
	 * @return bool
	 */
	public function isError()
	{
		$str = (string)$this->http_code;
		if ($str[0] != '2' && $str[0] != '3') {
			return true;
		}

		return false;
	}


	/**
	 * @return string
	 */
	public function getErrorCode()
	{
		return $this->get('error', null);
	}


	/**
	 * @return string
	 */
	public function getErrorDescription()
	{
		return $this->get('description', null);
	}


	/**
	 * @return array
	 */
	public function all()
	{
		return $this->data;
	}


	/**
	 * @param string $id
	 * @return mixed
	 */
	public function get($id, $default = null)
	{
		return Arrays::keyAsPath($this->data, $id, '.', $default);
	}


	/**
	 * Check if a value is set
	 *
	 * @param string $id
	 * @return bool
	 */
	public function has($id)
	{
		$v = $this->get($id, NullValue::get());

		if (NullValue::is($v)) {
			return false;
		}

		return true;
	}


	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->data[$offset];
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException();
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException();
	}


}