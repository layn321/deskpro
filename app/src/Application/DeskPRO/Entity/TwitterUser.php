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
 * Twitter User
 *
 */
class TwitterUser extends \Application\DeskPRO\Domain\DomainObject
{
	const TIMELINE_UPDATE_FREQUENCY = 900;
	const FOLLOW_UPDATE_FREQUENCY = 3600;
	const PROFILE_UPDATE_FREQUENCY = 86400;

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $screen_name;

	/**
	 * @var string
	 */
	protected $profile_image_url = '';

	/**
	 * @var string
	 */
	protected $language = '';

	/**
	 * @var Boolean
	 */
	protected $is_protected = false;

	/**
	 * @var Boolean
	 */
	protected $is_verified = false;

	/**
	 * @var string
	 */
	protected $location = '';

	/**
	 * @var string
	 */
	protected $description = '';

	/**
	 * @var string
	 */
	protected $url = '';

	/**
	 * @var Boolean
	 */
	protected $is_geo_enabled = false;

	/**
	 * @var bool
	 */
	protected $is_stub = false;

	/**
	 * @var \DateTime|null
	 */
	protected $last_timeline_update;

	/**
	 * @var \DateTime|null
	 */
	protected $last_profile_update;

	/**
	 * @var \DateTime|null
	 */
	protected $last_follow_update;

	/**
	 * @var int
	 */
	protected $followers_count = 0;

	/**
	 * @var int
	 */
	protected $friends_count = 0;

	/**
	 * @var int
	 */
	protected $statuses_count = 0;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $statuses;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $replies;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $mentions;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $messages;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $friends;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $followers;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterAccount
	 */
	protected $account;

	protected static $_stubs = array();
	protected static $_processing_stubs = false;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->statuses = new \Doctrine\Common\Collections\ArrayCollection();
		$this->replies  = new \Doctrine\Common\Collections\ArrayCollection();
		$this->mentions = new \Doctrine\Common\Collections\ArrayCollection();
		$this->messages = new \Doctrine\Common\Collections\ArrayCollection();

		$this->friends = new \Doctrine\Common\Collections\ArrayCollection();
		$this->followers = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @return Boolean
	 */
	public function isProtected()
	{
		return (Boolean) $this->is_protected;
	}

	/**
	 * @return Boolean
	 */
	public function isVerified()
	{
		return (Boolean) $this->is_verified;
	}

	/**
	 * @return Boolean
	 */
	public function isGeoEnabled()
	{
		return (Boolean) $this->is_geo_enabled;
	}

	public function getProfileImageUrl($size = 'normal')
	{
		if ($size == 'normal') {
			return $this->profile_image_url;
		} else {
			return str_replace('_normal.', ($size ? "_$size" : '') . '.', $this->profile_image_url);
		}
	}

	public function getStatuses($page = 1, $per_page = 25)
	{
		if ($page == 1) {
			if (!$this->last_timeline_update
				|| $this->last_timeline_update->getTimestamp() < time() - self::TIMELINE_UPDATE_FREQUENCY
				|| (App::getSetting('core.twitter_last_cleanup') && $this->last_timeline_update->getTimestamp() < App::getSetting('core.twitter_last_cleanup'))
			) {
				$em = App::getOrm();

				$account = $em->getRepository('DeskPRO:TwitterAccount')->getFirst();
				if ($account) {
					// need to grab the first api we can get
					$api = $account->getTwitterApi();
					try {
						$response = $api->get_statusesUser_timeline(array(
							'user_id' => $this->id,
							'count' => 100
						));
						$twitter_service = new \Application\DeskPRO\Service\Twitter();
						foreach ($response AS $status) {
							$twitter_service->processStatus($api, $status, true, 1);
						}
					} catch (\EpiTwitterException $e) {
					} catch (\EpiOAuthException $e) {
					}

					$this['last_timeline_update'] = new \DateTime();
					$em->persist($this);
					$em->flush();
				}
			}
		}

		return App::getOrm()->getRepository('DeskPRO:TwitterStatus')->findOutgoingForUserId($this->id, true, 'desc', $per_page, $page);
	}

	public function getMessages()
	{
		$from_user_ids = array();
		foreach (App::getOrm()->getRepository('DeskPRO:TwitterAccount')->getAllForPerson() AS $account) {
			$from_user_ids[] = $account->user->id;
		}

		return App::getOrm()->getRepository('DeskPRO:TwitterStatus')->findMessagesForUserId($this->id, $from_user_ids, true, 'desc');
	}

