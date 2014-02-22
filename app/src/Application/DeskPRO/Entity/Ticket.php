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

namespace Application\DeskPRO\Entity;

use DeskPRO\Kernel\KernelErrorHandler;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;
use Orb\Util\Util;

/**
 * Ticket
 *
 *
 * @property \Application\DeskPRO\Entity\Person $person
 */
class Ticket extends \Application\DeskPRO\Domain\DomainObject
{
	const CREATED_WEB_PERSON        = 'web.person';
	const CREATED_WEB_PERSON_PORTAL = 'web.person.portal';
	const CREATED_WEB_PERSON_WIDGET = 'web.person.widget';
	const CREATED_WEB_PERSON_EMBED  = 'web.person.embed';
	const CREATED_WEB_AGENT         = 'web.agent';
	const CREATED_WEB_AGENT_PORTAL  = 'web.agent.portal';
	const CREATED_WEB_API           = 'web.api';
	const CREATED_GATEWAY_PERSON    = 'gateway.person';
	const CREATED_GATEWAY_AGENT     = 'gateway.agent';

	const STATUS_AWAITING_AGENT = 'awaiting_agent';
	const STATUS_AWAITING_USER = 'awaiting_user';
	const STATUS_RESOLVED = 'resolved';
	const STATUS_CLOSED = 'closed';
	const STATUS_HIDDEN = 'hidden';

	const HIDDEN_STATUS_VALIDATING = 'validating';
	const HIDDEN_STATUS_SPAM = 'spam';
	const HIDDEN_STATUS_DELETED = 'deleted';
	const HIDDEN_STATUS_TEMP = 'temp';

	/**#@+
	 * These strings in $notify_email_name have special meanings.
	 * NOTIFY_NAME_HELPDESK: The helpdesk name
	 * NOTIFY_NAME_PERSON: The person who sent the reply, or if no person (eg auto-response), then the helpdesk
	 */
	const NOTIFY_NAME_HELPDESK = '__DP_HELPDESK__';
	const NOTIFY_NAME_PERSON = '__DP_PERSON__';
	/**#@-*/

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $ref = null;

	/**
	 * @var int
	 */
	protected $auth;

	/**
	 * Parent ticket
	 *
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $parent_ticket = null;

	/**
	 * The language the ticket is in
	 *
	 * @var \Application\DeskPRO\Entity\Language
	 */
	protected $language = null;

	/**
	 * @var \Application\DeskPRO\Entity\Department
	 */
	protected $department = null;

	/**
	 * @var \Application\DeskPRO\Entity\TicketCategory
	 */
	protected $category = null;

	/**
	 * @var \Application\DeskPRO\Entity\TicketPriority
	 */
	protected $priority = null;

	/**
	 * @var \Application\DeskPRO\Entity\TicketWorkflow
	 */
	protected $workflow = null;

	/**
	 * @var \Application\DeskPRO\Entity\Product
	 */
	protected $product = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \Application\DeskPRO\Entity\PersonEmail
	 */
	protected $person_email = null;

	/**
	 * @var \Application\DeskPRO\Entity\PersonEmailValidating
	 */
	protected $person_email_validating = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $agent = null;

	/**
	 * @var \Application\DeskPRO\Entity\AgentTeam
	 */
	protected $agent_team = null;

	/**
	 * @var \Application\DeskPRO\Entity\Organization
	 */
	protected $organization = null;

	/**
	 * @var \Application\DeskPRO\Entity\ChatConversation
	 */
	protected $linked_chat = null;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $attachments;

	/**
	 */
	protected $access_codes;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $messages;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $custom_data;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $labels;

	/**
	 * @var string[]
	 */
	protected $original_labels = null;

	/**
	 * The email address the ticket was sent to if it came in via a gateway
	 *
	 * @var string
	 */
	protected $sent_to_address = '';

	/**
	 * The gateway this ticket originated from
	 *
	 * @var \Application\DeskPRO\Entity\EmailGateway
	 */
	protected $email_gateway = null;

	/**
	 * The gateway email address the ticket matched
	 *
	 * @var \Application\DeskPRO\Entity\EmailGatewayAddress
	 */
	protected $email_gateway_address = null;

	/**
	 * The "from" address to send from
	 * @var string
	 */
	protected $notify_email = '';

	/**
	 * The name to send from
	 * @var string
	 */
	protected $notify_email_name = '';

	/**
	 * The "from" address to send from for agent emails
	 * @var string
	 */
	protected $notify_email_agent = '';

	/**
	 * The name to send from for agent emails
	 *
	 * @var string
	 */
	protected $notify_email_name_agent = '';

	/**
	 * @var string
	 */
	protected $creation_system;

	/**
	 * Optional information about the creation system. For example, source URL the ticket came from.
	 *
	 * @var string
	 */
	protected $creation_system_option = '';

	/**
	 * @var string
	 */
	protected $ticket_hash;

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var string
	 */
	protected $hidden_status = null;

	/**
	 * @var string
	 */
	protected $validating = null;

	/**
	 * Is the ticket on hold?
	 *
	 * @var bool
	 */
	protected $is_hold = false;

	/**
	 * @var int
	 */
	protected $urgency = 1;

	/**
	 * @var int
	 */
	protected $feedback_rating = null;

	/**
	 * @var \DateTime
	 */
	protected $date_feedback_rating = null;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var \DateTime
	 */
	protected $date_resolved = null;

	/**
	 * @var \DateTime
	 */
	protected $date_closed = null;

	/**
	 * @var \DateTime
	 */
	protected $date_first_agent_assign = null;

	/**
	 * @var \DateTime
	 */
	protected $date_first_agent_reply = null;

	/**
	 * @var \DateTime
	 */
	protected $date_last_agent_reply = null;

	/**
	 * @var \DateTime
	 */
	protected $date_last_user_reply = null;

	/**
	 * @var \DateTime
	 */
	protected $date_agent_waiting = null;

	/**
	 * @var \DateTime
	 */
	protected $date_user_waiting = null;

	/**
	 * @var \DateTime
	 */
	protected $date_status = null;

	/**
	 * @var int
	 */
	protected $total_user_waiting = 0;

	/**
	 * @var int
	 */
	protected $total_to_first_reply = 0;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $locked_by_agent = null;

	/**
	 * @var \DateTime
	 */
	protected $date_locked = null;

	/**
	 * @var bool
	 */
	protected $has_attachments = false;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var string
	 */
	protected $original_subject = '';

	/**
	 * @var array
	 */
	protected $properties = null;

	/**
	 * @var int
	 */
	protected $count_agent_replies = 0;

	/**
	 * @var int
	 */
	protected $count_user_replies = 0;

	/**
	 * @var string|null
	 */
	protected $worst_sla_status = null;

	/**
	 * @var array
	 */
	protected $waiting_times = array();

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $participants;

	/**
	 * Array cache of user participants
	 * @var array
	 * @see getUserParticipants
	 */
	protected $_user_participants;

	public $part_add_ids = array();
	public $part_del_ids = array();

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $charges;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $ticket_slas;

	/**
	 * @var bool
	 */
	protected $_recalculate_slas = false;

	/**
	 * @var bool
	 */
	protected $_reset_slas = false;

	/**
	 * Ticket logger
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $_ticket_logger;

	/**
	 * When true the ticket log doesnt run in the post event
	 * @var bool
	 */
	public $_no_log = false;

	/**
	 * Parts that were originally on the ticket (before any changes)
	 * @var array
	 */
	protected $_loaded_part_ids = array();

	/**
	 * An exploded version of sent_to_addresses
	 *
	 * @var array
	 */
	protected $_sent_to_addresses;

	protected $_label_manager = null;

	/**
	 * @var \Orb\Util\WorkHoursSet|null
	 */
	protected $_work_hours_set = null;

	public $_isRemoved;

	/**
	 * When true the changelog tracker is auto-commited post-flush
	 *
	 * @var bool
	 */
	protected $_auto_commit_changelog = true;

	/**
	 * If the tikcet was created from an email just now, then this is the reader
	 * @var \Application\DeskPRO\EmailGateway\Reader\AbstractReader
	 */
	public $email_reader;

	/**
	 * The action the email reader was used for (reply/note/action)
	 * @var string
	 */
	public $email_reader_action;

	/**
	 * @var null
	 */
	public $_old_status = null;

	/**
	 * Sometimes we need to keep track of certain properties on a
	 * ticket before they have been saved. e.g., labels has a PK on ticket ID and
	 * we cant save them as managed entities until after the tikcet is first saved,
	 * but labels added need to be saved somewhere so we can test them during triggers.
	 *
	 * @var array
	 */
	public $_presave_state = array();

	/**
	 * To get around scoping issues with TicketSla event callbacks, we set the
	 * parent ticket log during TriggerExecutor.
	 */
	public $inserted_log_row_batch;

