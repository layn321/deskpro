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

namespace Application\DeskPRO\EmailGateway\Cutter;

use Application\DeskPRO\App;

/**
 * This works on an email message to detect a forwarded email, parse out
 * the original message and reply, and original author email/name.
 */
class ForwardCutter
{
	protected $body;
	protected $is_html;

	protected $forwarded_message;
	protected $forward_info;
	protected $reply;

	protected $error_code = null;

	/**
	 * @var \Application\DeskPRO\EmailGateway\Cutter\Def\ForwardDef
	 */
	protected $cutter;


	/**
	 * Check if a subject matches the pattern for a forwarded message.
	 *
	 * @param string $subject
	 * @return bool
	 */
	public static function subjectIsForward($subject)
	{
		// Prefixes for FW/FWD and in other langs too
		return (bool)preg_match('#^(FW|FWD|VL|WG|FS|VB|RV|VS):#i', ltrim($subject));
	}


	/**
	 * Cut out the FWD prefix from subject
	 *
	 * @param string $subject
	 * @return string
	 */
	public static function cutSubjectForwardPrefix($subject)
	{
		return preg_replace('#^(FW|FWD|VL|WG|FS|VB|RV|VS):\s*#i', '', trim($subject));
	}


	public function __construct($body, $is_html, $cutter)
	{
		$this->body = $body;
		$this->is_html = $is_html;
		$this->cutter = $cutter;

		if ($this->cutter instanceof Def\ForwardDef) {
			$this->_process();
		}
	}

	protected function _process()
	{
		$this->forward_info = $this->cutter->getForwardInfo($this->body, $this->is_html);

		if (!$this->forward_info['fwd_message_body']) {
			$this->error_code = 'unknown_body';
		} elseif (!$this->forward_info['fwd_from_email'] || !\Orb\Validator\StringEmail::isValueValid($this->forward_info['fwd_from_email']) || App::getSystemService('gateway_address_matcher')->isManagedAddress($this->forward_info['fwd_from_email'])) {
			$this->error_code = 'unknown_email';
		}
	}


	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->forward_info;
	}


	/**
	 * Check if the forwarded message was read correctly and has all required information
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return $this->error_code === null;
	}


	/**
	 * @return string
	 */
	public function getErrorCode()
	{
		return $this->error_code;
	}


	/**
	 * Get the users message
	 *
	 * @return string
	 */
	public function getForwardedMessage()
	{
		return $this->forward_info['fwd_message_body'];
	}


	/**
	 * Get the reply above the forwarded message
	 *
	 * @return string
	 */
	public function getReply()
	{
		return $this->forward_info['message_body'];
	}


	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\EmailAddress
	 */
	public function getUserEmailItem()
	{
		$item = new \Application\DeskPRO\EmailGateway\Reader\Item\EmailAddress();
		$item->email = $this->forward_info['fwd_from_email'];
		$item->name  = $this->getUserName();

		return $item;
	}


	/**
	 * Get the user email address from the forwarded message
	 *
	 * @return string
	 */
	public function getUserEmailAddress()
	{
		return $this->forward_info['fwd_from_email'];
	}


	/**
	 * Get the users name from the forwarded message (based on their name in From:)
	 *
	 * @return string
	 */
	public function getUserName()
	{
		if (!empty($this->forward_info['fwd_from_name'])) {
			return $this->forward_info['fwd_from_name'];
		}

		return null;
	}
}
