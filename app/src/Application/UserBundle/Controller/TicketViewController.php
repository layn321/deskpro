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

use Symfony\Component\HttpFoundation\Response;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Ticket;

use Application\DeskPRO\Tickets\TicketDisplay;
use Application\UserBundle\Form\NewTicketReplyType;

use Orb\Util\Arrays;
use Orb\Util\Numbers;

class TicketViewController extends AbstractController
{
	###########################################################################
	# load
	###########################################################################

	/**
	 * Loader action takes one of: ticket ID, ref, TAC or PTAC.
	 *
	 * With ID or Ref, the user must be logged in. TAC or PTAC shows the
	 * "guest view" of a ticket.
	 *
	 * @param string|int $ticket_id
	 */
	public function loadAction($ticket_ref, array $display_data = array())
	{
		if ($this->person['id']) {
			$try_order = array('id', 'ref', 'ptac');
		} else {
			// People who arent logged in we can try ptac first to save a query
			// (since ref and ptac could look smiliar based on ref format, we'd have to check two)
			$try_order = array('id', 'ptac', 'ref');
		}

		$return = $this->generateUrl('user_tickets_view', array('ticket_ref' => $ticket_ref));

		foreach ($try_order as $lookup_type) {
			switch ($lookup_type) {
				case 'id':
					if (Numbers::isInteger($ticket_ref)) {
						$ticket = $this->em->find('DeskPRO:Ticket', $ticket_ref);
						if ($ticket) {
							return $this->viewTicket($ticket, $display_data);
						}
					}
					break;

				case 'ref':
					$ticket = $this->em->getRepository('DeskPRO:Ticket')->findOneByRef($ticket_ref);
					if ($ticket) {
						return $this->viewTicket($ticket, $display_data);
					}
					break;

				case 'ptac':

					$ticket = $this->em->getRepository('DeskPRO:Ticket')->getByAccessCode($ticket_ref);

					if ($ticket) {
						// If they arent a user they can register now
						if ($ticket && $this->person->isGuest()) {

							if ($this->container->getDataService('Language')->isMultiLang()) {
								if (
									$ticket->person->getRealLanguage()
									&& $ticket->person->getRealLanguage()->getId() != $this->session->get('language_id')
									&& $ticket->person->getRealLanguage()->getId() != $this->container->getDataService('Language')->getDefault()->getId()
								) {
									$this->session->set('language_id', $ticket->person->getRealLanguage()->getId());
									$this->session->save();

									// Reload self
									return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket_ref));
								}
							}

							$this->session->set('ticket_from_ptac_register', $ticket->getPublicId());
							$this->session->save();

							$tpl_globals = $this->container->get('templating.globals');

							// If they have a password it means
							// they have a local DeskPRO account
							if ($ticket->person->password) {
								if ($ticket->person_email) {
									$tpl_globals->setVariable('login_with_email', $ticket->person_email->email);
								} elseif ($ticket->person && $ticket->person->getPrimaryEmailAddress()) {
									$tpl_globals->setVariable('login_with_email', $ticket->person->getPrimaryEmailAddress());
								}

							// Otherwise, they might have an account
							// from elsewhere (eg active directory)
							} else {
								foreach ($ticket->person->usersource_assoc as $us) {
									if ($us->identity_friendly) {
										$tpl_globals->setVariable('login_with_email', $us->identity_friendly);
										break;
									}
								}
							}

							$type = 'login';
							if (!$ticket->person->is_user) {
								$type = 'register';
							}

							return $this->renderLoginOrPermissionError($return, $type);
						}

						if ($ticket->person->getId() == $this->person->getId() || $ticket->hasParticipantPerson($this->person)) {
							return $this->viewTicket($ticket, $display_data);
						} else {
							// If they came here through the access code but arent on the ticket,
							// then we need to add them so they can see it
							if ($this->in->getBool('join')) {

								$part = $ticket->addParticipantPerson($this->person);
								if ($part) {
									$this->em->persist($part);
								}
								$this->em->persist($ticket);
								$this->em->flush();

								return $this->viewTicket($ticket, $display_data);
							} else {
								return $this->render('UserBundle:TicketView:part-join.html.twig', array(
									'ticket' => $ticket,
									'request_ref' => $ticket_ref,
								));
							}
						}
					}
					break;
			}
		}

		return $this->renderLoginOrPermissionError($return);
	}

	###########################################################################
	# viewTicket
	###########################################################################

	/**
	 * View a ticket without a user session
	 *
	 * @param \Application\DeskPRO\Entity\Ticket $ticket
	 */
	public function viewTicket(Ticket $ticket, array $display_data = array())
	{
        $is_pdf = $this->in->getBool('pdf');

		$is_participant = ($this->person->id == $ticket->person->id || $ticket->hasParticipantPerson($this->person->id));
		$is_org_manager = (
			$ticket->organization
			&& $this->person->organization
			&& $ticket->organization->id == $this->person->organization->id
			&& $this->person->organization_manager
		);

		if (!$is_participant && !$is_org_manager) {
			return $this->renderStandardError(null, null, 403);
		}

		$ticket_display = new TicketDisplay($ticket, $this->person);
		$vars = $ticket_display->getDisplayArray();

		$vars['is_participant'] = $is_participant;
		$vars['is_org_manager'] = $is_org_manager;

		$newreply_form = $this->get('form.factory')->create(new NewTicketReplyType());
		$vars['newreply_form'] = $newreply_form->createView();

		if ($display_data) {
			$vars = array_merge($vars, $display_data);
		}

		$user_participants = $ticket->getUserParticipants();
		$vars['user_participants'] = $user_participants;

        if($is_pdf) {
            $content_html = $this->renderView('DeskPRO:pdf_user:view_ticket.html.twig', $vars);

            $mpdf = new \mPDF_mPDF
            (
                'utf-8', // Language/Character set
                'A4', // Size
                '8', // Default Font Size
                '', // Default Font
                20, // Margin Left
                20, // Margin Right
                40, // Margin Top
                40, // Margin Bottom
                10, // Margin Header
                10, // Margin Footer
                'P' // Orientation
            );

            $mpdf->SetBasePath(realpath(__DIR__.'/../../../../../web/images'));

            $mpdf->WriteHTML($content_html);

            $pdf = $mpdf->Output('', 'S');

            $response = new Response();

            if($this->in->getBool('html')) {
                $response->setContent($content_html);
            }
            else
            {
                $response->setContent($pdf);
                $response->headers->set('Content-Type', 'application/pdf');
                $response->headers->set('Content-Disposition', 'attachment; filename=Ticket-'.$ticket->id.'.pdf');
            }

            return $response;
        }

		$ticket_display = new \Application\DeskPRO\PageDisplay\Page\TicketPageZoneCollection('view');
		$ticket_display->setPersonContext($this->person);
		$ticket_display->addPagesFromDb();
		$page = $ticket_display->getDepartmentPage($ticket->getDepartmentId());
		$vars['page_display'] = $page->getPageDisplay('default')->data;


		$tpl = 'UserBundle:TicketView:view.html.twig';
		if ($this->in->getBool('edit')) {

			$newticket = new \Application\DeskPRO\Tickets\EditTicket\EditTicket(
				$ticket
			);
			$newticket_formtype = new \Application\UserBundle\Form\EditTicketType($this->person);
			$form = $this->get('form.factory')->create($newticket_formtype, $newticket);

			$errors = array();
			$error_fields = array();

			$field_manager = $this->container->getSystemService('ticket_fields_manager');
			$custom_fields = $field_manager->getDisplayArrayForObject($ticket);

			$ticket_display = new \Application\DeskPRO\PageDisplay\Page\TicketPageZoneCollection('modify');
			$ticket_display->setPersonContext($this->person);
			$ticket_display->addPagesFromDb();
			$ticket_display_js = "window.DESKPRO_TICKET_DISPLAY = " . $ticket_display->compileJs() . ";";
			$ticket_display_js .= "\nwindow.DESKPRO_TICKET_PRI_MAP = " . json_encode($this->container->getDataService('TicketPriority')->getIdToPriorityMap()) . ';';

			$default_page = $ticket_display->getDepartmentPage($newticket->ticket->department_id);

			if ($default_page) {
				$default_page_data = $default_page->getPageDisplay('default')->data;
				$page_data_field_ids = array();
				foreach ($default_page_data as $info) {
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

			$errors = array();
			$error_fields = array();

			if ($this->in->getBool('process')) {

				$newticket->setPageData($default_page_data);

				$validator = new \Application\UserBundle\Validator\NewTicketValidator();
				$validator->setPageData($default_page_data);
				$form->bindRequest($this->get('request'));

				if ($validator->isValid($newticket)) {
					$newticket->save();

					$is_participant = ($this->person->id == $ticket->person->id || $ticket->hasParticipantPerson($this->person->id));
					if (!$is_participant && !$is_org_manager) {
						// removed self from the ticket, so redirect to the main page
						return $this->redirectRoute('user');
					}

					return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $ticket->getPublicId()));
				} else {
					$errors = $validator->getErrors(true);
					$error_fields = $validator->getErrorGroups(true);
				}
			}

			$vars = array_merge($vars, array(
				'default_page_data' => $default_page_data,
				'page_data_field_ids' => $page_data_field_ids,
				'all_items' => $unique_items,

				'form' => $form->createView(),
				'newticket' => $newticket,
				'custom_fields' => $custom_fields,
				'errors' => $errors,
				'error_fields' => $error_fields,
				'ticket_display_js' => $ticket_display_js,
			));

			$tpl = 'UserBundle:TicketView:view-modify.html.twig';
		}

		return $this->render($tpl, $vars);
	}
}
