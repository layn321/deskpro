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

use Application\DeskPRO\Domain\DomainObject;
use Imagine\Image\Box;
use Orb\Util\Strings;

if (!defined('DP_ROOT')) exit('No access');

require_once DP_ROOT.'/src/Orb/Data/ContentTypes.php';
require_once DP_ROOT.'/sys/serve_abstract.php';

if (!isset($DP_LOG_MESSAGES)) {
	$DP_LOG_MESSAGES = array();
}

/**
 * Database and filesystem-stored files are served through this file.
 *
 * Every blob has an "authcode" which doubely serves as a sort of password,
 * but it also embeds the blobs own ID and other required data.
 *
 * Database Files
 * --------------
 *
 * There is nothing very special about database-stored files. The blobs authcode is:
 * (id)(password)(0)
 *
 * That is the blob ID, a random string, and zero. The trailing zero tells this script
 * that it needs to fetch it from the database rather than the filesystem.
 *
 * Filesystem Files
 * ----------------
 *
 * Files in the filesystem are stored under the file root in folders counting up from 0. Each
 * folder has 1000 files in it.
 *
 * The authcode is:
 * (folder)(password)(id)(namehash)
 *
 * The "namehash" is a specil hash of the file filename. Since we don't connect to the database,
 * there's no way to know what the "real" filename of a file is. We output URLs with the correct filename in it
 * (aka it's a template-time thing), but we use the namehash to verify it is correct as an anti-spoofing mechanism.
 *
 * The hash is six characters, the three is part of a sha1 and the second three is part of an md5. While probably
 * possible to spoof the name still, using two different hashing functions should make it relatively hard.
 *
 * Since we can now trust the filename, we can use it to guess a mime-type based on extension, and send the correct headers,
 * all without connecting to the database.
 */
class FilestorageLoader extends LoaderAbstract
{
	/**
	 * @var string
	 */
	protected $error_mode = 'error';

	public function runAction()
	{
		if (isset($_GET['debug'])) {
			$this->error_mode = 'exception';
		}

		try {
			$pathinfo = $this->getPathInfo();

			$this->addLogMessage("pathinfo: %s", $pathinfo);

			if (preg_match('#^/size/([0-9]+)/#', $pathinfo, $m)) {
				$_GET['s'] = $m[1];
				$pathinfo = str_replace($m[0], '/', $pathinfo);
			}
			if (preg_match('#^/size-fit/#', $pathinfo, $m)) {
				$_GET['size-fit'] = 1;
				$pathinfo = str_replace($m[0], '/', $pathinfo);
			}

			// Default avatar: /avatar/50/default
			if (preg_match('#^/avatar/([0-9]+)/default.jpg#', $pathinfo, $m)) {
				$this->defaultAvatarAction($m[1]);

			// Person avatar: /avatar/13
			} elseif (preg_match('#^/avatar/([0-9]+)#', $pathinfo, $m)) {
				$this->personAvatarAction($m[1]);

			// Default org avatar: /o-avatar/default
			} elseif (preg_match('#^/o-avatar/default#', $pathinfo)) {
				$this->defaultOrgAvatarAction();

			// Org avatar: /o-avatar/13
			} elseif (preg_match('#^/o-avatar/([0-9]+)#', $pathinfo, $m)) {
				$this->orgAvatarAction($m[1]);

			// User CSS
			} elseif (preg_match('#^/res-user/main.css#', $pathinfo, $m)) {
				$this->userCssAction();

			// sitemap.xml
			} elseif (preg_match('#^/sitemap.xml#', $pathinfo, $m)) {
				$this->sitemapXmlAction();

			// A filesystem blob like /123AJKJKHSD1244AXC/filename.zip
			// That is: /(batch)(authcode)(id)(namehash)/name.zip
			//0XNSNTQHTNR43DD567
			} elseif (preg_match('#^/([0-9]+)([A-Z]+)([0-9]+)([A-Z0-9]{6})(?:/|\-)(.*?)$#', $pathinfo, $m)) {
				$this->addLogMessage("handleFilesystemBlobRequest: %s", implode(', ', $m));
				$this->handleFilesystemBlobRequest(
					$m[1],
					$m[2],
					$m[3],
					$m[4],
					$m[5]
				);

			// A database-stored bloblike /123AHSDHJGSD0/filename.zip
			// That is (id)(authcode0)
			// The trailing 0 denotes it as a database storage authcode
			} elseif (preg_match('#^/([0-9]+)([A-Z]+0)(?:/|\-)(.*?)$#', $pathinfo, $m)) {
				$this->addLogMessage("handleDbBlobRequest: %s", implode(', ', $m));
				$this->handleDbBlobRequest($m[1], $m[2], $m[3]);
			} elseif (preg_match('#^/gradient$#', $pathinfo)) {
				$this->handleGradientRequest();
			} else {
				if ($this->error_mode == 'exception') {
					throw new \Exception("File not found. (bad_route)", 400);
				}
				header("HTTP/1.0 404 Not Found");
				echo "File not found. (bad_route)";
			}
		} catch (\Exception $exception) {
			if (isset($GLOBALS['DP_CONFIG']['debug']['dev']) || isset($GLOBALS['DP_CONFIG']['serve_file_debug']) && $GLOBALS['DP_CONFIG']['serve_file_debug']) {

				if (!empty($GLOBALS['DP_LOG_MESSAGES'])) {
					echo "\n\n\n";
					echo "LOG\n" . str_repeat('=', 72) . "\n";
					foreach ($GLOBALS['DP_LOG_MESSAGES'] as $minfo) {
						printf("[%s] %s\n", date('Y-m-d H:i:s', $minfo['time']), $minfo['message']);
					}
				}

				echo "\n\n\nEXCEPTION\n" . str_repeat('=', 72) . "\n";
				echo "[{$exception->getCode()}] {$exception->getMessage()}\n\n";

				$backtrace = $exception->getTrace();
				$trace = self::formatBacktrace($backtrace);
				echo $trace;
			}

			$this->handleException($exception);
		}
	}