	public function getMentions()
	{
		$from_user_ids = array();
		foreach (App::getOrm()->getRepository('DeskPRO:TwitterAccount')->getAllForPerson() AS $account) {
			$from_user_ids[] = $account->user->id;
		}

		return App::getOrm()->getRepository('DeskPRO:TwitterStatus')->findMentionsForUserId($this->id, $from_user_ids, true, 'desc');
	}

	public function getFollowers($page = 1, $per_page = 25)
	{
		return App::getOrm()->getRepository('DeskPRO:TwitterUserFollower')->getFollowersForUser($this, $page, $per_page);
	}

	public function getFriends($page = 1, $per_page = 25)
	{
		return App::getOrm()->getRepository('DeskPRO:TwitterUserFriend')->getFriendsForUser($this, $page, $per_page);
	}

	public function countAccountInteractions(TwitterAccount $account)
	{
		$repo = App::getOrm()->getRepository('DeskPRO:TwitterStatus');

		return $repo->countMessagesForUserId($this->id, array($account->user->id), true)
			+ $repo->countMentionsForUserId($this->id, array($account->user->id), true);
	}

	public function getVerifiedPeople()
	{
		$output = array();
		$results = App::getOrm()->createQuery("
			SELECT tu, p
			FROM DeskPRO:PersonTwitterUser tu
			INNER JOIN tu.person p
			WHERE tu.screen_name = ?0
				AND tu.is_verified = true
			ORDER BY p.name
		")->execute(array($this->screen_name));
		foreach ($results AS $result) {
			$output[] = $result->person;
		}

		return $output;
	}

	public function getPossiblePeople()
	{
		$output = array();
		$results = App::getOrm()->createQuery("
			SELECT tu, p
			FROM DeskPRO:PersonTwitterUser tu
			INNER JOIN tu.person p
			WHERE tu.screen_name = ?0
				AND tu.is_verified = false
			ORDER BY p.name
		")->execute(array($this->screen_name));
		foreach ($results AS $result) {
			$output[] = $result->person;
		}

		return $output;
	}

	public function getPossibleOrganizations()
	{
		$output = array();
		$results = App::getOrm()->createQuery("
			SELECT tu, o
			FROM DeskPRO:OrganizationTwitterUser tu
			INNER JOIN tu.organization o
			WHERE tu.screen_name = ?0
				AND tu.is_verified = false
			ORDER BY o.name
		")->execute(array($this->screen_name));
		foreach ($results AS $result) {
			$output[] = $result->organization;
		}

		return $output;
	}

	protected static $_stub_read = array(
		'id' => true,
		'is_stub' => true,
		'last_timeline_update' => true,
		'last_profile_update' => true,
		'last_follow_update' => true,
	);

	public function offsetGet($offset)
	{
		if (self::$_processing_stubs) {
			return parent::offsetGet($offset);
		}

		if ($this->is_stub && isset(self::$_stub_read[$offset])) {
			return parent::offsetGet($offset);
		}

		if ($this->is_stub && self::$_stubs) {
			if (!empty($this->$offset)) {
				return $this->$offset;
			}

			self::$_processing_stubs = true;
			$em = App::getOrm();
			$account = $em->getRepository('DeskPRO:TwitterAccount')->getFirst();
			if ($account) {
				// need to grab the first api we can get
				$api = $account->getTwitterApi();

				$id_sets = array_chunk(array_keys(self::$_stubs), 100);
				foreach ($id_sets AS $ids) {
					try {
						$response = $api->post_usersLookup(array(
							'user_id' => implode(',', $ids)
						));
						foreach ($response AS $user) {
							if (isset(self::$_stubs[$user->id_str])) {
								$entity = self::$_stubs[$user->id_str];
								$entity->ensureDefaultPropertyChangedListener();
								$entity->updateFromJson($user);
								$em->persist($entity);
								unset(self::$_stubs[$user->id_str]);
							}
						}
						foreach ($ids AS $id) {
							if (isset(self::$_stubs[$id])) {
								// can't get information for this user, so un-stub
								$entity = self::$_stubs[$id];
								$entity->ensureDefaultPropertyChangedListener();
								$entity['name']              = 'Unknown';
								$entity['is_stub']           = false;
								$entity['last_profile_update'] = new \DateTime();

								$em->persist($entity);
								unset(self::$_stubs[$id]);
							}
						}
					} catch (\EpiTwitterException $e) {
						break;
					} catch (\EpiOAuthException $e) {
						break;
					}
					// catches prevent any twitter errors from breaking the page
				}

				$em->flush();
			}

			self::$_stubs = array();
			self::$_processing_stubs = false;
		}

		return parent::offsetGet($offset);
	}

	public function updateProfile()
	{
		$account = App::getOrm()->getRepository('DeskPRO:TwitterAccount')->getFirst();
		if ($account) {
			try {
				$response = $account->getTwitterApi()->get_usersShow(array('user_id' => $this->id));
				$this->updateFromJson($response);
			} catch (\EpiTwitterException $e) {
			} catch (\EpiOAuthException $e) {
			}

			$this['last_profile_update'] = new \DateTime();
		}
	}

	public function updateFollows($register_stubs = false)
	{
		$account = App::getOrm()->getRepository('DeskPRO:TwitterAccount')->getFirst();
		if (!$account) {
			return;
		}

		$existing_users = array();

		try {
			$response = $account->getTwitterApi()->get_friendsIds(array(
				'user_id' => $this->id,
				'stringify_ids' => true
			));
			if (isset($response->ids)) {
				$ids = array_slice($response->ids, 0, 100);
				$existing_users += App::getOrm()->getRepository('DeskPRO:TwitterUser')->getByIds($ids);
				$existing_for_type = App::getOrm()->getRepository('DeskPRO:TwitterUserFriend')->getByUserAndFriends(
					$this->id, $ids
				);
				$count = $this->friends_count;

				foreach ($ids AS $id) {
					if (!isset($existing_users[$id])) {
						$new_user = \Application\DeskPRO\Entity\TwitterUser::createStub($id);
						if (!$register_stubs) {
							$new_user->unregisterStub();
						}
						App::getOrm()->persist($new_user);
						$existing_users[$id] = $new_user;
					} else {
						$new_user = $existing_users[$id];
					}

					if (!isset($existing_for_type[$id])) {
						$new_for_type = new TwitterUserFriend();
						$new_for_type->user = $this;
						$new_for_type->friend_user = $new_user;
						$new_for_type->display_order = $count;

						App::getOrm()->persist($new_for_type);

						$existing_for_type[$id] = $new_for_type;
					}

					$count--;
				}
			}
		} catch (\EpiTwitterException $e) {
		} catch (\EpiOAuthException $e) {
		}

		try {
			$response = $account->getTwitterApi()->get_followersIds(array(
				'user_id' => $this->id,
				'stringify_ids' => true
			));
			if (isset($response->ids)) {
				$ids = array_slice($response->ids, 0, 100);
				$existing_users += App::getOrm()->getRepository('DeskPRO:TwitterUser')->getByIds($ids);
				$existing_for_type = App::getOrm()->getRepository('DeskPRO:TwitterUserFollower')->getByUserAndFollowers(
					$this->id, $ids
				);
				$count = $this->followers_count;

				foreach ($ids AS $id) {
					if (!isset($existing_users[$id])) {
						$new_user = \Application\DeskPRO\Entity\TwitterUser::createStub($id);
						if (!$register_stubs) {
							$new_user->unregisterStub();
						}
						App::getOrm()->persist($new_user);
						$existing_users[$id] = $new_user;
					} else {
						$new_user = $existing_users[$id];
					}

					if (!isset($existing_for_type[$id])) {
						$new_for_type = new TwitterUserFollower();
						$new_for_type->user = $this;
						$new_for_type->follower_user = $new_user;
						$new_for_type->display_order = $count;

						App::getOrm()->persist($new_for_type);

						$existing_for_type[$id] = $new_for_type;
					}

					$count--;
				}
			}
		} catch (\EpiTwitterException $e) {
		} catch (\EpiOAuthException $e) {
		}

		$this['last_follow_update'] = new \DateTime();
	}

	public function updateFromJson($user)
	{
		$processing = self::$_processing_stubs;
		self::$_processing_stubs = true; // don't want to trigger loads here

		$this['name']              = $user->name;
		$this['screen_name']       = $user->screen_name;
		$this['profile_image_url'] = $user->profile_image_url;
		$this['url']               = (string)$user->url;
		$this['language']          = (string)$user->lang;
		$this['description']       = (string)$user->description;
		$this['is_verified']       = $user->verified;
		$this['location']          = $user->location;
		$this['is_geo_enabled']    = $user->geo_enabled;
		$this['followers_count']   = $user->followers_count;
		$this['friends_count']     = $user->friends_count;
		$this['statuses_count']    = $user->statuses_count;
		$this['is_stub']           = false;
		$this['last_profile_update'] = new \DateTime();

		self::$_processing_stubs = $processing;
	}

	public function _checkStub()
	{
		if ($this->is_stub) {
			self::$_stubs[$this->id] = $this;
		}
	}

	/**
	 * @param object $user
	 * @return \Application\DeskPRO\Entity\TwitterUser
	 */
	static public function createFromJson($user)
	{
		$entity                      = new self();
		$entity['id']                = $user->id_str;
		$entity['name']              = $user->name;
		$entity['screen_name']       = $user->screen_name;
		$entity['profile_image_url'] = $user->profile_image_url;
		$entity['url']               = (string)$user->url;
		$entity['language']          = (string)$user->lang;
		$entity['description']       = (string)$user->description;
		$entity['is_protected']      = $user->protected;
		$entity['is_verified']       = $user->verified;
		$entity['location']          = $user->location;
		$entity['is_geo_enabled']    = $user->geo_enabled;
		$entity['followers_count']   = $user->followers_count;
		$entity['friends_count']     = $user->friends_count;
		$entity['statuses_count']    = $user->statuses_count;
		$entity['is_stub']           = false;
		$entity['last_profile_update'] = new \DateTime();

		return $entity;
	}

	public static function createStub($id, $screen_name = '', $name = '')
	{
		$entity = new self();
		$entity['id'] = $id;
		$entity['name'] = $name;
		$entity['screen_name'] = $screen_name;
		$entity['profile_image_url'] = '';
		$entity['url'] = '';
		$entity['language'] = '';
		$entity['description'] = '';
		$entity['location'] = '';
		$entity['followers_count'] = 0;
		$entity['friends_count'] = 0;
		$entity['statuses_count'] = 0;
		$entity['is_stub'] = true;
		self::$_stubs[$id] = $entity;

		return $entity;
	}

	public function unregisterStub()
	{
		unset(self::$_stubs[$this->id]);
	}

	public function registerStub()
	{
		if ($this->is_stub) {
			self::$_stubs[$this->id] = $this;
		}
	}



	############################################################################
	# Doctrine Metadata
	############################################################################


	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TwitterUser';
		$metadata->setPrimaryTable(array(
			'name' => 'twitter_users',
			'indexes' => array(
				'last_follow_update_idx' => array('columns' => array('last_follow_update'))
			),
		));
		$metadata->addLifecycleCallback('_checkStub', 'postLoad');
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'bigint', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'name', 'type' => 'string', 'length' => 40, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'name', ));
		$metadata->mapField(array( 'fieldName' => 'screen_name', 'type' => 'string', 'length' => 20, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'screen_name', ));
		$metadata->mapField(array( 'fieldName' => 'profile_image_url', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'profile_image_url', ));
		$metadata->mapField(array( 'fieldName' => 'language', 'type' => 'string', 'length' => 3, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'language', ));
		$metadata->mapField(array( 'fieldName' => 'url', 'type' => 'string', 'length' => 200, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'url', ));
		$metadata->mapField(array( 'fieldName' => 'is_protected', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_protected', ));
		$metadata->mapField(array( 'fieldName' => 'is_verified', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_verified', ));
		$metadata->mapField(array( 'fieldName' => 'location', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'location', ));
		$metadata->mapField(array( 'fieldName' => 'description', 'type' => 'string', 'length' => 500, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'description', ));
		$metadata->mapField(array( 'fieldName' => 'is_geo_enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_geo_enabled', ));
		$metadata->mapField(array( 'fieldName' => 'is_stub', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_stub', ));
		$metadata->mapField(array( 'fieldName' => 'last_timeline_update', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'last_timeline_update', ));
		$metadata->mapField(array( 'fieldName' => 'last_profile_update', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'last_profile_update', ));
		$metadata->mapField(array( 'fieldName' => 'last_follow_update', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'last_follow_update', ));
		$metadata->mapField(array( 'fieldName' => 'followers_count', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'followers_count', ));
		$metadata->mapField(array( 'fieldName' => 'friends_count', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'friends_count', ));
		$metadata->mapField(array( 'fieldName' => 'statuses_count', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'statuses_count', ));
		$metadata->mapOneToMany(array( 'fieldName' => 'statuses', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => 'user',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'replies', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => 'in_reply_to_user',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'mentions', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatusMention', 'mappedBy' => 'user',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'messages', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => 'recipient',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'friends', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUserFriend', 'mappedBy' => 'user',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'followers', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUserFollower', 'mappedBy' => 'user',  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'account', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccount', 'mappedBy' => 'user', 'inversedBy' => NULL, 'joinColumns' => array( ),  ));
	}
}
