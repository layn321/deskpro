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

namespace Application\InstallBundle\Upgrade\Build;

use Application\DeskPRO\App;

class Build1352975028 extends AbstractBuild
{
	public function run()
	{
		$this->out("Add a default SLA if necessary");

		$db = $this->container->getDb();

		$count = $db->fetchColumn("
			SELECT COUNT(*)
			FROM slas
		");
		if ($count) {
			return;
		}

		$db->insert('slas', array(
			'title' => 'First Response',
			'sla_type' => 'first_response',
			'active_time' => 'default',
			'work_start' => 32400,
			'work_end' => 61200,
			'work_days' => serialize(array(1 => true, 2 => true,3 => true, 4 => true, 5 => true)),
			'work_timezone' => App::getSetting('core.default_timezone'),
			'work_holidays' => serialize(array()),
			'apply_all' => 1,
			'allow_agent_manual' => 0
		));
		$sla_id = $db->lastInsertId();

		$db->insert('ticket_triggers', array(
			'title' => 'First Response - SLA Warning',
			'event_trigger' => 'sla.warning',
			'is_enabled' => 1,
			'terms' => serialize(array(
				array('type' => 'sla_status', 'op' => 'is', 'options' => array('sla_status' => 'warn', 'sla_id' => $sla_id))
			)),
			'actions' => serialize(array(
				array('type' => 'recalculate_sla_status', 'options' => array())
			)),
			'sys_name' => NULL,
			'run_order' => 5400,
			'event_trigger_options' => serialize(array(
				'time' => '90 minutes'
			)),
			'terms_any' => serialize(array()),
			'date_created' => gmdate('Y-m-d H:i:s')
		));
		$warning_trigger_id = $db->lastInsertId();

		$db->insert('ticket_triggers', array(
			'title' => 'First Response - SLA Failure',
			'event_trigger' => 'sla.fail',
			'is_enabled' => 1,
			'terms' => serialize(array(
				array('type' => 'sla_status', 'op' => 'is', 'options' => array('sla_status' => 'fail', 'sla_id' => $sla_id))
			)),
			'actions' => serialize(array(
				array('type' => 'recalculate_sla_status', 'options' => array())
			)),
			'sys_name' => NULL,
			'run_order' => 7200,
			'event_trigger_options' => serialize(array(
				'time' => '120 minutes'
			)),
			'terms_any' => serialize(array()),
			'date_created' => gmdate('Y-m-d H:i:s')
		));
		$fail_trigger_id = $db->lastInsertId();

		$db->update('slas', array(
			'warning_trigger_id' => $warning_trigger_id,
			'fail_trigger_id' => $fail_trigger_id
		), array('id' => $sla_id));
	}
}