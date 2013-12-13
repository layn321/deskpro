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

use Orb\Util\Arrays;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;
use Application\DeskPRO\Tickets\TicketActions\ActionsFactory;
use Application\DeskPRO\Tickets\TicketActions\ActionsCollection;

/**
 * Ticket macros
 *
 */
class TicketMacro extends \Application\DeskPRO\Domain\DomainObject
{
	/**
	 * @var int
	 *
	 */
	protected $id = null;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person = null;

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
	protected $is_global = false;

	/**
	 * @var string
	 */
	protected $actions = array();

	/**
	 * @var ActionsCollection
	 */
	protected $_actions_coll;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getActionsArrayDesc()
	{
		$ret = array();

		foreach ($this->actions as $info) {
			if (!isset($info['rule_type'])) continue;

			$type = $info['rule_type'];
			unset($info['rule_type']);

			if (count($info) == 1) {
				$info = array_pop($info);
			}

			$ret[$type] = $info;
		}

		return $ret;
	}


	/**
	 * Get a simple array of actions used to pass back to views to update
	 * UI.
	 *
	 * $ticket may be null, in which case no conditions are assumed.
	 *
	 * @param Entity\Ticket $ticket The context (used ex in replies for replacements)
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionsCollection
	 */
	public function getActionsCollection(Entity\Ticket $ticket = null)
	{
		if ($this->_actions_coll) return $this->_actions_coll;

		$factory = new ActionsFactory();
		$collection = new ActionsCollection();

		foreach ($this->actions as $action_info) {
			$action = $factory->createFromInfo($action_info);
			if ($action) {
				$collection->add($action);
			}
		}

		$this->_actions_coll = $collection;
		return $this->_actions_coll;
	}



	/**
	 * @return array
	 */
	public function getActionDescriptions(Entity\Ticket $ticket = null)
	{
		return $this->getActionsCollection()->getDescriptions($ticket);
	}



	/**
	 * Get actions for a collection of tickets.
	 *
	 * @param array $tickets
	 * @return \Application\DeskPRO\Tickets\TicketActions\ActionsCollection[]
	 */
	public function getActionsCollectionsForTickets($tickets = null)
	{
		$actions = array();

		if ($tickets) {
			foreach ($tickets as $ticket) {
				$actions[$ticket['id']] = $this->getActionsCollection($ticket);
			}
		}

		return $actions;
	}



	public function performOnTicket(Ticket $ticket, Entity\Person $person_context = null)
	{
		$collection = $this->getActionsCollection($ticket);

		if (!$person_context) {
			$person_context = App::getCurrentPerson();
		}

		$collection->apply($ticket->getTicketLogger(), $ticket, $person_context);
	}

	public function performOnPerson(Entity\Person $person)
	{
		$did_change = false;

		foreach ($this->actions as $action) {

			$term = $action['type'];
			$term_id = null;

			// $term of people_field[12] becomes $term=people_field, $term_id=12
			$m = null;
			if (preg_match('#^(.*?)\[(.*?)\]$#', $term, $m)) {
				$term = $m[1];
				$term_id = $m[2];
			}

			switch ($term) {
				case 'people_field':

					$value = $action;
					unset($value['rule_type'], $value['op'], $value['renderable_value']);

					$field = App::getEntityRepository('DeskPRO:CustomDefPerson')->find($term_id);
					if (!$field) {
						break;
					}

					foreach ($field->getHandler()->getDataFromForm($value) as $info) {
						$person->setCustomData($info[0], $info[1], $info[2]);
					}

					$did_change = true;
					break;

				case 'person_organization_id':
					$person['organization_id'] = $action['person_organization_id'];
					$did_change = true;
					break;
			}
		}

		if ($did_change) {
			App::getOrm()->persist($person);
		}

		return $did_change;
	}



	############################################################################
	# Doctrine Metadata
	############################################################################

	public static function loadMetadata(ClassMetadata $metadata)
	{
		$metadata->setInheritanceType(ClassMetadataInfo::INHERITANCE_TYPE_NONE);
		$metadata->customRepositoryClassName = 'Application\DeskPRO\EntityRepository\TicketMacro';
		$metadata->setPrimaryTable(array( 'name' => 'ticket_macros', ));
		$metadata->setChangeTrackingPolicy(ClassMetadataInfo::CHANGETRACKING_NOTIFY);
		$metadata->mapField(array( 'fieldName' => 'id', 'type' => 'integer', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'id', 'id' => true, ));
		$metadata->mapField(array( 'fieldName' => 'title', 'type' => 'string', 'length' => 255, 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'title', ));
		$metadata->mapField(array( 'fieldName' => 'is_enabled', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_enabled', ));
		$metadata->mapField(array( 'fieldName' => 'is_global', 'type' => 'boolean', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'is_global', ));
		$metadata->mapField(array( 'fieldName' => 'actions', 'type' => 'array', 'precision' => 0, 'scale' => 0, 'nullable' => false, 'columnName' => 'actions', ));
		$metadata->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_IDENTITY);
		$metadata->mapManyToOne(array( 'fieldName' => 'person', 'targetEntity' => 'Application\\DeskPRO\\Entity\\Person', 'mappedBy' => NULL, 'inversedBy' => NULL, 'joinColumns' => array( 0 => array( 'name' => 'person_id', 'referencedColumnName' => 'id', 'nullable' => true, 'onDelete' => 'set null', 'columnDefinition' => NULL, ), ),  ));
	}
}
