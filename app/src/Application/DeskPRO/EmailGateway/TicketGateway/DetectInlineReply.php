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

namespace Application\DeskPRO\EmailGateway\TicketGateway;

use Application\DeskPRO\EmailGateway\Reader\AbstractReader as AbstractEmailReader;
use Application\DeskPRO\Entity\TicketMessage;
use Doctrine\ORM\EntityManager;
use Orb\Log\Loggable;
use Orb\Log\Logger;

class DetectInlineReply implements Loggable
{
	/**
	 * @var \Application\DeskPRO\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\EmailGateway\Reader\AbstractReader
	 */
	protected $reader;

	/**
	 * @var array
	 */
	protected $message_texts = null;

	/**
	 * @var float
	 */
	protected $threshold = 0.24;

	/**
	 * How many messages to go back to detect changes
	 *
	 * @var int
	 */
	protected $history_limit = 1;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;

	public function __construct(EntityManager $em, AbstractEmailReader $reader)
	{
		$this->em     = $em;
		$this->reader = $reader;
	}


	/**
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * @return \Orb\Log\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}


	/**
	 * How much longer/shorter does a messag eneed to be before we think its an inline reply
	 *
	 * E.g., 0.1 for 10%
	 *
	 * @param float $threshold
	 */
	public function setThreshold($threshold)
	{
		$this->threshold = $threshold;
	}


	/**
	 * Check to see if we've detected an inline reply
	 *
	 * @return bool
	 */
	public function hasDifferentMessage()
	{
		return $this->getDifferentMessage() !== null;
	}


	/**
	 * Gets the first message that we detect has changed
	 *
	 * @param bool $all
	 * @return \Application\DeskPRO\Entity\TicketMessage|null
	 */
	public function getDifferentMessage()
	{
		$message_texts = $this->getMessageTexts();
		if (!$message_texts) {
			return null;
		}

		if ($this->logger) $this->logger->logDebug('[DetectInlineReply] Found ' . count($message_texts) .' texts');
		$ticket_messages = $this->em->getRepository('DeskPRO:TicketMessage')->getByIds(array_keys($message_texts));

		foreach ($message_texts as $message_id => $message_text) {
			if (!isset($ticket_messages[$message_id])) {
				if ($this->logger) $this->logger->logDebug('[DetectInlineReply] Invalid message text for id ' . $message_id);
				continue;
			}

			$ticket_message = $ticket_messages[$message_id];

			$real_message_text = $this->normalizeMessage($ticket_message->message);

			$diff = $this->getMessageDifference($message_text, $real_message_text);
			if ($this->logger) $this->logger->logDebug('[DetectInlineReply] Message diff for message ' . $message_id . ' is ' . $diff);
			if ($diff >= $this->threshold) {
				if ($this->logger) $this->logger->logDebug('[DetectInlineReply] -- Match. Diff over threshold of ' . $this->threshold);
				return $ticket_message;
			}
		}

		return null;
	}


	/**
	 * Get difference as a float between two messages
	 *
	 * @param string $message1
	 * @param string $message2
	 * @return float
	 */
	public function getMessageDifference($message1, $message2)
	{
		$len1 = strlen($message1);
		$len2 = strlen($message2);

		// Prevent division by zero
		if (!$len1 || !$len2) {
			return 0;
		}

		if ($len1 > $len2) {
			$diff = 1.0 - ($len2 / $len1);
		} else {
			$diff = 1.0 - ($len1 / $len2);
		}

		return $diff;
	}


	/**
	 * Read body and extract message texts from the source
	 *
	 * @return array
	 */
	public function getMessageTexts()
	{
		if ($this->message_texts !== null) {
			return $this->message_texts;
		}

		$this->message_texts = array();

		$body = $this->reader->getBodyHtml()->getBodyUtf8();
		if (!$body) {
			if ($this->logger) $this->logger->logDebug('[DetectInlineReply] No body');
			return $this->message_texts;
		}

		$matches = 0;
		if (!preg_match_all('#<a[^>]*dp_message_([0-9]+)_begin[^>]*>(.*?)<a[^>]*dp_message_\\1_end#s', $body, $matches, \PREG_SET_ORDER)) {
			if ($this->logger) $this->logger->logDebug('[DetectInlineReply] No message texts');
			return $this->message_texts;
		}

		foreach ($matches as $match) {
			$message_id = $match[1];
			$message    = $match[2];

			if ($this->logger) $this->logger->logDebug('[DetectInlineReply] Found message: ' . $message_id);

			// Trim off the </a> which is part of the marker
			if (($pos = stripos($message, '</a>')) !== false) {
				$message = substr($message, $pos + 4);
			}

			$message = $this->normalizeMessage($message);

			// Too short to try and guess
			if (!$message || strlen($message) < 100) {
				if ($this->logger) $this->logger->logDebug('[DetectInlineReply] -- Too short for guess');
				continue;
			}

			$this->message_texts[$message_id] = $message;

			if (count($this->message_texts) >= $this->history_limit) {
				if ($this->logger) $this->logger->logDebug('[DetectInlineReply] Reached history limit of ' . $this->history_limit);
				break;
			}
		}

		return $this->message_texts;
	}


	/**
	 * Normalize message text so our detection can be a bit more accurate.
	 *
	 * @param string $message_text
	 * @return string
	 */
	public function normalizeMessage($message_text)
	{
		// Remove embedded image attachment tokens that can be
		// in database-messages. They render to <img> in emails,
		// and then in replies the <img> would be removed, so we
		// can just remove them here too.
		// Eg: [attach:image:14ACHKTRCNWD1382226544B:test.png]
		$message_text = preg_replace('#\[attach:(.*?):(.*?):(.*?)\]#', '', $message_text);

		$message_text = str_replace(array('<br/>', '<br />', '<br>'), ' ', $message_text);
		$message_text = strip_tags($message_text);
		$message_text = html_entity_decode($message_text, \ENT_QUOTES, 'UTF-8');
		$message_text = preg_replace('#[\pZ\pC\s]+#u', ' ', $message_text);
		$message_text = preg_replace('# {2,}#', ' ', $message_text);
		$message_text = trim($message_text);

		return $message_text;
	}
}