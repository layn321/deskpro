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
 * @subpackage
 */

namespace Application\ReportBundle\OverviewStat;

use Application\DeskPRO\App;
use Orb\Util\Arrays;
use Orb\Util\Strings;

class GroupingField
{
	const DEPARTMENT            = 'department';
	const TICKET_CATEGORY       = 'ticket_category';
	const TICKET_WORKFLOW       = 'ticket_workflow';
	const TICKET_PRIORITY       = 'ticket_priority';
	const LANGUAGE              = 'language';
	const PRODUCT               = 'product';
	const TICKET_FIELD          = 'ticket_field';
	const USER_FIELD            = 'user_field';
	const AGENT                 = 'agent';
	const AGENT_TEAM            = 'agent_team';
	const TICKET_URGENCY        = 'ticket_urgency';
	const ORGANIZATION          = 'organization';
	const USER                  = 'user';
	const USERGROUP             = 'usergroup';

	/**
	 * @var string
	 */
	protected $field;

	/**
	 * @var int
	 */
	protected $field_id;

	/**
	 * @var null
	 */
	protected $titles = null;

	/**
	 * @param $field
	 * @param null $field_id
	 */
	public function __construct($field)
	{
		if (strpos($field, '.') === false) {
			$this->field = $field;
		} else {
			list ($field, $field_id) = explode('.', $field);
			$this->field = $field;
			$this->field_id = $field_id;
		}
	}


