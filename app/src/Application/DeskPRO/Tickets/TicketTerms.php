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

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Searcher\TicketSearch;
use Application\DeskPRO\Searcher\PersonSearch;
use Application\DeskPRO\Searcher\OrganizationSearch;

use Application\DeskPRO\Tickets\TicketChangeTracker;

use Orb\Util\Dates;
use Orb\Util\Numbers;
use Orb\Util\Arrays;
use Orb\Util\Strings;

class TicketTerms
{
	const OP_IS          = 'is';
	const OP_NOT         = 'not';
	const OP_LT          = 'lt';
	const OP_GT          = 'gt';
	const OP_LTE         = 'lte';
	const OP_GTE         = 'gte';
	const OP_BETWEEN     = 'between';
	const OP_CONTAINS    = 'contains';
	const OP_NOTCONTAINS = 'notcontains';
	const OP_NOOP        = null;
	const OP_IS_REGEX    = 'is_regex';
	const OP_NOT_REGEX   = 'not_regex';

	const OP_CHANGED            = 'changed';
	const OP_CHANGED_TO         = 'changed_to';
	const OP_CHANGED_FROM       = 'changed_from';
	const OP_NOT_CHANGED_TO     = 'not_changed_to';
	const OP_NOT_CHANGED_FROM   = 'not_changed_from';

	const OP_CHANGED_TO_GTE         = 'changed_to_gte';
	const OP_CHANGED_TO_LTE         = 'changed_to_lte';
	const OP_CHANGED_FROM_GTE       = 'changed_from_gte';
	const OP_CHANGED_FROM_LTE       = 'changed_from_lte';
	const OP_NOT_CHANGED_TO_GTE     = 'not_changed_to_gte';
	const OP_NOT_CHANGED_TO_LTE     = 'not_changed_to_let';
	const OP_NOT_CHANGED_FROM_GTE   = 'not_changed_from_gte';
	const OP_NOT_CHANGED_FROM_LTE   = 'not_changed_from_lte';

	/**
	 * @var array
	 */
	protected $terms = array();

	/**
	 * @var array
	 */
	protected $term_ids_map = array();

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker = null;


	/**
	 * @param array $terms
	 */
	public function __construct(array $terms)
	{
		$this->terms = $terms;

		foreach ($terms as $info) {
			if (!isset($info['options'])) {
				continue;
			}
			if (isset($info['type'])) {
				if (!isset($this->term_ids[$info['type']])) {
					$this->term_ids_map[$info['type']] = array();
				}
				$this->term_ids_map[$info['type']][] = $info;
			}
		}
	}


	/**
	 * Check if there is a certain term in this collection
	 *
	 * @param string $type
	 * @return bool
	 */
	public function hasTicketTerm($type)
	{
		return isset($this->term_ids_map[$type]);
	}


	/**
	 * @param string $type
	 * @param bool $first
	 * @return array
	 */
	public function getTicketTerm($type, $first = true)
	{
		if (!isset($this->term_ids_map[$type])) {
			return array();
		}

		if (!$first) {
			return $this->term_ids_map[$type];
		}

		return Arrays::getFirstItem($this->term_ids_map[$type]);
	}


	/**
	 * @param $tracker
	 */
	public function setChangeTracker($tracker)
	{
		$this->tracker = $tracker;
	}


