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

namespace Application\DeskPRO\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Symfony\Component\Finder\Finder;

use Orb\Util\Util;
use Orb\Util\Strings;
use Orb\Util\Arrays;

class Deskpro3RedirectController extends AbstractController
{
	############################################################################
	# downloads
	############################################################################

	/**
	 * files.php
	 * files.php?id=123
	 */
	public function downloadCatAction()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		if ($id) {
			$new_id = $this->getNewId('dp3_file_cat_'.$id);
			if ($new_id) {
				$obj = $this->em->find('DeskPRO:DownloadCategory', $new_id);
				if ($obj) {
					return $this->redirectRoute('user_downloads', array('slug' => $obj->getUrlSlug()), 301);
				}
			}
		}

		return $this->redirectRoute('user_downloads_home', array(), 301);
	}

	/**
	 * attachment_files.php?id=123
	 */
	public function downloadViewAction()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		if ($id) {
			$new_id = $this->getNewId('dp3_filescat_'.$id);
			if ($new_id) {
				$obj = $this->em->find('DeskPRO:Download', $new_id);
				if ($obj) {
					return $this->redirectRoute('user_downloads_file', array('slug' => $obj->getUrlSlug()), 301);
				}
			}
		}

		return $this->redirectRoute('user_downloads_home', array(), 301);
	}

	############################################################################
	# Feedback
	############################################################################

	/**
	 * ideas.php
	 * ideas.php?cat=123
	 * ideas.php?123-some-idea
	 */
	public function feedbackAction()
	{
		$cat_id = isset($_GET['cat']) ? $_GET['cat'] : 0;
		$idea_str = Arrays::getFirstKey($_GET);

		if ($cat_id) {
			// Ignore (go to home)
			// We dont filter on cats anymore
		} elseif ($idea_str) {
			$id = Strings::extractRegexMatch('#^([0-9]+)#', $idea_str);
			$new_id = $this->getNewId('dp3_ideaid_'.$id);
			if ($new_id) {
				$obj = $this->em->find('DeskPRO:Feedback', $new_id);
				if ($obj) {
					return $this->redirectRoute('user_feedback_view', array('slug' => $obj->getUrlSlug()), 301);
				}
			}
		}

		return $this->redirectRoute('user_feedback', array(), 301);
	}


	############################################################################
	# Articles
	############################################################################

	/**
	 * kb_article.php?ref=1790-TMRE-3093
	 */
	public function articleViewAction()
	{
		$id = isset($_GET['ref']) ? $_GET['ref'] : 0;

		if ($id) {
			$new_id = $this->getNewId('dp3_kbref_'.$id);
			if ($new_id) {
				$obj = $this->em->find('DeskPRO:Article', $new_id);
				if ($obj) {
					return $this->redirectRoute('user_articles_article', array('slug' => $obj->getUrlSlug()), 301);
				}
			}
		}

		return $this->redirectRoute('user_articles_home', array(), 301);
	}

	/**
	 * kb_cat.php?id=1
	 */
	public function articleCatAction()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		if ($id) {
			$new_id = $this->getNewId('dp3_kbcatid_'.$id);
			if ($new_id) {
				$obj = $this->em->find('DeskPRO:ArticleCategory', $new_id);
				if ($obj) {
					return $this->redirectRoute('user_articles', array('slug' => $obj->getUrlSlug()), 301);
				}
			}
		}

		return $this->redirectRoute('user_articles_home', array(), 301);
	}

	/**
	 * kb.php
	 */
	public function articlesHomeAction()
	{
		return $this->redirectRoute('user_articles_home', array(), 301);
	}

	############################################################################
	# News
	############################################################################

	/**
	 * news.php?id=2
	 * news_full.php?id=2
	 */
	public function newsViewAction()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		if ($id) {
			$new_id = $this->getNewId('dp3_newsid_'.$id);
			if ($new_id) {
				$obj = $this->em->find('DeskPRO:News', $new_id);
				if ($obj) {
					return $this->redirectRoute('user_news_view', array('slug' => $obj->getUrlSlug()), 301);
				}
			}
		}

		return $this->redirectRoute('user_news_home', array(), 301);
	}

	/**
	 * news_archive.php
	 */
	public function newsArchiveAction()
	{
		return $this->redirectRoute('user_news_home', array(), 301);
	}

	############################################################################
	# Tickets
	############################################################################

	/**
	 * newticket.php
	 */
	public function newTicketAction()
	{
		return $this->redirectRoute('user_tickets_new', array(), 301);
	}

	/**
	 * ticketlist.php
	 * ticketlist_company.php
	 * ticketlist_participate.php
	 */
	public function ticketListAction()
	{
		return $this->redirectRoute('user_tickets', array(), 301);
	}

	/**
	 * view.php?ticketref=6630-QVNM-6486
	 */
	public function ticketViewAction()
	{
		$id = isset($_GET['ticketref']) ? $_GET['ticketref'] : 0;

		if ($id) {
			$new_id = $this->getNewId('dp3_ticketref_'.$id);
			if ($new_id) {
				$new_id = $new_id['new_id'];
				$obj = $this->em->find('DeskPRO:Ticket', $new_id);
				if ($obj) {
					return $this->redirectRoute('user_tickets_view', array('ticket_ref' => $obj->getRef()), 301);
				}
			}
		}

		return $this->redirectRoute('user_tickets', array(), 301);
	}

	############################################################################
	# Login, reg and profiles
	############################################################################

	/**
	 * login.php
	 */
	public function loginAction()
	{
		return $this->redirectRoute('user_login', array(), 301);
	}

	/**
	 * register.php
	 */
	public function registerAction()
	{
		return $this->redirectRoute('user_register', array(), 301);
	}

	/**
	 * profile_email.php
	 * profile_password.php
	 * profile.php
	 */
	public function profileAction()
	{
		return $this->redirectRoute('user_profile', array(), 301);
	}

	############################################################################
	# Unsupported : Manuals and Troubles
	############################################################################

	public function rewrittenManualsAction($manual_bit = '', $page_bit = '')
	{
		$manual_id = Strings::extractRegexMatch('#^(\d+)#', $manual_bit);
		$page_id   = Strings::extractRegexMatch('#^(\d+)#', $page_bit);

		if (!$manual_id && !$page_id) {
			return $this->redirectRoute('user', array(), 301);
		}

		if ($page_id) {
			return $this->redirectRoute('dp3_redirect_manual_php', array('m' => $manual_id, 'p' => $page_id));
		} else {
			return $this->redirectRoute('dp3_redirect_manual_php', array('m' => $manual_id));
		}
	}


	/**
	 * manual.php
	 * manual.php?m=2
	 * manual.php?m=2
	 * manual.php?p=49
	 * manual_download.php?m=2&do=single
	 * manual_download.php?m=2&do=zip
	 */
	public function manualsAction()
	{
		$data_dir = dp_get_data_dir() . '/manuals';
		if (!is_dir($data_dir)) {
			return $this->redirectRoute('user', array(), 301);
		}

		if (!isset($_GET['m']) && !isset($_GET['p']) && is_file($data_dir . '/index.html')) {
			$html = file_get_contents($data_dir . '/index.html');
			return $this->createResponse($html);
		}

		$manual_dir = null;
		$manual_id  = 0;
		$index_data = array();

		if (isset($_GET['m'])) {
			$manual_dir = $data_dir . '/manual' . (int)$_GET['m'];
			if (is_file($manual_dir.'/index-data.php')) {
				$index_data = include($manual_dir.'/index-data.php');
				$manual_id = $_GET['m'];
			} else {
				$manual_dir = null;
			}
		} elseif (isset($_GET['p'])) {
			$dir = dir($data_dir);

			while (($f = $dir->read()) !== false) {
				if ($f == '.' || $f == '..') continue;

				$path = $data_dir . '/' . $f;
				if (is_dir($path) && is_file($path.'/index-data.php')) {
					$index_data = include($path.'/index-data.php');
					if (isset($index_data[$_GET['p']])) {
						$manual_dir = $path;
						$manual_id = str_replace('manual', '', $f);
						break;
					}
				}
			}
		}

		if (!$manual_dir) {
			return $this->redirectRoute('user', array(), 301);
		}

		if (isset($_GET['img'])) {
			$file_path = realpath($manual_dir . '/images/' . $_GET['img']);
			if (strpos($file_path, $manual_dir) !== 0 || !is_file($file_path)) {
				return $this->redirectRoute('user', array(), 301);
			}

			$file = file_get_contents($file_path);
			$mimetype = \Orb\Data\ContentTypes::getContentTypeFromFilename($_GET['img']);

			$res = new \Symfony\Component\HttpFoundation\Response($file, 200, array(
				'Content-Type' => $mimetype,
				'Content-Disposition' => 'inline; filename=' . $_GET['img'],
			));

			return $res;
		}

		if (!isset($_GET['p'])) {
			$html = file_get_contents($manual_dir . '/index.html');
		} else {
			if (!isset($index_data[$_GET['p']])) {
				return $this->redirectRoute('dp3_redirect_manual_php', array('m' => $manual_id));
			}
			$page_file = $manual_dir . '/pages/' . $index_data[$_GET['p']];
			$html = file_get_contents($page_file);
		}

		foreach ($index_data as $pid => $page) {
			$html = str_replace('pages/'.$page, 'manual.php?m='.$manual_id.'&p=' . $pid, $html);
		}

		$html = preg_replace('#../images/(.*?)\b#', 'manual.php?m='.$manual_id.'&img=$1', $html);

		if (dp_get_config('legacy_manual_custom_header')) {
			$header_markup = file_get_contents(dp_get_config('legacy_manual_custom_header'));
			$html = str_replace('<body>', $header_markup, $html);
		}

		return $this->createResponse($html);
	}


	/**
	 * troubleshooter.php
	 * troubleshooter.php?id=1
	 */
	public function troublesAction()
	{
		return $this->redirectRoute('user', array(), 301);
	}

	############################################################################

	/**
	 * @param string $lookup_id
	 * @return int
	 */
	public function getNewId($lookup_id)
	{
		$data = $this->db->fetchColumn("SELECT data FROM import_datastore WHERE typename = ?", array($lookup_id));

		if (preg_match('#^a:[0-9]+:\{#', $data)) {
			$data = unserialize($data);
		}

		return $data;
	}
}