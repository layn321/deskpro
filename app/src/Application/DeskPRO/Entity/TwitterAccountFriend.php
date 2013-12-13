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
use Application\DeskPRO\Entity;

/**
 * Twitter Account following a User
 */
class TwitterAccountFriend extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterAccount
	 */
	protected $account;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterUser
	 */
	protected $user;

	/**
	 * @return integer
	 */
	public function getAccountId()
	{
		if (null !== $this->account) {
			return $this->account->getId();
		}

		return 0;
	}

	/**
	 * @param integer $id
	 */
	public function setAccountId($id)
	{
		if ($id && $account = App::getOrm()->getRepository('DeskPRO:TwitterAccount')->find($id)) {
			$this->account = $account;
		} else {
			$this->account = null;
		}
	}

	/**
	 * @return integer
	 */
	public function getUserId()
	{
		if (null !== $this->user) {
			return $this->user->getId();
		}

		return 0;
	}

	/**
	 * @param integer $id
	 */
	public function setUserId($id)
	{
		if ($id && $user = App::getOrm()->getRepository('DeskPRO:TwitterUser')->find($id)) {
			$this->user = $user;
		} else {
			$this->user = null;
		}
	}



	############################################################################
	# Doctrine Metadata
	############################################################################


	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TwitterAccountFriend';
		$metadata->setPrimaryTable(array( 'name' => 'twitter_accounts_friends', 'uniqueConstraints' => array( 'account_user_idx' => array( 'columns' => array( 0 => 'account_id', 1 => 'user_id', ), ), ), ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'account', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccount', 'mappedBy' => NULL, 'inversedBy' => null, 'joinColumns' => array( 0 => array( 'name' => 'account_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'user', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUser', 'mappedBy' => NULL, 'inversedBy' => null, 'joinColumns' => array( 0 => array( 'name' => 'user_id', 'referencedColumnName' => 'id', 'nullable' => false, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
