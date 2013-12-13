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
 * @category Controller
 */

namespace Application\DeskPRO\ResourceScanner;

use Application\DeskPRO\App;
use Orb\Util\Arrays;

/**
 * Defines which settings are to be displayed in the 'advanced' page
 */
class AdvancedSettings extends SettingFiles
{
	public function getAllSettings()
	{
		$settings = parent::getAllSettings();

		$accept_settings = array(
			'agent.max_login_attempts',
			'agent.login_lockout_time',
			'agent.notify_self_login',
			'agent.notify_login_emaillist',
			'agent_notify_list_login',
			'agent_notify_list_failed_login',
			'agent_notify_list_adminlogin',
			'agent_notify_list_failed_adminlogin',
			'core_chat.assign_ack_timeout',
			'core_chat.agent_timeout',
			'core_chat.user_timeout',
			'core_chat.require_department',
			'core.bcc_all_emails',
			'core.drafts_lifetime',
			'core.store_sent_mail_days',
			'core.site_id',
			'core.sessions_lifetime',
			'core.email_source_storetime',
			'core_email.failed_email_attempts_notify',
			'core_email.antiflood_newtickets',
			'core_email.antiflood_newtickets_warn',
			'core_email.antiflood_newreplies',
			'core_email.antiflood_newreplies_warn',
			'core_misc.cleanup_visitors',
			'core_misc.cleanup_login_logs',
			'core_misc.cleanup_gateway_sources',
			'core_misc.cleanup_gateway_sources_onlyclosed',
			'core_misc.cleanup_task_logs',
			'core.allow_arbitrary_gateway_address',
			'core_tickets.gateway_agent_require_marker',
			'core_tickets.gateway_enable_subject_match',
			'core.agent_translate_debug',
			'core_email.enable_date_limit_rejection',
		);

		if (!defined('DPC_IS_CLOUD')) {
			$accept_settings[] = 'core.api_rate_limit';
		}

		$ret = array();

		foreach ($accept_settings as $k) {
			if (isset($settings[$k])) {
				$ret[$k] = $settings[$k];
			}
		}

		return $ret;
	}
}
