<?php
/**************************************************************************\
| DeskPRO (r) has been developed by DeskPRO Ltd. http://www.deskpro.com/   |
| a British company located in London, England.                            |
|                                                                          |
| All source code and content Copyright (c) 2012, DeskPRO Ltd.             |
1|                                                                          |
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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller;

use Orb\Data\ContentTypes;
use Symfony\Component\HttpFoundation\Response;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Article;
use Application\DeskPRO\Entity\ArticleAttachment;
use Application\DeskPRO\Entity\ArticleComment;
use Application\DeskPRO\Entity\ArticlePendingCreate;
use Application\DeskPRO\Entity\ResultCache;
use Application\DeskPRO\Searcher\ArticleSearch;
use Application\DeskPRO\UI\RuleBuilder;
use Application\DeskPRO\Publish\AgentHelper as PublishHelper;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;

use Application\DeskPRO\ContentRevision\Util as ContentRevisionUtil;

use Application\AgentBundle\Controller\Helper\ArticleResults;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Numbers;
use Orb\Util\Util;

use FineDiff;
use Zend\Http\Header\ContentType;

/**
 * Handles ticket searches
 */
class KbController extends AbstractController
{
	############################################################################
	# Edit article
	############################################################################

	public function viewArticleAction($article_id)
	{
        $is_pdf = $this->in->getBool('pdf');
		$article = $this->em->find('DeskPRO:Article', $article_id);
		if (!$article) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Unknown article $article_id");
		}

		if ($this->in->getBool('do_validate') AND $article['status_code'] == 'hidden.validating' && $this->person->hasPerm('agent_publish.validate')) {
			$article['status_code'] = Article::STATUS_PUBLISHED;
			$this->em->persist($article);
			$this->em->flush();
		}

		$tpl = 'AgentBundle:Kb:view.html.twig';

		#------------------------------
		# Custom fields
		#------------------------------

		$field_manager = $this->container->getSystemService('article_fields_manager');
		$custom_fields = $field_manager->getDisplayArrayForObject($article);

		#------------------------------
		# Article props
		#------------------------------

		$article_comments = $this->em->getRepository('DeskPRO:ArticleComment')->getComments($article);

		$article_revisions = $article->getRevisions();

		$related_finder = new RelatedContentFinder($this->person, $article);
		$related_content = $related_finder->getRelatedEntities();

		$glossary = new \Application\DeskPRO\Publish\GlossaryHandler($this->em);
		$content = $article->content;
		$glossary_words = $glossary->findWords($content);

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.editarticle.' . $article->getId(), $this->person->id);

		$sticky_search_words = $this->em->getRepository('DeskPRO:SearchStickyResult')->getWordsForObject($article);

		$rated_searches = $this->em->getRepository('DeskPRO:SearchLog')->getRatedSearchesFor('article', $article['id'], 'counted');

		$article_categories  = $this->em->getRepository('DeskPRO:ArticleCategory')->getInHierarchy();
		$article_products    = $this->em->getRepository('DeskPRO:Product')->getInHierarchy();

		$perms = array(
			'can_edit' => $this->person->PermissionsManager->PublishChecker->canEdit($article),
			'can_delete' => $this->person->PermissionsManager->PublishChecker->canDelete($article),
		);

