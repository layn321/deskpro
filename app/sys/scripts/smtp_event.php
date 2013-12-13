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

require_once DP_ROOT.'/sys/serve_abstract.php';

/**
 * Take a request that saves an SMTP event.
 * @see http://sendgrid.com/docs/API_Reference/Webhooks/event.html
 */
class SmtpEvent extends LoaderAbstract
{
	/**
	 * @var \PDOStatement
	 */
	protected $email_log_q;

	/**
	 * @var bool
	 */
	protected $debug = false;

	public function runAction()
	{
		if (isset($_GET['debug'])) {
			$this->debug = true;
		}

		$is_json = false;
		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) != 'HTTP_') {
				continue;
			}

			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			if ($header == 'Content-Type') {
				if ($value == 'application/json') {
					$is_json = true;
				}
				break;
			}
		}

		// Batched events
		if ($is_json) {
			$postlines = file("php://input");
			$this->log('Processing event batch (%d events)', count($postlines));

			foreach ($postlines as $post) {
				$post_array = json_decode($post, true);
				if (!$post) {
					$this->log('WARN', 'Failed to decode JSON: %s', $post);
					continue;
				}
				$this->processEvent($post_array);
			}

		// Single event
		} else {
			$this->log('Processing single event');
			$this->processEvent($_POST);
		}
	}


	/**
	 * @param array $post
	 * @return bool
	 */
	public function processEvent(array $post)
	{
		if (!isset($post['dp_code']) || !is_string($post['dp_code'])) {
			$this->log('WARN', 'Missing dp_code');
			return false;
		}
		if (!isset($post['email']) || !is_string($post['email'])) {
			$this->log('WARN', 'Missing email');
			return false;
		}
		if (!isset($post['event']) || !is_string($post['event'])) {
			$this->log('WARN', 'Missing event');
			return false;
		}

		$log = $this->getSendmailLog($post['dp_code'], $post['email']);

		if (!$log) {
			$this->log('WARN', 'Could not find log: %s/%s', $post['dp_code'], $post['email']);
			return false;
		}

		$this->log('Got log ID: %s (for %s/%s)', $log['id'], $post['dp_code'], $post['email']);

		$now = date('Y-m-d H:i:s');
		$update = array();

		$this->log('Event: %s', $post['event']);

		switch ($post['event']) {
			case 'processed':
				$update['date_process'] = $now;
				break;

			case 'deferred':
				$update['date_defer'] = $now;
				$update['reason_defer'] = $this->appendReasonLog($log, 'reason_defer', "(#{$post['attempt']}) {$post['response']}");
				break;

			case 'delivered':
				$update['date_deliver'] = $now;
				$update['reason_deliver'] = $this->appendReasonLog($log, 'reason_deliver', $post['response']);
				break;

			case 'open':
				if (!$log['date_open']) {
					$update['date_open'] = $now;
				}
				$update['count_open'] = $log['count_open'] + 1;
				break;

			case 'click':
				if (!$log['date_click']) {
					$update['date_click'] = $now;
				}
				if (!$log['date_open']) {
					$update['date_open'] = $now;
					$update['count_open'] = 1;
				}

				$update['count_click'] = $log['count_click'] + 1;
				$update['clicked_urls'] = $this->appendReasonLog($log, 'clicked_urls', $post['url']);
				break;

			case 'bounce':
				$update['date_bounce']   = $now;
				$update['bounce_code']   = $post['status'];
				$update['bounce_type']   = $post['type'] . '/' . $post['status'];
				$update['reason_bounce'] = $post['reason'];
				break;

			case 'dropped':
				$update['date_drop'] = $now;
				$update['reason_drop'] = $post['reason'];
				break;

			case 'spamreport':
				$update['date_spam'] = $now;
				break;
		}

		if ($update) {
			$update_p = array();
			$update_v = array();
			foreach ($update as $k => $v) {
				$update_p[] = "$k = ?";
				$update_v[] = $v;
			}
			$update_v[] = $log['id'];

			$this->getPdo()->prepare("
				UPDATE sendmail_logs
				SET " . implode(', ', $update_p) . "
				WHERE id = ?
			")->execute($update_v);
		}

		return true;
	}


	/**
	 * @param array $log
	 * @param $key
	 * @param $log_string
	 * @return string
	 */
	protected function appendReasonLog(array $log, $key, $log_string)
	{
		$str = '';
		if ($log[$key]) {
			$str = $log[$key] . "\n";
		}

		$now = date('Y-m-d H:i:s');
		$str .= "[$now] " . $log_string;

		return $str;
	}


	/**
	 * @param string $code
	 * @param string $email
	 * @return array
	 */
	public function getSendmailLog($code, $email)
	{
		if (!$this->email_log_q) {
			$this->email_log_q = $this->getPdo()->prepare("
				SELECT * FROM sendmail_logs
				WHERE code = ? AND to_address = ?
				LIMIT 1
			");
		}

		$this->email_log_q->execute(array($code, $email));
		$log = $this->email_log_q->fetch(\PDO::FETCH_ASSOC);
		$this->email_log_q->closeCursor();

		return $log;
	}

	/**
	 * Log a debug message
	 */
	public function log()
	{
		$args = func_get_args();

		if (!$args) {
			return;
		}

		$level = 'DEBUG';

		$message = array_shift($args);
		if ($message == 'DEBUG' || $message == 'WARN') {
			$level = $message;
			$message = array_shift($args);
		}

		if ($args) {
			$message = vsprintf($message, $args);
		} else {
			$message = $message;
		}

		$now = date('Y-m-d H:i:s');
		$message = "[$now] $level -- $message";

		if ($this->debug) {
			echo $message;
			echo "\n";
		}
	}
}

$x = new SmtpEvent();
$x->run();