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
 * @category Util
 */

namespace Orb\Util;

/**
 * A utility class for working with HTTP/Web related tasks such as cookies or sending headers.
 *
 * @static
 */
class Web
{
	const HTTP_STATUS_OK = 200;
	const HTTP_STATUS_BAD_REQUEST = 400;
	const HTTP_STATUS_NOT_FOUND = 404;
	const HTTP_STATUS_FORBIDDEN = 403;
	const HTTP_STATUS_MOVED_PERM = 301;
	const HTTP_STATUS_SERVER_ERR = 500;

	/**
	 * Redirect to a given URL and then halt the script. Will send the location
	 * header if headers haven't been sent, otherwise it will attempt to output
	 * HTML/Javascript to execute the redirect. Will exit the script
	 * afterwards.
	 *
	 * @param string $url The URL to redirect to.
	 */
	static public function redirect($url)
	{
		// Headers already sent, this is the best we can do
		if (headers_sent()) {
			$html = '<meta http-equiv="refresh" content="0;url='.$url.'" />';
			$html .= '<script type="text/javascript">window.location="'.Orb_String::addslashesJs($url).'";</script>';
			echo $html;

		// Standard header
		} else {
			header('Location: ' . Strings::getFirstLine($url));
		}

		exit;
	}



	/**
	 * Send an HTTP status code.
	 *
	 * @param int $type One of the HTTP_STATUS_* constants.
	 * @return bool True if sent, false if it couldnt be sent
	 */
	static function sendHttpStatus($type)
	{
		if (headers_sent()) return false;

		switch ($type) {
			case self::HTTP_STATUS_OK: header('HTTP/1.1 200 OK'); break;
			case self::HTTP_STATUS_BAD_REQUEST: header('HTTP/1.1 400 Bad Request'); break;
			case self::HTTP_STATUS_NOT_FOUND: header('HTTP/1.1 404 Not Found'); break;
			case self::HTTP_STATUS_FORBIDDEN: header('HTTP/1.1 403 Forbidden'); break;
			case self::HTTP_STATUS_MOVED_PERM: header('HTTP/1.1 301 Moved Permanently'); break;
			case self::HTTP_STATUS_SERVER_ERR: header('HTTP/1.1 500 Internal Server Error'); break;
			default: return false; break;
		}

		return true;
	}



	/**
	 * Get some default headers for serving an attachment.
	 *
	 * @param string $filename   The filename
	 * @param bool   $is_inline  Should the file be served inline
	 * @param string $mimetype   The mimetype
	 * @param int    $filesize   The filesize
	 * @return array
	 */
	public function getAttachmentHeaders($filename, $is_inline = false, $mimetype = null, $filesize = null)
	{
		$headers = array();

		if (!$filename) {
			$filename = 'file';
		}

		$disp = 'inline';
		if (!$is_inline) {
			$disp = 'attachment';
		}

		$headers['Content-Disposition'] = 'inline; filename="' . str_replace('"', '\\"', $filename) . '"';

		if ($mimetype !== null) {
			$headers['Content-Type'] = $mimetype;
		}

		if ($filesize !== null) {
			$headers['Content-Length'] = $filesize;
		}

		return $headers;
	}



	/**
	 * Sets a cookie. Will get values for $path and $domain from Zend_Registry if they are set (cookie_path
	 * and cookie_domain as keys).
	 *
	 * @param  string  $name	  The name of the cookie
	 * @param  string  $value	  The value of the cookie
	 * @param  int     $expire	  A timestamp or a string compatible with strtotime, or 'never' for a far far away time
	 * @param  bool	   $httponly  Make this a HTTP-only cookie
	 * @param  string  $path	  The path to set the cookie for
	 * @param  string  $domain	  The domain to set the cookie for
	 * @param  bool	   $secure	  To make this a secure cookie
	 */
	static public function setCookie($name, $value, $expire = 'next week', $httponly = true, $secure = null, $path = null, $domain = null)
	{
		// Not a timestamp
		if ($expire === null) {
			$expire = false;
		} else {
			if (!ctype_digit($expire)) {
				if ($expire == 'never') {
					$expire = mktime(0, 0, 0, 0, 0, 2020);
				} else {
					$expire = strtotime($expire);

					if (!$expire) {
						$expire = null;
						throw new \Exception('Unknown expire format: ' . $expire);
					}
				}
			}
		}

		if ($path === null) {
			$path = '/';
		}

		return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}



	/**
	 * Get the request protocol. Returns either 'HTTP' or 'HTTPS'.
	 *
	 * @return string
	 */
	public static function getRequestProtocol()
	{
		static $request_protocol = null;

		if ($request_protocol === null) {
			if (isset($_SERVER['HTTPS']) AND !empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] != 'off') {
				$request_protocol = 'HTTPS';
			} else {
				$request_protocol = 'HTTP';
			}
		}

