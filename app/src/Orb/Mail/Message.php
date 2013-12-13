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
 * @subpackage Mail
 */

namespace Orb\Mail;

use \Orb\Util\Strings;
use \Orb\Util\Util;

/**
 * Represents an email message to send
 */
class Message extends \Swift_Message
{
	/**
	 * @var bool
	 */
	protected $_queue_hint = false;

	/**
	 * @var int
	 */
	protected $_queue_priority = 10;

	/**
	 * @var bool
	 */
	protected $_suppress_autoreply = false;

	/**
	 * @var \Swift_Transport
	 */
	protected $force_transport;

	/**
	 * @var bool
	 */
	protected  $has_prepared = false;

	/**
	 * @var bool
	 */
	protected $has_presend = false;

	/**
	 * Metadata that might be used by the transports or queue processor
	 * @var array
	 */
	public $meta = array();

	public function __construct($subject = null, $body = null, $contentType = null, $charset = null)
	{
		call_user_func_array(array($this, 'Swift_Mime_SimpleMessage::__construct'), \Swift_DependencyContainer::getInstance()->createDependenciesFor('mime.message'));

		if (!isset($charset)) {
			$charset = \Swift_DependencyContainer::getInstance()->lookup('properties.charset');
		}

		$this->setSubject($subject);
		$this->setBody($body);
		$this->setCharset($charset);
		if ($contentType) {
			$this->setContentType($contentType);
		}
	}


	/**
	 * Prepares the message to be set. This is a hook that is called right before sending.
	 */
	public function prepare()
	{
		if ($this->has_prepared) {
			return;
		}

		$this->has_prepared = true;

		$this->doPrepare();
	}

	protected function doPrepare() { }


	/**
	 * Called just before a send attempt
	 */
	public function preSend()
	{
		if (!$this->has_presend) {
			$from = $this->getFrom();
			if ($from && count($from) == 1) {
				if (!$this->getReplyTo()) {
					$this->setReplyTo($from);
				}

				if (!$this->_suppress_autoreply && !$this->getReturnPath()) {
					$addr = array_keys($from);
					$addr = array_pop($addr);
					$this->setReturnPath($addr);
				}
			}

			if ($this->_suppress_autoreply) {
				// Tell Outlook/Exchange to suppress autoreplies (http://msdn.microsoft.com/en-us/library/ee219609(v=exchg.80).aspx)
				$this->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'All');
			}

			$this->doPreSend(false);
		} else {
			$this->doPreSend(true);
		}

		$this->has_presend = true;
	}

	protected function doPreSend($is_retry = false) { }


	/**
	 * Enable the queue hint that hints that it's okay to queue and send later.
	 */
	public function enableQueueHint($priority = 10)
	{
		$this->_queue_hint = true;
		$this->_queue_priority = $priority;
	}


	/**
	 * @return int
	 */
	public function getQueuePriority()
	{
		return $this->_queue_priority;
	}


	/**
	 * Disable the queue hint
	 */
	public function disableQueueHint()
	{
		$this->_queue_hint = false;
	}


	/**
	 * Set the suppress autoreplies headers
	 *
	 * @param bool $on
	 */
	public function setSuppressAutoreplies($on = true)
	{
		$this->_suppress_autoreply = (bool)$on;
	}


	/**
	 * Is it okay to queue this message to send later?
	 *
	 * @return bool
	 */
	public function isQueueHinted()
	{
		return $this->_queue_hint;
	}


	/**
	 * Set a transport to use instead of whatever is configured in the mailer.
	 *
	 * @param \Swift_Transport $tr
	 */
	public function setForceTransport(\Swift_Transport $tr)
	{
		$this->force_transport = $tr;
	}


	/**
	 * Get the forced transport.
	 *
	 * @return \Swift_Transport
	 */
	public function getSpecificTransport()
	{
		return $this->force_transport;
	}


	/**
	 * @static
	 * @param null $subject
	 * @param null $body
	 * @param null $contentType
	 * @param null $charset
	 * @return \Orb\Mail\Message
	 */
	public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
	{
		return new self($subject, $body, $contentType, $charset);
	}
}
