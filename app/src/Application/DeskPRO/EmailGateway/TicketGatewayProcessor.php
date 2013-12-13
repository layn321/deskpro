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

use Application\DeskPRO\App;
use Application\DeskPRO\EmailGateway\TicketGateway\AgentReplyCodes;
use Application\DeskPRO\Entity;
use Application\DeskPRO\EmailGateway\AbstractGatewayProcessor;
use Application\DeskPRO\EmailGateway\Reader\AbstractReader;
use Application\DeskPRO\EmailGateway\Ticket\CodeTicketDetector;
use Application\DeskPRO\EmailGateway\Ticket\ToEmailTicketDetector;
use Application\DeskPRO\EmailGateway\Ticket\InReplyToDetector;
use Application\DeskPRO\EmailGateway\Ticket\SubjectMatchDetector;
use Application\DeskPRO\EmailGateway\Ticket\SubjectRefMatchDetector;
use Application\DeskPRO\EmailGateway\Ticket\Dp3Detector;
use Application\DeskPRO\EmailGateway\Cutter\CutterDefFactory;
use Application\DeskPRO\EmailGateway\Cutter\ForwardCutter;
use Orb\Validator\StringEmail;

class TicketGatewayProcessor extends AbstractGatewayProcessor
{
	const EVENT_EVENT                    = 'DeskPRO_onTicketGatewayInit';
	const EVENT_BEFORE_RUN_ACTION        = 'DeskPRO_onBeforeTicketGatewayRunAction';
	const EVENT_RUN_ACTION               = 'DeskPRO_onTicketGatewayRunAction';
	const EVENT_BEFORE_NEWREPLY          = 'DeskPRO_onBeforeTicketGatewayNewReply';
	const EVENT_NEWREPLY                 = 'DeskPRO_onTicketGatewayNewReply';
	const EVENT_BEFORE_NEWTICKET         = 'DeskPRO_onBeforeTicketGatewayNewTicket';
	const EVENT_NEWTICKET                = 'DeskPRO_onTicketGatewayNewTicket';
	const EVENT_BEFORE_FWD_NEWTICKET     = 'DeskPRO_onBeforeTicketGatewayNewFwdTicket';
	const EVENT_FWD_NEWTICKET            = 'DeskPRO_onTicketGatewayNewFwdTicket';

	/**
	 * @var \Application\DeskPRO\EmailGateway\Cutter\Def\Generic
	 */
	protected $cutterDef;

	/**
	 * True when there was an error converting an incoming charset to utf8.
	 * When this happens, the standard is to use the original string (unconverted)
	 * and save an original version of the message.
	 *
	 * @var bool
	 */
	protected $charset_error = false;

	/**
	 * The person replying or submitting the ticket.
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * If in reply mode, this is the ticket being replied to
	 *
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $ticket;

	/**
	 * If the Detector detected that an email should come from a specific person,
	 * then this is the person.
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $detected_tac_person;

	/**
	 * @var string
	 */
	protected $reply_actions;

	/**
	 * @var string
	 */
	protected $email_body_html;

	/**
	 * @var string
	 */
	protected $email_body_text;

	protected $error;
	protected $source_info;
	protected $is_dp3_reply = false;
	protected $is_bounce = false;
	protected $inline_blobs = array();
	protected $dupe_inline_blobs = array();

	protected function init()
	{
		$this->cutterDef = CutterDefFactory::getDef($this->reader);
	}

	public function run()
	{
		$person_processor = new PersonFromEmailProcessor();

		#-------------------------
		# Run detectors to see if its a reply
		#-------------------------

		$ticket = null;
		$person = null;

		$bounce_detector = new \Application\DeskPRO\EmailGateway\Ticket\BounceDetector($this->reader, App::getOrm());
		$bounce_detector->setLogger($this->logger);

		if ($bounce_detector->isBounced()) {
			$this->is_bounce = true;

			$this->logMessage("Is bounced");
			$ticket	= $bounce_detector->getGuessedTicket();
		}

		$detector = null;
		if (!$ticket) {
			if (!$ticket) {
				$detector = new CodeTicketDetector();
				if ($this->is_bounce) {
					$detector->enableBouncedMode();
				}
				if ($this->logger) $detector->setLogger($this->logger);
				$ticket = $detector->findExistingTicket($this->reader);

				$this->logMessage('[TicketGatewayProcessor] CodeTicketDetector detected: ' . ($ticket ? $ticket['id'] : 'nothing'));

				$this->detected_tac_person = $detector->getTacPerson();
				if ($this->detected_tac_person) {
					$this->logMessage(sprintf('[TicketGatewayProcessor] CodeTicketDetector used TAC belonging to %d %s', $this->detected_tac_person->getId(), $this->detected_tac_person->getDisplayContact()));
				}
			}

			// If we imported form DP3, run the old codes
			if (!$ticket && App::getSetting('core.deskpro3importer')) {
				$detector = new Dp3Detector();
				$ticket = $detector->findExistingTicket($this->reader);
				$this->logMessage('[TicketGatewayProcessor] Dp3Detector detected: ' . ($ticket ? $ticket['id'] : 'nothing'));

				if ($ticket) {
					$this->is_dp3_reply = true;
				}
			}

			if (!$ticket) {
				// Try ref match
				$detector = new SubjectRefMatchDetector();
				$ticket = $detector->findExistingTicket($this->reader);

				$this->logMessage('[TicketGatewayProcessor] SubjectRefMatchDetector detected: ' . ($ticket ? $ticket['id'] : 'nothing'));
			}

			if (!$ticket && App::getSetting('core_tickets.gateway_enable_subject_match')) {
				// Finally try subject string match
				$detector = new SubjectMatchDetector();
				if ($this->logger) $detector->setLogger($this->logger);

				if ($this->is_bounce) {
					$detector->enableBouncedMode();
				}

				$ticket = $detector->findExistingTicket($this->reader);

				$this->logMessage('[TicketGatewayProcessor] SubjectMatchDetector detected: ' . ($ticket ? $ticket['id'] : 'nothing'));
			}

			if ($ticket) {
				$person = $detector->findExistingPerson($ticket, $this->reader);
				$this->logMessage('[TicketGatewayProcessor] findExistingPerson detected: ' . ($person ? $person['id'] : 'nothing'));
			}
		}

		#-------------------------
		# If we have aticket and user, run reply,
		# otherwise just make a new ticket
		#-------------------------

		// The detectors above use TAC's, but they might
		// exist for people who are no longer on tickets
		// so we need to confirm that user parts are still
		// participants
		if ($ticket AND $person AND !$person['is_agent']) {
			if ($ticket->person['id'] != $person['id'] AND !$ticket->hasParticipantPerson($person)) {
				$this->logMessage('[TicketGatewayProcessor] Detected person is not on the ticket. Message will be considered a new ticket.');
				$person = null;
			}
		}

		if ($ticket AND !$person AND (!$detector || $detector->canAddUnknownPerson())) {

			$this->logMessage(sprintf('[TicketGatewayProcessor] Could not find user on ticket, adding user with email %s', $this->reader->getFromAddress()->getEmail()));

			// If the detector didnt find a person, doesnt mean they dont exist
			$person = App::getOrm()->getRepository('DeskPRO:Person')->findOneByEmail($this->reader->getFromAddress()->getEmail());

			// But we'll create them now if they dont
			if (!$person) {
				$this->logMessage('[TicketGatewayProcessor] No existing person found, will try and create it');
				$person = Entity\Person::newContactPerson(array('email' => $this->reader->getFromAddress()->getEmail()));

				App::getDb()->beginTransaction();
				try {
					App::getOrm()->persist($person);
					App::getOrm()->flush($person);
					App::getDb()->commit();
				} catch (\Exception $e) {
					App::getDb()->rollback();
					throw $e;
				}
			}

			if ($person && !$person->is_agent) {
				$ticket->addParticipantPerson($person);
			}
		}

		$ev = $this->createGatewayEvent(array(
			'ticket' => $ticket,
			'person' => $person,
			'cancel' => false
		));
		$this->event_dispatcher->dispatch(self::EVENT_BEFORE_RUN_ACTION, $ev);

		if ($ev->cancel) {
			return null;
		}

		if ($person && $person->is_agent && $this->is_bounce) {
			$this->logMessage('[TicketGatewayProcessor] Is an agent message and is detected as bounced. Rejecting message.');
			$this->error = 'agent_bounce';
			return null;
		}

		if ($person && $person->is_disabled && !$ticket) {
			$this->logMessage('[TicketGatewayProcessor] User is disabeld, rejecting message');
			$this->error = 'from_disabled_user';
			return null;
		}

		if (!$person['is_agent'] && $person['is_disabled']) {
			// user is disabled so can't create/reply to tickets
			$message = App::getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_user:account-disabled.html.twig', array(
				'subject' => $this->reader->getSubject()->getSubjectUtf8(),
				'name' => $this->reader->getFromAddress()->getName() ?: $this->reader->getFromAddress()->getEmail(),
			));
			$message->setTo($this->reader->getFromAddress()->getEmail());
			App::getMailer()->send($message);

