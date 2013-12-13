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

use Orb\Util\Strings;
use Orb\Util\Arrays;

/**
 */
class UserRule extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * The unique ID.
	 *
	 * @var int
	 */
	protected $id = null;

	/**
	 * An array of email addres patterns
	 * @var array
	 */
	protected $email_patterns = array();

	/**
	 * @var \Application\DeskPRO\Entity\Organization
	 */
	protected $add_organization;

	/**
	 * @var \Application\DeskPRO\Entity\Usergroup
	 */
	protected $add_usergroup;

	/**
	 * The order in which to runthis source
	 *
	 * @var int
	 */
	protected $run_order = 0;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Set the patterns string which is a number of patterns separated by a newline
	 *
	 * @param $patterns
	 */
	public function setPatternsString($patterns)
	{
		$items = array();

		$patterns = Strings::standardEol($patterns);
		$patterns = explode("\n", $patterns);
		foreach ($patterns as $p) {
			$p = Strings::utf8_strtolower($p);
			$items[] = trim($p);
		}

		$items = Arrays::removeFalsey($items);

		$this->setModelField('email_patterns', $items);
	}


	/**
	 * Get the patterns string
	 *
	 * @return string
	 */
	public function getPatternsString()
	{
		return implode("\n", $this->email_patterns);
	}


	/**
	 * Check if an email address to see if it matches any of the patterns in this rule.
	 *
	 * @param string $email_address
	 * @return string
	 */
	public function isEmailMatch($email_address)
	{
		$email_address = Strings::utf8_strtolower($email_address);

		foreach ($this->email_patterns as $pattern) {
			if (Strings::isStarMatch($pattern, $email_address)) {
				return true;
			}
		}

		return false;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\UserRule';
		$metadata->setPrimaryTable(array( 'name' => 'user_rules', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'email_patterns', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'email_patterns', ));
		$metadata->mapField(array( 'fieldName' => 'run_order', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'run_order', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'add_organization', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Organization', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'add_organization_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'add_usergroup', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Usergroup', 'cascade' => array('persist','merge'), 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'add_usergroup_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
