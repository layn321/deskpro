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
 * @subpackage
 */

namespace Application\DeskPRO\Feedback;

use Orb\Util\Arrays;

use Doctrine\ORM\EntityManager;
use Application\DeskPRO\CustomFields\FeedbackFieldManager;
use Application\DeskPRO\Feedback\UserCategory;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\CustomDataFeedback;
use Application\DeskPRO\Entity\CustomDefFeedback;

class FeedbackCollection
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \Application\DeskPRO\Entity\Feedback[]
	 */
	protected $feedbacks;

	/**
	 * @var \Application\DeskPRO\Entity\CustomDataFeedback[]
	 */
	protected $feedback_data;

	/**
	 * @var \Application\DeskPRO\CustomFields\FeedbackFieldManager
	 */
	protected $feedback_fm;

	/**
	 * @var \Application\DeskPRO\Feedback\UserCategory[]
	 */
	protected $user_cats = array();

	public function __construct(array $feedbacks, EntityManager $em, FeedbackFieldManager $feedback_fm)
	{
		$feedbacks = Arrays::keyFromData($feedbacks, 'id');

		$this->feedbacks = $feedbacks;
		$this->em = $em;
		$this->feedback_fm = $feedback_fm;
	}


	/**
	 * Get the full array of feedback
	 *
	 * @return \Application\DeskPRO\Entity\Feedback[]
	 */
	public function getFeedback()
	{
		return $this->feedbacks;
	}


	/**
	 * Get an array of display data which includes feedback and all associated data with it.
	 *
	 * @return array
	 */
	public function getDisplayArray()
	{
		$data = array();

		foreach ($this->feedbacks as $feedback) {
			$data[$feedback->getId()] = $this->getDisplayArrayForFeedback($feedback);
		}

		return $data;
	}


	/**
	 * Get a display array for a feedback
	 *
	 * @param \Application\DeskPRO\Entity\Feedback $feedback
	 * @return array
	 */
	public function getDisplayArrayForFeedback(Feedback $feedback)
	{
		$custom_data = $this->getDataForFeedback($feedback);
		$user_category = $this->getUserCategory($feedback);

		$data = array(
			'feedback'      => $feedback,
			'custom_data'   => $custom_data,
			'user_category' => $user_category
		);

		return $data;
	}


	/**
	 * Get an array of all custom data on feedback
	 *
	 * @return \Application\DeskPRO\Entity\CustomDataFeedback[]
	 */
	public function getCustomData()
	{
		if ($this->feedback_data !== null) {
			return $this->feedback_data;
		}

		$this->feedback_data = array();

		if ($this->feedbacks) {
			$ids = array_keys($this->feedbacks);

			$results = $this->em->createQuery("
				SELECT d
				FROM DeskPRO:CustomDataFeedback d
				LEFT JOIN d.field AS field
				WHERE d.feedback IN (?0)
			")->setParameter(0, $ids)->execute();

			foreach ($results as $data) {
				if (!isset($this->feedback_data[$data->feedback->getId()])) {
					$this->feedback_data[$data->feedback->getId()] = array();
				}

				$this->feedback_data[$data->feedback->getId()][$data->field->getId()] = $data;
			}
		}

		return $this->feedback_data;
	}


	/**
	 * Get data for a specific feedback
	 *
	 * @param \Application\DeskPRO\Entity\Feedback $feedback
	 * @return \Application\DeskPRO\Entity\CustomDataFeedback[]
	 */
	public function getDataForFeedback(Feedback $feedback)
	{
		$all_data = $this->getCustomData();

		return isset($all_data[$feedback->getId()]) ? $all_data[$feedback->getId()] : array();
	}


	/**
	 * Get the user category title
	 *
	 * @param \Application\DeskPRO\Entity\Feedback $feedback
	 * @return \Application\DeskPRO\Feedback\UserCategory|null
	 */
	public function getUserCategory(Feedback $feedback)
	{
		if (array_key_exists($feedback->getId(), $this->user_cats)) {
			return $this->user_cats[$feedback->getId()];
		}

		$cat_field = $this->feedback_fm->getUserCategoryField();
		if (!$cat_field) {
			$this->user_cats[$feedback->getId()] = null;
			return null;
		}

		$options = $this->feedback_fm->getFieldChildren($cat_field);
		$options = Arrays::keyFromData($options, 'id');

		$custom_data = $this->getDataForFeedback($feedback);
		$chosen = null;
		foreach ($options as $opt) {
			if (isset($custom_data[$opt->getId()])) {
				$chosen = $opt;
			}
		}

		if (!$chosen) {
			$this->user_cats[$feedback->getId()] = null;
			return null;
		}

		if ($chosen->getOption('parent_id')) {
			$chosen_parent = $options[$chosen->getOption('parent_id')];
			$this->user_cats[$feedback->getId()] = new UserCategory($chosen_parent, $chosen);
		} else {
			$this->user_cats[$feedback->getId()] = new UserCategory($chosen);
		}

		return $this->user_cats[$feedback->getId()];
	}
}