			return null;
		}

		$this->email_body_html = $this->reader->getBodyHtml()->getBodyUtf8();
		$this->email_body_text = $this->reader->getBodyText()->getBodyUtf8();

		// Get reply actions
		if ($person) {
			$check_person = $person;
		} elseif ($this->reader->getFromAddress()) {
			$check_person = App::getDataService('Agent')->getByEmail($this->reader->getFromAddress()->getEmail());
		} else {
			$check_person = null;
		}
		if ($check_person && $check_person['is_agent']) {
			if ($this->email_body_html) {
				$rc = new AgentReplyCodes($this->email_body_html, true);
				$rc->setLogger($this->logger);
				$this->reply_actions = $rc->getProperties();
				if ($this->reply_actions) {
					$this->email_body_html = $rc->getNewBody();
				}
			} else {
				$rc = new AgentReplyCodes($this->email_body_text, false);
				$rc->setLogger($this->logger);
				$this->reply_actions = $rc->getProperties();
				if ($this->reply_actions) {
					$this->email_body_text = $rc->getNewBody();
				}
			}
		}

		$reply_as_new = false;
		if ($ticket AND $person AND $ticket->status == 'resolved' AND !$person->hasPerm('tickets.reopen_resolved')) {

			$this->logMessage('[TicketGatewayProcessor] Ticket is resolved');

			// ticket is resolved and can't be reopend, so make a new ticket
			if ($person->hasPerm('tickets.reopen_resolved_createnew')) {
				$this->logMessage('[TicketGatewayProcessor] Has perm reopen_resolved_createnew so creating a new ticket');
				$ticket = null;
				$reply_as_new = true;

			// No perm to create new ticket from resolved,
			// so its a rejection
			} else {
				$this->logMessage('[TicketGatewayProcessor] Message is being rejected because ticket is resolved');

				$email_to = '';
				if ($this->gateway_address) {
					$email_to = $this->gateway_address->match_pattern;
				} else {
					$email_trans = App::getEntityRepository('DeskPRO:EmailTransport')->getDefaultTransport();
					if ($email_trans) {
						$email_to = $email_trans->match_pattern;
					}
				}

				// user is disabled so can't create/reply to tickets
				$message = App::getMailer()->createMessage();
				$message->setTemplate('DeskPRO:emails_user:new-reply-reject-resolved.html.twig', array(
					'subject'  => $this->reader->getSubject()->getSubjectUtf8(),
					'name'     => $this->reader->getFromAddress()->getName() ?: $this->reader->getFromAddress()->getEmail(),
					'ticket'   => $ticket,
					'person'   => $person,
					'email_to' => $email_to
				));
				$message->setTo($this->reader->getFromAddress()->getEmail());
				$message->attach(\Swift_Attachment::newInstance(
					$this->reader->getRawSource(),
					'message.eml',
					'message/rfc822'
				));
				App::getMailer()->send($message);

				$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_OBJ_CLOSED;
				return null;
			}
		}

		if ($ticket AND App::getSetting('core_tickets.process_agent_fwd') AND $person['is_agent'] AND ForwardCutter::subjectIsForward($this->reader->getSubject()->subject)) {
			$this->logMessage(sprintf("Found a ticket match #%d but this is an agent fwd so unsetting", $ticket->getId()));
			$ticket = null;
		}

		$ret = null;
		if ($ticket AND $person) {

			if ($this->logger) {
				$ticket->getTicketLogger()->setLogger($this->logger);
			}

			$person_processor->passPerson($this->reader->getFromAddress(), $person);

			if ($this->reader->getHeader('X-DeskPRO-Build') && $this->reader->getHeader('X-DeskPRO-Build')->getHeader()) {
				$this->logMessage('[TicketGatewayProcessor] Detected a DeskPRO reply, disabling disable_autoresponses');
				$person->setDisableAutoresponses(
					true,
					'User detected as a DeskPRO helpdesk'
				);
			}

			if (!$person->disable_autoresponses) {
				if ($return_path = $this->reader->getHeader('Return-Path')) {
					if ($return_path->getHeader() == '<>') {
						$this->logMessage("Null return path, disabling auto-responses for this user");
						$person->setDisableAutoresponses(
							true,
							'Client sent a null Return-Path'
						);
					}
				}
			}

			App::setCurrentPerson($person);

			// If the agent is replying to an email that is not a notification, then this check doesnt
			// need to run (e.g., they replied to an email they were CCd on).
			$is_reply_to_dpmail = false;
			if ($body_html = $this->email_body_html) {
				if (
					strpos($body_html, 'DP_BOTTOM_MARK') !== false
					|| strpos($body_html, 'DP_TOP_MARK') !== false
					|| strpos($body_html, 'DP_MESSAGE_BEGIN') !== false
					|| strpos($body_html, 'DP_USER_EMAIL') !== false
					|| strpos($body_html, 'DP_AGENT_EMAIL') !== false
				) {
					$is_reply_to_dpmail = true;
				}
			}

			if ($is_reply_to_dpmail) {
				$this->logMessage('[TicketGatewayProcessor] IS a reply to a DeskPRO email');
			} else {
				$this->logMessage('[TicketGatewayProcessor] NOT a reply to a DeskPRO email');
			}

			if (
				$person['is_agent']
				&& (
					// Is not a user email
					(strpos($this->email_body_html, 'DP_USER_EMAIL') === false && $is_reply_to_dpmail)
					||
					// Or is a text email where user email markers wouldnt be detected
					($this->detected_tac_person && $this->detected_tac_person->getId() == $person->getId() && !$is_reply_to_dpmail)
				)
			) {
				$this->logMessage('[TicketGatewayProcessor] runNewAgentReply');
				$ret = $this->runNewAgentReply($ticket, $person);
			} else {
				$this->logMessage('[TicketGatewayProcessor] runNewUserReply');
				$ret = $this->runNewUserReply($ticket, $person);
			}
		} else {
			$this->logMessage('[TicketGatewayProcessor] Creating new ticket');
			$person = $person_processor->findPerson($this->reader->getFromAddress());
			if ($person) {
				$this->logMessage('[TicketGatewayProcessor] Found existing person: ' . $person['id']);
				$person_processor->passPerson($this->reader->getFromAddress(), $person);
			} else {
				$this->logMessage('[TicketGatewayProcessor] Creating new contact');
				if (App::getSetting('core.user_mode') == 'closed') {
					$this->logMessage('[TicketGatewayProcessor] No user and closed registration');
					$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_PERM_INSUFFICIENT;

					$gateway_address_matcher = App::getSystemService('gateway_address_matcher');
					$user_email = $this->reader->getFromAddress()->getEmail();

					if (!$this->is_bounce && !$this->reader->isFromRobot() && !$gateway_address_matcher->getMatchingAddress($user_email) && !$gateway_address_matcher->isHelpdeskAddress($user_email)) {
						$message = App::getMailer()->createMessage();
						$message->setTemplate('DeskPRO:emails_user:new-ticket-reg-closed.html.twig', array(
							'subject' => $this->reader->getSubject()->getSubjectUtf8(),
							'name' => $this->reader->getFromAddress()->getName() ?: $this->reader->getFromAddress()->getEmail(),
						));
						$message->setTo($this->reader->getFromAddress()->getEmail());
						App::getMailer()->send($message);
						return null;
					}
				}
				$person = $person_processor->createPerson($this->reader->getFromAddress());
				$this->logMessage('[TicketGatewayProcessor] Created new contact: ' . $person['id']);
			}

			if ($person && $person->is_agent && $this->is_bounce) {
				$this->logMessage('[TicketGatewayProcessor] Is an agent message and is detected as bounced. Rejecting message.');
				$this->error = 'agent_bounce';
				return null;
			}

			if ($this->reader->getHeader('X-DeskPRO-Build') && $this->reader->getHeader('X-DeskPRO-Build')->getHeader()) {
				$this->logMessage('[TicketGatewayProcessor] Detected a DeskPRO reply, disabling disable_autoresponses');
				$person->setDisableAutoresponses(
					true,
					'User detected as a DeskPRO helpdesk'
				);
			}

			if (!$person->disable_autoresponses) {
				if ($return_path = $this->reader->getHeader('Return-Path')) {
					if ($return_path->getHeader() == '<>') {
						$this->logMessage("Null return path, disabling auto-responses for this user");
						$person->setDisableAutoresponses(
							true,
							'Client sent a null Return-Path'
						);
					}
				}
			}

			App::setCurrentPerson($person);

			if (App::getSetting('core_tickets.process_agent_fwd') AND $person['is_agent'] AND ForwardCutter::subjectIsForward($this->reader->getSubject()->subject)) {
				$this->logMessage('[TicketGatewayProcessor] runNewForwardedTicket');
				$ret = $this->runNewForwardedTicket($person);
			} else {
				$this->logMessage('[TicketGatewayProcessor] runNewTicket');
				$ret = $this->runNewTicket($person, $reply_as_new);
			}
		}

		$ev = $this->createGatewayEvent(array(
			'ticket' => $ticket,
			'person' => $person,
			'return' => $ret
		));
		$this->event_dispatcher->dispatch(self::EVENT_RUN_ACTION, $ev);

		return $ret;
	}

	protected function doNewReply(Entity\Ticket $ticket, $person, $context)
	{
		$this->person = $person;
		$this->ticket = $ticket;
		$ticket->email_reader = $this->reader;

		$this->logMessage("doNewRelpy context $context");
		$this->processBlobs();

		if ($context == 'user') {
			$ticket->getTicketLogger()->recordExtra('is_user_reply', true);
			$ticket->email_reader_action = 'user_reply';
		} else {
			$ticket->getTicketLogger()->recordExtra('is_agent_reply', true);
			$ticket->email_reader_action = 'agent_reply';
		}

		$ticket->getTicketLogger()->recordExtra('reply_actions_override', $this->reply_actions);

		// If this was a reply via a TAC, then the person detected via address and the person who owns the TAC
		// should be the sames. Otherwise, *probably* means the agent used a different email address.
		if ($this->detected_tac_person && $this->detected_tac_person->is_agent && $this->detected_tac_person->getId() != $person->getId()) {
			$this->logMessage('doNewRelpy agent reply with TAC from unknown email address');
			$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_AUTH_INVALID;

			$message = App::getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_agent:error-unknown-from.html.twig', array(
				'ticket'  => $ticket,
				'subject' => $this->reader->getSubject()->getSubjectUtf8(),
				'name'    => $this->reader->getFromAddress()->getName() ?: $this->reader->getFromAddress()->getEmail(),
			));
			$message->setTo($this->reader->getFromAddress()->getEmail());
			App::getMailer()->send($message);

			return null;
		}

		$email_info = array(
			'found_top_marker' => false,
		);

		$email_info['subject'] = $this->reader->getSubject()->getSubjectUtf8();
		if (!$email_info['subject'] && $this->reader->getSubject()->getSubject()) {
			$email_info['subject'] = $this->reader->getSubject()->getSubject();
		}

		if ($this->is_dp3_reply) {
			$email_info = array_merge($email_info, $this->getEmailBodyInfoDp3());
		} else {
			$email_info = array_merge($email_info, $this->getEmailBodyInfo());
		}

		$ev = $this->createGatewayEvent(array(
			'ticket' => $ticket,
			'person' => $person,
			'email_info' => $email_info,
			'cancel' => false,
		));
		$this->event_dispatcher->dispatch(self::EVENT_BEFORE_NEWREPLY, $ev);

		if ($ev->cancel) {
			$this->logMessage('[TicketGatewayProcessor] doNewReply cancel');
			return null;
		}

		if (App::getSetting('core_tickets.gateway_agent_require_marker') && $context == 'agent' && !$email_info['found_top_marker']) {
			// The marker is required for agent emails
			$this->logMessage('doNewRelpy agent reply missing marker');
			$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_MISSING_MARKER;

			$message = App::getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_agent:error-marker-missing.html.twig', array(
				'ticket'  => $ticket,
				'subject' => $this->reader->getSubject()->getSubjectUtf8(),
				'name'    => $this->reader->getFromAddress()->getName() ?: $this->reader->getFromAddress()->getEmail(),
			));
			$message->setTo($this->reader->getFromAddress()->getEmail());
			App::getMailer()->send($message);

			return null;
		}

		$email_info = $ev->email_info;

		if ($this->is_bounce) {
			$ticket->getTicketLogger()->recordExtra('is_bounce_message', true);
		}

		$message = new Entity\TicketMessage();
		$message->email_reader = $this->reader;
		if ($this->reader->hasProperty('email_source')) {
			$message['email_source'] = $this->reader->getProperty('email_source');
		}

		if ($person->is_agent) {
			$message->creation_system = 'gateway.agent';
		} else {
			$message->creation_system = 'gateway.person';
		}

		$message['ticket'] = $ticket;
		$message['person'] = $person;
		$message['email'] = $this->reader->getFromAddress()->getEmail();

		$message['message'] = $email_info['body'];
		$message['message_full'] = $email_info['body_full'];
		$message['message_raw'] = $email_info['body_raw'];

		$message['show_full_hint'] = false;
		$inline_reply_detector = new \Application\DeskPRO\EmailGateway\TicketGateway\DetectInlineReply(App::getOrm(), $this->reader);
		if ($this->logger) {
			$inline_reply_detector->setLogger($this->logger);
		}

		if ($inline_reply_detector->hasDifferentMessage() && $message['message_full']) {
			$message['show_full_hint'] = true;
		}

		if (isset($this->reply_actions['is_note'])) {
			$message['is_agent_note'] = true;
			$ticket->email_reader_action = 'agent_note';
		}

		$ticket_attach = array();
		foreach ($this->processBlobs() as $blob) {

			if (isset($this->dupe_inline_blobs[$blob->getId()])) {
				continue;
			}

			$attach = new Entity\TicketAttachment();
			$attach['blob'] = $blob;
			$attach['person'] = $person;

			if (isset($this->inline_blobs[$blob->getId()])) {
				$attach->is_inline = true;
			}

			$message->addAttachment($attach);
			$ticket_attach[] = $attach;
		}

		$has_message = true;
		if (!$ticket_attach && !trim(strip_tags($email_info['body']))) {
			$has_message = false;
		}

		$has_reply_codes = false;
		if ($this->reply_actions) {
			$has_reply_codes = true;
		}

		$ticket->getTicketLogger()->recordExtra('by_agent', $person);
		$ticket->getTicketLogger()->recordExtra('action_performer', $person->id);

		// - Only add the message if we have an actual message
		// This allows email replies with action codes but no reply,
		// so the "empty reply" isnt processed as a reply
		$did_add_message = false;
		if (!isset($this->reply_actions['no_reply']) && ($has_message || ($has_reply_codes && !$has_message))) {

			$this->logMessage('[TicketGatewayProcessor] Checking for dupe message: ' . $message->getMessageHash());

			$did_add_message = true;
			if ($dupe_message = App::getOrm()->getRepository('DeskPRO:TicketMessage')->checkDupeMessage($message, $ticket, 10800, $this->logger)) {
				$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_DUPE;
				$this->logMessage('[TicketGatewayProcessor] doNewReply duplicate message ' . $dupe_message->getId());

				// Reset some objects so they dont get flushed during next loop
				$ticket->resetTicketLogger();
				App::getOrm()->detach($ticket);
				App::getOrm()->detach($message);

				foreach ($ticket_attach as $a) {
					$a->ticket = null;
					$a->message = null;
					App::getOrm()->detach($a);
				}

				return $dupe_message;
			}

			$ticket->addMessage($message);
		} else {
			if (isset($this->reply_actions['no_reply'])) {
				$this->logMessage('No reply because of #noreply tag');
			} else {
				$this->logMessage('No reply because empty reply');
			}
		}

		if ($this->reader->getCcAddresses() || count($this->reader->getToAddresses()) > 1) {
			$this->logMessage('[TicketGatewayProcessor] Has CC');
			$this->handleCc($ticket, $this->reader->getDeliveredAddresses());
		}

		if (!$this->is_bounce && !$message->is_agent_note) {
			if ($person['is_agent'] && $context == 'agent') {
				$this->logMessage('[TicketGatewayProcessor] doNewReply set status = awaiting_user');
				$ticket['status'] = Entity\Ticket::STATUS_AWAITING_USER;
			} else {
				$this->logMessage('[TicketGatewayProcessor] doNewReply set status = awaiting_agent');
				$ticket['status'] = Entity\Ticket::STATUS_AWAITING_AGENT;
			}
		}

		$this->applyChangesArray($ticket);

		// If we didnt add a message, then it was an actions-only message
		// So we should reply with the standard 'updated' email which lists actions
		if (!$did_add_message) {
			$ticket->email_reader_action = 'agent_actions';
			$ticket->getTicketLogger()->recordExtra('force_notify_email', $person->id);
		}

		$charset_error = $this->charset_error;
		App::getDb()->beginTransaction();

		try {
			App::getOrm()->persist($person);
			App::getOrm()->persist($ticket);
			if ($did_add_message) {
				App::getOrm()->persist($message);
			}
			App::getOrm()->flush();

			if ($charset_error) {
				App::getOrm()->getConnection()->insert('tickets_messages_raw', array(
					'message_id' => $message['id'],
					'raw'        => $email_info['body'],
					'charset'    => $charset_error,
				));
			}
			App::getDb()->commit();
		} catch (\Exception $e) {
			App::getDb()->rollback();
			throw $e;
		}

		$ev = $this->createGatewayEvent(array(
			'ticket' => $ticket,
			'person' => $person,
			'message' => $message
		));
		$this->event_dispatcher->dispatch(self::EVENT_NEWREPLY, $ev);

		return $message;
	}

	protected function getEmailBodyInfo()
	{
		$email_info = array();

		$inline_images = new InlineImageTokens($this->reader);
		$inline_images2 = new InlineImageTokens($this->reader);

		$orig_text = $this->email_body_text;
		$did_html_trim = false;
		$is_text = false;
		$has_text_cut = false;
		$has_cut = false;

		$precut_do_plaintext = false;

		if ($this->email_body_html) {
			$this->logMessage('[TicketGatewayProcessor] doNewReply read HTML email');
			$email_info['body'] = $this->email_body_html;
			if (!$email_info['body']) {
				$email_info['body'] = $this->reader->getBodyHtml()->getBody();
				$this->charset_error = $this->reader->getBodyHtml()->getOriginalCharset();
			}
			$email_info['body_is_html'] = true;

			// Sent from a DeskPRO instance, we should get the specific message by looking for our delims
			// But dont do this cut if its an auto-reply, we want the real message in those cases. The actual notifs we sent
			// are silenced in those cases anyway so the auto-replies are handled like other robot replies
			if (
				$this->reader->getHeader('X-DeskPRO-Build') && $this->reader->getHeader('X-DeskPRO-Build')->getHeader()
				&& !($this->reader->getHeader('X-DeskPRO-Auto') && $this->reader->getHeader('X-DeskPRO-Auto')->getHeader())
			) {
				$body = trim(\Orb\Util\Strings::extractRegexMatch('#<!\-\- DP_MESSAGE_BEGIN \-\->(.*?)<!\-\- DP_MESSAGE_END \-\->#s', $email_info['body'], 1));
				if ($body) {
					$email_info['body'] = $body;
				}
			}

			$body_raw = $email_info['body'];

			// If the document is too complex then htmlpurifier can crash.
			// We'll try to find a cut-mark now and trim the document down to see if we can still use it
			// (We dont alway cut first because we want an in-tact 'full body' if possible)
			if (substr_count($email_info['body'], '>') > 15000) {
				$this->logMessage('[TicketGatewayProcessor] Document too complex, pre-cut');

				$cut = new \Application\DeskPRO\EmailGateway\Cutter\Def\Generic();
				if ($this->ticket) {
					$generic_cut = $cut->cutQuoteBlock($email_info['body'], $email_info['body_is_html']);
				} else {
					$generic_cut = $email_info['body'];
				}

				// If we had no successful cut or the body is still too complex, use the plaintext version
				if ($email_info['body'] == $generic_cut || substr_count($email_info['body'], '>') > 15000) {
					$this->logMessage('[TicketGatewayProcessor] Cut document still too complex, using plaintext');

					$email_info['body'] = $this->email_body_text;
					if ($email_info['body']) {
						$email_info['body'] = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($email_info['body'], \ENT_QUOTES, 'UTF-8')));
					} else {
						$email_info['body'] = strip_tags($this->email_body_html);
						$email_info['body'] = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($email_info['body'], \ENT_QUOTES, 'UTF-8')));
					}
					$email_info['body_is_html'] = false;

					$precut_do_plaintext = true;

				// The trimmed document is short enough to use
				} else {
					$this->logMessage('[TicketGatewayProcessor] Using cut-trimmed document');

					$did_html_trim = true;
					$email_info['body'] = $generic_cut;
					$email_info['body_is_html'] = true;
				}
			}
		}

		if ($precut_do_plaintext || !$this->email_body_html) {
			$is_text = true;

			$this->logMessage('[TicketGatewayProcessor] doNewReply read text email');
			$txt = $this->email_body_text;
			if (!$txt && $this->email_body_text) {
				$txt = $this->reader->getBodyText()->getBody();
				$this->charset_error = $this->reader->getBodyText()->getOriginalCharset();
			}

			if (strlen($txt) > 25000) {
				$this->logMessage('[TicketGatewayProcessor] Message too long, trimming');
				$did_html_trim = true;
				$txt = substr($txt, 0, 25000);
			}

			$body_raw = @htmlspecialchars($txt, \ENT_QUOTES, 'UTF-8');

			$has_text_cut = true;
			$email_info['body_raw'] = $txt;
			$email_info['generic_cut'] = $txt;
			$email_info['body'] = $txt;
			$email_info['body_full'] = $txt;

			// Always generic cut from the DP_TOP_MARK position first
			// The PatternCutter will trim off the remaining quoted headers
			$cut = new \Application\DeskPRO\EmailGateway\Cutter\Def\Generic();
			$generic_cut = $cut->cutQuoteBlock($email_info['body'], false);
			if ($email_info['body'] != $generic_cut) {
				$this->logMessage("Generic cutter matched");
				$email_info['body'] = $generic_cut;
				$email_info['generic_cut'] = $generic_cut;
				$email_info['found_top_marker'] = true;
				$has_cut = true;
			} else {
				$this->logMessage("Generic cutter did not match");
				$email_info['found_top_marker'] = false;
			}

			$cutter = new \Application\DeskPRO\EmailGateway\Cutter\TextPatternCutter();
			$pattern_config = new \Application\DeskPRO\Config\UserFileConfig('text-cut-patterns');
			$cutter->addPatterns($pattern_config->all());

			$email_info['body'] = $cutter->cutQuoteBlock($email_info['body'], false);

			if ($cutter->getMatchedPatterns()) {
				$has_text_cut = true;
				foreach ($cutter->getMatchedPatterns() as $p) {
					$this->logMessage("Text cutter matched pattern: " . $p->getPattern());
				}
			} else {
				$this->logMessage("Text cutter did not match any pattern");
			}

			// Run generic cutter as well, in case it matches higher
			$parts = $this->cutterDef->splitFromFirstHeaderText($email_info['body']);
			if ($parts && count($parts) == 2) {
				$this->logMessage("Split header cutter matched, cut from standard quote headers");
				$email_info['body'] = trim($parts[0]);
			} else {
				$this->logMessage("Split header cutter did not match");
			}

			$email_info['body'] = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($email_info['body'], \ENT_QUOTES, 'UTF-8')));
			$email_info['body_full'] = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($email_info['body_full'], \ENT_QUOTES, 'UTF-8')));
			$email_info['generic_cut'] = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($email_info['generic_cut'], \ENT_QUOTES, 'UTF-8')));
			$email_info['body_is_html'] = false;
		}

		if (!$is_text) {
			$email_info['body_raw'] = $email_info['body'];
			$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_preclean');

			if ($did_html_trim) {
				// We pre-trimmed, lets set the full body to the plaintext version so we always have the full message
				$email_info['body_full'] = nl2br(htmlspecialchars($orig_text, \ENT_QUOTES, 'UTF-8'));
			} else {
				$email_info['body_full'] = $email_info['body'];
			}

			// Always generic cut from the DP_TOP_MARK position first
			// The PatternCutter will trim off the remaining quoted headers
			$generic_cutter = new \Application\DeskPRO\EmailGateway\Cutter\Def\Generic();
			if ($this->ticket) {
				$generic_cut = $generic_cutter->cutQuoteBlock($email_info['body'], $email_info['body_is_html']);
			} else {
				$generic_cut = $email_info['body'];
			}

			if ($email_info['body'] != $generic_cut) {
				$email_info['body'] = $generic_cut;
				$email_info['generic_cut'] = $generic_cut;
				$email_info['found_top_marker'] = true;
				$has_cut = true;
			} else {
				$email_info['found_top_marker'] = false;
			}

			if ($email_info['body_is_html']) {
				$cutter = new \Application\DeskPRO\EmailGateway\Cutter\PatternCutter();
				$pattern_config = new \Application\DeskPRO\Config\UserFileConfig('html-cut-patterns');
				$cutter->addPatterns($pattern_config->all());

				$email_info['body'] = $cutter->cutQuoteBlock($email_info['body'], true);

				if ($cutter->getMatchedPatterns()) {
					$has_cut = true;
					foreach ($cutter->getMatchedPatterns() as $p) {
						$this->logMessage("Cutter matched pattern: " . $p->getPattern());
					}
				} else {
					$this->logMessage("Cutter did not match any pattern");
				}
			}

			$email_info['body'] .= $generic_cutter->cutBottomBlock($email_info['body_raw'], true);
		}

		// Cut down the quoted message part to 10000 chars
		$cut_len = strlen($email_info['body']);
		$full_len = strlen($email_info['body_full']);

		if (($full_len - $cut_len) > 10000) {
			$this->logMessage('body_full too long, trimming');
			$email_info['body_full'] = substr($email_info['body_full'], 0, 10000 + $cut_len);

			// Simple way to try and handle if we cut in the middle of a tag name
			$tag_start_pos = strrpos($email_info['body_full'], '<');
			if ($tag_start_pos) {
				$tag_end_pos = strrpos($email_info['body_full'], '>');
				if ($tag_end_pos === false || $tag_end_pos < $tag_start_pos) {
					$email_info['body_full'] = substr($email_info['body_full'], 0, $tag_start_pos);
				}
			}

			$email_info['body_full'] .= "\n\n";
			if ($email_info['body_is_html']) {
				$email_info['body_full'] .= "<br /><br />";
			}

			$email_info['body_full'] .= App::getTranslator()->phrase('user.emails.message-clipped');
		}

		// Replace inline image tags with tokens
		$email_info['body'] = $inline_images->processTokens($email_info['body']);
		$email_info['body_full'] = $inline_images2->processTokens($email_info['body_full']);

		if ($email_info['body_is_html']) {
			// The basic cleaner cleans out outlook type stuff like empty <p>'s that cause whitespace
			$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_preclean');
			$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_basicclean');
			$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email');
			$email_info['body'] = $this->trimHtmlWhitespace($email_info['body']);
			$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_postclean');
		}

		$email_info['body'] = $this->replaceInlineAttachTokens($email_info['body'], $inline_images);
		$email_info['body_full'] = $this->replaceInlineAttachTokens($email_info['body_full'], $inline_images2);

		// If there was no cutting, then the body is the full body
		// Dont store the dupe content
		if (!$has_cut && !$has_text_cut) {
			$this->logMessage('no cut was made, no body_full needed');
			$email_info['body_full'] = '';
		}

		$email_info['body_full'] = $this->cleaner->clean($email_info['body_full'], 'html_email_basicclean');
		$email_info['body_full'] = $this->cleaner->clean($email_info['body_full'], 'html_email');

		// The cut message is blank, fallback to using the full message
		if (!trim(strip_tags($email_info['body']))) {
			if (isset($email_info['generic_cut']) && trim(strip_tags($email_info['generic_cut']))) {
				$email_info['body'] = $email_info['generic_cut'];
			} else {
				$email_info['body'] = $email_info['body_full'];
				$email_info['body_full'] = '';
			}
		}

		// Clean out PTAC's on this ticket to prevent mistakes with forwarding
		// (Check on ticket since this can still be called from newticket if the users original ticket was closed)
		if ($this->ticket) {
			foreach ($this->ticket->access_codes as $code) {
				$email_info['body']      = str_replace('(#' . $code->getAccessCode() . ')', '', $email_info['body']);
				$email_info['body_full'] = str_replace('(#' . $code->getAccessCode() . ')', '', $email_info['body_full']);
			}
		}

		$email_info['body_raw'] = $body_raw;

		$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_postclean');
		$email_info['body_raw'] = $this->cleaner->clean($email_info['body_raw'], 'html_email_postclean');
		$email_info['body_full'] = $this->cleaner->clean($email_info['body_full'], 'html_email_postclean');

		if ($is_text && $did_html_trim) {
			$email_info['body_full'] = nl2br(htmlspecialchars($this->email_body_text));
		}

		// Fix empty body_full that might result if a trim was made
		// that caused the domdocument to become invalid, and the html cleaners might
		// fail. In these cases, fall back on text
		if (!trim($email_info['body_full'])) {
			$email_info['body_full'] = nl2br(htmlspecialchars($this->email_body_text));
		}

		return $email_info;
	}

	protected function getEmailBodyInfoDp3()
	{
		$email_info = array();
		$email_info['body_is_html'] = false;

		$inline_images = new InlineImageTokens($this->reader);

		$this->logMessage('[TicketGatewayProcessor] Processing DP3 reply text');

		if ($this->email_body_text) {
			$this->logMessage('[TicketGatewayProcessor] doNewReply read text email');
			$txt = $this->email_body_text;
			if (!$txt && $this->email_body_text) {
				$txt = $this->reader->getBodyText()->getBody();
				$this->charset_error = $this->reader->getBodyText()->getOriginalCharset();
			}

			$email_info['body'] = $txt;
		} else {
			$this->logMessage('[TicketGatewayProcessor] doNewReply read HTML email');
			$email_info['body'] = $this->email_body_html;
			if (!$email_info['body']) {
				$email_info['body'] = strip_tags($this->reader->getBodyHtml()->getBodyUtf8());
				$this->charset_error = $this->reader->getBodyHtml()->getOriginalCharset();
			}

			// Replace inline image tags with tokens
			$email_info['body'] = $inline_images->processTokens($email_info['body']);
			$email_info['body_full'] = $inline_images->processTokens($email_info['body']);
		}

		$email_info['body_raw'] = $email_info['body'];
		$email_info['body_full'] = $email_info['body'];

		$agent_pos_1 = strpos($email_info['body'], '=== Enter your reply below this line ===');
		$agent_pos_2 = strpos($email_info['body'], '=== Enter your reply above this line ===');

		#------------------------------
		# Agent markers
		#------------------------------

		if ($agent_pos_1 !== false && $agent_pos_2 !== false) {
			$email_info['body'] = \Orb\Util\Strings::getBetweenBoundary(
				$email_info['body'],
				'=== Enter your reply below this line ===',
				'=== Enter your reply above this line ==='
			);

		#------------------------------
		# User email
		#------------------------------

		} else {
			$user_pos_1 = strpos($email_info['body'], '========= Please enter your reply ABOVE this line =========');
			if ($user_pos_1 !== false) {
				$email_info['body'] = \Orb\Util\Strings::getAboveBoundary(
					$email_info['body'],
					'========= Please enter your reply ABOVE this line ========='
				);
			}
		}

		$cut = new \Application\DeskPRO\EmailGateway\Cutter\Def\Generic();
		$email_info['body'] = $cut->cutQuoteBlock($email_info['body'], $email_info['body_is_html']);

		$email_info['body'] = trim($email_info['body'], " >\n\r");

		$email_info['body'] = nl2br(htmlspecialchars($email_info['body'], \ENT_QUOTES, 'UTF-8'));
		$email_info['body_full'] = nl2br(htmlspecialchars($email_info['body_full'], \ENT_QUOTES, 'UTF-8'));

		$email_info['body'] = $this->replaceInlineAttachTokens($email_info['body'], $inline_images);

		return $email_info;
	}

	public function trimHtmlWhitespace($html)
	{
		return \Orb\Util\Strings::trimHtmlAdvanced($html);
	}

	public function handleCc($ticket, array $ccs)
	{
		$gateway_address_matcher = App::getSystemService('gateway_address_matcher');

		$count = 0;
		foreach ($ccs as $cc) {

			$cc_email = $cc->getEmail();
			$this->logMessage("Checking cc: $cc_email");

			// Max 10 CC's to prevent mass spamming
			if ($count >= 10) {
				$this->logMessage("CC limit reached, break");
				break;
			}

			// Make sure its actually valid
			if (!StringEmail::isValueValid($cc_email)) {
				$this->logMessage("Invalid email address");
				continue;
			}

			$addr = $gateway_address_matcher->getMatchingAddress($cc_email);
			if ($addr) {
				$this->logMessage("Skipping cc: $cc_email (matches gateway address {$addr->id})");
				continue;
			}
			if ($gateway_address_matcher->isHelpdeskAddress($cc_email)) {
				$this->logMessage("Skipping cc: $cc_email (matches helpdesk address)");
				continue;
			}

			if ($ticket->hasParticipantEmailAddress($cc_email)) {
				$this->logMessage("Skipping cc: $cc_email (address already on ticket)");
				continue;
			}

			$person_processor = new PersonFromEmailProcessor();

			$cc_person = $person_processor->findPerson($cc);
			if (!$cc_person) {
				// Closed helpdesk and an unknown CC means we drop it
				if (App::getContainer()->getSetting('core.user_mode') == 'closed') {
					$this->logMessage("Skipping cc: $cc_email (no person match and closed helpdesk)");
					continue;
				}
				$cc_person = $person_processor->createPerson($cc, true);
				$this->logMessage("Added cc: $cc_email (Person {$cc_person->id})");
			}

			if (!$cc_person) {
				continue;
			}

			if ($cc_person->is_agent && !$this->person->is_agent) {
				if (!$this->person || !$this->person->getId() || !$this->person->is_agent) {
					if (!App::getSetting('core_tickets.add_agent_ccs')) {
						$this->logMessage("Skipping agent CC because core_tickets.add_agent_ccs is off");
						continue;
					}
				}
			}

			$this->logMessage("Add CC person: {$cc_person->getId()}");

			if (!$ticket->hasParticipantPerson($cc_person)) {
				$ticket->addParticipantPerson($cc_person);
				$count++;
			}
		}
	}


	############################################################################
	# New Reply: Agent
	############################################################################

	protected function runNewAgentReply(Entity\Ticket $ticket, Entity\Person $person)
	{
		$message = $this->doNewReply($ticket, $person, 'agent');
		return $message;
	}


	############################################################################
	# New Reply: User
	############################################################################

	protected function runNewUserReply(Entity\Ticket $ticket, Entity\Person $person)
	{
		$message = $this->doNewReply($ticket, $person, 'user');
		return $message;
	}


	############################################################################
	# New Ticket
	############################################################################

	protected function runNewTicket(Entity\Person $person, $run_reply_cutter = false)
	{
		$this->person = $person;

		#------------------------------
		# Read email body/subject
		#------------------------------

		$email_info = array();

		$this->processBlobs();
		$inline_images = new InlineImageTokens($this->reader);

		$email_info['subject'] = $this->reader->getSubject()->getSubjectUtf8();
		if (!$email_info['subject'] && $this->reader->getSubject()->getSubject()) {
			$email_info['subject'] = $this->reader->getSubject()->getSubject();
		}

		if (!$run_reply_cutter) {
			// Auto-detect if we should run the cutter anyway to catch large
			// emails that weren't caught as replies
			if (
				strpos($this->email_body_html, 'DP_TOP_MARK') !== false
				|| strpos($this->email_body_html, '<!-- DP_MESSAGE_BEGIN -->') !== false
				|| substr_count($this->email_body_html, '>') > 15000
			) {
				$run_reply_cutter = true;
			}
		}

		if ($run_reply_cutter) {
			$this->logMessage('[TicketGatewayProcessor] runNewTicket running reply cutter (new ticket from reply)');
			$email_info = array_merge($email_info, $this->getEmailBodyInfo());
		} else {
			if ($this->email_body_html) {
				$this->logMessage('[TicketGatewayProcessor] runNewTicket read HTML email');
				$email_info['body'] = $this->email_body_html;
				if (!$email_info['body']) {
					$email_info['body'] = $this->reader->getBodyHtml()->getBody();
					$this->charset_error = $this->reader->getBodyHtml()->getOriginalCharset();
				}

				// Sent from a DeskPRO instance, we should get the specific message by looking for our delims
			// But dont do this cut if its an auto-reply, we want the real message in those cases. The actual notifs we sent
			// are silenced in those cases anyway so the auto-replies are handled like other robot replies
			if (
				$this->reader->getHeader('X-DeskPRO-Build') && $this->reader->getHeader('X-DeskPRO-Build')->getHeader()
				&& !($this->reader->getHeader('X-DeskPRO-Auto') && $this->reader->getHeader('X-DeskPRO-Auto')->getHeader())
			) {
					$body = trim(\Orb\Util\Strings::extractRegexMatch('#<!\-\- DP_MESSAGE_BEGIN \-\->(.*?)<!\-\- DP_MESSAGE_END \-\->#s', $email_info['body'], 1));
					if ($body) {
						$email_info['body'] = $body;
					}
				}

				$email_info['body_is_html'] = true;
			} else {
				$this->logMessage('[TicketGatewayProcessor] runNewTicket read text email');
				$txt = $this->email_body_text;
				if (!$txt && $this->email_body_text) {
					$txt = $this->reader->getBodyText()->getBody();
					$this->charset_error = $this->reader->getBodyText()->getOriginalCharset();
				}

				if (strlen($txt) > 25000) {
					$this->logMessage('[TicketGatewayProcessor] Message too long, trimming');
					$txt = substr($txt, 0, 25000);
				}

				$email_info['body'] = str_replace(array("\n", "\r"), '', nl2br(@htmlspecialchars($txt, \ENT_QUOTES, 'UTF-8')));
				$email_info['body_is_html'] = false;
			}

			// Replace inline image tags with tokens
			$email_info['body_raw'] = $email_info['body'];
			$email_info['body'] = $inline_images->processTokens($email_info['body']);
			$email_info['body_full'] = '';

			if ($email_info['body_is_html']) {
				// The basic cleaner cleans out outlook type stuff like empty <p>'s that cause whitespace
				$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_preclean');
				$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_basicclean');
				$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email');
			}

			$email_info['body'] = \Orb\Util\Strings::trimHtml($email_info['body']);
		}

		$ev = $this->createGatewayEvent(array(
			'person' => $person,
			'email_info' => $email_info,
			'cancel' => false,
		));
		$this->event_dispatcher->dispatch(self::EVENT_BEFORE_NEWTICKET, $ev);

		if ($ev->cancel) {
			return null;
		}

		$email_info = $ev->email_info;

		$email_info['body'] = $this->cleaner->clean($email_info['body'], 'html_email_postclean');
		$email_info['body'] = $this->replaceInlineAttachTokens($email_info['body'], $inline_images);

		#------------------------------
		# Try to guess based off the email
		#------------------------------

		$use_lang = null;

		if (!$person->getRealLanguage() && App::getDataService('Language')->isLangSystemEnabled()) {
			$detect_body = strip_tags($email_info['body']);
			if (strlen($detect_body) < 300) {
				$this->logMessage('Message too short to attempt lang detection');
			} else {
				/** @var $lang_detect \Application\DeskPRO\Languages\Detect */
				$lang_detect = App::getSystemService('language_detect');
				$this->logMessage("Detectable languages: " . implode(', ', $lang_detect->getDetectableLanguages()));

				$lang = $lang_detect->detectLanguage($detect_body);
				if ($lang) {
					$this->logMessage("Detected language {$lang->title} (#{$lang->id})");
					$use_lang = $lang;
				}
			}
		}

		#------------------------------
		# Create the ticket
		#------------------------------

		$newticket = new \Application\DeskPRO\Tickets\NewTicket\NewTicket(
			Entity\Ticket::CREATED_GATEWAY_PERSON,
			$person
		);

		if ($use_lang) {
			$newticket->language = $use_lang;
		}

		// We do our own dupe check here
		$newticket->do_dupe_check = false;

		$newticket->setPersonContext($person);
		$newticket->gateway = $this->gateway;
		$newticket->gateway_address = $this->gateway_address;
		if ($this->gateway->department && !count($this->gateway->department->getChildren())) {
			$newticket->ticket->department_id = $this->gateway->department->getId();
		}
		$newticket->sent_to = $this->sent_to;
		$newticket->setEmailReader($this->reader);

		if ($this->logger) {
			$newticket->logger = $this->logger;
		}
		$newticket->setPersonContext($person);

		$newticket->ticket->subject = $email_info['subject'];
		$newticket->ticket->message = $email_info['body'];
		$newticket->ticket->message_raw = $email_info['body_raw'];
		$newticket->ticket->message_is_html = true;
		$newticket->ticket->department_id = null;

		#------------------------------
		# Check for dupe first
		#------------------------------

		if ($person && !$person->isNewPerson()) {
			$ticket_message = new Entity\TicketMessage();
			$ticket_message['person']  = $person;
			$ticket_message->setMessageHtml($email_info['body']);
			$ticket_message->withNewSubject = $newticket->ticket->subject;

			if ($dupe_message = App::getOrm()->getRepository('DeskPRO:TicketMessage')->checkDupeMessage($ticket_message, null, 10800, $this->logger)) {
				$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_DUPE;
				$this->logMessage('[TicketGatewayProcessor] Duplicate message ' . $dupe_message->getId());
				return $dupe_message;
			}
		}

		#------------------------------
		# Process new ticket
		#------------------------------

		App::getDb()->beginTransaction();

		try {
			$newticket->attach_blobs = $this->processBlobs();

			$newticket->blobs_inline_ids = array();
			foreach ($newticket->attach_blobs as $bid => $b) {
				if (isset($this->inline_blobs[$bid])) {
					$newticket->blobs_inline_ids[] = $bid;
				}
			}

			$self = $this;
			$ticket = $newticket->save(array(
				'pre_persist_callback' => function(Entity\Ticket $ticket) use ($self) {
					$self->applyChangesArray($ticket);
				}
			));

			$this->logMessage('[TicketGatewayProcessor] Ticket record ' . $ticket->id);

			$message = $newticket->new_message;
			$message['email'] = $this->reader->getFromAddress()->getEmail();

			$newticket = null;

			if ($this->reader->getCcAddresses() || count($this->reader->getToAddresses()) > 1) {
				$this->logMessage('[TicketGatewayProcessor] Has CC');
				$this->handleCc($ticket, $this->reader->getDeliveredAddresses());
			}

			if ($this->reader->hasProperty('email_source')) {
				$message['email_source'] = $this->reader->getProperty('email_source');
			}

			// Set the proper email address on the ticket from the users account
			if ($this->reader->getFromAddress()->email != $person->getPrimaryEmailAddress()) {
				$email_rec = $person->findEmailAddress($this->reader->getFromAddress()->getEmail());
				if ($email_rec) {
					$ticket->person_email = $email_rec;
				}
			}

			$ticket->gateway = $this->getGateway();
			$ticket->gateway_address = $this->getGatewayAddress();

			$message['message'] = $email_info['body'];

			App::getOrm()->persist($ticket);
			App::getOrm()->persist($message);
			App::getOrm()->persist($person);
			App::getOrm()->flush();

			if ($this->charset_error) {
				App::getOrm()->getConnection()->insert('tickets_messages_raw', array(
					'message_id' => $message['id'],
					'raw'        => $email_info['body'],
					'charset'    => $this->charset_error,
				));
			}

			$this->logMessage('[TicketGatewayProcessor] Created ticket ' . $ticket['id']);

			App::getDb()->commit();
		} catch (\Exception $e) {
			App::getDb()->rollback();
			throw $e;
		}

		$ev = $this->createGatewayEvent(array(
			'ticket' => $ticket,
			'person' => $person,
		));
		$this->event_dispatcher->dispatch(self::EVENT_NEWTICKET, $ev);

		return $ticket;
	}

	############################################################################
	# New Ticket: Agent forwarded message
	############################################################################

	protected function runNewForwardedTicket(Entity\Person $agent)
	{
		$this->person = $agent;

		$this->logMessage('[TicketGatewayProcessor] Forwarded ticket by ' . $agent->getId() . ' ' . $agent->getDisplayContact());

		#------------------------------
		# Read in email props and create cutter
		#------------------------------

		$email_info = array();
		$email_info['subject'] = $this->reader->getSubject()->subject;
		if ($email_info['body'] = $this->email_body_text) {
			$email_info['body_is_html'] = false;
		} else {
			$email_info['body'] = $this->email_body_html;
			$email_info['body_is_html'] = false;
			$email_info['body'] = \Orb\Util\Strings::html2Text($email_info['body']);
		}

		$fwd_cutter = new ForwardCutter($email_info['body'], $email_info['body_is_html'], $this->cutterDef);

		$ev = $this->createGatewayEvent(array(
			'email_info' => $email_info,
			'fwd_cutter' => $fwd_cutter,
			'cancel' => false,
		));

		$this->event_dispatcher->dispatch(self::EVENT_BEFORE_FWD_NEWTICKET, $ev);

		if ($ev->cancel OR !$fwd_cutter->isValid()) {
			$this->logMessage('[TicketGatewayProcessor] Invalid forward');

			$has_eml_attach = false;
			foreach ($this->reader->getAttachments() as $attach) {
				if ($attach->mime_type == 'message/rfc822' && $attach->file_name == 'email.eml') {
					$has_eml_attach = $attach;
					break;
				}
			}

			if ($has_eml_attach) {
				return $this->runNewForwardedEmailAsAttachTicket($agent, $has_eml_attach);
			}

			if ($fwd_cutter->getErrorCode() == 'unknown_email') {
				$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_INVALID_FWD_EMAIL;
			} else {
				$this->error = \Application\DeskPRO\Entity\EmailSource::ERR_INVALID_FWD;
			}

			$message = App::getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_agent:error-invalid-forward.html.twig', array(
				'subject' => $this->reader->getSubject()->getSubjectUtf8(),
				'name'    => $this->reader->getFromAddress()->getName() ?: $this->reader->getFromAddress()->getEmail(),
				'error'   => $this->error
			));
			$message->setTo($this->reader->getFromAddress()->getEmail());
			$message->attach(\Swift_Attachment::newInstance(
				$this->reader->getRawSource(),
				'message.eml',
				'message/rfc822'
			));

			App::getMailer()->send($message);

			return null;
		}

		$email_info['subject'] = ForwardCutter::cutSubjectForwardPrefix($email_info['subject']);

		$agent_reply = $fwd_cutter->getReply();

		if ($agent_reply) {
			$agent_reply = $this->cleanBodyText($agent_reply);
		}

		#------------------------------
		# Find person
		#------------------------------

		$person_processor = new PersonFromEmailProcessor();
		$person_email_item = $fwd_cutter->getUserEmailItem();

		$person = $person_processor->findPerson($person_email_item);
		if ($person) {
			$person_processor->passPerson($person_email_item, $person);
		} else {
			$person = $person_processor->createPerson($person_email_item, true);
		}

		#------------------------------
		# Create ticket
		#------------------------------

		$newticket = new \Application\DeskPRO\Tickets\NewTicket\NewTicket(
			Entity\Ticket::CREATED_GATEWAY_AGENT,
			$person
		);
		$newticket->setPersonContext($person);
		$newticket->gateway = $this->gateway;
		$newticket->gateway_address = $this->gateway_address;
		$newticket->sent_to	= $this->sent_to;

		if ($this->logger) {
			$newticket->logger = $this->logger;
		}

		$newticket->ticket->subject = $email_info['subject'];

		$body = $fwd_cutter->getForwardedMessage();
		$body = $this->cleanBodyText($body);
		$newticket->ticket->message = $body;

		$tracker_extras = array(
			'fwd_via_agent' => $agent
		);
		if ($agent->getPref("agent_notify_override.forward.email")) {
			$tracker_extras['force_notify_email'] = array($agent->id);
		}
		if ($agent->getPref("agent_notify_override.forward.alert")) {
			$tracker_extras['force_notify_alert'] = array($agent->id);
		}

		$fwd_info = $fwd_cutter->getData();
		if (!empty($fwd_info['fwd_cc_unknown'])) {
			$tracker_extras['fwd_cc_unknown'] = $fwd_info['fwd_cc_unknown'];
		}

		if (isset($this->reply_actions['user'])) {
			$newticket->creation_system = 'gateway.agent';
		}

		App::getOrm()->beginTransaction();
		$ticket = $newticket->save(array(), $tracker_extras);

		if (!$newticket->new_message) {
			$this->logMessage("[TicketGatewayProcessor] Found as duplicate of ticket: $ticket->id");
		}

		// Handle CC's
		$fwd_data = $fwd_cutter->getData();
		$cc_emails = $this->reader->getDeliveredAddresses();

		if ($fwd_data['fwd_cc_addresses']) {
			foreach ($fwd_data['fwd_cc_addresses'] as $e) {
				$e_a = new Reader\Item\EmailAddress();
				$e_a->email = $e['email'];
				$e_a->name = $e['name'];

				$cc_emails[] = $e_a;
			}
		}

		if ($cc_emails) {
			$this->handleCc($ticket, $cc_emails);
		}

		if ($this->reader->hasProperty('email_source') && $newticket->new_message) {
			$message = $newticket->new_message;
			$message['email'] = $fwd_cutter->getUserEmailItem()->getEmail();
			$message['email_source'] = $this->reader->getProperty('email_source');

			App::getOrm()->persist($message);
			App::getOrm()->flush();
		}

		// Add attachments to users message if no agent reply
		if (!$agent_reply && $this->processBlobs() && $newticket->new_message) {
			$this->logMessage('[TicketGatewayProcessor] Adding attachments to user message');
			$message = $newticket->new_message;
			foreach ($this->processBlobs() as $blob) {
				$attach = new Entity\TicketAttachment();
				$attach['blob'] = $blob;
				$attach['person'] = $person;

				$message->addAttachment($attach);
				App::getOrm()->persist($attach);
			}

			$message->email_source = null;
			$message = null;
		}

		App::getOrm()->flush();
		App::getOrm()->commit();

		// Add agent reply if there was one
		if ($agent_reply) {


			$ticket->getTicketLogger()->recordExtra('is_fwd_reply', true);

			$this->logMessage('[TicketGatewayProcessor] Adding agent reply');
			$agent_reply = nl2br(htmlspecialchars($agent_reply, \ENT_QUOTES, 'UTF-8'));

			App::getOrm()->beginTransaction();
			$agent_message = new \Application\DeskPRO\Entity\TicketMessage();
			$agent_message->email_reader = $this->reader;
			$agent_message->person = $agent;
			$agent_message['message'] = $agent_reply;
			$ticket->addMessage($agent_message);

			if ($this->processBlobs()) {
				$this->logMessage('[TicketGatewayProcessor] Adding attachments to agent message');
				foreach ($this->processBlobs() as $blob) {
					$attach = new Entity\TicketAttachment();
					$attach['blob'] = $blob;
					$attach['person'] = $agent;

					$agent_message->addAttachment($attach);
					App::getOrm()->persist($attach);
				}
			}

			$ticket->setStatus('awaiting_user');

			App::getOrm()->persist($ticket);
			App::getOrm()->flush($ticket);
			App::getOrm()->commit();
		}

		return $ticket;
	}

	protected function runNewForwardedEmailAsAttachTicket(Entity\Person $agent, \Application\DeskPRO\EmailGateway\Reader\Item\Attachment $has_eml_attach)
	{
		$user_raw_source = $has_eml_attach->getFileContents();
		$user_reader = new \Application\DeskPRO\EmailGateway\Reader\EzcReader();
		$user_reader->setRawSource($user_raw_source);
		$user_reader->setProperty('email_source', $user_raw_source);

		$this->person = $agent;

		$this->logMessage('[TicketGatewayProcessor] Forwarded attached ticket by ' . $agent->getId() . ' ' . $agent->getDisplayContact());

		if ($this->reader->getBodyHtml() && $this->reader->getBodyHtml()->body_utf8) {
			$agent_reply = $this->reader->getBodyHtml()->body_utf8;

			$agent_reply = $this->cleaner->clean($agent_reply, 'html_email_preclean');
			$agent_reply = $this->cleaner->clean($agent_reply, 'html_email_basicclean');
			$agent_reply = $this->cleaner->clean($agent_reply, 'html_email');
			$agent_reply = $this->trimHtmlWhitespace($agent_reply);
			$agent_reply = $this->cleaner->clean($agent_reply, 'html_email_postclean');

		} else {
			$agent_reply = trim($this->reader->getBodyText()->body_utf8);
			if ($agent_reply) {
				$agent_reply = nl2br(@htmlspecialchars($agent_reply, \ENT_QUOTES, 'UTF-8'));
			}
		}

		if ($agent_reply && !trim(str_replace('&nbsp;', '', strip_tags($agent_reply)))) {
			$agent_reply = null;
		}

		#------------------------------
		# Find person
		#------------------------------

		$person_processor = new PersonFromEmailProcessor();
		$person_email_item = $user_reader->getFromAddress();

		$person = $person_processor->findPerson($person_email_item);
		if ($person) {
			$person_processor->passPerson($person_email_item, $person);
		} else {
			$person = $person_processor->createPerson($person_email_item, true);
		}

		#------------------------------
		# Create ticket
		#------------------------------

		$newticket = new \Application\DeskPRO\Tickets\NewTicket\NewTicket(
			Entity\Ticket::CREATED_GATEWAY_AGENT,
			$person
		);
		$newticket->setPersonContext($person);
		$newticket->gateway = $this->gateway;
		$newticket->gateway_address = $this->gateway_address;
		$newticket->sent_to	= $this->sent_to;

		// We do our own dupe check here
		$newticket->do_dupe_check = false;

		if ($this->logger) {
			$newticket->logger = $this->logger;
		}

		$newticket->ticket->subject = $user_reader->getSubject()->subject;

		if ($user_reader->getBodyHtml() && $user_reader->getBodyHtml()->body_utf8) {
			$body = $user_reader->getBodyHtml()->body_utf8;

			$body = $this->cleaner->clean($body, 'html_email_preclean');
			$body = $this->cleaner->clean($body, 'html_email_basicclean');
			$body = $this->cleaner->clean($body, 'html_email');
			$body = $this->trimHtmlWhitespace($body);
			$body = $this->cleaner->clean($body, 'html_email_postclean');


		} else {
			$body = nl2br(@htmlspecialchars(trim($user_reader->getBodyText()->body_utf8), \ENT_QUOTES, 'UTF-8'));
		}

		$newticket->ticket->message = $body;
		$newticket->ticket->message_is_html = true;

		$tracker_extras = array(
			'fwd_via_agent' => $agent
		);
		if ($agent->getPref("agent_notify_override.forward.email")) {
			$tracker_extras['force_notify_email'] = array($agent->id);
		}
		if ($agent->getPref("agent_notify_override.forward.alert")) {
			$tracker_extras['force_notify_alert'] = array($agent->id);
		}

		if (isset($this->reply_actions['user'])) {
			$newticket->creation_system = 'gateway.agent';
		}

		App::getOrm()->beginTransaction();
		$ticket = $newticket->save(array(), $tracker_extras);

		if (!$newticket->new_message) {
			$this->logMessage("[TicketGatewayProcessor] Found as duplicate of ticket: $ticket->id");
		}

		// Handle CC's
		$ccs = $this->reader->getCcAddresses();
		$ccs = array_merge($user_reader->getCcAddresses());

		$cc_emails = $this->reader->getDeliveredAddresses();
		$cc_emails = array_merge($cc_emails, $user_reader->getDeliveredAddresses());

		if ($ccs) {
			foreach ($ccs as $e) {
				$cc_emails[] = $e;
			}
		}

		if ($cc_emails) {
			$this->handleCc($ticket, $cc_emails);
		}

		if ($this->reader->hasProperty('email_source') && $newticket->new_message) {
			$message = $newticket->new_message;
			$message['email'] = $user_reader->getFromAddress()->email;
			$message['email_source'] = $this->reader->getProperty('email_source');

			App::getOrm()->persist($message);
			App::getOrm()->flush();
		}

		// Add attachments to users message if no agent reply
		if ($user_reader->getAttachments() && $newticket->new_message) {
			$this->logMessage('[TicketGatewayProcessor] Adding attachments to user message');
			$message = $newticket->new_message;

			foreach ($user_reader->getAttachments() as $attach) {

				$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromString(
					$attach->getFileContents(),
					$attach->getFileName(),
					$attach->getMimeType()
				);
				$blob_id = $blob->getId();

				$this->logMessage(sprintf("Processed blob %s (%d)", $blob->filename, $blob->id));

				$attach = new Entity\TicketAttachment();
				$attach['blob'] = $blob;
				$attach['person'] = $person;

				$message->addAttachment($attach);
				App::getOrm()->persist($attach);
			}

			$message->email_source = null;
			$message = null;
		}

		App::getOrm()->flush();
		App::getOrm()->commit();

		// Add agent reply if there was one
		if ($agent_reply) {
			$ticket->getTicketLogger()->recordExtra('is_fwd_reply', true);

			$this->logMessage('[TicketGatewayProcessor] Adding agent reply');

			App::getOrm()->beginTransaction();
			$agent_message = new \Application\DeskPRO\Entity\TicketMessage();
			$agent_message->email_reader = $this->reader;
			$agent_message->person = $agent;
			$agent_message->setMessageHtml($agent_reply);
			$ticket->addMessage($agent_message);

			if ($this->processBlobs()) {
				$this->logMessage('[TicketGatewayProcessor] Adding attachments to agent message');
				foreach ($this->processBlobs() as $blob) {
					if ($blob->filename == 'email.eml') {
						continue;
					}

					$attach = new Entity\TicketAttachment();
					$attach['blob'] = $blob;
					$attach['person'] = $agent;

					$agent_message->addAttachment($attach);
					App::getOrm()->persist($attach);
				}
			}

			$ticket->setStatus('awaiting_user');

			App::getOrm()->persist($ticket);
			App::getOrm()->flush($ticket);
			App::getOrm()->commit();
		}

		return $ticket;
	}

	public function replaceInlineAttachTokens($body, InlineImageTokens $inline_images)
	{
		$exist_inline_blobs = array();

		if ($this->ticket) {
			$blob_hashes = array();

			foreach ($this->processed_blobs as $blob) {
				$blob_hashes[] = $blob->blob_hash;
			}

			if ($blob_hashes) {
				$exist_attach = App::getOrm()->createQuery("
					SELECT a, b
					FROM DeskPRO:TicketAttachment a
					LEFT JOIN a.blob b
					WHERE a.ticket = ?0 AND b.blob_hash IN (?1)
				")->execute(array($this->ticket, $blob_hashes));

				foreach ($exist_attach as $a) {
					$exist_inline_blobs[$a->blob->blob_hash] = $a->blob;
				}
			}
		}

		foreach ($inline_images->getCids() as $cid) {
			if (!isset($this->processed_blobs_cid[$cid])) {
				continue;
			}

			$blob = $this->processed_blobs_cid[$cid];

			// If this ticket already has a blob like this,
			// then mark it as a dupe and rewrite the inline reference
			// to the one we've already saved
			if (isset($exist_inline_blobs[$blob->blob_hash])) {
				$this->logMessage(sprintf("Duplicate inline blob %s is being discarded, existing blob %s will be used", $blob->getFilenameSafe(), $blob->getId()));
				$this->dupe_inline_blobs[$blob->getId()] = $blob;
				$blob = $exist_inline_blobs[$blob->blob_hash];
			}

			if ($blob->isImage()) {
				$this->inline_blobs[$blob->getId()] = $blob;
				$replace = '[attach:image:' . $blob->getAuthId() . ':' . $blob->getFilenameSafe() . ']';
			} else {
				$replace = '[attach:file:' . $blob->getAuthId() . ':' . $blob->getFilenameSafe() . ']';
			}

			$body = $inline_images->replaceToken($cid, $replace, $body);
		}

		return $body;
	}

	public function getErrorCode()
	{
		return $this->error;
	}

	public function getSourceInfo()
	{
		if ($this->source_info) {
			$messages = is_array($this->source_info) ? $this->source_info : array($this->source_info);
		} else {
			$messages = array();
		}

		if (isset($this->options['logger_messages'])) {
			$messages = array_merge($messages, $this->options['logger_messages']->getMessages());
		}

		return $messages;
	}

	public function cleanBodyText($text)
	{
		if ($this->reader->isOutlookMailer()) {
			$text = \Orb\Util\Strings::standardEol($text);
			$text = str_replace("\n\n", "\n", $text);
		}

		return $text;
	}

	public function applyChangesArray(Entity\Ticket $ticket)
	{
		if (!$this->reply_actions) {
			return;
		}

		foreach ($this->reply_actions as $type => $value) {
			switch ($type) {
				case 'user':
					$ticket->person = $value;
					break;

				case 'status':
					$ticket->status = $value;
					break;

				case 'is_hold':
					$ticket->is_hold = $value;
					break;

				case 'agent':
					$ticket->agent = $value;
					break;

				case 'assign_agent':
					$ticket->agent = $value;
					break;

				case 'assign_agent_team':
					$ticket->agent = $value;
					break;

				case 'department':
					$ticket->department = $value;
					break;

				case 'product':
					$ticket->product = $value;
					break;

				case 'category':
					$ticket->category = $value;
					break;

				case 'priority':
					$ticket->priority = $value;
					break;

				case 'workflow':
					$ticket->workflow = $value;
					break;

				case 'labels':
					$ticket->getLabelManager()->setLabelsArray($value);
					break;

				case 'ticket_fields':
					$form_data = array();
					foreach ($value as $field_id => $data) {
						$form_data["field_{$field_id}"] = $data;
					}

					$field_manager = App::getSystemService('ticket_fields_manager');
					$field_manager->saveFormToObject($form_data, $ticket);
					break;
			}
		}
	}
}
