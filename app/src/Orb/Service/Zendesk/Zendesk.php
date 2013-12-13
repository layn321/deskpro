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
 * @category Zendesk
 */

namespace Orb\Service\Zendesk;

/**
 * @see http://developer.zendesk.com/documentation/rest_api/introduction.html
 */
class Zendesk
{
	const POST   = 'POST';
	const GET    = 'GET';
	const PUT    = 'PUT';
	const DELETE = 'DELETE';

	/**
	 * @var string
	 */
	protected $api_key;

	/**
	 * @var string
	 */
	protected $user_id;

	/**
	 * @var string
	 */
	protected $zendesk_url;

	/**
	 * @var int
	 */
	protected $timeout = 10;

	/**
	 * @var array
	 */
	protected $listeners = array();


	/**
	 * Get your $api_key from Settings > Channels > API.
	 * If you use your password instead of a token, prefix it with password:. Ex: $api_key = "password:secretpassword".
	 *
	 * The user is your user account that all calls will be made from (your email address).
	 *
	 * @param string $zendesk_url   The API endpoint. You may pass just a domain name to use the default v2 API endpoint.
	 * @param string $user_id       The user to use
	 * @param string $api_key       API Key
	 */
	public function __construct($zendesk_url, $user_id, $api_key)
	{
		// "password:" prefix is our own invention so we
		// know its not a token,
		if (preg_match('#^password:#', $api_key)) {
			$api_key = preg_replace('#^password:#', '', $api_key);
		} else {
			// If its a token we need to ensure the token: prefix
			if (!preg_match('#^token:#', $api_key)) {
				$api_key = "token:$api_key";
			}
		}

		$this->api_key = $api_key;
		$this->user_id = $user_id;

		// Not a URL, assume we got just a domain
		if (!preg_match('#^https?://#', $zendesk_url)) {
			$zendesk_url = 'https://' . $zendesk_url . '/api/v2';
		} else {
			$zendesk_url = rtrim($zendesk_url, '/');
		}

		$this->zendesk_url = $zendesk_url;
	}


	/**
	 * @return string
	 */
	public function getZendeskUrl()
	{
		return $this->zendesk_url;
	}


	/**
	 * @return string
	 */
	public function getZendeskApiUserId()
	{
		return $this->user_id;
	}


	/**
	 * @return string
	 */
	public function getZendeskApiKey($token_prefix = true)
	{
		if ($token_prefix) {
			return $this->api_key;
		}

		return preg_replace('#^token:#', '', $this->api_key);
	}


	/**
	 * Add a callback function to listen to events. Mainly useful for logging.
	 *
	 * The callbacks are passed an array and must return the same array
	 * (with any changes).
	 *
	 * @param callable $callback
	 */
	public function addListener($callback)
	{
		$this->listeners[] = $callback;
	}


	/**
	 * @param int $timeout
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = (int)$timeout;
	}


	/**
	 * @param string $id
	 * @return string
	 */
	public function getUrlForEndpoint($id)
	{
		return $this->zendesk_url . '/' . $id . '.json';
	}


	/**
	 * Send a GET request
	 *
	 * @return \Orb\Service\Zendesk\ApiResponse
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @throws \Orb\Service\Zendesk\ApiException
	 */
	public function sendGet($id, array $query_data = null)
	{
		return $this->sendRequest($id, self::GET, null, $query_data);
	}


	/**
	 * @param array $requests Array of array($id, $query_data) to be called async
	 * @return array Array of results
	 */
	public function sendGetMulti(array $requests)
	{
		$request_ev = array();

		$mh = curl_multi_init();

		foreach ($requests as $k => $req) {
			if (!is_array($req)) {
				$req = array($req, null);
			}

			$ev = $this->sendRequest($req[0], self::GET, null, $req[1], true);
			$request_ev[$k] = $ev;

			$ev = $this->_callListeners('preCall', $ev);

			curl_multi_add_handle($mh, $ev['ch']);
		}

		do {
			curl_multi_exec($mh, $running);
			usleep(25000);
		} while ($running > 0);

		foreach ($request_ev as $k => &$ev) {
			$ev['output'] = @curl_multi_getcontent($ev['ch']);
			$ev['http_code'] = @curl_getinfo($ev['ch'], CURLINFO_HTTP_CODE);
			$ev = $this->_callListeners('postCall', $ev);

			$ev['exception'] = null;
			$ev['response'] = null;

			if (@curl_errno($ev['ch'])) {
				$ev['exception'] = new \RuntimeException(sprintf("cURL Error: %s: %s", curl_errno($ev['ch']), curl_error($ev['ch'])));
			}

			if ($ev['output'] === false || !$ev['http_code']) {
				$ev['exception'] = new ApiException("Request failed", ApiException::REQUEST_FAILED, null, $ev['output']);
			}

			if (!$ev['exception']) {
				try {
					$response = new ApiResponse($ev['http_code'], $ev['output']);
					$ev['response'] = $response;
				} catch (ApiException $e) {
					$ev['response'] = null;
					$ev['exception'] = $e;
				}
				$ev = $this->_callListeners('postResponse', $ev);
			}

			curl_multi_remove_handle($mh, $ev['ch']);
			unset($ev['ch']);
		}
		unset($ev);

		curl_multi_close($mh);

		return $request_ev;
	}


