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
use Application\UserBundle\Form\ProfileType;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\PersonEmailValidating;
use Application\DeskPRO\Entity\TmpData;

class ProfileController extends AbstractController implements RequireUserInterface
{
	############################################################################
	# index
	############################################################################

	/**
	 * Shows emails, link to edit password, form to edit name and timezone
	 */
	public function indexAction()
	{
		$form = $this->get('form.factory')->create(new ProfileType(), $this->person);
		$field_manager = $this->container->getSystemService('person_fields_manager');

		$is_org_manager = ($this->person->organization && $this->person->organization_manager);
		$new_blob_key = false;

		$invalid_name = false;
		$profile_saved = false;
		$invalid_custom_fields = array();
		if ($this->get('request')->getMethod() == 'POST') {
			$form->bindRequest($this->get('request'));

			$is_valid = true;
			if (!$this->person->first_name) {
				$is_valid = false;
				$invalid_name = true;
			} elseif (!in_array($this->person->timezone, \DateTimeZone::listIdentifiers())) {
				$is_valid = false;
			}

			$custom_fields = !empty($_POST['custom_fields']) ? $_POST['custom_fields'] : null;
			foreach ($field_manager->getFields() as $field) {
				$errors = $field->getHandler()->validateFormData($custom_fields ?: array());
				foreach ($errors as $code) {
					$invalid_custom_fields['field_' . $field->getId()] = true;
					$invalid_custom_fields[$code] = true;
					$is_valid = false;
				}
			}

			if ($is_org_manager) {
				$this->person->setPreference('org.manager_auto_add', $this->in->getBool('org_manager_auto_add') ? 1 : 0);
			}

			/** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
			if ($this->person->getPermissionsManager()->get('GeneralChecker')->canSetPicture()) {
				$file = $this->request->files->get('new_picture');
				if ($file && $file->getClientSize()) {
					$accept = $this->container->getAttachmentAccepter();

					$picture_error = $accept->getError($file, 'user');
					if ($picture_error) {
						switch ($picture_error['error_code']) {
							case 'size': $phrase_id = 'user.error.attach_size'; break;
							case 'failed_upload': $phrase_id = 'user.error.attach_failed'; break;
							case 'no_file': $phrase_id = 'user.error.attach_no-file'; break;
							case 'server_error': $phrase_id = 'user.error.attach_unknown-error'; break;
							case 'not_in_allowed_exts': $phrase_id = 'user.error.attach_ext-allowed'; break;
							case 'not_allowed_exts': $phrase_id = 'user.error.attach_ext-not-allow'; break;
						}
						$picture_error['error'] = $this->container->getTranslator()->phrase($phrase_id, $picture_error);
					}
					if (!$picture_error) {
						$set = new \Application\DeskPRO\Attachments\RestrictionSet();
						$set->setAllowedExts(array('gif', 'png', 'jpg', 'jpeg'));
						$accept->addRestrictionSet('only_images', $set);
						$picture_error = $accept->getError($file, 'only_images');
					}

					if (!$picture_error) {
						$blob = $accept->accept($file);
						$this->person->setPictureBlob($blob);
						$new_blob_key = $blob->getId() . '-' . $blob->getAuthId();
					}
				} else {
					$new_blob_key = $this->in->getString('new_blob_key');
					if ($new_blob_key) {
						list($id, $auth_code) = explode('-', $new_blob_key);
						$blob = $this->em->getRepository('DeskPRO:Blob')->find($id);
						if ($new_blob_key && $blob->getAuthId() == $auth_code) {
							$this->person->setPictureBlob($blob);
						}
					}
				}

				if ($this->in->getBool('remove_picture')) {
					$this->person->setPictureBlob(null);
					$new_blob_key = false;
				}
			}

			if ($is_valid) {
				$this->em->persist($this->person);
				$this->em->flush();

				if ($custom_fields) {
					$field_manager->saveFormToObject($custom_fields, $this->person);
				}

				$profile_saved = true;
			}
		}

		$validating_emails = $this->em->getRepository('DeskPRO:PersonEmailValidating')->getForPerson($this->person);

		$custom_fields = $field_manager->getDisplayArrayForObject($this->person);

		$enable_twitter = App::getConfig('enable_twitter') && \Application\DeskPRO\Service\Twitter::getUserConsumerKey();

		return $this->render('UserBundle:Profile:index.html.twig', array(
			'form'               => $form->createView(),
			'validating_emails'  => $validating_emails,
			'invalid_name'       => $invalid_name,
			'profile_saved'      => $profile_saved,
			'custom_fields'      => $custom_fields,
			'invalid_custom_fields' => $invalid_custom_fields,
			'is_org_manager'     => $is_org_manager,
			'org_manager_auto_add' => ($is_org_manager && $this->person->getPref('org.manager_auto_add')),
			'new_blob_key'       => $new_blob_key,
			'enable_twitter'     => $enable_twitter
		));
	}

	############################################################################
	# change-password
	############################################################################

	public function changePasswordAction()
	{
		$password = $this->in->getString('password');
		$password2 = $this->in->getString('password2');

		if (!$this->person->checkPassword($this->in->getString('current_password'))) {
			$this->session->setFlash('invalid_current_password', 1);
		} else if ($password != $password2) {
			$this->session->setFlash('invalid_repeat_password', 1);
		} elseif (\Orb\Util\Strings::utf8_strlen($password) < 4) {
			$this->session->setFlash('invalid_password_length', 1);
		} else {
			$this->person->setPassword($password);
			$person = $this->person;
			$this->em->transactional(function ($em) use ($person) {
				$em->persist($person);
			});

			// Reset user session
			$this->db->delete('sessions', array('person_id' => $this->person->id));

			return $this->redirectRoute('user_login');
		}

		return $this->redirectRoute('user_profile');
	}

	############################################################################
	# associateTwitter
	############################################################################

	public function associateTwitterAction()
	{
		if (\Application\DeskPRO\Service\Twitter::getUserConsumerKey()) {
			if ($this->in->getBool('start')) {
				$api = \Application\DeskPRO\Service\Twitter::getUserTwitterApi();
				$api->setCallback($this->generateUrl('user_profile_associate_twitter', array(), true));
				return $this->redirect($api->getAuthenticateUrl());
			} else if ($this->in->getString('oauth_token')) {
				$api = \Application\DeskPRO\Service\Twitter::getUserTwitterApi();
				$api->setToken($this->in->getString('oauth_token'));
				$access = $api->getAccessToken();

				$verified = App::getEntityRepository('DeskPRO:PersonTwitterUser')->getVerifiedPersonForTwitterUser($access->user_id);
				if (!$verified || $verified->id == $this->person->id) {
					App::getDb()->executeUpdate("
						INSERT INTO people_twitter_users
							(person_id, twitter_user_id, screen_name, is_verified, oauth_token, oauth_token_secret)
						VALUES (?, ?, ?, 1, ?, ?)
						ON DUPLICATE KEY UPDATE
							twitter_user_id = VALUES(twitter_user_id),
							screen_name = VALUES(screen_name),
							is_verified = 1,
							oauth_token = VALUES(oauth_token),
							oauth_token_secret = VALUES(oauth_token_secret)
					", array($this->person->id, $access->user_id, $access->screen_name, $access->oauth_token, $access->oauth_token_secret));

					$has_account = false;
					foreach ($this->person->getContactData('twitter') AS $twitter_details) {
						if ($twitter_details->field_1 == $access->screen_name || ($twitter_details->field_3 && $twitter_details->field_3 == $access->user_id)) {
							$twitter_details->field_10 = '1';
							$this->em->persist($twitter_details);
							$has_account = true;
						}
					}

					if (!$has_account) {
						$twitter_details = new \Application\DeskPRO\Entity\PersonContactData();
						$twitter_details->contact_type = 'twitter';
						$twitter_details->person = $this->person;
						$twitter_details->field_1 = $access->screen_name;
						$twitter_details->field_2 = '0';
						$twitter_details->field_3 = $access->user_id;
						$twitter_details->field_10 = '1';
						$this->em->persist($twitter_details);
					}

					$this->em->flush();
				}
			}
		}

		return $this->redirectRoute('user_profile');
	}

	############################################################################
	# removeTwitter
	############################################################################

	public function removeTwitterAction($account_id)
	{
		$twitter_user_id = null;

		foreach ($this->person->twitter_users AS $account) {
			if ($account->id == $account_id) {
				$this->em->remove($account);
				$twitter_user_id = $account->twitter_user_id;
				break;
			}
		}

		if ($twitter_user_id) {
			foreach ($this->person->getContactData('twitter') AS $twitter_details) {
				if ($twitter_details->field_3 && $twitter_details->field_3 == $twitter_user_id) {
					$this->em->remove($twitter_details);
				}
			}
		}

		$this->em->flush();

		return $this->redirectRoute('user_profile');
	}

	############################################################################
	# setDefaultEmail
	############################################################################

	/**
	 * Switches the primary email address on the account
	 */
	public function setDefaultEmailAction($email_id)
	{
		$email = $this->person->getEmailId($email_id);

		if (!$email) {
			return $this->renderStandardError('@user.error.invalid_email-explain', '@user.error.invalid_email', 404);
		}

		if (!$email['is_validated']) {
			return $this->renderStandardError('@user.profile.error_validate_to_use', '@user.profile.error_validate_to_use_tilte', 409);
		}

		$person = $this->person;
		$person->primary_email = $email;

		$this->em->transactional(function ($em) use ($person) {
			$em->persist($person);
		});

		return $this->redirectRoute('user_profile');
	}


	############################################################################
	# removeEmail
	############################################################################

	/**
	 * Removes an email address from the account
	 */
	public function removeEmailAction($email_id)
	{
		$email = $this->person->getEmailId($email_id);

		if (!$email) {
			return $this->renderStandardError('@user.error.invalid_email-explain', '@user.error.invalid_email', 404);
		}

		#------------------------------
		# Can we remove this address?
		#------------------------------

		$validated_emails = $this->person->getValidatedEmails();

		$pass_count = false;
		if (!$email['is_validated']) {
			if (count($validated_emails) >= 1) $pass_count = true;
		} else {
			if (count($validated_emails) >= 2) $pass_count = true; // 2 because 1 wil be this email
		}

		if (!$pass_count) {
			return $this->renderStandardError('@user.profile.error_last_email_explain', '@user.profile.error_last_email', 409);
		}

		#------------------------------
		# Do the remove now
		#------------------------------

		$this->em->beginTransaction();
		$this->person->removeEmailAddressId($email['id']);
		$this->em->persist($this->person);
		$this->em->flush();
		$this->em->commit();

		$this->session->setFlash('removed_email', $email['email']);

		return $this->redirectRoute('user_profile');
	}

	public function removeEmailValidatingAction($email_id)
	{
		$validating_email = $this->em->find('DeskPRO:PersonEmailValidating', $email_id);

		if (!$validating_email || $validating_email->person['id'] != $this->person['id']) {
			return $this->renderStandardError('@user.error.invalid_email-explain', '@user.error.invalid_email', 404);
		}

		$this->em->transactional(function ($em) use ($validating_email) {
			$em->remove($validating_email);
			$em->flush();
		});

		$this->session->setFlash('removed_email', $validating_email['email']);

		return $this->redirectRoute('user_profile');
	}


	############################################################################
	# newEmail
	############################################################################

	public function newEmailAction()
	{
		$email_address = $this->in->getString('new_email');

		// Already have this email on their account
		if ($this->person->findEmailAddress($email_address)) {
			return $this->redirectRoute('user_profile');
		}

		if (!$this->container->getSystemService('email_address_validator')->isValidUserEmail($email_address)) {
			$this->session->setFlash('invalid_email', 1);
			$this->session->save();
			return $this->redirectRoute('user_profile');
		}

		$email_exists = $this->em->getRepository('DeskPRO:PersonEmail')->getEmail($email_address);
		if ($email_exists) {
			$this->session->setFlash('email_exists', 1);
			$this->session->save();
			return $this->redirectRoute('user_profile');
		}

		$validating_exists = $this->db->fetchColumn("
			SELECT id
			FROM people_emails_validating
			WHERE email = ?
		", array($email_address));
		if ($validating_exists) {
			return $this->redirectRoute('user_profile');
		}

		$validating_email = new PersonEmailValidating($email_address);
		$validating_email['email'] = $email_address;
		$validating_email->person = $this->person;

		$this->em->transactional(function ($em) use ($validating_email) {
			$em->persist($validating_email);
			$em->flush();
		});

		$this->_doSendValidationEmail($validating_email);

		$this->session->setFlash('new_email_validating', $validating_email['email']);
		$this->session->save();

		return $this->redirectRoute('user_profile');
	}

	############################################################################
	# sendValidateEmailLink
	############################################################################

	protected function _doSendValidationEmail($validating_email)
	{
		$person = $this->person;

		$vars = array(
			'validating_email' => $validating_email
		);

		$container = $this->container;
		App::getTranslator()->setTemporaryLanguage($person->getLanguage(), function($tr, $lang) use ($vars, $person, $validating_email, $container) {

			$message = $container->getMailer()->createMessage();
			$message->setTo($validating_email->getEmail(), $person->getDisplayName());
			$message->setTemplate('DeskPRO:emails_user:new-email-validate.html.twig', $vars);
			$message->enableQueueHint();

			$container->getMailer()->send($message);
		});
	}

	/**
	 * Re-send the validation link
	 */
	public function sendValidateEmailLinkAction($email_id)
	{
		$validating_email = $this->em->find('DeskPRO:PersonEmailValidating', $email_id);

		if (!$validating_email || $validating_email->person['id'] != $this->person['id']) {
			return $this->renderStandardError('@user.error.invalid_email-explain', '@user.error.invalid_email', 404);
		}

		$this->_doSendValidationEmail($validating_email);

		$this->session->setFlash('resent_validation_email', $validating_email['email']);

		return $this->redirectRoute('user_profile');
	}
}
