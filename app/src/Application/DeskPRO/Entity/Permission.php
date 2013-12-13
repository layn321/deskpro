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
use Orb\Util\Numbers;
use Orb\Util\Arrays;
use Orb\Util\Web;

/**
 * Permissions are flags applied groups or specific users.
 *
 */
class Permission extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 */
	protected $id = null;

	/**
	 * The name of the permission
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 * The usergroup this properly belongs to. Note that a permission applies to either
	 * a person or a usergroup, never both.
	 *
	 * @var Application\DeskPRO\Entity\Usergroup
	 */
	protected $usergroup;

	/**
	 * The person this properly belongs to. Note that a permission applies to either
	 * a person or a usergroup, never both.
	 *
	 * @var Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * Any numeric number (ex filesize, flag)
	 *
	 * @var bool
	 */
	protected $value = null;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function __toString()
	{
		$str = '[' . $this->name . ':';
		if ($prop->value !== null) {
			$str .= $prop->data;
		} else {
			$str .= 'NULL';
		}
		$str .= ']';

		return $str;
	}


	/**
	 * Combine an array of permissions into a superduper array of effective permissions.
	 *
	 * @param \Application\DeskPRO\Entity\Permission[] $perms
	 * @return array
	 */
	public static function getEffectivePermissions(array $perms)
	{
		$effective_perms = array();

		foreach ($perms as $perm) {
			$k = $perm->name;
			$v = $perm->value;

			if (!Numbers::isInteger($v)) {
				$v = (int)$v;
			}

			// If it hasnt been set yet, or the one we have is "lower",
			// then take the new value.
			if (!isset($effective_perms[$k]) || (is_int($v) && $effective_perms[$k] < $v)) {
				$effective_perms[$k] = $v;
			}
		}

		return $effective_perms;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array( 'name' => 'permissions', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'value', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'value', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'usergroup', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Usergroup', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'usergroup_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
