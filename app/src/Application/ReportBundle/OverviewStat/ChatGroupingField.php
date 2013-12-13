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

class ChatGroupingField extends GroupingField
{
	const DEPARTMENT            = 'department';
	const USER_FIELD            = 'user_field';
	const AGENT                 = 'agent';
	const ORGANIZATION          = 'organization';
	const USER                  = 'user';
	const USERGROUP             = 'usergroup';

	public function getFieldInfo()
	{
		switch ($this->field) {
			case self::DEPARTMENT:
				return array('select' => 'COALESCE(chat_conversations.department_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::AGENT:
				return array('select' => 'COALESCE(chat_conversations.agent_id, 0) AS group_field', 'group_by' => 'group_field', 'join' => '', 'where' => '');
				break;

			case self::ORGANIZATION:
				return array(
					'select' => 'COALESCE(organizations.id, 0) AS org_id',
					'group_by' => 'org_id',
					'join' => 'LEFT JOIN people ON (chat_conversations.person_id = people.id) LEFT JOIN organizations ON (organizations.id = people.organization_id)',
					'where' => ''
				);
				break;

			case self::USERGROUP:
				return array(
					'select' => 'COALESCE(person2usergroups.usergroup_id, 0) AS usergroup_id',
					'group_by' => 'usergroup_id',
					'join' => 'LEFT JOIN person2usergroups ON (person2usergroups.person_id = chat_conversations.person_id)',
					'where' => ''
				);
				break;

			case self::USER:
				return array('select' => 'COALESCE(chat_conversations.person_id, 0) AS person_id', 'group_by' => 'person_id', 'join' => '', 'where' => '');
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
						'select' => 'COALESCE(custom_data_person.id, 0) AS group_field',
						'group_by' => 'group_field',
						'join' => 'LEFT JOIN custom_data_person ON (custom_data_person.person_id = chat_conversations.person_id AND custom_data_person.field_id IN('.$ids.'))',
						'where' => ''
					);
				} else {
					return array(
						'select' => 'COALESCE(custom_data_person.input, 0) AS group_field',
						'group_by' => 'group_field',
						'join' => 'LEFT JOIN custom_data_person ON (custom_data_person.person_id = chat_conversations.person_id AND custom_data_person.field_id = ' . $this->field_id  .')',
						'where' => ''
					);
				}
				break;

			default:
				throw new \InvalidArgumentException("Invalid field: {$this->field}");
		}
	}
}