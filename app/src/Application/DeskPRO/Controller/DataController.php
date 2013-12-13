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
*/

namespace Application\DeskPRO\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Util;
use Orb\Util\Arrays;

class DataController extends AbstractController
{
	public function interfaceDataAction()
	{
		$what = $this->in->getCleanValueArray('types', 'string', 'discard');

		$js = array();


		$js = implode("\n", $js);
		$response = App::getResponse();
		$response->headers->set('Content-Type', 'application/javascript');
		$response->setContent($js);

		return $response;
	}

	public function logJsErrorAction()
	{
		// The incoming data is sent from ErrorLogger.js

		$info = array(
			'message'           => $this->in->getString('message'),
			'trace'             => $this->in->getString('trace'),
			'script'            => $this->in->getString('script'),
			'line'              => $this->in->getString('line'),
			'client_user_agent' => isset($_SERVER['HTTP_REFERER'])    ? $_SERVER['HTTP_REFERER'] : '',
			'client_request'    => isset($_REQUEST)                   ? implode(', ', array_keys($_REQUEST)) : '',
			'client_fragment'   => $this->in->getString('fragment'),
		);

		\Application\DeskPRO\Service\ErrorReporter::reportJsError($info);

		return $this->createJsonResponse(array(
			'logged' => true
		));
	}

	public function sendErrorReportAction()
	{
		$error_text = $this->in->getString('error_text');

		$ip_address = dp_get_user_ip_address();
		$user_agent = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
		$referrer   = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
		$hash       = $this->in->getString('hash');

		$info = array(
			'hash' => $this->in->getString('hash'),
			'ip_address' => $ip_address,
			'user_agent' => $user_agent,
			'referrer' => $referrer,
			'fragment' => $hash,
			'comment' => $this->in->getString('comment'),
			'error_text' => $error_text,
		);

		\Application\DeskPRO\Service\ErrorReporter::sendReport('report-error-manual', array('error_summary' => 'Manually submitted error report', 'log' => $info), 15);

		return $this->createJsonResponse(array('success' => true));
	}
}
