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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle;

use Application\DeskPRO\App;
use Application\DeskPRO\DependencyInjection\DeskproContainer;
use Orb\Util\Arrays;

class SetupGuide
{
	/**
	 * @var \Application\DeskPRO\DependencyInjection\DeskproContainer
	 */
	protected $container;

	/**
	 * @var \Application\AdminBundle\Controller\AbstractController
	 */
	protected $controller;

	/**
	 * This is an array of id=>array(info)
	 *
	 * The ID is used in settings to store when something has been completed,
	 * and also used as a phrase ID in the templates to fetch the title of a step.
	 *
	 * @var array
	 */
	protected $tasks = array(
		'incoming_email'     => array('route' => 'admin_emailgateways'),
		'add_agents'         => array('route' => 'admin_agents_new'),
		'add_ticketcategory' => array('route' => 'admin_ticketcats'),
		'add_ticketpriority' => array('route' => 'admin_ticketpris'),
		'add_ticketfield'    => array('route' => 'admin_customdeftickets')
	);

	public function __construct(DeskproContainer $container, $controller)
	{
		$this->container  = $container;
		$this->controller = $controller;

		foreach ($this->tasks as $id => &$task) {
			$task['id'] = $id;
		}
	}


	/**
	 * In the AbstractController the preaction calls this to see if we need to redirect the user
	 * forcefully based on install step.
	 */
	public function preActionHelper($action)
	{
		// Only care when they've already logged in
		if (!$this->container->getSession()->getPerson()->getId()) {
			return null;
		}

		// Dont care about ajax requests
		if ($this->container->getRequest()->isXmlHttpRequest()) {
			return null;
		}

		#------------------------------
		# Setup first
		#------------------------------

		if (!$this->hasDoneInitialSetup()) {
			if (
				!($this->controller instanceof \Application\AdminBundle\Controller\SettingsController)
				&& !($this->controller instanceof \Application\AdminBundle\Controller\LicenseController)
				&& !($this->controller instanceof \Application\AdminBundle\Controller\EmailTransportsController)
				&& !($this->controller instanceof \Application\AdminBundle\Controller\EmailGatewaysController)
			) {
				return $this->controller->redirectRoute('admin_welcome');
			}
		}

		return null;
	}


	/**
	 * Has the user been through the initial setup?
	 *
	 * @return bool
	 */
	public function hasDoneInitialSetup()
	{
		$step = (int)$this->container->getSetting('core.setup_initial');
		if (!$step) {
			return false;
		}

		return true;
	}


	/**
	 * This goes through the tasks and returns its info. If no more tasks are left to complete, then null is returned.
	 *
	 * @return null
	 */
	public function getNextTask()
	{
		foreach ($this->tasks as $t => $info) {
			if (!$this->container->getSetting('core.task_completed_' . $t)) {
				return $info;
			}
		}

		return null;
	}


	/**
	 * Goes through the tasks and marks the next item as finished.
	 *
	 * @return null
	 */
	public function skipNextTask()
	{
		foreach ($this->tasks as $t => $info) {
			if (!$this->container->getSetting('core.task_completed_' . $t)) {
				App::getEntityRepository('DeskPRO:Setting')->updateSetting('core.task_completed_' . $t, time());
				return $info;
			}
		}

		return null;
	}


	/**
	 * Percentage finished
	 *
	 * @return int
	 */
	public function getPercentComplete($skew = 6)
	{
		$done = 0;
		foreach ($this->tasks as $t => $info) {
			if ($this->container->getSetting('core.task_completed_' . $t)) {
				$done++;
			}
		}

		$total = count($this->tasks) + $skew;
		$done += $skew;

		return ceil(($done / $total)*100);
	}


	/**
	 * Check if a task is complete. If it is complete, then whatever status value
	 * set by that tasks controller will be returned (usually a timestamp).
	 *
	 * @param $id
	 * @return bool
	 */
	public function isTaskComplete($id)
	{
		$s = $this->container->getSetting('core.task_completed_' . $id);
		if ($s) {
			return $s;
		}

		return false;
	}


	/**
	 * Gets full array of tasks
	 *
	 * @return
	 */
	public function getTasks()
	{
		return $this->tasks;
	}
}
