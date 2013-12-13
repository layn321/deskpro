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
 */

namespace Application\DeskPRO\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

// Usage: php cmd.php dpdev:load-data --count=# --types=a,b,c --range="3 years"
// Count defaults to 100, types must be explicitly specified. If no
// types are specified, a list of available ones is given. If you want
// to insert into everything, use --types=*

class DevLoadDataCommand extends \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('dpdev:load-data');
		$this->addOption('count', null, InputOption::VALUE_REQUIRED, 'Amount of data for each type to create', 0);
		$this->addOption('types', null, InputOption::VALUE_REQUIRED, 'Comma separated list of data types (* for all)', '');
		$this->addOption('types-not', null, InputOption::VALUE_REQUIRED, 'Comma separated list of data types to skip (implies --types=*)', '');
		$this->addOption('range', null, InputOption::VALUE_REQUIRED, 'Range of dates to cover data for (eg, "3 years")', '');
		$this->addOption('wordlist', null, InputOption::VALUE_REQUIRED, 'Optional path to a wordlist file with one word per line', '');
	}

	protected $_data_cache = array();
	protected $_batch_insert = array();
	protected $_batch_insert_ignore = array();
	protected $_batch_insert_label_def = array();
	protected $_start_ts = null;
	protected $_date_offset = null;
	protected $_wordlist_file;

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		set_time_limit(0);
		App::getDb()->getConfiguration()->setSQLLogger(null);

		// todo: triggers, escalations, banned emails, banned IPs, agent teams, perm groups

		$available_types = array(
			'agent',
			'org_field', 'organization', 'usergroup',
			'person_field', 'person',
			'sla', 'ticket_department', 'ticket_field', 'ticket', 'ticket_filter',
			'ticket_snippet_category', 'ticket_snippet', 'ticket_macro',
			'chat_snippet_category', 'chat_snippet',
			'chat_department',
			'feedback_status', 'feedback_type', 'feedback',
			'article_field', 'article_category','article',
			'news_category','news',
			'download_category', 'download',
			'glossary',
			'task',
			'twitter_user', 'twitter_status'
		);
		$types_manual = array('agent', 'twitter_user', 'twitter_status');

		$amount = intval($input->getOption('count'));
		if ($amount <= 0) {
			$amount = 100;
		}

		$this->_wordlist_file = $input->getOption('wordlist');
		if ($this->_wordlist_file && !is_file($this->_wordlist_file)) {
			echo "--wordlist is not a valid file\n";
			return 1;
		}

		$type_input = $input->getOption('types');
		$types_not = $input->getOption('types-not');
		if ($types_not) {
			if (!$type_input) {
				$type_input = '*';
			} else {
				echo "Cannot specify --types and --types-not together.\n";
				return 1;
			}
		}

		if ($type_input === '*') {
			$types = $available_types;

			foreach ($types_manual AS $type_manual) {
				$manual_type_key = array_search($type_manual, $types);
				if ($manual_type_key !== false) {
					unset($types[$manual_type_key]);
				}
			}

			if ($types_not) {
				$type_not_list = preg_split('/,\s*/', $types_not, -1, PREG_SPLIT_NO_EMPTY);
				foreach ($type_not_list AS $not) {
					$type_key = array_search($not, $types);
					if ($type_key !== false) {
						unset($types[$type_key]);
					}
				}
			}
		} else {
			$types = preg_split('/,\s*/', $type_input, -1, PREG_SPLIT_NO_EMPTY);
		}

		if (!$types) {
			sort($available_types);
			echo "No types given. Cannot continue. Available types:\n\t" . implode(', ', $available_types) . "\n";
			return 2;
		}

		if (!$input->getOption('range')) {
			$output->writeln("A date range (--range) must be specified");
			return 3;
		}

		$range = $input->getOption('range');

		try {
			$start_date = new \DateTime('-' . $range);
		} catch (\Exception $e) {
			$output->writeln("Failed to parse date range '$range'.");
			return 4;
		}
		$this->_start_ts = $start_date->getTimestamp();
		$this->_date_offset = (time() - $start_date->getTimestamp()) / $amount;

		$total = count($types);
		$db = App::getDb();
		$begin = microtime(true);

		$db->exec("SET unique_checks=0");
		$db->exec("SET foreign_key_checks=0");
		$db->beginTransaction();

		// loop through all to keep the order the same as we create some dependent stuff first
		$type_count = 0;
		foreach ($available_types AS $type) {
			if (!in_array($type, $types)) {
				continue;
			}

			$start = microtime(true);
			$count = ++$type_count;

			$method = '_load' . str_replace('_', '', $type);
			if (!method_exists($this, $method)) {
				echo str_pad(
					sprintf("[%02d/%02d] %s is unknown, skipping.", $count, $total, $type),
					60
				) . "\n";
				continue;
			}

			$memory = memory_get_usage() / 1024 / 1024;

			echo str_pad(
				sprintf("[%02d/%02d] %s... 0/%d (%.2f MB)", $count, $total, $type, $amount, $memory),
				60
			) . "\r";

			for ($i = 0; $i < $amount; $i++) {
				$this->$method($i);

				if ($i > 0 && $i % 10 == 0) {
					$time = microtime(true) - $start;
					$memory = memory_get_usage() / 1024 / 1024;

					if ($i % 500 == 0) {
						$this->_flushAndClear();
					}

					echo str_pad(
						sprintf("[%02d/%02d] %s... %d/%d (%.2f s, %.2f MB)", $count, $total, $type, $i, $amount, $time, $memory),
						60
					) . "\r";
				}
			}

			$time = microtime(true) - $start;
			$memory = memory_get_usage() / 1024 / 1024;
			echo str_pad(
				sprintf("[%02d/%02d] %s... completing (%.2f s, %.2f MB)", $count, $total, $type, $time, $memory),
				60
			) . "\r";

			$this->_flushAndClear();

			$complete_method = '_complete' . str_replace('_', '', $type);
			if (method_exists($this, $complete_method)) {
				$this->$complete_method();
			}

			$time = microtime(true) - $start;
			$memory = memory_get_usage() / 1024 / 1024;
			echo str_pad(
				sprintf("[%02d/%02d] %s... Done, inserted %d (%.2f s, %.2f MB)", $count, $total, $type, $amount, $time, $memory),
				60
			) . "\n";
		}

		$db->commit();
		$db->exec("SET unique_checks=1");
		$db->exec("SET foreign_key_checks=1");

		$time = microtime(true) - $begin;
		$memory = memory_get_usage() / 1024 / 1024;
		echo "\n" .
			sprintf("Data load completed (%.2f s, %.2f MB)", $time, $memory)
			. "\n";
	}

	protected function _flushAndClear()
	{
		$orm = App::getOrm();
		$db = App::getDb();

		$orm->flush();

		foreach ($this->_batch_insert AS $table => $batches) {
			$db->batchInsert($table, $batches);
		}
		foreach ($this->_batch_insert_ignore AS $table => $batches) {
			$db->batchInsert($table, $batches, true);
		}
		foreach ($this->_batch_insert_label_def AS $type => $labels) {
			$batches = array();
			foreach ($labels AS $label => $total) {
				$batches[] = "('$type', '$label', $total)";
			}
			$db->executeUpdate("
				INSERT INTO label_defs
					(label_type, label, total)
				VALUES
					" . implode(',', $batches) . "
				ON DUPLICATE KEY UPDATE total = VALUES(total);
			");
		}

		$orm->commit();
		$orm->clear();
		$orm->clearRepositoryCache();
		$this->_data_cache = array();
		$this->_batch_insert = array();
		$this->_batch_insert_ignore = array();
		$this->_batch_insert_label_def = array();
		gc_collect_cycles();

		$db->beginTransaction();
	}

	protected function _loadAgent($i)
	{
		$agent = new Entity\Person();
		$agent->name = $this->_getRandomText(2);
		$agent->setEmail($this->_getRandomText(1) . microtime(true) . '@example.com', true);
		$agent->date_created = $this->_getRandomDate();
		$agent->is_user = true;
		$agent->is_confirmed = true;
		$agent->is_agent = true;
		App::getOrm()->persist($agent);
		App::getOrm()->flush();

		$db = App::getDb();

		// Default to non-destructive perm group, or if thats deleted, the default all perms group
		$has_ug = $db->fetchColumn("SELECT id FROM usergroups WHERE id IN (4,3) ORDER BY id DESC");
		if ($has_ug) {
			$db->insert('person2usergroups', array(
				'person_id' => $agent->getId(),
				'usergroup_id' => $has_ug
			));
		}

		// Default access to all departments
		if (!isset($this->_data_cache['all_departments'])) {
			$this->_data_cache['all_departments'] = App::getDataService('Department')->getAll();
		}

		$batch = array();
		foreach ($this->_data_cache['all_departments'] as $dep) {
			$batch[] = array(
				'department_id' => $dep->getId(),
				'person_id'     => $agent->getId(),
				'app'           => $dep->is_tickets_enabled ? 'tickets' : 'chat',
				'name'          => 'full',
				'value'         => 1
			);
		}
		$db->batchInsert('department_permissions', $batch);

		// Default notifications
		$agent_id = $agent->getId();
		$db->executeUpdate("
			INSERT INTO `ticket_filter_subscriptions` (`id`, `filter_id`, `person_id`, `email_created`, `email_new`, `email_user_activity`, `email_agent_activity`, `email_property_change`, `alert_new`, `alert_user_activity`, `alert_agent_activity`, `alert_property_change`)
			VALUES
				(NULL, 1, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1),
				(NULL, 2, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1),
				(NULL, 3, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1),
				(NULL, 4, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1),
				(NULL, 5, $agent_id, 1, 1, 1, 1, 1, 1, 1, 1, 1)
		");

		$db->executeUpdate("
			INSERT INTO `people_prefs` (`person_id`, `name`, `value_str`, `value_array`, `date_expire`)
			VALUES
				($agent_id, 'agent_notif.chat_message.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.login_attempt_fail.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.task_assign_self.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.task_assign_self.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.task_assign_team.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.task_assign_team.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.task_complete.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.task_complete.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.task_due.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.task_due.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_comment.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_comment.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_comment_validate.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_comment_validate.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_feedback.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_feedback.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_feedback_validate.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_feedback_validate.email', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_user.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_user_validate.alert', '1', X'4E3B', NULL),
				($agent_id, 'agent_notif.new_user_validate.email', '1', X'4E3B', NULL)
		");

		// Add pref for first login marker
		$db->insert('people_prefs', array(
			'person_id'   => $agent_id,
			'name'        => 'agent.first_login',
			'value_str'   => 1,
			'value_array' => null,
			'date_expire' => null
		));
		$db->insert('people_prefs', array(
			'person_id'   => $agent_id,
			'name'        => 'agent.first_login_name',
			'value_str'   => 1,
			'value_array' => null,
			'date_expire' => null
		));
	}

	protected function _loadOrgField()
	{
		$org_field = new Entity\CustomDefOrganization();
		$org_field->title = $this->_getRandomText(2);
		$org_field->description = $this->_getRandomText(mt_rand(1, 10));
		$org_field->handler_class = 'Application\DeskPRO\CustomFields\Handler\Text';

		App::getOrm()->persist($org_field);
	}

	protected function _loadOrganization($i)
	{
		if (!isset($this->_data_cache['usergroups'])) {
			$this->_data_cache['usergroups'] = App::getEntityRepository('DeskPRO:Usergroup')->findAll();
		}
		if (!isset($this->_data_cache['org_fields'])) {
			$this->_data_cache['org_fields'] = App::getEntityRepository('DeskPRO:CustomDefOrganization')->findAll();
		}

		$org = array(
			'name' => $this->_getRandomText(mt_rand(1, 3)),
			'date_created' => $this->_getRandomDate('string')
		);
		$org_ent = new Entity\Organization();
		$org = array_merge($org_ent->getScalarData(), $org);

		$db = App::getDb();
		$db->insert('organizations', $org);
		$org['id'] = $db->lastInsertId();

		$this->_applyLabelsDb('organization', $org['id']);

		if (mt_rand(1, 4) == 1) {
			$count = mt_rand(1, 3);
			for ($i = 0; $i < $count; $i++) {
				$ug_id = $this->_getRandomFromCache('usergroups', 'id');
				$db->executeUpdate("
					INSERT IGNORE INTO organization2usergroups
						(organization_id, usergroup_id)
					VALUES
						(?, ?)
				", array($org['id'], $ug_id));
			}
		}

		foreach ($this->_data_cache['org_fields'] AS $field) {
			if ($field->getTypeName() == 'text') {
				$db->insert('custom_data_organizations', array(
					'organization_id' => $org['id'],
					'field_id' => $field->id,
					'root_field_id' => $field->id,
					'value' => 0,
					'input' => $this->_getRandomText(mt_rand(1, 5))
				));
			}
		}

		// todo: contact data
	}

	protected function _loadUsergroup()
	{
		$usergroup = new Entity\Usergroup();
		$usergroup->title = $this->_getRandomText(2);
		$usergroup->note = $this->_getRandomText(mt_rand(1, 5));

		App::getOrm()->persist($usergroup);
	}

	protected function _loadPersonField()
	{
		$field = new Entity\CustomDefPerson();
		$field->title = $this->_getRandomText(2);
		$field->description = $this->_getRandomText(mt_rand(1, 10));
		$field->handler_class = 'Application\DeskPRO\CustomFields\Handler\Text';

		App::getOrm()->persist($field);
	}

	protected function _loadPerson($i)
	{
		if (!isset($this->_data_cache['usergroups'])) {
			$this->_data_cache['usergroups'] = App::getEntityRepository('DeskPRO:Usergroup')->findAll();
		}
		if (!isset($this->_data_cache['person_fields'])) {
			$this->_data_cache['person_fields'] = App::getEntityRepository('DeskPRO:CustomDefPerson')->findAll();
		}

		list($first_name, $last_name) = explode(' ', $this->_getRandomText(2));

		$person = array(
			'name' => "$first_name $last_name",
			'first_name' => $first_name,
			'last_name' => $last_name,
			'date_created' => $this->_getRandomDate('string'),
			'gravatar_url' => ''
		);
		if (mt_rand(1, 3) == 1) {
			$person['organization_id'] = $this->_getRandomOrgId();
		}

		$person_ent = new Entity\Person();
		$person = array_merge($person_ent->getScalarData(), $person);

		$db = App::getDb();
		$db->insert('people', $person);
		$person['id'] = $db->lastInsertId();

		$email = array(
			'person_id' => $person['id'],
			'email' => $this->_getRandomText(1) . microtime(true) . '@example.com',
			'email_domain' => 'example.com',
			'is_validated' => 1,
			'date_created' => $person['date_created'],
			'date_validated' => $person['date_created']
		);
		$db->insert('people_emails', $email);
		$email['id'] = $db->lastInsertId();

		$person['primary_email_id'] = $email['id'];
		$db->update('people',
			array('primary_email_id' => $email['id']),
			array('id' => $person['id'])
		);

		$this->_applyLabelsDb('person', $person['id']);

		if (mt_rand(1, 4) == 1) {
			$count = mt_rand(1, 3);
			for ($i = 0; $i < $count; $i++) {
				$this->_addBatchInsert('person2usergroups', array(
					'person_id' => $person['id'],
					'usergroup_id' => $this->_getRandomFromCache('usergroups', 'id')
				), true);
			}
		}

		foreach ($this->_data_cache['person_fields'] AS $field) {
			if ($field->getTypeName() == 'text') {
				$this->_addBatchInsert('custom_data_person', array(
					'person_id' => $person['id'],
					'field_id' => $field->id,
					'root_field_id' => $field->id,
					'value' => 0,
					'input' => $this->_getRandomText(mt_rand(1, 5))
				));
			}
		}

		// todo: contact data, secondary emails
	}

	protected function _loadSla()
	{
		$sla = new Entity\Sla();
		$sla->title = $this->_getRandomText(mt_rand(1, 4));
		$types = array(
			0 => \Application\DeskPRO\Entity\Sla::TYPE_FIRST_RESPONSE,
			1 => \Application\DeskPRO\Entity\Sla::TYPE_RESOLUTION,
			2 => \Application\DeskPRO\Entity\Sla::TYPE_WAITING_TIME
		);
		$sla->sla_type = $types[mt_rand(0, 2)];
		$sla->active_time = \Orb\Util\WorkHoursSet::ACTIVE_24X7;
		$sla->apply_type = mt_rand(1, 6) == 1 ? 'all' : 'manual';

		App::getOrm()->persist($sla);
		App::getOrm()->flush();

		$warning_trigger = new Entity\TicketTrigger();
		$warning_trigger->title = $sla->title . " - SLA Warning";
		$warning_trigger->event_trigger = 'sla.warning';
		$warning_time = mt_rand(30, 500);
		$time = $warning_time . ' minutes';
		$warning_trigger->setEventTriggerOption('time', $time);
		$warning_trigger->terms = array(
			array('type' => 'sla_status', 'op' => 'is', 'options' => array('sla_status' => 'warn', 'sla_id' => $sla->id)),
		);
		$warning_trigger->actions = array(
			array('type' => 'recalculate_sla_status', 'options' => array())
		);

		App::getOrm()->persist($warning_trigger);

		$fail_trigger = new Entity\TicketTrigger();
		$fail_trigger->title = $sla->title . " - SLA Failure";
		$fail_trigger->event_trigger = 'sla.fail';
		$time = mt_rand($warning_time, 600) . ' minutes';
		$fail_trigger->setEventTriggerOption('time', $time);
		$fail_trigger->terms = array(
			array('type' => 'sla_status', 'op' => 'is', 'options' => array('sla_status' => 'fail', 'sla_id' => $sla->id)),
		);
		$fail_trigger->actions = array(
			array('type' => 'recalculate_sla_status', 'options' => array())
		);

		App::getOrm()->persist($fail_trigger);

		$sla->warning_trigger = $warning_trigger;
		$sla->fail_trigger = $fail_trigger;
		App::getOrm()->persist($sla);
	}

	protected function _loadTicketField()
	{
		$field = new Entity\CustomDefTicket();
		$field->title = $this->_getRandomText(2);
		$field->description = $this->_getRandomText(mt_rand(1, 10));
		$field->handler_class = 'Application\DeskPRO\CustomFields\Handler\Text';

		App::getOrm()->persist($field);
	}

	protected function _loadTicketDepartment()
	{
		$department = new Entity\Department();
		$department->title = $this->_getRandomText(mt_rand(2, 4));
		$department->is_tickets_enabled = true;
		$department->is_chat_enabled = false;
		$department->display_order = mt_rand(1, 1000000);
		if (!empty($this->_data_cache['ticket_department_parent'])) {
			$department->parent = $this->_data_cache['ticket_department_parent'];
		}

		App::getOrm()->persist($department);
		App::getOrm()->flush($department);

		$dep_perms = array();

		$this->_getRandomAgent();

		foreach ($this->_data_cache['agents'] AS $agent) {
			$dep_perms[] = array(
				'department_id' => $department->getId(),
				'usergroup_id' => null,
				'person_id' => $agent->getId(),
				'app' => 'tickets',
				'name' => 'full',
				'value' => 1
			);
			$dep_perms[] = array(
				'department_id' => $department->getId(),
				'usergroup_id' => null,
				'person_id' => $agent->getId(),
				'app' => 'tickets',
				'name' => 'assign',
				'value' => 1
			);
		}

		$dep_perms[] = array(
			'department_id' => $department->getId(),
			'usergroup_id' => 1,
			'person_id' => null,
			'app' => 'tickets',
			'name' => 'full',
			'value' => 1
		);

		App::getDb()->batchInsert('department_permissions', $dep_perms);

		if (empty($this->_data_cache['ticket_department_parent'])) {
			$this->_data_cache['ticket_department_parent'] = $department;
		}
	}

	protected $_ticket_statuses = array(
		0 => 'awaiting_user',
		1 => 'awaiting_user',
		2 => 'awaiting_user',
		3 => 'awaiting_user',
		4 => 'awaiting_user',
		5 => 'resolved',
		6 => 'resolved',
		7 => 'resolved',
		8 => 'closed',
		9 => 'closed',
	);

	protected function _loadTicket($i)
	{
		if (!isset($this->_data_cache['ticket_departments'])) {
			$this->_data_cache['ticket_departments'] = App::getEntityRepository('DeskPRO:Department')->getChildDepartments('ticket');
		}
		if (!isset($this->_data_cache['ticket_fields'])) {
			$this->_data_cache['ticket_fields'] = App::getEntityRepository('DeskPRO:CustomDefTicket')->findAll();
		}

		$date_created = $this->_getRandomDate();

		$ticket = array(
			'subject' => $this->_getRandomText(mt_rand(2, 6)),
			'date_created' => $date_created->format('Y-m-d H:i:s'),
			'person_id' => $this->_getRandomPersonId(),
			'department_id' => $this->_getRandomFromCache('ticket_departments', 'id'),
			'creation_system' => Entity\Ticket::CREATED_WEB_API,
			'ref' => App::getRefGenerator()->generateReference('DeskPRO:Ticket')
		);
		if (mt_rand(0, 2) == 0) {
			$rand = $this->_getRandomAgent();
			$ticket['agent_id'] = $rand->id;
		}
		if (time() - $date_created->getTimestamp() > 90*86400) {
			$ticket['status'] = 'closed';
		} else {
			if (mt_rand(0, 100) == 0) {
				$ticket['status'] = 'awaiting_agent';
			} else {
				$ticket['status'] = $this->_ticket_statuses[mt_rand(0, 9)];
			}
		}

		$ticket_ent = new Entity\Ticket(false);
		$ticket = array_merge($ticket_ent->getScalarData(), $ticket);

		$db = App::getDb();
		$db->insert('tickets', $ticket);
		$ticket['id'] = $db->lastInsertId();

		$this->_applyLabelsDb('ticket', $ticket['id']);

		$message = array(
			'ticket_id' => $ticket['id'],
			'person_id' => $ticket['person_id'],
			'is_agent_note' => 0,
			'creation_system' => Entity\TicketMessage::CREATED_WEB_API,
			'message' => $this->_getRandomText(mt_rand(50, 500)),
			'date_created' => $ticket['date_created']
		);
		if (mt_rand(1, 50) == 1) {
			$db->insert('tickets_messages', $message);
			$message['id'] = $db->lastInsertId();
			$this->_addTicketMessageAttachments($ticket['id'], $message);
		} else {
			$this->_addBatchInsert('tickets_messages', $message);
		}

		$message_count = mt_rand(0, 10);
		if ($message_count > 0) {
			$range = $date_created->getTimestamp() + mt_rand(200, max(201, time() - $date_created->getTimestamp()));
			for ($j = 0; $j < $message_count; $j++) {
				$is_agent = !empty($ticket['agent_id']) && mt_rand(0, 1);
				$message = array(
					'ticket_id' => $ticket['id'],
					'person_id' => $is_agent ? $ticket['agent_id'] : $ticket['person_id'],
					'is_agent_note' => ($is_agent && mt_rand(0, 1) ? 1 : 0),
					'creation_system' => Entity\TicketMessage::CREATED_WEB_API,
					'message' => $this->_getRandomText(mt_rand(50, 500)),
					'date_created' => $this->_getRandomDate('string', $ticket['date_created'], $range)
				);
				if (mt_rand(1, 50) == 1) {
					$db->insert('tickets_messages', $message);
					$message['id'] = $db->lastInsertId();
					$this->_addTicketMessageAttachments($ticket['id'], $message);
				} else {
					$this->_addBatchInsert('tickets_messages', $message);
				}
			}
		}

		foreach ($this->_data_cache['ticket_fields'] AS $field) {
			if ($field->getTypeName() == 'text') {
				$this->_addBatchInsert('custom_data_ticket', array(
					'ticket_id' => $ticket['id'],
					'field_id' => $field->id,
					'root_field_id' => $field->id,
					'value' => 0,
					'input' => $this->_getRandomText(mt_rand(1, 5))
				));
			}
		}
	}

	protected function _addTicketMessageAttachments($ticket_id, array $message)
	{
		$files = array(
			DP_WEB_ROOT . '/web/images/dp-logo-16.png' => 'data-load1.png',
			DP_WEB_ROOT . '/web/images/dp-logo-130.png' => 'data-load2.png',
			DP_WEB_ROOT . '/web/images/agent/icons/big-plus.png' => 'data-load3.png',
			DP_WEB_ROOT . '/web/images/admin/portal-off.png' => 'data-load4.png',
			DP_WEB_ROOT . '/README.txt' => 'data-load1.txt',
			DP_WEB_ROOT . '/robots.txt' => 'data-load2.txt',
		);

		$amount = mt_rand(1, 3);
		for ($i = 0; $i < $amount; $i++) {
			$key = array_rand($files);
			$name = $files[$key];
			$mime = substr($name, -3) == 'png' ? 'image/png' : 'text/plain';

			$key = str_replace('/', DIRECTORY_SEPARATOR, $key);

			$upload = new \Symfony\Component\HttpFoundation\File\UploadedFile($key, $name, $mime, filesize($key), 0);
			$blob = App::getContainer()->getAttachmentAccepter()->accept($upload);

			$this->_addBatchInsert('tickets_attachments', array(
				'ticket_id' => $ticket_id,
				'person_id' => $message['person_id'],
				'message_id' => $message['id'],
				'blob_id' => $blob->id,
				'is_agent_note' => 0,
				'is_inline' => 0
			));
		}
	}

	protected function _loadTicketFilter()
	{
		$possible_terms = array(
			0 => array('type' => 'subject', 'op' => 'contains', 'options' => array('subject' => 'test')),
			1 => array('type' => 'urgency', 'op' => 'gte', 'options' => array('num' => '5')),
			2 => array('type' => 'label', 'op' => 'is', 'options' => array('labels' => array('test'))),
			3 => array('type' => 'person_email_domain', 'op' => 'is', 'options' => array('email_domain' => 'example.com')),
			4 => array('type' => 'person_contact_phone', 'op' => 'contains', 'options' => array('phone' => '123')),
			5 => array('type' => 'org_label', 'op' => 'is', 'options' => array('label' => 'organization')),
			6 => array('type' => 'org_email_domain', 'op' => 'is', 'options' => array('email_domain' => 'example.com')),
			7 => array('type' => 'agent', 'op' => 'is', 'options' => array('agent' => '0')),
			8 => array('type' => 'organization', 'op' => 'is', 'options' => array('organization' => $this->_getRandomOrgId())),
			9 => array(
				'type' => 'date_created',
				'op' => 'lte',
				'options' => array(
					'date1' => '',
					'date2' => '',
					'date1_relative' => '5',
					'date1_relative_type' => 'days',
					'date2_relative' => '',
					'date2_relative_type' => '',
				),
			),
		);

		$filter = new Entity\TicketFilter();
		$filter->title = $this->_getRandomText(2);
		$filter->is_global = true;

		$terms = array();
		$count = mt_rand(1, 4);
		for ($i = 0; $i < $count; $i++) {
			$k = mt_rand(0, 9);
			$terms[$k] = $possible_terms[$k];
		}
		$filter->terms = array_values($terms);

		App::getOrm()->persist($filter);
	}

	protected function _loadTicketMacro()
	{
		$macro = new Entity\TicketMacro();
		$macro->title = $this->_getRandomText(mt_rand(2, 4));
		$macro->is_global = (mt_rand(0, 1) == 1);
		$macro->is_enabled = true;
		$macro->actions = array(
			array('type' => 'agent', 'options' => array('agent' => '-1'))
		);
		$macro->person = $this->_getRandomAgent();

		App::getOrm()->persist($macro);
	}

	protected function _loadTicketSnippetCategory()
	{
		$category = new Entity\TextSnippetCategory();
		$category->is_global = true;
		$category->person = $this->_getRandomAgent();
		$category->typename = 'tickets';

		App::getOrm()->persist($category);
		App::getOrm()->flush();

		App::getDb()->replace('object_lang', array(
			'language_id' => 1,
			'ref'         => 'text_snippet_categories.'.$category->getId(),
			'prop_name'   => 'title',
			'value'       => $this->_getRandomText(mt_rand(1, 4)),
		));
	}

	protected function _loadTicketSnippet()
	{
		if (!isset($this->_data_cache['ticket_snippet_categories'])) {
			$this->_data_cache['ticket_snippet_categories'] = App::getEntityRepository('DeskPRO:TextSnippetCategory')->findAll();
		}

		$snippet = new Entity\TextSnippet();
		$snippet->category = $this->_getRandomFromCache('text_snippet_categories');
		$snippet->person = $this->_getRandomAgent();

		$title = $this->_getRandomText(mt_rand(2, 5));
		$text = $this->_getRandomText(mt_rand(10, 200));

		App::getOrm()->persist($snippet);
		App::getOrm()->flush();

		App::getDb()->replace('object_lang', array(
			'language_id' => 1,
			'ref'         => 'text_snippets.'.$snippet->getId(),
			'prop_name'   => 'title',
			'value'       => $title,
		));

		App::getDb()->replace('object_lang', array(
			'language_id' => 1,
			'ref'         => 'text_snippets.'.$snippet->getId(),
			'prop_name'   => 'snippet',
			'value'       => $text,
		));
	}

	protected function _loadChatSnippetCategory()
	{
		$category = new Entity\TextSnippetCategory();
		$category->typename = 'chat';
		$category->is_global = true;
		$category->title = $this->_getRandomText(mt_rand(1, 4));
		$category->person = $this->_getRandomAgent();

		App::getOrm()->persist($category);
	}

	protected function _loadChatSnippet()
	{
		if (!isset($this->_data_cache['chat_snippet_categories'])) {
			$this->_data_cache['chat_snippet_categories'] = App::getEntityRepository('DeskPRO:TextSnippetCategory')->getAllByType('chat');
		}

		$snippet = new Entity\TextSnippet();
		$snippet->title = $this->_getRandomText(mt_rand(2, 5));
		$snippet->snippet = $this->_getRandomText(mt_rand(10, 200));

		$snippet->category = $this->_getRandomFromCache('chat_snippet_categories');
		$snippet->person = $this->_getRandomAgent();

		App::getOrm()->persist($snippet);
	}

	protected function _loadChatDepartment()
	{
		$department = new Entity\Department();
		$department->title = $this->_getRandomText(mt_rand(2, 4));
		$department->is_tickets_enabled = false;
		$department->is_chat_enabled = true;
		$department->display_order = mt_rand(1, 1000000);
		if (!empty($this->_data_cache['chat_department_parent'])) {
			$department->parent = $this->_data_cache['chat_department_parent'];
		}

		App::getOrm()->persist($department);
		App::getOrm()->flush($department);

		$dep_perms = array();

		$this->_getRandomAgent();
		foreach ($this->_data_cache['agents'] AS $agent) {
			$dep_perms[] = array(
				'department_id' => $department->getId(),
				'usergroup_id' => null,
				'person_id' => $agent->getId(),
				'app' => 'chat',
				'name' => 'full',
				'value' => 1
			);
		}

		$dep_perms[] = array(
			'department_id' => $department->getId(),
			'usergroup_id' => 1,
			'person_id' => null,
			'app' => 'chat',
			'name' => 'full',
			'value' => 1
		);

		App::getDb()->batchInsert('department_permissions', $dep_perms);

		if (empty($this->_data_cache['chat_department_parent'])) {
			$this->_data_cache['chat_department_parent'] = $department;
		}
	}

	protected function _loadFeedbackType()
	{
		$category = new Entity\FeedbackCategory();
		$category->title = $this->_getRandomText(mt_rand(1, 4));
		$category->display_order = mt_rand(1, 1000000);

		App::getOrm()->persist($category);
		App::getOrm()->flush();

		App::getDb()->insert('feedback_category2usergroup', array(
			'category_id'  => $category->getId(),
			'usergroup_id' => 1
		));
	}

	protected function _loadFeedbackStatus()
	{
		$category = new Entity\FeedbackStatusCategory();
		$category->title = $this->_getRandomText(mt_rand(1, 4));
		$category->display_order = mt_rand(1, 1000000);
		$category->status_type = mt_rand(1, 2) == 1 ? 'active' : 'closed';

		App::getOrm()->persist($category);
	}

	protected function _loadFeedback($i)
	{
		if (!isset($this->_data_cache['feedback_types'])) {
			$this->_data_cache['feedback_types'] = App::getEntityRepository('DeskPRO:FeedbackCategory')->findAll();
		}
		if (!isset($this->_data_cache['feedback_statuses'])) {
			$this->_data_cache['feedback_statuses'] = App::getEntityRepository('DeskPRO:FeedbackStatusCategory')->findAll();
		}

		$title = $this->_getRandomText(mt_rand(2, 6));

		$feedback = array(
			'title' => $title,
			'slug' => \Orb\Util\Strings::slugifyTitle($title) ?: 'view',
			'content' => htmlspecialchars($this->_getRandomText(30)),
			'date_created' => $this->_getRandomDate('string'),
			'status' => 'published',
			'person_id' => $this->_getRandomAgent(true),
			'category_id' => $this->_getRandomFromCache('feedback_types', 'id')
		);

		if (mt_rand(1, 3) == 1) {
			$feedback['status'] = 'new';
		} else {
			$status = $this->_getRandomFromCache('feedback_statuses');
			$feedback['status'] = $status->status_type;
			$feedback['status_category_id'] = $status->id;
		}

		$feedback_ent = new Entity\Feedback();
		$feedback = array_merge($feedback_ent->getScalarData(), $feedback);

		$db = App::getDb();
		$db->insert('feedback', $feedback);
		$feedback['id'] = $db->lastInsertId();

		$this->_applyLabelsDb('feedback', $feedback['id']);

		// todo: attachments, user categories, validation?, comments
	}

	protected function _loadArticleField()
	{
		$field = new Entity\CustomDefArticle();
		$field->title = $this->_getRandomText(2);
		$field->description = $this->_getRandomText(mt_rand(1, 10));
		$field->handler_class = 'Application\DeskPRO\CustomFields\Handler\Text';

		App::getOrm()->persist($field);
	}

	protected function _loadArticleCategory()
	{
		$category = new Entity\ArticleCategory();
		$category->title = $this->_getRandomText(mt_rand(1, 4));
		$category->display_order = mt_rand(1, 1000000);

		App::getOrm()->persist($category);
		App::getOrm()->flush();

		App::getDb()->insert('article_category2usergroup', array(
			'category_id'  => $category->getId(),
			'usergroup_id' => 1
		));
	}

	protected function _loadArticle($i)
	{
		if (!isset($this->_data_cache['article_categories'])) {
			$this->_data_cache['article_categories'] = App::getEntityRepository('DeskPRO:ArticleCategory')->findAll();
		}
		if (!isset($this->_data_cache['article_fields'])) {
			$this->_data_cache['article_fields'] = App::getEntityRepository('DeskPRO:CustomDefArticle')->findAll();
		}

		$title = $this->_getRandomText(mt_rand(2, 6));

		$article = array(
			'title' => $title,
			'slug' => \Orb\Util\Strings::slugifyTitle($title) ?: 'view',
			'content' => htmlspecialchars($this->_getRandomText(30)),
			'date_created' => $this->_getRandomDate('string'),
			'status' => 'published',
			'person_id' => $this->_getRandomAgent(true)
		);

		$article_ent = new Entity\Article();
		$article = array_merge($article_ent->getScalarData(), $article);

		$db = App::getDb();
		$db->insert('articles', $article);
		$article['id'] = $db->lastInsertId();

		$this->_applyLabelsDb('article', $article['id']);

		$db->insert('article_to_categories', array(
			'article_id' => $article['id'],
			'category_id' => $this->_getRandomFromCache('article_categories', 'id')
		));

		foreach ($this->_data_cache['article_fields'] AS $field) {
			if ($field->getTypeName() == 'text') {
				$db->insert('custom_data_article', array(
					'article_id' => $article['id'],
					'field_id' => $field->id,
					'root_field_id' => $field->id,
					'value' => 0,
					'input' => $this->_getRandomText(mt_rand(1, 5))
				));
			}
		}

		// todo: products, attachments, comments (with validation), varied statuses
	}

	protected function _loadNewsCategory()
	{
		$category = new Entity\NewsCategory();
		$category->title = $this->_getRandomText(mt_rand(1, 4));
		$category->display_order = mt_rand(1, 1000000);

		App::getOrm()->persist($category);
		App::getOrm()->flush();

		App::getDb()->insert('news_category2usergroup', array(
			'category_id'  => $category->getId(),
			'usergroup_id' => 1
		));
	}

	protected function _loadNews($i)
	{
		if (!isset($this->_data_cache['news_categories'])) {
			$this->_data_cache['news_categories'] = App::getEntityRepository('DeskPRO:NewsCategory')->findAll();
		}

		$title = $this->_getRandomText(mt_rand(2, 6));

		$news = array(
			'title' => $title,
			'slug' => \Orb\Util\Strings::slugifyTitle($title) ?: 'view',
			'content' => htmlspecialchars($this->_getRandomText(30)),
			'date_created' => $this->_getRandomDate('string'),
			'status' => 'published',
			'person_id' => $this->_getRandomAgent(true),
			'category_id' =>  $this->_getRandomFromCache('news_categories', 'id')
		);

		$ent = new Entity\News();
		$news = array_merge($ent->getScalarData(), $news);

		$db = App::getDb();
		$db->insert('news', $news);
		$news['id'] = $db->lastInsertId();

		$this->_applyLabelsDb('news', $news['id']);

		// todo: attachments, comments (with validation)
	}

	protected function _loadDownloadCategory()
	{
		$category = new Entity\DownloadCategory();
		$category->title = $this->_getRandomText(mt_rand(1, 4));
		$category->display_order = mt_rand(1, 1000000);

		App::getOrm()->persist($category);
		App::getOrm()->flush();

		App::getDb()->insert('download_category2usergroup', array(
			'category_id'  => $category->getId(),
			'usergroup_id' => 1
		));
	}

	protected function _loadDownload($i)
	{
		if (!isset($this->_data_cache['download_categories'])) {
			$this->_data_cache['download_categories'] = App::getEntityRepository('DeskPRO:DownloadCategory')->findAll();
		}

		$title = $this->_getRandomText(mt_rand(2, 6));

		$download = array(
			'title' => $title,
			'slug' => \Orb\Util\Strings::slugifyTitle($title) ?: 'view',
			'content' => htmlspecialchars($this->_getRandomText(30)),
			'date_created' => $this->_getRandomDate('string'),
			'status' => 'published',
			'person_id' => $this->_getRandomAgent(true),
			'category_id' =>  $this->_getRandomFromCache('download_categories', 'id')
		);

		$ent = new Entity\Download();
		$download = array_merge($ent->getScalarData(), $download);

		$db = App::getDb();
		$db->insert('downloads', $download);
		$download['id'] = $db->lastInsertId();

		$this->_applyLabelsDb('download', $download['id']);

		// todo: attachments, comments (with validation)
	}

	protected function _loadGlossary()
	{
		$def = new Entity\GlossaryWordDefinition();
		$def->definition = $this->_getRandomText(mt_rand(5, 10));
		$word_count = mt_rand(1, 5);
		for ($i = 0; $i < $word_count; $i++) {
			$start = chr(mt_rand(64, 90)); // @ and A-Z
			$def->addWord($start . $this->_getRandomText(1));
		}

		if (count($def->words)) {
			App::getOrm()->persist($def);
			App::getOrm()->flush(); // need to flush each as might get a dupe error
		}
	}

	protected function _loadTask($i)
	{
		$task = new Entity\Task();
		$task->title = $this->_getRandomText(mt_rand(2, 8));
		$task->person = $this->_getRandomAgent();
		$task->setVisibility(mt_rand(1, 3) == 1 ? 0 : 1);
		$task->date_created = $this->_getRandomDate();
		if (mt_rand(0, 1)) {
			$task->due_date = new \DateTime('@' . ($task->date_created->getTimestamp() + mt_rand(10000, 10000000)));
		}
		if (mt_rand(1, 3) == 1) {
			$task->assigned_agent = $this->_getRandomAgent();
		} else if (mt_rand(1, 3) == 1) {
			$task->assigned_agent_team = $this->_getRandomAgentTeam();
		}

		$task->setCompleted(mt_rand(1, 3) == 1);

		// todo: comments, ticket linking

		App::getOrm()->persist($task);
		$this->_applyLabels($task);
	}

	protected function _loadTwitterUser()
	{
		$this->_addBatchInsert('twitter_users', array(
			'id' => mt_rand(1, mt_getrandmax()),
			'name' => $this->_getRandomText(2),
			'screen_name' => $this->_getRandomText(1) . microtime(true),
			'profile_image_url' => '',
			'language' => 'en',
			'is_protected' => 0,
			'is_verified' => 0,
			'location' => '',
			'description' => $this->_getRandomText(mt_rand(3, 10)),
			'is_geo_enabled' => 0,
			'is_stub' => 0,
			'url' => '',
			'last_timeline_update' => null,
			'last_profile_update' => null,
			'followers_count' => 0,
			'friends_count' => 0,
			'last_follow_update' => null
		), true);
	}

	protected function _loadTwitterStatus()
	{
		$db = App::getDb();

		$data = array(
			'id' => mt_rand(1, mt_getrandmax()),
			'user_id' => $this->_getRandomTwitterUserId(),
			'text' => $this->_getRandomText(mt_rand(1, 20)),
			'date_created' => $this->_getRandomDate('string')
		);

		$modified = $db->executeUpdate("
			INSERT IGNORE INTO twitter_statuses
				(id, user_id, text, is_truncated, date_created)
			VALUES (?, ?, ?, 0, ?)
		", array($data['id'], $data['user_id'], $data['text'], $data['date_created']));

		if ($modified == 2) {
			return;
		}

		$status_types = array(
			0 => 'direct',
			1 => 'reply',
			2 => 'mention',
			3 => 'retweet',
			4 => 'timeline',
			5 => 'timeline',
			6 => 'timeline',
			7 => 'timeline',
			8 => null
		);

		$this->_addBatchInsert('twitter_accounts_statuses', array(
			'account_id' => 1,
			'status_id' => $data['id'],
			'agent_id' => mt_rand(0, 1) ? $this->_getRandomAgent(true) : null,
			'agent_team_id' => null,
			'retweeted_id' => null,
			'in_reply_to_id' => null,
			'date_created' => $data['date_created'],
			'status_type' => $status_types[mt_rand(0, 8)],
			'is_archived' => mt_rand(1, 1000) == 1 ? 0 : 1,
			'is_favorited' => mt_rand(1, 10000) == 1 ? 1 : 0,
			'action_agent_id' => null
		));
	}

	protected function _getRandomDate($format = null, $start = null, $end = null)
	{
		if ($start === null) {
			$start = $this->_start_ts;
		}
		if ($start instanceof \DateTime) {
			$start = $start->getTimestamp();
		}
		$start = intval($start);

		if ($end === null) {
			$end = time();
		}
		if ($end instanceof \DateTime) {
			$end = $end->getTimestamp();
		}
		$end = intval($end);
		if ($end <= $start) {
			$end = $start + 1000;
		}

		$rand = mt_rand($start, $end);
		switch ($format) {
			case 'ts':
				return $rand;

			case 'string':
				return gmdate('Y-m-d H:i:s', $rand);

			default:
				return new \DateTime('@' . $rand);
		}
	}

	protected $_person_hits = 0;
	protected function _getRandomPersonId()
	{
		if ($this->_person_hits <= 0 || !isset($this->_data_cache['random_people_ids'])) {
			$this->_data_cache['random_people_ids'] = App::getDb()->fetchAllCol('
				SELECT id
				FROM people
				WHERE is_agent = 0
				ORDER BY RAND()
				LIMIT 1000
			');
			if (!$this->_data_cache['random_people_ids']) {
				$this->_data_cache['random_people_ids'] = array_keys($this->_data_cache['agents']);
			}

			$this->_person_hits = count($this->_data_cache['random_people_ids']);
		}
		$this->_person_hits--;

		return $this->_getRandomFromCache('random_people_ids');
	}

	protected function _getRandomAgent($id = false)
	{
		if (!isset($this->_data_cache['agents'])) {
			$this->_data_cache['agents'] = App::getEntityRepository('DeskPRO:Person')->getAgents();
		}

		return $this->_getRandomFromCache('agents', $id ? 'id' : null);
	}

	protected function _getRandomAgentTeam()
	{
		if (!isset($this->_data_cache['agent_teams'])) {
			$this->_data_cache['agent_teams'] = App::getEntityRepository('DeskPRO:AgentTeam')->getTeams();
		}

		return $this->_getRandomFromCache('agent_teams');
	}

	protected function _getRandomOrgId()
	{
		if (!isset($this->_data_cache['random_org_ids'])) {
			$this->_data_cache['random_org_ids'] = App::getDb()->fetchAllCol('
				SELECT id
				FROM organizations
				ORDER BY RAND()
				LIMIT 1000
			');
		}

		return $this->_getRandomFromCache('random_org_ids');
	}

	protected $_twitter_hits = 0;
	protected function _getRandomTwitterUserId()
	{
		if ($this->_twitter_hits <= 0 || !isset($this->_data_cache['random_twitter_user_ids'])) {
			$this->_data_cache['random_twitter_user_ids'] = App::getDb()->fetchAllCol('
				SELECT id
				FROM twitter_users
				ORDER BY RAND()
				LIMIT 1000
			');

			$this->_twitter_hits = count($this->_data_cache['random_twitter_user_ids']);
		}
		$this->_twitter_hits--;

		return $this->_getRandomFromCache('random_twitter_user_ids');
	}

	protected function _getRandomFromCache($key, $obj_field = null)
	{
		if (!isset($this->_data_cache[$key]) || empty($this->_data_cache[$key])) {
			return null;
		}

		$rand = array_rand($this->_data_cache[$key]);
		$data = $this->_data_cache[$key][$rand];
		return $obj_field ? $data->$obj_field : $data;
	}

	protected function _addBatchInsert($table, array $data, $ignore = false)
	{
		if ($ignore) {
			if (!isset($this->_batch_insert_ignore[$table])) {
				$this->_batch_insert_ignore[$table] = array();
			}

			$this->_batch_insert_ignore[$table][] = $data;
		} else {
			if (!isset($this->_batch_insert[$table])) {
				$this->_batch_insert[$table] = array();
			}

			$this->_batch_insert[$table][] = $data;
		}
	}

	protected $_label_type_map = array(
		'article' => array('labels_articles', 'article_id'),
		'download' => array('labels_downloads', 'download_id'),
		'feedback' => array('labels_feedback', 'feedback_id'),
		'news' => array('labels_news', 'news_id'),
		'organization' => array('labels_organizations', 'organization_id'),
		'person' => array('labels_people', 'person_id'),
		'ticket' => array('labels_tickets', 'ticket_id'),
	);

	protected function _applyLabelsDb($type, $id)
	{
		if (!isset($this->_label_type_map[$type])) {
			throw new \Exception("Unknown label type $type");
		}

		if (mt_rand(0, 1) == 0) {
			return;
		}

		$labels = mt_rand(0, 4);
		if ($labels && isset($this->_label_type_map[$type])) {
			for ($i = 0; $i < $labels; $i++) {
				list($table, $field) = $this->_label_type_map[$type];

				$label = $this->_getRandomText(1);
				$label = strtolower(trim($label));

				$this->_addBatchInsert($table, array(
					$field => $id,
					'label' => $label
				), true);

				$type_name = $type . 's';
				if (!isset($this->_batch_insert_label_def[$type_name])) {
					$this->_batch_insert_label_def[$type_name] = array();
				}
				if (!isset($this->_batch_insert_label_def[$type_name][$label])) {
					$this->_batch_insert_label_def[$type_name][$label] = 1;
				} else {
					$this->_batch_insert_label_def[$type_name][$label]++;
				}
			}
		}
	}

	protected function _applyLabels($entity)
	{
		if (!method_exists($entity, 'getLabelManager')) {
			return;
		}

		/** @var $manager \Application\DeskPRO\Labels\LabelManager */
		$manager = $entity->getLabelManager();

		$labels = mt_rand(0, 4);
		if ($labels) {
			App::getOrm()->flush(); // must generate an ID first

			for ($i = 0; $i < $labels; $i++) {
				$label = $manager->addLabel($this->_getRandomText(1));
				App::getOrm()->persist($label);
			}
		}
	}

	protected $_words = null;
	protected $_max_word_index;

	protected function _getRandomText($word_length = 1)
	{
		if (!is_array($this->_words)) {
			if ($this->_wordlist_file) {
				$fp = fopen($this->_wordlist_file, 'r');
				$this->_words = array();
				while (!feof($fp)) {
					$line = fgets($fp);
					$line = trim($line);
					$line = str_replace(array("'", '"'), '', $line);

					$this->_words[] = $line;
				}
			} else {
				$this->_words = explode(' ', 'Lorem ipsum dolor sit amet consectetur adipiscing elit Morbi ac semper lorem Mauris ut suscipit leo Suspendisse orci sem consequat venenatis quis volutpat sit amet lorem Nulla sed sodales leo Duis erat magna commodo nec consectetur quis rhoncus ac arcu Suspendisse egestas metus id nunc interdum nec volutpat orci laoreet Ut porttitor nisi vel urna congue eleifend Fusce semper justo sit amet elit tempor ut ultrices neque pharetra In at tellus at dolor consectetur dapibus in eleifend est Aenean sed neque id sapien aliquet semper id at velit Nullam laoreet est vitae dui pulvinar consectetur Aenean ipsum ipsum convallis ac pellentesque nec ullamcorper sit amet ipsum Fusce accumsan orci in bibendum ornare dolor nunc condimentum massa eget aliquam lectus tortor sed est Proin tempor quam congue mi tempus vitae cursus orci interdum Aliquam aliquet vulputate cursus Etiam hendrerit lorem vitae ipsum lacinia feugiat Fusce ornare purus et felis placerat ut venenatis nisl dignissim Mauris sed lacus nunc Curabitur et metus quis orci molestie sodales Suspendisse interdum cursus ullamcorper Donec pretium consequat lacus ac condimentum Fusce lacinia faucibus urna eu varius Etiam volutpat porta nisi in euismod sapien consequat vitae Ut feugiat porttitor dui nec vehicula Suspendisse sed nibh id leo euismod scelerisque Praesent malesuada sagittis dui et iaculis ante vulputate id Quisque risus nec orci eleifend volutpat sit amet sit amet lectus Aliquam ut felis felis mattis turpis Nulla eget orci lorem id rutrum orci Donec neque nisl tristique ac fringilla vel ullamcorper vitae erat Praesent erat metus tristique in gravida id tempus fringilla diam Integer vitae aliquet nulla Sed dictum lectus ac sem rhoncus et laoreet augue volutpat Ut venenatis laoreet mauris non pulvinar Etiam lacinia augue vel elit facilisis quis molestie sapien congue Praesent eu lacus justo vitae iaculis libero Curabitur nibh massa Aenean sed dui orci Suspendisse vehicula nibh eu dictum bibendum lorem nisl congue felis ac dictum mauris nisl vitae orci Phasellus et turpis massa tempor sodales eget eget quam Cras ut purus nisl sit amet ultricies lacus Nunc congue molestie accumsan Sed ut volutpat dui Donec sit amet nunc rhoncus risus convallis adipiscing Aenean tincidunt tempor consequat Vivamus blandit lacus quam ornare tortor Vestibulum tellus in orci ultrices semper Aenean sit amet libero ipsum aliquet condimentum Quisque volutpat congue felis vel hendrerit Proin congue enim et mi mattis tempor Praesent nec ante nec mauris suscipit pulvinar condimentum eu massa Aliquam iaculis ipsum sed ligula condimentum sed ultrices odio iaculis Nulla viverra ipsum et auctor viverra dolor est condimentum nisl in tincidunt erat massa vitae lacus Donec convallis tincidunt nisl vitae laoreet Mauris ligula mauris lacinia quis dictum volutpat tincidunt ac neque Phasellus dapibus suscipit pulvinar Fusce lacus est ultrices adipiscing sed condimentum sit amet leo Proin mauris ante tempor non tempor at commodo id mi Quisque ac massa justo Quisque lacinia malesuada ipsum hendrerit facilisis Nulla metus augue viverra placerat dapibus ac lacus Integer lectus metus laoreet semper eget dictum at purus Sed');
			}
			$this->_max_word_index = count($this->_words) - 1;
		}

		$output = array();
		for ($i = 0; $i < $word_length; $i++) {
			$output[] = $this->_words[mt_rand(0, $this->_max_word_index)];
		}

		return implode(' ', $output);
	}

	protected function _getPerson($id)
	{
		return App::getEntityRepository('DeskPRO:Person')->find($id);
	}
}
