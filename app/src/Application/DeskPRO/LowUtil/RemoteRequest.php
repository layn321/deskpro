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
 * @subpackage LowUtil
 */

/**
 * Makes a request or downloads a file
 */
interface DeskPRO_LowUtil_Requester
{
	/**
	 * Download a remote file to the fs
	 *
	 * @param  string $url        The URL to fetch
	 * @param  string $save_path  The path to save to
	 * @param  int    $timeout    Timeout
	 * @return int Filesize
	 */
	public function download($url, $save_path, $timeout = 15);

	/**
	 * Make a web request
	 *
	 * @param  string $url        The URL to fetch
	 * @param  string $save_path  The path to save to
	 * @param  int    $timeout    Timeout
	 * @return string
	 */
	public function request($url, array $data = array(), $method = 'GET', $timeout = 15);
}

########################################################################################################################

/**
 * Fetcher that uses any supported fetcher to do actual work
 */
class DeskPRO_LowUtil_RemoteRequester implements DeskPRO_LowUtil_Requester
{
	const STRATEGY_CURL   = 'curl';
	const STRATEGY_NATIVE = 'native';

	/**
	 * @var DeskPRO_LowUtil_Requester
	 */
	protected $requester;

	public function __construct()
	{
		$strategy = self::detectStrategy();

		if (!$strategy) {
			throw new DeskPRO_LowUtil_Fetch_Exception("No supported fetchers", DeskPRO_LowUtil_Fetch_Exception::NO_SUPPORTED_FETCHER);
		}

		switch ($strategy) {
			case 'curl':
				$this->requester = new DeskPRO_LowUtil_RequestCurl();
				break;
			case 'native':
				$this->requester = new DeskPRO_LowUtil_RequestNative();
				break;
			default:
				throw new UnexpectedValueException();
		}
	}

	/**
	 * @static
	 * @return DeskPRO_LowUtil_RemoteRequester
	 */
	public static function create()
	{
		return new self();
	}

	/**
	 * Detect which strategy to use to fetch the file, or null if no supported strategy exists.
	 *
	 * @return string
	 */
	public static function detectStrategy()
	{
		if (function_exists('curl_init')) {
			return 'curl';
		} elseif (in_array(ini_get('allow_url_fopen'), array('1', 'On'))) {
			return 'native';
		} else {
			return null;
		}
	}

	/**
	 * @return DeskPRO_LowUtil_Requester
	 */
	public function getRequester()
	{
		return $this->requester;
	}


	/**
	 * {@inheritdoc}
	 */
	public function download($url, $save_path, $timeout = 15)
	{
		return $this->requester->download($url, $save_path, $timeout);
	}


	/**
	 * {@inheritdoc}
	 */
	public function request($url, array $data = array(), $method = 'GET', $timeout = 15)
	{
		return $this->requester->request($url, $data, $method, $timeout);
	}
}

########################################################################################################################

