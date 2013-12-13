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
 * @subpackage Tickets
 */

namespace Application\DeskPRO\Tickets\TicketActions;

use Application\DeskPRO\Tickets\TicketActions\ActionInterface;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;

use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\Translate\DelegatePhrase;
use Application\DeskPRO\App;
use Orb\Util\Arrays;

/**
 * This action handles toggling email validation features,
 * and handles sending auto-response to users
 */
class NewTicketAction extends AbstractAction implements BreakableAction
{
	/**
	 * True to enable email validation on accounts that have not been validated yet.
	 *
	 * @var bool
	 */
	protected $enable_validation = false;

	/**
	 * @var string
	 */
	protected $validating_email_tpl = 'DeskPRO:emails_user:new-ticket-validate.html.twig';

	/**
	 * @var string
	 */
	protected $newticket_email_tpl = 'DeskPRO:emails_user:new-ticket.html.twig';

	/**
	 * @var string
	 */
	protected $newticket_agent_email_tpl = 'DeskPRO:emails_user:new-ticket-agent.html.twig';

	/**
	 * @var bool
	 */
	protected $enable_notify = false;

	/**
	 * @var bool
	 */
	protected $do_break = false;

	protected $op_mode = 'run';

	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;
	protected $person_context;

	public function __construct(TicketChangeTracker $tracker, $mode = 'run')
	{
		$this->tracker = $tracker;
		$this->op_mode = $mode;
	}

	/**
	 * Enable email notifications (auto-reply)
	 */
	public function enableNotifications()
	{
		$this->tracker->logMessage("[NewTicketAction] Enable notify");
		$this->enable_notify = true;
	}


	/**
	 * Disable email notifications
	 */
	public function disableNotifications()
	{
		$this->tracker->logMessage("[NewTicketAction] Disable notify");
		$this->enable_notify = false;
	}

	/**
	 * Enable email validation on new accounts
	 */
	public function enableValidation()
	{
		$this->tracker->logMessage("[NewTicketAction] enabling email validation");
		$this->enable_validation = true;
	}


	/**
	 * Disable email validation on new accounts
	 */
	public function disableValidation()
	{
		$this->tracker->logMessage("[NewTicketAction] disabling email validation");
		$this->enable_validation = false;
	}


	/**
	 * @param string $tpl
	 */
	public function setValidationEmailTemplate($tpl)
	{
		$this->validating_email_tpl = $tpl;
	}


	/**
	 * @param string $tpl
	 */
	public function setEmailTemplate($tpl, $type = '')
	{
		$this->tracker->logMessage("set template $tpl $type");
		switch ($type) {
			case 'user_new_ticket':
				$this->newticket_email_tpl = $tpl;
				break;
			case 'user_new_ticket_validate':
				$this->validating_email_tpl = $tpl;
				break;
			case 'user_new_ticket_agent':
				$this->newticket_agent_email_tpl = $tpl;
				break;
		}
	}


