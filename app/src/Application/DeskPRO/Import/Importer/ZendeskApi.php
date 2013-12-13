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
 * @subpackage Import
 */

namespace Application\DeskPRO\Import\Importer;

use Orb\Log\Logger;
use Orb\Service\Zendesk\ApiException;
use Orb\Service\Zendesk\Zendesk;
use Orb\Util\Arrays;

class ZendeskApi extends Zendesk
{
	/**
	 * @var \Application\DeskPRO\Import\Importer\ZendeskImporter
	 */
	public $importer;

	/**
	 * How many times to try an API call before re-throwing an error?
	 * @var int
	 */
	public $try_count = 6;

	/**
	 * The number of seconds between try attempts
	 * when the attempts are errors;
	 *
	 * @var int
	 */
	public $try_time_error  = 6;

	/**
	 * The number of seconds between try attempts
	 * when the attempts are rate limit errors.
	 *
	 * @var int
	 */
	public $try_time_ratelimit  = 15;

	/**
	 * The number of seconds between try attempts increases
	 * by this number every time. So try #2 is $try_time_ratelimit,
	 * try #3 is $try_time_ratelimit+$try_time_inc, etc.
	 *
	 * @var int
	 */
	public $try_time_inc = 15;

	/**
	 * @var \Orb\Log\Logger
	 */
	protected $logger;


	/**
	 * @param \Orb\Log\Logger $logger
	 */
	public function setLogger(\Orb\Log\Logger $logger)
	{
		$this->logger = $logger;
	}


	/**
	 * Adds some handling of rate limiting
	 *
	 * @param string $id
	 * @param string $action
	 * @param array $call_data
	 * @param array $query_data
	 * @param bool $no_exec
	 * @return null|\Orb\Service\Zendesk\ApiResponse
	 * @throws \Exception|null|\Orb\Service\Zendesk\ApiException
	 * @throws \Orb\Service\Zendesk\ApiException
	 */
	public function sendRequest($id, $action, array $call_data = null, array $query_data = null, $no_exec = false)
	{
		if ($no_exec) {
			return parent::sendRequest($id, $action, $call_data, $query_data, $no_exec);
		}

		$try = $this->try_count;
		$x = 0;
		while ($try-- > 0) {
			$x++;
			$ex  = null;
			$err = null;
			$res = null;

			if ($x > 1) {
				$this->setTimeout(45);
			}

			try {
				$res = parent::sendRequest($id, $action, $call_data, $query_data);
			} catch (\Exception $e) {
				$ex = $e;
				$err = 'exception';
			}

			$this->setTimeout(10);

			if (!$err && $res && $res->isError()) {
				$err = 'exception';
				if ($res->getHttpStatusCode() == '429') {
					$err = 'rate';
				}
			}

			if ($ex && $ex instanceof ApiException && $ex->api_error_code == '429') {
				$err = 'rate';
			}

			// Success, return
			if (!$err) {
				return $res;
			} else {
				// No more tries, rethrow any errors
				// or return the error result from ZD
				if (!$try) {
					if ($ex) {
						throw $ex;
					} else {
						return $res;
					}

				// Try again after a sleep
				} else {
					if ($err == 'exception') {
						if ($this->logger) {
							$this->logger->logDebug(sprintf("[ZD API] Call to $id failed due to an exception: %s %s", $ex->getCode(), $ex->getMessage()));
						}
						sleep($this->try_time_error);
					} elseif ($err == 'rate') {
						if ($this->logger) {
							$body = '';
							if ($res) {
								$body = $res->getRaw();
							}
							$this->logger->logDebug(sprintf("[ZD API] Call to $id failed due to rate limiting: %s", $body));
						}
						sleep(min(60, $this->try_time_ratelimit + (($x-1) * $this->try_time_inc)));
					} else {
						if ($this->logger) {
							$body = '';
							if ($res) {
								$body = $res->getRaw();
							}
							$this->logger->logDebug(sprintf("[ZD API] Call to $id failed with an error status: %s", $body));
						}

						if ($res) {
							throw new ApiException("API call failed with error status", $res->getHttpStatusCode(), $res->getErrorCode(), $res->getRaw());
						} else {
							throw new ApiException("API call failed: {$ex->getCode()} {$ex->getMessage()}", 0, 0, '', $ex);
						}
					}
				}
			}
		}

		return null;
	}


	/**
	 * @param array $requests
	 * @return array|void
	 */
	public function sendGetMulti(array $requests)
	{
		$results = array();

		$batch_requests = array_chunk($requests, 200, true);

		foreach ($batch_requests as $batch) {
			$do_requests = $batch;

			$try = $this->try_count;
			$x = 0;
			while ($do_requests && $try-- > 0) {
				$x++;
				$results = Arrays::mergeAssoc($results, parent::sendGetMulti($do_requests));
				$do_requests = array();

				foreach ($results as $id => $info) {
					if ($info['exception'] || !$info['response']) {
						$do_requests[$id] = $requests[$id];
					}
				}

				if (!$do_requests) {
					break;
				}

				$modifier = count($batch);
				if ($modifier > 115) {
					$modifier = 60;
				} else if ($modifier > 60) {
					$modifier = 30;
				}

				sleep(min(60, $this->try_time_ratelimit + (($x-1) * $this->try_time_inc) + $modifier));
			}
		}

		return $results;
	}


