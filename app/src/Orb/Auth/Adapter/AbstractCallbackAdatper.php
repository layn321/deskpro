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


/**
 * A shell abstract adapter useful for all types that follow the two(or more)-step process of redirecting
 * the user offsite and back.
 */
abstract class AbstractCallbackAdatper implements AdapterInterface, SessionStateInterface, CallbackInterface
{
	const DISLPAY_CONTEXT_PAGE = 'page';
	const DISLPAY_CONTEXT_POPUP = 'popup';

	/**
	 * If in callback context, then an array of callback data
	 * @var array
	 */
	protected $callback_data = null;

	/**
	 * State handler to store session data
	 * @var Orb\Auth\StateHandler\StateHandlerInterface;
	 */
	protected $state;

	/**
	 * The callback URL
	 * @var string
	 */
	protected $callback_url = null;

	/**
	 * @var null
	 */
	protected $display_context = null;

	/**
	 * Switches the adapter to the callback context using form data $data.
	 *
	 * @param array $data Form data or other callback data
	 * @return void
	 */
	public function setCallbackContext(array $data)
	{
		$this->callback_data = $data;
	}



	/**
	 * Set the URL the user is returned to
	 *
	 * @param string $url
	 */
	public function setCallbackUrl($url)
	{
		$this->callback_url = $url;
	}



	/**
	 * Get the callback URL
	 *
	 * @throws RuntimeException
	 * @return string
	 */
	public function getCallbackUrl()
	{
		if (!$this->callback_url) {
			throw new \RuntimeException('No callback URL was set');
		}
		return $this->callback_url;
	}



	/**
	 * Are we currently in callback mode?
	 *
	 * @return bool
	 */
	public function isCallbackMode()
	{
		return $this->callback_data !== null;
	}



	/**
	 * Authenticate a user.
	 *
	 * @return Orb\Auth\Result
	 */
	public function authenticate()
	{
		if ($this->isCallbackMode()) {
			return $this->authenticateCallback($this->callback_data, $this->getStateHandler());
		} else {
			return $this->authenticateInitialize($this->getStateHandler());
		}
	}



	/**
	 * Process the callback and return a final result.
	 *
	 * @return Orb\Auth\Result
	 */
	abstract protected function authenticateCallback(array $callback_data, StateHandlerInterface $state);



	/**
	 * Initialize the auth process by setting state, and returning a redirect result.
	 *
	 * @return Orb\Auth\Result
	 */
	abstract protected function authenticateInitialize(StateHandlerInterface $state);



	/**
	 * Set the state handler.
	 *
	 * @param Orb\Auth\StateHandler\StateHandlerInterface $state The state handler
	 * @return void
	 */
	public function setStateHandler(StateHandlerInterface $state)
	{
		$this->state = $state;
	}



	/**
	 * Get the state handler.
	 *
	 * @return Orb\Auth\StateHandler\StateHandlerInterface
	 */
	public function getStateHandler()
	{
		if (!$this->state) {
			throw new \RuntimeException('No state handler was set. Set one with setStateHandler');
		}
		return $this->state;
	}
}
