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
 * @category Controller
 */

namespace Application\ApiBundle\StaticLoader;

use Application\DeskPRO\App;

class RequestKey
{
	public static function getApiKeyFromRequest()
	{
		$em = App::getOrm();
		$request = App::getRequest();

		static $api_key = null;

		if ($api_key !== null) {
			return $api_key;
		}

		$headers = $request->server->getHeaders();
		$key_str = false;
		if ($request->headers->get('X-DeskPRO-API-Key', null, true)) {
			$key_str = $request->headers->get('X-DeskPRO-API-Key', null, true);
		} else if (!empty($_REQUEST['API-KEY'])) {
			$key_str = $_REQUEST['API-KEY'];
		} else if (!empty($headers['PHP_AUTH_USER']) AND !empty($headers['PHP_AUTH_PW'])) {
			$key_str = $headers['PHP_AUTH_USER'].':'.$headers['PHP_AUTH_PW'];
		}

		if (!$key_str) {
			$api_key = false;
			return false;
		}

		$api_key = $em->getRepository('DeskPRO:ApiKey')->findByKeyString($key_str);
		if ($api_key) {
			return $api_key;
		}

		$api_key = false;
		return false;
	}

	public static function getApiTokenFromRequest()
	{
		$em = App::getOrm();
		$request = App::getRequest();

		static $api_token = null;

		if ($api_token !== null) {
			return $api_token;
		}

		$headers = $request->server->getHeaders();
		$token_str = false;
		if ($request->headers->get('X-DeskPRO-API-Token', null, true)) {
			$token_str = $request->headers->get('X-DeskPRO-API-Token', null, true);
		} else if (!empty($_REQUEST['API-TOKEN'])) {
			$token_str = $_REQUEST['API-TOKEN'];
		} else if (!empty($headers['PHP_AUTH_USER']) AND !empty($headers['PHP_AUTH_PW'])) {
			$token_str = $headers['PHP_AUTH_USER'].':'.$headers['PHP_AUTH_PW'];
		}

		if (!$token_str) {
			$api_token = false;
			return false;
		}

		$api_token = $em->getRepository('DeskPRO:ApiToken')->findByTokenString($token_str);
		if ($api_token) {
			return $api_token;
		}

		$api_token = false;
		return false;
	}
}