	public function __construct($tracker = true)
	{
		$this->participants = new \Doctrine\Common\Collections\ArrayCollection();
		$this->messages = new \Doctrine\Common\Collections\ArrayCollection();
		$this->custom_data = new \Doctrine\Common\Collections\ArrayCollection();
		$this->labels = new \Doctrine\Common\Collections\ArrayCollection();
		$this->access_codes = new \Doctrine\Common\Collections\ArrayCollection();
		$this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
		$this->charges = new \Doctrine\Common\Collections\ArrayCollection();
		$this->ticket_slas = new \Doctrine\Common\Collections\ArrayCollection();

		$this['date_created'] = new \DateTime();
		$this['date_status'] = new \DateTime();

		$len = App::getSetting('core_tickets.ptac_auth_code_len');
		$this['auth'] = Strings::random($len, Strings::CHARS_KEY);

		if ($tracker) {
			$this->_initTicketLogger();
			$this->_ticket_logger->recordExtra('ticket_created', true);
		}
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setNoLog()
	{
		$this->_no_log = true;
	}

	public function isLoggingDisabled()
	{
		return $this->_no_log;
	}

	public function _initTicketLogger()
	{
		$this->_old_status = $this->getStatusCode();
		if ($this->_ticket_logger) {
			$this->removePropertyChangedListener($this->_ticket_logger);
		}
		$ticket_logger = new \Application\DeskPRO\Tickets\TicketChangeTracker($this);
		$this->_ticket_logger = $ticket_logger;
		$this->addPropertyChangedListener($ticket_logger);
	}

	public function getSubject()
	{
		if (!$this->subject) {
			return App::getTranslator()->getPhraseText('user.tickets.no_subject');
		}

		return $this->subject;
	}


	/**
	 * Get an array of addresses the ticket was sent To or CC's
	 *
	 * @return array
	 */
	public function getSentToAddresses()
	{
		if (!$this->sent_to_address) {
			return array();
		}

		if ($this->_sent_to_addresses !== null) {
			return $this->_sent_to_addresses;
		}

		$this->_sent_to_addresses = explode(',', $this->sent_to_address);
		$this->_sent_to_addresses = array_combine($this->_sent_to_addresses, $this->_sent_to_addresses);
		return $this->_sent_to_addresses;
	}


	/**
	 * Check if an address was in To or CC
	 *
	 * @param $address
	 * @return bool
	 */
	public function hasSentToAddress($address)
	{
		$address = strtolower($address);
		$this->getSentToAddresses();

		return isset($this->_sent_to_addresses[$address]);
	}


	/**
	 * @param string $addresses
	 */
	public function setSentToAddress($addresses)
	{
		if (is_array($addresses)) {
			$addresses = implode(',', $addresses);
		}

		$addresses = strtolower($addresses);

		$this->setModelField('sent_to_address', $addresses);
		$this->_sent_to_addresses = null;
	}


	public function setSubject($subject)
	{
		$subject = Strings::standardEol($subject);
		$subject = Strings::trimLines($subject);
		$subject = preg_replace("#\n+#", ' ', $subject);

		$this->setModelField('subject', $subject);

		if (!$this->original_subject) {
			$this->setProcessedOriginalSubject($subject);
		}
	}


	/**
	 * Parse off common prefixes on subjects and then set the original subject
	 *
	 * @param $subject
	 */
	public function setProcessedOriginalSubject($subject)
	{
		do {
			$orig = $subject;
			$subject = preg_replace('#^(RE|VS|AW|SV|FW|FWD|VL|WG|FS|VB|RV|VS):\s*#i', '', $subject);
		} while ($orig != $subject);

		$this->setModelField('original_subject', $subject);
	}


	public function getUserParticipants()
	{
		$this->getOriginalParticipantIds();
		$ret = array();

		foreach ($this['participants'] as $p) {
			if (!$p['person']['is_agent']) {
				$ret[] = $p;
			}
		}

		return $ret;
	}

	public function getAgentParticipants()
	{
		$this->getOriginalParticipantIds();

		$ret = array();
		foreach ($this->participants as $p) {
			if ($p->person['is_agent']) {
				$ret[] = $p;
			}
		}

		return $ret;
	}

	/**
	 * The ticket tracker needs to know who was originally added on the ticket, to properly
	 * determine if the pre-updated ticket used to match a filter. So the change tracker uses this construct
	 * the "original ticket" object
	 *
	 * @return array
	 */
	public function getOriginalParticipantIds()
	{
		if ($this->_loaded_part_ids !== null) {
			return $this->_loaded_part_ids;
		}

		$this->_loaded_part_ids = array();
		foreach ($this->participants as $part) {
			$this->_loaded_part_ids[$part->person->getId()] = $part->person->getId();
		}
		return $this->_loaded_part_ids;
	}


	/**
	 * Given an array of agents, sync the current parts with those in the array.
	 * So remove ones that aren't in it, or add new ones
	 *
	 * @param array $parts
	 * @return void
	 */
	public function setAgentParticipants(array $agents)
	{
		$this->getOriginalParticipantIds();
		$current_agent_ids = array();
		foreach ($this->participants as $p) {
			if ($p->person->is_agent) {
				$current_agent_ids[] = $p->person->id;
			}
		}

		$got_agent_ids = array();
		foreach ($agents as $p) {
			$got_agent_ids[] = $p->id;
		}
		foreach ($got_agent_ids as $id) {
			$this->addParticipantPerson($id);
		}

		$remove_agent_ids = array_diff($current_agent_ids, $got_agent_ids);
		foreach ($remove_agent_ids as $id) {
			$this->removeParticipantPerson($id);
		}
	}


	/**
	 * Try to find a user that is a part of this tikcet based on
	 * their email address.
	 * @param string $email_address
	 * @return Person
	 */
	public function findUserByEmail($email_address)
	{
		$email_address = strtolower($email_address);

		// The author
		if ($this->person->findEmailAddress($email_address)) {
			return $this->person;

		// Any of the participants
		} else {
			foreach ($this->getUserParticipants() as $part) {
				if ($part->person->findEmailAddress($email_address)) {
					return $part->person;
				}
			}
		}

		return null;
	}


	/**
	 * Try to find an agent that is part of this ticket based on an email addres
	 *
	 * @param string $email_address
	 * @return Person|null
	 */
	public function findAgentByEmail($email_address)
	{
		if ($this->person->is_agent && $this->person->findEmailAddress($email_address)) {
			return $this->person;
		} else {
			foreach ($this->participants as $part) {
				if ($part->person->is_agent && $part->person->findEmailAddress($email_address)) {
					return $part->person;
				}
			}
		}

		return null;
	}


	/**
	 * Modify urgency by $mod, which can be positive or negative.
	 *
	 * @param int $mod
	 * @param bool $reset_on_reply True to reset this urgency after the next reply
	 */
	public function modifyUrgency($mod, $reset_on_reply = false)
	{
		$old_u = $this->urgency;
		$new_u = \Orb\Util\Numbers::bound($old_u + $mod, 1, 10);

		if ($old_u != $new_u) {
			$this->urgency = $new_u;

			$this->_onPropertyChanged('urgency', $old_u, $new_u);
			if ($reset_on_reply) {
				$real_diff = $old_u - $new_u;// real mod, taking into account bound()
				$this->getTicketLogger()->recordExtra('urgency_reset_reply', $real_diff);
			}
		}
	}


	/**
	 * Set the urgency to a specific value
	 *
	 * @param int $set
	 */
	public function setUrgency($set)
	{
		$old_u = $this->urgency;
		$new_u = \Orb\Util\Numbers::bound($set, 1, 10);

		if ($old_u != $new_u) {
			$this->urgency = $new_u;
			$this->_onPropertyChanged('urgency', $old_u, $new_u);
		}
	}


	/**
	 * @param string $rating
	 */
	public function setFeedbackRating($rating)
	{
		$this->setModelField('feedback_rating', $rating);
		$this->setModelField('date_feedback_rating', new \DateTime());
	}

	/**
	 * @return string
	 */
	public function getFeedbackRatingType()
	{
		if ($this->feedback_rating == 1) return 'positive';
		elseif ($this->feedback_rating == -1) return 'negative';
		else return 'neutral';
	}


	/**
	 * Get a simple array of person ID's of participants.
	 *
	 * @return array
	 */
	public function getParticipantPeopleIds()
	{
		$this->getOriginalParticipantIds();
		$ids = array();
		foreach ($this->getParticipants() as $p) {
			$ids[] = $p['person']['id'];
		}

		return $ids;
	}

	public function getRawParticipants()
	{
		$this->getOriginalParticipantIds();
		return $this->participants;
	}

	public function setRawParticipants($parts)
	{
		$this->getOriginalParticipantIds();
		$this->participants = $parts;
	}


	/**
	 * Check if there exists a person on this ticket with a particular email address.
	 *
	 * @param string $email_address
	 * @return Person|bool
	 */
	public function hasParticipantEmailAddress($email_address)
	{
		if ($this->agent && $this->agent->hasEmailAddress($email_address)) {
			return $this->agent;
		}

		if ($this->person && $this->person->hasEmailAddress($email_address)) {
			return $this->person;
		}

		foreach ($this->participants as $p) {
			if ($p->person->hasEmailAddress($email_address)) {
				return $p->person;
			}
		}

		return false;
	}


	/**
	 * Check if a person ID or a person object is current a participant.
	 *
	 * @param  $person_or_id
	 * @param $only_parts Only check participants (not assigned agent)
	 * @return bool
	 */
	public function hasParticipantPerson($person_or_id)
	{
		$this->getOriginalParticipantIds();
		$person_id = $person_or_id;
		if ($person_or_id instanceof Person) {
			$person_id = $person_or_id['id'];
		}

		// User not commited yet, so obviously they dont exist
		if (!$person_id) {
			return false;
		}

		foreach ($this->participants as $p) {
			if ($p['person']['id'] == $person_id) {
				return $p;
			}
		}

		return false;
	}



	/**
	 * Add a participant
	 *
	 * @param $person_or_id
	 * @return TicketParticipant
	 */
	public function addParticipantPerson($person_or_id)
	{
		$this->getOriginalParticipantIds();
		$person = $person_or_id;
		if (!($person instanceof Person)) {
			$person = App::getEntityRepository('DeskPRO:Person')->find($person);
		}

		if (!$person) {
			return null;
		}

		if ($person->id == $this->person->id && DP_INTERFACE != 'agent') {
			return null;
		}

		if ($ticket_part = $this->hasParticipantPerson($person)) {
			return $ticket_part;
		}

		$ticket_part = new TicketParticipant();
		$ticket_part['person'] = $person;
		$ticket_part['ticket'] = $this;
		$this->participants->add($ticket_part);
		$this->part_add_ids[] = $person->getId();

		if ($this->getTicketLogger()) {
			$this->getTicketLogger()->recordMultiPropertyChanged('participants', null, $person);
		}

		if ($this->_user_participants !== null AND !$person['is_agent']) {
			$this->_user_participants[] = $ticket_part;
		}

		return $ticket_part;
	}



	/**
	 * Remove a participant
	 *
	 * @param  $person_or_id
	 * @return null
	 */
	public function removeParticipantPerson($person_or_id)
	{
		$this->getOriginalParticipantIds();
		$person = $person_or_id;
		if (!($person instanceof Person)) {
			$person = App::getEntityRepository('DeskPRO:Person')->find($person);
		}

		if (!$person) {
			return null;
		}

		foreach ($this->participants as $k => $p) {
			if ($p['person']->getId() == $person->getId()) {
				if ($this->getTicketLogger()) $this->getTicketLogger()->recordMultiPropertyChanged('participants', $p['person'], null);
				$this->participants->remove($k);
				$this->part_del_ids[] = $person->getId();
				return $p;
			}
		}

		return null;
	}

	public function addParticipant(TicketParticipant $part)
	{
		$this->getOriginalParticipantIds();
		$part->ticket = $this;
		$this->participants->add($part);
		$this->part_add_ids[] = $part->person->getId();
		if ($this->getTicketLogger()) $this->getTicketLogger()->recordMultiPropertyChanged('participants', null, $part->person);
	}


	/**
	 * Set agent participants. Agents are added/removed so that
	 * all participants on the ticket are in the array.
	 *
	 * @param array $set_agent_ids
	 * @return void
	 */
	public function setParticipantAgentIds(array $set_agent_ids)
	{
		$this->getOriginalParticipantIds();
		$got_agent_ids = array();
		$remove_ks = array();

		/*
		 * Bug in Doctrine: $this->participants only ever has 1 record,
		 * so we're fetching them manually
		 */

		$participants = App::getOrm()->createQuery("
			SELECT p
			FROM DeskPRO:TicketParticipant p
			WHERE p.ticket = ?1
		")->setParameter(1, $this)->execute();

		foreach ($participants as $k => $part) {
			if (!$part->person['is_agent']) {
				continue;
			}

			if (!in_array($part->person['id'], $set_agent_ids)) {
				$remove_ks[] = $k;
			} else {
				$got_agent_ids[] = $part->person['id'];
			}
		}

		foreach ($remove_ks as $k) {
			App::getOrm()->remove($participants[$k]);
			if ($this->getTicketLogger()) $this->getTicketLogger()->recordMultiPropertyChanged('participants', $participants[$k], null);
		}

		$new_agent_ids = array_diff($set_agent_ids, $got_agent_ids);

		if ($new_agent_ids) {
			foreach ($new_agent_ids as $agent_id) {
				$part = new \Application\DeskPRO\Entity\TicketParticipant();
				$part['person_id'] = $agent_id;

				$this->addParticipant($part);
				if ($this->getTicketLogger()) $this->getTicketLogger()->recordMultiPropertyChanged('participants', null, $part);
			}
		}
	}


	/**
	 * Set user participants.
	 *
	 * If item in $set_user_ids is an array, its expected to be
	 * array(person_id, person_email_id)
	 *
	 * @param array $set_agent_ids
	 * @return void
	 */
	public function setParticipantUserIds(array $set_user_ids)
	{
		$this->getOriginalParticipantIds();
		$got_user_ids = array();

		$set_user_ids_info = array();
		foreach ($set_user_ids as $id) {
			if (is_array($id)) {
				$set_user_ids_info[$id[0]] = array($id[0], $id[1]);
			} else {
				$set_user_ids_info[$id] = array($id, null);
			}
		}

		$set_user_ids = array_keys($set_user_ids_info);

		$participants = App::getOrm()->createQuery("
			SELECT p
			FROM DeskPRO:TicketParticipant p
			WHERE p.ticket = ?1
		")->setParameter(1, $this)->execute();

		foreach ($participants as $k => $part) {
			if ($part->person['is_agent']) continue;

			if (!isset($set_user_ids_info[$part->person['id']])) {
				//$this->participants->remove($k);
				App::getOrm()->remove($participants[$k]);
			} else {
				$got_user_ids[] = $part->person['id'];

				$info = $set_user_ids_info[$part->person['id']];
				if ($info[1] AND $info[1] != $part->person_email['id']) {
					$part->setPersonEmailId($info[1]);
				}
			}
		}

		$new_user_ids = array_diff($set_user_ids, $got_user_ids);

		if ($new_user_ids) {
			foreach ($new_user_ids as $person_id) {
				$part = new \Application\DeskPRO\Entity\TicketParticipant();
				$part['person_id'] = $person_id;

				$info = $set_user_ids_info[$part->person['id']];
				if ($info[1]) {
					$part->setPersonEmailId($info[1]);
				}

				$this->addParticipant($part);
			}
		}
	}

	// The Other Guys | #201402112258 @Layne Modify method definition adding billing_rate and billing_dept
	public function addCharge(Person $agent, $time, $amount = null, $comment = '')
	{
		if ($time !== null) {
			$time = intval($time);
			if ($time == 0) {
				$time = null;
			}
		}
		if ($amount !== null) {
			$amount = floatval($amount);
			if ($amount == 0) {
				$amount = null;
			}
		}

		if ($time === null && $amount === null) {
			return false;
		}

		$charge = new TicketCharge();
		//$charge->charge_time = $time; 	// The Other Guys -- original code
		//$charge->amount = $amount; 		// The Other Guys -- original code
		$charge->comment = strval($comment);
		$charge->ticket = $this;
		$charge->person = $this->person;
		$charge->organization = $this->organization;
		$charge->agent = $agent;
		// The Other Guys | #201402112258 @Layne add assignments for billing_rate and billing_dept
		$charge->billing_rate = $agent->getRate();
		$charge->billing_dept = $agent->getDepartmentId();
		if ($time !== null) {
			$charge->amount = floatval( ($charge->billing_rate * ($time/3600)) );
			$charge->charge_time = $time;
		} else {
			$charge->charge_time = ($amount/$charge->billing_rate) * 3600;
			$charge->amount = $amount;
		}
		// end #201402112258
		
		
		$this->charges->add($charge);

		return $charge;
	}

	public function addSla(Sla $sla)
	{
		foreach ($this->ticket_slas AS $ticket_sla) {
			if ($ticket_sla->sla->id == $sla->id) {
				return $ticket_sla;
			}
		}

		$ticket_sla = new TicketSla();
		$ticket_sla->ticket = $this;
		$ticket_sla->sla = $sla;

		$this->ticket_slas->add($ticket_sla);

		return $ticket_sla;
		
	}

	public function removeSla(Sla $sla)
	{
		foreach ($this->ticket_slas AS $k => $ticket_sla) {
			if ($ticket_sla->sla->id == $sla->id) {
				$this->ticket_slas->remove($k);
				$this->updateWorstSlaStatus();
				return true;
			}
		}

		return false;
	}

	public function removeAllSlas()
	{
		foreach ($this->ticket_slas AS $k => $ticket_sla) {
			$this->ticket_slas->remove($k);
		}

		$this->setModelField('worst_sla_status', null);
	}

	public function hasSla(Sla $sla)
	{
		foreach ($this->ticket_slas AS $ticket_sla) {
			if ($ticket_sla->sla->id == $sla->id) {
				return $ticket_sla;
			}
		}

		return false;
	}

	public function getSlaById($sla_id)
	{
		foreach ($this->ticket_slas AS $ticket_sla) {
			if ($ticket_sla->sla->id == $sla_id) {
				return $ticket_sla;
			}
		}

		return null;
	}

	public function getSlaIds()
	{
		$ids = array();
		foreach ($this->ticket_slas AS $ticket_sla) {
			$ids[] = $ticket_sla->sla->id;
		}

		return $ids;
	}


	/**
	 * Add a message to this ticket.
	 *
	 * @param TicketMessage $message
	 */
	public function addMessage(TicketMessage $message)
	{
		$this->messages->add($message);
		$message->ticket = $this;

		$now = new \DateTime();
		if ($message->person['is_agent'] && !(defined('DP_INTERFACE') && DP_INTERFACE == 'user')) {
			if (!$message->is_agent_note) {
				if (!$this->date_last_agent_reply || $this->date_last_agent_reply < $now) {
					$this['date_last_agent_reply'] = $now;
				}

				if (!$this->date_first_agent_reply) {
					$this['date_first_agent_reply'] = $now;
					$this->_recalculate_slas = true; // may have a "first reply" sla

					$this['total_to_first_reply'] = $this->date_first_agent_reply->getTimestamp() - $this->date_created->getTimestamp();
				}
			}
		} else {
			if (!$this->date_last_user_reply || $this->date_last_user_reply < $now) {
				$this['date_last_user_reply'] = $now;
			}
		}

		if (count($message->attachments) && $this->getTicketLogger()) {
			foreach ($message->attachments as $attach) {
				$this->getTicketLogger()->recordMultiPropertyChanged('attachments', null, $attach);
			}
		}

		$this->_onPropertyChanged('messages', null, $message);

		if ($this->getTicketLogger() && DP_INTERFACE == 'user') {
			$this->getTicketLogger()->recordExtra('is_user_reply', true);
		}
	}


	/**
	 * Add a ticket attachment
	 *
	 * @param TicketAttachment $attach
	 * @return void
	 */
	public function addAttachment(TicketAttachment $attach)
	{
		$attach->ticket = $this;
		$this->attachments->add($attach);

		if ($this->getTicketLogger()) {
			$this->getTicketLogger()->recordMultiPropertyChanged('attachments', null, $attach);
		}
	}


	/**
	 * Find an existing data record for a field id.
	 *
	 * @param int $field_id
	 * @return CustomDataTicket
	 */
	public function getCustomDataForField($field_id)
	{
		if ($field_id instanceof CustomDefTicket) {
			$field_id = $field_id['id'];
		}

		foreach ($this->custom_data as $data) {
			if ($data['field_id'] == $field_id) {
				return $data;
			}
		}

		return null;
	}


	/**
	 * Gets a display array for a specific field
	 * @param $field_id
	 * @return array|mixed|null
	 */
	public function getCustomFieldDisplayArray($field_id)
	{
		$data = $this->getCustomDataForField($field_id);
		if (!$data) {
			return null;
		}

		$ticket_field_defs = App::getApi('custom_fields.tickets')->getEnabledFields();
		$ticket_data_structured = App::getApi('custom_fields.util')->createDataHierarchy(array($data), $ticket_field_defs);

		$custom_fields = App::getApi('custom_fields.tickets')->getFieldsDisplayArray(
			$ticket_field_defs,
			$ticket_data_structured
		);

		$custom_fields = array_pop($custom_fields);

		return $custom_fields;
	}



	/**
	 * Set custom field data for a particular field.
	 *
	 * @param int $field_id
	 * @param mixed $value
	 * @return mixed
	 */
	public function setCustomData($field_id, $value_type, $value)
	{
		$custom_data = $this->getCustomDataForField($field_id);
		$is_new = false;

		if (!$custom_data) {
			if ($value === null) return null;

			$is_new = true;

			$field = App::getEntityRepository('DeskPRO:CustomDefTicket')->find($field_id);
			if (!$field) {
				throw new \Exception("Invalid field_id `$field_id`");
			}
			$custom_data = new CustomDataTicket();
			$custom_data['field'] = $field;
		}

		$field = $custom_data->field;
		if ($field->parent) {
			foreach ($this->custom_data as $d) {
				if ($d->field && $d->field->parent && $d->field->parent['id'] == $field->parent['id']) {
					$this->custom_data->removeElement($d);
				}
			}
		}

		$this->custom_data->removeElement($custom_data);

		if ($value === null) {
			$this->custom_data->removeElement($custom_data);
			return null;
		}

		if ($field->getTypeName() == 'choice') {

		}

		$custom_data[$value_type] = $value;

		if ($is_new) {
			$this->addCustomData($custom_data);
		}

		if ($this->id) {
			App::getEntityRepository('DeskPRO:Cache')->delete("ticket_custom_fields.{$this->id}");
		}

		return $custom_data;
	}

	public function removeCustomDataForField($field)
	{
		$parent_id = null;
		$field_id = $field['id'];
		if ($field->parent) {
			$parent_id = $field->parent['id'];
		}

		foreach ($this->custom_data as $data) {
			if ($data['field_id'] == $field_id OR $data['field_id'] == $parent_id) {
				$this->custom_data->removeElement($data);
			}
		}
	}

	/**
	 * Add a custom data item to this ticket
	 *
	 * @param CustomDataTicket $data
	 */
	public function addCustomData(CustomDataTicket $data)
	{
		$this->custom_data->add($data);
		$data['ticket'] = $this;
	}


	/**
	 * Check if this ticket has a custom field.
	 *
	 * @param $field_id
	 * @return bool
	 */
	public function hasCustomField($field_id)
	{
		foreach ($this->custom_data as $data) {
			if ($data->field['id'] == $field_id) {
				return true;
			}
		}

		foreach ($this->custom_data as $data) {
			if ($data->field->parent AND $data->field->parent['id'] == $field_id) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Render a custom field
	 *
	 * !depreciated
	 */
	public function renderCustomField($field_id, $context = 'html')
	{
		$f_def = App::getEntityRepository('DeskPRO:CustomDefTicket')->find($field_id);

		$data_structured = App::getApi('custom_fields.util')->createDataHierarchy($this->custom_data, array($f_def));

		$value = !empty($data_structured[$f_def['id']]) ? $data_structured[$f_def['id']] : null;
		$rendered = $value ? $f_def->getHandler()->renderContext($context, $value) : null;

		return $rendered;
	}


	/**
	 * Add a label
	 * @param \Application\DeskPRO\Entity\LabelTicket $label
	 */
	public function addLabel(LabelTicket $label)
	{
		$label['ticket'] = $this;
		$this->labels->add($label);
	}

	public function getPersonId()
	{
		return $this->person['id'];
	}

	public function setPersonId($id)
	{
		$person = App::getOrm()->getRepository('DeskPRO:Person')->find($id);
		$this['person'] = $person;
	}

	public function setPerson(Person $person)
	{
		$this->setModelField('person', $person);

		if ($person->getRealLanguage()) {
			$this['language'] = $person->getRealLanguage();
		}

		if ($person->organization) {
			$this['organization'] = $person->organization;
		}

		if ($this->person_email && $this->person_email->person->getId() != $person->getId()) {
			$this['person_email'] = null;
		}
	}

	public function getPersonEmail()
	{
		if ($this->person_email) {
			return $this->person_email;
		} else {
			return $this->person['primary_email'];
		}
	}

	public function getPersonEmailAddress()
	{
		$email = $this->getPersonEmail();
		return $email['email'];
	}

	public function getDepartmentId()
	{
		if (!$this->department) {
			return 0;
		}

		return $this->department['id'];
	}

	public function setDepartmentId($id)
	{
		if ($id) {
			$dep = App::getOrm()->getRepository('DeskPRO:Department')->find($id);
			$this['department'] = $dep;
		} else {
			$this['department'] = null;
		}
	}

	public function setDepartment(Department $dep = null)
	{
		// Dep check
		if ($dep) {
			// For backwards compat, with TicketChangeTracker::getOriginalTicket,
			// dont apply for that
			if (!$this->_isNoPersist()) {
				if (!$dep->is_tickets_enabled) {
					$e = new \InvalidArgumentException("Department is not a ticket department");
					KernelErrorHandler::logException($e, true, 'ticket_dep_err1');
					return;
				}

				if (count($dep->children)) {
					$e = new \InvalidArgumentException("Department is a parent");
					KernelErrorHandler::logException($e, true, 'ticket_dep_err2');
					return;
				}
			}
		}

		$old_dep = $this->department;
		$this->department = $dep;
		$this->_onPropertyChanged('department', $old_dep, $dep);
	}

	public function isLangSet()
	{
		return $this->language ? true : false;
	}

	public function getRealLanguage()
	{
		return $this->language;
	}

	public function getLanguage()
	{
		if ($this->language) {
			return $this->language;
		} elseif ($this->person && $this->person->getRealLanguage()) {
			return $this->person->getRealLanguage();
		}

		return null;
	}

	public function getLanguageId()
	{
		$l = $this->getLanguage();
		return $l ? $l->getId() : 0;
	}

	public function setLanguageId($id)
	{
		if ($id) {
			$lang = App::getOrm()->getRepository('DeskPRO:Language')->find($id);
			$this['language'] = $lang;
		} else {
			$this['language'] = null;
		}
	}

	public function getCategoryId()
	{
		if (!$this->category) {
			return 0;
		}
		return $this->category['id'];
	}

	public function setCategoryId($id)
	{
		if ($id) {
			$cat = App::getOrm()->getRepository('DeskPRO:TicketCategory')->find($id);
			$this['category'] = $cat;
		} else {
			$this['category'] = null;
		}
	}

	public function getProductId()
	{
		if (!$this->product) {
			return 0;
		}
		return $this->product['id'];
	}

	public function setProductId($id)
	{
		if ($id) {
			$prod = App::getOrm()->getRepository('DeskPRO:Product')->find($id);
			$this['product'] = $prod;
		} else {
			$this['product'] = null;
		}
	}

	public function getPriorityId()
	{
		if (!$this->priority) {
			return 0;
		}

		return $this->priority['id'];
	}

	public function setPriorityId($id)
	{
		if ($id) {
			$pri = App::getOrm()->getRepository('DeskPRO:TicketPriority')->find($id);
			$this['priority'] = $pri;
		} else {
			$this['priority'] = null;
		}
	}

	public function getWorkflowId()
	{
		if (!$this->workflow) {
			return 0;
		}

		return $this->workflow['id'];
	}

	public function setWorkflowId($id)
	{
		if ($id) {
			$work = App::getOrm()->getRepository('DeskPRO:TicketWorkflow')->find($id);
			$this['workflow'] = $work;
		} else {
			$this['workflow'] = null;
		}
	}

	public function getAgentId()
	{
		if (!$this->agent) {
			return 0;
		}

		return $this->agent['id'];
	}

	public function setAgentId($id)
	{

		if ($id) {
			$agent = App::getOrm()->getRepository('DeskPRO:Person')->find($id);
			if (!$agent['is_agent']) {
				throw new \InvalidArgumentException("$id is not an agent");
			}

			$this['agent'] = $agent;
			// Do we need to update the first assign date?
			if (is_null($this->date_first_agent_assign)) {
				$this['date_first_agent_assign'] = new \DateTime();
			}

		} else {
			$this['agent'] = null;
		}
	}

	public function getAgentTeamId()
	{
		if (!$this->agent_team) {
			return 0;
		}
		return $this->agent_team['id'];
	}

	public function setAgentTeamId($id)
	{
		if ($id) {
			$agent_team = App::getOrm()->getRepository('DeskPRO:AgentTeam')->find($id);
			$this['agent_team'] = $agent_team;
		} else {
			$this['agent_team'] = null;
		}
	}

	public function getEmailGatewayId()
	{
		if (!$this->email_gateway) {
			return 0;
		}

		return $this->email_gateway['id'];
	}

	public function setEmailGatewayId($id)
	{
		if ($id) {
			$g = App::getOrm()->getRepository('DeskPRO:EmailGateway')->find($id);
			$this['email_gateway'] = $g;
		} else {
			$this['email_gateway'] = null;
		}
	}

	public function getIsAssigned()
	{
		if ($this->agent OR $this->agent_team) {
			return true;
		}

		return false;
	}


	public function getAssignedName()
	{
		if ($this->agent) {
			return $this->agent['display_name'];
		} elseif ($this->agent_team) {
			return $this->agent_team['name'];
		} else {
			return null;
		}
	}

	public function setLockedByAgentId($agent_id)
	{
		if ($agent_id) {
			$agent = App::getOrm()->getRepository('DeskPRO:Person')->find($agent_id);
			$this->setLockedByAgent($agent);
		} else {
			$this->setLockedByAgent(null);
		}
	}

	public function setLockedByAgent(Person $agent = null)
	{
		$this->setModelField('locked_by_agent', $agent);
		if ($agent) {
			$this->setModelField('date_locked', new \DateTime());
		} else {
			$this->setModelField('date_locked', null);
		}
	}

	public function unlockTicket()
	{
		$this->setLockedByAgentId(null);
	}

	public function getIsLocked()
	{
		return $this->isLocked();
	}

	public function hasLock()
	{
		return $this->locked_by_agent ? true : false;
	}

	public function isLocked(Person $current_agent = null)
	{
		if (!$this->locked_by_agent) {
			return false;
		}

		if ($current_agent === null) {
			$current_agent = App::getCurrentPerson();
		}
		if ($current_agent && $this->locked_by_agent['id'] == $current_agent['id']) {
			return false;
		}

		return true;
	}

	public function getIsArchived()
	{
		return $this->isArchived();
	}



	/**
	 * Gets messages we should be showing to the user. In other words,
	 * messages that are not private agent notes.
	 *
	 * @return array
	 */
	public function getDisplayableMessages()
	{
		$ret = array();

		foreach ($this->messages as $msg) {
			if (!$msg['is_agent_note']) {
				$ret[] = $msg;
			}
		}

		return $ret;
	}



	/**
	 * Set a flag color for this ticket for a particular perosn.
	 * $color of null or 'none' removes the flag.
	 *
	 * @param Person $person
	 * @param string $color
	 * @return void
	 */
	public function setFlagForPerson($person, $color = null)
	{
		if ($color == 'none') $color = null;

		if ($color) {
			App::getDb()->replace('tickets_flagged', array(
				'person_id' => $person['id'],
				'ticket_id' => $this->id,
				'color'     => $color
			));
		} else {
			App::getDb()->delete('tickets_flagged', array(
				'person_id' => $person['id'],
				'ticket_id' => $this->id,
			));
		}
	}


	/**
	 * Get the deletion record if there is one
	 *
	 * @return \Application\DeskPRO\Entity\TicketDeleted
	 */
	public function getDeletionRecord()
	{
		$del = App::getOrm()->createQuery("
			SELECT d
			FROM DeskPRO:TicketDeleted d
			WHERE d.ticket_id = ?1
		")->setParameter(1, $this->id)->getOneOrNullResult();

		return $del;
	}


	public function isHidden()
	{
		return $this->getIsHidden();
	}

	public function getIsHidden()
	{
		if ($this->status == self::STATUS_HIDDEN) {
			return true;
		}

		return false;
	}

	public function isDeleted()
	{
		return $this->getIsDeleted();
	}


	/**
	 * Is the ticket archived? Archived tickets are closed to replies.
	 *
	 * @return bool
	 */
	public function isArchived()
	{
		if ($this->status != 'closed') {
			return false;
		}

		return true;
	}


	/**
	 * Is this ticket deleted?
	 *
	 * @return bool
	 */
	public function getIsDeleted()
	{
		if ($this->hidden_status == self::HIDDEN_STATUS_DELETED) {
			return true;
		}

		return false;
	}


	public function getRealTotalUserWaiting()
	{
		$secs = $this->total_user_waiting;

		if ($this->date_user_waiting && $this->status == 'awaiting_agent') {
			$secs += time() - $this->date_user_waiting->getTimestamp();
		}

		return $secs;
	}

	public function getTotalUserWaitingWorkTime()
	{
		$work_hours_set = $this->getWorkHoursSet();

		$time = 0;
		if ($this->waiting_times) {
			foreach ($this->waiting_times AS $waiting) {
				if ($waiting['type'] == 'user') {
					$time += $work_hours_set->getWorkTimeBetween($waiting['start'], $waiting['end']);
				}
			}
		}

		if ($this->date_user_waiting && $this->status == 'awaiting_agent') {
			$time += $work_hours_set->getWorkTimeBetween($this->date_user_waiting);
		}

		return $time;
	}

	public function getCurrentUserWaitingTime()
	{
		if ($this->date_user_waiting && $this->status == 'awaiting_agent') {
			return time() - $this->date_user_waiting->getTimestamp();
		}

		return null;
	}

	public function getCurrentUserWaitingWorkTime()
	{
		if ($this->date_user_waiting && $this->status == 'awaiting_agent') {
			return $this->getWorkHoursSet()->getWorkTimeBetween($this->date_user_waiting);
		}

		return null;
	}

	public function getWorkTimeToFirstReply()
	{
		if ($this->date_first_agent_reply) {
			return $this->getWorkHoursSet()->getWorkTimeBetween($this->date_created, $this->date_first_agent_reply);
		}

		return null;
	}


	/**
	 * Get how long, in seconds, the ticket was open for. This only applies
	 * for tikcets that are resolved (or closed).
	 *
	 * @return int
	 */
	public function getTimeUntilResolution()
	{
		if (!$this->date_resolved && !$this->date_closed) {
			return null;
		}

		$date = $this->date_resolved;
		if (!$date || ($this->date_closed && $date > $this->date_closed)) {
			$date = $this->date_closed;
		}

		$secs = $date->getTimestamp() - $this->date_created->getTimestamp();

		return $secs;
	}

	public function getWorkTimeUntilResolution()
	{
		if (!$this->date_resolved && !$this->date_closed) {
			return null;
		}

		$date = $this->date_resolved;
		if (!$date || ($this->date_closed && $date > $this->date_closed)) {
			$date = $this->date_closed;
		}

		return $this->getWorkHoursSet()->getWorkTimeBetween($this->date_created, $date);
	}


	/**
	 * @return \DateTime
	 */
	public function getLastActivityDate()
	{
		$dates = array();
		if ($this->date_last_agent_reply) {
			$dates[] = $this->date_last_agent_reply;
		}
		if ($this->date_last_user_reply) {
			$dates[] = $this->date_last_user_reply;
		}

		if (!$dates) {
			return $this->date_created;
		}

		$use_date = $this->date_created;
		foreach ($dates as $d) {
			if ($d > $use_date) {
				$use_date = $d;
			}
		}

		return $use_date;
	}


	public function setStatus($status)
	{
		$this['date_status'] = new \DateTime();

		$old_status  = $this->status;

		if ($status != 'awaiting_agent' && $old_status == 'awaiting_agent' && $this->date_user_waiting) {
			$this->setModelField('total_user_waiting', $this->total_user_waiting + time() - $this->date_user_waiting->getTimestamp());
			$this->addWaitingTimeRecord('user', $this->date_user_waiting);
		}
		if ($status == 'awaiting_agent' && !$this->date_user_waiting) {
			$this->setModelField('date_user_waiting', new \DateTime());
		}
		if ($status != 'awaiting_agent' && $this->date_user_waiting) {
			$this->setModelField('date_user_waiting', null);
		}

		if ($status != 'awaiting_user' && $old_status == 'awaiting_user' && $this->date_agent_waiting) {
			$this->addWaitingTimeRecord('agent', $this->date_agent_waiting);
		}
		if ($status == 'awaiting_user' && !$this->date_agent_waiting) {
			$this->setModelField('date_agent_waiting', new \DateTime());
		}
		if ($status != 'awaiting_user' && $this->date_agent_waiting) {
			$this->setModelField('date_agent_waiting', null);
		}

		if ($status == 'closed' && !$this->date_closed) {
			$this['date_closed'] = new \DateTime();
		}
		if ($status != 'closed' && $this->date_closed) {
			$this->setModelField('date_closed', null);
		}

		if ($status == 'resolved' && !$this->date_resolved) {
			$this['date_resolved'] = new \DateTime();
		}
		if ($status != 'resolved' && $this->date_resolved) {
			$this->setModelField('date_resolved', null);
		}

		if ($status != 'awaiting_agent' && $this->is_hold) {
			$this['is_hold'] = false;
		}

		$old_hstatus = $this->hidden_status;
		$old_status_code = "$old_status.$old_hstatus";

		$status_code = $status;
		$hstatus = null;
		if (strpos($status, '.')) {
			list($status, $hstatus) = explode('.', $status, 2);
		}

		if (!$status || !in_array($status, array(
			self::STATUS_AWAITING_AGENT,
			self::STATUS_AWAITING_USER,
			self::STATUS_CLOSED,
			self::STATUS_RESOLVED,
			self::STATUS_HIDDEN
		))) {
			throw new \InvalidArgumentException("Invalid status `$status`");
		}

		if ($hstatus && !in_array($hstatus, array(
			self::HIDDEN_STATUS_DELETED,
			self::HIDDEN_STATUS_SPAM,
			self::HIDDEN_STATUS_VALIDATING,
			self::HIDDEN_STATUS_TEMP
		))) {
			throw new \InvalidArgumentException("Invalid hidden status `$hstatus`");
		}

		if ($hstatus && $status != 'hidden') {
			throw new \InvalidArgumentException("Invalid status must be hidden to set a hidden status, got `$status` instead.");
		}

		$this->setModelField('status', $status);
		$this->setModelField('hidden_status', $hstatus);

		if ($old_status_code == 'hidden.deleted' && $status_code != 'hidden.deleted') {
			$this->undeleteTicket();
		}

		if ($this->is_hold && $status != self::STATUS_AWAITING_AGENT) {
			$this->setModelField('is_hold', false);
		}

		$this->_reset_slas = true;
		$this->_recalculate_slas = true;
	}

	public function setHiddenStatus($hstatus)
	{
		if (!$hstatus) {
			if ($this->status == 'hidden') {
				$this->setStatus('awaiting_agent');
			}
		} else {
			$this->setStatus('hidden.' . $hstatus);
		}
	}

	public function getStatusCode()
	{
		if ($this->status == 'hidden') {
			return 'hidden.' . ($this->hidden_status ?: 'validating');
		} else {
			return $this->status;
		}
	}

	public function setIsHold($is_hold)
	{
		if ($is_hold) {
			$this->setStatus(self::STATUS_AWAITING_AGENT);
			$this->setModelField('is_hold', true);
		} else {
			$this->setModelField('is_hold', false);
		}
	}

	public function recalculateSlaDates()
	{
		foreach ($this->ticket_slas AS $ticket_sla) {
			$ticket_sla->calculateSlaDates();
		}
	}

	public function resetSlaStatuses()
	{
		foreach ($this->ticket_slas AS $ticket_sla) {
			if (!$ticket_sla->is_completed_set) {
				$ticket_sla->is_completed = false;
			}
		}
	}


	/**
	 * Undelete a ticket.
	 *
	 * This will set the status to 'awaiting_agent' if it wasn't changed before.
	 */
	public function undeleteTicket()
	{
		if (!$this->id) {
			return;
		}

		$del = $this->getDeletionRecord();
		if (!$del) {
			return;
		}

		if ($this->status == 'hidden') {
			$this->setModelField('status', self::STATUS_AWAITING_AGENT);
		}

		App::getOrm()->remove($del);
		App::getOrm()->persist($this);
	}



	/**
	 * Soft-delete a ticket
	 *
	 * @param null $person
	 * @param string $reason
	 * @return void
	 */
	public function deleteTicket($person = null, $reason = '')
	{
		$del = $this->getDeletionRecord();
		if (!$del) {
			$del = new TicketDeleted();
		}

		$del['ticket_id']     = $this->id;
		$del['old_ptac']      = $this->auth;
		$del['by_person']     = $person;
		$del['new_ticket_id'] = 0;
		$del['reason']        = $reason;

		$this->setStatus('hidden.deleted');

		App::getOrm()->persist($del);
		App::getOrm()->flush($del);
		App::getOrm()->persist($this);
	}

	public function updateWorstSlaStatus()
	{
		$status = $this->getWorstSlaStatus();
		$this->setModelField('worst_sla_status', $status);
		return $status;
	}

	public function getWorstSlaStatus()
	{
		if (!count($this->ticket_slas)) {
			return null;
		}

		$status = null;
		foreach ($this->ticket_slas AS $ticket_sla) {
			if ($ticket_sla->is_completed) {
				continue;
			}

			if (!$status) {
				$status = $ticket_sla->sla_status;
			} else if ($ticket_sla->sla_status == 'fail') {
				$status = 'fail';
			} else if ($ticket_sla->sla_status == 'warning' && $status !== 'fail') {
				$status = 'warning';
			}
		}

		return $status;
	}

	public function addWaitingTimeRecord($type, $start_ts, $end_ts = null)
	{
		$start_ts = ($start_ts instanceof \DateTime ? $start_ts->getTimestamp() : intval($start_ts));
		$end_ts = ($end_ts instanceof \DateTime ? $end_ts->getTimestamp() : intval($end_ts));

		if (!$end_ts) {
			$end_ts = time();
		}

		if ($end_ts <= $start_ts) {
			return;
		}

		if (!is_array($this->waiting_times)) {
			$this->waiting_times = array();
		}

		$old = $this->waiting_times;
		$this->waiting_times[] = array(
			'type' => $type,
			'start' => $start_ts,
			'end' => $end_ts,
			'length' => ($end_ts - $start_ts)
		);
		$this->_onPropertyChanged('waiting_times', $old, $this->waiting_times);
	}

	public function getWaitingTimes()
	{
		if (!is_array($this->waiting_times)) {
			return array();
		} else {
			return $this->waiting_times;
		}
	}



	/**
	 * Add an access code for a person
	 *
	 * @param PersonEmail $email
	 */
	public function addAccessCodeForPerson(Person $person)
	{
		if ($tac = $this->findAccessCodeForPerson($person)) {
			return $tac;
		}

		$tac = new TicketAccessCode();
		$tac['ticket'] = $this;
		$tac['person'] = $person;
		$this->access_codes->add($tac);
	}



	/**
	 * Find the access code for a person if it exists
	 *
	 * @return TicketAccessCode
	 */
	public function findAccessCodeForPerson(Person $person)
	{
		foreach ($this->access_codes as $tac) {
			if ($tac->person = $person) {
				return $tac;
			}
		}

		return null;
	}



	/**
	 * Find an access code
	 *
	 * @return TicketAccessCode
	 */
	public function findAccessCode($auth)
	{
		foreach ($this->access_codes as $tac) {
			if ($tac['auth'] == $auth) {
				return $tac;
			}
		}

		return null;
	}


	/**
	 * Goes through everyone associated with this ticket (user owner, agent, participants)
	 * and fetches their preferred email address.
	 *
	 * Returns null if the person isn't on the ticket or if they don't have any email
	 * addresses.
	 *
	 * @param Person $person
	 * @return PersonEmail|null
	 */
	public function findEmailForPerson(Person $person)
	{
		if ($this->person == $person) {
			if ($this->person_email) {
				return $this->person_email;
			} else {
				return $this->person->primary_email;
			}
		} else if ($this->agent == $person) {
			return $this->agent->primary_email;
		} else {
			foreach ($this->participants as $part) {
				if ($part->person == $person) {
					if ($part->person_email) {
						return $part->person_email;
					} else {
						return $part->person->primary_email;
					}
				}
			}
		}

		return null;
	}


	/**
	 * Gets the access code which is an encoded ticket ID and authcode into one string.
	 *
	 * @return string
	 */
	public function getAccessCode()
	{
		$str = Util::baseEncode($this->id, 'letters');
		$str .= $this->auth;

		return $str;
	}


	/**
	 * Get the Message-ID field for an email regarding this ticket, witht he
	 * embedded PTAC code.
	 *
	 * @return string
	 */
	public function getUniqueEmailMessageId()
	{
		$uid = 'PTAC-' . $this->getAccessCode() . '.';
		$uid .= uniqid('', true) . '-' . App::getSetting('core.site_id');
		$uid .= '@' . md5(App::getSetting('core.site_url', 'deskpro'));

		return $uid;
	}


	/**
	 * @return string
	 */
	public function getEmailReferencesHeader()
	{
		$uid = 'TICKET-' . $this->getAccessCode() . '.';
		$uid .= App::getSetting('core.site_id');
		$uid .= '@' . md5(App::getSetting('core.site_url', 'deskpro'));

		return $uid;
	}


	/**
	 * Get the ID used in the interface for links etc.
	 *
	 * @return int
	 */
	public function getPublicId()
	{
		if (App::getSetting('core.tickets.use_ref')) {
			return $this->ref;
		}

		return $this->id;
	}


	/**
	 * Did this ticket originate from a gateway?
	 *
	 * @return bool
	 */
	public function isFromGateway()
	{
		if (strpos($this->creation_system, 'gateway') === 0) {
			return true;
		}

		return false;
	}


	/**
	 * Decodes an access code into a ticket id and the standalone auth.
	 *
	 * @param  $access_code
	 * @return array
	 */
	public static function decodeAccessCode($access_code)
	{
		$len = App::getSetting('core_tickets.ptac_auth_code_len');
		if (strlen($access_code) < ($len+1)) return false;

		$matches = Strings::extractRegexMatch('#^(.+)(.{'.$len.'})$#', $access_code, -1);
		if (!$matches) return false;

		list (, $ticket_id, $auth) = $matches;

		$ticket_id = Util::baseDecode($ticket_id, 'letters');

		return array(
			'ticket_id' => $ticket_id,
			'auth'      => $auth
		);
	}

	public function __clone()
	{
		parent::__clone();
		$this->_ticket_logger = null;
		$this->participants = new \Doctrine\Common\Collections\ArrayCollection();
	}


	public function getTicketHash()
	{
		if (!$this->ticket_hash) {
			$this->initHashCode();
		}

		return $this->ticket_hash;
	}

	/**
	 * Resets the ticket hash
	 */
	public function recomputeHash()
	{
		$hashes = array();
		$hashes[] = sha1(
			$this->subject
			. ($this->person ? $this->person->id : '')
			. $this->getAgentId()
			. $this->getAgentTeamId()
			. $this->getDepartmentId()
			. $this->getCategoryId()
			. $this->getWorkflowId()
			. $this->getPriorityId()
			. $this->getProductId()
		);

		foreach ($this->custom_data as $d) {
			$hashes[] = sha1($d['field_id'] . $d['value'] . $d['input']);
		}

		if ($this->messages->containsKey(0)) {
			$hashes[] = $this->messages->get(0)->getMessageHash();
		}

		sort($hashes, \SORT_STRING);

		$this->ticket_hash = sha1(implode('', $hashes));
		$this->_onPropertyChanged('ticket_hash', '', $this->ticket_hash);
	}

	/**
	 */
	public function initHashCode()
	{
		if ($this->ticket_hash) {
			return;
		}

		$this->recomputeHash();
	}


	/**
	 */
	public function _preInsert()
	{
		// Get the new ref
		if (!$this->ref) {
			try {
				$this['ref'] = App::getRefGenerator()->generateReference('DeskPRO:Ticket');
			} catch (\Exception $e) {
				KernelErrorHandler::logException($e);

				// Using a custom format.
				// We just ran into a collision which means the pattern is not a good pattern.
				// We are going to append a random number automatically if it isn't part of the pattern already
				if (App::getSetting('core.ref_pattern') && strpos(App::getSetting('core.ref_pattern'), '<?>') === -1 && strpos(App::getSetting('core.ref_pattern'), '<A>') === -1) {
					$set_pattern = App::getSetting('core.ref_pattern');
					$set_pattern .= '-<A><A><A>';
					App::getContainer()->getSettingsHandler()->setSetting('core.ref_pattern', $set_pattern);
				}

				// Log and fallback to a random ref
				$ref = Strings::random(4, Strings::CHARS_ALPHA_IU) . '-' . Strings::random(4, Strings::CHARS_NUM) . '-' . Strings::random(4, Strings::CHARS_ALPHA_IU) . '-' . date('ymd');
				$this['ref'] = $ref;
			}
		}

		if (!$this->_no_log && $this->_ticket_logger) {
			$this->getTicketLogger()->recordExtra('created', true);
		}

		if ($this->organization) {
			$managers = App::getEntityRepository('DeskPRO:Organization')->getManagers($this->organization);
			foreach ($managers AS $manager) {
				if ($manager->getPref('org.manager_auto_add')) {
					$this->addParticipantPerson($manager);
				}
			}
		}

		$this->_applySlas();
	}

	public function _preUpdate()
	{
		$self = $this;
		$reset = $this->_reset_slas;

		if ($this->_recalculate_slas) {
			$orm = App::getOrm();

			if (method_exists($orm, 'delayedUpdate')) {
				$orm->delayedUpdate(function($em) use ($self, $reset) {
					// this is deferred until all changes are done to ensure everything is correct
					if ($reset) {
						$self->resetSlaStatuses();
					}
					$self->recalculateSlaDates();
				});
			}
		}

		$this->_reset_slas = false;
		$this->_recalculate_slas = false;
	}

	public function _applySlas()
	{
		if ($this->status == 'hidden') {
			return;
		}

		$slas = App::getEntityRepository('DeskPRO:Sla')->getAllSlas();
		foreach ($slas AS $sla) {
			if ($sla->apply_type == 'all') {
				$this->addSla($sla);
				continue;
			}

			if ($sla->apply_type == 'priority' && $sla->apply_priority && $this->priority && $sla->apply_priority->id == $this->priority->id) {
				$this->addSla($sla);
				continue;
			}

			if ($sla->apply_type == 'people_orgs' && $sla->appliesToPerson($this->person)) {
				$this->addSla($sla);
				continue;
			}

			if ($sla->apply_type == 'people_orgs' && $this->organization && $sla->appliesToOrganization($this->organization)) {
				$this->addSla($sla);
				continue;
			}

			// don't need to do the apply trigger here - it will be handled elsewhere
		}
	}

	/**
	 */
	public function _presaveTicketLogs()
	{
		if (!$this->_no_log && $this->_ticket_logger) {
			$this->_ticket_logger->preDone();
		}
	}

	public function resetTicketLogger()
	{
		$this->_initTicketLogger();
		$this->_presave_state = array();
	}

	public function unsetTicketLogger()
	{
		$this->_ticket_logger = null;
	}


	public function _autoSaveTicketLogs()
	{
		if ($this->_auto_commit_changelog) {
			$this->_saveTicketLogs();
		}
	}

	public function _saveTicketLogs()
	{
		if (!$this->_no_log && $this->_ticket_logger) {
			$this->_ticket_logger->done();
			$this->resetTicketLogger();
		}
	}

	/**
	 * @return \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	public function getTicketLogger()
	{
		return $this->_ticket_logger;
	}


	/**
	 * @return \Application\DeskPRO\Labels\LabelManager
	 */
	public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:LabelTicket');
		}

		return $this->_label_manager;
	}

	public function copy()
	{
		$alt_ticket = new Ticket();

		$load = array(
			'agent',
			'agent_team',
			'person',
			'person_email',
			'department',
			'category',
			'product',
			'workflow',
			'language',
			'organization',
			'status',
			'hidden_status',
			'subject',
			'is_hold',
			'urgency',
		);

		foreach ($load as $k) {
			$alt_ticket[$k] = $this[$k];
		}

		// Custom field data
		foreach ($this->custom_data as $custom_data) {
			$new_custom_data = clone $custom_data;
			$new_custom_data->ticket = $alt_ticket;

			$alt_ticket->addCustomData($new_custom_data);
		}

		return $alt_ticket;
	}


	public static function getStatusInt($status_code)
	{
		$status = $status_code;
		$hstatus = null;
		if (strpos($status, '.')) {
			list($status, $hstatus) = explode('.', $status, 2);
		}

		switch ($status) {
			case self::STATUS_AWAITING_AGENT:
				return 100;
			case self::STATUS_AWAITING_USER:
				return 110;
			case self::STATUS_RESOLVED:
				return 200;
			case self::STATUS_CLOSED:
				return 210;
			case self::STATUS_HIDDEN:
				switch ($hstatus) {
					case self::HIDDEN_STATUS_VALIDATING:
						return 300;
					case self::HIDDEN_STATUS_DELETED:
						return 310;
					case self::HIDDEN_STATUS_SPAM:
						return 320;
				}
				break;
		}

		return 0;
	}


	public function _markRemoved()
	{
		$this->_isRemoved = $this->getId();
	}



	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		if ($deep) {
			$data['labels'] = array();
			foreach ($this->labels AS $label) {
				$data['labels'][] = $label['label'];
			}
		}

		$data['total_user_waiting_real'] = $this->getRealTotalUserWaiting();
		$data['total_user_waiting_work'] = $this->getTotalUserWaitingWorkTime();
		$data['current_user_waiting'] = $this->getCurrentUserWaitingTime();
		$data['current_user_waiting_work'] = $this->getCurrentUserWaitingWorkTime();
		$data['total_to_first_reply_work'] = $this->getWorkTimeToFirstReply();
		$data['total_to_resolution'] = $this->getTimeUntilResolution();
		$data['total_to_resolution_work'] = $this->getWorkTimeUntilResolution();

		if ($this->agent) {
			$data['agent']['display_name'] = $this->agent->getDisplayNameUser();
			$data['agent']['display_name_real'] = $this->agent->getDisplayName();
		}

		$data['access_code'] = $this->getAccessCode();
		$data['access_code_email_body_token'] = '(#' . $this->getAccessCode() . ')';
		$data['access_code_email_header_token'] = 'PTAC-' . $this->getAccessCode();

		// Render custom fields to text values
		$field_manager = App::getContainer()->getSystemService('ticket_fields_manager');
		$field_manager->addApiData($this, $data);

		return $data;
	}


	/**
	 * @param string $string
	 * @param Person|null $performer
	 * @param bool $escape
	 * @param bool $to_user
	 * @return mixed
	 */
	public function replaceVarsInString($string, Person $performer = null, $escape = false, $to_user = true)
	{
		$repl = array();

		$display_name = $to_user ? $this->person->getDisplayNameUser() : $this->person->getDisplayName();

		$repl = array_merge(array(
			'user.name'                   => $display_name,
			'user.email'                  => $this->person->getPrimaryEmailAddress(),
			'user.organization_position'  => $this->person->organization_position,

			'org.name' => $this->person->organization ? $this->person->organization->name : '',
		), $repl);

		// Custom user fields: {{ user.field23 }}
		$field_manager = App::getSystemService('person_fields_manager');
		$custom_fields = $field_manager->getRenderedToTextForObject($this->person);
		foreach ($custom_fields as $f) {
			$repl["user.field{$f['id']}"] = $f['rendered'];
		}

		// Custom org fields: {{ agent.field23 }}
		if ($this->person->organization) {
			$field_manager = App::getSystemService('org_fields_manager');
			$custom_fields = $field_manager->getRenderedToTextForObject($this->person->organization);
			foreach ($custom_fields as $f) {
				$repl["org.field{$f['id']}"] = $f['rendered'];
			}
		}

		if (!$performer && App::getCurrentPerson() && App::getCurrentPerson()->getId()) {
			$performer = App::getCurrentPerson();
		}

		if ($performer) {
			$display_name = $to_user ? $performer->getDisplayNameUser() : $performer->getDisplayName();

			$repl = array_merge(array(
				'performer.name'                   => $display_name,
				'performer.email'                  => $performer->getPrimaryEmailAddress(),
				'performer.organization_position'  => $performer->organization_position,

				'performer.org.name' => $performer->organization ? $performer->organization->name : '',
			), $repl);

			$field_manager = App::getSystemService('person_fields_manager');
			$custom_fields = $field_manager->getRenderedToTextForObject($performer);
			foreach ($custom_fields as $f) {
				$repl["performer.field{$f['id']}"] = $f['rendered'];
			}

			if ($performer->organization) {
				$field_manager = App::getSystemService('org_fields_manager');
				$custom_fields = $field_manager->getRenderedToTextForObject($performer->organization);
				foreach ($custom_fields as $f) {
					$repl["performer.org.field{$f['id']}"] = $f['rendered'];
				}
			}
		} else {
			$repl = array_merge(array(
				'performer.name'                   => '',
				'performer.email'                  => '',
				'performer.organization_position'  => '',
				'performer.org.name' => '',
			), $repl);
		}

		if ($this->agent) {
			$agent_display_name = $to_user ? $this->agent->getDisplayNameUser() : $this->agent->getDisplayName();
		} else {
			$agent_display_name = '';
		}

		$repl = array_merge(array(
			'ticket.id'               => $this->id,
			'ticket.ref'              => $this->ref,
			'ticket.subject'          => $this->subject,
			'ticket.department'       => $this->department ? $this->department->full_title : '',
			'ticket.product'          => $this->product ? $this->product->full_title : '',
			'ticket.category'         => $this->category ? $this->category->full_title : '',
			'ticket.workflow'         => $this->workflow ? $this->workflow->title : '',
			'ticket.priority'         => $this->priority ? $this->priority->title : '',

			'agent.name'     => $agent_display_name,
			'agent.email'    => $this->agent ? $this->agent->getPrimaryEmailAddress() : '',

			'agent_team.name' => $this->agent_team ? $this->agent_team->name : '',
		), $repl);

		// Custom ticket fields: {{ ticket.field23 }}
		$field_manager = App::getSystemService('ticket_fields_manager');
		$custom_fields = $field_manager->getRenderedToTextForObject($this);
		foreach ($custom_fields as $f) {
			$repl["ticket.field{$f['id']}"] = $f['rendered'];
		}

		foreach ($repl as $k => $v) {
			if ($escape) {
				$v = htmlspecialchars($v);
			}
			$string = str_replace("{{ $k }}", $v, $string);
			$string = str_replace("{{{$k}}}", $v, $string);
		}

		return $string;
	}

	public function getPath()
	{
		return App::getRouter()->generate('user_tickets_view', array('ticket_ref' => $this->getAccessCode()));
	}

	public function getLink()
	{
		return App::getRouter()->generateUrl('user_tickets_view', array('ticket_ref' => $this->getAccessCode()));
	}

	public function isAgentCreated()
	{
		return strpos($this->creation_system, '.agent') !== false;
	}

	public function getWorkHoursSet()
	{
		if (!$this->_work_hours_set) {
			$work_hours = unserialize(App::getSetting('core_tickets.work_hours'));
			$this->_work_hours_set = new \Orb\Util\WorkHoursSet(
				$work_hours['active_time'], $work_hours['start_hour'] * 3600 + $work_hours['start_minute'] * 60,
				$work_hours['end_hour'] * 3600 + $work_hours['end_minute'] * 60,
				$work_hours['days'], $work_hours['timezone'], $work_hours['holidays']
			);
		}

		return $this->_work_hours_set;
	}


	/**
	 * Get property from properties array
	 *
	 * @param string $key
	 * @param mixed  $default
	 * @return mixed
	 */
	public function getProperty($key, $default = null)
	{
		return ($this->properties !== null && isset($this->properties[$key]) ? $this->properties[$key] : $default);
	}


	/**
	 * Set properties
	 *
	 * @param  $key
	 * @param  $value
	 * @return void
	 */
	public function setProperty($key, $value)
	{
		$old = $this->properties;

		if ($value === null) {
			if ($this->properties) {
				unset($this->properties[$key]);
			}
			if (!$this->properties) {
				$this->properties = null;
			}
		} else {
			if ($this->properties === null) {
				$this->properties = array();
			}
			$this->properties[$key] = $value;
		}

		$this->_onPropertyChanged('properties', $old, $this->properties);
	}

	public function recountStats()
	{
		$agent_ids_in = implode(',', App::getDataService('Agent')->getIds());

		$this['count_agent_replies'] = App::getDb()->fetchColumn("
			SELECT COUNT(*) FROM tickets_messages
			WHERE ticket_id = ? AND is_agent_note = 0 AND person_id IN ($agent_ids_in)
		", array($this->id));

		$this['count_user_replies'] = App::getDb()->fetchColumn("
			SELECT COUNT(*) FROM tickets_messages
			WHERE ticket_id = ? AND person_id NOT IN ($agent_ids_in)
		", array($this->id));
	}

	public function getFromAddress($context = 'user', array $options = null)
	{
		if ($context == 'user' && $this->notify_email) {
			$from_email = $this->notify_email;
		} elseif ($context == 'agent' && $this->notify_email_agent) {
			$from_email = $this->notify_email_agent;
		} elseif ($this->email_gateway && $this->email_gateway->getPrimaryEmailAddress() && $this->email_gateway->is_enabled) {
			$from_email = $this->email_gateway->getPrimaryEmailAddress();
		} else {
			$from_email = App::getSetting('core.default_from_email');
			$default_address = App::getDb()->fetchColumn("
				SELECT match_pattern
				FROM email_gateway_addresses
				WHERE match_type = 'exact'
				ORDER BY run_order ASC, id ASC
				LIMIT 1
			");

			if ($default_address) {
				$from_email = $default_address;
			}
		}

		if ($context == 'user' && $this->notify_email_name) {
			$from_name = $this->notify_email_name;
		} elseif ($context == 'agent' && $this->notify_email_name_agent) {
			$from_name = $this->notify_email_name_agent;
		} else {
			$from_name = App::getSetting('core.deskpro_name');

			if ($options && isset($options['default_from']) && $options['default_from']) {
				$from_name = $options['default_from'];
			}
		}

		return array(
			'email' => $from_email,
			'name'  => $from_name
		);
	}

	public function disableChangetrackerAutocommit()
	{
		$this->_auto_commit_changelog = false;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Ticket';
		$metadata->setPrimaryTable(array(
			'name' => 'tickets',
			'indexes' => array(
				'date_created_idx' => array('columns' => array('date_created')),
				'date_locked_idx' => array('columns' => array('date_locked')),
				'status_idx' => array('columns' => array('status')),
			),
			'uniqueConstraints' => array(
				'ref_idx' => array('columns' => array('ref'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->addLifecycleCallback('_initTicketLogger', 'postLoad');
		$metadata->addLifecycleCallback('initHashCode', 'prePersist');
		$metadata->addLifecycleCallback('_preInsert', 'prePersist');
		$metadata->addLifecycleCallback('_preUpdate', 'preUpdate');
		$metadata->addLifecycleCallback('_presaveTicketLogs', 'prePersist');
		$metadata->addLifecycleCallback('_presaveTicketLogs', 'preUpdate');
		$metadata->addLifecycleCallback('_autoSaveTicketLogs', 'postPersist');
		$metadata->addLifecycleCallback('_autoSaveTicketLogs', 'postUpdate');
		$metadata->addLifecycleCallback('_markRemoved', 'preRemove');
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'ref', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ref', ));
		$metadata->mapField(array( 'fieldName' => 'auth', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'auth', ));
		$metadata->mapField(array( 'fieldName' => 'sent_to_address', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'sent_to_address', ));
		$metadata->mapField(array( 'fieldName' => 'notify_email', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'notify_email', ));
		$metadata->mapField(array( 'fieldName' => 'notify_email_name', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'notify_email_name', ));
		$metadata->mapField(array( 'fieldName' => 'notify_email_agent', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'notify_email_agent', ));
		$metadata->mapField(array( 'fieldName' => 'notify_email_name_agent', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'notify_email_name_agent', ));
		$metadata->mapField(array( 'fieldName' => 'creation_system', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'creation_system', ));
		$metadata->mapField(array( 'fieldName' => 'creation_system_option', 'type' => 'string', 'length' => 1000, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'creation_system_option', ));
		$metadata->mapField(array( 'fieldName' => 'ticket_hash', 'type' => 'string', 'length' => 40, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ticket_hash', ));
		$metadata->mapField(array( 'fieldName' => 'status', 'type' => 'string', 'length' => 30, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'status', ));
		$metadata->mapField(array( 'fieldName' => 'hidden_status', 'type' => 'string', 'length' => 30, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'hidden_status', ));
		$metadata->mapField(array( 'fieldName' => 'validating', 'type' => 'string', 'length' => 35, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'validating', ));
		$metadata->mapField(array( 'fieldName' => 'is_hold', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_hold', ));
		$metadata->mapField(array( 'fieldName' => 'urgency', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'urgency', ));
		$metadata->mapField(array( 'fieldName' => 'count_agent_replies', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'count_agent_replies', ));
		$metadata->mapField(array( 'fieldName' => 'count_user_replies', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'count_user_replies', ));
		$metadata->mapField(array( 'fieldName' => 'feedback_rating', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'feedback_rating', ));
		$metadata->mapField(array( 'fieldName' => 'date_feedback_rating', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_feedback_rating', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_resolved', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_resolved', ));
		$metadata->mapField(array( 'fieldName' => 'date_closed', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_closed', ));
		$metadata->mapField(array( 'fieldName' => 'date_first_agent_assign', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_first_agent_assign', ));
		$metadata->mapField(array( 'fieldName' => 'date_first_agent_reply', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_first_agent_reply', ));
		$metadata->mapField(array( 'fieldName' => 'date_last_agent_reply', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_last_agent_reply', ));
		$metadata->mapField(array( 'fieldName' => 'date_last_user_reply', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_last_user_reply', ));
		$metadata->mapField(array( 'fieldName' => 'date_agent_waiting', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_agent_waiting', ));
		$metadata->mapField(array( 'fieldName' => 'date_user_waiting', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_user_waiting', ));
		$metadata->mapField(array( 'fieldName' => 'date_status', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_status', ));
		$metadata->mapField(array( 'fieldName' => 'total_user_waiting', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'total_user_waiting', ));
		$metadata->mapField(array( 'fieldName' => 'total_to_first_reply', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'total_to_first_reply', ));
		$metadata->mapField(array( 'fieldName' => 'date_locked', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_locked', ));
		$metadata->mapField(array( 'fieldName' => 'has_attachments', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'has_attachments', ));
		$metadata->mapField(array( 'fieldName' => 'subject', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'subject', ));
		$metadata->mapField(array( 'fieldName' => 'original_subject', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'original_subject', ));
		$metadata->mapField(array( 'fieldName' => 'properties', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'properties', ));
		$metadata->mapField(array( 'fieldName' => 'worst_sla_status', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'worst_sla_status', ));
		$metadata->mapField(array( 'fieldName' => 'waiting_times', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'waiting_times', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'parent_ticket', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Ticket', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'parent_ticket_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'language', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Language', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'language_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'department', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Department', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'department_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'category', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketCategory', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'category_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'priority', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketPriority', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'priority_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'workflow', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketWorkflow', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'workflow_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'product', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Product', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'product_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person_email', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonEmail', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_email_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person_email_validating', 'targetEntity' => 'Application\\DeskPRO\\Entity\\PersonEmailValidating', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_email_validating_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'agent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'agent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'agent_team', 'targetEntity' => 'Application\\DeskPRO\\Entity\\AgentTeam', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'agent_team_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'organization', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Organization', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'organization_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'linked_chat', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ChatConversation', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'linked_chat_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'attachments', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketAttachment', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'ticket',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'access_codes', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketAccessCode', 'cascade' => array('persist', 'merge'), 'mappedBy' => 'ticket', 'onDelete' => 'cascade' ));
		$metadata->mapOneToMany(array( 'fieldName' => 'messages', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketMessage', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'ticket',  'orderBy' => array( 'date_created' => 'ASC', ), ));
		$metadata->mapOneToMany(array( 'fieldName' => 'custom_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDataTicket', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'ticket', 'orphanRemoval' => true,  'dpApi' => true));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelTicket', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'ticket', 'orphanRemoval' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'email_gateway', 'targetEntity' => 'Application\\DeskPRO\\Entity\\EmailGateway', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'email_gateway_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'email_gateway_address', 'targetEntity' => 'Application\\DeskPRO\\Entity\\EmailGatewayAddress', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'email_gateway_address_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'locked_by_agent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'locked_by_agent', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'participants', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketParticipant', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'ticket', 'orphanRemoval' => true, 'dpApi' => true, 'dpApiDeep' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'charges', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketCharge', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'ticket', 'orphanRemoval' => true, 'dpApi' => true, 'dpApiDeep' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'ticket_slas', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TicketSla', 'cascade' => array( 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'ticket', 'orphanRemoval' => true, 'dpApi' => true, 'dpApiDeep' => true ));
		}
}
