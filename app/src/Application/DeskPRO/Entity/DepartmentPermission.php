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

/**
 * Stores who has access to departments
 *
 */
class DepartmentPermission extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var \Application\DeskPRO\Entity\Department
	 */
	protected $department = null;

	/**
	 * The connected usergroup. If this is set, then person cannot be set.
	 *
	 * @var \Application\DeskPRO\Entity\Department
	 */
	protected $usergroup = null;

	/**
	 * The connected person. If this is set, then usergroup cannot be set.
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 */
	protected $app;

	/**
	 * The name of the permission
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * Any numeric number (ex filesize, flag)
	 *
	 * @var integer
	 */
	protected $value = null;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	public function setUsergroup($ug)
	{
		$this->setModelField('usergroup', $ug);

		if ($ug !== null) {
			$this->person = null;
		}
	}

	public function setPerson($p)
	{
		$this->setModelField('person', $p);

		if ($p !== null) {
			$this->usergroup = null;
		}
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\DepartmentPermission';
		$metadata->setPrimaryTable(array( 'name' => 'department_permissions', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'app', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'app', ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'value', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'value', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'department', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Department', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'department_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'usergroup', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Usergroup', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'usergroup_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
