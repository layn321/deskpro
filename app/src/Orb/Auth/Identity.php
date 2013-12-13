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
 *
 * Suggested common names:
 * - identity_friendly: A unique username to correspond with the ID
 * - name: For users full name
 * - email_address: For users email address
 * - nickname: The users nickname or displayname (if using usernames, probably that)
 */
class Identity implements \ArrayAccess
{
	/**
	 * A unique ID used by some service
	 * @var mixed
	 */
	protected $identity;

	/**
	 * A human-friendly identity. Still unique, but capable of changing (ie a username)
	 * @var string
	 */
	protected $friendly_identity;

	/**
	 * An array of raw userinfo
	 * @var array
	 */
	protected $raw_userinfo = array();

	/**
	 * @param mixed $identity
	 * @param array $raw_userinfo
	 */
	public function __construct($identity, array $raw_userinfo = array())
	{
		$this->identity = $identity;
		$this->raw_userinfo = $raw_userinfo;
	}

	

	/**
	 * Set the human friendly identity
	 * 
	 * @param string $friendly_identity 
	 */
	public function setFriendlyIdentity($friendly_identity)
	{
		$this->friendly_identity = $friendly_identity;
	}

	
	
	/**
	 * Get the identitiy
	 *
	 * @return mixed
	 */
	public function getIdentity()
	{
		return $this->identity;
	}



	/**
	 * Get the human friendly identity
	 *
	 * @return string
	 */
	public function getFriendlyIdentity()
	{
		return $this->friendly_identity;
	}



	/**
	 * Get the raw userdata returned with the auth record
	 *
	 * @return array
	 */
	public function getRawData()
	{
		return $this->raw_userinfo;
	}




	public function offsetExists($offset)
	{
		return isset($this->raw_userinfo[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->raw_userinfo[$offset];
	}

	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException("Cannot set on the Identity object (tried to set `$offset`)");
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException("Cannot unset on the Identity object (tried to unset `$offset`)");
	}
}
