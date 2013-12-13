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
use Application\DeskPRO\UI\RuleBuilder;

/**
 * Ticket filters
 */
class TicketFilter extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * Always who created the filter. Or if its not a team or global,
	 * also means the person it belongs to.
	 *
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

	/**
	 * If this is a team filter, the team it belongs to.
	 *
	 * @var \Application\DeskPRO\Entity\AgentTeam
	 */
	protected $agent_team = null;

	/**
	 * @var bool
	 */
	protected $is_global = false;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var bool
	 */
	protected $is_enabled = true;

	/**
	 * @var bool
	 */
	protected $sys_name = null;

	/**
	 * @var string
	 */
	protected $terms;

	/**
	 * @var string
	 */
	protected $group_by = '';

	/**
	 * @var string
	 */
	protected $order_by = '';

	/**
	 * Results from the last search
	 * @var array
	 */
	protected $_results = null;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getPersonId()
	{
		if ($this->person === null) {
			return 0;
		}

		return $this->person['id'];
	}

	public function setPersonId($id)
	{
		if ($id) {
			$this->setModelField('person', App::getEntityRepository('DeskPRO:Person')->find($id));
		} else {
			$this->setModelField('person', null);
		}
	}

	public function getAgentTeamId()
	{
		if (!$this->agent_team) {
			return 0;
		}
		return $this->agent_team['id'];
	}

	public function setAgentTeamId($id)
	{
		if ($id) {
			$agent_team = App::getOrm()->getRepository('DeskPRO:AgentTeam')->find($id);
			$this['agent_team'] = $agent_team;
		} else {
			$this['agent_team'] = null;
		}
	}



	/**
	 * Reset results so next calls will re-do the search.
	 */
	public function resetResults()
	{
		$this->_results = null;
	}



	/**
	 * Get the searcher for this.
	 *
	 * @return \Application\DeskPRO\Searcher\TicketSearch
	 */
	public function getSearcher(array $force_terms = array())
	{
		$searcher = new \Application\DeskPRO\Searcher\TicketSearch();

		if (!$this->sys_name || strpos($this->sys_name, 'archive_') !== 0) {
			$searcher->enableFilterSearch();
		}

		$user_searcher  = new \Application\DeskPRO\Searcher\PersonSearch();
		$org_searcher   = new \Application\DeskPRO\Searcher\OrganizationSearch();
		$has_user_terms = false;
		$has_org_terms  = false;

		$force_term_types = array();
		foreach ($force_terms as $term) {
			if ($term['op'] != 'ignore') {
				if (strpos($term['type'], 'person_') === 0) {
					$user_searcher->addTerm($term['type'], $term['op'], $term['options']);
					$has_user_terms = true;
				} elseif (strpos($term['type'], 'org_') === 0) {
					$org_searcher->addTerm($term['type'], $term['op'], $term['options']);
					$has_org_terms = true;
				} else {
					$searcher->addTerm($term['type'], $term['op'], $term['options']);
				}
			}

			$force_term_types[] = $term['type'];
		}

		foreach ($this->terms as $term) {

			if (in_array($term['type'], $force_term_types)) {
				continue;
			}

			if (strpos($term['type'], 'person_') === 0) {
				$user_searcher->addTerm($term['type'], $term['op'], $term['options']);
				$has_user_terms = true;
			} elseif (strpos($term['type'], 'org_') === 0) {
				$org_searcher->addTerm($term['type'], $term['op'], $term['options']);
				$has_org_terms = true;
			} else {
				$searcher->addTerm($term['type'], $term['op'], $term['options']);
			}
		}

		if ($has_user_terms) {
			$searcher->setPersonSearch($user_searcher);
		}
		if ($has_org_terms) {
			$searcher->setOrganizationSearch($org_searcher);
		}

		return $searcher;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		if ($this->sys_name) {
			$tr = App::getTranslator();

			switch ($this->sys_name) {
				case 'agent':
				case 'agent_w_hold':
					return $tr->phrase('agent.tickets.filter_agent');

				case 'agent_team':
				case 'agent_team_w_hold':

					$person = App::getCurrentPerson();
					if ($person && $person->isAgent()) {
						$person->loadHelper('Agent');

						if (count($person->getTeams()) > 1) {
							return $tr->phrase('agent.tickets.filter_agent_teams');
						}
					}

					return $tr->phrase('agent.tickets.filter_agent_team');

				case 'participant':
				case 'participant_w_hold':
					return $tr->phrase('agent.tickets.filter_participant');

				case 'unassigned':
				case 'unassigned_w_hold':
					return $tr->phrase('agent.tickets.filter_unassigned');

				case 'all':
				case 'all_w_hold':
					return $tr->phrase('agent.tickets.filter_all');
			}
		}

		if (!$this->title && $this->sys_name) {
			return $this->sys_name;
		}

		return $this->title;
	}


	/**
	 * Gets the actual value in the title field.
	 *
	 * @return string
	 */
	public function getRawTitle()
	{
		return $this->title;
	}


	/**
	 * Get an array of criteria phrases.
	 *
	 * @return array
	 */
	public function getSummaryParts()
	{
		return $this->getSearcher()->getSummary();
	}


	/**
	 * Explain criteria in the filter. Ex: Agent is Unassigned, Category is None
	 *
	 * @return string
	 */
	public function getSummaryPhrase()
	{
		return implode(', ', $this->getSummaryParts());
	}


	public function getResults(Person $person = null)
	{
		if ($this->_results !== null) return $this->_results;

		$searcher = $this->getSearcher();

		if (!$person) {
			$person = App::getCurrentPerson();
		}
		$searcher->setPerson($person);
		$this->_results = $searcher->getMatches();

		return $this->_results;
	}

	public function isArchiveTableFilter()
	{
		return self::checkFilterNameForArchiveTable($this->sys_name);
	}

	public static function checkFilterNameForArchiveTable($name)
	{
		return in_array($name, self::getArchiveTableFilterNames());
	}

	public static function getArchiveTableFilterNames()
	{
		return array(
			'archive_closed',
			'archive_validating',
			'archive_spam',
			'archive_deleted'
		);
	}

	public function getResultsCount()
	{
		return count($this->getResults());
	}

	public function __toString()
	{
		return (string)$this->id;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketFilter';
		$metadata->setPrimaryTable(array( 'name' => 'ticket_filters', 'uniqueConstraints' => array( 'sys_name_unique' => array( 'columns' => array( 0 => 'sys_name', ), ), ), ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'is_global', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_global', ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'is_enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_enabled', ));
		$metadata->mapField(array( 'fieldName' => 'sys_name', 'type' => 'string', 'length' => 50, 'precision' => 0, 'scale' => 0, 'nullable' => true, 'columnName' => 'sys_name', ));
		$metadata->mapField(array( 'fieldName' => 'terms', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'terms', ));
		$metadata->mapField(array( 'fieldName' => 'group_by', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'group_by', ));
		$metadata->mapField(array( 'fieldName' => 'order_by', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'order_by', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
		$metadata->mapManyToOne(array( 'fieldName' => 'agent_team', 'targetEntity' => 'Application\\DeskPRO\\Entity\\AgentTeam', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'agent_team_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'cascade', 'columnDefinition' => NULL, ), ),  ));
	}
}