	/**
	 * @param string $message
	 */
	private function addLogMessage($message)
	{
		global $DP_LOG_MESSAGES;

		$args = func_get_args();
		array_shift($args);

		if ($args) {
			$message = vsprintf($message, $args);
		}

		$DP_LOG_MESSAGES[] = array(
			'time' => time(),
			'message' => $message
		);
	}


	/**
	 * Serve user CSS blob
	 */
	public function userCssAction()
	{
		$is_rtl = !empty($_GET['rtl']);
		$blob_column = $is_rtl ? 'css_blob_rtl_id' : 'css_blob_id';

		$sth = $this->getPdo()->prepare("
			SELECT blobs.*
			FROM styles
			INNER JOIN blobs ON (blobs.id = styles.$blob_column)
			WHERE styles.id = 1
		");
		$sth->execute();
		$blob = $sth->fetch(\PDO::FETCH_ASSOC);
		$did_reload = false;

		if (
			!$blob ||
			(
				isset($_GET['reload'])
				&& (
					filemtime(DP_ROOT . '/src/Application/UserBundle/Resources/views/Css/main.css.twig') > strtotime($blob['date_created'])
					|| filemtime(DP_ROOT . '/src/Application/UserBundle/Resources/views/Css/custom.css.twig') > strtotime($blob['date_created'])
				)
			)
		) {
			$did_reload = true;
			$container = $this->bootFullSystem();
			$css = $container->get('templating')->render('UserBundle:Css:main.css.twig', array());

			if ($is_rtl) {
				// filter CSS to change LTR ideas to RTL
				preg_match_all('#/\*@no_rtl\*/(.*)/\*@/no_rtl\*/#sU', $css, $matches, PREG_SET_ORDER);
				$replace = array();

				foreach ($matches AS $key => $match) {
					$replace[$key] = $match[1];
					$css = str_replace($match[0], "\x1a$key\x1a", $css);
				}

				// where the value is left/right
				$css = preg_replace_callback('/(?<=[^a-z0-9_-])(float|clear|text-align)\s*:\s*(left|right)/i', function($match) {
					switch (strtolower($match[2])) {
						case 'left': $new = 'right'; break;
						case 'right': $new = 'left'; break;
						default: $new = $match[2];
					}

					return "$match[1]: $new";
				}, $css);

				// where the rule name contains left/right
				$css = preg_replace_callback('/(?<=[^a-z0-9_-])(padding|margin|border)-(left|right)\s*:/i', function($match) {
					switch (strtolower($match[2])) {
						case 'left': $new = 'right'; break;
						case 'right': $new = 'left'; break;
						default: $new = $match[2];
					}

					return "$match[1]-$new:";
				}, $css);
				$css = preg_replace_callback('/(?<=[^a-z0-9_-])(border)-(left|right)-([a-z]+)\s*:/i', function($match) {
					switch (strtolower($match[2])) {
						case 'left': $new = 'right'; break;
						case 'right': $new = 'left'; break;
						default: $new = $match[2];
					}

					return "$match[1]-$new-$match[3]:";
				}, $css);

				// where the shortcut defines left/right
				$css = preg_replace_callback(
					'/(?<=[^a-z0-9_-])(padding|margin)\s*:\s*([a-z0-9\._-]+)\s+([a-z0-9\._-]+)\s+([a-z0-9\._-]+)\s+([a-z0-9\._-]+)/i',
					function($match) {
						return "$match[1]: $match[2] $match[5] $match[4] $match[3]";
					}, $css
				);
				$css = preg_replace_callback(
					'/(?<=[^a-z0-9_-])((-[a-z]+-)?border-radius)\s*:\s*([a-z0-9\._-]+)\s+([a-z0-9\._-]+)\s+([a-z0-9\._-]+)\s+([a-z0-9\._-]+)/i',
					function($match) {
						$tl = $match[3];
						$tr = $match[4];
						$br = $match[5];
						$bl = $match[6];
						return "$match[1]: $tr $tl $bl $br";
					}, $css
				);
				$css = preg_replace_callback(
					'/(?<=[^a-z0-9_-])((-[a-z]+-)?border-[a-z]+)-(left|right)-(radius)\s*:/i',
					function($match) {
						switch (strtolower($match[3])) {
							case 'left': $new = 'right'; break;
							case 'right': $new = 'left'; break;
							default: $new = $match[2];
						}

						return "$match[1]-$new-$match[4]:";
					}, $css
				);
				$css = preg_replace_callback(
					'/(?<=[^a-z0-9_-])((-[a-z]+-)?border-radius-[a-z]+)(left|right)\s*:/i',
					function($match) {
						switch (strtolower($match[3])) {
							case 'left': $new = 'right'; break;
							case 'right': $new = 'left'; break;
							default: $new = $match[2];
						}

						return "$match[1]$new:";
					}, $css
				);

				// where the rule name is left/right
				$css = preg_replace_callback('/(?<=[^a-z0-9_-])(left|right)\s*:/i', function($match) {
					switch (strtolower($match[1])) {
						case 'left': $new = 'right'; break;
						case 'right': $new = 'left'; break;
						default: $new = $match[1];
					}

					return "$new:";
				}, $css);

				$position_flip = function($x) {
					if (strtolower($x) == 'left') {
						return 'right';
					} else if (strtolower($x) == 'left') {
						return 'left';
					} else if (preg_match('/^([0-9.]+)%$/', $x, $percent)) {
						return (100 - $percent[1]) . '%'; // percentage left offset on right
					} else if (preg_match('/^0[a-z]*$/i', $x)) {
						return '100%'; // left to completely right
					} else {
						return $x; // can't flip
					}
				};

				// flip background position
				$css = preg_replace_callback(
					'/(?<=[^a-z0-9_-])(background-position)\s*:\s*([a-z0-9\._-]+)/i',
					function($match) use ($position_flip) {
						$x = $position_flip($match[2]);
						return "$match[1]: $x";
					}, $css
				);

				// flip background
				$css = preg_replace_callback(
					'/(?<=[^a-z0-9_-])(background)\s*:\s*([^;}]*?url\([^;}]+?)\s+(left|right|center|[0-9.]+%|0[a-z]*)/i',
					function($match) use($position_flip) {
						$x = $position_flip($match[3]);
						return "$match[1]: $match[2] $x";
					}, $css
				);

				foreach ($replace AS $key => $replace_css) {
					$css = str_replace("\x1a$key\x1a", $replace_css, $css);
				}

				$css .= "/* RTL filter */";
			} else {
				$css = str_replace('/*@no_rtl*/', '', $css);
				$css = str_replace('/*@/no_rtl*/', '', $css);
			}

			$blob = $container->getBlobStorage()->createBlobRecordFromString(
				$css,
				$is_rtl ? 'main-rtl.css' : 'main.css',
				'text/css'
			);
			$blob_id = $blob->getId();

			$container->getDb()->update('styles', array($blob_column => $blob_id), array('id' => 1));

			$sth = $this->getPdo()->prepare("
				SELECT blobs.*
				FROM blobs
				WHERE blobs.id =?
				LIMIT 1
			");
			$sth->execute(array($blob_id));
			$blob = $sth->fetch(\PDO::FETCH_ASSOC);
		}

		if (!$blob) {
			if ($this->error_mode == 'exception') {
				throw new \Exception("File not found. (no_css_blob_id)", 400);
			}
			header("HTTP/1.0 404 Not Found");
			echo "File not found (no_css_blob_id)";
			return;
		}

		if (!$did_reload) {
			$this->error_mode = 'exception';
		}

		try {
			$this->showBlob($blob);
		} catch (\Exception $e) {
			$_GET['reload'] = true;
			$this->showBlob($blob);
		}
	}


	/**
	 * Generates a gradient image on the fly
	 */
	public function handleGradientRequest()
	{
		if (!function_exists('imagepng') || (!function_exists('imagecreatetruecolor') && !function_exists('imagecreate'))) {
			if ($this->error_mode == 'exception') {
				throw new \Exception("File not found. (no_image_manip)", 400);
			}
			header("HTTP/1.0 404 Not Found");
			echo "File not found (no_image_manip)";
			return;
		}

		require DP_ROOT . '/src/Orb/Util/Colors.php';
		require DP_ROOT . '/src/Orb/Images/Util.php';

		$start_color = isset($_REQUEST['start_color']) ? (string)$_REQUEST['start_color'] : '000000';
		$end_color   = isset($_REQUEST['end_color'])   ? (string)$_REQUEST['end_color']   : '000000';

		$get_rgb = function($color) {
			// Not rgb(
			if (!strpos($color, '(') || !strpos($color, ')')) {
				$color = preg_replace('#[^a-fA-F0-9]#', '', $color);
				if (strlen($color) == 6 || strlen($color) == 3) {
					$color = \Orb\Util\Colors::hex2rgb($color);
					if ($color) {
						$color = 'rgb(' . implode(',', $color) . ')';
					} else {
						$color = 'rgb(0,0,0)';
					}
				} else {
					$color = 'rgb(0,0,0)';
				}
			}

			if (preg_match('#rgb\((.*?),(.*?),(.*?)\)#i', $color, $m)) {
				$rgb = array(
					'red'   => (int)trim($m[1]),
					'green' => (int)trim($m[2]),
					'blue'  => (int)trim($m[3]),
				);
				return $rgb;
			} else {
				return array('red' => 0, 'green' => 0, 'blue' => 0);
			}
		};

		$start_color = $get_rgb($start_color);
		$end_color   = $get_rgb($end_color);

		$size = isset($_REQUEST['size']) ? (int)$_REQUEST['size'] : 20;
		if ($size < 1) $size = 20;
		if ($size > 1000) $size = 1000;

		$direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'vertical';
		if ($direction != 'vertical' && $direction != 'horizontal') {
			$direction = 'vertical';
		}

		$im = \Orb\Images\Util::getGradientImage($size, $start_color, $end_color, $direction, 1, 1);

		$desc = implode('-',$start_color) . '_' . implode('-', $end_color) . '_' . $direction . '_' . $size . '.png';

		header('Last-Modified: ' . date('D, d M Y H:i:s', 1366187634).' GMT');
		header('Expires: ' . date('D, d M Y H:i:s', 1366187657).' GMT');
		header('Cache-Control: max-age=31556926,public');
		header('Content-Disposition: inline; filename=' . $desc);
		header("Content-type: image/png");
		imagepng($im);
		exit;
	}


	/**
	 * Serve the sitemap.xml file
	 */
	public function sitemapXmlAction()
	{
		$sth = $this->getPdo()->prepare("
			SELECT *
			FROM blobs
			WHERE sys_name = 'sitemap_xml'
		");
		$sth->execute();
		$blob = $sth->fetch(\PDO::FETCH_ASSOC);

		if (!$blob) {
			if ($this->error_mode == 'exception') {
				throw new \Exception("File not found. (no_sitemap_blob)", 400);
			}
			header("HTTP/1.0 404 Not Found");
			echo "File not found. (no_sitemap_blob)";
			return;
		}

		$this->showBlob($blob);
	}


	/**
	 * Render a persons avatar.
	 *
	 * @deprecated If you have a person record, then use picture_blob_id and directly link to the avatar
	 * @param $person_id
	 * @return mixed
	 */
	public function personAvatarAction($person_id)
	{
		$sth = $this->getPdo()->prepare("
			SELECT *
			FROM blobs
			LEFT JOIN people ON (blobs.id = people.picture_blob_id)
			WHERE people.id = :person_id
		");
		$sth->execute(array('person_id' => $person_id));
		$blob = $sth->fetch(\PDO::FETCH_ASSOC);

		if (!$blob) {
			$this->defaultAvatarAction();
			return;
		}

		$size = null;
		if (isset($_GET['s']) && is_numeric($_GET['s']) && $_GET['s'] > 1 && $_GET['s'] <= 600) {
			$size = $_GET['s'];
		}

		$this->showBlob($blob, $size);
	}

	/**
	 * Render an org avatar.
	 *
	 * @deprecated If you have a org record, then use picture_blob_id and directly link to the avatar
	 * @param $person_id
	 * @return mixed
	 */
	public function orgAvatarAction($org_id)
	{
		$sth = $this->getPdo()->prepare("
			SELECT *
			FROM blobs
			LEFT JOIN organizations ON (blobs.id = organizations.picture_blob_id)
			WHERE organizations.id = :org_id
		");
		$sth->execute(array('org_id' => $org_id));
		$blob = $sth->fetch(\PDO::FETCH_ASSOC);

		if (!$blob) {
			$this->defaultOrgAvatarAction();
			return;
		}

		$size = null;
		if (isset($_GET['s']) && is_numeric($_GET['s']) && $_GET['s'] > 1 && $_GET['s'] <= 600) {
			$size = $_GET['s'];
		}

		$this->showBlob($blob, $size);
	}


	/**
	 * Serve the default avatar
	 */
	public function defaultAvatarAction($s = null)
	{
		$name = 'picture-default';
		if (isset($_GET['is_agent'])) {
			$name = 'picture-default-agent';
		}

		$sth = $this->getPdo()->prepare("SELECT * FROM blobs WHERE sys_name = :sys_name");
		$sth->execute(array('sys_name' => $name));
		$blob = $sth->fetch(\PDO::FETCH_ASSOC);

		// The default avatar blob hasnt been inserted yet, default it from the resources dir now
		if (!$blob) {
			$container = $this->bootFullSystem();
			$blob_entity = $container->getBlobStorage()->createBlobRecordFromFile(
				DP_ROOT.'/src/Application/DeskPRO/Resources/assets/'.$name.'.jpeg',
				$name . '.jpeg',
				'image/jpeg',
				array('sys_name' => $name)
			);

			$blob = $blob_entity->toArray(DomainObject::TOARRAY_ONLY_PRIMATIVES);
		}

		$size = null;
		if ($s !== null) {
			$size = (int)$s;
		} elseif (isset($_GET['s']) && is_numeric($_GET['s']) && $_GET['s'] > 1 && $_GET['s'] <= 600) {
			$size = $_GET['s'];
		}

		$this->showBlob($blob, $size);
	}


	/**
	 * Serve the default org avatar
	 */
	public function defaultOrgAvatarAction()
	{
		$name = 'orgpicture-default';

		$sth = $this->getPdo()->prepare("SELECT * FROM blobs WHERE sys_name = :sys_name");
		$sth->execute(array('sys_name' => $name));
		$blob = $sth->fetch(\PDO::FETCH_ASSOC);

		// The default avatar blob hasnt been inserted yet, default it from the resources dir now
		if (!$blob) {
			$container = $this->bootFullSystem();
			$blob_entity = $container->getBlobStorage()->createBlobRecordFromFile(
				DP_ROOT.'/src/Application/DeskPRO/Resources/assets/'.$name.'.jpeg',
				$name . '.jpeg',
				'image/jpeg',
				array('sys_name' => $name)
			);

			$blob = $blob_entity->toArray(DomainObject::TOARRAY_ONLY_PRIMATIVES);
		}

		$size = null;
		if (isset($_GET['s']) && is_numeric($_GET['s']) && $_GET['s'] > 1 && $_GET['s'] <= 600) {
			$size = $_GET['s'];
		}

		$this->showBlob($blob, $size);
	}


	/**
	 * @param int $blob_id
	 * @param string $blob_auth
	 * @param string $blob_filename
	 */
	protected function handleFilesystemBlobRequest($batch, $authcode, $blob_id, $namehash, $filename)
	{
		#------------------------------
		# If its a simple file request we
		# can serve it without a db connection
		#------------------------------

		global $DP_CONFIG;

		$base_path = dp_get_blob_dir();

		$filepath = $base_path . DIRECTORY_SEPARATOR . $batch . DIRECTORY_SEPARATOR . $batch.$authcode . $blob_id . $namehash;

		$filename_safe = Strings::utf8_accents_to_ascii($filename);
		$filename_safe = preg_replace('#[^a-zA-Z0-9\-_\.]#', '-', $filename_safe);
		$filename_safe = preg_replace('#\-{2,}#', '-', $filename_safe);

		$check_namehash = strtoupper(substr(sha1($filename_safe . $blob_id), 0, 3));
		$check_namehash .= strtoupper(substr(md5($filename_safe . $blob_id), 0, 3));

		$this->addLogMessage("Expecting file path: %s", $filepath);

		$size = null;
		if (isset($_GET['s']) && ((is_numeric($_GET['s']) && $_GET['s'] > 1 && $_GET['s'] <= 600) || preg_match('#^\d+x\d+$#', $_GET['s']))) {
			$size = $_GET['s'];
			$this->addLogMessage("With size: %s", $size);
		}

		// Invalid name hash
		// But we have to double-check before failing since the filename could
		// possibly be custom in the case of downloads
		if ($check_namehash != $namehash) {
			$this->addLogMessage("Hash mismatch: %s !=", $check_namehash, $namehash);

			$sth = $this->getPdo()->prepare("SELECT * FROM blobs WHERE id = :id");
			$sth->execute(array('id' => $blob_id));
			$blob = $sth->fetch(\PDO::FETCH_ASSOC);

			if ($blob['filename']) {
				$blob['filename_safe'] = Strings::utf8_accents_to_ascii($blob['filename']);
				$blob['filename_safe'] = preg_replace('#[^a-zA-Z0-9\-_\.]#', '-', $blob['filename_safe']);
				$blob['filename_safe'] = preg_replace('#\-{2,}#', '-', $blob['filename_safe']);
			}

			if (!$blob || ($blob['filename'] != $filename && $blob['filename_safe'] != $filename && $blob['filename_safe'] != $filename_safe)) {
				if ($this->error_mode == 'exception') {
					throw new \Exception("File not found. (2.1)", 400);
				}
				header("HTTP/1.0 404 Not Found");
				echo "File not found. (2.1)";
				return;
			}
		}

		// The file doesnt exist on disk
		if (!file_exists($filepath)) {
			$sth = $this->getPdo()->prepare("SELECT * FROM blobs WHERE id = :id");
			$sth->execute(array('id' => $blob_id));
			$blob = $sth->fetch(\PDO::FETCH_ASSOC);

			// Try to detect bad css file and reload it automatically
			if ($filename == 'main.css') {
				$is_css = false;
				$q = $this->getPdo()->query("SELECT css_blob_id FROM styles");
				while ($r = $q->fetch(\PDO::FETCH_ASSOC)) {
					if ($r['css_blob_id'] == $blob_id) {
						$is_css = true;
						break;
					}
				}

				if ($is_css) {
					$this->getPdo()->exec("UPDATE styles SET css_blob_id = NULL");
					$this->userCssAction();
					exit;
				}
			}

			// Fallback on DB check, it may have been moved
			if ($blob['storage_loc'] != 'fs') {
				$this->showBlob($blob_id, $size);
			}
			return;
		}

		#------------------------------
		# See if we need to resize
		#------------------------------

		if ($size) {
			$this->showBlob($blob_id, $size);
			return;
		}

		#------------------------------
		# Serve up
		#------------------------------

		$mimetype = \Orb\Data\ContentTypes::getContentTypeFromFilename($filename);
		if (!$mimetype) {
			$mimetype = 'application/octet-stream';
		}

		$content_disposition = 'attachment';
		if (!isset($_GET['dl']) && \Orb\Data\ContentTypes::isInlineContentType($mimetype)) {
			$content_disposition = 'inline';
		}

		header('Content-Type: ' . $mimetype . '; filename="' . addslashes($filename) . '"');
		header('Content-Length: ' . filesize($filepath));
		header('Content-Disposition: '.$content_disposition.'; filename="' . addslashes($filename) . '"');
		header('Last-Modified: ' . date('D, d M Y H:i:s', strtotime('2010-01-01')).' GMT');
		header('Expires: ' . date('D, d M Y H:i:s', strtotime('+1 year')).' GMT');
		header('Cache-Control: max-age=31556926,private');

		if (isset($DP_CONFIG['filestorage_use_xsendfile']) && $DP_CONFIG['filestorage_use_xsendfile']) {
			header("X-Sendfile: $filepath");
		} else {
			readfile($filepath);
		}
	}

	protected function handleDbBlobRequest($blob_id, $authseg, $filename)
	{
		$authcode = $blob_id . $authseg;

		$size = null;
		if (isset($_GET['s']) && ((is_numeric($_GET['s']) && $_GET['s'] > 1 && $_GET['s'] <= 600) || preg_match('#^\d+x\d+$#', $_GET['s']))) {
			$size = $_GET['s'];
		}

		$this->showBlob($blob_id, $size, $authcode);
	}


	/**
	 * Renders a blob
	 *
	 * @param $blob
	 * @param null $size
	 */
	protected function showBlob($blob, $size = null, $blob_auth = null)
	{
		#------------------------------
		# Fetch the blob
		#------------------------------

		if (!is_array($blob)) {

			$blob_id = $blob;

			$this->addLogMessage("Loading blob %d", $blob_id);

			$sth = $this->getPdo()->prepare("SELECT * FROM blobs WHERE id = :id");
			$sth->execute(array('id' => $blob_id));
			$blob = $sth->fetch(\PDO::FETCH_ASSOC);

			if (!$blob) {
				$this->addLogMessage("Could not load blob record");
			} elseif ($blob_auth && $blob['authcode'] != $blob_auth) {
				$this->addLogMessage("bad authcode: %s != %s", $blob['authcode'], $blob_auth);
			}

			if (!$blob || ($blob_auth && $blob['authcode'] != $blob_auth)) {
				// Check for a sign code that overrides the authcode check
				// (See TicketMessage::procInlineAttach)
				$okay = false;
				if (!empty($_GET['sc'])) {
					$sth = $this->getPdo()->prepare("SELECT value FROM settings WHERE name = 'core.install_token'");
					$sth->execute();
					$install_token = $sth->fetchColumn(0);

					if (\Orb\Util\Util::checkStaticSecurityToken($_GET['sc'], $install_token . $blob_auth)) {
						$okay = true;
					}
				}

				if (!$okay) {
					if ($this->error_mode == 'exception') {
						throw new \Exception("File not found. (3)", 400);
					}
					header("HTTP/1.0 404 Not Found");
					echo "File not found. (3)";
					return;
				}
			}
		}

		$blob_id = $blob['id'];

		#------------------------------
		# Serve the file
		#------------------------------

		if (!isset($blob['filename_safe'])) {
			$filename_safe = Strings::utf8_accents_to_ascii($blob['filename']);
			$filename_safe = preg_replace('#[^a-zA-Z0-9\-_\.]#', '-', $filename_safe);
			$filename_safe = preg_replace('#\-{2,}#', '-', $filename_safe);
			$blob['filename_safe'] = $filename_safe;
		}

		$is_image = false;
		switch ($blob['content_type']) {
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/gif':
			case 'image/png':
				$is_image = true;
				break;
		}

		if ($is_image && $size) {
			$this->addLogMessage("Showing resized: " . $size);

			$is_fit = false;

			if (isset($_GET['size-fit'])) {
				$is_fit = (boolean)$_GET['size-fit'];
				$this->addLogMessage("Is fit: %d", $is_fit);
			}

			$sth = $this->getPdo()->prepare("SELECT * FROM blobs WHERE original_blob_id = :original_blob_id AND sys_name = :sys_name");
			$sth->execute(array('original_blob_id' => $blob_id, 'sys_name' => $this->getSizedBlobSysName($blob_id, $size, $is_fit)));
			$sub_blob = $sth->fetch(\PDO::FETCH_ASSOC);

			// Already have the cached resized blob
			if ($sub_blob) {
				$sub_blob['filename_safe'] = $blob['filename_safe'];
				$blob = $sub_blob;

			// Generate the resized blob and save it now
			} else {
				$new_blob = $this->createSizedBlob($blob, $size, $is_fit, $this->getPdo(), false);

				if ($new_blob) {
					// Possible the resize failed, in which case we'd fall back on showing the orig
					// So only reassign blob if we know $new_blob was actually made
					$blob = $new_blob;
				}
			}
		}

		if (!empty($blob['file_url']) && $blob['file_url']) {
			// Need to send through this controller if its a download
			// request and the file is usually stored with an inline disposition
			if (!empty($_GET['dl']) && \Orb\Data\ContentTypes::isInlineContentType($blob['content_type'])) {
				$this->sendHeaders($blob);
				$fp = @fopen($blob['file_url'], 'r');
				while (!@feof($fp)) {
					echo @fread($fp, 1024);
				}
				@fclose($fp);
				exit;
			}

			header("HTTP/1.1 301 Moved Permanently");
			header("Location: {$blob['file_url']}");
			exit;
		}

		if ($blob['storage_loc'] == 'fs') {
			$this->sendFromFilesystem($blob);
		} else {
			$this->sendFromDatabase($blob, $this->getPdo());
		}
	}


	/**
	 * Send general file headers.
	 *
	 * Blobs never change so we can enable aggresive cache control on files served.
	 *
	 * @param $blob
	 */
	protected function sendHeaders($blob)
	{
		header('Content-Type: ' . $blob['content_type'] . '; filename="' . addslashes($blob['filename']) . '"');
		header('Content-Length: ' . $blob['filesize']);

		if (!isset($_GET['dl']) && \Orb\Data\ContentTypes::isInlineContentType($blob['content_type'])) {
			header('Content-Disposition: inline; filename="' . addslashes($blob['filename']) . '"');
		} else {
			header('Content-Disposition: attachment; filename="' . addslashes($blob['filename_safe']) . '"');
		}

		$d = \DateTime::createFromFormat('Y-m-d H:i:s', $blob['date_created']);
		if (!$d) {
			$d = new \DateTime();
		}
		header('Last-Modified: ' . $d->format('D, d M Y H:i:s').' GMT');
		header('Expires: ' . date('D, d M Y H:i:s', strtotime('+1 year')).' GMT');
		header('Cache-Control: max-age=31556926,private');
	}


	/**
	 * Send a file that is stored in the filesystem
	 *
	 * @param $blob
	 */
	protected function sendFromFilesystem($blob)
	{
		global $DP_CONFIG;

		$this->sendHeaders($blob);

		// folder we store blobs in
		$base_path = dp_get_blob_dir();

		$filepath = $base_path . DIRECTORY_SEPARATOR . $blob['save_path'];

		$this->addLogMessage("Expecting file path: %s", $filepath);

		if (!file_exists($filepath)) {
			if ($this->error_mode == 'exception') {
				throw new \Exception("File not found. (4)", 400);
			}
			header("HTTP/1.0 404 Not Found");
			echo "File not found. (4)";
			return;
		}

		if (isset($DP_CONFIG['filestorage_use_xsendfile']) && $DP_CONFIG['filestorage_use_xsendfile']) {
			header("X-Sendfile: $filepath");
		} else {
			readfile($filepath);
		}
	}


	/**
	 * Send a file that is stored in the database
	 *
	 * @param $blob
	 */
	public function sendFromDatabase($blob)
	{
		$this->sendHeaders($blob);

		$sth = $this->getPdo()->prepare("SELECT data FROM blobs_storage WHERE blob_id = :blob_id ORDER BY id ASC");
		$sth->execute(array('blob_id' => $blob['id']));

		while (($seg = $sth->fetchColumn(0)) !== false) {
			echo $seg;
			flush();
		}

		$sth->closeCursor();
	}


	protected function getSizedBlobSysName($blob_id, $size, $is_fit)
	{
		$sys_name = 'blob-' . $blob_id . '-' . $size;

		if($is_fit) {
			$sys_name .= '-fit';
		}

		$this->addLogMessage("Sized blob sys_name: %s", $sys_name);

		return $sys_name;
	}

	/**
	 * Resize a blob. This needs to load the entire environment.
	 */
	protected function createSizedBlob($blob_info, $size, $is_fit, $die_fail = true)
	{
		$container = $this->bootFullSystem();
		$bs = $container->getBlobStorage();

		$blob = $container->getEm()->find('DeskPRO:Blob', $blob_info['id']);
		$file = $bs->copyBlobRecordToString($blob);

		if (!$file) {
			$this->addLogMessage("Could not load blob file descriptor");

			if ($this->error_mode == 'exception') {
				throw new \Exception("File not found. (no_exist)", 400);
			}
			header("HTTP/1.0 404 Not Found");
			echo "File not found. (no_exist)";
			exit;
		}

		try {
			// Imagine doesnt suppress normal errors, so in addition to exception we'll get errors logged,
			// So @ to get rid of those exceptions
			$image = @$container->getImagine()->load($file);
		} catch (\Imagine\Exception\InvalidArgumentException $e) {
			$this->addLogMessage("Failed to resize: %s", $e->getMessage());
			if ($die_fail) {
				header("HTTP/1.0 500 Internal Server Error");
				echo "Invalid image file. (invalid_image_data)";
				exit;
			}
			return null;
		}

		$width = $image->getSize()->getWidth();
		$height = $image->getSize()->getHeight();

		$m = null;
		if (preg_match('#^(\d+)x(\d+)$#', $size, $m)) {
			$req_w = $m[1];
			$req_h = $m[2];
		} else {
			$req_w = $size;
			$req_h = $size;
		}

		$req_w = (int)$req_w;
		$req_h = (int)$req_h;

		if ($req_w < 1) $req_w = 1;
		if ($req_h < 1) $req_h = 1;

		if ($req_w > 1000) $req_w = 1000;
		if ($req_h > 1000) $req_h = 1000;

		if ($req_h == $req_h) {
			$no_fit = max($width, $height) > $size;
		} else {
			$no_fit = ($width > $req_w) || ($height > $req_h);
		}

		// Only shrink if it doesn't fit inside the box.
		if ($no_fit || $is_fit) {

			// If the image has a w/h of 1, then scaling with
			// fit will result in a dim of 0 when Imagine tries to scale
			if ($width < 2) {
				$is_fit = false;
			}
			if ($height < 2) {
				$is_fit = false;
			}

			if ($is_fit) {
				try {
					$width  = $req_w;
					$height = $req_h;

					$size_box  = new \Imagine\Image\Box($req_w, $req_h);

					$mode      = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
					$resizeimg = $image->thumbnail($size_box, $mode);
					$sizeR     = $resizeimg->getSize();
					$widthR    = $sizeR->getWidth();
					$heightR   = $sizeR->getHeight();

					$preserve  = $container->getImagine()->create($size_box);
					$startX = $startY = 0;
					if ( $widthR < $width ) {
						$startX = ( $width - $widthR ) / 2;
					}
					if ( $heightR < $height ) {
						$startY = ( $height - $heightR ) / 2;
					}
					$preserve->paste($resizeimg, new \Imagine\Image\Point($startX, $startY));
					$image = $preserve;

				// Imagine rounds down, so its possible in certain cases
				// that a 'fit' resize creates a 0 width or height,
				// so just fallback on normal resizing if that happens
				} catch (\Imagine\Exception\InvalidArgumentException $e) {
					$size_w = $size_h = $size;

					if ($height > $width) {
						$size_w = round($size_w * ($width / $height));
					}
					elseif ($width > $height) {
						$size_h = round($size_h * ($height / $width));
					}

					if ($size_w == 0) {
						$size_w = 1;
					}

					if ($size_h == 0) {
						$size_h = 1;
					}

					$box = new \Imagine\Image\Box($size_w, $size_h);
					$image->resize($box);
				}
			} else {
				$size_w = $req_w;
				$size_h = $req_h;

				if ($height > $width) {
					$size_w = round($size_w * ($width / $height));
				}
				elseif ($width > $height) {
					$size_h = round($size_h * ($height / $width));
				}

				if ($size_w == 0) {
					$size_w = 1;
				}

				if ($size_h == 0) {
					$size_h = 1;
				}

				$box = new \Imagine\Image\Box($size_w, $size_h);
				$image->resize($box);
			}
		}

		try {
			$file = $image->get($blob->getImageType());

		// Workaround for potential bug in some Windows servers
		// where the GD handler tries to save a temp file and the default
		// temp dir is not writable.
		} catch (\Imagine\Exception\RuntimeException $e) {
			$tmp = dp_get_tmp_dir() . DIRECTORY_SEPARATOR . uniqid('img', true) . '.' . Strings::getExtension($blob->filename);
			$image->save($tmp);
			$file = file_get_contents($tmp);
			@unlink($tmp);
		}

		$new_blob = $bs->createBlobRecordFromString($file, $blob->filename, $blob->content_type, array(
			'sys_name'       => $this->getSizedBlobSysName($blob->id, $size, $is_fit),
			'original_blob'  => $blob
		));

		$this->addLogMessage("Cached resize as blob %d", $new_blob->getId());

		$new_blob_info = $new_blob->toArray(DomainObject::TOARRAY_ONLY_PRIMATIVES);
		$new_blob_info['filename_safe'] = $blob->getFilenameSafe();

		return $new_blob_info;
	}
}

$file_loader = new FilestorageLoader();
$file_loader->run();