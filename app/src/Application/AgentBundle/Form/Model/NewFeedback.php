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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Form\Model;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\Person;

class NewFeedback
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public $title;
	public $category_id;
	public $status_code;
	public $content;

	public $slug;
	public $labels = array();

	public $attach_ids;

	protected $_feedback;

	public function __construct(Person $person_context)
	{
		$this->_person_context = $person_context;

		$this->em = App::getOrm();
	}

	public function save()
	{
		$this->em->beginTransaction();

		$feedback = new Feedback();
		$feedback->person = $this->_person_context;
		$feedback->setStatusCode($this->status_code);
		$feedback->title = $this->title;
		$feedback->content = $this->content ?: '';

		$cat = $this->em->find('DeskPRO:FeedbackCategory', $this->category_id);
		$feedback->category = $cat;
		$this->em->persist($feedback);
		$this->em->flush();

		if ($this->labels) {
			$feedback->getLabelManager()->setLabelsArray($this->labels, $this->em);
			$this->em->flush();
		}

		if ($this->attach_ids) {
			foreach ($this->attach_ids as $aid) {
				$blob = $this->em->getRepository('DeskPRO:Blob')->find($aid);
				if ($blob) {
					$attach = new \Application\DeskPRO\Entity\FeedbackAttachment();
					$attach->person   = $feedback->person;
					$attach->feedback = $feedback;
					$attach->blob     = $blob;

					$feedback->addAttachment($attach);
					$this->em->persist($attach);
				}
			}
			$this->em->flush();
		}

		$this->em->commit();

		$this->_feedback = $feedback;
	}

	public function getFeedback()
	{
		return $this->_feedback;
	}
}
