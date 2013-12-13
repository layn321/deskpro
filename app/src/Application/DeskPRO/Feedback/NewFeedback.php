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

namespace Application\DeskPRO\Feedback;

use Application\DeskPRO\App;
use Application\DeskPRO\EmailGateway\PersonFromEmailProcessor;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\PersonEmailValidating;
use Application\DeskPRO\Entity\Visitor;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\Rating;

/**
 * New feedback acts as the processor and domain object for a newfeedback form
 */
class NewFeedback implements \Application\DeskPRO\People\PersonContextInterface
{
	protected $mode = 'default';

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	public $require_login = false;
	public $person_name = '';
	public $person_email = '';
	public $category_id = 0;
	public $title = '';
	public $content = '';
	public $attach_blobs = array();
	public $custom_fields = array();

	public function __construct(Visitor $visitor = null, Person $person = null)
	{
		$this->em = App::getOrm();

		$this->visitor = $visitor;

		if ($person && !$person->isGuest()) {
			$this->person_name = $person->name;
			if ($person->primary_email) {
				$this->person_email = $person->primary_email_address;
			}
		}

		if ($visitor && $visitor->name && !$this->person_name) {
			$this->person_name = $visitor->name;
		}
		if ($visitor && $visitor->email && !$this->person_email) {
			$this->person_email = $visitor->email;
		}
	}

	public function enableWidgetMode()
	{
		$this->mode = 'widget';
	}

	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}

	public function getPersonContext()
	{
		return $this->person_context;
	}


	/**
	 * @param array $attach_ids
	 */
	public function setAttachBlobs(array $attach_auth_ids)
	{
		foreach ($attach_auth_ids as $auth_id) {
			$blob = $this->em->getRepository('DeskPRO:Blob')->getByAuthId($auth_id);
			if ($blob) {
				$this->attach_blobs[] = $blob;
			}
		}
	}


	public function save()
	{
		$this->em->getConnection()->beginTransaction();

		try {

			#------------------------------
			# Handle the person first
			#------------------------------

			$status = 'visible';
			$validating = null;

			$person = null;
			$email = null;
			$email_validating = null;

			if ($this->person_context->isGuest()) {

				$person_processor = new PersonFromEmailProcessor();
				$person = $person_processor->findPersonByEmailAddress($this->person_email);

				if ($person) {
					$email = $person->getPrimaryEmail();
					$email_validating = null;
				} else {
					$person = null;
					$email = $this->em->getRepository('DeskPRO:PersonEmail')->getEmail($this->person_email);
					$email_validating = $this->em->getRepository('DeskPRO:PersonEmailValidating')->getEmail($this->person_email);
				}

				// Email already exists on an account
				// Means use the same person, but depending on the setting we
				// might require the user to log in (in which case the ticket is a temp ticket for a bit)
				if ($email) {
					$person = $email->person;
					if ($this->person_name) {
						$person->name = $this->person_name;
					}
					if (App::getSetting('core.existing_account_login') && $person->is_user) {
						$this->require_login = true;
					}
					$email_validating = null;

				// Email doesnt exist,
				// Might already be validating, or we might require validation based on the setting
				} elseif (!$this->person_context->hasPerm('feedback.no_submit_validate') || $email_validating) {
					$validating = 'new';
					if (!$email_validating) {
						$person = Person::newContactPerson();
						if ($this->person_name) {
							$person->name = $this->person_name;
						}
						$this->em->persist($person);

						$email_validating = new PersonEmailValidating();
						$email_validating->email = $this->person_email;
						$email_validating->person = $person;
						$this->em->persist($email_validating);

					} else {
						$person = $email_validating->person;
					}

				// If we get here, then its a new user and we dont require validation
				// Note a user isnt a "user" at this point, they cant log in etc,
				// no validation just means they dont need to validate to get their ticket reads
				} else {
					$person = Person::newContactPerson();
					if ($this->person_name) {
						$person->name = $this->person_name;
					}
					$this->em->persist($person);

					$email = new PersonEmail();
					$email->email = $this->person_email;
					$email->person = $person;
					$person->addEmailAddress($email);
					$this->em->persist($email);

					$email_validating = null;
				}
			} else {
				$person = $this->person_context;

				if ($this->person_name) {
					$person->name = $this->person_name;
					$this->em->persist($person);
				}
			}

			$this->em->flush();

			$feedback = new Feedback();

			$feedback['title']        = $this->title;
			$feedback['content']      = nl2br(htmlspecialchars($this->content, \ENT_QUOTES, 'UTF-8'));
			$feedback['category_id']  = $this->category_id;
			$feedback['status']       = Feedback::STATUS_NEW;
			$feedback['date_created'] = new \DateTime();
			$feedback['validating']   = $validating;
			$feedback['person']       = $person;

			if ($this->require_login && $this->mode != 'widget') {
				$feedback->setStatusCode('hidden.temp');
			} elseif ($validating) {
				$feedback->setStatusCode('hidden.user_validating');
			} elseif (!$this->person_context->hasPerm('feedback.no_submit_validate')) {
				$feedback->setStatusCode('hidden.validating');
			}

			$this->em->persist($feedback);

			if ($this->custom_fields) {
				$cf_man = App::getSystemService('FeedbackFieldsManager');
				$cf_man->saveFormToObject($this->custom_fields, $feedback);
			}

			foreach ($this->attach_blobs as $blob) {
				$attach = new \Application\DeskPRO\Entity\FeedbackAttachment();
				$attach->person   = $person;
				$attach->feedback = $feedback;
				$attach->blob     = $blob;

				$feedback->addAttachment($attach);
				$this->em->persist($attach);
			}

			$this->em->persist($feedback);
			$this->em->flush();

			$rating = Rating::create(1);
			$rating->person = $person;
			if ($this->visitor) {
				$rating->visitor = $this->visitor;
			}
			$feedback->addRating($rating);

			$this->em->persist($rating);
			$this->em->persist($feedback);

			$this->em->flush();

			if ($email_validating) {
				$email_validating->addValidatingContent('DeskPRO:Feedback', $feedback->id);
				$this->em->flush();
			}

			$this->em->commit();

			// Send confirmation email
			if ($feedback->status_code != 'hidden.temp') {
				App::getTranslator()->setTemporaryLanguage($person->getLanguage(), function($tr, $lang) use ($feedback, $person, $email_validating, $email, $validating) {

					if ($validating == 'existing') {
						$email_to       = $email->email;
					} elseif ($validating == 'new') {
						$email_to       = $email_validating->email;
					} else {
						$email_to       = $person->primary_email_address;
					}

					$vars = array(
						'feedback' => $feedback,
						'person' => $person,
						'email_validating' => $email_validating,
						'email' => $email,
						'validating' => $validating,
					);

					$message = App::getMailer()->createMessage();
					$message->setTo($email_to, $person->getDisplayName());
					$message->setTemplate('DeskPRO:emails_user:feedback-new.html.twig', $vars);
					$message->enableQueueHint();

					App::getMailer()->send($message);
				});
			}

		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		return $feedback;
	}
}
