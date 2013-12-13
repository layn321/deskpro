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
 * @subpackage Comments
 */

namespace Application\DeskPRO\Comments;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\PersonEmail;
use Application\DeskPRO\Entity\PersonEmailValidating;

use Orb\Util\Arrays;

use Symfony\Component\Form;

class NewComment implements \Application\DeskPRO\People\PersonContextInterface
{
	protected $class;
	protected $assignments;

	public $name = '';
	public $email = '';
	public $content = '';

	protected $person_context = null;

	public $require_login = false;

	public function __construct($class, Person $person, array $assignments)
	{
		$this->class = $class;
		$this->person_context = $person;
		$this->assignments = $assignments;
	}

	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}

	public function getPersonContext()
	{
		return $this->person_context;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function save()
	{
		$obj = new $this->class();

		$validating = null;
		$person = null;

		App::getOrm()->beginTransaction();

		$no_validation_required = true;
		switch ($this->class) {
			case 'Application\\DeskPRO\\Entity\\FeedbackComment':  $no_validation_required = $this->person_context->hasPerm('feedback.no_comment_validate'); break;
			case 'Application\\DeskPRO\\Entity\\ArticleComment':   $no_validation_required = $this->person_context->hasPerm('articles.no_comment_validate'); break;
			case 'Application\\DeskPRO\\Entity\\DownloadComment':  $no_validation_required = $this->person_context->hasPerm('downloads.no_comment_validate'); break;
			case 'Application\\DeskPRO\\Entity\\NewsComment':      $no_validation_required = $this->person_context->hasPerm('news.no_comment_validate'); break;
		}

		try {
			if ($this->person_context && !$this->person_context->isGuest()) {
				$person = $this->person_context;
				$email_validating = null;
			} else {
				$email = App::getEntityRepository('DeskPRO:PersonEmail')->getEmail($this->email);
				$email_validating = App::getEntityRepository('DeskPRO:PersonEmailValidating')->getEmail($this->email);

				// Email already exists on an account
				// Means use the same person, but depending on the setting we
				// might require the user to log in (in which case the ticket is a temp ticket for a bit)
				if ($email) {
					if (App::getSetting('core.existing_account_login')) {
						$person = $email->person;
						$person->name = $this->name;
						$this->require_login = true;

					} else {
						$person = $email->person;
						$person->name = $this->name;
					}

					$email_validating = null;

				// Email doesnt exist,
				// Might already be validating, or we might require validation based on the setting
				} elseif (!$no_validation_required || $email_validating) {
					$validating = 'new';
					if (!$email_validating) {
						$person = Person::newContactPerson();
						$person->name = $this->name;
						App::getOrm()->persist($person);

						$email_validating = new PersonEmailValidating();
						$email_validating->email = $this->email;
						$email_validating->person = $person;
						App::getOrm()->persist($email_validating);

					} else {
						$person = $email_validating->person;
					}

				// If we get here, then its a new user and we dont require validation
				// Note a user isnt a "user" at this point, they cant log in etc,
				// no validation just means they dont need to validate to get their ticket reads
				} else {
					$person = Person::newContactPerson();
					$person->name = $this->name;
					App::getOrm()->persist($person);

					$email = new PersonEmail();
					$email->email = $this->email;
					$email->person = $person;
					$person->addEmailAddress($email);
					App::getOrm()->persist($email);

					$email_validating = null;
				}
			}

			$obj->person = $person;
			$obj->name = $person->name;
			if ($person->getPrimaryEmailAddress()) {
				$obj->email = $person->getPrimaryEmailAddress();
			} else if ($email_validating) {
				$obj->email = $email_validating->email;
			}

			$obj->validating = $validating;
			$obj->visitor = App::getSession()->getVisitor();
			$obj->content = $this->content;

			if ($this->require_login) {
				$obj->setStatus('temp');
			} elseif ($validating && !$no_validation_required) {
				$obj->setStatus('user_validating');
			} elseif (!$no_validation_required) {
				$obj->setStatus('validating');
			} else {
				$obj->setStatus('visible');
			}

			foreach ($this->assignments as $k => $v) {
				$obj[$k] = $v;

				// this is the object that owns the comment,
				// we need to increase its comment count by using addComment on it
				if ($k == $obj::OBJ_PROP) {
					$v->addComment($obj);
					App::getOrm()->persist($v);
				}
			}

			App::getOrm()->persist($obj);
			App::getOrm()->flush();

			if ($email_validating) {
				$email_validating->addValidatingContent($this->class, $obj->id);
				App::getOrm()->flush();
			} elseif ($this->require_login) {
				$login_validate_comments = App::getSession()->get('login_validate_comments', array());
				$login_validate_comments[] = array($this->class, $obj->id);
				App::getSession()->set('login_validate_comments', $login_validate_comments);
				App::getSession()->save();
			}

			// Send confirmation email
			if ($email_validating) {
				App::getTranslator()->setTemporaryLanguage($person->getLanguage(), function($tr, $lang) use ($obj, $person, $email_validating, $email, $validating) {

					if ($validating == 'existing') {
						$email_to = $email->email;
					} else {
						$email_to = $email_validating->email;
					}

					$vars = array(
						'comment' => $obj,
						'person' => $person,
						'email_validating' => $email_validating,
						'email' => $email,
						'validating' => $validating,
					);

					$message = App::getMailer()->createMessage();
					$message->setTo($email_to, $person->getDisplayName());
					$message->setTemplate('DeskPRO:emails_user:comment-new.html.twig', $vars);
					$message->enableQueueHint();

					App::getMailer()->send($message);
				});
			}

			$send_notify = new \Application\DeskPRO\Notifications\NewCommentNotification($obj);
			$send_notify->send();

			App::getOrm()->commit();

			if ($obj instanceof \Application\DeskPRO\Entity\FeedbackComment && $obj->status == \Application\DeskPRO\Entity\FeedbackComment::STATUS_VISIBLE) {
				$commenting = new \Application\DeskPRO\Feedback\FeedbackCommenting(App::getContainer(), $person);
				$commenting->newCommentNotify($obj);
			}

			if (isset($this->assignments['article']) && $obj->status == 'visible') {
				$this->assignments['article']->date_last_comment = new \DateTime();
				App::getOrm()->persist($this->assignments['article']);
				App::getOrm()->flush();
			}

			return $obj;

		} catch (\Exception $e) {
			App::getOrm()->rollback();
			throw $e;
		}
	}
}
