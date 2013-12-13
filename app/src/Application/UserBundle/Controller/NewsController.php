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
use Orb\Util\Numbers;

use Application\DeskPRO\Comments\NewCommentFormType;

use Application\UserBundle\Controller\Helper\ContentRating;
use Application\UserBundle\Controller\Helper\Comments;
use Application\UserBundle\Controller\Helper\FacebookLike;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;

class NewsController extends AbstractController
{
	public function sectionPermissionCheck()
	{
		return $this->person->hasPerm('news.use');
	}

	public function browseAction($slug = '', $_format = 'html')
	{
		/** @var $structure \Application\DeskPRO\Publish\Structure */
		$structure = $this->container->getSystemService('publish_structure');

		$page = 1;
		if ($this->in->getUint('p')) {
			$page = $this->in->getUint('p');
		}
		if (!$page || $page < 1) $page = 1;

		$search_options = array();
		$search_options['order_by'] = $this->in->getString('order_by');

		if ($slug) {
			$category_id = $this->container->getRouter()->getIdFromSlug($slug);
			$category = null;

			if ($category_id && $structure->hasNewsCategory($category_id)) {
				$category = $structure->getNewsCategory($category_id);
			}

			if (!$category) {
				return $this->renderStandardError('@user.error.not-found-title', '@user.error.not-found', 404);
			}

			// Auto-correct URL
			if ($slug != $category->getUrlSlug()) {
				return $this->redirectRoute('user_news', array('slug' => $category->getUrlSlug()), 301);
			}

			$category_path = $category->getTreeParents();

			$searcher = new \Application\DeskPRO\Searcher\NewsSearch();
			$searcher->setPersonContext($this->person);
			$searcher->addTerm('category', 'is', $category['id']);

		} else {
			$category = null;
			$category_path = null;

			$searcher = new \Application\DeskPRO\Searcher\NewsSearch();
			$searcher->setPersonContext($this->person);
		}

		$searcher->addTerm('status', 'is', 'published');

		$news_cats = $structure->getNewsCategories();
		$news_cat_objs = $structure->getNewsCategories();
		$category_counts = $structure->getNewsCategoryCounts($this->person);

		if ($search_options['order_by']) {
			$searcher->setOrderByCode($search_options['order_by']);
		} else {
			$searcher->setOrderBy('id', 'desc');
		}

		$per_page = 5;
		if ($this->request->isPartialRequest() == 'portal') {
			$per_page = 2;
		}

		$total = $searcher->getCount();
		$pageinfo = Numbers::getPaginationPages($total, $page, $per_page, 3);
		$limit = array(
			'offset' => ($pageinfo['curpage']-1) * $per_page,
			'max' => $per_page
		);

		$news_ids = $searcher->getMatches($limit);
		$news = $this->em->getRepository('DeskPRO:News')->getByIds($news_ids, true);

		$show_more = false;
		if ($page < $pageinfo['last']) {
			$show_more = true;
		}

		$comment_counts = array();
		if ($news) {
			$comment_counts = $this->em->getRepository('DeskPRO:NewsCategory')
				->getCommentHelper()
				->countsOnCollection($news);
		}

		$tpl = 'UserBundle:News:filter.html.twig';
		if ($this->request->isPartialRequest() == 'portal') {
			$tpl = 'UserBundle:News:portal-display.html.twig';
		}

		if ($_format == 'rss') {
			$tpl = 'UserBundle:News:filter.rss.twig';
		}

		return $this->render($tpl, array(
			'news_cats' => $news_cats,
			'news_cat_objs' => $news_cat_objs,
			'category' => $category,
			'category_counts' => $category_counts,
			'category_path' => $category_path,
			'news_entries' => $news,
			'comment_counts' => $comment_counts,
			'num_results' => $total,
			'pageinfo' => $pageinfo,
			'per_page' => $per_page,
			'show_more' => $show_more
		));
	}