	/**
	 * Just like sendGet except this will attempt to build a complete collection
	 * by re-calling the 'next_page' and appending results.
	 *
	 * This returns an ARRAY of all results.
	 *
	 * @return array
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @throws \Orb\Service\Zendesk\ApiException
	 */
	public function sendGetAll($id, $key, array $query_data = null)
	{
		$result = array();

		$next_id = $id;
		$next_params = $query_data;
		while ($next_id) {
			$res = $this->sendRequest($next_id, self::GET, null, $next_params);
			$next_params = null;

			if ($res->isError()) {
				throw new ApiException(
					"Could not complete: " . $res->getErrorDescription(),
					ApiException::API_ERROR,
					$res->getErrorCode(),
					$res->getRaw()
				);
			}

			if ($collection = $res->get($key)) {
				$result = array_merge($result, $collection);
			}

			$next_id = $res->get('next_page');
		}

		return $result;
	}


	/**
	 * Send a DELETE request
	 *
	 * @return \Orb\Service\Zendesk\ApiResponse
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @throws \Orb\Service\Zendesk\ApiException
	 */
	public function sendDelete($id)
	{
		return $this->sendRequest($id, self::DELETE, null);
	}


	/**
	 * Send a PUT request
	 *
	 * @param string $id
	 * @param array $call_data
	 * @return \Orb\Service\Zendesk\ApiResponse
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @throws \Orb\Service\Zendesk\ApiException
	 */
	public function sendPut($id, array $call_data)
	{
		return $this->sendRequest($id, self::PUT, $call_data);
	}


	/**
	 * Send a GET request
	 *
	 * @param string $id
	 * @param array $call_data
	 * @return \Orb\Service\Zendesk\ApiResponse
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @throws \Orb\Service\Zendesk\ApiException
	 */
	public function sendPost($id, array $call_data)
	{
		return $this->sendRequest($id, self::POST, $call_data);
	}


	/**
	 * Send an API request
	 *
	 * @param string $id
	 * @param string $action
	 * @param array  $call_data   The set
	 * @return \Orb\Service\Zendesk\ApiResponse
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @throws \Orb\Service\Zendesk\ApiException
	 */
	public function sendRequest($id, $action, array $call_data = null, array $query_data = null, $no_exec = false)
	{
		$ev_data = array(
			'id'         => $id,
			'action'     => $action,
			'call_data'  => $call_data,
			'query_data' => $query_data
		);

		$ev_data = $this->_callListeners('preInit', $ev_data);
		extract($ev_data, \EXTR_OVERWRITE);

		#------------------------------
		# Set up cURL
		#------------------------------

		if ($call_data) {
			$call_json = json_encode($call_data);
			if (!$call_json) {
				throw new \InvalidArgumentException("Could not encode call data", -1);
			}
		} else {
			$call_json = '[]';
		}

		$query_string = '';
		if ($query_data) {
			$query_string = http_build_query($query_data, null, '&');
		}

		if (preg_match('#^https?://#', $id)) {
			$url = $id;
		} else {
			$url = $this->getUrlForEndpoint($id);
		}

		if ($query_string) {
			$url .= '?' . $query_string;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERPWD, $this->user_id."/".$this->api_key);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_USERAGENT, "DeskPRO_Orb/1.0");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

		switch($action){
			case self::POST:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $call_json);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length: ' . strlen($call_json))
				);

				break;
			case self::GET:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
				break;
			case self::PUT:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $call_json);
				break;
			case self::DELETE:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			default:
				throw new \InvalidArgumentException("Invalid request action `$action` must be one of: POST, GET, PUT, DELETE", -1);
				break;
		}

		#------------------------------
		# Make the call
		#------------------------------

		$ev_data['url']       = $url;
		$ev_data['call_json'] = $call_json;
		$ev_data['ch']        = $ch;

		$ev_data = $this->_callListeners('preCall', $ev_data);
		extract($ev_data, \EXTR_OVERWRITE);

		if ($no_exec) {
			return $ev_data;
		}

		$output    = curl_exec($ch);
		$http_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);

		$ev_data['output']    = $output;
		$ev_data['http_code'] = $http_code;
		$ev_data = $this->_callListeners('postCall', $ev_data);
		extract($ev_data, \EXTR_OVERWRITE);

		if (curl_errno($ch)) {
			throw new \RuntimeException(sprintf("cURL Error: %s: %s", curl_errno($ch), curl_error($ch)));
		}

		if ($output === false || !$http_code) {
			throw new ApiException("Request failed", ApiException::REQUEST_FAILED, null, $output);
		}

		curl_close($ch);

		$response = new ApiResponse($http_code, $output);

		$ev_data['response'] = $response;
		$ev_data = $this->_callListeners('postResponse', $ev_data);
		extract($ev_data, \EXTR_OVERWRITE);

		return $response;
	}


	/**
	 * @param array $ev_data
	 * @return array
	 */
	protected function _callListeners($event_name, array $ev_data)
	{
		foreach ($this->listeners as $l) {
			$ev_data = call_user_func($l, $event_name, $ev_data);
		}

		return $ev_data;
	}
}