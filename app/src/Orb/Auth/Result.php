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

namespace Orb\Auth;

/**
 * Holds the result from an auth try.
 */
class Result
{
	/**
	 * Failure due to invalid credentials.
	 */
	const FAILURE_INVALID_CREDS = -2;

	/**
	 * Failure due to an inner exception. The exception will be in the messages
	 * array under the 'exception' key.
	 */
	const FAILURE_EXCEPTION = -1;

	/**
	 * A general error. More information might be in the messages array.
	 */
	const FAILURE = 0;

	/**
	 * Success
	 */
	const SUCCESS = 1;

	/**
	 * Not a login success, but not a failure either. This indicates the user
	 * needs to be redirected (ie to an offsite resource) before login is complete.
	 * The URL will be in the messages array under the key 'redirect_url'.
	 */
	const REQUIRES_REDIRECT = 2;

	/**
	 * The key that an Exception is stored under for FAILURE_EXCEPTION codes.
	 */
	const MSG_EXCEPTION = 'exception';

	/**
	 * The key that the redirect URL is stored under in the REQUIRES_REDIRECT code.
	 */
	const MSG_REDIRECT = 'redirect_url';



	/**
	 * Array of info (ie debug info etc) from the adapter
	 * @var array
	 */
	protected $_messages = array();

	/**
	 * The identity returned if the auth was success.
	 *
	 * @return Orb\Auth\Identity
	 */
	protected $_identity = null;

	/**
	 * Result code from the ogin attempt
	 * @var int
	 */
	protected $_code = 0;

	

	/**
	 * If $code is Result::REQUIRES_REDIRECT then $messages should have an item called
	 * 'redirect_url'.
	 *
	 * @param int $code Success (Result::SUCCESS) or error code on failure
	 * @param \Orb\Auth\Identity $identity If successful, the user identity
	 * @param array $messages Messages of why the login failed, or any additional info
	 */
	public function __construct($code, \Orb\Auth\Identity $identity = null, array $messages = array())
	{
		$this->_code = $code;
		$this->_identity = $identity;
		$this->_messages = $messages;
	}



	/**
	 * Was the login valid?
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return $this->_code == self::SUCCESS;
	}

	

	/**
	 * Does the user need to be redirected to finish authentication?
	 *
	 * @return bool
	 */
	public function isRedirectRequired()
	{
		return $this->_code == self::REQUIRES_REDIRECT;
	}


	
	/**
	 * If the result says the user must be redirect, get the URL to redirect the user to.
	 *
	 * @return string
	 */
	public function getRedirectUrl()
	{
		if (!$this->isRedirectRequired() OR !isset($this->_messages['redirect_url'])) {
			throw new \UnexpectedValueException('The result does not specify redirection');
		}

		return $this->_messages['redirect_url'];
	}


	
	/**
	 * Get the identity.
	 * 
	 * @return \Orb\Auth\Identity
	 */
	public function getIdentity()
	{
		if ($this->_identity === null) {
			throw new \UnexpectedValueException('No identity was set, the login failred');
		}

		return $this->_identity;
	}



	/**
	 * An array of extra data returned from the adapters, such as error information.
	 * Returns an array, or if a key is supplied, that one key or null if it doesn't exist.
	 *
	 * @array string $key A specific key to get, or null to get the whole array
	 * @return mixed
	 */
	public function getMessages($key = null)
	{
		if ($key !== null) {
			return isset($this->_messages[$key]) ? $this->_messages[$key] : null;
		}

		return $this->_messages;
	}
}
