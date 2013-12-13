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

use Application\UserBundle\Form\NewTicketType;

class NewTicketController extends AbstractController
{
	################################################################################
	# new-ticket
	################################################################################

	/**
	 * Create a new ticket
	 */
    public function newAction($format = 'normal', $for_department_id = 0)
    {
		if (!$this->person->hasPerm('core.tickets_submit_check')) {
			return $this->renderLoginOrPermissionError($this->generateUrl('user_tickets_new'));
		}

		$interface = Entity\Ticket::CREATED_WEB_PERSON_PORTAL;
		if ($format == 'iframe') {
			$interface = Entity\Ticket::CREATED_WEB_PERSON_EMBED;
		}

		$website_url = App::getRequest()->getUri();
		if (!empty($GLOBALS['DP_WEBSITE_URL'])) {
			$website_url = $GLOBALS['DP_WEBSITE_URL'];
		}

		$newticket = new \Application\DeskPRO\Tickets\NewTicket\NewTicket(
			$interface,
			$this->person
		);
		$newticket->setPersonContext($this->person);

		$hide_name_field  = false;
		$hide_email_field = false;
		if ($this->in->getString('default_user_name')) {
			$newticket->person->name = $this->in->getString('default_user_name');
			$hide_name_field = true;
		}
		if ($this->in->getString('default_user_email')) {
			$newticket->person->email = $this->in->getString('default_user_email');
			$hide_email_field = true;
		}

		if ($website_url) {
			$newticket->creation_system_option = $website_url;
		}

		if ($this->search_query && !$this->request->isPost()) {
			$newticket->ticket->subject = $this->search_query;
		}

		$newticket_formtype = new NewTicketType($this->person);
		$form = $this->get('form.factory')->create($newticket_formtype, $newticket);

		$ticket_display = new \Application\DeskPRO\PageDisplay\Page\TicketPageZoneCollection('create');
		$ticket_display->setPersonContext($this->person);
		$ticket_display->addPagesFromDb();
		$ticket_display_js = "window.DESKPRO_TICKET_DISPLAY = " . $ticket_display->compileJs() . ";";
		$ticket_display_js .= "\nwindow.DESKPRO_TICKET_PRI_MAP = " . json_encode($this->container->getDataService('TicketPriority')->getIdToPriorityMap()) . ';';

		$default_page = $ticket_display->getDepartmentPage($newticket->ticket->department_id);

		if ($default_page) {
			$default_page_data = $default_page->getPageDisplay('default')->data;
			$page_data_field_ids = array();
			foreach ($default_page->getPageDisplay('default')->data as $info) {
				$page_data_field_ids[] = $info['id'];
			}
		} else {
			$default_page_data = array();
			$page_data_field_ids = array();
		}

		$unique_items = array();
		foreach ($ticket_display->getPagesData() as $page) {
			foreach ($page as $item) {
				$unique_items[$item['id']] = $item;
			}
		}

		$captcha = null;
		if (isset($unique_items['captcha']) && empty($this->person->id)) {
			$captcha = $this->container->getSystemObject('form_captcha', array('type' => 'user_newticket'));
		}

		$errors = array();
		$error_fields = array();

		$validator = new \Application\UserBundle\Validator\NewTicketValidator();
		if ($captcha) {
			$validator->setCaptcha($captcha);
		}

		// Custom fields
		// We use this fieldgroup so the form names are part of custom_fields array: custom_fields[field_1] etc
		// So dont remove it even though it looks like it's not used! :-)
		$custom_fields_form = $this->get('form.factory')->createNamedBuilder('form', 'newticket[custom_ticket_fields]');

		/** @var $fm \Application\DeskPRO\CustomFields\TicketFieldManager */
		$fm = $this->container->getSystemService('TicketFieldsManager');
		if (isset($_POST['newticket']['custom_ticket_fields'])) {
			$field_data = $fm->getStrucutredDataFromForm($_POST['newticket']['custom_ticket_fields'], 'Application\\DeskPRO\\Entity\\CustomDataTicket');

			$field_form_data = $fm->createFieldDataFromArray($field_data);
			$custom_fields = $fm->getDisplayArray($field_form_data, $custom_fields_form, false);
		} else {
			$custom_fields = $fm->getDisplayArray(array(), $custom_fields_form, true);
		}

		$captcha_html = '';
		if ($captcha) {
			$captcha_html = $captcha->getHtml();
		}

		$set_dep_id = null;
		if ($this->in->getUint('set_dep_id')) {
			$for_department_id = $this->in->getUint('set_dep_id');
		}

		if ($for_department_id) {
			$newticket->ticket->department_id = $for_department_id;
			$set_dep_id = $newticket->ticket->department_id;
		}

		if ($this->get('request')->getMethod() == 'POST' && !$this->in->getBool('no_submit')) {

			$validator->setPageData($default_page_data);

			if (!$this->consumeRequest('newticket')) {
				return $this->redirectRoute('user');
			}

			$form->bindRequest($this->get('request'));
			$newticket->ticket->attach_ids = $this->in->getCleanValueArray('attach_ids', 'string', 'discard');
			$newticket->ticket->attach_ids_authed = true;
			$newticket->custom_ticket_fields = isset($_POST['newticket']['custom_ticket_fields']) ? $_POST['newticket']['custom_ticket_fields'] : array();

			$trap_fail = false;
			if (!empty($_POST['first_name']) || !empty($_POST['last_name']) || !empty($_POST['email'])) {
				$trap_fail = true;
			}

			if ($validator->isValid($newticket) && !$trap_fail) {
				$ticket = $newticket->save();
				$person = $ticket['person'];

				App::setSkipCache(true);

				// Its no longer a preticket, so we can delete the record
				if ($preticket_id = $this->in->getUint('preticket_status_id')) {
					$preticket = $this->em->find('DeskPRO:PreticketContent', $preticket_id);

					// Must be same user
					if ($preticket) {
						if (!$preticket->visitor || $preticket->visitor->getId() != $this->session->getVisitor()->getId()) {
							$preticket = null;
						}
					}

					if ($preticket) {
						$this->em->remove($preticket);
						$this->em->flush();
					}
				}

				if ($ticket->person_email_validating) {
					$this->session->setFlash('new_ticket_validating_email', $ticket->person_email_validating->getEmail());
					$this->session->save();
				}

				// Require login means we need to ask the user to log in now
				if ($newticket->require_login) {

					$this->session->setFlash('new_ticket_login', 1);
					$this->session->save();

					return $this->redirectRoute('user_login', array('return' => $this->generateUrl('user_tickets_new_finishlogin', array('ticket_id' => $ticket->id))));

				// New users are always sent back to home with flash message.
				} elseif ($person->isNewPerson() || !$person->is_user) {

					$go = 'front';

				// Existing users are redirected to the ticket if they're using a validated email address.
				// Otherwise they're sent back to the homepage just like an unregistered user is
				} else {
					if ($ticket->person_email_validating || $ticket->person->id != $this->person->id) {
						$go = 'front';
					} else {
						$go = 'ticket';
					}
				}

				if ($this->in->getString('redirect_after')) {
					return $this->redirect($this->in->getString('redirect_after'));
				} elseif ($format == 'iframe') {
					return $this->redirectRoute('user_tickets_new_thanks_simple', array('ticket_ref' => $ticket['public_id']));
				} else {
					if ($go == 'front') {
						if ($ticket->person_email_validating) {
							$this->session->setFlash('new_ticket_validating_email', $ticket->person_email_validating->getEmail());
						} else {
							$this->session->setFlash('new_ticket_email', $ticket->person_email->getEmail());
						}
						$this->session->setFlash('new_ticket', $ticket->getPublicId());

						$this->session->save();

						return $this->redirectRoute('user');
					} else {
						return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket['public_id']));
					}
				}
			} else {
				$errors = $validator->getErrors(true);
				$error_fields = $validator->getErrorGroups(true);
			}
		}

		$tpl = 'UserBundle:NewTicket:new-ticket.html.twig';
		$redirect_after = '';
		if ($format == 'iframe') {
			$tpl = 'UserBundle:NewTicket:new-ticket-iframe.html.twig';
			$redirect_after = $this->in->getString('redirect_after');
		}

		return $this->render($tpl, array(
			'set_dep_id' => $set_dep_id,
			'all_items' => $unique_items,

			'newticket' => $newticket,
			'ticket_options' => $newticket_formtype->getTicketOptions(),
			'newticket_formtype' => $newticket_formtype,
			'form' => $form->createView(),
			'custom_fields' => $custom_fields,
			'ticket_display_js' => $ticket_display_js,

			'captcha_html' => $captcha_html,
			'errors' => $errors,
			'error_fields' => $error_fields,

			'default_page_data' => $default_page_data,
			'page_data_field_ids' => $page_data_field_ids,

			'redirect_after' => $redirect_after,
			'website_url' => $website_url,

			'hide_name_field'  => $hide_name_field,
			'hide_email_field' => $hide_email_field,
		));
    }

	/**
	 * Saves a users form in the database incase they abandon the form
	 */
	public function saveStatusAction()
	{
		$id = $this->in->getUint('preticket_status_id');

		$preticket = null;
		if ($id) {
			$preticket = $this->em->find('DeskPRO:PreticketContent', $id);

			// Must be same user
			if ($preticket) {
				if (!$preticket->visitor || $preticket->visitor->getId() != $this->session->getVisitor()->getId()) {
					$preticket = null;
				}
			}
		}

		if (!$preticket) {
			$preticket = Entity\PreticketContent::newForPerson($this->person, true);
		}

		$form_data = $_POST;
		unset($form_data['preticket_status_id']);

		if (!empty($form_data['newticket']['ticket']['subject'])) {
			$preticket->subject = $form_data['newticket']['ticket']['subject'];
		}
		if (!empty($form_data['newticket']['ticket']['message'])) {
			$preticket->message = $form_data['newticket']['ticket']['message'];
		}
		if (!empty($form_data['newticket']['ticket']['department_id'])) {
			$preticket->department_id = $form_data['newticket']['ticket']['department_id'];
		}
		if (!empty($form_data['newticket']['person']['email'])) {
			$preticket->email = $form_data['newticket']['person']['email'];
		}
		if (!empty($form_data['newticket']['person']['name'])) {
			$preticket->name = $form_data['newticket']['person']['name'];
		}

		$preticket->data = $form_data;

		$this->em->beginTransaction();
		$this->em->persist($preticket);
		$this->em->flush();
		$this->em->commit();

		App::setSkipCache(true);

		$this->session->set('preticket_id', $preticket->getId());

		return $this->createJsonResponse(array(
			'preticket_status_id' => $preticket->id
		));
	}

	public function contentSolvedRedirectAction()
	{
		$id = $this->in->getUint('preticket_status_id');

		$preticket = null;
		if ($id) {
			$preticket = $this->em->find('DeskPRO:PreticketContent', $id);

			// Must be same user
			if ($preticket) {
				if (!$preticket->visitor || $preticket->visitor->getId() != $this->session->getVisitor()->getId()) {
					$preticket = null;
				}
			}
		}

		$url = $this->in->getString('url');
		if (!$url) {
			$url = $this->get('router')->generate('user');
		}

		$content_type = $this->in->getString('content_type');
		$content_id   = $this->in->getString('content_id');

		// Invalid preticket or content
		if (!$preticket || !$content_type || !$content_id) {
			return $this->redirect($url);
		}

		$preticket->is_solved    = true;
		$preticket->object_type = $content_type;
		$preticket->object_id   = $content_id;

		$this->em->beginTransaction();
		$this->em->persist($preticket);
		$this->em->flush();
		$this->em->commit();

		App::setSkipCache(true);

		$this->session->remove('preticket_id');

		return $this->redirect($url);
	}

	public function contentSolvedSaveAction()
	{
		$id = $this->in->getUint('preticket_status_id');

		$preticket = null;
		if ($id) {
			$preticket = $this->em->find('DeskPRO:PreticketContent', $id);

			// Must be same user
			if ($preticket) {
				if (!$preticket->visitor || $preticket->visitor->getId() != $this->session->getVisitor()->getId()) {
					$preticket = null;
				}
			}
		}

		$content_type = $this->in->getString('content_type');
		$content_id   = $this->in->getString('content_id');

		// Invalid preticket or content
		if (!$preticket || !$content_type || !$content_id) {
			return $this->createJsonResponse(array('invalid_details' => 1));
		}

		if ($this->in->getBool('add_unsolved')) {
			$unsolved = $preticket->unsolved_content;
			$unsolved[] = array($content_type, $content_id);

			$preticket->unsolved_content = $unsolved;
		} else {
			$preticket->is_solved    = true;
			$preticket->object_type = $content_type;
			$preticket->object_id   = $content_id;
		}

		// Mark content as helpful while we're at it
		try {
			$save_rating = new \Application\DeskPRO\Publish\SaveRating($this->person);
			$save_rating->save($content_type, $content_id, 1);
		} catch (\Exception $e) {}

		$this->em->beginTransaction();
		$this->em->persist($preticket);
		$this->em->flush();
		$this->em->commit();

		App::setSkipCache(true);

		return $this->createJsonResponse(array('success' => 1));
	}

	################################################################################
	# thanks
	################################################################################

	public function thanksAction($ticket_ref)
	{
		$ticket = $this->em->getRepository('DeskPRO:Ticket')->findOneByRef($ticket_ref);

		// Must exist, and match the ref n the session (so theres no info leak)
		if (!$ticket OR $ticket['ref'] != $this->session->get('submitted_ticket')) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		return $this->render('UserBundle:NewTicket:thanks.html.twig', array(
			'ticket' => $ticket
		));
	}

	/**
	 * Standard thanks page after a user submits a tikcet from an embedded iframe,
	 * and the webmaster didnt supply an after-redirection URL
	 *
	 * @param $ticket_ref
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function simpleThanksAction()
	{
		return $this->render('UserBundle:NewTicket:thanks-simple.html.twig', array(

		));
	}

	public function newFinishLoginAction($ticket_id)
	{
		if ($this->person->isGuest()) {
			$return_url = $this->generateUrl('user_tickets_new_finishlogin', array('ticket_id' => $ticket_id));
			return $this->redirectRoute('user_login', array('return' => $return_url));
		}

		$ticket = $this->em->find('DeskPRO:Ticket', $ticket_id);
		if (!$ticket) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if ($ticket->person->id != $this->person->id) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$ticket->status = 'awaiting_agent';
		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($ticket);
			$this->em->flush();

			$this->em->getConnection()->commit();

			App::setSkipCache(true);
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket->getPublicId()));
	}
}
