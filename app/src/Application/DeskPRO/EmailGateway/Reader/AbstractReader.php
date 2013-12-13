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

namespace Application\DeskPRO\EmailGateway\Reader;

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Arrays;

abstract class AbstractReader
{
	protected $vals = array();
	protected $properties = array();
	protected $raw_source;
	protected $raw_headers;

	public function _kill()
	{
		$this->vals        = null;
		$this->properties  = null;
		$this->raw_source  = null;
		$this->raw_headers = null;
	}

	public function resetAll()
	{
		$this->vals = array();
		$this->properties = array();
	}

	public function setProperty($name, $value)
	{
		$this->properties[$name] = $value;
	}

	public function getProperty($name, $default = null)
	{
		return isset($this->properties[$name]) ? $this->properties[$name] : $default;
	}

	public function hasProperty($name)
	{
		return isset($this->properties[$name]);
	}

	public function setRawSource($source)
	{
		$this->raw_source = Strings::standardEol($source);

		$pos = strpos($this->raw_source, "\n\n");
		if ($pos) {
			$this->raw_headers = substr($this->raw_source, 0, $pos);
		}

		$this->_setRawSource($source);
	}

	public function getRawSource()
	{
		return $this->raw_source;
	}

	public function getRawHeaders()
	{
		return $this->raw_headers;
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\BodyText
	 */
	public function getBodyText()
	{
		if (!isset($this->vals['body_text'])) {
			$this->vals['body_text'] = $this->_getBodyText();
		}

		return $this->vals['body_text'];
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\BodyHtml
	 */
	public function getBodyHtml()
	{
		if (!isset($this->vals['body_html'])) {
			$this->vals['body_html'] = $this->_getBodyHtml();
		}

		return $this->vals['body_html'];
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\Attachment[]
	 */
	public function getAttachments()
	{
		if (!isset($this->vals['attach'])) {
			$this->vals['attach'] = $this->_getAttachments();
		}

		return $this->vals['attach'];
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\Subject
	 */
	public function getSubject()
	{
		if (!isset($this->vals['subject'])) {
			$this->vals['subject'] = $this->_getSubject();
		}

		return $this->vals['subject'];
	}

	/**
	 * If the email contains a Thread-Topic that tells us the original subject, then that.
	 *
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\Subject
	 */
	public function getOriginalSubject()
	{
		if (!isset($this->vals['original_subject'])) {
			$this->vals['original_subject'] = $this->_getOriginalSubject();
		}

		return $this->vals['original_subject'];
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\EmailAddress
	 */
	public function getFromAddress()
	{
		if (!isset($this->vals['from_address'])) {
			$this->vals['from_address'] = $this->_getFromAddress();
		}

		return $this->vals['from_address'];
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\EmailAddress[]
	 */
	public function getToAddresses()
	{
		if (!isset($this->vals['to_address'])) {
			$this->vals['to_address'] = $this->_getToAddresses();
		}

		return $this->vals['to_address'];
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\EmailAddress[]
	 */
	public function getCcAddresses()
	{
		if (!isset($this->vals['cc_addresses'])) {
			$this->vals['cc_addresses'] = $this->_getCcAddresses();
		}

		return $this->vals['cc_addresses'];
	}

	/**
	 * This is a collection of both To and CC addresses.
	 *
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\EmailAddress[]
	 */
	public function getDeliveredAddresses()
	{
		$to = $this->getToAddresses();
		$cc = $this->getCcAddresses();
		$from = $this->getFromAddress();

		$all = array_merge($to, $cc);
		if ($from) {
			$all[] = $from;
		}

		return $all;
	}

	/**
	 * @return string
	 */
	public function getOriginalTo()
	{
		if (isset($this->vals['original_to'])) {
			return $this->vals['original_to'] ? $this->vals['original_to'] : null;
		}

		$try = new \Application\DeskPRO\Config\UserFileConfig('original-to-headers');
		$try = $try->all();

		foreach ($try as $header_name) {
			if (!($h = $this->getHeader($header_name))) {
				return null;
			}

			if (!$h->getHeader() || !\Orb\Validator\StringEmail::isValueValid($h->getHeader())) {
				continue;
			}

			$this->vals['original_to'] = strtolower($h->getHeader());
			return strtolower($h->getHeader());
		}

		$this->vals['original_to'] = false;
		return null;
	}

	/**
	 * @return \Application\DeskPRO\EmailGateway\Reader\Item\Header
	 */
	public function getHeader($header)
	{
		if (!isset($this->vals['headers']) || !isset($this->vals['headers'][$header])) {
			if (!isset($this->vals['headers'])) $this->vals['headers'] = array();
			$this->vals['headers'][$header] = $this->_getHeader($header);
		}

		return $this->vals['headers'][$header];
	}

	abstract protected function _setRawSource($source);
	abstract protected function _getBodyText();
	abstract protected function _getBodyHtml();
	abstract protected function _getAttachments();
	abstract protected function _getSubject();
	abstract protected function _getFromAddress();
	abstract protected function _getToAddresses();
	abstract protected function _getCcAddresses();
	abstract protected function _getHeader($header);

	/**
	 * Returns true if message marks itself as from a robot
	 *
	 * @return bool
	 */
	public function isFromRobot()
	{
		$deskpro_auto = $this->getHeader('X-DeskPRO-Auto')->getAllParts();
		if ($deskpro_auto) {
			foreach ($deskpro_auto as $v) {
				if (stripos($v, 'Yes') !== false) {
					return true;
				}
			}
		}

		$auto = $this->getHeader('Auto-Submitted')->getAllParts();
		if ($auto) {
			foreach ($auto as $v) {
				$v = strtolower($v);
				if (strpos($v, 'auto-replied') !== false || strpos($v, 'auto-notified') !== false || strpos($v, 'auto-generated') !== false) {
					return true;
				}
			}
		}

		$auto = $this->getHeader('X-Autoreply')->getAllParts();
		if ($auto) {
			foreach ($auto as $v) {
				$v = strtolower($v);
				if ($v == "1" || $v == "yes") {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Checks if the email was sent via outlook
	 *
	 * @return bool
	 */
	public function isOutlookMailer()
	{
		if (isset($this->vals['is_outlook'])) {
			return $this->vals['is_outlook'];
		}

		$is_outlook = false;

		$mailer = $this->getHeader('X-Mailer');
		if ($mailer && strpos($mailer->getHeader(), 'Outlook') !== false) {
			$is_outlook = true;
		}
		if (!$is_outlook) {
			$headers = $this->getRawHeaders();
			if (preg_match('#^X\-MS\-#', $headers)) {
				$is_outlook = true;
			}
		}

		$this->vals['is_outlook'] = $is_outlook;

		return $this->vals['is_outlook'];
	}


	/**
	 * Gets a Date object representing the Date header or null if there is no Date header.
	 * If there are multiple Date headers, the latest (closest to now) date is used.
	 *
	 * @return \DateTime|null
	 */
	public function getDate()
	{
		if (isset($this->vals['date'])) {
			return $this->vals['date'] ? $this->vals['date'] : null;
		}

		$this->vals['date'] = false;

		$use_date = null;
		$date = null;

		$date_header = $this->getHeader('Date');
		if (!$date_header || !count($date_header->header_parts)) {
			return null;
		}

		foreach ($date_header->header_parts as $date_part) {
			if (!is_string($date_part)) {
				continue;
			}

			$date = \DateTime::createFromFormat(\DateTime::RFC2822, $date_part);

			if ($date && (!$use_date || $date > $use_date)) {
				$use_date = $date;
			}
		}

		if ($use_date) {
			$this->vals['date'] = $use_date;
			return $use_date;
		}

		return null;
	}
}
