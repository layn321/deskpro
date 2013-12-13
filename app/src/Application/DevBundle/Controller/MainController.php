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

namespace Application\DevBundle\Controller;

use Application\DeskPRO\App;

class MainController extends \Application\DeskPRO\HttpKernel\Controller\Controller
{
	public function indexAction()
	{
		return $this->render('DevBundle:Main:index.html.php', array(

		));
	}

	public function seeFileAction()
	{
		$file = $_GET['file'];
		$file = \preg_replace('#^' . preg_quote(DP_ROOT, '#') . '/#', '', $file);
		$file = DP_ROOT . '/' . $file;

		if (!file_exists($file)) {
			return $this->createResponse('No such file', 404);
		}

		ob_start();
		highlight_file($file);
		$file = ob_get_clean();

		return $this->createResponse($file);
	}

	public function phpInfoAction()
	{
		ob_start();
		phpinfo();
		$phpinfo = ob_get_clean();

		return $this->createResponse($phpinfo);
	}

	public function phpTestAction()
	{
		return $this->render('DevBundle:Main:php-test.html.php', array(

		));
	}

	public function phpTestRunAction()
	{
		if (!empty($_POST['code'])) {
			$php = $_POST['code'];
			$php = preg_replace('#^\s*<\?(php)?#', '', $php);

			eval($php);
		}

		return $this->createResponse('');
	}

	public function runWorkerJobAction()
	{
		$worker_job = App::getEntityRepository('DeskPRO:WorkerJob')->find(@$_GET['id']);

		$runner = new \Application\DeskPRO\WorkerProcess\Runner\Standard();

		$fp = fopen('php://memory', 'r+');
		$runner->setCustomLoggerInit(function ($logger) use ($fp) {
			$out_writer = new \Orb\Log\Writer\Stream($fp);
			$logger->addWriter($out_writer);
		});

		$runner->runJobs(array($worker_job));

		rewind($fp);
		$log_result = stream_get_contents($fp);

		return $this->createResponse('<pre>' . htmlspecialchars($log_result) . '</pre>');
	}
}
