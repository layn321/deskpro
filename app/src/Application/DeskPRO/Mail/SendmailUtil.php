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

namespace Application\DeskPRO\Mail;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\SendmailQueue;
use Orb\Util\Strings;

class SendmailUtil
{
	public static function rewriteFromAddress(SendmailQueue $sendmail, $from_address)
	{
		$data = App::getContainer()->getBlobStorage()->copyBlobRecordToString($sendmail->blob);

		// A DeskPRO queue job
		// We have to rewrite it a bit and update the smtp data
		if ($sendmail->blob->filename == 'sendmail.job') {

			$mode = 0;
			$pre = array();
			$json = array();
			$email = array();

			$old_from = $sendmail->from_address;
			$new_from = $from_address;

			$data = explode("\n", $data);
			while (($l = array_shift($data)) !== null) {
				if ($mode == 0) {
					if ($l == "") {
						$mode++;
					} else {
						$pre[] = $l;
					}
				} elseif ($mode == 1) {
					if ($l == "") {
						$mode++;
					} else {
						$json[] = $l;
					}
				} else {
					if ($l == "") {
						$mode++;
					}

					if ($mode < 3) {
						$l = str_replace($old_from, $new_from, $l);
						if (preg_match('#^From: #', $l)) {
							$l = "From: " . $new_from;
						} elseif (preg_match('#^Reply-To: #', $l)) {
							$l = "Reply-To: " . $new_from;
						}
					}

					$email[] = $l;
				}
			}

			$json = json_decode(implode("\n", $json), true);
			$json['from_addresses'] = array($new_from);

			$use_tr = App::getEntityRepository('DeskPRO:EmailTransport')->findTransportForAddress($new_from);
			if (!$use_tr) {
				$use_tr = App::getEntityRepository('DeskPRO:EmailTransport')->getDefaultTransport();
			}
			unset($json['smtp_options']);

			if ($use_tr && $smtp_options = $use_tr->getSmtpOptions()) {
				$json['smtp_options'] = $smtp_options;
			}

			$data = implode("\n", $pre) . "\n\n" . json_encode($json) . "\n\n" . implode("\n", $email);

		// A serialised message
		// We can just change the from in the object. The SMTP info is selected at the time its sent
		} else {

			$message = @unserialize($data);

			// A bug could result in the message being double encoded
			if ($message && !is_object($message)) {
				$message = @unserialize($message);
			}

			$message->setFrom($from_address);
			$data = serialize($message);
		}

		// Delete old blob
		$old_filename = $sendmail->blob->filename;
		App::getContainer()->getBlobStorage()->deleteBlobRecord($sendmail->blob);

		// Create new blob
		$blob = App::getContainer()->getBlobStorage()->createBlobRecordFromString($data, $old_filename, 'plain/text');
		$sendmail->blob = $blob;

		$sendmail->setFromAddress($from_address);

		App::getOrm()->persist($blob);
		App::getOrm()->persist($sendmail);
		App::getOrm()->flush();
	}
}