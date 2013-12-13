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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;

use Application\UserBundle\Form\EditTicketType;
use Application\UserBundle\Form\NewTicketReplyType;
use Application\UserBundle\Form\NewTicketParticipantType;

class TicketsController extends AbstractController
{
	protected $limited_person = null;

	protected $session_allowed = array();

	protected function init()
	{
		parent::init();

		if ($this->session->get('ticket_access')) {
			$this->session_allowed = $this->session->get('ticket_access');
			App::setSkipCache(true);
		}
	}

	################################################################################
	# list
	################################################################################

	/**
	 * View a list of all tickets
	 */
    public function listAction()
    {
		if (!$this->person['id']) {
			$return = $this->request->getRequestUri();
			$redirect_url = $this->get('router')->generate('user_login', array('return' => $return));
			return $this->redirect($redirect_url);
		}

		$dql_join = '';
		$sort = $this->in->getString('sort');
		switch ($sort) {
			case 'department':
				$dql_join = 'LEFT JOIN ticket.department d'
					. "\nLEFT JOIN d.parent d_parent";
				$sort_dql = 'd_parent.display_order, d.display_order, ticket.id DESC';
				break;

			case 'last_reply':
				$sort_dql = 'ticket.date_last_user_reply DESC';
				break;

			case 'date_created':
			default:
				$sort = 'date_created';
				$sort_dql = 'ticket.id DESC';
		}

		if ($this->person->is_agent) {
			$tickets = $this->em->createQuery("
				SELECT ticket
				FROM DeskPRO:Ticket ticket
				$dql_join
				WHERE ticket.person = :person AND ticket.status != 'hidden'
				ORDER BY $sort_dql
			")->execute(array('person' => $this->person));
		} else {
			if ($this->person->organization && $this->person->organization_manager) {
				// Managers can always see their org tickets, so dont show them
				// tickets if they are of their own org because those will be on the org page
				$tickets = $this->em->createQuery("
					SELECT ticket
					FROM DeskPRO:Ticket ticket
					LEFT JOIN ticket.participants part
					$dql_join
					WHERE (ticket.person = :person OR (part.person = :person AND ticket.organization != :org)) AND ticket.status != 'hidden'
					ORDER BY $sort_dql
				")->execute(array('person' => $this->person, 'org' => $this->person->organization));
			} else {
				$tickets = $this->em->createQuery("
					SELECT ticket
					FROM DeskPRO:Ticket ticket
					LEFT JOIN ticket.participants part
					$dql_join
					WHERE (ticket.person = :person OR part.person = :person) AND ticket.status != 'hidden'
					ORDER BY $sort_dql
				")->execute(array('person' => $this->person));
			}
		}

		$active_tickets   = array();
		$resolved_tickets = array();

		$ticket_ids = array();

		foreach ($tickets as $t) {
			$ticket_ids[] = $t['id'];
			if ($t['status'] == 'awaiting_agent' OR $t['status'] == 'awaiting_user') {
				$active_tickets[] = $t;
			} else {
				$resolved_tickets[] = $t;
			}
		}

		$ticket_ids = implode(',', $ticket_ids);

		$last_messages = array();
		if ($tickets) {
			$last_mesasge_ids = App::getDb()->fetchAllCol("
				SELECT MAX(id)
				FROM tickets_messages
				WHERE ticket_id IN ($ticket_ids)
				GROUP BY ticket_id
			");
			$last_mesasge_ids = implode(',', $last_mesasge_ids);
			if ($last_mesasge_ids) {
				$last_messages = $this->em->createQuery("
					SELECT m, p
					FROM DeskPRO:TicketMessage m
					LEFT JOIN m.person p
					WHERE m.id IN ($last_mesasge_ids)
					GROUP BY m.ticket
					ORDER BY m.id DESC
				")->execute();
			}

			$last_messages = Arrays::keyFromData($last_messages, 'ticket_id');
		}

        return $this->render('UserBundle:Tickets:list.html.twig', array(
			'active_tickets'   => $active_tickets,
			'resolved_tickets' => $resolved_tickets,
			'last_messages'    => $last_messages,
			'sort'             => $sort,
		));
    }

	/**
	 * View a list of all organization tickets
	 */
    public function listOrganizationAction()
    {
		if (!$this->person->organization || !$this->person->organization_manager) {
			return $this->redirectRoute('user_tickets');
		}

		$allowed_ids = $this->person->getPermissionsManager()->Departments->getAllowedIds('tickets');
		if (!$allowed_ids) {
			return $this->redirectRoute('user_tickets');
		}

		$dql_join = '';
		$sort = $this->in->getString('sort');
		switch ($sort) {
			case 'creator':
				$dql_join = 'INNER JOIN ticket.person p';
				$sort_dql = 'p.last_name, p.first_name, ticket.id DESC';
				break;

			case 'department':
				$dql_join = 'LEFT JOIN ticket.department d'
					. "\nLEFT JOIN d.parent d_parent";
				$sort_dql = 'd_parent.display_order, d.display_order, ticket.id DESC';
				break;

			case 'last_reply':
				$sort_dql = 'ticket.date_last_user_reply DESC';
				break;

			case 'date_created':
			default:
				$sort = 'date_created';
				$sort_dql = 'ticket.id DESC';
		}

		$tickets = $this->em->createQuery("
			SELECT ticket
			FROM DeskPRO:Ticket ticket
			$dql_join
			WHERE ticket.organization = :organization AND ticket.status != 'hidden'
			ORDER BY $sort_dql
		")->execute(array('organization' => $this->person->organization));

		$active_tickets   = array();
		$resolved_tickets = array();

		$ticket_ids = array();

		foreach ($tickets as $t) {
			$ticket_ids[] = $t['id'];
			if ($t['status'] == 'awaiting_agent' OR $t['status'] == 'awaiting_user') {
				$active_tickets[] = $t;
			} else {
				$resolved_tickets[] = $t;
			}
		}

		$ticket_ids = implode(',', $ticket_ids);

		$last_messages = array();
		if ($tickets) {
			$last_mesasge_ids = App::getDb()->fetchAllCol("
				SELECT MAX(id)
				FROM tickets_messages
				WHERE ticket_id IN ($ticket_ids)
				GROUP BY ticket_id
			");
			$last_mesasge_ids = implode(',', $last_mesasge_ids);
			if ($last_mesasge_ids) {
				$last_messages = $this->em->createQuery("
					SELECT m, p
					FROM DeskPRO:TicketMessage m
					LEFT JOIN m.person p
					WHERE m.id IN ($last_mesasge_ids)
					GROUP BY m.ticket
					ORDER BY m.id DESC
				")->execute();
			}

			$last_messages = Arrays::keyFromData($last_messages, 'ticket_id');
		}

        return $this->render('UserBundle:Tickets:list-organization.html.twig', array(
			'organization'     => $this->person->organization,
			'sort'             => $sort,
			'active_tickets'   => $active_tickets,
			'resolved_tickets' => $resolved_tickets,
			'last_messages'    => $last_messages
		));
    }


	################################################################################
	# add-reply
	################################################################################

	/**
	 * Only posted forms get here. The actual form is on the viewtikcet page.
	 *
	 * @param  $ticket_ref
	 */
	public function addReplyAction($ticket_ref)
	{
		$ticket = $this->getTicketOr404($ticket_ref);

		if (!in_array($ticket->status, array('awaiting_agent', 'awaiting_user', 'resolved'))) {
			return $this->renderLoginOrPermissionError();
		}

		if ($ticket->status == 'resolved' && !$this->person->hasPerm('tickets.reopen_resolved')) {
			return $this->renderLoginOrPermissionError();
		}

		$newreply = new \Application\UserBundle\Tickets\NewReply($ticket, $this->person);
		$form = $this->get('form.factory')->create(new NewTicketReplyType(), $newreply);
		$validator = new \Application\UserBundle\Validator\NewTicketReplyValidator();

		$form->bindRequest($this->get('request'));

		$newreply->attach_ids = $this->in->getCleanValueArray('attach_ids', 'string', 'discard');
		$newreply->attach_ids_authed = true;

		if ($validator->isValid($newreply)) {
			$newreply->save();

			$ticket_message = $newreply->getNewMessage();

			App::setSkipCache(true);
		} else {
			$errors = $validator->getErrors(true);
			$error_fields = $validator->getErrorGroups(true);

			return $this->forward('UserBundle:TicketView:load', array(
				'ticket_ref' => $ticket->getPublicId(),
				'display_data' => array(
					'errors' => $errors,
					'error_fields' => $error_fields,
					'newreply' => $newreply,
					'newreply_form' => $form
				)
			));
		}

		return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket->getPublicId()));
	}

	################################################################################
	# manage-participants
	################################################################################

	public function manageParticipantsAction($ticket_ref)
	{
		$ticket = $this->getTicketOr404($ticket_ref);

		$newpart_form = $this->get('form.factory')->create(new NewTicketParticipantType());

		return $this->render('UserBundle:Tickets:manage-participants.html.twig', array(
			'ticket' => $ticket,
			'newpart_form' => $newpart_form->createView()
		));
	}

	################################################################################
	# add-participant
	################################################################################

	public function addParticipantAction($ticket_ref)
	{
		$ticket = $this->getTicketOr404($ticket_ref);

		$newpart = new \Application\UserBundle\Tickets\NewParticipant(
			$ticket
		);

		$newpart_form = $this->get('form.factory')->create(new NewTicketParticipantType(), $newpart);

		if ($this->get('request')->getMethod() == 'POST') {
			$newpart_form->bindRequest($this->get('request'));

			if ($newpart_form->isValid()) {
				$newpart->save();

				App::setSkipCache(true);
			}
		}

		return $this->redirectRoute('user_tickets_participants', array('ticket_ref' => $ticket['ref']));
	}

	################################################################################
	# remove-participant
	################################################################################

	public function removeParticipantAction($ticket_ref, $person_id)
	{
		$ticket = $this->getTicketOr404($ticket_ref);

		$ticket->removeParticipant($person_id);

		$this->em->transactional(function($em) use ($ticket) {
			$em->persist($ticket);
			$em->flush();
		});

		App::setSkipCache(true);

		return $this->redirectRoute('user_tickets_participants', array('ticket_ref' => $ticket['ref']));
	}

	################################################################################
	# feedback
	################################################################################

	public function feedbackAction($ticket_ref, $auth, $message_id)
	{
		$ticket = $this->em->getRepository('DeskPRO:Ticket')->getTicketByPublicId($ticket_ref);
		if (!$ticket) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$message = $this->em->find('DeskPRO:TicketMessage', $message_id);
		$person  = $this->person->getId() ? $this->person : $ticket->person;

		// Verify ticket and message
		if ($auth != $ticket->auth OR !$message OR $message['ticket_id'] != $ticket['id'] OR $message['is_agent_note'] OR !$message['person']['is_agent']) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Invalid message");
		}

		$feedback = $this->em->getRepository('DeskPRO:TicketFeedback')->getFeedback($message, $person, true);

		$rating = null;
		$setrating = false;
		if ($this->container->getIn()->checkIsset('rating')) {
			$rating = $this->in->getInt('rating');
		} elseif ($this->container->getIn()->checkIsset('setrating')) {
			$rating = $this->in->getInt('setrating');
			$setrating = true;
		}

		if ($rating !== null) {
			$feedback->setRating($rating);

			if ($this->in->getBool('save')) {
				$last_message_id = App::getDb()->fetchColumn("
					SELECT message_id FROM ticket_feedback
					WHERE ticket_id = ?
					ORDER BY message_id DESC
					LIMIT 1
				", array($ticket->getId()));

				if (!$last_message_id || $message->getId() >= $last_message_id) {
					$ticket->feedback_rating = $feedback->rating;
					$this->em->persist($ticket);
				}

				$this->em->persist($feedback);
				$this->em->flush();

				App::setSkipCache(true);

				// AJAX request used to auto-save rating as soon as user clicked link
				if ($this->request->isXmlHttpRequest()) {
					return $this->createJsonResponse(array('success' => true));
				}
			}
		}

		return $this->render('UserBundle:Tickets:feedback.html.twig', array(
			'ticket'      => $ticket,
			'message'     => $message,
			'feedback'    => $feedback,
			'is_resolved' => $this->in->getBool('resolved'),
			'setrating'   => $setrating,
		));
	}

	public function feedbackSaveAction($ticket_ref, $auth, $message_id)
	{
		$ticket = $this->em->getRepository('DeskPRO:Ticket')->getTicketByPublicId($ticket_ref);
		if (!$ticket) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$message = $this->em->find('DeskPRO:TicketMessage', $message_id);
		$person  = $this->person->getId() ? $this->person : $ticket->person;

		// Verify ticket and message
		if ($auth != $ticket->auth OR !$message OR $message['ticket_id'] != $ticket['id'] OR $message['is_agent_note'] OR !$message['person']['is_agent']) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Invalid message");
		}

		$feedback = $this->em->getRepository('DeskPRO:TicketFeedback')->getFeedback($message, $person, true);
		$feedback->message = $this->in->getString('message');
		$feedback->setRating($this->in->getInt('rating'));
		$this->em->persist($feedback);

		$last_message_id = App::getDb()->fetchColumn("
			SELECT message_id FROM ticket_feedback
			WHERE ticket_id = ?
			ORDER BY message_id DESC
			LIMIT 1
		", array($ticket->getId()));

		if (!$last_message_id || $message->getId() >= $last_message_id) {
			$ticket->feedback_rating = $feedback->rating;
			$this->em->persist($ticket);
		}

		$this->em->transactional(function($em) use ($feedback) {
			$em->flush();
		});

		App::setSkipCache(true);

		return $this->render('UserBundle:Tickets:feedback-thank.html.twig', array(
			'ticket' => $ticket,
			'message' => $message,
			'feedback' => $feedback,
		));
	}

	public function feedbackCloseTicketAction($ticket_ref, $message_id)
	{
		/** @var $ticket \Application\DeskPRO\Entity\Ticket */
		$ticket = $this->em->getRepository('DeskPRO:Ticket')->getTicketByPublicId($ticket_ref);
		if (!$ticket) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$message = $this->em->find('DeskPRO:TicketMessage', $message_id);
		$person  = $this->person->getId() ? $this->person : $ticket->person;

		// Verify ticket and message
		if (!$message OR $message['ticket_id'] != $ticket['id'] OR $message['is_agent_note'] OR !$message['person']['is_agent']) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Invalid message");
		}

		$feedback = $this->em->getRepository('DeskPRO:TicketFeedback')->getFeedback($message, $person, false);

		if (!$feedback) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (!$this->person->getId()) {
			$ticket->getTicketLogger()->recordExtra('person_performer', $ticket->person);
		}

		$ticket->setStatus(Entity\Ticket::STATUS_RESOLVED);

		$this->em->transactional(function($em) use ($ticket) {
			$em->persist($ticket);
			$em->flush();
		});

		App::setSkipCache(true);

		return $this->render('UserBundle:Tickets:feedback-close.html.twig', array(
			'ticket' => $ticket,
			'message' => $message,
			'feedback' => $feedback,
			'close_window' => $this->in->getBool('close_win')
		));
	}

	public function resolveAction($ticket_ref)
	{
		$ticket  = $this->getTicketOr404($ticket_ref);
		$message = $this->em->getRepository('DeskPRO:TicketMessage')->getLastAgentReply($ticket);

		if ($this->in->getBool('process')) {
			$ticket->setStatus('resolved');

			$this->em->transactional(function ($em) use ($ticket) {
				$em->persist($ticket);
				$em->flush();
			});

			App::setSkipCache(true);

			$ticket_message = null;
			if (!$ticket->date_feedback_rating) {
				$ticket_message = App::getOrm()->createQuery("
					SELECT m
					FROM DeskPRO:TicketMessage m
					LEFT JOIN m.person person
					WHERE m.is_agent_note = false AND person.is_agent = true AND m.ticket = ?0
					ORDER BY m.id DESC
				")->setParameter(0, $ticket)->setMaxResults(1)->getOneOrNullResult();
			}

			if (App::getSetting('core.tickets.enable_feedback') && !$ticket->date_feedback_rating && $ticket_message) {
				return $this->redirectRoute('user_tickets_feedback', array('resolved' => 1, 'ticket_ref' => $ticket->getPublicId(), 'auth' => $ticket->getAuth(), 'message_id' => $ticket_message->getId()));
			} else {
				return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket->getPublicId()));
			}
		}

		return $this->render('UserBundle:Tickets:resolve.html.twig', array(
			'ticket' => $ticket,
			'message' => $message,
		));
	}

	public function unresolveAction($ticket_ref)
	{
		$ticket  = $this->getTicketOr404($ticket_ref);

		if ($ticket->status != 'resolved' || !$this->person->hasPerm('tickets.reopen_resolved')) {
			return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket->getPublicId()));
		}

		$ticket->setStatus('awaiting_agent');
		$this->em->persist($ticket);
		$this->em->flush();

		return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket->getPublicId()));
	}


