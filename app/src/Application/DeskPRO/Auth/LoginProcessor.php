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
 * @subpackage DeskPRO
 */

namespace Application\DeskPRO\Auth;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\PersonContactData;
use Orb\Util\Arrays;
use Orb\Auth\Identity;

use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\PersonUsersourceAssoc;
use Application\DeskPRO\Entity\Usersource;

class LoginProcessor
{
	/**
	 * The users identity
	 * @var \Orb\Auth\Identity
	 */
	protected $identity;

	/**
	 * The usersource
	 * @var \Application\DeskPRO\Entity\Usersource
	 */
	protected $usersource;

	/**
	 * The association
	 * @var \Application\DeskPRO\Entity\PersonUsersourceAssoc
	 */
	protected $assoc;

	/**
	 * The person the login represents
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;


	public function __construct(Usersource $usersource, Identity $identity)
	{
		$this->identity = $identity;
		$this->usersource = $usersource;
	}

	public function getPerson()
	{
		if ($this->person !== null) return $this->person;

		#------------------------------
		# Figure if we have an existing Person mapped, or if its
		# a new Person
		#------------------------------

		$em = App::getOrm();
		$assoc_repos = $em->getRepository('DeskPRO:PersonUsersourceAssoc');

		$em->beginTransaction();

		$this->assoc = $assoc_repos->getIdentityAssociation(
			$this->usersource,
			$this->identity->getIdentity()
		);

		$mapped_fields = $this->usersource->getFieldsFromIdentity($this->identity);
		$mapped_fields = Arrays::removeEmptyString($mapped_fields);
		$mapped_fields = new \Orb\Util\OptionsArray($mapped_fields);

		#------------------------------
		# If we dont have one yet, we're have to create the assoc and maybe a new user too
		#------------------------------

		if (!$this->assoc) {

			$this->person = null;

			// If we can trust the email address and there already exists a person
			// with this email address, then we can just link the accounts now
			$set_email = false;
			if ($mapped_fields->has('email') && $mapped_fields->get('email_confirmed')) {
				$set_email = $mapped_fields->get('email');
				$email = App::getEntityRepository('DeskPRO:PersonEmail')->getEmail($mapped_fields->get('email'));
				if ($email) {
					$this->person = $email->person;
				}
			}

			// if someone has already associated this twitter account with them, then connect with them
			if ($mapped_fields->has('twitter')) {
				$twitter = $mapped_fields->get('twitter');
				$this->person = App::getEntityRepository('DeskPRO:PersonTwitterUser')->getVerifiedPersonForTwitterUser($twitter['user_id']);
			}

			if (!$this->person) {
				$this->person = new Person();
				$this->person->is_user = true;
				$this->person->creation_system = 'web.usersource';
			}

			foreach (array('first_name', 'last_name', 'name') as $k) {
				if (!$this->person[$k] && $mapped_fields->has($k)) {
					$this->person[$k] = $mapped_fields->get($k);
				}
			}

			if ($mapped_fields->has('picture_data') && !$this->person->picture_blob) {
				$filename = tempnam(dp_get_tmp_dir(), 'picture');
				$fp = @fopen($filename, 'w');
				if ($fp) {
					@fwrite($fp, $mapped_fields->get('picture_data'));
					@fclose($fp);

					$mime_map = array(
						IMAGETYPE_GIF => array('gif', 'image/gif'),
						IMAGETYPE_JPEG => array('jpg', 'image/jpeg'),
						IMAGETYPE_PNG => array('png', 'image/png')
					);
					$image_info = getimagesize($filename);
					if ($image_info && $image_info[0] && $image_info[1] && isset($mime_map[$image_info[2]])) {
						$mime = $mime_map[$image_info[2]];
						$file = new \Symfony\Component\HttpFoundation\File\UploadedFile(
							$filename, 'picture.' . $mime[0], $mime[1], strlen($mapped_fields->get('picture_data'))
						);

						$accept = App::getContainer()->getAttachmentAccepter();
						$blob = $accept->accept($file);
						$this->person->setPictureBlob($blob);
					}
				}
				@unlink($filename);
			}

			$em->persist($this->person);

			if ($mapped_fields->has('phone')) {
				$contact_data = new PersonContactData();
				$contact_data->contact_type = 'phone';
				$contact_data->applyFormData(array(
					'number' => $mapped_fields->get('phone')
				));

				$contact_data->person = $this->person;

				$em->persist($contact_data);
			}

			$em->flush();

			if ($set_email && !$this->person->findEmailAddress($set_email)) {
				$email_obj = $this->person->addEmailAddressString($set_email);
				$em->persist($email_obj);
				$em->flush();
			}

			if ($mapped_fields->has('twitter')) {
				$twitter = $mapped_fields->get('twitter');

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
				", array($this->person->id, $twitter['user_id'], $twitter['screen_name'], $twitter['oauth_token'], $twitter['oauth_token_secret']));

				$has_account = false;
				foreach ($this->person->getContactData('twitter') AS $twitter_details) {
					if ($twitter_details->field_1 == $twitter['screen_name'] || ($twitter_details->field_3 && $twitter_details->field_3 == $twitter['user_id'])) {
						$twitter_details->field_10 = '1';
						$em->persist($twitter_details);
						$has_account = true;
					}
				}

				if (!$has_account) {
					$twitter_details = new \Application\DeskPRO\Entity\PersonContactData();
					$twitter_details->contact_type = 'twitter';
					$twitter_details->person = $this->person;
					$twitter_details->field_1 = $twitter['screen_name'];
					$twitter_details->field_2 = '0';
					$twitter_details->field_3 = $twitter['user_id'];
					$twitter_details->field_10 = '1';
					$em->persist($twitter_details);
				}

				$em->flush();
			}

			// New assoc
			$this->assoc = new PersonUsersourceAssoc();
			$this->assoc['person']            = $this->person;
			$this->assoc['usersource']        = $this->usersource;
			$this->assoc['identity']          = $this->identity->getIdentity();
			$this->assoc['identity_friendly'] = $this->identity->getFriendlyIdentity() ?: $this->identity->getIdentity();
			$this->assoc['data']              = $this->identity->getRawData();
			$em->persist($this->assoc);
			$em->flush();

		#------------------------------
		# The assoc exists
		#------------------------------

		} else {
			$this->person = $this->assoc['person'];

			// Need to make sure the email address on the local account matches that of the
			// identity (it could have been updated).
			if ($mapped_fields->has('email') && $mapped_fields->get('email_confirmed')) {
				if (!$this->person->hasEmailAddress($mapped_fields->get('email'))) {
					$email = App::getEntityRepository('DeskPRO:PersonEmail')->getEmail($mapped_fields->get('email'));
					if (!$email) {
						$email_obj = $this->person->addEmailAddressString($mapped_fields->get('email'));
						$em->persist($email_obj);
						$this->person->primary_email = $email_obj;
						$em->persist($this->person);
						$em->flush();
					}
				}
			}
		}

		// Update custom field data
		App::getSystemService('person_fields_manager')->copyUsersourceData(
			$this->person,
			$this->identity,
			$this->usersource
		);

		$this->person['is_user'] = true;
		$this->person->setLastLoginAt();

		$em->persist($this->person);
		$em->persist($this->assoc);
		$em->flush();
		$em->commit();

		return $this->person;
	}
}
