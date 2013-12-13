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

class Build1339522924 extends AbstractBuild
{
	public function run()
	{
		$this->out("Inserting new system archive filters");
		$this->execMutateSql("
			REPLACE INTO `ticket_filters` (`id`, `person_id`, `agent_team_id`, `is_global`, `title`, `is_enabled`, `sys_name`, `terms`, `group_by`, `order_by`)
			VALUES
				(NULL, NULL, NULL, 1, 'Awaiting User', 1, 'archive_awaiting_user', X'613A313A7B693A303B613A333A7B733A343A2274797065223B733A363A22737461747573223B733A323A226F70223B733A323A226973223B733A373A226F7074696F6E73223B613A313A7B733A363A22737461747573223B733A31333A226177616974696E675F75736572223B7D7D7D', '', 'ticket.date_created:desc'),
				(NULL, NULL, NULL, 1, 'Resolved', 1, 'archive_resolved', X'613A313A7B693A303B613A333A7B733A343A2274797065223B733A363A22737461747573223B733A323A226F70223B733A323A226973223B733A373A226F7074696F6E73223B613A313A7B733A363A22737461747573223B733A383A227265736F6C766564223B7D7D7D', '', 'ticket.date_created:desc'),
				(NULL, NULL, NULL, 1, 'Resolved', 1, 'archive_closed', X'613A313A7B693A303B613A333A7B733A343A2274797065223B733A363A22737461747573223B733A323A226F70223B733A323A226973223B733A373A226F7074696F6E73223B613A313A7B733A363A22737461747573223B733A363A22636C6F736564223B7D7D7D', '', 'ticket.date_created:desc'),
				(NULL, NULL, NULL, 1, 'Awaiting Validation', 1, 'archive_validating', X'613A313A7B693A303B613A333A7B733A343A2274797065223B733A363A22737461747573223B733A323A226F70223B733A323A226973223B733A373A226F7074696F6E73223B613A313A7B733A363A22737461747573223B733A31373A2268696464656E2E76616C69646174696E67223B7D7D7D', '', 'ticket.date_created:desc'),
				(NULL, NULL, NULL, 1, 'Spam', 1, 'archive_spam', X'613A313A7B693A303B613A333A7B733A343A2274797065223B733A363A22737461747573223B733A323A226F70223B733A323A226973223B733A373A226F7074696F6E73223B613A313A7B733A363A22737461747573223B733A31313A2268696464656E2E7370616D223B7D7D7D', '', 'ticket.date_created:desc'),
				(NULL, NULL, NULL, 1, 'Deleted', 1, 'archive_deleted', X'613A313A7B693A303B613A333A7B733A343A2274797065223B733A363A22737461747573223B733A323A226F70223B733A323A226973223B733A373A226F7074696F6E73223B613A313A7B733A363A22737461747573223B733A31343A2268696464656E2E64656C65746564223B7D7D7D', '', 'ticket.date_created:desc');
		");
	}
}