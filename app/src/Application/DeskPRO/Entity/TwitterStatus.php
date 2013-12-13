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
 * Twitter Status
 *
 */
class TwitterStatus extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterUser
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterStatus
	 */
	protected $in_reply_to_status;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $replies;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterStatus
	 */
	protected $retweet;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $retweets;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterUser
	 */
	protected $in_reply_to_user;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterUser
	 */
	protected $recipient;


	/**
	 * @var Boolean
	 */
	protected $is_truncated = false;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var double
	 */
	protected $geo_latitude;

	/**
	 * @var double
	 */
	protected $geo_longitude;

	/**
	 * @var string
	 */
	protected $source;

	/**
	 * @var \Application\DeskPRO\Entity\TwitterStatusLong
	 */
	protected $long;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $mentions;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $tags;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $urls;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $account_statuses;

	/**
	 * @var string
	 */
	protected $_parsed_text;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->replies = new \Doctrine\Common\Collections\ArrayCollection();
		$this->retweets = new \Doctrine\Common\Collections\ArrayCollection();

		$this->mentions = new \Doctrine\Common\Collections\ArrayCollection();
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->urls = new \Doctrine\Common\Collections\ArrayCollection();

		$this->account_statuses = new \Doctrine\Common\Collections\ArrayCollection();
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

	public function addMention(TwitterStatusMention $mention)
	{
		$this->mentions->add($mention);
		$this->_onPropertyChanged('mentions', null, $mention);
	}

	public function addTag(TwitterStatusTag $tag)
	{
		$this->tags->add($tag);
		$this->_onPropertyChanged('tags', null, $tag);
	}

	public function addUrl(TwitterStatusUrl $url)
	{
		$this->urls->add($url);
		$this->_onPropertyChanged('urls', null, $url);
	}

	/**
	 * @return Boolean
	 */
	public function isMessage()
	{
		return null !== $this->recipient;
	}

	/**
	 * @return Boolean
	 */
	public function isReply()
	{
		return null !== $this->in_reply_to_status || null !== $this->in_reply_to_user;
	}

	/**
	 * @return integer
	 */
	public function getInReplyToStatusId()
	{
		if (null !== $this->in_reply_to_status) {
			return $this->in_reply_to_status->getId();
		}

		return 0;
	}

	/**
	 * @param integer $id
	 */
	public function setInReplyToStatusId($id)
	{
		if ($id && $status = App::getOrm()->getRepository('DeskPRO:TwitterStatus')->find($id)) {
			$this->in_reply_to_status = $status;
		} else {
			$this->in_reply_to_status = null;
		}
	}

	/**
	 * @return integer
	 */
	public function getRetweetId()
	{
		if (null !== $this->retweet) {
			return $this->retweet->getId();
		}

		return 0;
	}

	/**
	 * @param integer $id
	 */
	public function setRetweetId($id)
	{
		if ($id && $status = App::getOrm()->getRepository('DeskPRO:TwitterStatus')->find($id)) {
			$this->retweet = $status;
		} else {
			$this->retweet = null;
		}
	}

	/**
	 * @return Boolean
	 */
	public function isRetweet()
	{
		return null !== $this->retweet;
	}

	/**
	 * @return integer
	 */
	public function getInReplyToUserId()
	{
		if (null !== $this->in_reply_to_user) {
			return $this->in_reply_to_user->getId();
		}

		return 0;
	}

	/**
	 * @param integer $id
	 */
	public function setInReplyToUserId($id)
	{
		if ($id && $user = App::getOrm()->getRepository('DeskPRO:TwitterUser')->find($id)) {
			$this->in_reply_to_user = $user;
		} else {
			$this->in_reply_to_user = null;
		}
	}

	/**
	 * @return integer
	 */
	public function getRecipientId()
	{
		if (null !== $this->recipient) {
			return $this->recipient->getId();
		}

		return 0;
	}

	/**
	 * @param integer $id
	 */
	public function setRecipientId($id)
	{
		if ($id && $user = App::getOrm()->getRepository('DeskPRO:TwitterUser')->find($id)) {
			$this->recipient = $user;
		} else {
			$this->recipient = null;
		}
	}

	/**
	 * @return Boolean
	 */
	public function isTruncated()
	{
		return (Boolean) $this->is_truncated;
	}

	/**
	 * Retrieve a parsed version of status' text.
	 *
	 * @return string
	 */
	public function getParsedText()
	{
		if (null !== $this->_parsed_text) {
			return $this->_parsed_text;
		}

		$replacements = array();
		foreach ($this['mentions'] as $mention) {
			$replacements[$mention['starts']] = $mention;
		}
		foreach ($this['tags'] as $tag) {
			$replacements[$tag['starts']] = $tag;
		}
		foreach ($this['urls'] as $url) {
			$replacements[$url['starts']] = $url;
		}

		if (!count($replacements)) {
			$this->_parsed_text = $this['text'];
			return $this->_parsed_text;
		}

		ksort($replacements);

		$cursor = 0;
		$this->_parsed_text = '';
		foreach ($replacements as $starts => $replacement) {
			$this->_parsed_text .= \Orb\Util\Strings::utf8_substr($this['text'], $cursor, $starts - $cursor);
			$replace = \Orb\Util\Strings::utf8_substr($this['text'], $starts, $replacement['ends'] - $starts);
			$cursor = $replacement['ends'];

			switch (get_class($replacement)) {
				case 'Application\\DeskPRO\\Entity\\TwitterStatusMention':
					$this->_parsed_text .= sprintf('<a class="mention" href="https://twitter.com/%2$s" data-user-id="%1$s">@%2$s</a>', $replacement['user']['id'], htmlspecialchars($replacement['user']['screen_name']));
					break;
				case 'Application\\DeskPRO\\Entity\\TwitterStatusTag':
					$this->_parsed_text .= sprintf('<a class="hash" href="https://twitter.com/search?q=%2$s" target="twitter-hash" data-hash="%1$s">#%1$s</a>', htmlspecialchars($replacement['hash']), urlencode('#' . $replacement['hash']));
					break;
				case 'Application\\DeskPRO\\Entity\\TwitterStatusUrl':
					$this->_parsed_text .= sprintf('<a class="url" href="%s" target="_twitter_url_%s">%s</a>',
						htmlspecialchars($replacement['url']),
						md5($replacement['id']),
						htmlspecialchars($replacement['display_url'] ?: $replacement['url'])
					);
					break;
				default:
					$this->_parsed_text .= $replace;
					break;
			}
		}

		if ( \Orb\Util\Strings::utf8_strlen($this['text']) != $cursor) {
			$this->_parsed_text .=  \Orb\Util\Strings::utf8_substr($this['text'], $cursor);
		}

		return $this->_parsed_text;
	}

	public function getClippedText($length = null)
	{
		if (!$length || $length > \Orb\Util\Strings::utf8_strlen($this->text)) {
			return $this->text;
		} else {
			return \Orb\Util\Strings::utf8_substr($this->text, 0, $length) . '...';
		}
	}

	/**
	 * @param object $status
	 * @return \Application\DeskPRO\Entity\TwitterStatus
	 */
	static public function createFromJson($status)
	{
		$entity                 = new self();
		$entity['id']           = $status->id_str;
		$entity['text']         = $status->text;
		$entity['is_truncated'] = $status->truncated;
		$entity['date_created'] = new \DateTime($status->created_at);
		$entity['source']       = $status->source;

		// @!TODO add geo informations
		// $entity['geo_latitude'] = $json['geo'][];
		// $entity['geo_longitude'] = $json['geo'][];

		return $entity;
	}

	/**
	 * @param object $dm
	 * @return \Application\DeskPRO\Entity\TwitterStatus
	 */
	static public function createFromDmJson($dm)
	{
		$entity                 = new self();
		$entity['id']           = $dm->id_str;
		$entity['text']         = $dm->text;
		$entity['date_created'] = new \DateTime($dm->created_at);

		return $entity;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################


	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TwitterStatus';
		$metadata->setPrimaryTable(array( 'name' => 'twitter_statuses', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'bigint', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'text', 'type' => 'string', 'length' => 4000, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'text', ));
		$metadata->mapField(array( 'fieldName' => 'is_truncated', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_truncated', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'geo_latitude', 'type' => 'decimal', 'precision' => 10, 'scale' => 5, 'nullable' => true, 'columnName' => 'geo_latitude', ));
		$metadata->mapField(array( 'fieldName' => 'geo_longitude', 'type' => 'decimal', 'precision' => 10, 'scale' => 5, 'nullable' => true, 'columnName' => 'geo_longitude', ));
		$metadata->mapField(array( 'fieldName' => 'source', 'type' => 'string', 'length' => 4000, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'source', ));
		$metadata->mapManyToOne(array( 'fieldName' => 'user', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUser', 'mappedBy' => NULL, 'inversedBy' => 'statuses', 'joinColumns' => array( 0 => array( 'name' => 'user_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => NULL, 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'in_reply_to_status', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => NULL, 'inversedBy' => 'replies', 'joinColumns' => array( 0 => array( 'name' => 'in_reply_to_status_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'replies', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => 'in_reply_to_status',  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'retweet', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => NULL, 'inversedBy' => 'retweets', 'joinColumns' => array( 0 => array( 'name' => 'retweet_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'retweets', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => 'retweet',  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'in_reply_to_user', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUser', 'mappedBy' => NULL, 'inversedBy' => 'replies', 'joinColumns' => array( 0 => array( 'name' => 'in_reply_to_user_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => NULL, 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'recipient', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterUser', 'mappedBy' => NULL, 'inversedBy' => 'messages', 'joinColumns' => array( 0 => array( 'name' => 'recipient_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => NULL, 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'long', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatusLong', 'mappedBy' => 'status', 'inversedBy' => NULL  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'mentions', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatusMention', 'mappedBy' => 'status',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'tags', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatusTag', 'mappedBy' => 'status',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'urls', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatusUrl', 'mappedBy' => 'status',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'account_statuses', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccountStatus', 'mappedBy' => 'status',  ));
	}
}
