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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer\Step\Deskpro3;

class UserRulesStep extends AbstractDeskpro3Step
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\Deskpro3Importer
	 */
	public $importer;

	public static function getTitle()
	{
		return 'Import User Rules';
	}

	public function run($page = 1)
	{
		$user_rules = $this->getOldDb()->fetchAll("SELECT * FROM user_rules WHERE link_company = 0");

		$this->getDb()->beginTransaction();
		try {
			foreach ($user_rules as $rule_info) {
				$this->processRule($rule_info);
			}
			$this->getDb()->commit();
		} catch (\Exception $e) {
			$this->getDb()->rollback();
			throw $e;
		}
	}

	public function processRule($rule_info)
	{
		$crit    = @unserialize($rule_info['criteria']);
		$actions = @unserialize($rule_info['actions']);

		if (!$crit || !$actions || empty($crit['email_match'])) {
			return;
		}

		$add_ug = null;
		if (!empty($rule_info['add_usergroups'])) {
			$add_ug = $this->getMappedNewId('usergroup', array_pop($rule_info['add_usergroups']));
			if (!$add_ug) return;
		}

		$add_org = null;
		if (!empty($rule_info['add_companies'])) {
			$add_org = $this->getMappedNewId('company', array_pop($rule_info['add_companies']));
			if (!$add_org) return;
		}

		// No valid actions
		if (!$add_org && !$add_ug) {
			return;
		}

		$insert_rule = array();
		$insert_rule['run_order'] = $rule_info['run_order'];
		$insert_rule['add_organization_id'] = $add_org;
		$insert_rule['add_usergroup_id'] = $add_ug;
		$insert_rule['email_patterns'] = serialize($crit['email_match']);

		$this->getDb()->insert('user_rules', $insert_rule);
	}
}
