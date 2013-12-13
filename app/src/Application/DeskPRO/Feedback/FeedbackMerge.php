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
 * @category Tickets
 */

namespace Application\DeskPRO\Feedback;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\FeedbackComment;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\People\PersonContextInterface;

use Orb\Util\Arrays;

/**
 * Handles merging of one feedback into the other
 */
class FeedbackMerge implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Application\DeskPRO\Entity\Feedback
	 */
	protected $feedback;

	/**
	 * @var \Application\DeskPRO\Entity\Feedback
	 */
	protected $other_feedback;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @throws \InvalidArgumentException
	 * @param \Application\DeskPRO\Entity\Person $person_performer
	 * @param \Application\DeskPRO\Entity\Feedback $feedback         The base feedback, this is the one that will still exist at the end
	 * @param \Application\DeskPRO\Entity\Feedback $other_feedback   The other feedback, the one that will be merged into $feedback and then deleted
	 */
	public function __construct(Person $person_performer, Feedback $feedback, Feedback $other_feedback)
	{
		$this->em = App::getOrm();

		$this->feedback = $feedback;
		$this->other_feedback = $other_feedback;
		$this->setPersonContext($person_performer);

		if ($feedback->getId() == $other_feedback->getId()) {
			throw new \InvalidArgumentException("You cannot merge an feedback with itself");
		}
	}

	public function setPersonContext(Person $person)
	{
		$this->person = $person;
	}

	public function checkPersonPermission()
	{
		return true;
	}

	public function merge()
	{
		if (!$this->checkPersonPermission()) {
			throw new \DomainException('User does not have permission to merge these tickets');
		}

		$this->em->beginTransaction();

		try {

			$this->mergeProps();
			$this->mergeVotes();
			$this->mergeComments();
			$this->mergeDescription();
			$this->em->persist($this->feedback);
			$this->em->flush();

			$this->em->remove($this->other_feedback);
			$this->em->flush();

			$this->em->commit();

		} catch (\Exception $e) {
			$this->em->rollback();

			throw $e;
		}

		return true;
	}

	protected function mergeProps()
	{
		if (!$this->feedback->category && $this->other_feedback->category) {
			$this->feedback->category = $this->other_feedback->category;
		}
		$this->feedback->view_count = $this->feedback->view_count + $this->other_feedback->view_count;
	}

	protected function mergeVotes()
	{
		$votes = App::getEntityRepository('DeskPRO:Rating')->getRatingsFor('feedback', $this->feedback->id);
		$other_votes = App::getEntityRepository('DeskPRO:Rating')->getRatingsFor('feedback', $this->other_feedback->id);

		$finished_votes = $votes;

		// Votes are ordered by id
		// Create a map of users and visitors so we can easily match conflicts
		$map_fn = function(&$map, $field) use ($votes) {
			foreach ($votes as $id => $v) {
				if (isset($v[$field]) && $v[$field]) {
					$map[$v[$field]] = $id;
				}
			}
		};

		$map_votes_person  = array();
		$map_votes_visitor = array();

		$map_fn($map_votes_person, 'person_id');
		$map_fn($map_votes_visitor, 'visitor_id');

		// Now go over all other votes to add them or merge them
		foreach ($other_votes as $v) {
			if ($v['person_id'] && isset($map_votes_person[$v['person_id']])) {
				// person already voted
				$this->em->remove($v);
			} elseif ($v['visitor_id'] && isset($map_votes_visitor[$v['visitor_id']])) {
				// same visitor voted
				$this->em->remove($v);
			} else {
				// Move the vote over
				$this->feedback->addRating($v);
				$this->em->persist($v);
				$finished_votes[] = $v;
			}
		}

		$this->feedback->recalculateVoteStats($finished_votes);
	}

	public function mergeComments()
	{
		foreach ($this->other_feedback->comments as $comment) {
			$comment->feedback = $this->feedback;
			$this->em->persist($comment);
		}
	}

	public function mergeDescription()
	{
		$comment = new FeedbackComment();

		$comment->person = $this->other_feedback->person;
		$comment->content = $this->other_feedback->content;
		$comment->date_created = $this->other_feedback->date_created;

		$this->feedback->addComment($comment);

		$this->em->persist($comment);
	}
}
