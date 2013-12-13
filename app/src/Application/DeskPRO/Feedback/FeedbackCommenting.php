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

namespace Application\DeskPRO\Feedback;

use Application\DeskPRO\App;
use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\FeedbackComment;
use Application\DeskPRO\Entity\Person;

class FeedbackCommenting implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Mail\Mailer
	 */
	protected $mailer;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\Translate\Translate
	 */
	protected $translator;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	/**
	 * @param DeskproContainer $container
	 * @param Person $person
	 */
	public function __construct(DeskproContainer $container, Person $person)
	{
		$this->mailer      = $container->getMailer();
		$this->em          = $container->getEm();
		$this->translator  = $container->getTranslator();

		$this->setPersonContext($person);
	}


	/**
	 * @param \Application\DeskPRO\Entity\Person $person
	 */
	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}


	/**
	 * @param Feedback $feedback
	 * @param \Application\DeskPRO\Feedback\FeedbackComment $comment
	 */
	public function saveComment(Feedback $feedback, FeedbackComment $comment)
	{
		#------------------------------
		# Save the comment
		#------------------------------

		if (!$comment->person) {
			$comment->person = $this->person_context;
		}

		if ($this->person_context->is_agent) {
			$comment->is_reviewed = true;
		}

		$comment->feedback = $feedback;
		$feedback->addComment($comment);

		$this->em->getConnection()->beginTransaction();
		try {
			$this->em->persist($comment);
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		if ($feedback->status == FeedbackComment::STATUS_VISIBLE) {
			$this->newCommentNotify($comment);
		}
	}


	/**
	 * Send notifications to everyone involved in feedback about a new comment
	 *
	 * @param \Application\DeskPRO\Feedback\FeedbackComment $comment
	 */
	public function newCommentNotify(FeedbackComment $comment)
	{
		if (!App::getSetting('user.feedback_notify_comments')) {
			return;
		}

		$feedback = $comment->feedback;

		$to_people_ids = array();

		#------------------------------
		# Send to author
		#------------------------------

		if ($comment->person->getId() != $feedback->person->getId()) {
			$to_people_ids[] = $feedback->person->getId();
		}

		#------------------------------
		# Send to anyone else who posted
		#------------------------------

		$to_people_ids = array_merge($to_people_ids, $this->em->getConnection()->fetchAllCol("
			SELECT DISTINCT(person_id)
			FROM feedback_comments
			WHERE feedback_id = ? AND person_id IS NOT NULL AND status = 'visible' AND person_id != ?
		", array($feedback->getId(), $comment->person ? $comment->person->getId() : 0)));

		$to_people_ids = array_unique($to_people_ids);

		if (!$to_people_ids) {
			return 0;
		}

		#------------------------------
		# Send the emails
		#------------------------------

		$mailer = $this->mailer;

		$people = $this->em->getRepository('DeskPRO:Person')->getByIds($to_people_ids);
		foreach ($people as $person) {
			$this->translator->setTemporaryPersonContext($person, function () use ($feedback, $comment, $mailer, $person) {
				$message = $mailer->createMessage();
				$message->setTemplate('DeskPRO:emails_user:feedback-new-comment.html.twig', array(
					'feedback' => $feedback,
					'comment'  => $comment,
					'person'   => $person
				));

				$message->setToPerson($person);

				$mailer->send($message);
			});
		}

		return count($to_people_ids);
	}
}