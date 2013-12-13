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

use Application\DeskPRO\App;

class TaskQueue extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 *
	 */
	protected $id = null;

	protected $runner_class;
	protected $task_data = array();
	protected $date_runnable;
	protected $task_group;
	protected $status = 'queued';
	protected $date_started;
	protected $date_completed;
	protected $error_text = '';
	protected $run_status = '';

	public function __construct()
	{
		$this['date_runnable'] = new \DateTime();
	}

	/**
	 * @param \Application\DeskPRO\Log\Logger|null $logger
	 *
	 * @return \Application\DeskPRO\TaskQueueJob\AbstractJob
	 */
	public function getRunner(\Application\DeskPRO\Log\Logger $logger = null)
	{
		$class = $this['runner_class'];
		return new $class($this['task_data'], $this, $logger);
	}

	public function getTitle()
	{
		return $this->getRunner()->getTitle();
	}

	public function runTask($max_time = 15, \Application\DeskPRO\Log\Logger $logger = null)
	{
		if ($this['status'] == 'completed') {
			throw new \Exception('Task has already been completed');
		}

		if (!$this['date_started']) {
			$this['date_started'] = new \DateTime();
		}
		$this['status'] = 'running';

		try {
			$runner = $this->getRunner($logger);
			$result = $runner->run($max_time);

			if ($result === \Application\DeskPRO\TaskQueueJob\AbstractJob::TASK_COMPLETED) {
				$this['status'] = 'completed';
				$this['date_completed'] = new \DateTime();
			} elseif ($result === \Application\DeskPRO\TaskQueueJob\AbstractJob::TASK_CONTINUING) {
				$this['task_data'] = $runner->getData();
			} else {
				throw new \Exception('Unexpected return value from task; expected TASK_COMPLETED or TASK_CONTINUING');
			}

			return $result;
		} catch (\Exception $e) {
			$this['status'] = 'errored';
			$this['error_text'] = $e->getMessage();
			throw $e;
		}
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TaskQueue';
		$metadata->setPrimaryTable(array(
			'name' => 'task_queue',
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'runner_class', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'runner_class', ));
		$metadata->mapField(array( 'fieldName' => 'task_data', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'task_data', ));
		$metadata->mapField(array( 'fieldName' => 'date_runnable', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_runnable', ));
		$metadata->mapField(array( 'fieldName' => 'task_group', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'task_group', ));
		$metadata->mapField(array( 'fieldName' => 'status', 'type' => 'string', 'length' => 25, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'status', ));
		$metadata->mapField(array( 'fieldName' => 'date_started', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_started', ));
		$metadata->mapField(array( 'fieldName' => 'date_completed', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_completed', ));
		$metadata->mapField(array( 'fieldName' => 'error_text', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'error_text', ));
		$metadata->mapField(array( 'fieldName' => 'run_status', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'run_status', ));

		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
