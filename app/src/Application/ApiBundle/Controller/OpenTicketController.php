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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;

use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\TicketMessage;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\App;


class OpenTicketController extends AbstractController
{
	public function preAction($action, $arguments = null)
	{
		return null;
	}

	public function newTicketMessageAction()
	{
		if (!\Orb\Validator\StringEmail::isValueValid($this->in->getString('email')) || App::getSystemService('gateway_address_matcher')->isManagedAddress($this->in->getString('email'))) {
			return $this->createApiErrorResponse('invalid_email', 'The email address supplied is invalid');
		}
		if (!$this->in->getString('subject')) {
			return $this->createApiErrorResponse('invalid_subject', 'The subject was empty');
		}
		if (!$this->in->getString('message')) {
			return $this->createApiErrorResponse('invalid_message', 'The message was empty');
		}

		if ($tac = $this->in->getString('tac')) {
			$ticket = $this->em->getRepository('DeskPRO:Ticket')->getByAccessCode($tac);

			if (!$ticket || !$ticket->getProperty('allow_send_reply_service')) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}

			$person = App::getOrm()->getRepository('DeskPRO:Person')->findOneByEmail($this->in->getString('email'));
			if (!$person) {
				$person = Person::newContactPerson(array(
					'email' => $this->in->getString('email'),
					'name'  => $this->in->getString('name')
				));
				$this->em->persist($person);
			}

		} else {
			// Not allowed to create new tickets using this service
			if (!$this->apikey && !$this->api_token && !dp_get_config('allow_open_ticket_create')) {
				$response = $this->createApiErrorResponse('invalid_auth', 'Please provide a valid API key or token', 401);
				$response->headers->add(array(
					'WWW-Authenticate' => 'Basic realm="API"'
				));

				return $response;
			}

			$person = App::getOrm()->getRepository('DeskPRO:Person')->findOneByEmail($this->in->getString('email'));
			if (!$person) {
				$person = Person::newContactPerson(array(
					'email' => $this->in->getString('email'),
					'name'  => $this->in->getString('name')
				));
				$this->em->persist($person);
			}

			$ticket = new Ticket();
			$ticket['creation_system']  = Ticket::CREATED_WEB_API;
			$ticket['person']  = $person;
			$ticket['subject'] = $this->in->getString('subject');
			$ticket->setProperty('allow_send_reply_service', true);

			$ticket->getTicketLogger()->recordExtra('suppress_user_notify', true);
			$ticket->getTicketLogger()->recordExtra('suppress_agent_notify', true);

			if ($my_tac = $this->in->getString('my_tac')) {
				$ticket->setProperty('send_reply_tac', $my_tac);
			}
			if ($reply_service_url = $this->in->getString('my_reply_service')) {
				$ticket->setProperty('send_reply_service', $reply_service_url);
			}
		}

		$message_html = $this->in->getHtmlCore('message');

		$ticket_message = new TicketMessage();
		$ticket_message['person']  = $person;
		$ticket_message->setMessageHtml($message_html);

		$ticket->addMessage($ticket_message);
		$ticket['status'] = 'awaiting_agent';

		$this->em->persist($ticket);
		$this->em->persist($ticket_message);

		$this->em->flush();

		return $this->createJsonResponse(array(
			'success' => true,
			'tac' => $ticket->getAccessCode()
		));
	}
}