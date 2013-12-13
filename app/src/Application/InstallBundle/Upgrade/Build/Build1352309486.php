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

class Build1352309486 extends AbstractBuild
{
	public function run()
	{
		$this->out("Ensure default triggers are set");
		$triggers = array(
			array(
				'title' => 'email_validation.email',
				'event_trigger' => 'new_ticket',
				'event_trigger_options' => 'N;',
				'is_enabled' => '0',
				'terms' => 'a:2:{i:0;a:3:{s:4:"type";s:11:"is_new_user";s:2:"op";s:2:"is";s:7:"options";a:1:{s:11:"is_new_user";s:1:"1";}}i:1;a:3:{s:4:"type";s:15:"creation_system";s:2:"op";s:2:"is";s:7:"options";a:1:{s:15:"creation_system";s:14:"gateway.person";}}}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:22:"force_email_validation";s:7:"options";a:1:{s:22:"force_email_validation";s:1:"1";}}}',
				'sys_name' => 'email_validation.email',
				'run_order' => '0',
				'date_created' => '2012-11-07 12:47:52',
			),
			array(
				'title' => 'email_validation.web',
				'event_trigger' => 'new_ticket',
				'event_trigger_options' => 'N;',
				'is_enabled' => '0',
				'terms' => 'a:2:{i:0;a:3:{s:4:"type";s:11:"is_new_user";s:2:"op";s:2:"is";s:7:"options";a:1:{s:11:"is_new_user";s:1:"1";}}i:1;a:3:{s:4:"type";s:15:"creation_system";s:2:"op";s:2:"is";s:7:"options";a:1:{s:15:"creation_system";s:10:"web.person";}}}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:22:"force_email_validation";s:7:"options";a:1:{s:22:"force_email_validation";s:1:"1";}}}',
				'sys_name' => 'email_validation.web',
				'run_order' => '0',
				'date_created' => '2012-11-07 12:47:52',
			),
			array(
				'title' => 'email_validation.widget',
				'event_trigger' => 'new_ticket',
				'event_trigger_options' => 'N;',
				'is_enabled' => '0',
				'terms' => 'a:2:{i:0;a:3:{s:4:"type";s:11:"is_new_user";s:2:"op";s:2:"is";s:7:"options";a:1:{s:11:"is_new_user";s:1:"1";}}i:1;a:3:{s:4:"type";s:15:"creation_system";s:2:"op";s:2:"is";s:7:"options";a:1:{s:15:"creation_system";s:6:"widget";}}}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:22:"force_email_validation";s:7:"options";a:1:{s:22:"force_email_validation";s:1:"1";}}}',
				'sys_name' => 'email_validation.widget',
				'run_order' => '0',
				'date_created' => '2012-11-07 12:47:52',
			),
			array(
				'title' => 'response.reply_confirm',
				'event_trigger' => 'update.user',
				'event_trigger_options' => 'N;',
				'is_enabled' => '0',
				'terms' => 'a:1:{i:0;a:3:{s:4:"type";s:14:"new_reply_user";s:2:"op";s:2:"is";s:7:"options";a:1:{s:2:"do";s:1:"1";}}}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:39:"enable_user_notification_new_reply_user";s:7:"options";a:1:{s:6:"enable";s:1:"1";}}}',
				'sys_name' => 'response.reply_confirm',
				'run_order' => '0',
				'date_created' => '2012-11-07 12:47:52',
			),
			array(
				'title' => '',
				'event_trigger' => 'new.email.user',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:10:"department";s:7:"options";a:1:{s:10:"department";s:13:"email_account";}}}',
				'sys_name' => 'setdep.newemail_user',
				'run_order' => '0',
				'date_created' => '2012-10-22 19:56:52',
			),
			array(
				'title' => '',
				'event_trigger' => 'new.web.user',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:19:"set_gateway_address";s:7:"options";a:1:{s:18:"gateway_address_id";s:10:"department";}}}',
				'sys_name' => 'setgateway.newweb_user',
				'run_order' => '0',
				'date_created' => '2012-10-22 19:57:20',
			),
			array(
				'title' => '',
				'event_trigger' => 'new.email.agent',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:10:"department";s:7:"options";a:1:{s:10:"department";s:13:"email_account";}}}',
				'sys_name' => 'setdep.newemail_agent',
				'run_order' => '0',
				'date_created' => '2012-10-22 19:57:36',
			),
			array(
				'title' => '',
				'event_trigger' => 'new.web.agent.portal',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:19:"set_gateway_address";s:7:"options";a:1:{s:18:"gateway_address_id";s:10:"department";}}}',
				'sys_name' => 'setgateway.newweb_agent',
				'run_order' => '0',
				'date_created' => '2012-10-22 19:59:50',
			),
			array(
				'title' => '',
				'event_trigger' => 'update.agent',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:1:{i:0;a:3:{s:4:"type";s:10:"department";s:2:"op";s:7:"changed";s:7:"options";a:1:{s:10:"department";s:1:"1";}}}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:19:"set_gateway_address";s:7:"options";a:1:{s:18:"gateway_address_id";s:10:"department";}}}',
				'sys_name' => 'setgateway.update_agent',
				'run_order' => '0',
				'date_created' => '2012-10-22 20:00:25',
			),
			array(
				'title' => '',
				'event_trigger' => 'update.user',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:1:{i:0;a:3:{s:4:"type";s:10:"department";s:2:"op";s:7:"changed";s:7:"options";a:1:{s:10:"department";s:1:"1";}}}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:19:"set_gateway_address";s:7:"options";a:1:{s:18:"gateway_address_id";s:10:"department";}}}',
				'sys_name' => 'setgateway.update_user',
				'run_order' => '0',
				'date_created' => '2012-10-22 20:03:33',
			),
			array(
				'title' => '',
				'event_trigger' => 'update.agent',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:21:"set_initial_from_name";s:7:"options";a:2:{s:9:"from_name";s:18:"{{performer.name}}";s:7:"to_whom";s:1:"0";}}}',
				'sys_name' => 'setfrom.reply_agent',
				'run_order' => '0',
				'date_created' => '2012-10-24 14:10:26',
			),
			array(
				'title' => '',
				'event_trigger' => 'update.user',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:21:"set_initial_from_name";s:7:"options";a:2:{s:9:"from_name";s:18:"{{performer.name}}";s:7:"to_whom";s:1:"0";}}}',
				'sys_name' => 'setfrom.reply_user',
				'run_order' => '0',
				'date_created' => '2012-10-24 14:12:46',
			),
			array(
				'title' => '',
				'event_trigger' => 'new.email.user',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:21:"set_initial_from_name";s:7:"options";a:2:{s:9:"from_name";s:18:"{{performer.name}}";s:7:"to_whom";s:5:"agent";}}}',
				'sys_name' => 'setfrom.newemail_user',
				'run_order' => '0',
				'date_created' => '2012-10-24 14:14:05',
			),
			array(
				'title' => '',
				'event_trigger' => 'new.web.user',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:21:"set_initial_from_name";s:7:"options";a:2:{s:9:"from_name";s:18:"{{performer.name}}";s:7:"to_whom";s:5:"agent";}}}',
				'sys_name' => 'setfrom.newweb_user',
				'run_order' => '0',
				'date_created' => '2012-10-24 14:14:05',
			),
			array(
				'title' => '',
				'event_trigger' => 'new.email.agent',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:21:"set_initial_from_name";s:7:"options";a:2:{s:9:"from_name";s:18:"{{performer.name}}";s:7:"to_whom";s:1:"0";}}}',
				'sys_name' => 'setfrom.newemail_agent',
				'run_order' => '0',
				'date_created' => '2012-10-24 14:14:05',
			),
			array(
				'title' => '',
				'event_trigger' => 'new.web.agent.portal',
				'event_trigger_options' => 'a:0:{}',
				'is_enabled' => '0',
				'terms' => 'a:0:{}',
				'terms_any' => 'a:0:{}',
				'actions' => 'a:1:{i:0;a:2:{s:4:"type";s:21:"set_initial_from_name";s:7:"options";a:2:{s:9:"from_name";s:18:"{{performer.name}}";s:7:"to_whom";s:1:"0";}}}',
				'sys_name' => 'setfrom.newweb_agent',
				'run_order' => '0',
				'date_created' => '2012-10-24 14:14:05',
			),
		);

		$have_sys_triggers = $this->container->getDb()->fetchAllCol("
			SELECT sys_name
			FROM ticket_triggers
			WHERE sys_name IS NOT NULL
		");

		$have_sys_triggers = array_flip($have_sys_triggers);

		foreach ($triggers as $trigger) {
			if (isset($have_sys_triggers[$trigger['sys_name']])) {
				$this->out("Already have {$trigger['sys_name']}, skipping");
				continue;
			}

			$this->out("Inserting {$trigger['sys_name']}");
			$this->container->getDb()->insert('ticket_triggers', $trigger);
		}
	}
}