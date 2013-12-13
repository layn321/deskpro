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
 * @subpackage Form
 */

namespace Application\DeskPRO\Form\Captcha;

use DeskPRO\Kernel\KernelErrorHandler;
use Orb\Util\Strings;

class Recaptcha extends CaptchaAbstract
{
	const RECAPTCHA_VERIFY_URL = 'http://www.google.com/recaptcha/api/verify';

	/**
	 * @var string
	 */
	protected $public_key = null;

	/**
	 * @var string
	 */
	protected $private_key = null;

	public function init()
	{
		$this->setOptions(array(
			'template' => 'DeskPRO:Common:recaptcha.html.twig'
		));

		$this->public_key  = $this->getOptionOrSetting('public_key', 'core.recaptcha_public_key');
		$this->private_key = $this->getOptionOrSetting('private_key', 'core.recaptcha_private_key');
	}

	public function getHtml()
	{
		$tpl = $this->getOption('template');
		$vars = array(
			'public_key' => $this->public_key
		);

		return $this->getTemplating()->render($tpl, $vars);
	}

	public function validate()
	{
		$challenge = $this->getRequest()->request->get('recaptcha_challenge_field');
		$response  = $this->getRequest()->request->get('recaptcha_response_field');
		$remote_ip = dp_get_user_ip_address();

		if (!$challenge || !$response || !$remote_ip) {
			return false;
		}

		$client = new \Zend\Http\Client(self::RECAPTCHA_VERIFY_URL);
		$client->setMethod(\Zend\Http\Request::METHOD_POST);
		$client->getRequest()->post()->set('privatekey', $this->private_key);
		$client->getRequest()->post()->set('remoteip', $remote_ip);
		$client->getRequest()->post()->set('challenge', $challenge);
		$client->getRequest()->post()->set('response', $response);

		try {
			$r_response = $client->send();
			$r_body = $r_response->getBody();
		} catch (\Exception $e) {
			KernelErrorHandler::logException($e, false);
			$r_body = '';
		}

		$line = trim(Strings::getFirstLine($r_body));

		return ($line == 'true');
	}
}
