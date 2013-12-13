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
 * A Twitter Account Status
 *
 */
class TwitterAccountStatus extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var TwitterAccount
	 */
	protected $account;

	/**
	 * @var TwitterStatus
	 */
	protected $status;

	/**
	 * @var Person
	 */
	protected $agent;

	/**
	 * @var AgentTeam
	 */
	protected $agent_team;

	/**
	 * @var Person
	 */
	protected $action_agent;

	/**
	 * @var \DateTime
	 */
	protected $date_created;

	/**
	 * @var string|null
	 */
	protected $status_type;

	/**
	 * @var bool
	 */
	protected $is_archived = false;

	/**
	 * @var bool
	 */
	protected $is_favorited = false;

	/**
	 * @var TwitterAccountStatus
	 */
	protected $retweeted;

	/**
	 * @var TwitterAccountStatus
	 */
	protected $in_reply_to;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $replies;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	protected $notes;

	public function __construct()
	{
		$this->date_created = new \DateTime();

		$this->notes = new \Doctrine\Common\Collections\ArrayCollection();
		$this->replies = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function setAgentId($agent_id)
	{
		if ($agent_id) {
			$agent = App::getOrm()->find('DeskPRO:Person', $agent_id);
			if ($agent && $agent->is_agent) {
				$this->setModelField('agent', $agent);
			} else {
				$this->setModelField('agent', null);
			}
		} else {
			$this->setModelField('agent', null);
		}
		$this->setModelField('agent_team', null);
	}

	public function setAgentTeamId($agent_team_id)
	{
		if ($agent_team_id) {
			$team = App::getOrm()->find('DeskPRO:AgentTeam', $agent_team_id);
			if ($team) {
				$this->setModelField('agent_team', $team);
			} else {
				$this->setModelField('agent_team', null);
			}
		} else {
			$this->setModelField('agent_team', null);
		}
		$this->setModelField('agent', null);
	}

	public function setStatus(TwitterStatus $status)
	{
		$this->setModelField('status', $status);
		$this->setModelField('date_created', $status->date_created);
	}

	public function canRetweet()
	{
		return (
			!$this->status->isMessage()
			&& $this->status->getUserId() != $this->account->getUserId()
			&& (!$this->status->isRetweet() || $this->status->retweet->getUserId() != $this->account->getUserId())
		);
	}

	public function isFromSelf()
	{
		return (
			$this->status->getUserId() == $this->account->getUserId()
		);
	}


	############################################################################
	# Doctrine Metadata
	############################################################################


	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TwitterAccountStatus';
		$metadata->setPrimaryTable(array(
			'name' => 'twitter_accounts_statuses',
			'indexes' => array(
				'account_type_archived_idx' => array('columns' => array('account_id', 'status_type', 'is_archived')),
				'account_archived_idx' => array('columns' => array('account_id', 'is_archived'))
			)
		));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'date_created', 'type' => 'datetime', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'date_created', ));
		$metadata->mapField(array( 'fieldName' => 'status_type', 'type' => 'string', 'length' => 25, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'status_type', ));
		$metadata->mapField(array( 'fieldName' => 'is_archived', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_archived', ));
		$metadata->mapField(array( 'fieldName' => 'is_favorited', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_favorited', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'account', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccount', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'account_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'status', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterStatus', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'status_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'agent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'agent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'agent_team', 'targetEntity' => 'Application\\DeskPRO\\Entity\\AgentTeam', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'agent_team_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'action_agent', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'action_agent_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'retweeted', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccountStatus', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'retweeted_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'in_reply_to', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccountStatus', 'mappedBy' => NULL, 'inversedBy' => 'replies', 'joinColumns' => array( 0 => array( 'name' => 'in_reply_to_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'replies', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccountStatus', 'mappedBy' => 'in_reply_to',  ));
		$metadata->mapOneToMany(array( 'fieldName' => 'notes', 'targetEntity' => 'Application\\DeskPRO\\Entity\\TwitterAccountStatusNote', 'mappedBy' => 'account_status',  ));
	}
}
