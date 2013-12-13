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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Service\ErrorReporter;
use Orb\Util\Numbers;
use Orb\Util\Strings;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server info
 */
class ServerController extends AbstractController
{
	############################################################################
	# phpinfo
	############################################################################

	public function phpinfoAction()
	{
		$vars = $this->_getPhpinfoVars();
		return $this->render('AdminBundle:Server:phpinfo.html.twig', $vars);
	}

	public function phpinfoDownloadAction()
	{
		$vars = $this->_getPhpinfoVars(true);
		$sections = array();

		$items = array_merge($vars['binary_paths'], $vars['web_php']['php_config']);
		$items['ini_path'] = $vars['web_php']['ini_path'];
		$items['effective_max_upload'] = $vars['web_php']['effective_max_upload'];
		$items['cli_ini_path'] = isset($vars['cli_php']['ini_path']) ? $vars['cli_php']['ini_path'] : '';
		if (isset($vars['cli_php']['php_config'])) {
			foreach ($vars['cli_php']['php_config'] as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $subk => $subv) {
						$items['cli_' . $k . '.' . $subk] = $subv;
					}
				} else {
					$items['cli_' . $k] = $v;
				}
			}
		}
		$items['has_apc'] = $vars['has_apc'] ? 'Yes' : 'No';
		$items['has_wincache'] = $vars['has_wincache'] ? 'Yes' : 'No';
		$items = array_merge($items, $vars['debug_settings']);

		$sections['Info'] = Strings::keyValueAsciiTable($items);
		$sections['Reporter Info'] = Strings::keyValueAsciiTable(ErrorReporter::getBasicData(true));

		try {
			$mysqlinfo = $this->db->fetchAllKeyValue("SHOW VARIABLES", array(), 0, 1);
			$sections['MySQL Variables'] = Strings::keyValueAsciiTable($mysqlinfo);
		} catch (\Exception $e) {}

		try {
			$mysqlstatus = $this->db->fetchAllKeyValue("SHOW STATUS", array(), 0, 1);
			$sections['MySQL Status'] = Strings::keyValueAsciiTable($mysqlstatus);
		} catch (\Exception $e) {}

		$sections['Web PHP Info'] = $vars['web_php']['phpinfo'];
		$sections['CLI PHP Info'] = isset($vars['cli_php']['phpinfo']) ? $vars['cli_php']['phpinfo'] : '(unset)';

		$out = '';
		foreach ($sections as $title => $content) {
			$out .= "\n\n\n\n\n";
			$out .= str_repeat('#', 80) . "\n";
			$out .= '# ' . str_pad($title, 76) . ' #' . "\n";
			$out .= str_repeat('#', 80) . "\n";
			$out .= "\n\n";
			$out .= $content;
		}

		$out = trim($out);

		$filename = 'phpinfo.txt';
		$filetype = 'text/plain';

		if (function_exists('gzencode') && !isset($_GET['nogzip'])) {
			$out = gzencode($out);
			$filename = 'phpinfo.txt.gz';
			$filetype = 'application/gzip';
		}

		$response = new Response($out, 200, array(
			'Content-Type' => "application/octet-stream; filename=$filename",
			'Content-Disposition' => "attachment; filename=$filename"
		));

		return $response;
	}

	protected function _getPhpinfoVars($noencode = false)
	{
		$config_hash = md5_file(DP_CONFIG_FILE);

		#------------------------------
		# Binary paths
		#------------------------------

		$binary_paths = array(
			'php' => dp_get_config('php_path'),
			'mysql' => dp_get_config('mysql_path'),
			'mysqldump' => dp_get_config('mysqldump_path')
		);

		#------------------------------
		# Web PHP
		#------------------------------

		$web_php = array();
		$web_php['php_config'] = array(
			'version' => phpversion(),
			'memory_limit' => \Orb\Util\Env::getMemoryLimit(),
			'memory_limit_real' => DP_REAL_MEMSIZE,
			'error_log' => ini_get('error_log'),
			'error_log_real' => DP_REAL_ERROR_LOG,
		);

		ob_start();
		phpinfo();
		$phpinfo = ob_get_clean();
		preg_match('#<body.*?>(.*?)</body>#ms', $phpinfo, $m);

		if (isset($m[1])) {
			$phpinfo = $m[1];
		}

		$web_php['phpinfo'] = $phpinfo;
		$web_php['ini_path'] = \Orb\Util\Env::getPhpIniPathFromInfo($web_php['phpinfo']);
		$web_php['effective_max_upload'] = \Orb\Util\Env::getEffectiveMaxUploadSize();

		#------------------------------
		# CLI PHP
		#------------------------------

		$cli_php = array('phpinfo' => null, 'php_config' => null);

		if (file_exists(dp_get_data_dir() .'/cli-phpinfo.html')) {
			$phpinfo = file_get_contents(dp_get_data_dir() .'/cli-phpinfo.html');
			$cli_php['ini_path'] = \Orb\Util\Env::getPhpIniPathFromInfo($phpinfo);

			if (strpos($phpinfo, '<body') === false) {
				if (!$noencode) {
					$phpinfo = '<code>' . nl2br(htmlspecialchars($phpinfo)) . '</code>';
				}
			} else {
				preg_match('#<body.*?>(.*?)</body>#ms', $phpinfo, $m);

				if (isset($m[1])) {
					$phpinfo = $m[1];
				}
			}

			$cli_php['phpinfo'] = $phpinfo;
		}

		if (file_exists(dp_get_data_dir() .'/cli-server-reqs-check.dat')) {
			$data = file_get_contents(dp_get_data_dir() .'/cli-server-reqs-check.dat');
			$data = @unserialize($data);

			$cli_php['php_config'] = $data;

			if (isset($cli_php['php_config']['memory_limit_real'])) {
				if ($cli_php['php_config']['memory_limit_real'] == -1) {
					$cli_php['effective_max_upload'] = -1;
				} else {
					$cli_php['effective_max_upload'] = $cli_php['php_config']['memory_limit_real'] / 3;
				}
			}
		}

		$has_apc = false;
		if (function_exists('apc_store') && ini_get('apc.enabled')) {
			$has_apc = true;
		}

		$has_wincache = false;
		if (function_exists('wincache_ucache_clear') && ini_get('wincache.ocenabled')) {
			$has_wincache = true;
		}

		$debug_settings = array();
		foreach (dp_get_config('debug') as $k => $v) {
			if (!$v) {
				continue;
			}
			if (is_array($v)) {
				foreach ($v as $sk => $sv) {
					if (!$sv) {
						continue;
					}
					if (!is_scalar($sv)) {
						$debug_settings[$k.'.'.$sk] = print_r($sv,1);
					} else {
						$debug_settings[$k.'.'.$sk] = $sv;
					}
				}
			} else {
				if (!is_scalar($v)) {
					$debug_settings[$k] = print_r($v,1);
				} else {
					$debug_settings[$k] = $v;
				}
			}
		}

		if (dp_get_config('cache.page_cache')) {
			foreach (dp_get_config('cache.page_cache') as $k => $v) {
				$debug_settings['cache.page_cache.'.$k] = $v;
			}
		}

		if (dp_get_config('SETTINGS')) {
			foreach (dp_get_config('SETTINGS') as $k => $v) {
				$debug_settings['SETTINGS.'.$k] = $v;
			}
		}

		$debug_settings['rewrite_urls'] = print_r(dp_get_config('rewrite_urls', false), true);

		return array(
			'binary_paths'   => $binary_paths,
			'web_php'        => $web_php,
			'cli_php'        => $cli_php,
			'config_hash'    => $config_hash,
			'has_apc'        => $has_apc,
			'has_wincache'   => $has_wincache,
			'debug_settings' => $debug_settings,
		);
	}

	############################################################################
	# download-database-schema
	############################################################################

	public function downloadDatabaseSchemaAction()
	{
		$sql = array();

		$sql[] = '### ' . $this->container->getSetting('core.deskpro_url') . "\n";
		$sql[] = '### DeskPRO Build: ' . DP_BUILD_TIME . "\n";
		$sql[] = '### Generated: ' . date('Y-m-d H:i:s') . "\n\n";

		$tables = App::getDb()->fetchAllCol("SHOW TABLES");
		foreach ($tables as $table) {
			$sql[] = "### TABLE: $table\n";
			$sql[] = App::getDb()->fetchColumn("SHOW CREATE TABLE `$table`", array(), 1);
			$sql[] = "\n\n";
		}

		$sql = implode('', $sql);

		$res = $this->createResponse($sql, 200);
		$res->headers->set('Content-Type', array('text/sql; filename=schema.sql'));
		return $res;
	}

	############################################################################
	# server-checks
	############################################################################

	public function serverChecksAction()
	{
		#------------------------------
		# Web checks
		#------------------------------

		$server_check = new \Application\InstallBundle\Install\ServerChecks();
		$server_check->checkServer();

		$is_fatal = $server_check->hasFatalErrors();

		$ini_path = '';
		if ($server_check->hasErrors()) {
			$ini_path = \Orb\Util\Env::getPhpIniPath();
		}

		$table_vars = array(
			'errors' => $server_check->getErrors(),
			'is_fatal' => $is_fatal,
			'has_db_checks' => false,
			'db_config' => App::getConfig('db'),
			'ini_path' => $ini_path,
			'is_win' => $this->container->getSystemService('instance_ability')->isWindows()
		);

		$table = $this->renderView('AdminBundle:Server:server-checks-table.html.php', $table_vars);
		$vars['web_table'] = $table;

		#------------------------------
		# CLI checks
		#------------------------------

		if (file_exists(dp_get_data_dir() .'/cli-server-reqs-check.dat')) {
			$data = file_get_contents(dp_get_data_dir() .'/cli-server-reqs-check.dat');
			$data = @unserialize($data);

			$phpinfo = '';
			if (file_exists(dp_get_data_dir() .'/cli-phpinfo.html')) {
				$phpinfo = file_get_contents(dp_get_data_dir() .'/cli-phpinfo.html');
			}

			$is_fatal = in_array('fatal', $data['checks']);

			$table_vars = array(
				'errors' => $data['checks'],
				'is_fatal' => $is_fatal,
				'has_db_checks' => false,
				'ini_path' => \Orb\Util\Env::getPhpIniPathFromInfo($phpinfo),
				'data_dir' => dp_get_data_dir()
			);

			$table = $this->renderView('AdminBundle:Server:server-checks-table.html.php', $table_vars);
			$vars['cli_table'] = $table;
		}

		return $this->render('AdminBundle:Server:server-checks.html.twig', $vars);
	}


	############################################################################
	# file-checks
	############################################################################

	public function fileChecksAction()
	{
		$verify = new \Application\DeskPRO\Distribution\VerifyChecksums();
		$count  = $verify->countChunks();

		return $this->render('AdminBundle:Server:file-checks.html.twig', array(
			'count' => $count,
		));
	}

	public function fileChecksDoAction($batch = 0)
	{
		$verify = new \Application\DeskPRO\Distribution\VerifyChecksums();
		$results = $verify->compareChunk($batch);

		return $this->render('AdminBundle:Server:file-checks-do.html.twig', array(
			'results' => $results,
			'batch' => $batch
		));
	}


	############################################################################
	# mysqlinfo
	############################################################################

	public function mysqlinfoAction()
	{
		try {
			$mysqlinfo = $this->db->fetchAllKeyValue("SHOW VARIABLES", array(), 0, 1);
		} catch (\Exception $e) {
			$mysqlinfo = null;
		}

		try {
			$schemadiff = \Application\DeskPRO\ORM\Util\Util::getUpdateSchemaSql();
			if ($schemadiff) {
				$schemadiff = implode(";\n", $schemadiff) . ";";
			}
		} catch (\Exception $e) {
			$schemadiff = null;
		}

		return $this->render('AdminBundle:Server:mysqlinfo.html.twig', array(
			'mysqlinfo'  => $mysqlinfo,
			'schemadiff' => $schemadiff,
		));
	}


	############################################################################
	# mysqlstatus
	############################################################################

	public function mysqlstatusAction()
	{
		try {
			$mysqlprocs = $this->db->fetchAll("SHOW PROCESSLIST");
		} catch (\Exception $e) {
			$mysqlprocs = null;
		}

		try {
			$mysqlstatus = $this->db->fetchAllKeyValue("SHOW STATUS", array(), 0, 1);
		} catch (\Exception $e) {
			$mysqlstatus = null;
		}

		return $this->render('AdminBundle:Server:mysqlstatus.html.twig', array(
			'mysqlprocs' => $mysqlprocs,
			'mysqlstatus' => $mysqlstatus
		));
	}

	############################################################################
	# mysqlCollation
	############################################################################

	public function mysqlSortingAction()
	{
		$collation_results = $this->db->fetchAll("SHOW COLLATION WHERE charset = 'utf8'");

		$collation_type_map = array(
			'general' => 'General Purpose (Default)',
			'unicode' => 'Unicode Default',
			'icelandic' => 'Icelandic',
			'latvian' => 'Latvian',
			'romanian' => 'Romanian',
			'slovenian' => 'Slovenian',
			'polish' => 'Polish',
			'estonian' => 'Estonian',
			'spanish' => 'Spanish',
			'spanish2' => 'Spanish (alternative)',
			'swedish' => 'Swedish',
			'turkish' => 'Turkish',
			'czech' => 'Czech',
			'danish' => 'Danish',
			'lithuanian' => 'Lithuanian',
			'slovak' => 'Slovak',
			'roman' => 'Latin',
			'persian' => 'Persian',
			'esperanto' => 'Esperanto',
			'hungarian' => 'Hungarian',
			'sinhala' => 'Sinhalese',
			'general_mysql500' => 'General Purpose (MySQL 5.0)'
		);
		$collations = array();
		foreach ($collation_results AS $collation) {
			if (preg_match('/^utf8_([a-z0-9_]+)_ci$/', $collation['Collation'], $match)) {
				if (isset($collation_type_map[$match[1]])) {
					$collations[$match[0]] = $collation_type_map[$match[1]];
				} else {
					$collations[$match[0]] = $match[1];
				}
			}
		}

		natcasesort($collations);

		$current_collation = App::getSetting('core.db_collation');
		if (!$current_collation) {
			$current_collation = 'utf8_general_ci';
		}

		if (preg_match('/^utf8_([a-z0-9_]+)_ci$/', $current_collation, $match)) {
			if (isset($collation_type_map[$match[1]])) {
				$current_collation_name = $collation_type_map[$match[1]];
			} else {
				$current_collation_name = $match[1];
			}
		} else {
			$current_collation_name = $current_collation;
		}

		$pending_collation = $this->in->getString('pending');
		if (!$pending_collation) {
			if (App::getSetting('core.db_collation_change')) {
				$pending_collation = App::getSetting('core.db_collation_change');
			} else if (file_exists(dp_get_tmp_dir() . '/db-collation-status.txt')) {
				$line = @file_get_contents(dp_get_tmp_dir() . '/db-collation-status.txt');
				if ($line && preg_match('/^\[(\d+)\|([a-z0-9_]+)]([a-z0-9_]+):(.*)$/si', $line, $match)) {
					$pending_collation = $match[2];
				}
			}
		}

		if ($pending_collation && preg_match('/^utf8_([a-z0-9_]+)_ci$/', $pending_collation, $match)) {
			if (isset($collation_type_map[$match[1]])) {
				$pending_collation_name = $collation_type_map[$match[1]];
			} else {
				$pending_collation_name = $match[1];
			}
		} else {
			$pending_collation_name = null;
		}


		return $this->render('AdminBundle:Server:mysql-sorting.html.twig', array(
			'collations' => $collations,
			'current_collation' => $current_collation,
			'current_collation_name' => $current_collation_name,
			'pending_collation' => $pending_collation,
			'pending_collation_name' => $pending_collation_name,
			'selected_collation' => ($pending_collation ? $pending_collation : $current_collation)
		));
	}

	public function mysqlSortingSaveAction()
	{
		$this->ensureRequestToken();

		$new_collation = $this->in->getString('collation');
		if (preg_match('/^utf8_([a-z0-9_]+)_ci$/', $new_collation)) {
			App::getContainer()->getSettingsHandler()->setSetting('core.db_collation_change', $new_collation);
		}

		return $this->redirectRoute('admin_server_mysql_sorting', array('pending' => $new_collation));
	}

	public function mysqlSortingStatusAction()
	{
		$status = 'not_found';
		$data = null;
		$collation = null;

		if (App::getSetting('core.db_collation_change')) {
			$status = 'pending';
			$collation = App::getSetting('core.db_collation_change');
		}

		if (file_exists(dp_get_tmp_dir() . '/db-collation-status.txt')) {
			$line = @file_get_contents(dp_get_tmp_dir() . '/db-collation-status.txt');
			if ($line && preg_match('/^\[(\d+)\|([a-z0-9_]+)]([a-z0-9_]+):(.*)$/si', $line, $match)) {
				$status = $match[3];
				$collation = $match[2];
				$data = array(
					'time' => $match[1],
					'message' => $match[4]
				);
			}
		}

		return $this->createJsonResponse(array(
			'status' => $status,
			'collation' => $collation,
			'data' => $data
		));
	}


	############################################################################
	# error-logs
	############################################################################

	public function errorLogsAction()
	{
		$config_hash = md5_file(DP_CONFIG_FILE);

		if ($this->in->getBool('download')) {

			$file = str_repeat('#', 72) . "\n# error.log\n" . str_repeat('#', 72) . "\n\n";
			$file .= file_get_contents(dp_get_log_dir() . '/error.log');

			$log_file_path = @ini_get('error_log');
			if (!$log_file_path) {
				$log_file_path = dp_get_log_dir() . '/server-phperr-web.log';
			}

			if (is_file($log_file_path) && is_readable($log_file_path)) {
				$file .= "\n\n\n\n\n" . str_repeat('#', 72) . "\n# server-phperr-web.log\n" . str_repeat('#', 72) . "\n\n";
				$file .= file_get_contents($log_file_path);
			}

			$log_file_path = dp_get_log_dir() . '/cli-phperr.log';
			if (is_file($log_file_path) && is_readable($log_file_path)) {
				$file .= "\n\n\n\n\n" . str_repeat('#', 72) . "\n\n\n\n\n# cli-phperr.log\n" . str_repeat('#', 72) . "\n\n";
				$file .= file_get_contents($log_file_path);
			}

			$filename = 'error.log';
			$filetype = 'text/plain';

			if (function_exists('gzencode')) {
				$file = gzencode($file);
				$filename = 'error.log.gz';
				$filetype = 'application/gzip';
			}

			$response = new Response($file, 200, array(
				'Content-Type' => "application/octet-stream; filename=$filename",
				'Content-Disposition' => "attachment; filename=$filename"
			));

			return $response;
		}

		$log_reader = new \Application\DeskPRO\Log\ErrorLog\ErrorLogReader(dp_get_log_dir() . '/error.log');
		$log_reader->setDateTimezone($this->person->getDateTimezone());

		return $this->render('AdminBundle:Server:error-logs.html.twig', array(
			'config_hash' => $config_hash,
			'logs' => $log_reader
		));
	}

	public function viewErrorLogAction($log_id)
	{
		$log_reader = new \Application\DeskPRO\Log\ErrorLog\ErrorLogReader(dp_get_log_dir() . '/error.log');
		$log_reader->setDateTimezone($this->person->getDateTimezone());
		$log_reader->enableRawLog();
		$log_reader->setIdFilter($log_id);

		$log = $log_reader->current();

		if (!$log) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if ($this->in->getBool('download')) {
			header('Content-Disposition: attachment; filename=log-' . $log_id . '.log');
			header('Content-type: text/plain; filename=log-' . $log_id . '.log');
			$res = new \Symfony\Component\HttpFoundation\Response($log['log'], 200);

			return $res;
		}

		return $this->render('AdminBundle:Server:error-view.html.twig', array(
			'log' => $log
		));
	}

	public function errorLogsClearAllAction()
	{
		$this->ensureRequestToken('clear_error_logs', 'x');

		if (!is_writable(dp_get_log_dir() . '/error.log')) {
			return $this->renderStandardError('
				Could not clear errors because the error log file is not writable. Make the file writable and try again.
				You should also <a href="' . $this->generateUrl('admin_server_checks') . '">check your data directory is writable</a>.
				<hr />
				Error log file: ' . dp_get_log_dir() . '/error.log
			');
		}

		@file_put_contents(dp_get_log_dir() . '/error.log', '');
		return $this->redirectRoute('admin_server_error_logs');
	}

	############################################################################
	# attachments
	############################################################################

	public function attachmentsAction()
	{
		$php_vars = array();

		foreach (array('file_uploads', 'upload_tmp_dir', 'upload_max_filesize', 'post_max_size') as $var) {
			$php_vars[$var] = @ini_get($var);
		}

		$php_vars['upload_tmp_dir_real'] = \Orb\Util\Env::getUploadTempDir();

		$failed = false;
		$can_tmp_write = false;
		$attach = false;
		$has_uploaded = false;
		if ($this->in->getBool('test')) {
			$has_uploaded = true;
			$file = $this->request->files->get('file');
			$accept = $this->container->getAttachmentAccepter();

			$error = $accept->getError($file, 'agent');
			if ($error) {
				if ($error['error_code'] == 'no_file') {
					// Try to test uplaod dir writable by us
					$can_tmp_write = \Orb\Util\Env::getUploadTempDir() && is_writable(\Orb\Util\Env::getUploadTempDir());
				}
				$failed = $this->container->getTranslator()->phrase('agent.general.attach_error_' . $error['error_code'], $error);
			} else {
				$attach = $accept->accept($file);
			}
		}

		$filestorage_path = $this->container->getBlobDir();
		$use_fs = ($this->container->getSetting('core.filestorage_method') == 'fs');

		$moving_id = $this->container->getSetting('core.filesystem_move_from_id');
		if ($moving_id) {
			if ($moving_id < 1) {
				$count_done = 0;
			} else {
				$count_done = $this->container->getDb()->fetchColumn("SELECT COUNT(*) FROM blobs WHERE id < ?", array($moving_id));
			}
			$count_todo = $this->container->getDb()->fetchColumn("SELECT COUNT(*) FROM blobs", array($moving_id));
			if (!$count_todo) {
				$count_todo = 1;
			}
			$count_left = $count_todo - $count_done;
			$count_perc = floor(($count_done / $count_todo) * 100);
		} else {
			$count_done = $count_todo = $count_left = $count_perc = 0;

			$count_todo = $this->container->getDb()->fetchColumn("SELECT COUNT(*) FROM blobs");
		}

		$total_size = $this->container->getDb()->fetchColumn("SELECT SUM(filesize) FROM blobs");
		$total_size_readable = Numbers::filesizeDisplay($total_size);

		$php_ini = \Orb\Util\Env::getPhpIniPath();
		$effective_max = \Orb\Util\Env::getEffectiveMaxUploadSize();

		$php_vars['memory_limit'] = \Orb\Util\Env::getMemoryLimit();
		$php_vars['memory_limit_real'] = DP_REAL_MEMSIZE;

		$effect_max_display = Numbers::getFilesizeDisplayParts($effective_max);
		$effect_max_display = ($effect_max_display['number'] > 1 ? floor($effect_max_display['number']) : $effect_max_display['number']) . ' ' . $effect_max_display['symbol'];

		return $this->render('AdminBundle:Server:attachments.html.twig', array(
			'php_vars' => $php_vars,
			'has_uploaded' => $has_uploaded,
			'attach' => $attach,
			'failed' => $failed,
			'can_tmp_write' => $can_tmp_write,
			'php_ini' => $php_ini,
			'effective_max' => $effective_max,
			'effective_max_display' => $effect_max_display,

			'filestorage_path' => $filestorage_path,
			'use_fs' => $use_fs,
			'moving_id' => $moving_id,
			'count_done' => $count_done,
			'count_todo' => $count_todo,
			'count_left' => $count_left,
			'count_perc' => $count_perc,
			'total_size' => $total_size,
			'total_size_readable' => $total_size_readable,
		));
	}

	public function attachmentsSwitchAction()
	{
		$this->ensureRequestToken();

		$use_fs = ($this->container->getSetting('core.filestorage_method') == 'fs');
		if ($use_fs) {
			$this->container->getEm()->getRepository('DeskPRO:Setting')->updateSetting('core.filestorage_method', 'db');
			$this->db->executeUpdate("
				UPDATE blobs
				SET storage_loc_pref = 'db'
				WHERE storage_loc != 'db'
			");
		} else {
			$this->container->getEm()->getRepository('DeskPRO:Setting')->updateSetting('core.filestorage_method', 'fs');
			$this->db->executeUpdate("
				UPDATE blobs
				SET storage_loc_pref = 'fs'
				WHERE storage_loc != 'fs'
			");
		}

		$this->container->getEm()->getRepository('DeskPRO:Setting')->updateSetting('core.filesystem_move_from_id', '-1');

		return $this->redirectRoute('admin_server_attach');
	}

	############################################################################
	# test-email
	############################################################################

	public function testEmailAction()
	{
		if ($this->getRequest()->getMethod() == 'POST') {
			$tr = $this->em->find('DeskPRO:EmailTransport', $this->in->getUint('email_transport_id'));
			$this->container->getSettingsHandler()->setTemporarySettingValues(array('core.default_from_email' => $this->in->getString('from')));

			$message = $this->container->getMailer()->createMessage();
			$message->setTo($this->in->getString('to'));
			$message->setFrom($this->in->getString('from'));
			$message->setSubject($this->in->getString('subject'));
			$message->setBody($this->in->getString('message'));

			$failed = array();

			$send_when = $this->in->getString('send_when');
			if ($send_when == 'queued') {
				$message->enableQueueHint();
				$this->container->getMailer()->send($message, $failed);
			} else {
				$message->setForceTransport($tr->getTransport());
				$this->container->getMailer()->sendNow($message, $failed);
			}


			$log = implode("\n", $this->container->getMailer()->getLogMessages());

			return $this->render('AdminBundle:Server:test-email-result.html.twig', array(
				'failed'    => $failed,
				'log'       => $log,
				'send_when' => $send_when,
			));
		}

		$all_transports = $this->em->getRepository('DeskPRO:EmailTransport')->findAll();

		return $this->render('AdminBundle:Server:test-email.html.twig', array(
			'all_transports' => $all_transports,
		));
	}
}
