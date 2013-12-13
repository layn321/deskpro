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

namespace Application\DeskPRO\HttpFoundation;

use Symfony\Component\HttpFoundation\Cookie as BaseCookie;

use Application\DeskPRO\App;

class Cookie extends BaseCookie
{
	const EXPIRE_NEVER = 'never';
	const EXPIRE_DELETE = 'delete';

	public static function makeDeleteCookie($name)
	{
		return new self($name, '', 'delete');
	}

	public static function makeCookie($name, $value, $expire, $httpOnly = false, $secure = false)
	{
		return new self($name, $value, $expire, null, null, $secure, $httpOnly);
	}

	public function __construct($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = false)
    {
		if ($path === null) {
			$path = App::getSetting('core.cookie_path');
			if (!$path) {
				$path = '/';
			}
		}

		if ($domain === null) {
			$domain = App::getSetting('core.cookie_domain');
			if (!$domain) {
				$domain = null;
			}
		}

		if ($expire === self::EXPIRE_NEVER) {
			$expire = '+5 years';
		} elseif ($expire === self::EXPIRE_DELETE) {
			$expire = '-1 week';
		}

	    parent::__construct($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}

	public function __toString()
	{
		$str = urlencode($this->getName()).'=';

		if ('' === (string) $this->getValue()) {
			$str .= 'deleted; expires='.gmdate("D, d-M-Y H:i:s T", time() - 31536001);
		} else {
			$str .= urlencode($this->getValue());

			if ($this->getExpiresTime() !== 0) {
				$str .= '; expires='.gmdate("D, d-M-Y H:i:s T", $this->getExpiresTime());
			}
		}

		if (null !== $this->path) {
			$str .= '; path='.$this->path;
		}

		if (null !== $this->getDomain()) {
			$str .= '; domain='.$this->getDomain();
		}

		if (true === $this->isSecure()) {
			$str .= '; secure';
		}

		if (true === $this->isHttpOnly()) {
			$str .= '; httponly';
		}

		return $str;
	}

	public function setDomain($domain)
	{
		$this->domain = $domain;
		return $this;
	}

	public function setExpire($expire)
	{
		$this->expire = $expire;
		return $this;
	}

	public function setHttpOnly($httpOnly)
	{
		$this->httpOnly = $httpOnly;
		return $this;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	public function setSecure($secure)
	{
		$this->secure = $secure;
		return $this;
	}

	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	public function send()
	{
		header('Set-Cookie: ' . $this->__toString(), false);
	}
}
