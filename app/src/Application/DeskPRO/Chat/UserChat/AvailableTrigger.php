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

namespace Application\DeskPRO\Chat\UserChat;

use Application\DeskPRO\App;
use DeskPRO\Kernel\KernelErrorHandler;

class AvailableTrigger
{
	/**
	 * Update the chat status
	 *
	 * @param bool|null $is_chat_available True/false to mark chat as available/unavailable, null to auto-detect with query
	 */
	public static function update($is_chat_available = null)
	{
		if ($is_chat_available === null) {
			$is_chat_available = false;

			if (!App::getSetting('core.apps_chat')) {
				$agent_ids = array();
			} else {
				$agent_ids = App::getDb()->fetchAllCol("
					SELECT person_id
					FROM sessions
					WHERE date_last >= ? AND active_status = 'available' AND is_person = 1 AND is_chat_available = 1 AND interface = 'agent'
				", array(date('Y-m-d H:i:s', time() - App::getSetting('core_chat.agent_timeout'))));
				$agent_ids = array_unique($agent_ids);
			}

			if ($agent_ids) {
				$agent_ids = array_filter($agent_ids, function($agent_id) {
					$agent = App::getContainer()->getAgentData()->get($agent_id);
					if ($agent && $agent->hasPerm('agent_chat.use')) {
						return true;
					}
					return false;
				});
			}

			if ($agent_ids) {
				// At least one department needs to be allowed for the online agents
				$dep_check = App::getDb()->fetchColumn("
					SELECT department_id
					FROM department_permissions
					WHERE person_id IN (" . implode(',', $agent_ids) . ") AND app = 'chat' AND value = '1'
					LIMIT 1
				");

				if ($dep_check) {
					$is_chat_available = true;
				}
			}
		}

		$trigger_File = dp_get_data_dir() . '/chat_is_available.trigger';
		if ($is_chat_available) {
			file_put_contents($trigger_File, time());
			@chmod($trigger_File, 0777);
		} elseif (is_file($trigger_File)) {
			unlink($trigger_File);
		}

		if ($update_urls = dp_get_config('chat_status_update_urls')) {
			$val = $is_chat_available ? '1' : '0';

			foreach ($update_urls as $url) {

				$url = str_replace('%CHAT_STATUS%', $val, $url);

				$context = stream_context_create(array(
					'http' => array(
						'timeout' => 5
					)
				));
				$res = file_get_contents($url, false, $context);

				if ($is_chat_available && strpos($res, 'DP_CHATSTATUS_WROTE_AVAILABLE') === false) {
					$e = new \RuntimeException("Failed to send chat status (1) to $url. Got response: $res");
					KernelErrorHandler::logException($e, false);
				} elseif (!$is_chat_available && strpos($res, 'DP_CHATSTATUS_WROTE_UNAVAILABLE') === false) {
					$e = new \RuntimeException("Failed to send chat status (0) to $url. Got response: $res");
					KernelErrorHandler::logException($e, false);
				}
			}
		}
	}
}