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

use \Symfony\Component\EventDispatcher\Event;
use \Symfony\Component\EventDispatcher\EventDispatcher;

use \Orb\Auth\Result;

/**
 * Authenticates a user using one of any compatible adapters.
 *
 * Orb\Auth is much like Zend\Auth except for some subtle differences, including the way we
 * gracefully handle remote-login sources (requiring redirect) in the Result object, as well
 * as how the Identity is an object and we are able to build adapters that can fetch additional
 * userinfo from the sources such as email addresses or names.
 */
class Auth
{
	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	protected $dispatcher;
	
	public function __construct(EventDispatcher $dispatcher = null)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Run auth on the adapter.
	 *
	 * The returned Result object is success, failure or requires a redirect.
	 * Success results will have an identity object.
	 * Results that require redirects you should redirect using the URL you get from the object
	 * Failures may be exceptions, check for the FAILURE_EXCEPTION code and the 'exception' message in the messages.
	 * 
	 * @param \Orb\Auth\Adapter\AdapterInterface $adapter
	 * @return \Orb\Auth\Result
	 */
	public function authenticate(\Orb\Auth\Adapter\AdapterInterface $adapter)
	{
		try {
			$result = $adapter->authenticate();
		} catch (Exception $e) {
			$result = new Result(Result::FAILURE_EXCEPTION, null, array(Result::MSG_EXCEPTION => $e));
		}

		if ($this->dispatcher) {
			$event = $this->dispatcher->filter(new Event($this, 'orb.auth.result', array('adapter' => $adapter)), $result);
			$result = $event->getReturnValue();
		}

		return $result;
	}
}