	/**
	 * Check a specific ticket against these terms to see if it matches.
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @return bool
	 */
	public function doesTicketMatch(Entity\Ticket $ticket)
	{
		foreach ($this->terms as $info) {

			if (empty($info['type']) || empty($info['op']) || empty($info['options'])) {
				continue;
			}

			$term = $info['type'];
			if (!$term) continue;

			$op = $info['op'];
			$choice = $info['options'];

			if (strpos($op, 'changed') !== false) {
				if ($this->tracker) {
					if (!$this->testChangedTerm($ticket, $term, $op, $choice)) {
						return false;
					}
				} else {
					return false;
				}
			} else {
				if (!$this->testTerm($ticket, $term, $op, $choice)) {
					return false;
				}
			}
		}

		return true;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @param TicketChangeTracker|null $tracker
	 * @return bool
	 */
	public function doesTicketMatchAny(Entity\Ticket $ticket)
	{
		foreach ($this->terms as $info) {

			if (empty($info['type']) || empty($info['op']) || empty($info['options'])) {
				continue;
			}

			$term = $info['type'];
			if (!$term) continue;

			$op = $info['op'];
			$choice = $info['options'];

			if (strpos($op, 'changed') !== false) {
				if ($this->tracker) {
					if ($this->testChangedTerm($ticket, $term, $op, $choice)) {
						return true;
					}
				}
			} else {
				if ($this->testTerm($ticket, $term, $op, $choice)) {
					return true;
				}
			}
		}

		return false;
	}



	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @param string $term
	 * @param string $op
	 * @param mixed $choice
	 * @return bool
	 */
	public function testChangedTerm(Entity\Ticket $ticket, $term, $op, $choice)
	{
		$tracker = $this->tracker;
		if (!$tracker) return false;

		if (!$tracker->isPropertyChanged($term)) {
			return false;
		}

		// No specific value check
		if ($op == 'changed') {
			return true;
		}

		$info = $tracker->getChangedProperty($term);

		if (strpos($op, '_to') !== false) {
			// Changed to is the same as testing the current value!
			$ticket2 = $ticket;
		} else {
			$ticket2 = $tracker->getOriginalTicket();
		}

		$rangeop = Strings::extractRegexMatch('#_(gte|lte|gt|lt)$#', $op, 1);

		if (strpos($op, 'not_') !== false) {
			if ($rangeop) {
				$pass = !$this->testTerm($ticket2, $term, $rangeop, $choice);
			} else {
				$pass = $this->testTerm($ticket2, $term, 'not', $choice);
			}
		} else {
			if ($rangeop) {
				$pass = $this->testTerm($ticket2, $term, $rangeop, $choice);
			} else {
				$pass = $this->testTerm($ticket2, $term, 'is', $choice);
			}
		}

		return $pass;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 * @param string $term
	 * @param string $op
	 * @param mixed $choice
	 * @return bool
	 */
	public function testTerm(Entity\Ticket $ticket, $term, $op, $choice)
	{
		$tracker = $this->tracker;

		// $term of people_field[12] becomes $term=people_field, $term_id=12 etc
		$m = null;
		$term_id = null;
		if (preg_match('#^(.*?)\[(.*?)\]$#', $term, $m)) {
			$term = $m[1];
			$term_id = $m[2];
		}

		$person = $ticket->person ? $ticket->person : null;
		$org    = $person && $person->organization ? $person->organization : null;

		switch ($term) {

			case 'date_created':
				if (!$this->_testDateMatch($ticket['date_created'], $op, $choice)) return false;
				break;

			case 'is_new_user':
				return $ticket->person->isNewPerson();
				break;

			case 'is_not_new_user':
				return !$ticket->person->isNewPerson();
				break;

			case 'creation_system':
				$choice = (array)$choice;
				$choice = array_pop($choice);

				if (!$tracker->isNewTicket() && ($reply = $tracker->getNewReply())) {
					$creation_system = $reply->creation_system;
				} else {
					$creation_system = $ticket->creation_system;
				}

				if ($op == 'is') {
					if ($creation_system != $choice) return false;
				}
				if ($op == 'not') {
					if ($creation_system == $choice) return false;
				}
				break;

			case 'creation_system_option':
				$url = isset($choice['website_url']) ? $choice['website_url'] : '';

				if (!$this->_testStringMatch($ticket->creation_system_option, $op, $url)) {
					return false;
				}

				break;

			case 'is_via_email':
				if (!$ticket->email_reader) {
					return false;
				}
				break;

			case 'is_via_email_reply':
				if (!$ticket->email_reader_action || strpos($ticket->email_reader_action, 'reply') === false) {
					return false;
				}
				break;

			case 'is_via_interface':
				if (!defined('DP_INTERFACE') || !in_array(DP_INTERFACE, array('user', 'agent'))) {
					return false;
				}
				break;

			case 'agent_performer':

				$performer = $tracker ? $tracker->getPersonPerformer() : null;
				if (!$performer && App::getCurrentPerson() && App::getCurrentPerson()->getId()) {
					$performer = App::getCurrentPerson();
				}

				if (!$performer || !$performer->is_agent) {
					if ($op == self::OP_CONTAINS) {
						return false;
					}
				} else {
					$agent_ids = $choice['agent_ids'];
					$any = false;
					foreach ($agent_ids as $id) {
						if ($person->getId() == $id) {
							$any = true;
							if ($op == self::OP_NOTCONTAINS) {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS && !$any) {
						return false;
					}
				}

				break;

			case 'user_performer_email':

				$performer = $tracker ? $tracker->getPersonPerformer() : null;
				if (!$performer && App::getCurrentPerson() && App::getCurrentPerson()->getId()) {
					$performer = App::getCurrentPerson();
				}

				if (!$performer) {
					if ($op == self::OP_CONTAINS) {
						return false;
					}
				} else {
					$email = $performer->getPrimaryEmailAddress();
					if (!$this->_testStringMatch($email, $op, $choice['user_email'])) {
						return false;
					}
				}

				break;

			case 'action_performer':
				$is_agent = App::getCurrentPerson()->isAgent();

				$choice = (array)$choice;
				$choice = array_pop($choice);

				if ($choice == 'agent') {
					if ($is_agent) {
						if ($op != 'is') return false;
					} else {
						if ($op != 'not') return false;
					}
				} else {
					if ($is_agent) {
						if ($op	!= 'not') return false;
					} else {
						if ($op != 'is') return false;
					}
				}
				break;

			case 'robot_email':
				if (!$ticket->email_reader) {
					return false;
				}

				$auto = $ticket->email_reader->getHeader('Auto-Submitted')->getAllParts();
				foreach ($auto as $v) {
					$v = strtolower($v);
					if ($v == 'auto-replied' || $v == 'auto-notified' || $v == 'auto-generated') {
						return true;
					}
				}
				break;

			case 'to_address':

				if (!$ticket->email_reader) {
					return false;
				}

				$check = strtolower($choice['to_address']);

				$tos = $ticket->email_reader->getToAddresses();
				foreach ($tos as $to) {
					$to = $to->getEmail();
					$to = strtolower($to);

					if ($check == $to) {
						return true;
					}
				}

				return false;

				break;

			case 'cc_address':

				if (!$ticket->email_reader) {
					return false;
				}

				$check = strtolower($choice['cc_address']);

				$ccs = $ticket->email_reader->getCcAddresses();
				foreach ($ccs as $cc) {
					$cc = $cc->getEmail();
					$cc = strtolower($cc);

					if ($check == $cc) {
						return true;
					}
				}

				return false;
				break;

			case 'email_to_email':

				if (!$ticket->email_reader) {
					return false;
				}

				$check = strtolower($choice['email_address']);

				$tos = $ticket->email_reader->getToAddresses();
				$match = false;
				foreach ($tos as $to) {
					$to = $to->getEmail();
					$to = strtolower($to);

					if ($this->_testStringMatch($to, $op, $check)) {
						$match = true;
					}
				}

				if (!$match) {
					return false;
				}
				break;

			case 'email_to_name':

				if (!$ticket->email_reader) {
					return false;
				}

				$check = strtolower($choice['name']);

				$tos = $ticket->email_reader->getToAddresses();
				$match = false;
				foreach ($tos as $to) {
					$to = $to->getName();
					$to = strtolower($to);

					if ($this->_testStringMatch($to, $op, $check)) {
						return true;
					}
				}

				if (!$match) {
					return false;
				}
				break;

			case 'email_cc_email':

				if (!$ticket->email_reader) {
					return false;
				}

				$check = strtolower($choice['email_address']);

				$tos = $ticket->email_reader->getCcAddresses();
				$match = false;
				foreach ($tos as $to) {
					$to = $to->getEmail();
					$to = strtolower($to);

					if ($this->_testStringMatch($to, $op, $check)) {
						$match = true;
					}
				}

				if (!$match) {
					return false;
				}
				break;

			case 'email_cc_name':

				if (!$ticket->email_reader) {
					return false;
				}

				$check = strtolower($choice['name']);

				$tos = $ticket->email_reader->getCcAddresses();
				$match = false;
				foreach ($tos as $to) {
					$to = $to->getName();
					$to = strtolower($to);

					if ($this->_testStringMatch($to, $op, $check)) {
						$match = true;
						break;
					}
				}

				if (!$match) {
					return false;
				}
				break;

			case 'email_from_email':

				if (!$ticket->email_reader) {
					return false;
				}

				$check = strtolower($choice['email_address']);

				$to = $ticket->email_reader->getFromAddress();
				$to = $to->getEmail();
				$to = strtolower($to);

				if (!$this->_testStringMatch($to, $op, $check)) {
					return false;
				}
				break;

			case 'email_from_name':

				if (!$ticket->email_reader) {
					return false;
				}

				if (empty($choice['email_name'])) {
					return true;
				}

				$check = strtolower($choice['email_name']);

				$to = $ticket->email_reader->getFromAddress();
				$to = $to->getName();
				$to = strtolower($to);

				if (!$this->_testStringMatch($to, $op, $check)) {
					return false;
				}
				break;

			case 'email_account_bcc':

				if (!$ticket->email_reader) {
					return false;
				}

				$matcher_service = App::getSystemService('GatewayAddressMatcher');
				$found_match = $matcher_service->getMatchingAddressFromReader($ticket->email_reader);

				if (!$found_match) {
					return true;
				}
				return false;

				break;

			case 'email_subject':
				if (!$ticket->email_reader) {
					return false;
				}
				$subject = $ticket->email_reader->getSubject()->getSubjectUtf8();
				if (!$this->_testStringMatch($subject, $op, $choice['subject'])) {
					return false;
				}
				break;

			case 'email_body':
				if (!$ticket->email_reader) {
					return false;
				}
				$body = $ticket->email_reader->getBodyText()->getBodyUtf8();
				if (!$body) {
					$body = strip_tags($ticket->email_reader->getBodyHtml()->getBodyUtf8());
				}
				if (!$this->_testStringMatch($body, $op, $choice['message'])) {
					return false;
				}
				break;

			case 'email_header':
				if (!$ticket->email_reader) {
					return false;
				}

				$header = $ticket->email_reader->getHeader($choice['header_name']);
				if (!$header) {
					return false;
				}
				$header = $header->getAllParts();

				$match = false;
				foreach ($header as $h) {
					if ($this->_testStringMatch($h, $op, $choice['header_value'])) {
						$match = true;
						break;
					}
				}
				if (!$match) {
					return false;
				}

				break;

			case 'email_has_attach':
				if (!$ticket->email_reader) {
					return false;
				}

				if (!$ticket->email_reader->getAttachments()) {
					return false;
				}
				break;

			case 'message':
				$reply = $this->tracker->getNewReply();
				if (!$reply) {
					return false;
				}
				if (!$this->_testStringMatch($reply->getMessageText(), $op, $choice['message'])) {
					return false;
				}
				break;

			case 'new_reply_agent':
				$reply = $this->tracker->getNewAgentReply();
				if (!$reply || $reply->is_agent_note) {
					return false;
				}
				break;

			case 'new_reply_note':
				$reply = $this->tracker->getNewAgentReply();
				if (!$reply || !$reply->is_agent_note) {
					return false;
				}
				break;

			case 'new_reply_user':
				if (!$this->tracker->getNewUserReply()) {
					return false;
				}
				break;

			case 'day_created':

				$days = isset($choice['days']) ? (array)$choice['days'] : array();
				$day = $ticket->person->getDateForTime('@' . $ticket->date_created->getTimestamp())->format('w');

				if (!in_array($day, $days)) {
					return false;
				}

				break;

			case 'time_created':

				$date_created = clone $ticket->date_created;
				if (!empty($choice['timezone'])) {
					$date_created->setTimezone(new \DateTimeZone($choice['timezone']));
				}

				$hour = (int)$date_created->format('H');
				$min  = (int)$date_created->format('i');

				$compare_hour = isset($choice['hour1']) ? $choice['hour1'] : -1;
				$compare_min  = isset($choice['minute1']) ? $choice['minute1'] : -1;

				if ($compare_hour == -1 || $compare_min == -1) {
					return false;
				}

				if ($op == 'after') {
					// No match if before
					if ($hour < $compare_hour || ($compare_hour == $hour && $min < $compare_min)) {
						return false;
					}
				} else {
					// No match if after
					if ($hour > $compare_hour || ($compare_hour == $hour && $min > $compare_min)) {
						return false;
					}
				}

				break;

			case 'current_day':

				$days = isset($choice['days']) ? (array)$choice['days'] : array();
				$day = $ticket->person->getDateForTime('@' . time())->format('w');

				if (!in_array($day, $days)) {
					return false;
				}

				break;

			case 'current_time':

				$date = new \DateTime('now', new \DateTimeZone('UTC'));

				if (!empty($choice['timezone'])) {
					$date->setTimezone(new \DateTimeZone($choice['timezone']));
					$date = \Orb\Util\Dates::convertToUtcDateTime($date);
				}

				$hour = (int)$date->format('H');
				$min  = (int)$date->format('i');

				$compare_hour = isset($choice['hour1']) ? $choice['hour1'] : -1;
				$compare_min  = isset($choice['minute1']) ? $choice['minute1'] : -1;

				if ($compare_hour == -1 || $compare_min == -1) {
					return false;
				}

				if ($op == 'after') {
					if (!($compare_hour < $hour || ($compare_hour == $hour && $min < $compare_min))) {
						return false;
					}
				} else {
					if ($compare_hour < $hour || ($compare_hour == $hour && $min < $compare_min)) {
						return false;
					}
				}

				break;

			case TicketSearch::TERM_DEPARTMENT:
				if (count($choice) == 1) $choice = array_pop($choice);
				$choice = App::getDataService('Department')->getIdsInTree($choice, true);

				if (!$this->_testChoiceMatch($ticket['department_id'], $op, $choice)) return false;
				break;

			case TicketSearch::TERM_STATUS:
				if (isset($choice['status'])) {
					$choice = $choice['status'];
				}

				if (!$this->_testChoiceMatch($ticket['status_code'], $op, $choice)) return false;
				break;

			case TicketSearch::TERM_CATEGORY:
				if (!$this->_testChoiceMatch($ticket['category_id'], $op, $choice)) return false;
				break;
			case TicketSearch::TERM_PRODUCT:
				if (!$this->_testChoiceMatch($ticket['product_id'], $op, $choice)) return false;
				break;
			case TicketSearch::TERM_PRIORITY:
				if (!$this->_testChoiceMatch($ticket['priority_id'], $op, $choice)) return false;
				break;
			case TicketSearch::TERM_WORKFLOW:
				if (!$this->_testChoiceMatch($ticket['workflow_id'], $op, $choice)) return false;
				break;
			case TicketSearch::TERM_ORGANIZATION:
				if (!$this->_testChoiceMatch($ticket['organization_id'], $op, $choice)) return false;
				break;
			case TicketSearch::TERM_LANGUAGE:
				if (!$this->_testChoiceMatch($ticket['language_id'], $op, $choice)) return false;
				break;
			case TicketSearch::TERM_AGENT:
				if (!$this->_testChoiceMatch($ticket['agent_id'], $op, $choice)) return false;
				break;
			case TicketSearch::TERM_AGENT_TEAM:
				if (!$this->_testChoiceMatch($ticket['agent_team_id'], $op, $choice)) return false;
				break;
			case TicketSearch::TERM_LABEL:

				if ($op == self::OP_IS) $op = self::OP_CONTAINS;
				elseif ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;

				if (is_array($choice) && isset($choice['label'])) {
					$choice = $choice['label'];
				}
				if (is_array($choice)) {
					$choice = array_pop($choice);
				}

				$any = false;
				foreach ($ticket->getLabelManager()->getLabelsArray() as $label) {
					if (strtolower($label) == strtolower($choice)) {
						$any = true;
						if ($op == self::OP_NOTCONTAINS) {
							return false;
						}
					}
				}

				if ($op == self::OP_CONTAINS AND !$any) {
					return false;
				}
				break;
			case TicketSearch::TERM_URGENCY:
				$choice = (array)$choice;
				$choice = array_pop($choice);

				switch ($op) {
					case self::OP_BETWEEN:
						if (!\Orb\Util\Numbers::inRange($ticket['urgency'], $choice['min'], $choice['max'])) return false;
						break;

					case self::OP_IS:
						if ($ticket['urgency'] != $choice['num']) return false;
						break;

					case self::OP_NOT:
						if ($ticket['urgency'] == $choice['num']) return false;
						break;

					case self::OP_LT:
						if (!($ticket['urgency'] < $choice['num'])) return false;
						break;

					case self::OP_LTE:
						if (!($ticket['urgency'] <= $choice['num'])) return false;
						break;

					case self::OP_GT:
						if (!($ticket['urgency'] > $choice['num'])) return false;
						break;

					case self::OP_GTE:
						if (!($ticket['urgency'] >= $choice['num'])) return false;
						break;
				}
				break;
			case TicketSearch::TERM_PARTICIPANT:
				$participant_ids = $ticket->getParticipantIds();

				if ($ticket->part_add_ids) {
					$participant_ids = array_merge($participant_ids, $ticket->part_add_ids);
				}
				if ($del_ids = $ticket->part_del_ids) {
					$participant_ids = array_filter($participant_ids, function($id) use ($del_ids) {
						if (in_array($id, $del_ids)) {
							return false;
						}
						return true;
					});
				}

				if (is_array($choice)) {
					$any = false;
					foreach ($choice as $person_id) {
						$is_in = in_array($person_id, $participant_ids);

						if ($is_in) {
							$any = true;
							if ($op == self::OP_CONTAINS) {
								break;
							} else {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS AND !$any) return false;
				} else {
					if (in_array($choice, $participant_ids)) {
						if ($op == self::OP_NOT) return false;
					} else {
						if ($op == self::OP_IS) return false;
					}
				}
				break;
			case TicketSearch::TERM_SUBJECT:
				$choice = isset($choice['subject']) ? $choice['subject'] : '';

				if (!$this->_testStringMatch($ticket['subject'], $op, $choice)) {
					return false;
				}
				break;
			case TicketSearch::TERM_SENT_TO_ADDRESS:
				$choice = (array)$choice;
				$choice = array_pop($choice);

				$choice = strtolower($choice);
				$has = $ticket->hasSentToAddress($choice);

				switch ($op) {
					case self::OP_IS:
					case self::OP_CONTAINS:
						if (!$has) return false;
						break;
					case self::OP_NOT:
					case self::OP_NOTCONTAINS:
						if ($has) return false;
						break;
				}
				break;

			case TicketSearch::TERM_FEEDBACK_RATING:
				$choice = isset($choice['rating']) ? $choice['rating'] : 'set';

				if ($choice == 'set') {
					if ($op == self::OP_IS) {
						if (!$ticket->date_feedback_rating) return false;
					} else {
						if ($ticket->date_feedback_rating) return false;
					}
				} else {
					$match = false;
					if ($choice == 'positive') {
						if ($ticket->feedback_rating == 1)  $match = true;
					} elseif ($choice == 'negative') {
						if ($ticket->feedback_rating == -1) $match = true;
					} else {
						if ($ticket->feedback_rating == 0)  $match = true;
					}

					if ($op == self::OP_IS) {
						if (!$match) return false;
					} else {
						if ($match) return false;
					}
				}
				break;

			case TicketSearch::TERM_TICKET_FIELD:
				$test = $this->testCustomField('CustomDefTicket', $term_id, $op, $choice, $ticket);
				if (!$test && $test !== null) {
					return false;
				}
				break;

			case PersonSearch::TERM_PERSON_FIELD:
				$test = $this->testCustomField('CustomDefPerson', $term_id, $op, $choice, $ticket->person);
				if (!$test && $test !== null) {
					return false;
				}
				break;

			case TicketSearch::TERM_SLA:
				$sla_id = $choice['sla_id'];

				if ($op == self::OP_IS) $op = self::OP_CONTAINS;
				elseif ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;

				$any = false;
				foreach ($ticket->ticket_slas as $ticket_sla) {
					if ($ticket_sla->sla->id == $sla_id) {
						$any = true;
						if ($op == self::OP_NOTCONTAINS) {
							return false;
						}
					}
				}

				if ($op == self::OP_CONTAINS AND !$any) {
					return false;
				}
				break;

			case TicketSearch::TERM_SLA_STATUS:
				$sla_status = $choice['sla_status'];
				$sla_id = $choice['sla_id'];

				if ($op == self::OP_IS) $op = self::OP_CONTAINS;
				elseif ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;

				$any = false;
				foreach ($ticket->ticket_slas as $ticket_sla) {
					if ($ticket_sla->sla_status == $sla_status && (!$sla_id || $ticket_sla->sla->id = $sla_id)) {
						$any = true;
						if ($op == self::OP_NOTCONTAINS) {
							return false;
						}
					}
				}

				if ($op == self::OP_CONTAINS AND !$any) {
					return false;
				}
				break;
				break;

			case PersonSearch::TERM_EMAIL:
			case PersonSearch::TERM_EMAIL_DOMAIN:
			case PersonSearch::TERM_DATE_CREATED:
			case PersonSearch::TERM_LABEL:
			case PersonSearch::TERM_LANGUAGE:
			case PersonSearch::TERM_CONTACT_ADDRESS:
			case PersonSearch::TERM_CONTACT_IM:
			case PersonSearch::TERM_CONTACT_PHONE:
			case PersonSearch::TERM_NAME:
			case PersonSearch::TERM_USERGROUP:
			case PersonSearch::TERM_ORGANIZATION:
				$search = new PersonSearch();
				$search->addTerm($term, $op, $choice);

				if (!$search->doesPersontMatch($ticket->person)) {
					return false;
				}

				break;

			case 'gateway_account':
				$gid = $ticket->email_gateway ? $ticket->email_gateway->getId() : 0;
				if (!$this->_testChoiceMatch($gid, $op, $choice)) {
					return false;
				}
				break;

			case 'gateway_address':
				$gid = $ticket->email_gateway_address ? $ticket->email_gateway_address->getId() : 0;
				if (!$this->_testChoiceMatch($gid, $op, $choice)) {
					return false;
				}
				break;

			case 'api_key':
				$api_key = $this->tracker->getExtra('api_key');
				if (!$api_key || !$this->_testChoiceMatch($api_key->id, $op, $choice)) {
					return false;
				}
				break;

			############################################################################################################
			# Organization Terms
			############################################################################################################

			case OrganizationSearch::TERM_NAME:
				if (is_array($choice)) {
					$choice = array_pop($choice);
				}

				$name = $org ? strtolower($org->name) : '';
				$choice = strtolower($choice);

				switch ($op) {
					case self::OP_IS:
						if ($name != $choice) return false;
						break;
					case self::OP_NOT:
						if ($name == $choice) return false;
						break;
					case self::OP_CONTAINS:
						if (strpos($name, $choice) === false) return false;
						break;
					case self::OP_NOTCONTAINS:
						if (strpos($name, $choice) !== false) return false;
						break;
				}
				break;

			case OrganizationSearch::TERM_EMAIL_DOMAIN:
				if (!$org) {
					if ($op == self::OP_IS || $op == self::OP_CONTAINS) {
						return false;
					}
				} else {
					$any = false;
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}
					foreach ($org->email_domains as $domain) {
						if (strpos(strtolower($domain['domain']), strtolower($choice)) !== false) {
							$any = true;
							if ($op == self::OP_NOTCONTAINS) {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS AND !$any) {
						return false;
					}
				}
				break;

			case OrganizationSearch::TERM_CONTACT_ADDRESS:
			case OrganizationSearch::TERM_CONTACT_IM:
			case OrganizationSearch::TERM_CONTACT_PHONE:
				if (!$org) {
					if ($op == self::OP_IS || $op == self::OP_CONTAINS) {
						return false;
					}
				} else {
					if (is_array($choice)) {
						$choice = array_pop($choice);
					}

					if ($term == OrganizationSearch::TERM_CONTACT_ADDRESS) $field = 'addresss';
					if ($term == OrganizationSearch::TERM_CONTACT_IM)      $field = 'instant_message';
					if ($term == OrganizationSearch::TERM_CONTACT_PHONE)   $field = 'phone';

					$any = false;
					foreach ($org->getContactData($field) as $cd) {
						if ($cd->checkStringMatch($choice)) {
							$any = true;
							if ($op == self::OP_NOTCONTAINS) {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS AND !$any) {
						return false;
					}
				}
				break;

			case OrganizationSearch::TERM_LABEL:

				if ($op == self::OP_IS) $op = self::OP_CONTAINS;
				elseif ($op == self::OP_NOT) $op = self::OP_NOTCONTAINS;

				if (!$org) {
					if ($op == self::OP_IS || $op == self::OP_CONTAINS) {
						return false;
					}
				} else {
					$any = false;
					if (isset($choice['label'])) {
						$choice = $choice['label'];
					}

					foreach ($org->getLabelManager()->getLabelsArray() as $label) {
						if (strpos(strtolower($label), strtolower($choice)) !== false) {
							$any = true;
							if ($op == self::OP_NOTCONTAINS) {
								return false;
							}
						}
					}

					if ($op == self::OP_CONTAINS AND !$any) {
						return false;
					}
				}
				break;

			case OrganizationSearch::TERM_ORGANIZATION_FIELD:
				if (!$org) {
					if ($op == self::OP_IS || $op == self::OP_CONTAINS) {
						return false;
					}
				} else {
					$test = $this->testCustomField('CustomDefOrganization', $term_id, $op, $choice, $ticket->person);
					if (!$test && $test !== null) {
						return false;
					}
				}
				break;

			case 'org_manager':
				if (!$org) {
					if ($op == self::OP_IS) {
						return false;
					}
				} else {
					$managers = App::getEntityRepository('DeskPRO:Organization')->getManagers($org);
					$exists = count($managers) > 0;

					if ($op == self::OP_IS && !$exists) {
						return false;
					} else if ($op == self::OP_NOT && $exists) {
						return false;
					}
				}
				break;

			default:
				$e = new \InvalidArgumentException("Unknown trigger criteria: " . $term);
				$einfo = \DeskPRO\Kernel\KernelErrorHandler::getExceptionInfo($e);
				\DeskPRO\Kernel\KernelErrorHandler::logErrorInfo($einfo);

				return false;
		}

		return true;
	}


	/**
	 * @param string  $type      The custom field type: CustomDefTicket or CustomDefPerson
	 * @param int     $term_id   The field ID
	 * @param string  $op        The test operation
	 * @param mixed   $choice    The value to test against
	 * @param mixed   $obj       The ticket or person object
	 * @return bool|null
	 */
	protected function testCustomField($type, $term_id, $op, $choice, $obj)
	{
		$field = App::getEntityRepository('DeskPRO:'.$type)->find($term_id);
		if (!$field) return null;

		$search_type = $field->getHandler()->getSearchType();

		if (!isset($choice['custom_fields']['field_' . $term_id])) {
			return null;
		}

		$choice = $choice['custom_fields']['field_' . $term_id];

		switch ($search_type) {
			case 'input':
			case 'value':

				if (is_array($choice)) {
					$choice = implode(' ', $choice);
				}

				$set_value = $obj->getCustomDataForField($term_id);
				if ($set_value) {
					$set_value = $set_value->getData();
				}
				if (is_string($set_value)) {
					$set_value = Strings::utf8_strtolower($set_value);
				}

				$choice = Strings::utf8_strtolower($choice);

				switch ($op) {
					case self::OP_IS:
						if ($set_value != $choice) return false;
						break;
					case self::OP_NOT:
						if ($set_value == $choice) return false;
						break;
					case self::OP_CONTAINS:
						if (strpos($set_value, $choice) === false) return false;
						break;
					case self::OP_NOTCONTAINS:
						if (strpos($set_value, $choice) !== false) return false;
						break;
				}
				break;

			case 'id':
				$choices_in = array();
				foreach ((array)$choice as $c) {
					$choices_in[] = (int)$c;
				}

				$has_choices = array();
				foreach ($choices_in as $id) {
					$c = $obj->getCustomDataForField($id);
					if ($c) {
						$has_choices[$id] = $id;
					}
				}

				switch ($op) {
					case self::OP_CONTAINS:
					case self::OP_IS:
						if (!$has_choices) return false;
						break;

					case self::OP_NOTCONTAINS:
					case self::OP_NOT:
						if ($has_choices) return false;
						break;
				}
				break;
		}

		return true;
	}


	/**
	 * @param mixed $value
	 * @param string $op
	 * @param mixed $choice
	 * @return bool
	 */
	protected function _testChoiceMatch($value, $op, $choice)
	{
		if (is_array($choice)) {
			if ($op == self::OP_IS || $op == self::OP_CONTAINS) {
				return in_array($value, $choice);
			} elseif ($op == self::OP_NOT || $op == self::OP_NOTCONTAINS) {
				return !in_array($value, $choice);
			}
		} else {
			if ($op == self::OP_IS || $op == self::OP_CONTAINS) {
				return $value == $choice;
			} elseif ($op == self::OP_NOT || $op == self::OP_NOTCONTAINS) {
				return $value != $choice;
			}
		}

		return false;
	}

	/**
	 * @param mixed $value
	 * @param string $op
	 * @param mixed $choice
	 * @param bool $suffix_only
	 * @param bool $force_like
	 * @return bool
	 */
	protected function _testStringMatch($value, $op, $choice, $suffix_only = false, $force_like = false)
	{
		if (is_array($choice) AND count($choice) == 1) {
			$choice = Arrays::getFirstItem($choice);
		}

		if ($op == self::OP_IS_REGEX || $op	== self::OP_NOT_REGEX) {

			if (is_array($choice)) {
				$choice = array_pop($choice);
			}

			$regex = (string)$choice;
			if ($regex) {
				$regex = Strings::getInputRegexPattern($regex);
			}

			if (!$regex) {
				return false;
			}

			$found = preg_match($regex, $value);
			if ($op == self::OP_IS_REGEX) {
				return $found;
			} else {
				return (!$found);
			}

		} elseif (!$force_like AND ($op == self::OP_IS OR $op == self::OP_NOT)) {
			$choices_in = (array)$choice;

			$found = false;
			foreach ($choices_in as $c) {
				if (strtolower($value) == strtolower($c)) {
					$found = true;
					break;
				}
			}

			if ($op == self::OP_IS) {
				return $found;
			} else {
				return (!$found);
			}

		} else {

			$choices_in = (array)$choice;

			$found = false;
			foreach ($choices_in as $c) {
				if ($suffix_only) {
					if (\Orb\Util\Strings::endsWith($c, $value)) {
						$found = true;
						break;
					}
				} else {
					if (stripos($value, $c) !== false) {
						$found = true;
						break;
					}
				}
			}

			if ($op == self::OP_CONTAINS) {
				return $found;
			} else {
				return (!$found);
			}
		}
	}


	/**
	 * @param string $value
	 * @param string $op
	 * @param string $choice
	 * @return bool
	 */
	protected function _testDateMatch($value, $op, $choice)
	{
		$choice = (array)$choice;

		$date1 = null;
		if (isset($choice['date1'])) {
			$date1 = $choice['date1'];
		} else if (isset($choice[0])) {
			$date1 = $choice[0];
		}

		$date2 = null;
		if (isset($choice['date2'])) {
			$date2 = $choice['date2'];
		} else if (isset($choice[1])) {
			$date2 = $choice[1];
		}

		if ($date1) {
			if ($date1 instanceof \DateTime) {
				$date1 = $date1->getTimestamp();
			} elseif (!Numbers::isInteger($date1)) {
				$date1 = strtotime($date1);
			}
		}

		if ($date2) {
			if ($date2 instanceof \DateTime) {
				$date2 = $date1->getTimestamp();
			} elseif (!Numbers::isInteger($date1)) {
				$date2 = strtotime($date2);
			}
		}

		// There should always be at least one date
		if ($date1 === null AND $date2 === null) {
			return false;
		}

		// Normalize operations
		if ($op == self::OP_LT) $op = self::OP_LTE;
		if ($op == self::OP_GT) $op = self::OP_GTE;

		// Between with only one date is invalid, so
		// we'll decide which op we really want to do
		if ($op == self::OP_BETWEEN && ($date1 === null or $date2 === null)) {
			if ($date1) {
				$op = self::OP_GTE;
			} else {
				$op = self::OP_LTE;
			}
		}

		if (!$date1) $date1 = 0;
		if (!$date2) $date2 = 0;

		if ($value instanceof \DateTime) {
			$value = $value->getTimestamp();
		} elseif (is_string($value) AND !ctype_digit($value)) {
			$value = strtotime($value);
		}

		if ($op == self::OP_BETWEEN) {

			// Make date2 'end of day'
			$date2 = mktime(
				23,
				59,
				59,
				date('m', $date2),
				date('d', $date2),
				date('Y', $date2)
			);

			return ($value >= $date1 AND $value <= $date2);
		} elseif ($op == self::OP_GTE) {
			$date = $date1 ? $date1 : $date2;
			return ($value >= $date);
		} else {
			$date = $date1 ? $date1 : $date2;
			return ($value <= $date);
		}
	}


	/**
	 * Compiles these sets of terms into a number of JS tests on a 'ticket' variable.
	 * Note that this just generates the tests, so any implementation still has to wrap it in a function
	 * body etc.
	 *
	 * @param string $mode 'all' or 'any'
	 * @return string
	 */
	public function compileTermsToJavascript($mode = 'all')
	{
		$js = array();

		if ($mode == 'all') {
			$test_pass = '';
			$test_fail = 'return false;';
			$test_bottom = 'return true;';
		} else {
			$test_pass = 'return true;';
			$test_fail = '';
			$test_bottom = 'return false;';
		}

		$rb = \Application\DeskPRO\UI\RuleBuilder::newTermsBuilder();
		$terms = $rb->readForm($this->terms);

		foreach ($terms as $info) {

			$term = $info['type'];

			if (!$term) continue;

			$op = $info['op'];
			$choice = $info['options'];

			if (count($choice) == 1) {
				$choice = array_pop($choice);
			}

			switch ($term) {
				case TicketSearch::TERM_DEPARTMENT:
					$ids = array();
					foreach ((array)$choice as $cond) {
						$ids = array_merge($ids, App::getDataService('Department')->getIdsInTree($cond, true));
					}
					$ids = Arrays::castToType($ids, 'int');
					if (count($ids) == 1) $ids = $ids[0];

					$js[] = $this->_compileJsChoiceTermCondition("ticket.getDepartmentId()", $op, $ids) . " { $test_pass } else { $test_fail } ";
					break;
				case TicketSearch::TERM_CATEGORY:
					$ids = array();
					foreach ((array)$choice as $cond) {
						$ids = array_merge($ids, App::getEntityRepository('DeskPRO:TicketCategory')->getIdsInTree($cond, true));
					}
					$ids = Arrays::castToType($ids, 'int');
					if (count($ids) == 1) $ids = $ids[0];

					$js[] = $this->_compileJsChoiceTermCondition("ticket.getCategoryId()", $op, $ids) . " { $test_pass } else { $test_fail } ";
					break;
				case TicketSearch::TERM_PRODUCT:
					$js[] = $this->_compileJsChoiceTermCondition("ticket.getProductId()", $op, $choice) . " { $test_pass } else { $test_fail } ";
					break;
				case TicketSearch::TERM_PRIORITY:
					$js[] = $this->_compileJsChoiceTermCondition("ticket.getPriorityVal()", $op, $choice) . " { $test_pass } else { $test_fail } ";
					break;
				case TicketSearch::TERM_ORGANIZATION:
					$js[] = $this->_compileJsChoiceTermCondition("ticket.getOrganizationId()", $op, $choice) . " { $test_pass } else { $test_fail } ";
					break;
				case TicketSearch::TERM_LANGUAGE:
					$js[] = $this->_compileJsChoiceTermCondition("ticket.getLanguageId()", $op, $choice) . " { $test_pass } else { $test_fail } ";
					break;
				case TicketSearch::TERM_AGENT:
					$js[] = $this->_compileJsChoiceTermCondition("ticket.getAgentId()", $op, $choice) . " { $test_pass } else { $test_fail } ";
					break;
			}
		}

		$js[] = $test_bottom;

		$js = implode(' ', $js);

		return $js;
	}


	/**
	 * @param mixed $value
	 * @param string $op
	 * @param string $choice
	 * @return string
	 */
	protected function _compileJsChoiceTermCondition($value, $op, $choice)
	{
		if (is_array($choice) AND count($choice) == 1) {
			$choice = array_pop($choice);
		}

		if (is_array($choice)) {
			if (Arrays::checkAll($choice, function($v) { return Numbers::isInteger($v); })) {
				$choice = Arrays::castToType($choice, 'integer');
			}

			$choice = json_encode(array_values($choice));
			if ($op == self::OP_IS) {
				return "if ($choice.indexOf($value) !== -1) ";
			} elseif ($op == self::OP_NOT) {
				return "if ($choice.indexOf($value) === -1) ";
			} else {
				return "if (0 /*invalid*/) ";
			}
		} else {
			if (Numbers::isInteger($choice)) {
				$choice = (int)$choice;
			}
			$choice = json_encode($choice);
			if ($op == self::OP_IS) {
				return "if ($value == $choice) ";
			} elseif ($op == self::OP_NOT) {
				return "if ($value != $choice) ";
			} elseif ($op == self::OP_GT) {
				return "if ($value > $choice) ";
			} elseif ($op == self::OP_GTE) {
				return "if ($value >= $choice) ";
			} elseif ($op == self::OP_LT) {
				return "if ($value < $choice) ";
			} elseif ($op == self::OP_LTE) {
				return "if ($value <= $choice) ";
			}
		}

		return '';
	}

	/**
	 * @return array
	 */
	public function getDescriptions($as_html = false)
	{
		$descs = array();
        $tr = App::getTranslator();

		foreach ($this->terms as $info) {

			if (empty($info['type']) || empty($info['op']) || empty($info['options'])) {
				continue;
			}

			$term = $info['type'];
			if (!$term) continue;

			$op = $info['op'];
			$choice = $info['options'];

			if (strpos($op, 'changed') !== false) {
				$term = $this->getTermDescription($term, $op, $choice, $as_html);
				if ($term) {
					$descs[] = $term;
				}
			} else {
				$term = $this->getTermDescription($term, $op, $choice, $as_html);
				if ($term) {
					$descs[] = $term;
				} else {
					error_log("Unknown term description for {$info['type']}");
				}
			}
		}

		return $descs;
	}


	/**
	 * Compiles a term into an english phrase to describe the test
	 *
	 * @param string $term
	 * @param string $op
	 * @param mixed $choice
	 * @return string
	 */
	public function getTermDescription($term, $op, $choice, $as_html = false)
	{
		$term_summary = new \Application\DeskPRO\Translate\TermSummary();
		if (strpos($term, 'person_') === 0) {
			$summary = $term_summary->getSummary($term, $op, $choice);
			if (!$summary) {
				$summary = $term_summary->getSummary(preg_replace('#^person_#', '', $term), $op, $choice);
			}
		} else {
			$summary = $term_summary->getSummary("ticket_$term", $op, $choice);
		}

		if (!$summary) {
			$summary = $term_summary->getSummary($term, $op, $choice);
		}

		if ($as_html) {
			$summary = htmlspecialchars($summary);
			$summary = str_replace(
				array('&lt;error&gt;', '&lt;/error&gt;'),
				array('<span class="term-error">', '</span>'),
				$summary
			);
		}

		return $summary;
	}
}
