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
 * @subpackage HttpKernel
 */

namespace Application\DeskPRO\HttpKernel;

use Application\DeskPRO\App;

use Orb\Log\Logger;
use Orb\Util\Strings;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use DeskPRO\Kernel\KernelErrorHandler;

class ExceptionListener
{
	protected $last_exception = null;
	private $handling_exception = false;

	public function getLastException()
	{
		return $this->last_exception;
	}

	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		if ($this->handling_exception === true) return;
		$this->handling_exception = true;

		$exception = $event->getException();
		$this->_logException($exception);

		// This is fetched from the template
		$this->last_exception = $exception;

		$this->handling_exception = false;
	}

	protected function _logException(\Exception $exception)
	{
		if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
			try {
				$req = App::getRequest();
				if ($req && $req->isXmlHttpRequest()) {
					$this->_log404($exception);
				}
			} catch (\Exception $e) {}

			return;
		}

		if ($exception instanceof \Application\DeskPRO\HttpKernel\Exception\NoPermissionException) {
			return;
		}

		if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
			return;
		}

		$exception->_dp_sn = KernelErrorHandler::genSessionName();

		$errinfo = KernelErrorHandler::getExceptionInfo($exception);
		KernelErrorHandler::logErrorInfo($errinfo);
	}

	public function _log404(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception)
	{
		return;
		$summary = $exception->getMessage();

		$trace = KernelErrorHandler::formatBacktrace($exception->getTrace());
		$trace = KernelErrorHandler::stripPathPrefix($trace);

		$exception->_dp_sn = KernelErrorHandler::genSessionName();

		try {
			$logger = App::createNewLogger('error_not_found', null);
			$logger->log($summary, 3, array(
				'session_name' => $exception->_dp_sn,
				'trace' => $trace,
				'class' => get_class($exception),
				'file' => $exception->getFile(),
				'line' => $exception->getLine()
			));
		} catch (\Exception $e) {}

		$errinfo = KernelErrorHandler::getExceptionInfo($exception);
		KernelErrorHandler::logErrorInfo($errinfo);
	}
}
