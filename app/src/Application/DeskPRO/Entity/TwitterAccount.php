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

use Application\DeskPRO\Entity;

/**
 * A Twitter Account contains twitter username and accesstoken
 *
 */
class TwitterAccount extends \Application\DeskPRO\Domain\DomainObject
{
	const DEFAULT_LIMIT = 50;

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $oauth_token;

	/**
	 * @var string
	 */
	protected $oauth_token_secret;

	/**
	 * @var int
	 */
	protected $last_processed_id = 0;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterUser
	 */
	protected $user;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $friends;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $followers;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $searches;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * )
	 */
	protected $persons;

	protected $_cache = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->friends = new \Doctrine\Common\Collections\ArrayCollection();
		$this->followers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->searches = new \Doctrine\Common\Collections\ArrayCollection();
		$this->persons = new \Doctrine\Common\Collections\ArrayCollection();
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

	/**
	 * Retrieve a list of Twitter users this account follows.
	 *
	 * @param Boolean $cache (optional)
	 * @return array
	 */
	public function getFriendIds($cache = true)
	{
		if ($cache && isset($this->_cache['friend_ids'])) {
			return $this->_cache['friend_ids'];
		}

		$this->_cache['friend_ids'] = App::getDb()->fetchAllCol("
			SELECT user_id
			FROM twitter_accounts_friends
			WHERE account_id = ?
			ORDER BY id DESC
		", array($this['id']));

		if (!is_array($this->_cache['friend_ids'])) {
			$this->_cache['friend_ids'] = array($this->_cache['friend_ids']);
		}

		return $this->_cache['friend_ids'];
	}

	/**
	 * Retrieve a list of Twitter users following this account.
	 *
	 * @param Boolean $cache (optional)
	 * @return array
	 */
	public function getFollowerIds($cache = true)
	{
		if ($cache && isset($this->_cache['follower_ids'])) {
			return $this->_cache['follower_ids'];
		}

		$this->_cache['follower_ids'] = App::getDb()->fetchAllCol("
			SELECT user_id
			FROM twitter_accounts_followers
			WHERE account_id = ?
			ORDER BY id DESC
		", array($this['id']));

		if (!is_array($this->_cache['follower_ids'])) {
			$this->_cache['follower_ids'] = array($this->_cache['follower_ids']);
		}

		return $this->_cache['follower_ids'];
	}

	/**
	 * Retrieve a list of associated Person ids.
	 *
	 * @param boolean $cache
	 *
	 * @return array
	 */
	public function getPersonIds($cache = true)
	{
		if (isset($this->_cache['person_ids'])) {
			return $this->_cache['person_ids'];
		}

		$this->_cache['person_ids'] = App::getDb()->fetchAllCol("
			SELECT person_id
			FROM twitter_accounts_person
			WHERE account_id = ?
		", array($this['id']));

		if (!is_array($this->_cache['person_ids'])) {
			$this->_cache['person_ids'] = array($this->_cache['person_ids']);
		}

		return $this->_cache['person_ids'];
	}

	public function hasPerson($agent_id)
	{
		if ($agent_id instanceof Person) {
			$agent_id = $agent_id->id;
		}

		$person_ids = $this->getPersonIds();
		return in_array($agent_id, $person_ids);
	}

	public function getNewFollowers($page = 1, $limit = self::DEFAULT_LIMIT)
	{
		$page = max(1, intval($page));
		$offset = ($page - 1) * $limit;

		$query = App::getOrm()->createQuery("
			SELECT f, u
			FROM DeskPRO:TwitterAccountFollower f
			INNER JOIN f.user u
			WHERE f.account = :account_id
				AND f.is_archived = false
			ORDER BY f.follow_order DESC
		");

		$followers = $query
			->setMaxResults($limit)
			->setFirstResult($offset)
			->setParameters(array('account_id' => $this->getId()))
			->execute();

		return $followers;
	}

	public function countNewFollowers($cache = true)
	{
		if ($cache && isset($this->_cache['count_new_followers'])) {
			return $this->_cache['count_new_followers'];
		}

		$query = App::getOrm()->createQuery("
			SELECT COUNT(f.id)
			FROM DeskPRO:TwitterAccountFollower f
			WHERE f.account = :account_id
				AND f.is_archived = false
			ORDER BY f.follow_order DESC
		");

		$this->_cache['count_new_followers'] = $query
			->setParameters(array('account_id' => $this->getId()))
			->getSingleScalarResult();

		return $this->_cache['count_new_followers'];
	}

	public function getFollowers($page = 1, $limit = self::DEFAULT_LIMIT)
	{
		$page = max(1, intval($page));
		$offset = ($page - 1) * $limit;

		$query = App::getOrm()->createQuery("
			SELECT f, u
			FROM DeskPRO:TwitterAccountFollower f
			INNER JOIN f.user u
			WHERE f.account = :account_id
			ORDER BY f.follow_order DESC
		");

		$followers = $query
			->setMaxResults($limit)
			->setFirstResult($offset)
			->setParameters(array('account_id' => $this->getId()))
			->execute();

		return $followers;
	}

	public function countFollowers($cache = true)
	{
		if ($cache && isset($this->_cache['count_followers'])) {
			return $this->_cache['count_followers'];
		}

		$query = App::getOrm()->createQuery("
			SELECT COUNT(f.id)
			FROM DeskPRO:TwitterAccountFollower f
			WHERE f.account = :account_id
		");

		$this->_cache['count_followers'] = $query
			->setParameters(array('account_id' => $this->getId()))
			->getSingleScalarResult();

		return $this->_cache['count_followers'];
	}

	public function countFollowing($cache = true)
	{
		if ($cache && isset($this->_cache['count_following'])) {
			return $this->_cache['count_following'];
		}

		$query = App::getOrm()->createQuery("
			SELECT COUNT(f.id)
			FROM DeskPRO:TwitterAccountFriend f
			WHERE f.account = :account_id
		");

		$this->_cache['count_following'] = $query
			->setParameters(array('account_id' => $this->getId()))
			->getSingleScalarResult();

		return $this->_cache['count_following'];
	}

	public function getFollowing($page = 1, $limit = self::DEFAULT_LIMIT)
	{
		$page = max(1, intval($page));
		$offset = ($page - 1) * $limit;

		$query = App::getOrm()->createQuery("
			SELECT f, u
			FROM DeskPRO:TwitterAccountFriend f
			INNER JOIN f.user u
			WHERE f.account = :account_id
		");

		$followers = $query
			->setMaxResults($limit)
			->setFirstResult($offset)
			->setParameters(array('account_id' => $this->getId()))
			->execute();

		return $followers;
	}

	public function getTwitterApi()
	{
		$api = \Application\DeskPRO\Service\Twitter::getAgentTwitterApi();
		if ($this->oauth_token && $this->oauth_token_secret) {
			$api->setToken($this->oauth_token, $this->oauth_token_secret);
		}

		return $api;
	}

	public function verifyCredentials(&$message = null, &$code = null)
	{
		try {
			$result = $this->getTwitterApi()->get_applicationRate_limit_status(array('resources' => 'application'));
			if ($result->rate_limit_context) {
				return true;
			}
		} catch (\Exception $e) {
			$data = @json_decode($e->getMessage(), true);
			if (isset($data['errors'][0]['message'])) {
				$message = $data['errors'][0]['message'];
			}
			if (isset($data['errors'][0]['code'])) {
				$code = $data['errors'][0]['code'];
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
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TwitterAccount';
		$metadata->setPrimaryTable(array( 'name' => 'twitter_accounts', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'oauth_token', 'type' => 'string', 'length' => 4000, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'oauth_token', 'dpApi' => false, 'dpqlAccess' => false));
		$metadata->mapField(array( 'fieldName' => 'oauth_token_secret', 'type' => 'string', 'length' => 4000, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'oauth_token_secret', 'dpApi' => false, 'dpqlAccess' => false ));
		$metadata->mapField(array( 'fieldName' => 'last_processed_id', 'type' => 'bigint', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'last_processed_id', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'user', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUser', 'mappedBy' => NULL, 'inversedBy' => 'account', 'joinColumns' => array( 0 => array( 'name' => 'user_id', 'referencedColumnName' => 'id', 'unique' => true, 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'friends', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccountFriend', 'mappedBy' => 'account',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'followers', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccountFollower', 'mappedBy' => 'account',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'searches', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccountSearch', 'mappedBy' => 'account',  ));
		$metadata->mapManyToMany(array( 'fieldName' => 'persons', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'joinTable' => array( 'name' => 'twitter_accounts_person', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'account_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), ));
	}
}
