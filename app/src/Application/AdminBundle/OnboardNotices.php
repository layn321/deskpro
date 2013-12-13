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

namespace Application\AdminBundle;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\DataStore;

class OnboardNotices
{
	/**
	 * @var array
	 */
	protected $dismissed;

	/**
	 * @var array
	 */
	protected $finished;

	/**
	 * @var array
	 */
	protected $newly_finished = array();

	/**
	 * @var array
	 */
	protected $newly_dismissed = array();

	/**
	 * @var bool
	 */
	protected $hide_all = false;

	/**
	 * @var \Application\DeskPRO\Entity\DataStore;
	 */
	protected $dataobj;

	public function __construct()
	{
		$this->dataobj   = App::getOrm()->getRepository('DeskPRO:DataStore')->getByName('admin.onboard_notices', true);
		$this->dismissed = $this->dataobj->getData('dismissed', array());
		$this->finished  = $this->dataobj->getData('finished', array());
		$this->hide_all  = $this->dataobj->getData('hide_all', false);
	}


	/**
	 * Hides all
	 */
	public function hideAll()
	{
		$this->hide_all = true;
	}


	/**
	 * @return bool
	 */
	public function isHideAll()
	{
		return $this->hide_all;
	}


	/**
	 * Reset all notices
	 */
	public function reset()
	{
		$this->hide_all  = false;
		$this->dismissed = array();
		$this->finished  = array();
	}


	/**
	 * Mark a task as complete
	 *
	 * @param string $id
	 */
	public function markDismissed($id)
	{
		$this->dismissed[$id] = time();
		$this->newly_dismissed[] = $id;
	}


	/**
	 * Mark a task as finished
	 *
	 * @param string $id
	 */
	public function markFinished($id)
	{
		$this->finished[$id] = time();
		$this->newly_finished[] = $id;
	}


	/**
	 * Check if a task is finished
	 *
	 * @param string $id
	 * @return bool
	 */
	public function isFinished($id)
	{
		return isset($this->finished[$id]);
	}

	/**
	 * Check if a task is dismissed
	 *
	 * @param string $id
	 * @return bool
	 */
	public function isDismissed($id)
	{
		return isset($this->dismissed[$id]);
	}


	/**
	 * Check if a task is hidden (finished or dismissed)
	 *
	 * @param string $id
	 * @return bool
	 */
	public function isHidden($id)
	{
		return $this->isFinished($id) || $this->isDismissed($id);
	}


	/**
	 * Get all dismissed tasks
	 *
	 * @return array
	 */
	public function getDismissed()
	{
		return array_keys($this->dismissed);
	}

	/**
	 * Get all finished tasks
	 *
	 * @return array
	 */
	public function getFinished()
	{
		return array_keys($this->finished);
	}


	/**
	 * Get all completed tasks
	 *
	 * @return array
	 */
	public function getComplete()
	{
		return array_keys(rray_merge($this->finished, $this->dismissed));
	}


	/**
	 * Count how many items are complete
	 *
	 * @return int
	 */
	public function countComplete()
	{
		return count($this->finished) + count($this->dismissed);
	}


	/**
	 * Save changes to the data object
	 */
	public function save()
	{
		$this->dataobj->setData('dismissed', $this->dismissed);
		$this->dataobj->setData('finished', $this->finished);
		$this->dataobj->setData('hide_all', $this->hide_all);

		App::getOrm()->persist($this->dataobj);
		App::getOrm()->flush($this->dataobj);
	}
}