class DeskPRO_LowUtil_RequestCurl implements DeskPRO_LowUtil_Requester
{
	public function __construct()
	{
		if (!function_exists('curl_init')) {
			throw new DeskPRO_LowUtil_Fetch_Exception("curl is not supported on your server", DeskPRO_LowUtil_Fetch_Exception::FETCHER_UNSUPPORTED);
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function download($url, $save_path, $timeout = 15)
	{
		$fp = @fopen($save_path, 'w');
		if (!$fp) {
			throw new DeskPRO_LowUtil_Fetch_Exception("Failed to open stream for writing", DeskPRO_LowUtil_Fetch_Exception::WRITE_FAILED);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_exec($ch);
		fflush($fp);
		fclose($fp);

		if (curl_error($ch)) {
			$e = new DeskPRO_LowUtil_Fetch_Exception(sprintf("Curl error: %s %s", curl_errno($ch), curl_error($ch)), DeskPRO_LowUtil_Fetch_Exception::REQUEST_FAILED);
			@curl_close($ch);
			throw $e;
		}

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code != 200) {
			throw new DeskPRO_LowUtil_Fetch_Exception(sprintf("Server returned a non-success response code: %s", $http_code), DeskPRO_LowUtil_Fetch_Exception::REQUEST_FAILED);
		}

		return filesize($save_path);
	}


	/**
	 * {@inheritdoc}
	 */
	public function request($url, array $data = array(), $method = 'GET', $timeout = 15)
	{
		$ch = curl_init();

		$method = strtoupper($method);
		if ($method == 'GET') {
			if ($data) {
				if (strpos('?', $url) !== false) {
					$url .= '&';
				} else {
					$url .= '?';
				}

				$url .= http_build_query($data, '', '&');
			}
			curl_setopt($ch, CURLOPT_URL, $url);
		} else {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$result = curl_exec($ch);

		if (curl_error($ch)) {
			$e = new DeskPRO_LowUtil_Fetch_Exception(sprintf("Curl error: %s %s", curl_errno($ch), curl_error($ch)), DeskPRO_LowUtil_Fetch_Exception::REQUEST_FAILED);
			@curl_close($ch);

			throw $e;
		}

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code != 200) {
			throw new DeskPRO_LowUtil_Fetch_Exception(sprintf("Server returned a non-success response code: %s", $http_code), DeskPRO_LowUtil_Fetch_Exception::REQUEST_FAILED);
		}

		return $result;
	}
}

########################################################################################################################

class DeskPRO_LowUtil_RequestNative implements DeskPRO_LowUtil_Requester
{
	public function __construct()
	{
		if (!in_array(ini_get('allow_url_fopen'), array('1', 'On'))) {
			throw new DeskPRO_LowUtil_Fetch_Exception("allow_url_include is disabled in your php.ini, you cannot use native functions to download remote files", DeskPRO_LowUtil_Fetch_Exception::FETCHER_UNSUPPORTED);
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function download($url, $save_path, $timeout = 15)
	{
		$context = stream_context_create(array(
			'http' => array('timeout' => $timeout)
		));
		$res = @copy($url, $save_path, $context);

		if (!$res) {
			throw new DeskPRO_LowUtil_Fetch_Exception("Failed downloading remote file", DeskPRO_LowUtil_Fetch_Exception::WRITE_FAILED);
		}

		if (!$this->isSuccessResponse($http_response_header)) {
			throw new DeskPRO_LowUtil_Fetch_Exception("Server returned a non-success response code", DeskPRO_LowUtil_Fetch_Exception::REQUEST_FAILED);
		}

		return filesize($save_path);
	}


	/**
	 * {@inheritdoc}
	 */
	public function request($url, array $data = array(), $method = 'GET', $timeout = 15)
	{

		$method = strtoupper($method);
		if ($method == 'GET') {
			if ($data) {
				if (strpos('?', $url) !== false) {
					$url .= '&';
				} else {
					$url .= '?';
				}

				$url .= http_build_query($data, '', '&');
			}

			$context = stream_context_create(array(
				'http' => array(
					'timeout'  => $timeout,
				)
			));
		} else {
			$context = stream_context_create(array(
				'http' => array(
					'timeout'  => $timeout,
					'method'   => 'POST',
					'header'   => 'Content-type: application/x-www-form-urlencoded',
					'content'  => http_build_query($data, null, '&')
				)
			));
		}

		$res = @file_get_contents($url, null, $context);

		// $http_response_header is the magic var that'll have the headers from the above web request
		// But if its not set, then it means the request failed

		if (!isset($http_response_header) || !$this->isSuccessResponse($http_response_header)) {
			throw new DeskPRO_LowUtil_Fetch_Exception("Server returned a non-success response code", DeskPRO_LowUtil_Fetch_Exception::REQUEST_FAILED);
		}

		return $res;
	}

	protected function isSuccessResponse(array $http_headers)
	{
		$string = implode("\n", $http_headers);

		if (strpos($string, '200 OK') === false) {
			return false;
		}

		return true;
	}
}

########################################################################################################################

class DeskPRO_LowUtil_Fetch_Exception extends Exception
{
	const NO_SUPPORTED_FETCHER = 1;
	const FETCHER_UNSUPPORTED  = 2;
	const TIMEOUT              = 3;
	const WRITE_FAILED         = 4;
	const REQUEST_FAILED       = 5;
}