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
use Application\DeskPRO\ClientMessage\Generator\Chat as ChatClientMessageGenerator;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Numbers;

/**
 * A conversation between one or more people
 *
 */
class ChatConversation extends \Application\DeskPRO\Domain\DomainObject
{
	const STATUS_OPEN  = 'open';
	const STATUS_ENDED = 'ended';

	const ENDED_TIMEOUT      = 'timeout';
	const ENDED_WAIT_TIMEOUT = 'wait_timeout';
	const ENDED_ABANDONED    = 'abandoned';
	const ENDED_AGENT        = 'agent';
	const ENDED_USER         = 'user';

	/**
	 * @var int
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Department
	 */
	protected $department = null;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $labels;

	/**
	 * @var string
	 */
	protected $subject = '';

	/**
	 * @var string
	 */
	protected $status = 'open';

	/**
	 * If this is a user conversation, this is the agent assigned.
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $agent = null;

	/**
	 * If this is a team chat, the team it is
	 *
	 * @var \Application\DeskPRO\Entity\AgentTeam
	 */
	protected $agent_team = null;

	/**
	 * If this is a user conversation, this is the user who started the chat
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * If this is a user convo, this is the users session
	 *
	 * @var \Application\DeskPRO\Entity\Session
	 */
	protected $session = null;

	/**
	 * User chat: The users name, if they arent a person
	 *
	 * @var string
	 */
	protected $person_name = '';

	/**
	 * User chat: The users email, if they arent a person
	 *
	 * @var string
	 */
	protected $person_email = '';

	/**
	 * ...and this is the users visitor
	 *
	 * @var \Application\DeskPRO\Entity\Visitor
	 */
	protected $visitor = null;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $participants;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $messages;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $custom_data;

	/**
	 * @var string
	 */
	protected $rating_response_time = null;

	/**
	 * @var string
	 */
	protected $rating_overall = null;

	/**
	 * @var string
	 */
	protected $rating_comment = '';

	/**
	 * Is this an agent chat
	 *
	 * @var bool
	 */
	protected $is_agent = false;

	/**
	 * If the chat is popped out into a window.
	 * This is used to make sure the JS widget on pages doesn't load again.
	 *
	 * @var bool
	 */
	protected $is_window = false;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * Since when the user has started waiting (i.e., time the assignment was 0)
	 * @var \DateTime
	 */
	protected $date_user_waiting = null;

	/**
	 * @var \DateTime
	 */
	protected $date_assigned;

	/**
	 * @var \DateTime
	 */
	protected $date_first_agent_message;

	/**
	 * @var \DateTime
	 */
	protected $date_ended;

	/**
	 * @var int
	 */
	protected $total_to_ended = 0;

	/**
	 * @var string
	 */
	protected $ended_by = '';

	/**
	 * @var bool
	 */
	protected $should_send_transcript = false;

	/**
	 * @var \DateTime
	 */
	protected $date_transcript_sent = null;

	/**
	 * @var array
	 */
	protected $_created_messages = array();

	/**
	 * @var null
	 */
	protected $_user_participants = null;

	/**
	 * @var \Application\DeskPRO\Labels\LabelManager
	 */
	protected $_label_manager = null;

	public function getChannelId($name = false)
	{
		return 'chat_convo.' . $this->id . ($name ? '.' . $name : '');
	}

	public function __construct()
	{
		$this->labels            = new \Doctrine\Common\Collections\ArrayCollection();
		$this->participants      = new \Doctrine\Common\Collections\ArrayCollection();
		$this->messages          = new \Doctrine\Common\Collections\ArrayCollection();
		$this->custom_data       = new \Doctrine\Common\Collections\ArrayCollection();
		$this->date_created      = new \DateTime();
		$this->date_user_waiting = new \DateTime();
	}


	/**
	 * @return \Application\DeskPRO\Labels\LabelManager
	 */
	public function getLabelManager()
	{
		if ($this->_label_manager === null) {
			$this->_label_manager = new \Application\DeskPRO\Labels\LabelManager($this, 'DeskPRO:LabelChatConversation');
		}

		return $this->_label_manager;
	}


	/**
	 * @static
	 * @param \Application\DeskPRO\Entity\Session $session
	 * @return \Application\DeskPRO\Entity\ChatConversation
	 */
	public static function newForUserSession($session)
	{
		$convo = new self();
		if ($session->person) {
			$convo->person = $session->person;
		}
		$convo->session = $session;
		$convo->visitor = $session->visitor;

		return $convo;
	}


