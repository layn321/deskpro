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
use Orb\Util\Arrays;

class AgentAlert extends \Application\DeskPRO\Domain\DomainObject
{
	const TARGET_BROWSER = 'browser';
	const TARGET_MOBILE  = 'mobile';

	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var string
	 */
	protected $typename;

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var bool
	 */
	protected $is_dismissed = false;

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

	/**
	 * Get data. Specify $target to optionally get only data to deliver to a $target type.
	 *
	 * @param string|null $target
	 * @return array
	 */
	public function getData($target = null)
	{
		if ($target === null || empty($this->data['@target_maps']) || empty($this->data['@target_maps'][$target])) {
			return $this->data;
		}

		$ret = Arrays::reduceToKeys($this->data, $this->data['@target_maps'][$target], null);
		if (!empty($this->data['@target_maps']['default'])) {
			$ret = array_merge($ret, Arrays::reduceToKeys($this->data, $this->data['@target_maps']['default'], null));
		}

		return $ret;
	}


	/**
	 * Add a target map.
	 *
	 * A target map specifies the keys in data that sholud be returned for a specific alert target.
	 * For example, if this is a newticket alert then we might want to deliver different data
	 * to the client depending on if the client is a browser or if they are a mobile device.
	 *
	 * @param string $target
	 * @param array $keys
	 */
	public function addTargetMap($target, array $keys)
	{
		if (!isset($this->data['@target_maps'])) {
			$this->data['@target_maps'] = array();
		}

		$this->data['@target_maps'][$target] = $keys;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->setPrimaryTable(array(
			'name' => 'agent_alerts',
			'indexes' => array(
				'date_created_idx' => array('columns' => array('date_created'))
			)
		));
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'typename', 'type' => 'string', 'length' => 255, 'nullable' => false, 'columnName' => 'typename', ));
		$metadata->mapField(array( 'fieldName' => 'data', 'type' => 'array', 'nullable' => false, 'columnName' => 'data', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_created' ));
		$metadata->mapField(array( 'fieldName' => 'is_dismissed', 'type' => 'boolean', 'nullable' => false, 'columnName' => 'is_dismissed', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ));
	}
}
