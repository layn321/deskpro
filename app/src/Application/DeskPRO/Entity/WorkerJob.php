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
 * @category Entities
 */

namespace Application\DeskPRO\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Application\DeskPRO\Log\Logger;

/**
 * A worker job is some task that needs to run regularly, or on a schedule.
 *
 */
class WorkerJob extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * Some runners can be configured to run a number of jobs at a time. This
	 * fields groups them into named bundles.
	 *
	 * @var string
	 */
	protected $worker_group = null;

	/**
	 * The name of the job.
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * What it does
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * The PHP classname of the job executor
	 *
	 * @var string
	 */
	protected $job_class;

	/**
	 * Options for the job
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * The most feedbackl interval for this task to run.
	 *
	 */
	protected $interval = 3600;

	/**
	 * The last time this job was run
	 *
	 * @var \DateTime
	 */
	protected $last_run_date = null;

	/**
	 * The last time this job was started
	 *
	 * @var \DateTime
	 */
	protected $last_start_date = null;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Is the task running right now?
	 *
	 * @return bool
	 */
	public function getIsRunning()
	{
		if (!$this->last_start_date) {
			return false;
		}

		if ($this->last_start_date && !$this->last_run_date) {
			return true;
		}

		if ($this->last_start_date->getTimestamp() > $this->last_run_date->getTimestamp()) {
			return true;
		}

		return false;
	}


	/**
	 * Guess if the task has crashed or did crash
	 *
	 * @param int $threshold
	 * @return bool
	 */
	public function getIsCrashed($threshold = 900)
	{
		// Only tasks thata re still running can be crashed
		if (!$this->getIsRunning()) {
			return false;
		}

		$start = $this->last_start_date->getTimestamp();
		$now   = time();

		if ($now - $start > $threshold) {
			return true;
		}

		return false;
	}


	/**
	 * @return int
	 */
	public function getRunningTime()
	{
		// Only tasks thata re still running can be crashed
		if (!$this->getIsRunning()) {
			return 0;
		}

		$start = $this->last_start_date->getTimestamp();
		$now   = time();

		return $now - $start;
	}


	/**
	 * Get the date of the next run
	 *
	 * @return \DateTime
	 */
	public function getNextRunDate()
	{
		if ($this->last_start_date) {
			$d = clone $this->last_start_date;
		} else {
			$d = new \DateTime();
		}

		$d->add(new \DateInterval('PT' . $this->interval . 'S'));
		return $d;
	}


	/**
	 * @return string
	 */
	public function getNextRunRelativeTime()
	{
		$date = $this->getNextRunDate();
		$ts = $date->getTimestamp();

		$diff = $ts - time();

		if ($diff < 1) {
			return 'immediately';
		}

		return \Orb\Util\Dates::secsToReadable($diff, 2, 'short');
	}


	/**
	 * Get interval in readable Enlgish
	 *
	 * @return string
	 */
	public function getIntervalReadable()
	{
		return \Orb\Util\Dates::secsToReadable($this->interval, 5, 'short');
	}


	/**
	 * @param \Application\DeskPRO\Log\Logger $logger
	 * @return \Application\DeskPRO\WorkerProcess\Job\AbstractJob
	 */
	public function createJobObj(Logger $logger, array $options = array())
	{
		$classname = $this->job_class;

		$options = array_merge($this->options, $options);

		$job = new $classname($logger, $options);
		return $job;
	}


	/**
	 * @return bool
	 */
	public function isReady()
	{
		if (!$this->interval OR !$this->last_start_date) {
			return true;
		}

		$cut = time() - $this->interval;
		if ($this->last_start_date->getTimestamp() < $cut) {
			return true;
		}

		return false;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array( 'name' => 'worker_jobs', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'worker_group', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'worker_group', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'description', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'description', ));
		$metadata->mapField(array( 'fieldName' => 'job_class', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'job_class', ));
		$metadata->mapField(array( 'fieldName' => 'options', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'data', ));
		$metadata->mapField(array( 'fieldName' => 'interval', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'run_interval', ));
		$metadata->mapField(array( 'fieldName' => 'last_run_date', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'last_run_date', ));
		$metadata->mapField(array( 'fieldName' => 'last_start_date', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'last_start_date', ));
	}
}
