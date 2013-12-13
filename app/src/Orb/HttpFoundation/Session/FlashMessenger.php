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
 * @category Usersources
 */

namespace Orb\HttpFoundation\Session;

/**
 * This is a session utility that helps create flash messages for a user. These are messages that appear
 * once and then disappear. For example, a success message at the top of a page could be added during a redirect
 * and then displayed on the next real page load.
 */
class FlashMessenger
{
	/**
	 * @var Orb\HttpFoundation\Session\SessionInterface
	 */
	protected $session;

	/**
	 * Messages we read from the session for the current request. They
	 * will be deleted now.
	 * @var array
	 */
	protected $messages = array();

	/**
	 * The messages that we'll save for the next request
	 * @var ArrayObject
	 */
	protected $current_messages;

	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
		$this->messages = $session->get('flash_messages')->getArrayCopy();

		$this->current_messages = new \ArrayObject();
		$session->set('flash_messages', $this->current_messages);
	}



	/**
	 * Get the messages for this request
	 *
	 * @return array
	 */
	public function getMessages()
	{
		return $this->messages;
	}



	/**
	 * Gets the messages added during this request, but won't be displayed until the next.
	 * @return array
	 */
	public function getCurrentMessages()
	{
		return $this->current_messages->getArrayCopy();
	}



	/**
	 * Get messages for this request, as well as messages we just added.
	 * @return array
	 */
	public function getAllMessages()
	{
		return array_merge($this->messages, $this->current_messages->getArrayCopy());
	}



	/**
	 * Remove the messages for this request
	 */
	public function clearMessages()
	{
		$this->messages = array();
	}



	/**
	 * Remove the messages we added during this request
	 */
	public function clearCurrentMessages()
	{
		$this->current_messages->exchangeArray(array());
	}



	/**
	 * Clear all messages, both from session and current.
	 */
	public function clearAllMessages()
	{
		$this->clearMessages();
		$this->clearCurrentMessages();
	}



	/**
	 * Add a message for the next request.
	 *
	 * @param  $message
	 */
	public function addMessage($message)
	{
		$this->session->get('flash_messages')->append($message);
	}
}
