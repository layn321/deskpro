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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Form\Model;

use Application\DeskPRO\Tickets\SnippetFormatter;
use Doctrine\ORM\EntityManager;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\TicketAttachment;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Organization;
use Application\DeskPRO\App;

class NewTicket
{
	public $person;

	public $subject;
	public $notify_template = '';
	public $message;
	public $is_html_reply;
	public $department_id;
	public $status;
	public $agent_id;
	public $agent_team_id;
	public $category_id = 0;
	public $priority_id = 0;
	public $workflow_id = 0;
	public $product_id = 0;

	public $billing_type = '';
	public $billing_amount = 0;
	public $billing_hours = 0;
	public $billing_minutes = 0;
	public $billing_seconds = 0;
	public $billing_comment = '';

	public $add_cc_person = array();
	public $add_cc_newpeople = array();
	public $add_cc_newperson = array();
	public $attach = array();
	public $ticket_fields = array();

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $_em;

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	protected $_ticket;

	/**
	 * @var callable
	 */
	protected $_pre_save_callback;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $_person_context;

	/**
	 * @var \Application\DeskPRO\Entity\Ticket
	 */
	public $exist_ticket;

	protected $_blob_inline_ids = array();
	public $suppress_user_notify = false;

	public function __construct(EntityManager $em, Person $person_context)
	{
		$this->_em = $em;
		$this->_person_context = $person_context;

		$this->person = new NewTicketPerson();
	}

	public function setValuesFromTicket(Ticket $ticket)
	{
		$this->exist_ticket  = $ticket;
		$this->department_id = $ticket->getDepartmentId();
		$this->workflow_id   = $ticket->getWorkflowId();
		$this->product_id    = $ticket->getProductId();
		$this->priority_id   = $ticket->getPriorityId();
		$this->category_id   = $ticket->getCategoryId();
		$this->status        = $ticket->status;

		$field_manager = App::getSystemService('ticket_fields_manager');
		$custom_fields = $field_manager->createFormArrayForObject($ticket);

		$this->ticket_fields = $custom_fields;
	}

	/**
	 * @param callable $callback
	 */
	public function setPreSaveCallback($callback)
	{
		$this->_pre_save_callback = $callback;
	}

