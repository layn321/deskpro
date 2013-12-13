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

namespace Application\DeskPRO;

use Application\DeskPRO\App;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Log\Logger;

class ErrorHandler
{
	public function registerHandler()
	{
		set_error_handler(array($this, 'handleError'), E_ALL | E_STRICT);
	}

	public function handleError($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno)) return;

		$die = false;

		switch ($errno) {
			case E_ERROR:
				$die = true;
				$pri = Logger::ERR;
				$errname = "E_ERROR";
				break;

			case E_WARNING:
			case E_USER_WARNING:
				$pri = Logger::WARN;
				$errname = "E_WARNING";
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
				$pri = Logger::NOTICE;
				$errname = "E_NOTICE";
				break;

			case E_STRICT:
				$pri = Logger::NOTICE;
				$errname = "E_STRICT";
				break;

			case E_RECOVERABLE_ERROR:
				$pri = Logger::ERR;
				$errname = "E_RECOVERABLE_ERROR";
				break;

			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$pri = Logger::NOTICE;
				$errname = "E_DEPRECATED";
				break;
		}

		$errfile = $this->stripPathPrefix($errfile);

		ob_start();
		debug_print_backtrace();
		$trace = ob_get_clean();

		$trace = $this->stripPathPrefix($trace);

		$summary = "[$errname:$errno] $errstr ($errfile:$errline)";

		try {
			$logger = App::createNewLogger('error_log', null);
			$logger->log($summary, $pri, array('trace' => $trace));
		} catch (\Exception $e) {}

		if (in_array(ini_get('display_errors'), array(1, '1', 'on', 'On', true))) {
			echo $summary;
			echo "\n";
			echo $trace;
		}

		if ($die) {
			exit;
		}

		return true;
	}

	public function stripPathPrefix($content)
	{
		$prefix = DP_ROOT . '/';

		$content = str_replace($prefix, '', $content);

		return $content;
	}
}