	/**
	 * @return \Application\DeskPRO\Entity\Ticket
	 */
	protected function getTicketOr404($ticket_ref, $authcode = null)
	{
		$ticket = null;
		if (ctype_digit($ticket_ref)) {
			$ticket = $this->em->getRepository('DeskPRO:Ticket')->findOneById($ticket_ref);
		}

		if (!$ticket) {
			$ticket = $this->em->getRepository('DeskPRO:Ticket')->findOneByRef($ticket_ref);
		}

		if (!$ticket) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no ticket with ID $ticket_ref");
		}

		/** @var $ticket \Application\DeskPRO\Entity\Ticket */

		$is_participant = ($this->person->id == $ticket->person->id || $ticket->hasParticipantPerson($this->person->id));
		$is_org_manager = (
			$ticket->organization
			&& $this->person->organization
			&& $ticket->organization->id == $this->person->organization->id
			&& $this->person->organization_manager
		);

		if (!$is_participant AND !$is_org_manager AND !isset($this->session_allowed[$ticket['id']])) {
			throw $this->createNotFoundException();
		}

		if (isset($this->session_allowed[$ticket['id']])) {
			$person = $this->em->getRepository('DeskPRO:Person')->find($this->session_allowed[$ticket['id']]['person_id']);

			// Set the current person context
			if ($person['is_user'] AND $this->person != $person) {
				$this->person = $person;
				App::setCurrentPerson($person);
			} else {
				$this->limited_person = $person;
			}
		}

		return $ticket;
	}
}
