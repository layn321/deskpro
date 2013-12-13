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
 * @subpackage
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\ResourceScanner\TemplateFiles;
use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Template;

class TemplatesController extends AbstractController
{
	####################################################################################################################
	# user-list
	####################################################################################################################

	/**
	 * Lists all user-related templates
	 */
	public function userListAction()
	{
		$tplfiles = new TemplateFiles();
		$map = $tplfiles->getUserTemplates();

		$custom_templates = $this->container->getSystemService('style')->getCustomTemplateInfo();

		$list = $this->groupMap($map, $custom_templates);

		// Put the DeskPRO ones last
        if(isset($list['DeskPRO'])) {
            $tmp = $list['DeskPRO'];
            unset($list['DeskPRO']);
            $list['DeskPRO'] = $tmp;
        }

		// Uset custom ones that we coded manually at the top of the page
		unset(
			$list['UserBundle']['!top']['templates']['UserBundle::custom-footer.html.twig'],
			$list['UserBundle']['!top']['templates']['UserBundle::custom-header.html.twig'],
			$list['UserBundle']['!top']['templates']['UserBundle::custom-headinclude.html.twig'],
			$list['UserBundle']['Css']['templates']['UserBundle:Css:custom.css.twig'],
			$list['UserBundle']['_form_fields']
		);

		return $this->render('AdminBundle:Templates:user-templates.html.twig', array(
			'list' => $list,
			'custom_templates' => $custom_templates,
			'open_template' => $this->in->getString('open')
		));
	}


	####################################################################################################################
	# other-list
	####################################################################################################################

	/**
	 * Lists agent and admin templates
	 */
	public function otherListAction()
	{
		$tplfiles = new TemplateFiles();
		$map = $tplfiles->getOtherTemplates();

		$custom_templates = $this->container->getSystemService('style')->getCustomTemplateInfo();

		$list = $this->groupMap($map, $custom_templates);

		// Put the DeskPRO ones last
		$tmp = $list['DeskPRO'];
		unset($list['DeskPRO']);
		$list['DeskPRO'] = $tmp;

		return $this->render('AdminBundle:Templates:other-templates.html.twig', array(
			'list' => $list,
			'custom_templates' => $custom_templates,
		));
	}


	####################################################################################################################
	# get-template-code
	####################################################################################################################

	/**
	 * Gets the raw template code for template. This will be either the the current modified template, or the
	 * pristine template from the filesystem. This is used to populate the textarea in the editor.
	 */
	public function getTemplateCodeAction()
	{
		$name = $this->in->getString('name');

		if ($pid = \Orb\Util\Strings::extractRegexMatch('#^EDIT_SIDEBAR_BLOCK:(.*?)$#', $name)) {
			$page_display = $this->em->find('DeskPRO:PortalPageDisplay', $pid);
			if (!$page_display || $page_display->type != 'template') {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
			}

			$name = $page_display->data['tpl'];
		}

		$code = App::getTemplating()->getSource($name);

		// Show default welcome block code
		if (!$code && $name == 'UserBundle:Portal:welcome-block.html.twig') {
			$code = App::getTemplating()->getDefaultSource($name);
		}

		if ($this->in->getBool('info')) {

			$default_code = App::getTemplating()->getDefaultSource($name);

			return $this->createJsonResponse(array(
				'custom' => App::getTemplating()->isCustomTemplate($name),
				'code' => $code,
				'default_code' => $default_code,
			));
		}

		return $this->createResponse($code);
	}


	####################################################################################################################
	# revert-template
	####################################################################################################################

