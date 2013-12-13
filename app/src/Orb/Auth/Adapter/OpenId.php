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

use \Orb\Util\Arrays;

use \Orb\Auth\Adapter\SessionStateInterface;
use \Orb\Auth\Adapter\CallbackInterface;
use \Orb\Auth\StateHandler\StateHandlerInterface;
use \Orb\Auth\Result;

class OpenId extends AbstractCallbackAdatper
{
	protected $openid_identifier = '';



	/**
	 * Sets the data got from a form
	 *
	 * @param string $url The URL
	 */
	public function setFormData(array $form_data)
	{
		if (!empty($form_data['openid_identifier'])) {
			$this->openid_identifier = $form_data['openid_identifier'];
		}
	}



	/**
	 * Initialize the auth process by setting state, and returning a redirect result.
	 *
	 * @return Orb\Auth\Result
	 */
	protected function authenticateInitialize(StateHandlerInterface $state)
	{
		$openid = new \LightOpenID();
		$openid->identity = $this->openid_identifier;
		$openid->returnUrl = $this->getCallbackUrl();
		$openid->optional = array(
			'namePerson/friendly', 'contact/email', 'namePerson',
			'birthDate', 'person/gender', 'contact/country/home',
			'pref/language', 'pref/timezone'
		);

		try {
			$result = new Result(Result::REQUIRES_REDIRECT, null, array(Result::MSG_REDIRECT => $openid->authUrl()));
			return $result;
		} catch (\ErrorException $e) {
			$result = new Result(Result::FAILURE_EXCEPTION, null, array(Result::MSG_EXCEPTION => $e));
			return $result;
		}		
	}


	protected function authenticateCallback(array $callback_data, StateHandlerInterface $state)
	{
		$openid = new \LightOpenID();

		if (!$openid->validate()) {
			return new Result(Result::FAILURE, null, array('error_code' => 'invalid_validate', 'error_message' => 'Could not validate'));
		}

		$attributes = $openid->getAttributes();
		$userinfo = array(
			'nickname'  => !empty($attributes['namePerson/friendly'])   ? $attributes['namePerson/friendly']    : null,
			'email'     => !empty($attributes['email'])                 ? $attributes['email']                  : null,
			'fullname'  => !empty($attributes['namePerson'])            ? $attributes['namePerson']             : null,
			'birthday'  => !empty($attributes['birthDate'])             ? $attributes['birthDate']              : null,
			'gender'    => !empty($attributes['person/gender'])         ? $attributes['person/gender']          : null,
			'country'   => !empty($attributes['contact/country/home'])  ? $attributes['contact/country/home']   : null,
			'language'  => !empty($attributes['pref/language'])         ? $attributes['pref/language']          : null,
			'timezone'  => !empty($attributes['pref/timezone'])         ? $attributes['pref/timezone']          : null,
		);
		$userinfo = Arrays::removeFalsey($userinfo);

		$identity = new \Orb\Auth\Identity($openid->identity, $userinfo);

		$result = new Result(Result::SUCCESS, $identity);

		return $result;
	}
}
