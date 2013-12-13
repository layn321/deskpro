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

/**
 * Basic hierarchicial category entity. Hierarchy is maintained automatically
 * by a Doctrine NestedSet implementation
 *
 */
class ChatMessage extends \Application\DeskPRO\Domain\DomainObject
{
	const ORIGIN_AGENT = 'agent';
	const ORIGIN_USER  = 'user';

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $origin = '';

	/**
	 * The type of message this is
	 *
	 * @var string
	 */
	protected $tag = null;

	/**
	 * The conversation the message belongs to
	 * @var \Application\DeskPRO\Entity\ChatConversation
	 */
	protected $conversation;

	/**
	 * Person who created the message
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $author = null;

	/**
	 * The authors name at the point of this message
	 *
	 * @var string
	 */
	protected $person_name = '';

	/**
	 * The message
	 * @var string
	 */
	protected $content;

	/**
	 * Is this a system message? (ended, joined, etc)
	 *
	 * @var bool
	 */
	protected $is_sys = false;

	/**
	 * Is the message hidden from the user?
	 *
	 * @var bool
	 */
	protected $is_user_hidden = false;

	/**
	 * Is the content an HTML message?
	 *
	 * @var bool
	 */
	protected $is_html = false;

	/**
	 * Data
	 *
	 * @var array
	 */
	protected $metadata = array();

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var \DateTime
	 */
	protected $date_received = null;

	public function __construct()
	{
		$this['date_created'] = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function setAuthor($author)
	{
		// Could be a guest, in which case we dont care
		if ($author && $author->id) {
			$old = $this->author;
			$this->setModelField('author', $author);
			if ($author && !$this->person_name) {
				$this['person_name'] = $author->getDisplayNameUser();
			}
		}
	}

	public function getAuthorId()
	{
		if ($this->author) {
			return $this->author['id'];
		}

		return 0;
	}

	public function getAuthorName()
	{
		if ($this->is_sys) {
			return '*';
		} elseif ($this->author) {
			return $this->author['display_name_user'];
		} else if ($this->conversation['person_name']) {
			return $this->conversation['person_name'];
		}

		return 'User';
	}


	/**
	 * Gets the URL to a picture for the person. Note that this will always return
	 * a path to an image, even if it's the default.
	 *
	 * @return null|string
	 */
	public function getAuthorPictureUrl($size = 80, $secure = null)
	{
		// Null means detect
		if ($secure === null AND App::isWebRequest()) {
			$request = App::getRequest();
			if ($request->isSecure()) {
				$secure = true;
			}
		}

		$url = false;
		if ($this->author) {
			$url = $this->author->getPictureUrl($size, $secure);
		}

		if (!$url && !$this->conversation->is_agent) {
			$url = $this->conversation->getPersonPictureUrl($size, $secure);
		}

		if (!$url) {
			$url = App::get('router')->generate('serve_default_picture', array(
				's' => $size,
				'size-fit' => 1,
			), true);
		}

		if ($secure) {
			$url = preg_replace('#^http:#', 'https:', $url);
		}

		return $url;
	}


	/**
	 */
	public function _setUserName()
	{
		// If we have no name, then assume the message is
		// by the user who started the chat
		if (!$this->person_name && $this->conversation['person_name']) {
			$this['person_name'] = $this->conversation['person_name'];
		}
	}

	/**
	 * Get a basic array of message information. These are generally used in templates or with
	 * client messages to render the message.
	 *
	 * @return array
	 */
	public function getInfo()
	{
		$info = array();

		$info['conversation_id'] = $this->conversation->id;
		$info['message_id'] = $this->id;

		if ($this->is_sys) {
			$info['author_id'] = 0;
			$info['author_name'] = '*';
			$info['author_type'] = 'sys';
		} elseif ($this->author) {
			$info['author_id'] = $this->author->id;
			$info['author_name'] = $this->author->display_name_user;
			$info['author_type'] = $this->author->is_agent ? 'agent' : 'user';

			// Handle the case where the author is an agent in the user interface
			if ($info['author_type'] == 'agent' && isset($this->metadata['is_user_message'])) {
				$info['author_type'] = 'user';
			}
		} else {
			$info['author_id'] = 0;
			$info['author_name'] = $this->getAuthorName();
			$info['author_type'] = 'user';
		}

		$info['content']       = $this->content;
		$info['is_html']       = $this->is_html;
		$info['metadata']      = $this->metadata;
		$info['date_created']  = $this->date_created->getTimestamp();

		return $info;
	}


	/**
	 * Get message as HTML
	 *
	 * @return string
	 */
	public function getContentHtml()
	{
		if ($this->is_html) {
			return $this->content;
		}

		$content = htmlspecialchars($this->content, \ENT_QUOTES, 'UTF-8');
		$content = nl2br($content);

		return $content;
	}


	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		if (is_string($data['content'])) {
			$content = @json_decode($data['content'], true);
			if ($content) {
				$data['content'] = $content;
			}
		}

		return $data;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\Basic';
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setPrimaryTable(array( 'name' => 'chat_messages', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->addLifecycleCallback('_setUserName', 'prePersist');
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'tag', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'tag', ));
		$metadata->mapField(array( 'fieldName' => 'origin', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'origin', ));
		$metadata->mapField(array( 'fieldName' => 'person_name', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'person_name', ));
		$metadata->mapField(array( 'fieldName' => 'content', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'content', ));
		$metadata->mapField(array( 'fieldName' => 'is_sys', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_sys', ));
		$metadata->mapField(array( 'fieldName' => 'is_user_hidden', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_user_hidden', ));
		$metadata->mapField(array( 'fieldName' => 'is_html', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_html', ));
		$metadata->mapField(array( 'fieldName' => 'metadata', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'metadata', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_received', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_received', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'conversation', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ChatConversation', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'conversation_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'author', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'author_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
	}
}