	/**
	 * Setting the person copies their name and email address to the chat row for record keeping
	 *
	 * @param Person $person
	 */
	public function setPerson(Person $person)
	{
		$this->setModelField('person', $person);
		$this->setModelField('person_name', $person->getDisplayName(false));
		if ($person->getPrimaryEmailAddress()) {
			$this->setModelField('person_email', $person->getPrimaryEmailAddress());
		}
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Create a new message and then add it to this convo
	 */
	public function addNewMessage($content, $author, $is_html = false)
	{
		$chat_message = new ChatMessage();
		$chat_message->conversation = $this;
		$chat_message->author = $author;
		$chat_message['content'] = $content;

		if ($is_html) {
			$chat_message['is_html'] = true;
		}

		return $this->addMessage($chat_message);
	}



	/**
	 * Create a new message for a user based on their session
	 *
	 * @return \Application\DeskPRO\Entity\ChatMessage
	 */
	public function addNewMessageForSession($content, $session)
	{
		$chat_message = new ChatMessage();
		$chat_message->conversation = $this;

		if ($session->person) {
			$chat_message->author = $session->person;
		}

		$chat_message['content'] = $content;

		return $this->addMessage($chat_message);
	}


	/**
	 * Add a system message
	 *
	 * @return \Application\DeskPRO\Entity\ChatMessage
	 */
	public function addSystemMessage($content, $is_user_hidden = false)
	{
		$chat_message = new ChatMessage();
		$chat_message->conversation = $this;
		$chat_message['is_sys'] = true;
		$chat_message['is_user_hidden'] = $is_user_hidden;

		$chat_message['content'] = $content;

		return $this->addMessage($chat_message);
	}


	/**
	 * Add a message to this convo
	 *
	 * @param
	 */
	public function addMessage($message)
	{
		if (!$this->date_first_agent_message AND $message->author AND $message->author['is_agent']) {
			$this['date_first_agent_message'] = new \DateTime();
		}

		$message->conversation = $this;
		$this->messages->add($message);

		$this->_created_messages[] = $message;

		return $message;
	}


	/**
	 * Get an array of only user participants
	 *
	 * @return array
	 */
	public function getUserParticipants()
	{
		if ($this->_user_participants !== null) return $this->_user_participants;

		$this->_user_participants = array();

		foreach ($this->participants as $p) {
			if (!$p['person']['is_agent']) {
				$this->_user_participants[] = $p;
			}
		}

		return $this->_user_participants;
	}


	/**
	 * Get a simple array of person ID's of participants.
	 *
	 * @return array
	 */
	public function getParticipantIds()
	{
		$ids = array();
		foreach ($this->participants as $p) {
			$ids[] = $p['id'];
		}

		return $ids;
	}



	/**
	 * Check if a person ID or a person object is current a participant.
	 *
	 * @param  $person_or_id
	 * @return bool
	 */
	public function hasParticipant($person_or_id)
	{
		$person_id = $person_or_id;
		if ($person_or_id instanceof Person) {
			$person_id = $person_or_id['id'];
		}

		foreach ($this->participants as $p) {
			if ($p['id'] == $person_id) {
				return $p;
			}
		}

		return false;
	}



	/**
	 * Add a participant
	 *
	 * @param $person_or_id
	 * @return Person
	 */
	public function addParticipant($person_or_id, $suppress_sys_msg = false)
	{
		$person = $person_or_id;
		if (!($person instanceof Person)) {
			$person = App::getEntityRepository('DeskPRO:Person')->find($person);
		}

		if ($this->hasParticipant($person)) {
			return $person;
		}

		$this->participants->add($person);

		if ($this->_user_participants !== null AND !$person['is_agent']) {
			$this->_user_participants[] = $person;
		}

		return $person;
	}



	/**
	 * Remove a participant
	 *
	 * @param  $person_or_id
	 * @return Person
	 */
	public function removeParticipant($person_or_id, $suppress_sys_msg = false)
	{
		$person = $person_or_id;
		if (!($person instanceof Person)) {
			$person = App::getEntityRepository('DeskPRO:Person')->find($person);
		}

		foreach ($this->participants as $k => $p) {
			if ($p['id'] == $person['id']) {
				$this->participants->remove($k);

				return $p;
			}
		}

		return null;
	}


	/**
	 * Set the status (open or ended).
	 *
	 * @param  $status
	 * @return void
	 */
	public function setStatus($status)
	{
		if ($this->status == $status) {
			return;
		}

		$this->_onPropertyChanged('status', $this->status, $status);
		$this->status = $status;

		if ($status == self::STATUS_ENDED) {
			if (!$this->date_ended) {
				$this['date_ended'] = new \DateTime();
			}

		} else {
			if ($this->date_ended) {
				$this['date_ended'] = null;
			}
		}
	}


	/**
	 * Set the agent
	 *
	 * @param  $agent
	 * @return void
	 */
	public function setAgent($agent = null)
	{
		if (!$agent) $agent = null;

		$old_agent = $this->agent;
		if (($agent === null && $old_agent === null) || ($agent && $old_agent && $agent->getId() == $old_agent->getId())) {
			return;
		}

		$this->_onPropertyChanged('agent', $old_agent, $agent);

		$this->agent = $agent;
		if ($agent AND !$this->date_assigned) {
			$this['date_assigned'] = new \DateTime();
		}

		// Make sure the user isnt both assigned and a part
		if ($agent) {
			$this->removeParticipant($agent, true);
		}

		// Automatically add old assigned guy as part
		if ($old_agent) {
			$this->addParticipant($old_agent, true);
		}

		if ($this->agent) {
			$this->setModelField('date_user_waiting', null);
		} else {
			$this->setModelField('date_user_waiting', new \DateTime());
		}
	}

	public function getAgentId()
	{
		if ($this->agent) {
			return $this->agent->id;
		}

		return 0;
	}

	public function getDepartmentId()
	{
		if ($this->department) {
			return $this->department->id;
		}

		return 0;
	}

	public function getCreatedMessages()
	{
		return $this->_created_messages;
	}

	public function _clearCreatedMessages()
	{
		$this->_created_messages = array();
	}

	public function getSubjectLine()
	{
		if ($this->subject) {
			return $this->subject;
		}
		if ($this->person_name && $this->person_email) {
			return $this->person_name . ' <' . $this->person_email . '>';
		}
		if ($this->person_name) {
			return $this->person_name;
		}
		if ($this->person_email) {
			return $this->person_email;
		}

		return 'Chat ' . $this->id;
	}

	public function setRatingOverall($rating)
	{
		if ($rating != 1 && $rating != -1) {
			$rating = 0;
		}
		$this->setModelField('rating_overall', $rating);
	}

	public function setRatingResponseTime($rating)
	{
		if ($rating != 1 && $rating != -1) {
			$rating = 0;
		}
		$this->setModelField('rating_response_time', $rating);
	}


	/**
	 * @param \DateTime $date
	 */
	public function setDateEnded(\DateTime $date = null)
	{
		if ($date) {
			$this->setModelField('date_ended', $date);
			$this->setModelField('total_to_ended', $date->getTimestamp() - $this->date_created->getTimestamp());
		} else {
			$this->setModelField('date_ended', null);
			$this->setModelField('total_to_ended', 0);
		}
	}


	/**
	 * Get a basic array of information. These are generally used in templates or with
	 * client messages to render the message.
	 *
	 * @return array
	 */
	public function getInfo()
	{
		$info = array();

		$info['conversation_id'] = $this->id;

		if ($this->person) {
			$info['author_id']     = $this->person->id;
			$info['author_name']   = $this->person->display_name;
			$info['author_email']  = $this->person->getPrimaryEmailAddress();
			$info['author_type']   = $this->person->is_agent ? 'agent' : 'user';
		} else {
			$info['author_id']     = 0;
			$info['author_name']   = $this->person_name ? $this->person_name : '';
			$info['author_email']  = $this->person_email ? $this->person_email : '';
			$info['author_type']   = 'user';
		}

		$info['subject_line']     = $this->getSubjectLine();
		$info['agent_id']         = $this->agent ? $this->agent->id : 0;
		$info['agent_name']       = $this->agent ? $this->agent->getDisplayName() : '';
		$info['department_id']    = $this->department_id;
		$info['department_name']  = $this->department ? $this->department->getFullTitle() : '';
		$info['date_created']     = $this->date_created->getTimestamp();

		if ($this->date_ended) {
			$info['date_ended'] = $this->date_ended->getTimestamp();
			$info['ended_by']   = $this->ended_by;
		}

		return $info;
	}

	/**
	 * Add a label
	 * @param \Application\DeskPRO\Entity\LabelChatConversation $label
	 */
	public function addLabel(LabelChatConversation $label)
	{
		$label['chat'] = $this;
		$this->labels->add($label);
	}


	/**
	 * @param bool $v
	 */
	public function setIsAgent($v)
	{
		$this->setModelField('is_agent', $v);
		if ($v) {
			$this->date_user_waiting = null;
		}
	}


	/**
	 * Gets the URL to a picture for the person. Note that this will always return
	 * a path to an image, even if it's the default.
	 *
	 * @return null|string
	 */
	public function getPersonPictureUrl($size = 80, $secure = null)
	{
		// Null means detect
		if ($secure === null AND App::isWebRequest()) {
			$request = App::getRequest();
			if ($request->isSecure()) {
				$secure = true;
			}
		}

		$url = false;
		if ($this->person) {
			$url = $this->person->getPictureUrl($size, $secure);
		}

		if (!$url) {
			if (App::getSetting('core.use_gravatar') && $this->person_email) {
				$hash = md5(strtolower($this->person_email));
				if ($secure) {
					$url = 'https://secure.gravatar.com/avatar/' . $hash . '?';
				} else {
					$url = 'http://www.gravatar.com/avatar/' . $hash . '?';
				}
				$url .= 's=' . $size . '&d=mm';
			} else {
				$url = App::get('router')->generate('serve_default_picture', array(
					's' => $size,
					'size-fit' => 1,
				), true);
			}
		}

		if ($secure) {
			$url = preg_replace('#^http:#', 'https:', $url);
		}

		return $url;
	}

	/**
	 * Add a custom data item to this chat
	 *
	 * @param CustomDataChat $data
	 */
	public function addCustomData(CustomDataChat $data)
	{
		$this->custom_data->add($data);
		$data['conversation'] = $this;
	}


	/**
	 * Check if this ticket has a custom field.
	 *
	 * @param $field_id
	 * @return bool
	 */
	public function hasCustomField($field_id)
	{
		foreach ($this->custom_data as $data) {
			if ($data->field['id'] == $field_id) {
				return true;
			}
		}

		foreach ($this->custom_data as $data) {
			if ($data->field->parent AND $data->field->parent['id'] == $field_id) {
				return true;
			}
		}

		return false;
	}


	public function toApiData($primary = true, $deep = true, array $visited = array())
	{
		$data = parent::toApiData($primary, $deep, $visited);
		if ($deep) {
			$data['labels'] = array();
			foreach ($this->labels AS $label) {
				$data['labels'][] = $label['label'];
			}
		}

		return $data;
	}

	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\ChatConversation';
		$metadata->setPrimaryTable(array(
			'name' => 'chat_conversations',
			'indexes' => array(
				'status_idx' => array('columns' => array('status')),
				'should_send_transcript_idx' => array('columns' => array('should_send_transcript'))
			),
		));
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'subject', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'subject', ));
		$metadata->mapField(array( 'fieldName' => 'status', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'status', ));
		$metadata->mapField(array( 'fieldName' => 'person_name', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'person_name', ));
		$metadata->mapField(array( 'fieldName' => 'person_email', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'person_email', ));
		$metadata->mapField(array( 'fieldName' => 'rating_response_time', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'rating_response_time', ));
		$metadata->mapField(array( 'fieldName' => 'rating_overall', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'rating_overall', ));
		$metadata->mapField(array( 'fieldName' => 'rating_comment', 'type' => 'text', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'rating_comment', ));
		$metadata->mapField(array( 'fieldName' => 'is_agent', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_agent', ));
		$metadata->mapField(array( 'fieldName' => 'is_window', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_window', ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'date_user_waiting', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_user_waiting', ));
		$metadata->mapField(array( 'fieldName' => 'date_assigned', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_assigned', ));
		$metadata->mapField(array( 'fieldName' => 'date_first_agent_message', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_first_agent_message', ));
		$metadata->mapField(array( 'fieldName' => 'date_ended', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_ended', ));
		$metadata->mapField(array( 'fieldName' => 'should_send_transcript', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'should_send_transcript', ));
		$metadata->mapField(array( 'fieldName' => 'date_transcript_sent', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'date_transcript_sent', ));
		$metadata->mapField(array( 'fieldName' => 'total_to_ended', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'total_to_ended'));
		$metadata->mapField(array( 'fieldName' => 'ended_by', 'type' => 'string', 'length' => 15, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'ended_by', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'department', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Department', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'department_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'agent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'agent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'agent_team', 'targetEntity' => 'Application\\DeskPRO\\Entity\\AgentTeam', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'agent_team_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ), 'dpApi' => true ));
		$metadata->mapManyToOne(array( 'fieldName' => 'session', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Session', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'session_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'visitor', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Visitor', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'visitor_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToMany(array( 'fieldName' => 'participants', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'joinTable' => array( 'name' => 'chat_conversation_to_person', 'schema' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'conversation_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), 'inverseJoinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ), ), 'indexBy' => 'id', 'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'messages', 'targetEntity' => 'Application\\DeskPRO\\Entity\\ChatMessage', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'conversation',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'custom_data', 'targetEntity' => 'Application\\DeskPRO\\Entity\\CustomDataChat', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'conversation', 'orphanRemoval' => true,  'dpApi' => true ));
		$metadata->mapOneToMany(array( 'fieldName' => 'labels', 'targetEntity' => 'Application\\DeskPRO\\Entity\\LabelChatConversation', 'cascade' => array( 0 => 'remove', 1 => 'persist', 3 => 'merge', ), 'mappedBy' => 'chat', 'orphanRemoval' => true, 'dpApi' => true ));
	}
}
