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

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;

class PortalController extends AbstractController
{
	############################################################################
	# Portal
	############################################################################

    public function indexAction()
	{
		$default_portal_style = $this->container->get('deskpro.core.settings')->getDefaultGroup('user_style');

		$portal_path = '';
		if ($this->in->getString('portal_path')) {
			$portal_path = ltrim($this->in->getString('portal_path'), '/');
		}

		$viewing_page = null;
		if (strpos($portal_path, 'new-ticket') !== false) {
			$viewing_page = 'new-ticket';
		}

		return $this->render('AdminBundle:Portal:index.html.twig', array(
			'default_portal_style' => $default_portal_style,
			'portal_path'          => $portal_path,
			'viewing_page'         => $viewing_page,
		));
	}

	public function uploadFaviconAction()
	{
		if ($this->request->isPost()) {
			if ($blob_auth = $this->in->getString('new_blob_auth_id')) {
				$orig_blob = $this->em->getRepository('DeskPRO:Blob')->getByAuthId($blob_auth);

				if (!$orig_blob) {
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
				}

				$ext = strtolower(\Orb\Util\Strings::getExtension($orig_blob->getFilename()));
				if (!$ext || !in_array($ext, array('gif', 'png', 'jpg', 'jpeg', 'ico'))) {
					throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Please upload a valid image");
				}

				$file = $this->container->getBlobStorage()->copyBlobRecordToString($orig_blob);

				if ($orig_blob->content_type != 'image/x-icon' && $orig_blob->content_type != 'image/vnd.microsoft.icon') {
					if (class_exists('Imagick')) {
						$im = new \Imagick();
						try {
							$im->readimageblob($file, $orig_blob->getFilename());
						} catch (\Exception $e) {
							throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Please upload a valid image");
						}
						$im->scaleImage(16, 16, true);
						$im->setImageFormat('ico');
						$file_content = $im->getImageBlob();
					} else {
						$gd = @imagecreatefromstring($file);
						if (!$gd) {
							throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Please upload a valid image");
						}
						$width = imagesx($gd);
						$height = imagesy($gd);

						$gd_dest = imagecreatetruecolor(16, 16);
						imagecopyresampled($gd_dest, $gd, 0, 0, 0, 0, 16, 16, $width, $height);

						$file_content = \phpthumb_ico::GD2ICOstring(array($gd_dest));
					}
				} else {
					$file_content = $file;
				}

				$blob = $this->container->getBlobStorage()->createBlobRecordFromString(
					$file_content,
					'favicon.ico',
					'image/x-icon'
				);
				$blob_id = $blob->getId();

				$url = trim($blob->getDownloadUrl(true, false), '/');

				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.favicon_blob_id', $blob_id);
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.favicon_blob_url', $url);
			} else {
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.favicon_blob_id', null);
				$this->em->getRepository('DeskPRO:Setting')->updateSetting('core.favicon_blob_url', null);
			}

			$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
			$cache->invalidateAll();

			return $this->redirectRoute('admin_portal_uploadfavicon');
		}

		return $this->render('AdminBundle:Portal:change-favicon.html.twig');
	}

	public function getEditorAction($type)
	{
		switch ($type) {
			case 'logo':
				return $this->render('AdminBundle:Portal:portal-editor-logo.html.twig');
				break;

			case 'portal-title':
				return $this->render('AdminBundle:Portal:portal-title-editor.html.twig');
				break;

			case 'twitter-sidebar':
				$twitter = $this->em->getRepository('DeskPRO:PortalPageDisplay')->findOneByType('twitter');
				if ($twitter) {
					$data = $twitter->data;
				} else {
					$data = array();
				}

				if (!empty($data['token']) && !empty($data['secret'])) {
					$api = \Application\DeskPRO\Service\Twitter::getUserTwitterApi($data['token'], $data['secret']);
					$oauth_ok = false;
					try {
						$res = $api->get_accountVerify_credentials();
						if (!empty($res->id_str)) {
							$oauth_ok = true;
						}
					} catch (\Exception $e) {}

					if (!$oauth_ok) {
						$data['token'] = false;
						$data['secret'] = false;
					}
				}

				return $this->render('AdminBundle:Portal:twitter-sidebar-editor.html.twig', array(
					'data' => $data,
					'consumer_key' => \Application\DeskPRO\Service\Twitter::getUserConsumerKey()
				));
				break;
		}

		throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
	}