	/**
	 * @param int $page
	 * @param bool $reload
	 * @return mixed|null|\Orb\Service\Zendesk\ApiResponse
	 */
	public function getTicketListPageResponse($page = 1, $reload = false)
	{
		$per_page = 100;

		if (!$reload) {
			$cached = $this->importer->db->fetchColumn("
				SELECT data
				FROM import_datastore
				WHERE typename = 'zd_tickets_cache.p{$page}'
			");
		} else {
			$cached = false;
		}

		$res = null;
		if ($cached) {
			$res = @unserialize($cached);
		}

		if (!$res) {
			$res = $this->importer->zd->sendGet('tickets', array('per_page' => $per_page, 'page' => $page));

			$this->importer->db->replace('import_datastore', array(
				'typename' => "zd_tickets_cache.p{$page}",
				'data'     => serialize($res)
			));
		}

		return $res;
	}


	/**
	 * Caches many ticket audits
	 *
	 * @param array $ticket_ids
	 */
	public function cacheManyTicketAudits($ticket_ids)
	{
		// Big ticket ids need multiple calls to get all audits (zd only sends in batches of 100)
		// array(ticket_id => count)
		$big_ticket_ids = array();

		// All audit data keyed by ticket id
		// Used with big tickets to merge to get a big resulting array
		$big_audits = array();

		#------------------------------
		# Get results for each ticket
		#------------------------------

		$reqs = array();

		foreach ($ticket_ids as $ticket_id) {
			$reqs[$ticket_id] = array(
				"tickets/$ticket_id/audits",
				array('per_page' => 100)
			);
		}

		$results = $this->sendGetMulti($reqs);

		foreach ($results as $ticket_id => $info) {
			if ($info['exception'] || !$info['response']) {
				// failed
			} else {
				/** @var $r \Orb\Service\Zendesk\ApiResponse */
				$r = $info['response'];

				if ($r->get('next_page') && $r->get('count')) {
					// Big ticket with more than one audit
					$big_audits[$ticket_id] = $r->get('audits', array());
					$big_ticket_ids[$ticket_id] = $r->get('count');
				} else {
					$this->importer->db->replace('import_datastore', array(
						'typename' => 'zd_tickets_audits_cache.t'.$ticket_id,
						'data' => serialize($r->get('audits', array()))
					));
				}
			}
		}

		#------------------------------
		# Get additional pages for larger tickets
		#------------------------------

		$reqs = array();

		foreach ($big_ticket_ids as $ticket_id => $count) {
			$num_pages = ceil($count / 100);
			$pages = range(1, $num_pages);

			foreach ($pages as $p) {
				$reqs[$ticket_id. '-' . $p] = array(
					"tickets/$ticket_id/audits",
					array('per_page' => 100, 'page' => $p)
				);
			}
		}

		if ($reqs) {
			$results = $this->sendGetMulti($reqs);

			foreach ($results as $key => $info) {
				if ($info['exception'] || !$info['response']) {
					// failed
				} else {
					/** @var $r \Orb\Service\Zendesk\ApiResponse */
					$r = $info['response'];

					list($ticket_id,) = explode('-', $key);

					$big_audits[$ticket_id] = array_merge($big_audits[$ticket_id], $r->get('audits'));
				}
			}
		}

		if ($big_audits) {
			foreach ($big_audits as $ticket_id => $audits) {
				$this->importer->db->replace('import_datastore', array(
					'typename' => 'zd_tickets_audits_cache.t'.$ticket_id,
					'data' => serialize($audits)
				));
			}
		}
	}


	/**
	 * @param int $ticket_id
	 * @param bool $reload
	 * @return array
	 */
	public function getTicketAudits($ticket_id, $reload = false)
	{
		$per_page = 100;

		if (!$reload) {
			if (isset($GLOBALS['DP_IMPORT_DATASTORE_CACHE']["zd_tickets_audits_cache.t{$ticket_id}"])) {
				$cached = $GLOBALS['DP_IMPORT_DATASTORE_CACHE']["zd_tickets_audits_cache.t{$ticket_id}"];
			} else {
				$cached = $this->importer->db->fetchColumn("
					SELECT data
					FROM import_datastore
					WHERE typename = 'zd_tickets_audits_cache.t{$ticket_id}'
				");
			}
		} else {
			$cached = false;
		}

		$res = null;
		if ($cached) {
			$res = @unserialize($cached);
		}

		if (!$res) {
			$res = $this->importer->zd->sendGet("tickets/$ticket_id/audits", array('per_page' => $per_page));

			$audits = $res->get('audits', array());

			// Many pages of audits
			if ($res->get('next') && $res->get('count')) {
				$num_pages = ceil($res->get('count') / 100);
				$pages = range(1, $num_pages);

				$reqs = array();
				foreach ($pages as $p) {
					$reqs[$p] = array(
						"tickets/$ticket_id/audits",
						array('per_page' => 100, 'page' => $p)
					);
				}

				$results = $this->sendGetMulti($reqs);

				foreach ($results as $info) {
					if ($info['exception'] || !$info['response']) {
						// failed
					} else {
						/** @var $r \Orb\Service\Zendesk\ApiResponse */
						$r = $info['response'];
						$audits = array_merge($audits, $r->get('audits', array()));
					}
				}
			}

			$this->importer->db->replace('import_datastore', array(
				'typename' => "zd_tickets_audits_cache.t{$ticket_id}",
				'data'     => serialize($audits)
			));
		}

		return $res;
	}
}