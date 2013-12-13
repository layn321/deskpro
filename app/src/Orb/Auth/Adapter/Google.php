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
 * @category Auth
 */

namespace Orb\Auth\Adapter;

use \Orb\Auth\Adapter\SessionStateInterface;
use \Orb\Auth\Adapter\CallbackInterface;
use \Orb\Auth\StateHandler\StateHandlerInterface;
use \Orb\Auth\Result;

use \GoogleOpenID;

/**
 * Requirements:
 * - GoogleOpenID: http://andrewpeace.com/php-google-login-class.html
 */
class Google extends AbstractCallbackAdatper implements DisplayContextInterface
{
	protected $display = 'page';

	public function __construct()
	{

	}


	/**
	 * Sets the display context: page or popup
	 *
	 * @param $context
	 * @throws \InvalidArgumentException
	 */
	public function setDisplayContext($context)
	{
		$context = strtolower($context);
		if (!in_array($context, array('page', 'popup'))) {
			throw new \InvalidArgumentException("Invalid display context `$context`");
		}

		$this->display = $context;
	}


	/**
	 * Initialize the auth process by setting state, and returning a redirect result.
	 *
	 * @return Orb\Auth\Result
	 */
	protected function authenticateInitialize(StateHandlerInterface $state)
	{
		$ah = GoogleOpenID::getAssociationHandle();
		$googleLogin = GoogleOpenID::createRequest($this->getCallbackUrl(), $ah, true);

		$params = $googleLogin->getArray();
		if ($this->display == 'popup') {
			$params['openid.ui.mode'] = 'popup';
		}

		$redirect_url = $googleLogin->endPoint() . '?' . http_build_query($params);

		$result = new Result(Result::REQUIRES_REDIRECT, null, array(Result::MSG_REDIRECT => $redirect_url));
		return $result;
	}



	/**
	 * Process the callback and return a final result.
	 *
	 * @return Orb\Auth\Result
	 */
	protected function authenticateCallback(array $callback_data, StateHandlerInterface $state)
	{
		$googleLogin = GoogleOpenID::getResponse();
		$user_id = $user_email = null;

		if($googleLogin->success()) {
			$user_id = $googleLogin->identity();
			$user_email = $googleLogin->email();
		}

		if ($user_id && $user_email) {

			$raw = array('user_id' => $user_id, 'user_email' => $user_email);
			foreach ($_GET as $k => $v) {
				if (strpos($k, 'openid_') === 0) {
					$raw[$k] = $v;
				}
			}

			$identity = new \Orb\Auth\Identity($user_id, $raw);
			$identity->setFriendlyIdentity($user_email);
			$result = new Result(Result::SUCCESS, $identity);

			return $result;
		}

		return new Result(Result::FAILURE, null, array('error_code' => 'failed_session', 'error_message' => 'No OpenID session'));
	}
}
