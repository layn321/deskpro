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
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

use Application\DeskPRO\Email\TicketUtil;
use Application\DeskPRO\Tickets\TicketChangeTracker;
use Application\DeskPRO\App;
use Orb\Util\Arrays;

/**
 * Sets agent
 */
abstract class AbstractUserNotificationAction extends AbstractAction
{
	/**
	 * @var \Application\DeskPRO\Tickets\TicketChangeTracker
	 */
	protected $tracker;
	protected $person_context;
	protected $email_sets = array();
	protected $via_message;

	public function __construct(TicketChangeTracker $tracker)
	{
		$this->tracker = $tracker;
	}

	public function getFromAddress(Ticket $ticket)
	{
		$info = $ticket->getFromAddress('user', array(
			'default_from' => $this->tracker->isExtraSet('set_initial_from_touser') ? $this->tracker->getExtra('set_initial_from_touser') : null
		));
		return array($info['email'] => $info['name']);
	}

	public function setEmailTemplate($tpl, $tpl_type)
	{
		$this->email_sets[$tpl_type] = $tpl;
	}

	public function getTemplate($tpl_type, $default)
	{
		return isset($this->email_sets[$tpl_type]) ? $this->email_sets[$tpl_type] : $default;
	}

	protected function doSend($tpl, $vars, Ticket $ticket, &$change_info = array())
	{
		$person = $ticket->person;

		$parts  = $ticket->getUserParticipants();

		$from_address = $this->getFromAddress($ticket);

		$change_info['emailed']    = array($person);
		$change_info['cced']       = $parts;
		$change_info['from_name']  = Arrays::getFirstItem($from_address);
		$change_info['from_email'] = Arrays::getFirstKey($from_address);

		// Is null if not provided,
		// or an array of people ID's if provided (from agent reply)
		$only_cc_ids = $this->tracker->getExtra('enabled_cc');
		$only_cc_ids = null;

		$vars['ticket'] = $ticket;
		$vars['person'] = $person;
		$vars['participants'] = $parts;
		$vars['access_code'] = $ticket->getAccessCode();

		$ticketdisplay = new \Application\DeskPRO\Tickets\TicketDisplay($ticket, $person);
		$ticketdisplay->setPersonContext($person, 'user');
		$vars['ticketdisplay'] = $ticketdisplay;
		$vars['messages']      = array_reverse($ticketdisplay->getMessages(), true);

		if ($this->via_message) {
			$has = false;
			foreach ($vars['messages'] as $m) {
				if ($m->getId() == $this->via_message->getId()) {
					$has = true;
					break;
				}
			}
			if (!$has) {
				array_unshift($vars['messages'], $this->via_message);
			}

			$vars['tracking_object'] = $this->via_message;
		} else {
			$vars['tracking_object'] = $ticket;
		}

		$attach_attachments = array();
		if (!$this->tracker->isExtraSet('is_user_reply') && $this->via_message) {
			$max = App::getSetting('core.sendemail_attach_maxsize');
			$max_embed = App::getSetting('core.sendemail_embed_maxsize');
			$size = 0;
			$attachments = $ticketdisplay->getMessageAttachments($this->via_message, true);
			if ($attachments) {
				foreach ($ticketdisplay->getMessageAttachments($this->via_message, true) as $attach) {
					if ($attach->is_inline && $attach->blob->filesize > $max_embed) {
						continue;
					}

					if ($size + $attach->blob->filesize > $max) {
						break;
					}

					$attach_attachments[$attach->blob->getDownloadUrl(true)] = $attach;
				}
			}

			foreach ($this->via_message->getUsedSignatureImageBlobs() AS $blob) {
				if ($blob->filesize > $max_embed) {
					continue;
				}

				if ($size + $blob->filesize > $max) {
					break;
				}

				$attach_attachments[$blob->getDownloadUrl(true)] = $blob;
			}
		}

		if (!$person->getPrimaryEmailAddress() && !isset($vars['validating_email'])) {
			$vars['validating_email'] = App::getEntityRepository('DeskPRO:PersonEmailValidating')->getForPerson($person);
			if (!$vars['validating_email']) {
				return;
			}
		}

		App::getTranslator()->setTemporaryLanguage($ticket->getLanguage(), function($tr, $lang) use ($tpl, $vars, $from_address, $ticket, $person, $parts, $only_cc_ids, $attach_attachments) {

			$message = App::getMailer()->createMessage();
			$message->setContextId('ticket_gateway');
			$message->setTemplate($tpl, $vars);

			if (empty($vars['skip_person_id']) || $vars['skip_person_id'] != $person->getId()) {
				if (!empty($vars['validating_email'])) {
					$message->setTo($vars['validating_email']->getEmail());
				} else {
					$message->setTo($ticket->getPersonEmailAddress(), $person->getDisplayName());
				}
			}

			foreach ($parts as $part) {
				if (!empty($vars['skip_person_id']) && $vars['skip_person_id'] == $part->person->getId()) {
					continue;
				}

				if ($part['email_address']) {
					if (!$message->getTo()) {
						$message->setTo($part['email_address'], $part->person->getDisplayName());
					} else {
						$message->addCc($part['email_address'], $part->person->getDisplayName());
					}
				}
			}

			if (!$message->getTo()) {
				// no one to send to
				return;
			}

			$message->setFrom($from_address);
			$message->getHeaders()->get('Message-ID')->setId($ticket->getUniqueEmailMessageId());
			$message->getHeaders()->addIdHeader('References', $ticket->getEmailReferencesHeader());

			if (isset($vars['is_auto']) && $vars['is_auto']) {
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
	}

	/**
	 * @param \Application\DeskPRO\Tickets\TicketActions\ActionInterface $other_action
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionInterface
	 */
	public function merge(ActionInterface $other_action)
	{
		return new static($this->tracker);
	}
}