	public function saveEditorAction($type)
	{
		switch ($type) {
			case 'css_var':
				$css_vars = $this->in->getCleanValueArray('vars', 'string', 'string');
				foreach ($css_vars as $name => $value) {
					$setting_name = 'user_style.' . $name;
					$this->container->getSettingsHandler()->setSetting($setting_name, $value);
				}

				\Application\DeskPRO\Style\RefreshStylesheets::refresh($this->container);

				break;

			case 'header_title':
				$this->container->getEm()->getRepository('DeskPRO:Setting')->updateSetting('core.deskpro_logo_blob', null);
				$this->container->getEm()->getRepository('DeskPRO:Setting')->updateSetting('user.portal_header', $this->in->getString('title'));
				$this->container->getEm()->getRepository('DeskPRO:Setting')->updateSetting('user.portal_tagline', $this->in->getString('tagline'));
				$this->container->getSettingsHandler()->setSetting('user.portal_simpleheader', 1);
				break;

			case 'header_logo':
				$blob = $this->container->getEm()->getRepository('DeskPRO:Blob')->getByAuthId($this->in->getString('blob_authid'));
				if ($blob) {
					$this->container->getEm()->getRepository('DeskPRO:Setting')->updateSetting('core.deskpro_logo_blob', $blob->id);
					$this->container->getSettingsHandler()->setSetting('user.portal_simpleheader', 1);
				}
				break;

			case 'disable_logo_area':
				$this->container->getSettingsHandler()->setSetting('user.portal_simpleheader', null);
				break;

			case 'enable_logo_area':
				$this->container->getSettingsHandler()->setSetting('user.portal_simpleheader', 1);
				break;

			case 'portal_title':
				$this->container->getSettingsHandler()->setSetting('user.portal_title', $this->in->getString('title'));
				break;

			case 'twitter_sidebar':
				$twitter = $this->em->getRepository('DeskPRO:PortalPageDisplay')->findOneByType('twitter');
				if ($twitter) {
					$twitter->addData('twitter_name', $this->in->getString('twitter_name'));
					$twitter->addData('max_items', $this->in->getUint('max_items'));

					$this->em->beginTransaction();
					$this->em->persist($twitter);
					$twitter->deleteCachedPages();
					$this->em->flush();
					$this->em->commit();
				}
				break;

			case 'toggle_tab':
				if ($this->in->getBool('on')) {
					$val = 1;
				} else {
					$val = 0;
				}

				$this->container->getSettingsHandler()->setSetting('user.portal_tab_' . $this->in->getStrSimple('tab'), $val);

				break;

			case 'reorder_tabs':
				$order = $this->container->getIn()->getCleanValueArray('display_order', 'str_simple', 'discard');
				$this->container->getSettingsHandler()->setSetting('user.portal_tabs_order', implode(',', $order));
				break;
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->createJsonResponse(array('success' => true));
	}

	public function twitterOauthAction()
	{
		$twitter = $this->em->getRepository('DeskPRO:PortalPageDisplay')->findOneByType('twitter');
		if ($twitter) {
			if (!\Application\DeskPRO\Service\Twitter::getUserConsumerKey()) {
				return $this->redirectRoute('admin_twitter_apps');
			}

			$api = \Application\DeskPRO\Service\Twitter::getUserTwitterApi();

			if ($this->in->getBool('start')) {
				$api->setCallback($this->generateUrl('admin_portal_twitter_oauth', array(), true));
				return $this->redirect($api->getAuthenticateUrl());
			}

			try {
				$api->setToken($this->in->getString('oauth_token'));
				$access = $api->getAccessToken();
				if ($access->oauth_token && $access->oauth_token_secret) {
					$twitter->addData('token', $access->oauth_token);
					$twitter->addData('secret', $access->oauth_token_secret);

					$this->em->beginTransaction();
					$this->em->persist($twitter);
					$twitter->deleteCachedPages();
					$this->em->flush();
					$this->em->commit();
				}
			} catch (\EpiOAuthException $e) {}
		}

		return $this->redirectRoute('admin_portal');
	}

	public function deleteCustomBlockSimpleAction($pid = 0)
	{
		$pd = $this->em->getRepository('DeskPRO:PortalPageDisplay')->find($pid);
		if (!$pd || $pd->type != 'sidebar_block_simple') {
			throw $this->createNotFoundException();
		}

		$this->em->remove($pd);
		$this->em->flush();

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->createJsonResponse(array(
			'success' => true,
			'pid'     => $pd->getId()
		));
	}

	public function saveCustomBlockSimpleAction($pid = 0)
	{
		if ($pid) {
			$pd = $this->em->getRepository('DeskPRO:PortalPageDisplay')->find($pid);
			if (!$pd || $pd->type != 'sidebar_block_simple') {
				throw $this->createNotFoundException();
			}
		} else {
			$pd = new \Application\DeskPRO\Entity\PortalPageDisplay();
		}

		$pd->type = 'sidebar_block_simple';
		$pd->data = array(
			'title'   => $this->in->getString('title'),
			'content' => $this->in->getString('content')
		);
		$pd->is_enabled = true;
		$pd->section = 'sidebar';

		$this->em->persist($pd);
		$this->em->flush();

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->createJsonResponse(array(
			'success' => true,
			'pid'     => $pd->getId()
		));
	}

	public function getCustomBlockSimpleAction($pid)
	{
		$pd = $this->em->getRepository('DeskPRO:PortalPageDisplay')->find($pid);
		if (!$pd || $pd->type != 'sidebar_block_simple') {
			throw $this->createNotFoundException();
		}

		$data = $pd->data;

		return $this->createJsonResponse(array(
			'pid'      => $pid,
			'title'    => isset($data['title'])   ? $data['title'] : '',
			'content'  => isset($data['content']) ? $data['content'] : '',
		));
	}

	public function togglePortalAction()
	{
		$enable = $this->in->getBoolInt('enable');
		$this->em->getRepository('DeskPRO:Setting')->updateSetting('user.portal_enabled', $enable);

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->redirectRoute('admin_portal');
	}

	public function updateBlockOrdersAction()
	{
		$helper = new \Application\AdminBundle\Controller\Helper\DisplayOrderUpdate($this);
		$result =  $helper->doUpdate('portal_page_display');

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $result;
	}

	public function blockToggleAction($pid)
	{
		$pd = $this->em->find('DeskPRO:PortalPageDisplay', $pid);
		if (!$pd) {
			throw $this->createNotFoundException();
		}

		$pd->is_enabled = $this->in->getBool('enabled');

		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($pd);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->createJsonResponse(array('success'=>1));
	}

	public function deleteTemplateBlockAction($pid)
	{
		$pd = $this->em->find('DeskPRO:PortalPageDisplay', $pid);
		if (!$pd || $pd->type != 'template') {
			throw $this->createNotFoundException();
		}

		$tpl = $pd->data['tpl'];
		$this->db->delete('templates', array('name' => $tpl));

		$this->em->getConnection()->beginTransaction();
		try {
			$this->db->delete('templates', array('name' => $tpl));
			$this->em->remove($pd);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->createJsonResponse(array('success'=>1));
	}

	############################################################################
	# Widget
	############################################################################

	public function widgetsAction()
	{
		$articles  = App::getDb()->fetchAllKeyValue("SELECT id, title FROM articles WHERE status = 'published'");
		$downloads = App::getDb()->fetchAllKeyValue("SELECT id, title FROM downloads WHERE status = 'published'");
		$news      = App::getDb()->fetchAllKeyValue("SELECT id, title FROM news WHERE status = 'published'");

		$article_cat_map   = App::getDb()->fetchAllGrouped("SELECT category_id, article_id FROM article_to_categories", array(), 'category_id', null, 'article_id');
		$download_cat_map  = App::getDb()->fetchAllGrouped("SELECT category_id, id FROM downloads", array(), 'category_id', null, 'id');
		$news_cat_map      = App::getDb()->fetchAllGrouped("SELECT category_id, id FROM news", array(), 'category_id', null, 'id');

		if ($this->in->getBool('save_selections')) {
			$set_selections = $this->in->getCleanValueArray('selections', 'raw', 'raw');
			$ds = App::getEntityRepository('DeskPRO:DataStore')->getByName('portal_widget_default_links', true);
			$ds->setData('selections', $set_selections);

			$this->em->persist($ds);
			$this->em->flush();

			$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
			$cache->invalidateAll();
		}

		$selections = App::getEntityRepository('DeskPRO:DataStore')->getByName('portal_widget_default_links');
		if ($selections) {
			$selections = $selections->getData('selections');
		} else {
			$selections = array();
		}

		$department = null;
		if ($dep_id = $this->in->getUint('department_id')) {
			$department = $this->em->find('DeskPRO:Department', $dep_id);
		}

		$widget_url = $this->container->getSetting('core.deskpro_url');
		if (defined('DPC_SITE_DOMAIN')) {
			$widget_url = 'http://' . DPC_SITE_DOMAIN . '/';
		}

		$chat_online = false;
		if (file_exists(dp_get_data_dir() . '/chat_is_available.trigger')) {
			$chat_online = file_get_contents(dp_get_data_dir() . '/chat_is_available.trigger');
			$chat_online = (bool)$chat_online;
		}

		return $this->render('AdminBundle:Portal:website-widgets.html.twig', array(
			'articles'    => $articles,
			'downloads'   => $downloads,
			'news'        => $news,
			'chat_online' => $chat_online,

			'selections' => $selections,
			'department' => $department,

			'widget_url' => $widget_url,

			'article_cat_map'   => $article_cat_map,
			'download_cat_map'  => $download_cat_map,
			'news_cat_map'      => $news_cat_map
		));
	}
}