	/**
	 * Apply the property to the ticket
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function apply(Ticket $ticket)
	{
		if ($this->tracker->isExtraSet('force_email_validation')) {
			$this->enableValidation();
		}

		if ($this->tracker->isExtraSet('email_template_user_newticket')) {
			$this->newticket_email_tpl = $this->tracker->getExtra('email_template_user_newticket');
			$this->tracker->logMessage("[NewTicketAction] Set newticket template: " . $this->newticket_email_tpl);
		}
		if ($this->tracker->isExtraSet('email_template_user_newticket_validate')) {
			$this->validating_email_tpl = $this->tracker->getExtra('email_template_user_newticket_validate');
			$this->tracker->logMessage("[NewTicketAction] Set newticket_validate template: " . $this->validating_email_tpl);
		}
		if ($this->tracker->isExtraSet('email_template_user_newticket_agent')) {
			$this->newticket_agent_email_tpl = $this->tracker->getExtra('email_template_user_newticket_agent');
			$this->tracker->logMessage("[UserNotificationNewReplyAgent] Set newticket_agent template: " . $this->tracker->getExtra('email_template_user_newticket_agent'));
		}

		$this->tracker->logMessage("[NewTicketAction] Mode: " . $this->op_mode);

		if ($ticket->person_email_validating) {
			$validating = true;
		} elseif ($ticket->person->isNewPerson()) {
			if ($this->enable_validation) {
				$validating = true;
			} else {
				$validating = false;
			}
		} elseif (!$ticket->person->isNewPerson() && !$ticket->person->is_confirmed) {
			$validating = true;
		} else {
			$validating = false;
		}

		if ($this->op_mode == 'pre') {
			$this->op_mode = 'run';

			if ($validating) {
				$ticket->setStatus('hidden.validating');
			} elseif ($ticket->status == 'hidden' && $ticket->person->is_agent_confirmed) {
				$ticket->setStatus('awaiting_agent');
			}

			App::getOrm()->persist($ticket);
			App::getOrm()->flush();
			return;
		}

		if ($validating) {
			$this->applyValidating($ticket);
			return;
		}

		// If we dont have validation enabled on the user and they're new,
		// then mark them as not needing validation
		if ($ticket->person->isNewPerson()) {
			$ticket->person->is_confirmed = true;
			App::getOrm()->persist($ticket->person);

			$primary_email = $ticket->person->primary_email;
			if ($primary_email) {
				$primary_email->is_validated = true;
				App::getOrm()->persist($primary_email);
			}

			App::getOrm()->flush();
		}

		#------------------------------
		# If we havent disabled notifications,
		# send the auto-reply
		#------------------------------

		$person = $ticket->person;

		if ($this->enable_notify && !$this->tracker->isExtraSet('suppress_user_notify')) {
			if (!$person->getPrimaryEmailAddress()) {
				$this->tracker->logMessage("[NewTicketAction] User has no primary email address, setting enable_notify=0");
				$this->enable_notify = false;
			} elseif ($person->disable_autoresponses && !$ticket->isAgentCreated()) {
				$this->tracker->logMessage("[NewTicketAction] User has disable_autoresponses enabled, setting enable_notify=0");
				$this->enable_notify = false;

				$change_info = array(
					'type'    => 'free',
					'message' => 'User notification disbaled because user is set as an auto-responder',
				);
				$this->tracker->recordMultiPropertyChanged('log_actions', null, $change_info);
			}
		}

		if ($this->enable_notify && !$this->tracker->isExtraSet('suppress_user_notify')) {

			if ($ticket->isAgentCreated()) {
				$tpl = $this->newticket_agent_email_tpl;
			} else {
				$tpl = $this->newticket_email_tpl;
			}

			$parts = $ticket->getUserParticipants();

			$from_address = $this->getFromAddress($ticket);

			$this->tracker->logMessage("[NewTicketAction] Sending email template: " . $tpl);
			$this->tracker->logMessage("[NewTicketAction] Sending email to: " . $person->getEmailAddress());
			$this->tracker->logMessage("[NewTicketAction] Sending email from: " . print_r($from_address,true));

			$change_info = array(
				'type'        => 'user_notify',
				'notify_type' => $ticket->isAgentCreated() ? 'newticket_agent' : 'newticket',
				'emailed'     => array($ticket->person),
				'cced'        => $parts ?: array(),
				'from_name'   => Arrays::getFirstItem($from_address),
				'from_email'  => Arrays::getFirstKey($from_address)
			);
			$this->tracker->recordMultiPropertyChanged('log_actions', null, $change_info);

			$vars = array(
				'ticket' => $ticket,
				'person' => $person,
			);

			$messages = App::getEntityRepository('DeskPRO:TicketMessage')->getTicketMessages($ticket,array(
				'limit' => 25,
				'order' => 'DESC',
				'with_notes' => false
			));
			$vars['messages'] = $messages;

			if ($ticket->creation_system == Ticket::CREATED_WEB_AGENT) {
				// First message is the name we'll send it from
				$first = \Orb\Util\Arrays::getFirstItem($messages);

				if ($first) {
					$from_address = array_keys($from_address);
					$from_address = $from_address[0];

					$from_address = array($from_address => $first->person->getDisplayName());
				}
			}

			$first = \Orb\Util\Arrays::getFirstItem($messages);

			$ticketdisplay = new \Application\DeskPRO\Tickets\TicketDisplay($ticket, $person);
			$vars['ticketdisplay'] = $ticketdisplay;

			$attach_attachments = array();
			if ($first && $ticket->isAgentCreated()) {
				$max = App::getSetting('core.sendemail_attach_maxsize');
				$max_embed = App::getSetting('core.sendemail_embed_maxsize');
				$size = 0;
				$attachments = $ticketdisplay->getMessageAttachments($first, true);
				if ($attachments) {
					foreach ($ticketdisplay->getMessageAttachments($first, true) as $attach) {
						if ($attach->is_inline && $attach->blob->filesize > $max_embed) {
							continue;
						}

						if ($size + $attach->blob->filesize > $max) {
							break;
						}

						$attach_attachments[$attach->blob->getDownloadUrl(true)] = $attach;
					}
				}

				foreach ($first->getUsedSignatureImageBlobs() AS $blob) {
					if ($blob->filesize > $max_embed) {
						continue;
					}

					if ($size + $blob->filesize > $max) {
						break;
					}

					$attach_attachments[$blob->getDownloadUrl(true)] = $blob;
				}
			}

			$vars['tracking_object'] = $ticket;

			App::getTranslator()->setTemporaryLanguage($ticket->getLanguage(), function($tr, $lang) use ($tpl, $vars, $from_address, $ticket, $person, $parts, $attach_attachments) {
				$message = App::getMailer()->createMessage();
				$message->setContextId('ticket_gateway');
				$message->setTemplate($tpl, $vars);

				$message->setTo($ticket->getPersonEmailAddress(), $person->getDisplayName());
				foreach ($parts as $part) {
					if ($part['email_address']) {
						$message->addCc($part['email_address'], $part->person->getDisplayName());
					}
				}
				$message->setFrom($from_address);
				$message->getHeaders()->get('Message-ID')->setId($ticket->getUniqueEmailMessageId());
				$message->getHeaders()->addIdHeader('References', $ticket->getEmailReferencesHeader());

				if (!$ticket->isAgentCreated()) {
					$message->getHeaders()->addTextHeader('X-DeskPRO-Auto', 'Yes');
					$message->setSuppressAutoreplies(true);
				}

				if ($attach_attachments) {
					foreach ($attach_attachments as $src => $attach) {
						if ($attach instanceof \Application\DeskPRO\Entity\Blob) {
							// signature image being attached
							$message->attachBlob($attach, $src, true);
						} else {
							$message->attachBlob($attach->blob, $src, $attach->is_inline);
						}
					}
				}

				App::getMailer()->send($message);
			});
		} else {
			$this->tracker->logMessage("[NewTicketAction] No notification");
		}
	}


	/**
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function applyValidating(Ticket $ticket)
	{
		// Signal to stop processing triggers
		$this->do_break = true;

		$tpl          = $this->validating_email_tpl;
		$person       = $ticket->person;
		$from_address = $this->getFromAddress($ticket);

		$this->tracker->logMessage("[NewTicketAction] Sending $tpl " . $tpl);

		$vars = array(
			'ticket' => $ticket,
			'person' => $person,
		);

		$messages = App::getEntityRepository('DeskPRO:TicketMessage')->getTicketMessages($ticket,array(
			'limit' => 25,
			'order' => 'DESC',
			'with_notes' => false
		));
		$vars['messages'] = $messages;

		App::getTranslator()->setTemporaryLanguage($person->getLanguage(), function($tr, $lang) use ($tpl, $vars, $from_address, $ticket, $person) {
			$message = App::getMailer()->createMessage();
			$message->setContextId('ticket_gateway');
			$message->setTemplate($tpl, $vars);

			if ($ticket->person_email_validating) {
				$message->setTo($ticket->person_email_validating->email, $person->getDisplayName());
			} else {
				$message->setTo($ticket->person_email->email, $person->getDisplayName());
			}

			$message->setFrom($from_address);
			$message->getHeaders()->get('Message-ID')->setId($ticket->getUniqueEmailMessageId());
			$message->getHeaders()->addTextHeader('X-DeskPRO-Auto', 'Yes');

			App::getMailer()->send($message);
		});
	}

	public function shouldBreakAction()
	{
		return $this->do_break;
	}

	/**
	 * @return string
	 */
	public function getFromAddress(Ticket $ticket)
	{
		$info = $ticket->getFromAddress('user', array(
			'default_from' => $this->tracker->isExtraSet('set_initial_from_touser') ? $this->tracker->getExtra('set_initial_from_touser') : null
		));
		return array($info['email'] => $info['name']);
	}


	/**
	 * @return string
	 */
	public function getDescription($as_html = true)
	{
		return '';
	}

	public function merge(ActionInterface $other_action)
	{
		return null;
	}
}