	/**
	 * View a post
	 *
	 * @param  $post_id
	 */
	public function viewAction($slug)
	{
		$news = $this->em->getRepository('DeskPRO:News')->getBySlug($slug);
		if (!$news) {
			return $this->renderStandardError('@user.news.news_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewNews($news)) {
			return $this->renderLoginOrPermissionError();
		}

		// Auto-correct URL
		if ($slug != $news->getUrlSlug()) {
			return $this->redirectRoute('user_news_view', array('slug' => $news->getUrlSlug()), 301);
		}

		$categories = $this->em->getRepository('DeskPRO:NewsCategory')->getRootNodes();
		$category = $news->category;
		$category_path = $category->getTreeParents();

		$comments = null;
		$comments_widget = null;
		$comments_helper = Comments::create($news);
		if ($comments_helper) {
			$comments_widget = $comments_helper->getHtml();
		} else {
			$comments = $this->em->getRepository('DeskPRO:NewsComment')->getDisplayComments($news, $this->person, $this->session->getVisitor());
		}

		if ($this->container->getSetting('core.facebook_like')) {
			$like_helper = FacebookLike::create($news);
			$facebook_like = $like_helper->getHtml();
		}

		$related_finder = new RelatedContentFinder($this->person, $news);
		$related_content = $related_finder->getRelatedEntities();

		$content_rating = new ContentRating($news, $this->person, $this->session->getVisitor());
		$content_rating->setRequest($this->request);
		$rating = $content_rating->getRating();

		if ($rating_log_search_id = $content_rating->getSearchLogId()) {
			$this->session->set('news.' . $news['id'], $rating_log_search_id);
		} elseif ($this->session->has('news.' . $news['id'])) {
			$rating_log_search_id = $this->session->get('news.' . $news['id']);
		} else {
			$rating_log_search_id = 0;
		}

		$tpl = 'UserBundle:News:view.html.twig';
		if ($this->in->getString('_partial') == 'overlayWidget' || $this->in->getString('_partial') == 'overlaySuggest') {
			$tpl = 'UserBundle:News:view-overlay.html.twig';
		}

		$this->container->getSystemService('view_log')->view($news);

		return $this->render($tpl, array(
			'rating' => $rating,
			'rating_log_search_id' => $rating_log_search_id,
			'news' => $news,
			'category_path' => $category_path,
			'category' => $category,
			'categories' => $categories,
			'comments' => $comments,
			'comments_widget' => $comments_widget,

			'facebook_like' => isset($facebook_like) ? $facebook_like : null,

			'related_content' => $related_content
		));
	}



	/**
	 * Submit a new comment
	 *
	 * @param  $post_id
	 */
	public function newCommentAction($post_id)
	{
		if ($this->container->getSetting('core.interact_require_login') && !$this->person->getId()) {
			return $this->forward('UserBundle:Login:index');
		}

		if (!$this->person->hasPerm('news.comment')) {
			return $this->renderLoginOrPermissionError();
		}

		$post = $this->em->getRepository('DeskPRO:News')->find($post_id);
		if (!$post) {
			return $this->renderStandardError('@user.news.news_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewNews($post)) {
			return $this->renderLoginOrPermissionError();
		}

		$new_comment = new \Application\DeskPRO\Comments\NewComment(
			'Application\\DeskPRO\\Entity\\NewsComment',
			$this->person,
			array('news' => $post)
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

			if (!$this->consumeRequest('newcomment_news') || $trap_fail) {
				return $this->redirectRoute('user_news_view', array(
					'slug' => $post->getUrlSlug()
				));
			}

			$form->bindRequest($this->get('request'));

			if (!$validator->isValid($new_comment)) {
				$this->session->setFlash('comment_error', $validator->getErrors(true));
				return $this->redirectRoute('user_news_view', array(
					'slug' => $post->getUrlSlug()
				));
			}

			$comment = $new_comment->save();

			App::setSkipCache(true);

			if ($new_comment->require_login) {
				return $this->redirectRoute('user_newcomment_finishlogin', array(
					'comment_type' => 'news',
					'comment_id' => $comment->id,
				));
			}
		}

		return $this->redirectRoute('user_news_view', array(
			'slug' => $post->getUrlSlug()
		));
	}
}