		$user_view_count = $this->db->fetchColumn("
			SELECT COUNT(*)
			FROM page_view_log
			WHERE object_type = 1 AND object_id = ? AND view_action = 1 AND person_id IS NOT NULL
		", array($article->id));

		// Existing translations
		$trans_langs = $this->db->fetchAllCol("SELECT language_id FROM object_lang WHERE ref = 'articles.{$article->getId()}'");
		$trans_langs[] = $article->language->getId();
		$trans_langs = array_combine($trans_langs,$trans_langs);

		foreach ($this->container->getLanguageData()->getAll() as $lang) {
			$this->container->getObjectLangRepository()->preloadObject($lang, $article);
		}
		$this->container->getObjectLangRepository()->runPreload();

		$trans_data = $this->container->getObjectLangRepository()->getLoadedRecs($article);

		if (!count($article->categories)) {
			$first = Arrays::getFirstKey($article_categories);
			$cat = $this->em->getRepository('DeskPRO:ArticleCategory')->find($first);
			$article->addToCategory($cat);
			$this->em->persist($article);
			$this->em->flush($article);
		}

        $vars = array(
            'article'              => $article,
			'trans_langs'          => $trans_langs,
			'trans_data'           => $trans_data,
            'custom_fields'        => $custom_fields,
            'sticky_search_words'  => $sticky_search_words,
            'rated_searches'       => $rated_searches,
            'content'              => $content,
            'article_comments'     => $article_comments,
            'article_revisions'    => $article_revisions,
            'related_content'      => $related_content,
            'state'                => $state,
            'article_categories'   => $article_categories,
            'article_products'     => $article_products,
            'glossary_words'       => $glossary_words,
			'perms'                => $perms,
			'user_view_count'      => $user_view_count,
        );

        if($is_pdf)
        {
            $content_html = $this->renderView('DeskPRO:pdf_agent:view_article.html.twig', $vars);

			if (!defined('_MPDF_TEMP_PATH')) {
				define('_MPDF_TEMP_PATH', dp_get_tmp_dir() . '/pdf');
				if (!is_dir(_MPDF_TEMP_PATH)) {
					@mkdir(_MPDF_TEMP_PATH, 0777, true);
				}
			}
            $mpdf = new \mPDF_mPDF(
                'utf-8', // Language/Character set
                'A4', // Size
                '8', // Default Font Size
                '', // Default Font
                20, // Margin Left
                20, // Margin Right
                40, // Margin Top
                40, // Margin Bottom
                10, // Margin Header
                10, // Margin Footer
                'P' // Orientation
            );

            $mpdf->SetBasePath($this->container->getSetting('core.deskpro_url') . '/');
			$mpdf->WriteHTML($content_html);

            if($this->in->getBool('html')) {
				$response = new Response();
                $response->setContent($content_html);
            } else {
				$mpdf->Output($article->title . '.pdf', 'D');
				exit;
            }
        }

		return $this->render($tpl, $vars);
	}

	public function viewRevisionsAction($article_id)
	{
		$article = $this->em->find('DeskPRO:Article', $article_id);
		if (!$article) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Unknown article $article_id");
		}

		$article_revisions = $article->getRevisions();

