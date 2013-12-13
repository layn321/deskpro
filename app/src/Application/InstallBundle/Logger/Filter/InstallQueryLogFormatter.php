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
 * @subpackage InstallBundle
 */

namespace Application\InstallBundle\Logger\Filter;

class InstallQueryLogFormatter extends \Orb\Filter\AbstractFilter
{
	public $max_time = 0.1;
	public $log_max_type = 100;
	public $done_log = array();

	public function filter($log_item)
	{
		// Dont care about this log message
		if (!isset($log_item['queryinfo']) || !isset($log_item['queryinfo']['tag'])) {
			return $log_item;
		}

		// Dont care if it ran quickly
		if ($log_item['queryinfo']['time_taken'] < $this->max_time) {
			return null;
		}

		// See if the SQL had a tag in it
		$query_tag = \Orb\Util\Strings::extractRegexMatch('#DP_QUERY:([a-zA-Z0-9]+)#', $log_item['queryinfo']['sql']);
		if (!$query_tag) {
			// Appears to be using placeholders, can use the sql as the tag
			if (strpos($log_item['queryinfo']['sql'], '?') !== false) {
				$query_tag = md5($log_item['queryinfo']['sql']);

			// Otherwise the tag is the type against the table
			} else {
				$query_tag = md5($log_item['queryinfo']['query_typename'] . $log_item['queryinfo']['table']);
			}
		}

		// Make sure we havent logged too many of them
		if (!isset($this->done_log[$query_tag])) {
			$this->done_log[$query_tag] = 0;
		}
		if ($this->done_log[$query_tag]++ > $this->log_max_type) {
			return null;
		}

		$params = array();
		if (is_array($log_item['queryinfo']['params'])) {
			foreach ($log_item['queryinfo']['params'] as $k => $v) {
				if (!is_scalar($v)) {
					if (is_object($v)) {
						$v = get_class($v);
					} else {
						$v = gettype($v);
					}
				} else {
					$len = strlen($v);
					$params[$k] = 'string(' . $len . ')';
				}
			}
		}

		$sql = $log_item['queryinfo']['sql'];
		$sql = substr($sql, 0, 1500);

		$log_item[\Orb\Log\LogItem::MESSAGE] = sprintf(
			"[%s %s took %.4fs] %s (%s)",
			$log_item['queryinfo']['tag'],
			$query_tag,
			$log_item['queryinfo']['time_taken'],
			$sql,
			\DeskPRO\Kernel\KernelErrorHandler::varToString($params)
		);

		$log_item[\Orb\Log\LogItem::PRIORITY] = \Orb\Log\Logger::DEBUG;
		$log_item[\Orb\Log\LogItem::PRIORITY_NAME] = 'DBEUG';

		return $log_item;
	}
}
