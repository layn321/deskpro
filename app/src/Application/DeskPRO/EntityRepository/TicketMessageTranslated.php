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
 * @category Entities
 */

namespace Application\DeskPRO\EntityRepository;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Entity\TicketMessage as TicketMessageEntity;

use Orb\Util\Arrays;
use Orb\Util\Numbers;


class TicketMessageTranslated extends AbstractEntityRepository
{
	/**
	 * Finds all translated messages on a message
	 *
	 * @param TicketMessageEntity    $ticket_message
	 * @param string|string[]|null  $lang           Optionally only fetch these lang codes
	 * @return array|TicketMessageEntity|Null
	 */
	public function getForMessage(TicketMessageEntity $ticket_message, $lang_code = null)
	{
		if ($lang_code) {
			if (!is_array($lang_code)) {
				$lang_code = array($lang_code);
			}

			// Also get generic ones. eg if we specified en_US but there might be ones as 'en'
			foreach (array_values($lang_code) as $c) {
				if (strpos($c, '_')) {
					list ($x,) = explode('_', $c, 2);
					$lang_code[] = $x;
				}
			}

			$lang_code = array_unique($lang_code);

			$got = $this->_em->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessageTranslated m INDEX BY m.lang_code
				WHERE m.ticket_message = ?0 AND m.lang_code IN (?1)
			")->setParameters(array($ticket_message, array_values($lang_code)))->execute();

			if (!$got) {
				return null;
			}

			foreach ($lang_code as $c) {
				if (isset($got[$c])) {
					return $got[$c];
				}
			}

			return null;
		} else {
			return $this->_em->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessageTranslated m INDEX BY m.lang_code
				WHERE m.ticket_message = ?0
			")->setParameters(array($ticket_message))->getOneOrNullResult();
		}
	}


	/**
	 * Get all translated messages for a collection of messages
	 *
	 * @param TicketMessageEntity[] $ticket_messages
	 * @param string[]|string|null  $lang_code       Optionally only these lang codes (if using $single, then in order of importance)
	 * @param single                $single          Only return a single translation per message
	 * @return array
	 */
	public function getForMessages(array $ticket_messages, $lang_code = null, $single = true)
	{
		if ($lang_code) {
			if (!is_array($lang_code)) {
				$lang_code = array($lang_code);
			}

			// Also get generic ones. eg if we specified en_US but there might be ones as 'en'
			foreach (array_values($lang_code) as $c) {
				if (strpos($c, '_')) {
					list ($x,) = explode('_', $c, 2);
					$lang_code[] = $x;
				}
			}

			$lang_code = array_unique($lang_code);

			if (!$ticket_messages || !$lang_code) {
				return array();
			}

			$trans_messages = $this->_em->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessageTranslated m
				WHERE m.ticket_message IN (?0) AND m.lang_code IN (?1)
			")->setParameters(array(array_values($ticket_messages), array_values($lang_code)))->execute();

			$ret = array();

			foreach ($trans_messages as $msg) {
				$message_id = $msg->ticket_message->getId();
				$lang = $msg->lang_code;

				if (!isset($ret[$message_id])) {
					$ret[$message_id] = array();
				}

				$ret[$message_id][$lang] = $msg;
			}

			if ($lang_code && $single) {
				$ret_all = $ret;
				$ret = array();

				foreach ($ret_all as $message_id => $langs) {
					foreach ($lang_code as $c) {
						if (isset($langs[$c])) {
							$ret[$message_id] = $langs[$c];
						}
					}
				}
			}

			return $ret;
		} else {
			$trans_messages_x = $this->_em->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessageTranslated m
				WHERE m.ticket_message IN (?0)
			")->setParameters(array($ticket_messages))->getOneOrNullResult();
			$trans_messages = array();
			foreach ($trans_messages_x as $tr) {
				$trans_messages[$tr->ticket_message->getId()] = $tr;
			}

			return $trans_messages;
		}
	}
}
