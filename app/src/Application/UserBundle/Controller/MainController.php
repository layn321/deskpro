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


namespace Application\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\People\EmailValidator;
use Application\DeskPRO\People\AccountValidator;

class MainController extends AbstractController
{
	/**
	 * This action is used to render the header/footer in the portal editor when it was updated.
	 * We need to actually render it like this because they could use twig tags in it, and it could
	 * depend on the user scope, so we can't render it as part of the admin request that does the saving.
	 */
	public function adminRenderTemplateAction($type)
	{
		if (!$this->person->can_admin) {
			throw $this->createNotFoundException();
		}

		if ($pid = \Orb\Util\Strings::extractRegexMatch('#^block:([0-9]+)$#', $type)) {
			$page_display = $this->em->find('DeskPRO:PortalPageDisplay', $pid);

			if (!$page_display) {
				throw new $this->createNotFoundException();
			}

			if (strpos($page_display->type, '\\') === false) {
				$type_class = ucfirst(\Orb\Util\Strings::underscoreToCamelCase($page_display->type));
				$type_class = "Application\\DeskPRO\\PageDisplay\\Item\\Portal\\$type_class";
			} else {
				$type_class = $type;
			}

			$data = $page_display->data;
			$data['pid'] = $page_display->id;
			$data['is_enabled'] = $page_display->is_enabled;
			$data['display_order'] = $page_display->display_order;
			$data['admin_mode'] = true;

			$obj = new $type_class($page_display->section, $page_display->data, $this->container, $this->person);

			$res = new \Symfony\Component\HttpFoundation\Response($obj->getHtml(), 200);

		} else {
			$res = null;
			switch ($type) {
				case 'header':
					$res = $this->render('UserBundle::custom-header.html.twig');
					break;
				case 'articles_header':
					$res = $this->render('UserBundle:Articles:section-header.html.twig');
					break;
				case 'downloads_header':
					$res = $this->render('UserBundle:Downloads:section-header.html.twig');
					break;
				case 'feedback_header':
					$res = $this->render('UserBundle:Feedback:section-header.html.twig');
					break;
				case 'welcome':
					$res = $this->render('UserBundle:Portal:welcome-block.html.twig');
					break;
				case 'footer':
					$res = $this->render('UserBundle::custom-footer.html.twig');
					break;
			}

			if (!$res) {
				throw new $this->createNotFoundException();
			}
		}

		$res->setMaxAge(0);
		$res->setExpires(new \DateTime('-7 days ago'));
		$res->setLastModified(new \DateTime());
		return $res;
	}

	public function acceptTempUploadAction()
	{
		$security_token = $this->in->getString('security_token');
		if (!$this->session->getEntity()->checkSecurityToken('attach_temp', $security_token)) {
			return $this->createJsonResponse(array(array(
				'error_code' => 'invalid_security_token'
			)), 403);
		}

		$file = $this->request->files->get('attach');
		if (is_array($file)) {
			$file = array_pop($file);
		}
		$accept = $this->container->getAttachmentAccepter();

		$error = $accept->getError($file, 'user');
		if ($error) {
			switch ($error['error_code']) {
				case 'size': $phrase_id = 'user.error.attach_size'; break;
				case 'failed_upload': $phrase_id = 'user.error.attach_failed'; break;
				case 'no_file': $phrase_id = 'user.error.attach_no-file'; break;
				case 'server_error': $phrase_id = 'user.error.attach_unknown-error'; break;
				case 'not_in_allowed_exts': $phrase_id = 'user.error.attach_ext-allowed'; break;
				case 'not_allowed_exts': $phrase_id = 'user.error.attach_ext-not-allow'; break;
			}
			$error['error'] = $this->container->getTranslator()->phrase($phrase_id, $error);
			return $this->createJsonResponse(array($error));
		}

		if ($error) {
			$error['error'] = $this->container->getTranslator()->phrase('user.error.attach_' . $error['error_code'], $error);
			return $this->createJsonResponse(array($error));
		}

		$blob = $accept->accept($file, true);

		return $this->createJsonResponse(array(array(
			'blob_id'           => $blob->getId(),
			'blob_auth_id'      => $blob->id . '-' . $blob->authcode,
			'download_url'      => $blob->getDownloadUrl(true),
			'filename'          => $blob->getFilename(),
			'filesize_readable' => $blob->getReadableFilesize()
		)));
	}

	public function commentFormLoginPartialAction()
	{
		return $this->render('UserBundle:Common:comments-login-form.html.twig');
	}

