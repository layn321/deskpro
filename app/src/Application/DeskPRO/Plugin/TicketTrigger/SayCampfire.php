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

namespace Application\DeskPRO\Plugin\TicketTrigger;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Strings;
use Orb\Util\Arrays;

class SayCampfire
{
	public static function sayEvent(array $info)
	{
		$ticket = $info['ticket'];
		$logs = $info['logs'];

		$router = App::getRouter();
		$deskpro_url = App::getSetting('core.deskpro_url');
		$ticket_url = "{$deskpro_url}agent/#ticket-{$ticket['id']}";

		if (isset($logs['ticket_created'])) {
			$say = "New ticket created: {$ticket['subject']} $ticket_url";
		} elseif (isset($logs['message_created'])) {
			$message = $logs['message_created']->getMessage();
			$say = "{$message['person']['display_name']} replied to ticket {$ticket['subject']} $ticket_url";
		} else {
			return; // dont know how to handle the event then
		}

		$resource = "https://{$info['campfire_account_name']}.campfirenow.com/room/{$info['campfire_room_id']}/speak.json";
		$payload = json_encode(array('message' => array('type' => 'TextMessage', 'body' => $say)));

		$ch = curl_init($resource);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_USERPWD, $info['campfire_api_token'].':x');
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'DeskPRO Campfire Announce');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		$contents = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
	}
}