		return $request_protocol;
	}



	/**
	 * Get the users IP address.
	 *
	 * @return string
	 */
	public static function getUserHostname()
	{
		return @gethostbyaddr(self::getUserIp());
	}



	/**
	 * Get the users IP address.
	 *
	 * @return string
	 */
	public static function getUserIp()
	{
		return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
	}



	/**
	 * Get the users alternative IP address. For example, the IP address forwarded by the proxy.
	 * This method does not always return a value, or a correct value.
	 *
	 * @return string
	 */
	public static function getUserIpAlt()
	{
		$alt_ip = null;

		if ($alt_ip === null) {

			if (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$alt_ip = $_SERVER['HTTP_CLIENT_IP'];
			}

			if (!$alt_ip AND isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip_arr = array();

				if (preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $ip_arr)) {
					foreach($ip_arr[0] AS $ip) {
						if (!preg_match("#^(10|172\.16|192\.168)\.#", $ip)) {
							$alt_ip = $ip;
							break;
						}
					}
				}
			}

			if (!$alt_ip AND isset($_SERVER['HTTP_FROM'])) {
				$alt_ip = $_SERVER['HTTP_FROM'];
			}

			if (!$alt_ip AND isset($_SERVER['REMOTE_ADDR'])) {
				$alt_ip = $_SERVER['REMOTE_ADDR'];
			}
		}

		return $alt_ip;
	}



	/**
	 * Get the users browser useragent.
	 *
	 * @return string
	 */
	public static function getUserAgent()
	{
		return (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
	}



	/**
	 * Get the referrer for the current request.
	 *
	 * @return string
	 */
	public static function getScriptReferrer()
	{
		return (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
	}



	/**
	 * Get the web path to the current request.
	 *
	 * @return string
	 */
	public static function getScriptPath()
	{
		// The URL
		if (isset($_SERVER['REQUEST_URI']) AND $_SERVER['REQUEST_URI']) {
			$script_path = $_SERVER['REQUEST_URI'];
		} else	{
			if (isset($_SERVER['PATH_INFO']) AND $_SERVER['PATH_INFO']) {
				$script_path = $_SERVER['PATH_INFO'];

			} elseif (isset($_SERVER['REDIRECT_URL']) AND $_SERVER['REDIRECT_URL']) {
				$script_path = $_SERVER['REDIRECT_URL'];

			} elseif (isset($_SERVER['PHP_SELF']) AND $_SERVER['PHP_SELF']) {
				$script_path = $_SERVER['PHP_SELF'];
			}

			if (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING']) {
				$script_path .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		$quest_pos = strpos($script_path, '?');

		if ($quest_pos !== false) {
			$script = urldecode(substr($script_path, 0, $quest_pos));
			$script_path = $script . substr($script_path, $quest_pos);
		} else {
			$script_path = urldecode($script_path);
		}

		return $script_path;
	}



	/**
	 * Get the request method. POST, GET, PUT, DELETE etc.
	 *
	 * @return string
	 */
	public static function getRequestMethod()
	{
		return strtoupper((isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : ''));
	}



	/**
	 * Look up a users country based off of their IP address. Returns null if no country could be found.
	 *
	 * @param	string	$ip	 The IP address. Use null if you want to use the current users IP address.
	 * @return	string
	 */
	public static function getCountryFromIp($ip = null)
	{
		if (!$ip) {
			$ip = Web::getUserIp();
		}

		$country = null;
		if (function_exists('geoip_country_code_by_name')) {
			$country = @geoip_country_code_by_name($ip);
		}

		if (!$country) {
			return null;
		}

		return strtoupper($country);
	}



	/**
	 * Checks a URL to see if it exists (that it returns a 200 OK, not a 404 etc).
	 *
	 * @param string $url The URL to check
	 * @return bool
	 */
	public static function urlExists($url)
	{
		$url_parts = @parse_url($url);

		if (empty($url_parts['host'])) {
			return false;
		}

		if (empty($url_parts['path'])) {
			$url_parts['path'] = '/';
		}

		if (empty($url_parts['query'])) {
			$url_parts['query'] = '';
		} else {
			$url_parts['query'] = '?' . $url_parts['query'];
		}

		if (empty($url_parts['port'])) {
			$url_parts['port'] = '80';
		}

		$errno = $errstr = null;
		$socket = @fsockopen(
			$url_parts['host'],
			$url_parts['port'],
			$errno,
			$errstr,
			10
		);

		if (!$socket) {
			return false;
		}

		@fwrite($socket, "HEAD {$url_parts['path']}{$url_parts['query']} HTTP/1.0\r\nHost: {$url_parts['host']}\r\n\r\n");
		$http_response = @fgets($socket, 22);

		$ret = false;
		if (Strings::isIn('200 OK', $http_response)) {
			$ret = true;
		}

		@fclose($socket);

		return $ret;
	}


	/**
	 * Check if a useragent is a known bot
	 *
	 * @param string $useragent The user agent to check or null to use the current request
	 * @return bool
	 */
	public static function isBotUseragent($useragent = null)
	{
		if ($useragent === null) {
			if (!isset($_SERVER['HTTP_USER_AGENT'])) {
				return false;
			}

			$useragent = $_SERVER['HTTP_USER_AGENT'];
		}

		$bot_strings = array(
			'AdsBot-Google', 'Googlebot-Image', 'Googlebot-Mobile', 'Googlebot',
			'Yahoo! Slurp', 'Yahoo! Slurp China', 'Yahoo-MMCrawler',
			'Openbot',
			'msnbot', 'msnbot-NewsBlogs',
			'ia_archiver',
			'Lycos',
			'Scooter',
			'AltaVista',
			'Ask Jeeves/Teoma', 'Teoma',
			'Gigabot',
			'bingbot'
		);

		foreach ($bot_strings as $bot) {
			if (strpos($useragent, $bot) !== false) {
				return true;
			}
		}

		return false;
	}
}
