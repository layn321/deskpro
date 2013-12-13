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

use Application\UserBundle\Form\RegisterType;

class RegisterController extends \Application\DeskPRO\Controller\AbstractController
{
	public function registerAction()
	{
		if ($this->session->getPerson()->getId()) {
			return $this->redirectRoute('user');
		}

		if ($this->container->getSetting('core.user_mode') == 'closed') {
			return $this->redirectRoute('user');
		}

		$captcha = null;
		if ($this->container->getSetting('user.register_captcha')) {
			$captcha = $this->container->getSystemObject('form_captcha', array('type' => 'user_reg'));
		}

		// Custom fields
		// We use this fieldgroup so the form names are part of custom_fields array: custom_fields[field_1] etc
		// So dont remove it even though it looks like it's not used! :-)
		$custom_fields_form = $this->get('form.factory')->createNamedBuilder('form', 'custom_fields');

		/** @var $fm \Application\DeskPRO\CustomFields\PersonFieldManager */
		$fm = $this->container->getSystemService('PersonFieldsManager');
		if (isset($_POST['custom_fields'])) {
			$field_data = $fm->getStrucutredDataFromForm($_POST['custom_fields'], 'Application\\DeskPRO\\Entity\\CustomDataPerson');

			$field_form_data = $fm->createFieldDataFromArray($field_data);
			$custom_fields = $fm->getDisplayArray($field_form_data, $custom_fields_form, false);
		} else {
			$custom_fields = $fm->getDisplayArray(array(), $custom_fields_form, true);
		}

		$register = new \Application\UserBundle\Form\Model\Register();
		$register->setCustomFields($fm->getFields());

		if ($this->session->get('language_id')) {
			$register->language_id = $this->session->get('language_id');
		}

		$tpl_globals = $this->container->get('templating.globals');
		if ($tpl_globals->getVariable('login_with_email')) {
			$register->email = $tpl_globals->getVariable('login_with_email');
		}

		$reg_formtype = new RegisterType();

		$from_ticket = false;
		if ($this->session->get('ticket_from_ptac_register')) {
			$from_ticket = $this->em->find('DeskPRO:Ticket', $this->session->get('ticket_from_ptac_register'));
			$register->from_ticket = $from_ticket;
		}

		$form = $this->get('form.factory')->create($reg_formtype, $register);

		$error_fields = null;
		$errors = null;
		if ($this->get('request')->getMethod() == 'POST' && !$this->in->getBool('no_submit')) {
			$form->bindRequest($this->get('request'));
			$register->custom_fields = !empty($_POST['custom_fields']) ? $_POST['custom_fields'] : null;

			$validator = new \Application\UserBundle\Validator\RegisterValidator();
			$validator->setCustomFields($fm->getFields());

			if ($captcha) {
				$validator->setCaptcha($captcha);
			}

			$is_valid = $validator->isValid($register);

			// If there is only one user on the ticket (no parts), then the access code on the ticket
			// proves the user is who they say they are and we can set their password etc
			// without a problem. if there are parts, then we cant be sure who they are,
			// so they'll just have to "forgot password" their account.
			if (
				$from_ticket
				&& !$from_ticket->person->is_user
				&& !$from_ticket->getUserParticipants()
				&& (!$is_valid && $validator->hasError('email.in_use'))
				&& $from_ticket->person->findEmailAddress($register->email)
			) {
				$validator->removeError('email.in_use');
				$register->no_validation = true;

				if (!count($validator->getErrors())) {
					$is_valid = true;
				}
			}

			if ($is_valid) {
				$person = $register->save();

				App::setSkipCache(true);

				$this->session->setFlash('register_done', 1);

				$to_login = false;
				if (!$person->primary_email) {
					$this->session->setFlash('register_done_email_validate', 1);
					$to_login = true;
				} elseif (!$person->is_agent_confirmed) {
					$this->session->setFlash('register_done_agent_validate', 1);
					$to_login = true;
				}

				// User not validating if they have an added email address already
				if ($person->primary_email) {

					$this->session->set('auth_person_id', $person->id);
					$this->session->set('dp_interface', DP_INTERFACE);

					// Set last login date
					App::getDb()->update('people', array('date_last_login' => date('Y-m-d H:i:s')), array('id' => $person->getId()));

					if ($from_ticket) {
						$this->session->remove('ticket_from_ptac_register');
					}

					$this->session->save();

					if ($from_ticket && $person->hasPerm('tickets.use')) {
						return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $from_ticket->id));
					}
				}

				if ($to_login) {
					return $this->redirectRoute('user_login');
				} else {
					return $this->redirectRoute('user');
				}
			} else {
				$errors = $validator->getErrors(true);
				$error_fields = $validator->getErrorGroups(true);
			}
		}

		return $this->render('UserBundle:Register:register.html.twig', array(
			'form' => $form->createView(),
			'custom_fields' => $custom_fields,
			'errors' => $errors,
			'error_fields' => $error_fields,
			'from_ticket' => $from_ticket,
			'this_page' => 'register',
			'captcha' => $captcha,
		));
	}
}