	/**
	 * @throws \Exception
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public function save()
	{
		$this->_em->getConnection()->beginTransaction();
		try {
			$res = $this->_save();
			$this->_em->getConnection()->commit();

			return $res;
		} catch (\Exception $e) {
			$this->_em->getConnection()->rollback();
			throw $e;
		}
	}

	public function setBlobInlineIds(array $ids)
	{
		$this->_blob_inline_ids = $ids;
	}

	protected function _save()
	{
		#------------------------------
		# The user owner
		#------------------------------

		if ($this->person->id) {
			$person = $this->_em->find('DeskPRO:Person', $this->person->id);
		} else {
			$person = App::getSystemService('UsersourceManager')->findPersonByEmail($this->person->email_address);
		}

		if (!$person) {
			$person = new Person();
			$email_obj = $person->addEmailAddressString($this->person->email_address);
			$person->primary_email = $email_obj;
		}

		if ($this->person->organization) {
			$org = $this->_em->getRepository('DeskPRO:Organization')->findOneByName($this->person->organization);
			if (!$org) {
				$org = new Organization();
				$org['name'] = $this->person->organization;
				$this->_em->persist($org);
			}
			$person->organization = $org;

			if ($this->person->organization_position) {
				$person['organization_position'] = $this->person->organization_position;
			}
		}

		if (!$person->name && $this->person->name) {
			$person->name = $this->person->name;
		}

		if ($this->person->language_id) {
			$person->setLanguageId($this->person->language_id);
		}

		$this->_em->persist($person);
		$this->_em->flush();

		#------------------------------
		# Participants
		#------------------------------

		$add_cc_peopleids = $this->add_cc_person;
		$add_cc_people = $this->add_cc_newpeople;

		foreach ($this->add_cc_newperson as $info) {
			if (empty($info['email']) || !\Orb\Validator\StringEmail::isValueValid($info['email']) || App::getSystemService('gateway_address_matcher')->isManagedAddress($info['email'])) {
				continue;
			}

			$check_exist = $this->_em->getRepository('DeskPRO:Person')->findOneByEmail($info['email']);
			if ($check_exist) {
				$add_cc_people[] = $check_exist;
			} else {
				// New person, coming right up
				$added_new = true;

				$new_cc_person = Person::newContactPerson(array(
					'email' => $info['email'],
					'name' => !empty($info['name']) ? $info['name'] : ''
				));
				$this->_em->persist($new_cc_person);

				$add_cc_people[] = $new_cc_person;
			}
		}

		foreach ($add_cc_people as $p) {
			$this->_em->persist($p);
		}

		$add_cc_people = array_merge(
			$add_cc_people,
			$this->_em->getRepository('DeskPRO:Person')->getByIds($add_cc_peopleids)
		);

		$this->_em->flush();

		#------------------------------
		# Ticket
		#------------------------------

		// Ticket props
		$ticket = new Ticket();
		$ticket['creation_system'] = Ticket::CREATED_WEB_AGENT_PORTAL;
		$ticket['language'] = $person->getRealLanguage();

		if ($this->suppress_user_notify) {
			$ticket->getTicketLogger()->recordExtra('suppress_user_notify', true);
		}

		$this->_email = $person->findEmailAddress($this->person->email_address);
		if ($this->_email) {
			$ticket->person_email = $this->_email;
		}

		$standard = array(
			'subject', 'status', 'agent_id', 'agent_team_id',
			'department_id', 'category_id', 'priority_id', 'workflow_id',
			'product_id', 'notify_template'
		);
		if (!$this->status) {
			$this->status = 'awaiting_agent';
		}

		foreach ($standard as $k) {
			$ticket[$k] = $this->$k;
		}

		if (!$ticket['notify_template']) {
			$ticket['notify_template'] = '';
		}


		$ticket->person = $person;


		#------------------------------
		# Message
		#------------------------------

		// Message
		$message = new TicketMessage();
		$message->person = $this->_person_context;
		$message->setVisitorFromRequest();

		$message_text = $this->message;
		$formatter = new SnippetFormatter(App::getContainer()->get('twig'));
		$message_text = $formatter->formatText($message_text, $ticket);

		if ($this->is_html_reply) {
			$message_text = App::get('deskpro.core.input_cleaner')->clean($message_text, 'html_core');
			$message_text = \Orb\Util\Strings::trimHtml($message_text);
			$message_text = \Orb\Util\Strings::prepareWysiwygHtml($message_text);
			$message->message = $message_text;
		} else {
			$message->setMessageText($message_text);
		}

		// Message Attachments
		foreach ($this->attach as $blob_id) {

			$blob = $this->_em->getRepository('DeskPRO:Blob')->find($blob_id);

			$attach = new TicketAttachment();
			$attach['blob'] = $blob;
			$attach['person'] = $this->_person_context;

			$message->addAttachment($attach);
			$ticket->addAttachment($attach);
		}

		foreach ($this->_blob_inline_ids as $blob_id) {
			$blob = $this->_em->getRepository('DeskPRO:Blob')->find($blob_id);

			$attach = new TicketAttachment();
			$attach['blob'] = $blob;
			$attach['person'] = $this->_person_context;
			$attach->is_inline = true;

			$message->addAttachment($attach);
			$ticket->addAttachment($attach);
		}

		$message->convertEmbeddedImagesToInlineAttach();

		$ticket->addMessage($message);

		switch ($this->billing_type) {
			case 'amount':
				$ticket->addCharge($this->_person_context, null, floatval($this->billing_amount), $this->billing_comment);
				break;

			case 'time':
				$time = (
					3600 * $this->billing_hours
					+ 60 * $this->billing_minutes
					+ $this->billing_seconds
				);
				$ticket->addCharge($this->_person_context, $time, null, $this->billing_comment);
		}

		$this->_em->persist($ticket);

		if ($this->_pre_save_callback) {
			call_user_func_array($this->_pre_save_callback, array($ticket, $message, $person));
		}

		$field_manager = App::getSystemService('ticket_fields_manager');
		$post_custom_fields = $this->ticket_fields;
		if (!empty($post_custom_fields)) {
			$field_manager->saveFormToObject($post_custom_fields, $ticket);
		}

		foreach ($add_cc_people as $add_cc_person) {
			if ($add_cc_person->getId() != $ticket->person->getId()) {
				$part = $ticket->addParticipantPerson($add_cc_person);
				if ($part) {
					$this->_em->persist($part);
				}
			}
		}

		$this->_em->flush();
		$this->_em->persist($message);
		$this->_em->flush();

		$this->_ticket = $ticket;

		return $this->_ticket;
	}


	/**
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	public function getTicket()
	{
		return $this->_ticket;
	}
}
