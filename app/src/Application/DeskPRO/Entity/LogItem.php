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
use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 * General logs
 */
class LogItem extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * The log group, or "file". Different types of logs can be in different groups for
	 * each kind of component (eg. gateways, error_log, etc).
	 *
	 * @var string
	 */
	protected $log_name;

	/**
	 * A log 'session'. A way to group many log items together as part of a whole
	 * procedure.
	 *
	 * @var string
	 */
	protected $session_name = null;

	/**
	 * Any kind of special flag to mark this log item.
	 *
	 * @var string
	 */
	protected $flag = null;

	/**
	 * @var int
	 */
	protected $priority;

	/**
	 * @var string
	 */
	protected $priority_name;

	/**
	 * The log message
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Other data, such as backtrace or debug info
	 *
	 * @var array
	 */
	protected $data = null;

	/**
	 * The date the user was inserted into the system
	 *
	 * @var \DateTime
	 */
	protected $date_created;

	public function __construct()
	{
		$this->setModelField('date_created', new \DateTime());
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\LogItem';
		$metadata->setPrimaryTable(array( 'name' => 'log_items', 'indexes' => array( 'log_name_idx' => array( 'columns' => array( 0 => 'log_name', 1 => 'session_name', ), ), 'flag_idx' => array( 'columns' => array( 0 => 'flag', ), ), ), ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'log_name', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'log_name', ));
		$metadata->mapField(array( 'fieldName' => 'session_name', 'type' => 'string', 'length' => 100, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'session_name', ));
		$metadata->mapField(array( 'fieldName' => 'flag', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'flag', ));
		$metadata->mapField(array( 'fieldName' => 'priority', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'priority', ));
		$metadata->mapField(array( 'fieldName' => 'priority_name', 'type' => 'string', 'length' => 25, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'priority_name', ));
		$metadata->mapField(array( 'fieldName' => 'message', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'message', ));
		$metadata->mapField(array( 'fieldName' => 'data', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'data', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
	}
}