		return $this->render('AgentBundle:Kb:view-revisions-tab.html.twig', array(
			'article'              => $article,
			'article_revisions'    => $article_revisions,
		));
	}

	public function ajaxSaveLabelsAction($article_id)
	{
		$article = $this->em->find('DeskPRO:Article', $article_id);

		$labels = $this->in->getCleanValueArray('labels', 'string', 'discard');

		$article->getLabelManager()->setLabelsArray($labels);

		$this->em->persist($article);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => 1));
	}

	public function ajaxMassSaveAction()
	{
		$articles = $this->in->getCleanValueArray('result_ids', 'int', 'discard');
		$from_category = $this->in->getInt('from_category');
		$action = $this->in->getString('action');

		$data = array('success' => 1, 'category' => $from_category);
		$skip = false;
		$tr = App::getTranslator();
		$error = null;

		switch ($action) {
			case 'move':
				$to_category = $this->in->getInt('to_category');

				if ($from_category && $from_category == $to_category) {
					$error = $tr->phrase('agent.publish.error_kb_cats_same');
					$skip = true;
					break;
				}

				if ($from_category) {
					$from = $this->em->find('DeskPRO:ArticleCategory', $from_category);
				} else {
					$from = null;
				}

				$to = $this->em->find('DeskPRO:ArticleCategory', $to_category);

				if(($from_category && !$from) || !$to) {
					$error = $tr->phrase('agent.publish.error_kb_not_in_db');
					$skip = true;
					break;
				}

				break;
		}

		if(!$skip) {
			$affected = 0;
			$perm_failures = 0;
			$missing = 0;
			$this->em->beginTransaction();

			foreach ($articles as $article_id) {
				$article = $this->em->find('DeskPRO:Article', $article_id);

				if(!$article) {
					$missing++;
					continue;
				}

				switch ($action) {
					case 'draft':
						if (!$this->person->PermissionsManager->PublishChecker->canEdit($article)) {
							$perm_failures++;
							continue;
						}

						$article->status_code = 'hidden.draft';
						$affected++;
						break;
					case 'delete':
						if (!$this->person->PermissionsManager->PublishChecker->canDelete($article)) {
							$perm_failures++;
							continue;
						}

						$article->status_code = 'hidden.deleted';
						$affected++;
						break;
					case 'move':
						if (!$this->person->PermissionsManager->PublishChecker->canEdit($article)) {
							$perm_failures++;
							continue;
						}

						if ($from) {
							$article->removeFromCategory($from);
						} else {
							// If theres no 'from' category, means we're
							// moving from all so delete all old cats
							foreach ($article->categories as $c) {
								if ($c->getId() != $to->getId()) {
									$article->removeFromCategory($c);
								}
							}
						}

						if(!$article->isInCategory($to)) {
							$article->addToCategory($to);
						}

						$affected++;
						break;
				}

				$this->em->persist($article);
			}

			$this->em->flush();
			$this->em->commit();

			if($affected < count($articles)) {
				$error = $tr->phrase('agent.publish.error_kb_unaffected');
				$error .= "<br />\n";

				$errors = array();

				if($missing) {
					$errors[] = $tr->phrase('agent.publish.error_kb_missing', array('count' => $missing));
				}

				if($perm_failures) {
					$errors[] = $tr->phrase('agent.publish.error_kb_perm_denied', array('count' => $perm_failures));
				}

				$error .= implode("<br />\n", $errors);
			}
		}
		else {
			$data['success'] = false;
		}

		$data['error'] = $error;
		return $this->createJsonResponse($data);
	}

	public function ajaxSaveAction($article_id)
	{
		$article = $this->em->find('DeskPRO:Article', $article_id);

		if (!$article) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$rev = null;

		$action = $this->in->getString('action');

		if ($action == 'delete') {
			if (!$this->person->PermissionsManager->PublishChecker->canDelete($article)) {
				return $this->createJsonResponse(array('success' => false));
			}
		} else {
			if (!$this->person->PermissionsManager->PublishChecker->canEdit($article)) {
				return $this->createJsonResponse(array('success' => false));
			}
		}

		$data = array('success' => 1);

		$this->em->beginTransaction();

		switch ($action) {
			case 'status':
				$article['status_code'] = $this->in->getString('status');
				if ($article['status_code'] == 'published' && !$this->person->hasPerm('agent_publish.validate')) {
					$article['status_code'] = 'hidden.validating';
				}
				break;

			case 'title':
				$article['title'] = $this->in->getString('title');

				$rev = ContentRevisionUtil::findOrCreate($article, 'title', $this->person);
				$rev['title'] = $article['title'];

				break;

			case 'delete':
				$article->status_code = 'hidden.deleted';
				break;

			case 'categories':
				$cat_ids = $this->in->getCleanValueArray('category_ids', 'uint', 'discard');
				$cats = $this->em->getRepository('DeskPRO:ArticleCategory')->getByIds($cat_ids);

				$article->setCategories($cats);

				$data['category_ids'] = $cat_ids;
				break;

			case 'products':
				$prod_ids = $this->in->getCleanValueArray('product_ids', 'uint', 'discard');
				$prods    = $this->em->getRepository('DeskPRO:Product')->getByIds($prod_ids);

				$article->setProducts($prods);

				$data['product_ids'] = $prod_ids;
				break;

			case 'remove-auto-unpub':
				$article->date_end = null;
				$article->end_action = null;
				break;

			case 'auto-unpub':
				$date = date_create('@' . $this->in->getUint('end_timestamp'));
				$action = $this->in->getString('end_action');

				$article->date_end = $date;
				$article->end_action = $action;
				break;

			case 'remove-auto-pub':
				$article->date_published = null;
				break;

			case 'add-related':
				$updater = new RelatedContentUpdate($article);
				$updater->addRelated(
					$this->in->getString('content_type'),
					$this->in->getString('content_id')
				);
				break;

			case 'remove-related':
				$updater = new RelatedContentUpdate($article);
				$updater->removeRelated(
					$this->in->getString('content_type'),
					$this->in->getString('content_id')
				);
				break;

			case 'remove-blob':

				foreach ($article->attachments as $k => $attach) {
					if ($attach->blob['id'] == $this->in->getUint('blob_id')) {
						$article->attachments->remove($k);
						$this->em->remove($attach);
						break;
					}
				}

				break;

			case 'content':

				$content_info = Strings::parseImageDataUrls($this->in->getCleanValue('content', 'string', null, array('noclean' => true)));

				if (!empty($content_info['files'])) {
					foreach ($content_info['files'] as $file_info) {
						$file_ext = ContentTypes::findExtensionForContentType($file_info['type'], false);
						if (!$file_ext) {
							continue;
						}

						$blob = $this->container->getBlobStorage()->createBlobRecordFromString(
							$file_info['data'],
							"file.$file_ext",
							$file_info['type'],
							array()
						);
						$blob->is_media_upload = true;

						$this->em->persist($blob);

						$content_info['string'] = str_replace($file_info['token'], $blob->getDownloadUrl(true, true), $content_info['string']);
					}
				}

				$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.editarticle', $this->person->id);

				$article['content'] = $content_info['string'];

				$rev = ContentRevisionUtil::findOrCreate($article, 'content', $this->person);
				$rev['content'] = $article['content'];

				$glossary = new \Application\DeskPRO\Publish\GlossaryHandler($this->em);
				$content = $article->content;
				$content = $glossary->processText($content);

				if ($lang_id = $this->in->getUint('language_id')) {
					$lang = $this->container->getLanguageData()->get($lang_id);
					if ($lang) {
						$article->language = $lang;
					}
				}

				$article->date_updated = new \DateTime();

				$data['content_html'] = $this->renderView('AgentBundle:Kb:view-content-tab.html.twig', array(
					'article' => $article,
					'content' => $content
				));
				break;

			case 'trans':

				foreach ($this->container->getLanguageData()->getAll() as $lang) {
					$this->container->getObjectLangRepository()->preloadObject($lang, $article);
				}

				foreach ($this->container->getLanguageData()->getAll() as $lang) {
					$lang_id = $lang->getId();

					if ($lang_id == $article->language->getId()) {
						continue;
					}

					$title       = $this->in->getString("title.$lang_id");
					$content_val = (string)$this->in->getRaw("content.$lang_id");

					if (!$title && !$content_val) {
						continue;
					}

					$rec = $this->container->getObjectLangRepository()->setRec($lang, $article, 'title', $title);
					$this->em->persist($rec);

					$rec = $this->container->getObjectLangRepository()->setRec($lang, $article, 'content', $content_val);
					$this->em->persist($rec);
				}

				break;
		}

		$this->em->persist($article);
		if ($rev) {
			$this->em->persist($rev);
		}

		$this->em->flush();
		$this->em->commit();

		if ($rev) {
			$data['revision_id'] = $rev['id'];
		} else {
			$data['revision_id'] = null;
		}

		return $this->createJsonResponse($data);
	}

	public function ajaxSaveCustomFieldsAction($article_id)
	{
		$article = $this->em->find('DeskPRO:Article', $article_id);

		if (!$article) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (!$this->person->PermissionsManager->PublishChecker->canEdit($article)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->beginTransaction();

		try {
			$field_manager = $this->container->getSystemService('article_fields_manager');
			$post_custom_fields = $this->request->request->get('custom_fields', array());
			if (!empty($post_custom_fields)) {
				$field_manager->saveFormToObject($post_custom_fields, $article);
			}

			$this->em->flush();
			$this->em->commit();
		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		$custom_fields = $field_manager->getDisplayArrayForObject($article);

		return $this->render('AgentBundle:Kb:view-customfields-rendered-rows.html.twig', array(
			'article' => $article,
			'custom_fields' => $custom_fields,
		));
	}

	public function ajaxSaveCommentAction($article_id)
	{
		$article = $this->em->find('DeskPRO:Article', $article_id);

		if (!$article) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$comment = new ArticleComment();
		$comment->article = $article;
		$comment->person = $this->person;
		$comment['content'] = $this->in->getString('content');
		$comment['status'] = 'visible';
		$comment['date_created']  = new \DateTime();

		if ($this->person->hasPerm('agent_publish.validate')) {
			$comment->is_reviewed = true;
		}

		$this->em->persist($comment);
		$this->em->flush();

		return $this->render('AgentBundle:Kb:view-comment.html.twig', array(
			'comment' => $comment
		));
	}

	############################################################################
	# Pending articles
	############################################################################

	/**
	 * List the articles
	 */
	public function listPendingArticlesAction()
	{
		$pending_articles = $this->em->getRepository('DeskPRO:ArticlePendingCreate')->getPendingArticles();

		$ticket_ids = array();
		foreach ($pending_articles as $pa) {
			if ($pa->getTicketId()) {
				$ticket_ids[] = $pa->getTicketId();
			}
		}

		$first_messages = array();
		if ($ticket_ids) {
			$first_messages_raw = $this->em->createQuery("
				SELECT m
				FROM DeskPRO:TicketMessage m
				LEFT JOIN m.ticket t
				WHERE t.id IN (?0)
				GROUP BY t
				ORDER BY m.id ASC
			")->setParameters(array($ticket_ids))->execute();

			$first_messages = array();
			foreach ($first_messages_raw as $m) {
				$first_messages[$m->ticket->getId()] = $m;
			}
		}

		return $this->render('AgentBundle:Kb:pending-articles.html.twig', array(
			'pending_articles' => $pending_articles,
			'first_messages'   => $first_messages,
		));
	}


	/**
	 * [AJAX] Adds a new pending article
	 */
	public function newPendingArticleAction()
	{
		$pending_article = new ArticlePendingCreate();
		$pending_article->person = $this->person;

		if ($this->in->getUint('ticket_id')) {
			$ticket = $this->em->find('DeskPRO:Ticket', $this->in->getUint('ticket_id'));
			if ($ticket) {
				$pending_article->ticket = $ticket;
			}
		}

		$pending_article['comment'] = $this->in->getString('comment');

		$this->em->persist($pending_article);
		$this->em->flush();

		$row_html = $this->renderView('AgentBundle:Kb:pending-articles-page.html.twig', array('pending_articles' => array($pending_article)));

		return $this->createJsonResponse(array(
			'row_html' => $row_html,
			'pending_article_id' => $pending_article['id']
		));
	}

	/**
	 * [AJAX] remove a pending article
	 */
	public function removePendingArticleAction($pending_article_id)
	{
		$pending_article = $this->em->find('DeskPRO:ArticlePendingCreate', $pending_article_id);

		if (!$pending_article) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}
		if (!$this->person->PermissionsManager->PublishChecker->canValidate($pending_article)) {
			return $this->createJsonResponse(array('success' => false));
		}

		$this->em->remove($pending_article);
		$this->em->flush();

		return $this->createJsonResponse(array(
			'success' => true,
			'pending_article_id' => $pending_article_id,
		));
	}

	public function pendingArticleInfoAction($pending_article_id)
	{
		$pending_article = $this->em->find('DeskPRO:ArticlePendingCreate', $pending_article_id);

		if (!$pending_article) {
			return $this->createNotFoundException();
		}

		$data = array();
		$data['id'] = $pending_article_id;
		$data['comment'] = $pending_article['comment'];

		$data['person_id'] = $pending_article->person['id'];
		$data['person_name'] = $pending_article->person->getDisplayName();

		$ticket = null;
		if ($pending_article->ticket) {
			$ticket = $pending_article->ticket;
			$data['ticket_id'] = $pending_article->ticket->id;
			$data['ticket_subject'] = $pending_article->ticket->subject;
			$data['ticket_url'] = $this->get('router')->generate('agent_ticket_view', array('ticket_id' => $pending_article->ticket->id));
		}
		if ($pending_article->message) {
			$ticket = $pending_article->message->ticket;
			$data['message_id'] = $pending_article->message->id;
			$data['message_content_html'] = $pending_article->message->getMessageHtml();
		}

		// First message
		if ($ticket) {
			$first_message = $this->em->getRepository('DeskPRO:TicketMessage')->getFirstTicketMessage($ticket);
			$data['initial_message_html'] = $first_message->getMessageHtml();
			$data['initial_message_id'] = $first_message->id;
		}

		return $this->createJsonResponse($data);
	}

	public function pendingArticlesMassActionsAction($action)
	{
		$this->em->beginTransaction();

		$p_articles = $this->em->getRepository('DeskPRO:ArticlePendingCreate')->getByIds($this->in->getCleanValueArray('ids', 'uint', 'discard'));

		foreach ($p_articles as $p_article) {
			switch ($action) {
				case 'delete':
					if (!$this->person->PermissionsManager->PublishChecker->canValidate($p_article)) {
						continue;
					}
					$this->em->remove($p_article);
					break;
			}
		}

		$this->em->flush();
		$this->em->commit();

		return $this->createJsonResponse(array(
			'success' => 1
		));
	}

	############################################################################
	# Listings
	############################################################################

	public function listAction($category_id = 0)
	{
		$category = null;
		if ($category_id) {
			$category = $this->em->find('DeskPRO:ArticleCategory', $category_id);
		}

		$show_all = false;
		if (!$category) {
			$show_all = $this->in->getBool('all');
		}

		$is_trans_view = false;
		$trans_lang_id = null;

		if ($this->in->getBool('pending_translate')) {

			$is_trans_view = true;
			$trans_lang_id = $this->in->getUint('language_id');

			$result_helper = ArticleResults::newFromRequest($this, array(
				'pending_translate'      => true,
				'pending_translate_lang' => $this->in->getUint('language_id')
			));
		} else {
			$result_helper = ArticleResults::newFromRequest($this, array(
				'category' => $category,
				'show_all' => $show_all
			));
		}

		$page = $this->in->getUint('p');
		if (!$page) $page = 1;

		$results = $result_helper->getArticlesForPage($page);
		$result_cache = $result_helper->getResultCache();

		$total_results = count($result_helper->getArticleIds());
		$num_pages = ceil($total_results / 50);
		$showing_to = min(($page) * 50, $total_results);

		$display_fields = $this->person->getPref('agent.ui.kb-filter-display-fields.0');
		if (!$display_fields) {
			$display_fields = array('author', 'date_created');
		}

		$tpl = 'AgentBundle:Kb:filter.html.twig';
		if ($this->request->isPartialRequest()) {
			$tpl = 'AgentBundle:Kb:filter-page.html.twig';
		}

		$article_categories = $this->em->getRepository('DeskPRO:ArticleCategory')->getInHierarchy();

		$comment_counts = array();
		if ($results) {
			$comment_counts = $this->db->fetchAllKeyValue("
				SELECT article_id, COUNT(*)
				FROM article_comments
				WHERE article_id IN (" . implode(',', array_keys($results)) . ")
				GROUP BY article_id
			");
		}

		$cat_usergroups = array();
		$cat_structure_data = array();
		if ($category) {
			$cat_usergroups = $this->db->fetchAllCol("
				SELECT usergroup_id
				FROM article_category2usergroup
				WHERE category_id = ?
			", array($category->getId()));

			$cat_structure_data = $article_categories;
			$cat_structure_data = Arrays::removeButKey($cat_structure_data, array('id' , 'title', 'children'), true, true);
			$cat_structure_data = Arrays::multiRenameKey($cat_structure_data, 'title', 'label');
			$cat_structure_data = Arrays::assocToNumericArary($cat_structure_data, 'children');
		}

		return $this->render($tpl, array(
			'results'            => $results,
			'result_id'          => $result_cache['id'],
			'display_fields'     => $display_fields,
			'comment_counts'     => $comment_counts,

			'is_trans_view'      => $is_trans_view,
			'trans_lang_id'      => $trans_lang_id,

			'total_results' => $total_results,
			'num_pages' => $num_pages,
			'cur_page' => $page,
			'showing_to' => $showing_to,

			'search_form'        => array('terms' => $result_cache['criteria']['terms']),
			'cache'              => $result_cache,
			'terms_summary'      => $result_cache['extra']['summary'],
			'category'           => $category,
			'cat_usergroups'     => $cat_usergroups,
			'cat_structure_data' => $cat_structure_data,

			'article_categories' => $article_categories
		));
	}

	public function articleInfoAction($article_id)
	{
		$article = $this->em->find('DeskPRO:Article', $article_id);

		$data = array(
			'article_id' => $article['id'],
			'permalink'  => $article->getLink(),
			'content'    => $article->getContentHtml(),
			'is_html'    => true,
		);

		return $this->createJsonResponse($data);
	}

	############################################################################
	# Compare revisions
	############################################################################

	public function compareRevisionsAction($rev_old_id, $rev_new_id)
	{
		$diff_info = ContentRevisionUtil::compareRevisions('DeskPRO:ArticleRevision', $rev_old_id, $rev_new_id);

		return $this->render('AgentBundle:Kb:compare-revs.html.twig', array(
			'rendered_content_diff' => $diff_info['rendered_content_diff'],
			'rendered_title_diff'   => $diff_info['rendered_title_diff'],
		));
	}

	############################################################################
	# New article
	############################################################################

	public function newArticleAction()
	{
		$article_categories = $this->em->getRepository('DeskPRO:ArticleCategory')->getInHierarchy();

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.newarticle', $this->person->id);

		return $this->render('AgentBundle:Kb:newarticle.html.twig', array(
			'article_categories' => $article_categories,
			'state' => $state
		));
	}



	public function newArticleSaveAction()
	{
		$newarticle = new \Application\AgentBundle\Form\Model\NewArticle($this->person);

		$formType = new \Application\AgentBundle\Form\Type\NewArticle();
		$form = $this->get('form.factory')->create($formType, $newarticle);

		$this->db->executeUpdate("DELETE FROM people_prefs WHERE name = 'agent.ui.state.newarticle' AND person_id = ?", array($this->person->id));

		if ($this->get('request')->getMethod() == 'POST') {
			$form->bindRequest($this->get('request'));
			$form->isValid();

			$validator = new \Application\AgentBundle\Validator\NewArticleValidator();
			if (!$validator->isValid($newarticle)) {
				return $this->createJsonResponse(array(
					'error' => true,
					'error_codes' => $validator->getErrorGroups()
				));
			}

			$newarticle->save();

			$article = $newarticle->getArticle();

			if ($this->in->getUint('pending_article_id')) {
				$pending_article = $this->em->find('DeskPRO:ArticlePendingCreate', $this->in->getUint('pending_article_id'));
				if ($pending_article) {
					$this->em->remove($pending_article);
					$this->em->flush();
				}
			}

			$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.newarticle', $this->person->id);

			return $this->createJsonResponse(array(
				'success' => true,
				'article_id' => $article['id']
			));
		} else {
			return $this->createJsonResponse(array(
				'success' => false,
			));
		}
	}
}
