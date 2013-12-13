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

use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\Person;

class FeedbackModerate implements PersonContextInterface
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
	 * @param \Application\DeskPRO\Entity\Feedback $feedback
	 */
	public function approveFeedback(Feedback $feedback)
	{
		$feedback->status = 'new';

		$this->em->getConnection()->beginTransaction();
		try {
			$this->em->persist($feedback);
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$agent  = $this->person_context;
		$mailer = $this->mailer;

		$this->translator->setTemporaryLanguage($feedback->person->getLanguage(), function() use ($mailer, $feedback, $agent) {
			$vars = array(
				'feedback' => $feedback,
				'agent' => $agent
			);

			$message = $mailer->createMessage();
			$message->setToPerson($feedback->person);
			$message->setTemplate('DeskPRO:emails_user:feedback-approved.html.twig', $vars);
			$message->enableQueueHint();

			$mailer->send($message);
		});
	}


	/**
	 * @param \Application\DeskPRO\Entity\Feedback $feedback
	 * @param string $reason
	 */
	public function disapproveFeedback(Feedback $feedback, $reason = '')
	{
		if (!$reason) {
			$reason = null;
		}

		$this->em->getConnection()->beginTransaction();
		try {
			$this->em->remove($feedback);
			$this->em->flush();
			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$agent  = $this->person_context;
		$mailer = $this->mailer;

		$this->translator->setTemporaryLanguage($feedback->person->getLanguage(), function() use ($mailer, $feedback, $agent, $reason) {
			$vars = array(
				'feedback' => $feedback,
				'agent'    => $agent,
				'reason'   => $reason,
			);

			$message = $mailer->createMessage();
			$message->setToPerson($feedback->person);
			$message->setTemplate('DeskPRO:emails_user:feedback-disapproved.html.twig', $vars);
			$message->enableQueueHint();

			$mailer->send($message);
		});
	}
}