	/**
	 * Reverts a template back to default by deleting the database copy.
	 */
	public function revertTemplateAction()
	{
		$name = $this->in->getString('name');
		$part = $this->in->getString('part');

		$template = $this->em->getRepository('DeskPRO:Template')->findOneBy(array('name' => $name));

		$revert_part = function($my_code, $default_code) use ($part) {
			if (!$part) {
				return $default_code;
			}

			$my_subj_code = '';
			$my_body_code = $my_code;
			$default_subj_code = '';
			$default_body_code = $default_code;

			$has_subj = strpos($default_code, '<dp:subject>') !== false;
			if ($has_subj) {
				if (preg_match('#<dp:subject>(.*?)</dp:subject>#s', $my_code, $m)) {
					$my_subj_code = trim($m[1]);
					$my_body_code = trim(str_replace($m[0], '', $my_code));
				}

				if (preg_match('#<dp:subject>(.*?)</dp:subject>#s', $default_code, $m)) {
					$default_subj_code = trim($m[1]);
					$default_body_code = trim(str_replace($m[0], '', $default_code));
				}
			}

			$set_subj_code = $default_subj_code;
			$set_body_code = $default_body_code;

			if ($part == 'subject') {
				// Only reset subject, so keep body
				$set_body_code = $my_body_code;
			} elseif ($part == 'body') {
				// Only reset body, so keep subj
				$set_subj_code = $my_subj_code;
			}

			if ($has_subj) {
				$set_code = '<dp:subject>' . $set_subj_code . '</dp:subject>' . $set_body_code;
			} else {
				$set_code = $set_body_code;
			}

			return $set_code;
		};

		if ($template && $template->variant_of) {
			$code = $revert_part(
				$template->template_code,
				App::getTemplating()->getSource($template->variant_of)
			);

			$twig = $this->container->get('twig');

			$proc = new \Application\DeskPRO\Twig\PreProcessor\EmailPreProcessor();
			$compile_code = $proc->process($code, $template->variant_of);

			$compiled = $twig->compileSource($this->_preProcessCustomTemplate($compile_code), $template->variant_of);

			$template->setTemplate($code, $compiled);
			$this->em->persist($template);
			$this->em->flush();
		} else {

			if (!$part) {
				$this->db->delete('templates', array('name' => $name));
			} else {
				$code = $revert_part(
					$template->template_code,
					App::getTemplating()->getDefaultSource($template->name)
				);

				$proc = new \Application\DeskPRO\Twig\PreProcessor\EmailPreProcessor();
				$compile_code = $proc->process($code, $template->name);

				$twig = $this->container->get('twig');
				$compiled = $twig->compileSource($this->_preProcessCustomTemplate($compile_code), $template->name);

				$template->setTemplate($code, $compiled);
				$this->em->persist($template);
				$this->em->flush();
			}
		}

		// Revert means blank in this thec ase of the welcome block
		if ($name == 'UserBundle:Portal:welcome-block.html.twig') {

			if (!$template) {
				$template = new Template();
				$template->style = $this->container->getSystemService('style');
				$template->name = $name;
			}

			$twig = $this->container->get('twig');
			$compiled = $twig->compileSource('', $name);

			$template->setTemplate('', $compiled);
			$this->em->persist($template);
			$this->em->flush();
		}


		if ($name == 'UserBundle:Css:main.css.twig' || $name == 'UserBundle:Css:custom.css.twig') {
			\Application\DeskPRO\Style\RefreshStylesheets::refresh($this->container);
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		$code = App::getTemplating()->getSource($name);

		return $this->createJsonResponse(array('success' => true, 'name' => $name, 'code' => $code));
	}


	####################################################################################################################
	# save-template
	####################################################################################################################

	/**
	 * Saves a template to the database. This involves compiling the template as well.
	 */
	public function saveTemplateAction()
	{
		$name = $this->in->getString('name');

		$block = null;
		if ($name == 'UserBundle:Portal:new-sidebar-block.html.twig') {
			$name = 'DeskPRO:CustomBlocks:Sidebar_' . mt_rand(1000,9999) . '_' . time() . '.html.twig';
			$block = new \Application\DeskPRO\Entity\PortalPageDisplay();
			$block->type = 'template';
			$block->data = array('tpl' => $name);
			$block->is_enabled = true;
			$block->section = 'sidebar';
		} elseif ($pid = \Orb\Util\Strings::extractRegexMatch('#^EDIT_SIDEBAR_BLOCK:(.*?)$#', $name)) {
			$page_display = $this->em->find('DeskPRO:PortalPageDisplay', $pid);
			if (!$page_display || $page_display->type != 'template') {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
			}

			$name = $page_display->data['tpl'];
		}

		$old_template_variant = App::getDb()->fetchColumn("
			SELECT variant_of
			FROM templates
			WHERE name = ?
			LIMIT 1
		", array($name));
		$this->db->delete('templates', array('name' => $name));

		$code = $this->in->getRaw('code');

		if ($name == 'UserBundle:Portal:welcome-block.html.twig') {
			// Fix common mistake of removing </article>
			if (stripos($code, '<article') !== false && stripos($code, '</article>') === false) {
				$code .= "\n</article>";
			}
		}

		try {
			/** @var $twig \Application\DeskPRO\Twig\Environment */
			$twig = $this->container->get('twig');

			$compile_code = $code;

			if (strpos($name, 'DeskPRO:emails_') !== false || strpos($name, 'DeskPRO:custom_emails_') !== false) {
				if (strpos($code, '{% extends') !== false) {
					$code = trim(preg_replace('#<dp:subject>\s*</dp:subject>#is', '', $code));
				}
				$proc = new \Application\DeskPRO\Twig\PreProcessor\EmailPreProcessor();
				$compile_code = $proc->process($compile_code, $name);
			}

			$compile_code = $this->_preProcessCustomTemplate($compile_code);

			$compiled = $twig->compileSource($compile_code, $name);
		} catch (\Twig_Error_Syntax $e) {
			return $this->createJsonResponse(array(
				'error' => true,
				'error_syntax' => true,
				'error_code' => $e->getCode(),
				'error_message' => $e->getMessage(),
				'error_line' => $e->getTemplateLine(),
				'source' => $code
			));
		} catch (\Twig_Error $e) {
			return $this->createJsonResponse(array(
				'error' => true,
				'error_code' => $e->getCode(),
				'error_message' => $e->getMessage(),
				'source' => $code
			));
		}

		if ($name == 'UserBundle::layout.html.twig' && !\DeskPRO\Kernel\License::getLicense()->isCopyfree()) {
			$code_test = strip_tags($code);
			if (!preg_match('#\{\{\s*dp_copyright\(\)\s*\}\}#', $code_test)) {
				return $this->createJsonResponse(array(
					'error' => true,
					'error_code' => 'missing_copyright',
					'error_message' => 'You cannot remove the DeskPRO copyright without purchasing copyright removal.',
					'error_line' => 'N/A',
				));
			}
		}

		$template = new Template();
		$template->style = $this->container->getSystemService('style');
		$template->name = $name;
		$template->setTemplate($code, $compiled);
		if ($old_template_variant) {
			$template->variant_of = $old_template_variant;
		}

		$ret_data = array(
			'success' => true,
			'name' => $name,
		);

		$this->db->beginTransaction();
		try {
			$this->em->persist($template);
			if ($block) {
				$this->em->persist($block);
			}

			$this->em->flush();
			$this->db->commit();

			if ($block) {
				$ret_data['pid'] = $block->getId();
			}
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		if ($name == 'UserBundle:Css:main.css.twig' || $name == 'UserBundle:Css:custom.css.twig') {
			\Application\DeskPRO\Style\RefreshStylesheets::refresh($this->container);
		}

		$cache = new \Application\DeskPRO\CacheInvalidator\UserPageCache();
		$cache->invalidateAll();

		return $this->createJsonResponse($ret_data);
	}

	####################################################################################################################

	/**
	 * Groups a template map into bundles/dirs for use in a template
	 *
	 * @param array $map
	 * @param array $custom_templates
	 * @return array
	 */
	protected function groupMap(array $map, array $custom_templates)
	{
		$grouped = array();

		foreach ($map as $k => $v) {
			preg_match('#^(.*?):(.*?):(.*?)$#', $k, $m);
			$bundle = $m[1];
			$dir = $m[2];
			if (!$dir) {
				$dir = '!top';
			}

			if (!isset($grouped[$bundle])) $grouped[$bundle] = array();
			if (!isset($grouped[$bundle][$dir])) $grouped[$bundle][$dir] = array('count_changed' => 0, 'count_outdated' => 0, 'templates' => array());

			$v['name'] = $k;
			$v['shortname'] = str_replace('.twig', '', $m[3]);

			if (isset($custom_templates[$k])) {
				$v['is_custom'] = true;
				$grouped[$bundle][$dir]['count_changed']++;

				$time = strtotime($custom_templates[$k]['date_updated']);
				if ($time < $v['last_updated']) {
					$grouped[$bundle][$dir]['count_outdated']++;
					$v['is_outdated'] = true;
				}
			}

			$grouped[$bundle][$dir]['templates'][$k] = $v;
		}

		ksort($grouped, \SORT_STRING);

		foreach ($grouped as &$bundle_dirs) {
			ksort($bundle_dirs, \SORT_STRING);
		}

		return $grouped;
	}

	####################################################################################################################
	# create-template
	####################################################################################################################

	public function createTemplateAction()
	{
		$name = $this->in->getString('name');

		if (!preg_match('#^([A-Za-z0-9]+):([A-Za-z0-9_]+):([A-Za-z0-9_\-\.]+)$#', $name)) {
			return $this->createJsonResponse(array('error' => true, 'error_message' => 'Invalid template name'));
		}

		$name = $this->in->getString('name');
		$this->db->delete('templates', array('name' => $name));

		$copy_tpl = $this->in->getString('copy_tpl');
		$code = '';
		if ($copy_tpl) {
			$tplfiles = new TemplateFiles();
			$map = $tplfiles->getTemplateMap();

			$code = $this->db->fetchColumn("SELECT template_code FROM templates WHERE name = ?", array($copy_tpl));
			if (!$code && isset($map[$copy_tpl])) {
				$code = file_get_contents($map[$copy_tpl]['path']);
			}
		}
		if (!$code) {
			$code = '';
		}

		try {
			/** @var $twig \Application\DeskPRO\Twig\Environment */
			$twig = $this->container->get('twig');
			$compiled = $twig->compileSource($this->_preProcessCustomTemplate($code), $name);
		} catch (\Twig_Error_Syntax $e) {
			return $this->createJsonResponse(array(
				'error' => true,
				'error_syntax' => true,
				'error_code' => $e->getCode(),
				'error_message' => $e->getMessage(),
				'error_line' => $e->getTemplateLine()
			));
		} catch (\Twig_Error $e) {
			return $this->createJsonResponse(array(
				'error' => true,
				'error_code' => $e->getCode(),
				'error_message' => $e->getMessage()
			));
		}

		$template = new Template();
		$template->style = $this->container->getSystemService('style');
		$template->name = $name;
		$template->setTemplate($code, $compiled);

		$this->db->beginTransaction();
		try {
			$this->em->persist($template);
			$this->em->flush();
			$this->db->commit();
		} catch (\Exception $e) {
			$this->db->rollback();
			throw $e;
		}

		return $this->createJsonResponse(array('success' => true, 'name' => $name));
	}

	####################################################################################################################
	# mini-manager
	####################################################################################################################

	public function miniManagerAction($dirname, $prefix)
	{
		$tplfiles = new TemplateFiles();
		$map = $tplfiles->getEmailTemplates();

		$custom_templates = $this->container->getSystemService('style')->getCustomTemplateInfo();
		$custom_templates = array_filter($custom_templates, function($v) use ($dirname, $prefix) {
			if (strpos($v['name'], "DeskPRO:custom_$dirname:$prefix") === 0) {
				return true;
			}
			return false;
		});

		$list = $this->groupMap($map, $custom_templates);
		$list = $list['DeskPRO'][$dirname];
		$list = array_filter($list['templates'], function($v) use ($list, $dirname, $prefix) {
			if (strpos($v['name'], "DeskPRO:$dirname:$prefix") === 0) {
				return true;
			}
			return false;
		});

		return $this->render('AdminBundle:Templates:mini-manager.html.twig', array(
			'default_templates' => $list,
			'custom_templates' => $custom_templates,
			'tpl_dirname' => $dirname,
			'tpl_prefix' => $prefix,
		));
	}

	####################################################################################################################
	# preview-email-template
	####################################################################################################################

	public function previewEmailTemplateAction($tpl)
	{
		$vars = array();

		$ticket = $this->em->createQuery("SELECT t FROM DeskPRO:Ticket t WHERE t.status != 'hidden' ORDER BY t.id DESC")->setMaxResults(1)->getSingleResult();
		$vars['ticket']      = $ticket;
		$vars['person']      = $this->person;
		$vars['access_code'] = $ticket->getAccessCode();

		$messages = $this->em->getRepository('DeskPRO:TicketMessage')->getTicketMessages($ticket,array(
			'limit' => 25,
			'order' => 'DESC',
			'with_notes' => true
		));
		$vars['messages'] = $messages;

		$html = $this->container->getTemplating()->render($tpl, $vars);

		$subject = '';
		if (strpos($html, '___DP___SUBJECT___SEP___') !== false) {
			list ($subject, $html) = explode('___DP___SUBJECT___SEP___', $html, 2);
			$subject = trim($subject);
			$html = trim($html);
		}

		$body = '<html><body>' . ($subject ? 'Subject: ' . htmlspecialchars($subject) . '<hr />' : '') . $html . '</body></html>';

		return $this->createResponse($body);
	}


	####################################################################################################################
	# email-list
	####################################################################################################################

	/**
	 * Lists agent and admin templates
	 */
	public function emailListAction($list_type = 'layout')
	{
		switch ($list_type) {
			case 'user':
				return $this->emailListUserAction();
			case 'agent':
				return $this->emailListAgentAction();
			default:
				return $this->emailListLayoutAction();
		}
	}

	public function emailListLayoutAction()
	{
		$vars = array();

		return $this->render('@emails-list-layout.html.twig', $vars);
	}

	public function emailListUserAction()
	{
		$vars = array();

		$vars['variations'] = App::getDb()->fetchAllGrouped("
			SELECT name, variant_of
			FROM templates
			WHERE name LIKE 'DeskPRO:emails_user:%'
		", array(), 'variant_of', null);

		$vars['custom_templates'] = App::getDb()->fetchAllCol("
			SELECT name
			FROM templates
			WHERE name LIKE 'DeskPRO:emails_user:custom_%' AND variant_of = 'DeskPRO:emails_user:blank.html.twig'
		");

		$vars['trigger_map'] = $this->em->getRepository('DeskPRO:TicketTrigger')->getTemplateVariantMap();

		return $this->render('@emails-list-user.html.twig', $vars);
	}

	public function emailListAgentAction()
	{
		$vars = array();

		$vars['variations'] = App::getDb()->fetchAllGrouped("
			SELECT name, variant_of
			FROM templates
			WHERE name LIKE 'DeskPRO:emails_agent:%'
		", array(), 'variant_of', null);

		$vars['custom_templates'] = App::getDb()->fetchAllCol("
			SELECT name
			FROM templates
			WHERE name LIKE 'DeskPRO:emails_agent:custom_%' AND variant_of = 'DeskPRO:emails_agent:blank.html.twig'
		");

		$vars['trigger_map'] = $this->em->getRepository('DeskPRO:TicketTrigger')->getTemplateVariantMap();

		return $this->render('@emails-list-agent.html.twig', $vars);
	}

	public function emailEditAction($name)
	{
		$vars = array();

		$template = $this->em->getRepository('DeskPRO:Template')->findOneBy(array('name' => $name));

		// A new variation
		if (!$template && $this->in->getString('variant_of') && in_array($this->in->getString('variant_of'), App::getTemplating()->getVariedTemplateNames())) {
			$template = new Template();
			$template->style = $this->container->getSystemService('style');
			$template->variant_of = $this->in->getString('variant_of');

			$name = preg_replace('#[^a-zA-Z0-9\-_]#', '_', $name);
			$nameparts = explode(':', $template->variant_of);
			array_pop($nameparts);

			$name = implode(':', $nameparts) . ':custom_' . $name . '.html.twig';
			$template->name = $name;

			$code = App::getTemplating()->getSource($template->variant_of);
			$twig = $this->container->get('twig');
			$compiled = $twig->compileSource($this->_preProcessCustomTemplate($code), $name);
			$template->setTemplate($code, $compiled);

			$this->em->persist($template);
			$this->em->flush();
		}

		$source = App::getTemplating()->getSplitSource($name);

		$vars['name']            = $name;
		$vars['source']          = $source;
		$vars['is_custom']       = strpos($name, ':custom_') !== false;
		$vars['template']        = $template;
		$vars['allow_variation'] = in_array($name, App::getTemplating()->getVariedTemplateNames());

		if ($template && $template->variant_of != 'DeskPRO:emails_user:blank.html.twig') {
			if ($template->variant_of) {
				$vars['default_template'] = $template->variant_of;
			} else {
				$vars['default_template'] = $template->name;
			}

			$vars['default_source'] = App::getTemplating()->splitSource(App::getTemplating()->getDefaultSource($vars['default_template']));
		}

		return $this->render('@email-edit.html.twig', $vars);
	}

	public function deleteCustomTemplateAction($name)
	{
		$this->ensureRequestToken('delete_template');

		$template = $this->em->getRepository('DeskPRO:Template')->findOneBy(array('name' => $name));
		if (!$template || !$template->isCustom()) {
			throw $this->createNotFoundException();
		}

		$this->em->remove($template);
		$this->em->flush();

		if (strpos($name, ':emails_user:') !== false) {
			return $this->redirectRoute('admin_templates_email', array('list_type' => 'user'));
		} else {
			return $this->redirectRoute('admin_templates_email', array('list_type' => 'agent'));
		}
	}

	####################################################################################################################
	# search-templates
	####################################################################################################################

	public function searchTemplatesAction()
	{
		$term = $this->in->getString('term');
		$is_regex = $this->in->getBool('is_regex');
		if ($is_regex) {
			$term = \Orb\Util\Strings::getInputRegexPattern($term);
		}

		#------------------------------
		# Find results in phrases
		#------------------------------

		$groups_reader = new \Application\DeskPRO\ResourceScanner\LanguagePhrases();
		$phrases = $groups_reader->getAllUserPhrases();

		$matched_phrases = array();
		foreach ($phrases as $phrase_id => $phrase_text) {
			if ($is_regex) {
				if (preg_match($term, $phrase_text)) {
					$matched_phrases[] = $phrase_id;
				}
			} else {
				if (stripos($phrase_text, $term) !== false) {
					$matched_phrases[] = $phrase_id;
				}
			}
		}

		#------------------------------
		# Now try to find it in templates
		#------------------------------

		$set = array();
		$set_map = array();

		switch ($this->in->getString('template_set')) {
			case 'emails_user':
				$set = array(
					'DeskPRO:emails_user:new-ticket.html.twig',
					'DeskPRO:emails_user:new-ticket-validate.html.twig',
					'DeskPRO:emails_user:new-ticket-agent.html.twig',
					'DeskPRO:emails_user:new-reply-agent.html.twig',
					'DeskPRO:emails_user:new-reply-user.html.twig',
					'DeskPRO:emails_user:new-reply-reject-resolved.html.twig',
					'DeskPRO:emails_user:ticket-rate.html.twig',
					'DeskPRO:emails_user:new-ticket-reg-closed.html.twig',
					'DeskPRO:emails_user:ticket-autoclose-warn.html.twig',
					'DeskPRO:emails_user:gateway-autoresponse-warn.html.twig',
					'DeskPRO:emails_user:chat-transcript.html.twig',
					'DeskPRO:emails_user:comment-new.html.twig',
					'DeskPRO:emails_user:comment-approved.html.twig',
					'DeskPRO:emails_user:comment-deleted.html.twig',
					'DeskPRO:emails_user:feedback-new.html.twig',
					'DeskPRO:emails_user:feedback-new-comment.html.twig',
					'DeskPRO:emails_user:feedback-updated.html.twig',
					'DeskPRO:emails_user:feedback-approved.html.twig',
					'DeskPRO:emails_user:feedback-disapproved.html.twig',
					'DeskPRO:emails_user:register-validate.html.twig',
					'DeskPRO:emails_user:reset-password.html.twig',
					'DeskPRO:emails_user:new-email-validate.html.twig',
					'DeskPRO:emails_user:account-disabled.html.twig',

					'DeskPRO:emails_common:ticket-rating-links.html.twig',
				);

				$set_map = array(
					'DeskPRO:emails_common:ticket-rating-links.html.twig' => array(
						'DeskPRO:emails_user:new-reply-agent.html.twig',
						'DeskPRO:emails_user:new-ticket-agent.html.twig',
					)
				);

				break;

			case 'emails_agent';
				$set = array(
					'DeskPRO:emails_agent:new-ticket.html.twig',
					'DeskPRO:emails_agent:ticket-update.html.twig',
					'DeskPRO:emails_agent:new-reply-user.html.twig',
					'DeskPRO:emails_agent:new-reply-agent.html.twig',
					'DeskPRO:emails_agent:new-agent-chat-message.html.twig',
					'DeskPRO:emails_agent:new-comment.html.twig',
					'DeskPRO:emails_agent:new-feedback.html.twig',
					'DeskPRO:emails_agent:new-registration.html.twig',
					'DeskPRO:emails_agent:login-alert.html.twig',
					'DeskPRO:emails_agent:agent-welcome.html.twig',
					'DeskPRO:emails_agent:agent-changeemail-mergeuser.html.twig',
					'DeskPRO:emails_agent:admin-noreset-password.html.twig',
					'DeskPRO:emails_agent:error-invalid-forward.html.twig',
					'DeskPRO:emails_agent:error-marker-missing.html.twig',
					'DeskPRO:emails_agent:error-unknown-from.html.twig'
				);
				break;
		}

		$matching_templates = array();
		foreach ($set as $tpl) {
			$source = App::getTemplating()->getSource($tpl);

			$match = false;
			if ($is_regex) {
				if (preg_match($term, $source)) {
					$match = true;
				}
			} else {
				if (stripos($source, $term) !== false) {
					$match = true;
				}
			}

			if (!$match && $matched_phrases) {
				foreach ($matched_phrases as $phrase_id) {
					if (strpos($source, $phrase_id) !== false) {
						$match = true;
						break;
					}
				}
			}

			if ($match) {
				if (isset($set_map[$tpl])) {
					foreach ($set_map[$tpl] as $map_tpl) {
						$matching_templates[] = $map_tpl;
					}
				} else {
					$matching_templates[] = $tpl;
				}
			}
		}

		$matching_templates = array_unique($matching_templates);

		return $this->createJsonResponse(array(
			'matches' => $matching_templates,
		));
	}

	protected function _preProcessCustomTemplate($code)
	{
		$code = preg_replace('#\{%\s*include\s+(\'|")(.*?)(\'|")\s+#', '{% include \'$2\' ignore missing ', $code);
		return $code;
	}
}