	public function getFieldInfo()
	{
		switch ($this->field) {
			case self::DEPARTMENT:
				return array('select' => 'COALESCE(tickets.department_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::AGENT:
				return array('select' => 'COALESCE(tickets.agent_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::AGENT_TEAM:
				return array('select' => 'COALESCE(tickets.agent_team_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::TICKET_CATEGORY:
				return array('select' => 'COALESCE(tickets.category_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::TICKET_WORKFLOW:
				return array('select' => 'COALESCE(tickets.workflow_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::TICKET_PRIORITY:
				return array('select' => 'COALESCE(tickets.priority_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::LANGUAGE:
				return array('select' => 'tickets.language_id', 'group_by' => 'tickets.language_id', 'join' => '', 'where' => '');
				break;

			case self::PRODUCT:
				return array('select' => 'COALESCE(tickets.product_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::TICKET_URGENCY:
				return array('select' => 'tickets.urgency', 'group_by' => 'tickets.urgency', 'join' => '', 'where' => '');
				break;

			case self::ORGANIZATION:
				return array('select' => 'COALESCE(tickets.organization_id, 0) AS org_id', 'group_by' => 'org_id', 'join' => '', 'where' => '');
				break;

			case self::USERGROUP:
				return array(
					'select' => 'COALESCE(person2usergroups.usergroup_id, 0) AS usergroup_id',
					'group_by' => 'usergroup_id',
					'join' => 'LEFT JOIN person2usergroups ON (person2usergroups.person_id = tickets.person_id)',
					'where' => ''
				);
				break;

			case self::USER:
				return array('select' => 'tickets.person_id', 'group_by' => 'tickets.person_id', 'join' => '', 'where' => '');
				break;

			case self::TICKET_FIELD:
				$field_def = App::getSystemService('ticket_fields_manager')->getFieldFromId($this->field_id);

				if ($field_def->isChoiceType()) {
					$children = App::getSystemService('ticket_fields_manager')->getFieldChildren($field_def);
					if (!$children) {
						return array(
							'select' => '0 as group_field',
							'group_by' => 'group_field',
							'join' => '',
							'where' => ''
						);
					}

					$ids = implode(',', array_keys($children));

					return array(
						'select' => 'COALESCE(custom_def_ticket.title, 0) AS group_field',
						'group_by' => 'group_field',
						'join' => "
							LEFT JOIN custom_data_ticket ON (custom_data_ticket.ticket_id = tickets.id AND custom_data_ticket.field_id IN($ids))
							LEFT JOIN custom_def_ticket ON (custom_def_ticket.id = custom_data_ticket.field_id)
						",
						'where' => ''
					);
				} else {
					return array(
						'select' => 'COALESCE(custom_data_ticket.input, 0) AS group_field',
						'group_by' => 'group_field',
						'join' => 'LEFT JOIN custom_data_ticket ON (custom_data_ticket.ticket_id = tickets.id AND custom_data_ticket.field_id = ' . $this->field_id  .')',
						'where' => ''
					);
				}
				break;

			case self::USER_FIELD:
				$field_def = App::getSystemService('person_fields_manager')->getFieldFromId($this->field_id);

				if ($field_def->isChoiceType()) {
					$children = App::getSystemService('person_fields_manager')->getFieldChildren($field_def);
					if (!$children) {
						return array(
							'select' => '0 as group_field',
							'group_by' => 'group_field',
							'join' => '',
							'where' => ''
						);
					}

					$ids = implode(',', array_keys($children));

					return array(
						'select' => 'COALESCE(custom_def_people.title, 0) AS group_field',
						'group_by' => 'group_field',
						'join' => "
							LEFT JOIN custom_data_person ON (custom_data_person.person_id = tickets.person_id AND custom_data_person.field_id IN($ids))
							LEFT JOIN custom_def_people ON (custom_def_people.id = custom_data_person.field_id)
						",
						'where' => ''
					);
				} else {
					return array(
						'select' => 'COALESCE(custom_data_person.input, 0) AS group_field',
						'group_by' => 'group_field',
						'join' => 'LEFT JOIN custom_data_person ON (custom_data_person.person_id = tickets.person_id AND custom_data_person.field_id = ' . $this->field_id  .')',
						'where' => ''
					);
				}
				break;

			default:
				throw new \InvalidArgumentException("Invalid field: {$this->field}");
		}
	}


	/**
	 * Get titles
	 *
	 * @return array
	 */
	public function getTitles(array $values = array())
	{
		if ($this->titles !== null) {
			return $this->titles;
		}

		switch ($this->field) {
			case self::DEPARTMENT:
				$this->titles = App::getDataService('Department')->getFullNames('tickets');
				break;

			case self::AGENT:
				$this->titles = App::getDataService('Person')->getAgentNames();
				break;

			case self::AGENT_TEAM:
				$this->titles = App::getDataService('AgentTeam')->getTeamNames();
				break;

			case self::TICKET_CATEGORY:
				$this->titles = App::getDataService('TicketCategory')->getFullNames();
				break;

			case self::TICKET_WORKFLOW:
				$this->titles = App::getDataService('TicketWorkflow')->getNames();
				break;

			case self::TICKET_PRIORITY:
				$this->titles = App::getDataService('TicketPriority')->getNames();
				break;

			case self::LANGUAGE:
				$this->titles = App::getDataService('Language')->getTitles();
				break;

			case self::PRODUCT:
				$this->titles = App::getDataService('Product')->getFullNames();
				break;

			case self::TICKET_URGENCY:
				$this->titles = array_combine(range(1, 10), range(1, 10));
				break;

			case self::ORGANIZATION:

				if ($values) {
					$names = App::getOrm()->getRepository('DeskPRO:Organization')->getOrganizationNames(array_keys($values));
				} else {
					$names = array();
				}

				Arrays::unshiftAssoc($names, '0', 'No Organization');

				$this->titles = $names;

				break;

			case self::USERGROUP:
				$names = App::getDataService('Usergroup')->getUsergroupNames();
				Arrays::unshiftAssoc($names, '0', 'No Usergroup');
				$this->titles = $names;
				break;

			case self::USER:

				$names = array();

				if ($values) {
					$people = App::getOrm()->getRepository('DeskPRO:Person')->getByIds(array_keys($values));
					foreach ($people as $p) {
						$names[$p->getId()] = $p->getDisplayContact();
					}
				}

				$this->titles = $names;
				break;

			case self::TICKET_FIELD:

				if ($values) {
					$names = array_combine(array_keys($values), array_keys($values));
					unset($names[0]);
					$this->titles = $names;
				} else {
					$this->titles = array();
				}
				break;

			case self::USER_FIELD:

				if ($values) {
					$names = array_combine(array_keys($values), array_keys($values));
					unset($names[0]);
					$this->titles = $names;
				} else {
					$this->titles = array();
				}
				break;

			default:
				throw new \InvalidArgumentException("Invalid field: {$this->field}");
		}

		if (!isset($this->titles[0])) {
			Arrays::unshiftAssoc($this->titles, '0', 'None');
		}

		return $this->titles;
	}
}