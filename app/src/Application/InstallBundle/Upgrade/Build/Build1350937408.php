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

class Build1350937408 extends AbstractBuild
{
	public function run()
	{
		$this->out("Create default triggers for gateway to department mapping");
		$this->execMutateSql("
			INSERT INTO `ticket_triggers` (`id`, `title`, `event_trigger`, `is_enabled`, `terms`, `actions`, `sys_name`, `run_order`, `date_created`, `event_trigger_options`, `terms_any`)
			VALUES
				(NULL, '', 'new.email.user', 1, X'613A303A7B7D', X'613A313A7B693A303B613A323A7B733A343A2274797065223B733A31303A226465706172746D656E74223B733A373A226F7074696F6E73223B613A313A7B733A31303A226465706172746D656E74223B733A31333A22656D61696C5F6163636F756E74223B7D7D7D', NULL, -10, '2012-10-22 19:56:52', X'613A303A7B7D', X'613A303A7B7D'),
				(NULL, '', 'new.web.user', 1, X'613A303A7B7D', X'613A313A7B693A303B613A323A7B733A343A2274797065223B733A31393A227365745F676174657761795F61646472657373223B733A373A226F7074696F6E73223B613A313A7B733A31383A22676174657761795F616464726573735F6964223B733A31303A226465706172746D656E74223B7D7D7D', NULL, -10, '2012-10-22 19:57:20', X'613A303A7B7D', X'613A303A7B7D'),
				(NULL, '', 'new.email.agent', 1, X'613A303A7B7D', X'613A313A7B693A303B613A323A7B733A343A2274797065223B733A31303A226465706172746D656E74223B733A373A226F7074696F6E73223B613A313A7B733A31303A226465706172746D656E74223B733A31333A22656D61696C5F6163636F756E74223B7D7D7D', NULL, -10, '2012-10-22 19:57:36', X'613A303A7B7D', X'613A303A7B7D'),
				(NULL, '', 'new.web.agent.portal', 1, X'613A303A7B7D', X'613A313A7B693A303B613A323A7B733A343A2274797065223B733A31393A227365745F676174657761795F61646472657373223B733A373A226F7074696F6E73223B613A313A7B733A31383A22676174657761795F616464726573735F6964223B733A31303A226465706172746D656E74223B7D7D7D', NULL, -10, '2012-10-22 19:59:50', X'613A303A7B7D', X'613A303A7B7D'),
				(NULL, '', 'update.agent', 1, X'613A303A7B7D', X'613A313A7B693A303B613A323A7B733A343A2274797065223B733A31393A227365745F676174657761795F61646472657373223B733A373A226F7074696F6E73223B613A313A7B733A31383A22676174657761795F616464726573735F6964223B733A31303A226465706172746D656E74223B7D7D7D', NULL, -10, '2012-10-22 20:00:25', X'613A303A7B7D', X'613A303A7B7D'),
				(NULL, '', 'update.user', 1, X'613A303A7B7D', X'613A313A7B693A303B613A323A7B733A343A2274797065223B733A31393A227365745F676174657761795F61646472657373223B733A373A226F7074696F6E73223B613A313A7B733A31383A22676174657761795F616464726573735F6964223B733A31303A226465706172746D656E74223B7D7D7D', NULL, -10, '2012-10-22 20:03:33', X'613A303A7B7D', X'613A303A7B7D')
		");
	}
}