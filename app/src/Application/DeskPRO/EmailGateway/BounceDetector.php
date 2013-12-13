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

namespace Application\DeskPRO\EmailGateway;

use Application\DeskPRO\EmailGateway\Reader\AbstractReader;
use Orb\Log\Logger;
use Orb\Util\Strings;
use Doctrine\ORM\EntityManager;

class BounceDetector
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\EmailGateway\Reader\AbstractReader
	 */
	protected $reader;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	/**
	 * @var string[]
	 */
	protected $patterns;

	/**
	 * @var string
	 */
	protected $original_subject;

	/**
	 * @var string[]
	 */
	protected $guessed_email_addresses;

	public function __construct(AbstractReader $reader, EntityManager $em)
	{
		$this->reader = $reader;
		$this->em = $em;
	}


	/**
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @return string[]
	 */
	public function getPatterns()
	{
		if ($this->patterns !== null) {
			return $this->patterns;
		}

		$pattern_config = new \Application\DeskPRO\Config\UserFileConfig('bounce-subject-patterns');
		$this->patterns = $pattern_config->all();

		return $this->patterns;
	}


	/**
	 * @return bool
	 */
	public function isBounced()
	{
		// Subject check first, which also populates original_subject
		// which is used again when detecting the ticket this belongs to

		$subject = $this->reader->getSubject()->getSubjectUtf8();

		foreach ($this->getPatterns() as $pattern) {
			$m = null;
			if (preg_match($pattern, $subject, $m)) {
				if (isset($m['subject'])) {
					$this->original_subject = $m['subject'];
				}
				if ($this->logger) $this->logger->logDebug('Is bounced based on subject match: ' . $pattern);
				return true;
			}
		}

		$failed = $this->reader->getHeader('X-Failed-Recipients');
		if ($failed && $failed->getHeader()) {
			if ($this->logger) $this->logger->logDebug('Is bounced based on X-Failed-Recipients');
			return true;
		}

		$from = $this->reader->getFromAddress();
		$postmaster_config = new \Application\DeskPRO\Config\UserFileConfig('postmaster-emails');
		foreach ($postmaster_config as $k => $pattern) {
			if (preg_match($pattern, $from->email)) {
				if ($this->logger) $this->logger->logDebug('Is bounced based on postmaster pattern #$k $pattern matching from address ' . $from->email);
				return true;
			}
		}

		if ($this->logger) $this->logger->logDebug('Not a bounce');
		return false;
	}


	/**
	 * Try to find possible addresses to match on
	 *
	 * @return string[]
	 */
	public function getGuessedEmailAddresses()
	{
		if ($this->guessed_email_addresses !== null) {
			return $this->guessed_email_addresses;
		}

		$this->guessed_email_addresses = array();

		// The actual From address should be tried too
		$this->guessed_email_addresses[] = $this->reader->getFromAddress()->getEmail();

		if ($failed = $this->reader->getHeader('X-Failed-Recipients')) {
			foreach ($failed->getAllParts() as $email) {
				$this->guessed_email_addresses[] = strtolower($email);
				if ($this->logger) $this->logger->logDebug('Found email via X-Failed-Recipients: ' . $email);
			}
		}

		// Find original message part
		$m = null;
		if (preg_match_all('#^To: (.*?)$#imu', $this->reader->getBodyText()->getBodyUtf8(), $m, \PREG_SET_ORDER)) {
			foreach ($m as $match) {
				$email = Strings::extractRegexMatch('#<((.*?)@(.*?))>#', $match[1]);
				if (!$email) {
					$email = Strings::extractRegexMatch('#((.*?)@(.*?))#', $match[1]);
				}

				if ($email) {
					$this->guessed_email_addresses[] = strtolower($email);
					if ($this->logger) $this->logger->logDebug('Found email via body: ' . $email);
				}
			}
		}

		$this->guessed_email_addresses = array_unique($this->guessed_email_addresses);

		return $this->guessed_email_addresses;
	}
}