	public function validateEmailAction($id, $auth)
	{
		$validator = EmailValidator::createFromId($id, $auth);

		if (!$validator) {
			return $this->renderStandardError('@user.error.invalid_email-code', '', 404);
		}

		$valdating_email = $validator->getValidatingEmail();

		$email_exists = $this->em->getRepository('DeskPRO:PersonEmail')->getEmail($validator->getValidatingEmail()->getEmail());
		if ($email_exists && $email_exists->person->id != $valdating_email->person->id) {
			return $this->render('UserBundle:Profile:validate-email-exists.html.twig', array(
				'email' => $email_exists,
				'person' => $validator->getPerson(),
				'ticket_ids' => $validator->getTicketIds()
			));
		}

		try {
			$email = $validator->validate();
		} catch (\OutOfBoundsException $e) {
			if ($e->getCode() == 100) {
				return $this->renderStandardError('@user.error.dupe_email');
			} else {
				throw $e;
			}
		}

		return $this->render('UserBundle:Profile:validate-email-success.html.twig', array(
			'email' => $email,
			'person' => $validator->getPerson(),
			'ticket_ids' => $validator->getTicketIds()
		));
	}

	public function validateTicketEmailAction($access_code)
	{
		/** @var $ticket \Application\DeskPRO\Entity\Ticket */
		$ticket = $this->em->getRepository('DeskPRO:Ticket')->getByAccessCode($access_code);

		if (!$ticket) {
			return $this->renderStandardError('@user.error.invalid_email-code', '', 404);
		}

		// A new email address
		if ($ticket->person_email_validating) {
			$validator = new EmailValidator($ticket->person_email_validating);
			$valdating_email = $validator->getValidatingEmail();

			$email_exists = $this->em->getRepository('DeskPRO:PersonEmail')->getEmail($validator->getValidatingEmail()->getEmail());
			if ($email_exists && $email_exists->person->id != $valdating_email->person->id) {

				// Unset the email on the ticket
				$ticket->person_email_validating = null;
				$this->em->persist($ticket);
				$this->em->flush();

				return $this->render('UserBundle:Profile:validate-email-exists.html.twig', array(
					'email' => $email_exists,
					'person' => $validator->getPerson(),
					'ticket_ids' => $validator->getTicketIds()
				));
			}

			$email = $validator->validate();

		// Validating the account
		} else {
			$validator = new AccountValidator($ticket->person, $ticket->person_email);
			$validator->validate();

			$email = $ticket->person_email;
		}

		return $this->render('UserBundle:Profile:validate-email-success.html.twig', array(
			'email' => $email,
			'person' => $validator->getPerson(),
			'ticket_ids' => $validator->getTicketIds()
		));
	}

	public function jstellLoginAction($jstell, $security_token, $usersource_id = 0)
	{
		if (!$this->session->getEntity()->checkSecurityToken('jstell', $security_token)) {
			return $this->createResponse('', 403);
		}

		if ($this->person->isGuest()) {
			$person_data = array(
				'person_id' => 0
			);
		} else {

			$person_data = array(
				'person_id' => $this->person->id,
				'person_name' => $this->person->name,
				'person_email' => $this->person->getPrimaryEmailAddress(),
			);

			if ($this->session->get('auth_usersource_id')) {
				$usersource = $this->em->getRepository('DeskPRO:Usersource')->getUsersource($this->session->get('auth_usersource_id'));
				if ($usersource) {
					$person_data['usersource_type']     = $usersource->source_type;
					$person_data['usersource_title']    = $usersource->title;

					if ($this->session->get('auth_usersource_display_name')) {
						$person_data['usersource_display_name']  = $this->session->get('auth_usersource_display_name');
					}
					if ($this->session->get('auth_usersource_display_link')) {
						$person_data['usersource_display_link']  = $this->session->get('auth_usersource_display_link');
					}
				}
			}

			if ($usersource_id && $this->person->usersource_assoc[$usersource_id]) {
				$person_data = array_merge(
					$this->person->usersource_assoc[$usersource_id]->getData(),
					$person_data
				);
			}
		}

		$person_data = json_encode($person_data);

		$html = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script type="text/javascript" charset="utf-8">
	function load() {
		window.opener['$jstell']($person_data);
		window.close();
	}
	</script>
</head>
<body onload="load()">
</body>
</html>
HTML;

		return $this->createResponse($html);
	}


	public function quickSetLanguageAction()
	{
		if ($return = $this->in->getString('return')) {
			$return = preg_replace('#(\?|&)?language_id=\d+#', '', $return);
			return $this->redirect($return);
		}

		return $this->redirectRoute('user');
	}
}
