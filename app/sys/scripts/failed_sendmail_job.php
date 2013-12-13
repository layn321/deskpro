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

namespace DeskPRO\Kernel;

if (!defined('DP_ROOT')) exit('No access');

use Application\DeskPRO\Entity\SendmailQueue;
use Orb\Util\Arrays;
use Orb\Util\Strings;
use Orb\Util\Util;
use Orb\Util\Web;

require_once DP_ROOT.'/sys/serve_abstract.php';

/**
 * Take a request that saves a failed email.
 *
 * The mailfile payload format is like: <headers>\n\n<data>\n\n<email>
 * We save the payload as-is, but parse out the 'data' (json encoded array) just
 * so we can save the proper subject/address data on the SendmailQueue record.
 */
class FailedSendmailJob extends LoaderAbstract
{
	public function runAction()
	{
		$auth = defined('DPC_SAVE_FAILED_MAIL_AUTH') ? DPC_SAVE_FAILED_MAIL_AUTH : null;
		if (!$auth || !isset($_GET[$auth])) {
			echo 'DP_FAIL_AUTH';
			exit;
		}

		if (!isset($_FILES['mailfile']) || !empty($_FILES['mailfile']['error']) || empty($_FILES['mailfile']['tmp_name'])) {
			echo "DP_MAILFILE_INVALID";
			exit(1);
		}

		#------------------------------
		# Parse out the headers/data
		# from the payload so we can fetch the subject/addresses bit
		#------------------------------

		$data = '';
		$mode = 0; // 0 = headers, 1 = data
		$fp = fopen($_FILES['mailfile']['tmp_name'], 'r');

		while (!feof($fp)) {
			$l = fgets($fp);
			if ($l == "\n") {
				$mode++;
			} elseif ($mode == 1) {
				$data .= $l;
			}

			if ($mode > 1) {
				break;
			}
		}
		fclose($fp);

		// $data may include a header of <DP_SMTP_DEBUG>...</DP_SMTP_DEBUG>
		$debug_data = '';
		$m = null;

		if (preg_match('#\s*<DP_SMTP_DEBUG>(.*?)</DP_SMTP_DEBUG>\s*#s', $data, $m)) {
			$debug_data = trim($m[1]);
			$data = substr($data, strlen($m[0]));
		}

		$data = @json_decode($data, true);
		if (!$data) {
			echo 'DP_BAD_MAILFILE';
			exit;
		}

		#------------------------------
		# Save it
		#------------------------------

		$container = $this->bootFullSystem();
		$blob = $container->getBlobStorage()->createBlobRecordFromFile($_FILES['mailfile']['tmp_name'], 'sendmail.job', 'plain/text');

		$email = new SendmailQueue();
		$email->blob         = $blob;
		$email->subject      = $data['subject'];
		$email->to_address   = array_merge($data['to_addresses'], $data['cc_addresses'], $data['bcc_addresses']);
		$email->from_address = $data['from_addresses'];
		$email->attempts     = 4;

		if ($debug_data) {
			$email->appendLog($debug_data);
		}

		$container->getEm()->persist($email);
		$container->getEm()->flush($email);

		echo "DP_ACCEPT: {$email->id}";
	}
}

$x = new FailedSendmailJob();
$x->run();