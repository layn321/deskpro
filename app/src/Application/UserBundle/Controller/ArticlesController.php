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
 * @subpackage UserBundle
 */

namespace Application\UserBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity;

use Orb\Util\Arrays;
use Orb\Util\Util;
use Orb\Util\Numbers;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Comments\NewCommentFormType;

use Application\UserBundle\Controller\Helper\ContentRating;
use Application\UserBundle\Controller\Helper\Comments;
use Application\UserBundle\Controller\Helper\FacebookLike;

class ArticlesController extends AbstractController
{
	public function preAction($action, $arguments = null)
	{
		if ($action != 'articleAgentIframeAction') {
			return parent::preAction($action, $arguments);
		}

		// The articleAgentIframeAction hadnles its own special auth
		$this->person = $this->session->getPerson();

		return null;
	}

	public function sectionPermissionCheck()
	{
		return $this->person->hasPerm('articles.use');
	}

	/**
	 * Main index shows initial category listing
	 */
	public function browseAction($slug = '')
	{
		/** @var $structure \Application\DeskPRO\Publish\Structure */
		$structure = $this->container->getSystemService('publish_structure');

		$page = $this->in->getUint('p');
		$page = max(1, $page);

		$per_page = 25;

		if ($slug) {
			$category_id = $this->container->getRouter()->getIdFromSlug($slug);
			$category = null;

			if ($category_id && $structure->hasArticleCategory($category_id)) {
				$category = $structure->getArticleCategory($category_id);
			}

			if (!$category) {
				return $this->renderStandardError('@user.error.not-found-title', '@user.error.not-found', 404);
			}

			// Auto-correct URL
			if ($slug != $category->getUrlSlug()) {
				return $this->redirectRoute('user_articles', array('slug' => $category->getUrlSlug()), 301);
			}

			$category_path = $category->getTreeParents();
			$category_children = array();

			$perm_manager = $this->person->PermissionsManager->get('ArticleCategories');
			foreach ($category->getChildren() as $subcat) {
				if ($perm_manager->isCategoryAllowed($subcat->getId())) {
					$category_children[$subcat->getId()] = $subcat;
				}
			}

			$searcher = new \Application\DeskPRO\Searcher\ArticleSearch();
			$searcher->setPersonContext($this->person);
			$searcher->addTerm('category_specific', 'is', $category['id']);
			$searcher->addTerm('status', 'is', 'published');
			$searcher->setOrderBy('id', 'desc');

			$total = $searcher->getCount();
			$pageinfo = Numbers::getPaginationPages($total, $page, $per_page, 3);
			$limit = array(
				'offset' => ($pageinfo['curpage']-1) * $per_page,
				'max' => $per_page
			);

			$article_ids = $searcher->getMatches($limit);

			$articles = $this->em->getRepository('DeskPRO:Article')->getByResultIds($article_ids);

			$pageinfo = Numbers::getPaginationPages($total, $page, $per_page);

		} else {
			$category = null;
			$category_children = $structure->getArticleRootCategories();
			$category_path = array();
			$articles = array();
			$pageinfo = null;
		}

		$category_counts = $structure->getArticleCategoryCounts($this->person);

		$comment_counts = array();
		if ($articles) {
			$comment_counts = $this->em->getRepository('DeskPRO:ArticleCategory')
				->getCommentHelper()
				->countsOnCollection($articles);
		}

		$category_children_articles = $this->em->getRepository('DeskPRO:Article')->getNewestInNodes($category_children, 5, $this->person);

		$tpl = 'UserBundle:Articles:browse.html.twig';

		$is_subscribed = false;
		if ($category && !$this->person->isGuest() && $this->settings->get('user.kb_subscriptions')) {
			$is_subscribed = $this->db->fetchColumn("
				SELECT id
				FROM kb_subscriptions
				WHERE person_id = ? AND category_id = ?
			", array($this->person->getId(), $category->getId()));
		}

		return $this->render($tpl, array(
			'pageinfo' => $pageinfo,
			'category' => $category,
			'category_path' => $category_path,
			'category_children' => $category_children,
			'category_children_articles' => $category_children_articles,
			'category_counts' => $category_counts,
			'articles' => $articles,
			'comment_counts' => $comment_counts,
			'section_counts' => $this->em->getRepository('DeskPRO:Article')->getSectionCounts(),
			'is_subscribed'  => $is_subscribed,
		));
	}


	public function filterAction()
	{
		/** @var $structure \Application\DeskPRO\Publish\Structure */
		$structure = $this->container->getSystemService('publish_structure');

		$page = $this->in->getUint('page');
		$page = max(1, $page);

		$per_page = 20;

		$kb_cats  = $structure->getArticleRootCategories();
		$products = $this->em->getRepository('DeskPRO:Product')->getFlatHierarchy();

		$searcher = new \Application\DeskPRO\Searcher\ArticleSearch();
		$searcher->setPersonContext($this->person);
		$searcher->addTerm('status', 'is', 'published');

		$search_options = array();
		$search_options['order_by'] = '';
		$search_options['product_id'] = '';
		$search_options['category_id'] = '';

		if ($this->in->getString('order_by')) {
			$searcher->setOrderByCode($this->in->getString('order_by'));
			$search_options['order_by'] = $this->in->getString('order_by');
		}
		if ($this->in->getUint('category_id')) {
			$searcher->addTerm('category', 'is', $this->in->getUint('category_id'));
			$search_options['category_id'] = $this->in->getUint('category_id');
		}
		if ($this->in->getUint('product_id')) {
			$searcher->addTerm('product', 'is', $this->in->getUint('product_id'));
			$search_options['product_id'] = $this->in->getUint('product_id');
		}

		$total = $searcher->getCount();
		$article_ids = $searcher->getMatches(array(
			'offset' => ($page-1) * $per_page,
			'max' => $per_page
		));

		if ($article_ids) {
			$articles = $this->em->getRepository('DeskPRO:Article')->getByResultIds($article_ids);
			$this->container->getObjectLangRepository()->preloadObjectCollection(null, $articles);
		} else {
			$articles = array();
		}

		$pageinfo = Numbers::getPaginationPages($total, $page, $per_page, 3);

		return $this->render('UserBundle:Articles:find.html.twig', array(
			'kb_cats' => $kb_cats,
			'products' => $products,
			'pageinfo' => $pageinfo,
			'search_options' => $search_options,
			'search_options_url' => http_build_query($search_options, null, '&amp;'),
			'articles' => $articles,
			'num_results' => $total,
			'section_counts' => $this->em->getRepository('DeskPRO:Article')->getSectionCounts($this->person),
		));
	}


	/**
	 * View an article listing
	 *
	 * @param  $article_id
	 */
	public function articleAction($slug)
	{
		$article = $this->em->getRepository('DeskPRO:Article')->getBySlug($slug);
		if (!$article) {
			return $this->renderStandardError('@user.knowledgebase.article_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewArticle($article)) {
			return $this->renderLoginOrPermissionError();
		}

		// Auto-correct URL
		if ($slug != $article->getUrlSlug()) {
			return $this->redirectRoute('user_articles_article', array('slug' => $article->getUrlSlug()), 301);
		}

		$all_categories = array();
		foreach ($article['categories'] as $cat) {
			$cats = array();
			$cats[] = $cat;
			$p = $cat['parent'];
			while ($p) {
				$cats[] = $p;
				$p = $p['parent'];
			}

			$all_categories[$cat['id']] = array_reverse($cats);
		}

		$this->container->getObjectLangRepository()->preloadObject(null, $article);

		$comments = null;
		$comments_widget = null;
		$comments_helper = Comments::create($article);
		if ($comments_helper) {
			$comments_widget = $comments_helper->getHtml();
		} else {
			$comments = $this->em->getRepository('DeskPRO:ArticleComment')->getDisplayComments($article, $this->person, $this->session->getVisitor());
		}

		if ($this->container->getSetting('core.facebook_like')) {
			$like_helper = FacebookLike::create($article);
			$facebook_like = $like_helper->getHtml();
		}

		$related_finder = new RelatedContentFinder($this->person, $article);
		$related_content = $related_finder->getRelatedEntities();

		$content_rating = new ContentRating($article, $this->person, $this->session->getVisitor());
		$content_rating->setRequest($this->request);
		$rating = $content_rating->getRating();

		if ($rating_log_search_id = $content_rating->getSearchLogId()) {
			$this->session->set('article.' . $article['id'], $rating_log_search_id);
		} elseif ($this->session->has('article.' . $article['id'])) {
			$rating_log_search_id = $this->session->get('article.' . $article['id']);
		} else {
			$rating_log_search_id = 0;
		}

		$tpl = 'UserBundle:Articles:article.html.twig';
		if ($this->in->getString('_partial') == 'overlayWidget' || $this->in->getString('_partial') == 'overlaySuggest') {
			$tpl = 'UserBundle:Articles:article-overlay.html.twig';
		}

		$glossary = new \Application\DeskPRO\Publish\GlossaryHandler($this->em);
		$glossary_words = $glossary->findWords($article->content);
		$word_defs = $glossary->getWordDefs($glossary_words);

		$this->container->getSystemService('view_log')->view($article);

		$is_subscribed = false;
		if (!$this->person->isGuest() && $this->settings->get('user.kb_subscriptions')) {
			$is_subscribed = $this->db->fetchColumn("
				SELECT id
				FROM kb_subscriptions
				WHERE person_id = ? AND article_id = ?
			", array($this->person->getId(), $article->getId()));
		}

		return $this->render($tpl, array(
			'rating' => $rating,
			'rating_log_search_id' => $rating_log_search_id,

			'article' => $article,
			'glossary_words' => $glossary_words,
			'word_defs' => $word_defs,
			'all_categories' => $all_categories,
			'comments' => $comments,
			'comments_widget' => $comments_widget,
			'facebook_like' => isset($facebook_like) ? $facebook_like : null,

			'related_content' => $related_content,
			'is_subscribed' => $is_subscribed,
		));
	}

	public function articleAgentIframeAction($article_id, $agent_session_id)
	{
		$agent_session = null;

		if (!$this->person->is_agent) {
			$agent_session = App::getEntityRepository('DeskPRO:Session')->getSessionFromCode($agent_session_id);
			if (!$agent_session || !$agent_session->person || !$agent_session->person->is_agent) {
				$agent_session = null;
			}
		}

		if ($agent_session) {
			$this->person = $agent_session->person;
		}

		if (!$this->person->is_agent) {
			return $this->renderStandardError('@user.knowledgebase.article_not_found', '@user.error.not-found', 404);
		}

		$article = $this->em->getRepository('DeskPRO:Article')->find($article_id);
		if (!$article) {
			return $this->renderStandardError('@user.knowledgebase.article_not_found', '@user.error.not-found', 404);
		}

		$glossary = new \Application\DeskPRO\Publish\GlossaryHandler($this->em);
		$glossary_words = $glossary->findWords($article->content);
		$word_defs = $glossary->getWordDefs($glossary_words);

		return $this->render('UserBundle:Articles:article-agent-iframe.html.twig', array(
			'article' => $article,
			'glossary_words' => $glossary_words,
			'word_defs' => $word_defs,
		));
	}


	/**
	 * @param $article_id
	 */
	public function articleSubscriptionAction($article_id, $auth)
	{
		$article = $this->em->getRepository('DeskPRO:Article')->find($article_id);
		if (!$article) {
			return $this->renderStandardError('@user.knowledgebase.article_not_found', '@user.error.not-found', 404);
		}

		if (!$this->checkAuthToken('subscribe_article', $auth) || !$this->settings->get('user.kb_subscriptions')) {
			return $this->redirectRoute('user_articles_article', array('slug' => $article->getUrlSlug()));
		}

		if ($this->person->isGuest()) {
			return $this->renderLoginOrPermissionError($this->generateUrl('user_articles_article_togglesub', array('article_id' => $article_id, 'auth' => $this->session->generateSecurityToken('subscribe_article'))));
		}

		$exist = $this->db->fetchColumn("
			SELECT id
			FROM kb_subscriptions
			WHERE person_id = ? AND article_id = ?
		", array($this->person->getId(), $article->getId()));

		if ($exist) {
			$this->db->delete('kb_subscriptions', array(
				'person_id'  => $this->person->getId(),
				'article_id' => $article->getId()
			));
		} else {
			$this->db->insert('kb_subscriptions', array(
				'person_id'  => $this->person->getId(),
				'article_id' => $article->getId()
			));
		}

		$url = $this->generateUrl('user_articles_article', array('slug' => $article->getUrlSlug()), true);
		return $this->redirect($url . '#dp_sb');
	}

	/**
	 * @param $category_id
	 */
	public function categorySubscriptionAction($category_id, $auth)
	{
		$category = $this->em->getRepository('DeskPRO:ArticleCategory')->find($category_id);
		if (!$category) {
			return $this->renderStandardError('@user.knowledgebase.article_not_found', '@user.error.not-found', 404);
		}

		if (!$this->checkAuthToken('subscribe_category', $auth) || !$this->settings->get('user.kb_subscriptions')) {
			return $this->redirectRoute('user_articles', array('slug' => $category->getUrlSlug()));
		}

		if ($this->person->isGuest()) {
			return $this->renderLoginOrPermissionError($this->generateUrl('user_articles_cat_togglesub', array('category_id' => $category_id, 'auth' => $this->session->generateSecurityToken('subscribe_category'))));
		}

		$exist = $this->db->fetchColumn("
			SELECT id
			FROM kb_subscriptions
			WHERE person_id = ? AND category_id = ?
		", array($this->person->getId(), $category->getId()));

		if ($exist) {
			$this->db->delete('kb_subscriptions', array(
				'person_id'   => $this->person->getId(),
				'category_id' => $category->getId()
			));
		} else {
			$this->db->insert('kb_subscriptions', array(
				'person_id'   => $this->person->getId(),
				'category_id' => $category->getId()
			));
		}

		$url = $this->generateUrl('user_articles', array('slug' => $category->getUrlSlug()), true);
		return $this->redirect($url . '#dp_sb');
	}

	public function unsubscribeAllAction($person_id, $auth)
	{
		$person = $this->em->find('DeskPRO:Person', $person_id);
		if (!$person) {
			return $this->renderStandardError('@user.knowledgebase.article_not_found', '@user.error.not-found', 404);
		}

		if (!\Orb\Util\Util::checkStaticSecurityToken($auth, App::getSetting('core.app_secret') . $person->getId() . $person->secret_string)) {
			return $this->renderStandardError('@user.knowledgebase.article_not_found', '@user.error.not-found', 404);
		}

		$this->db->delete('kb_subscriptions', array('person_id' => $person->getId()));

		$this->session->setFlash('email_prefs_saved', true);
		$this->session->save();

		// Disable cache for this guest so the flash message
		// appears and doesnt get cached for everyone
		if ($this->person->isGuest()) {
			App::setSkipCache(true);
		}

		return $this->redirectRoute('user');
	}


	/**
	 * Submit a new comment
	 *
	 * @param  $article_id
	 */
	public function newCommentAction($article_id)
	{
		if ($this->container->getSetting('core.interact_require_login') && !$this->person->getId()) {
			return $this->forward('UserBundle:Login:index');
		}

		if (!$this->person->hasPerm('articles.comment')) {
			return $this->renderLoginOrPermissionError();
		}

		$article = $this->em->getRepository('DeskPRO:Article')->find($article_id);
		if (!$article) {
			return $this->renderStandardError('@user.knowledgebase.article_not_found', '@user.error.not-found', 404);
		}

		$new_comment = new \Application\DeskPRO\Comments\NewComment(
			'Application\\DeskPRO\\Entity\\ArticleComment',
			$this->person,
			array('article' => $article)
		);

		$newcomment_formtype = new NewCommentFormType($this->person);
		$form = $this->get('form.factory')->create($newcomment_formtype, $new_comment);
		$validator = new \Application\UserBundle\Validator\NewCommentValidator();
		$validator->setPersonContext($this->person);

		if ($this->get('request')->getMethod() == 'POST') {

			$trap_fail = false;
			if (!empty($_POST['first_name']) || !empty($_POST['last_name']) || !empty($_POST['email'])) {
				$trap_fail = true;
			}

			if (!$this->consumeRequest('newcomment_articles') || $trap_fail) {
				return $this->redirectRoute('user_articles_article', array(
					'slug' => $article->getUrlSlug()
				));
			}

			$form->bindRequest($this->get('request'));

			if (!$validator->isValid($new_comment)) {
				$this->session->setFlash('comment_error', $validator->getErrors(true));
				return $this->redirectRoute('user_articles_article', array(
					'slug' => $article->getUrlSlug()
				));
			}

			$comment = $new_comment->save();

			App::setSkipCache(true);

			if ($new_comment->require_login) {
				$return_url = $this->generateUrl('user_newcomment_finishlogin', array(
					'comment_type' => 'article',
					'comment_id' => $comment->id,
				));
				return $this->redirectRoute('user_login', array('return' => $return_url));
			}
		}

		return $this->redirectRoute('user_articles_article', array(
			'slug' => $article->getUrlSlug()
		));
	}
}
