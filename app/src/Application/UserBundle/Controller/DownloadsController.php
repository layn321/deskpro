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
use Application\DeskPRO\Comments\NewCommentFormType;

use Orb\Util\Arrays;
use Orb\Util\Numbers;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\UserBundle\Controller\Helper\ContentRating;
use Application\UserBundle\Controller\Helper\Comments;
use Application\UserBundle\Controller\Helper\FacebookLike;

class DownloadsController extends AbstractController
{
	public function sectionPermissionCheck()
	{
		return $this->person->hasPerm('downloads.use');
	}

	public function browseAction($slug = '')
	{
		/** @var $structure \Application\DeskPRO\Publish\Structure */
		$structure = $this->container->getSystemService('publish_structure');

		$page = $this->in->getUint('p');
		if (!$page) $page = 1;

		$search_options = array();
		$search_options['order_by'] = $this->in->getString('order_by');

		if ($slug) {
			$category_id = $this->container->getRouter()->getIdFromSlug($slug);
			$category = null;

			if ($category_id && $structure->hasDownloadCategory($category_id)) {
				$category = $structure->getDownloadCategory($category_id);
			}

			if (!$category) {
				return $this->renderStandardError('@user.error.not-found-title', '@user.error.not-found', 404);
			}

			// Auto-correct URL
			if ($slug != $category->getUrlSlug()) {
				return $this->redirectRoute('user_downloads', array('slug' => $category->getUrlSlug()), 301);
			}

			$category_path = $category->getTreeParents();

			$searcher = new \Application\DeskPRO\Searcher\DownloadSearch();
			$searcher->setPersonContext($this->person);
			$searcher->addTerm('category', 'is', $category['id']);
			$searcher->addTerm('status', 'is', 'published');

			if ($search_options['order_by']) {
				$searcher->setOrderByCode($search_options['order_by']);
			} else {
				$searcher->setOrderBy('title', 'asc');
			}

			$total = $searcher->getCount();

			$per_page = 20;
			if ($this->request->isPartialRequest() == 'portal') {
				$per_page = 5;
			}

			$pageinfo = Numbers::getPaginationPages($total, $page, $per_page, 5);
			$limit = array(
				'offset' => ($pageinfo['curpage']-1) * $per_page,
				'max' => $per_page
			);

			$download_ids = $searcher->getMatches($limit);

			$downloads = $this->em->getRepository('DeskPRO:Download')->getByResultIds($download_ids);

			$comment_counts = array();
			if ($downloads) {
				$comment_counts = $this->em->getRepository('DeskPRO:DownloadCategory')
						->getCommentHelper()
						->countsOnCollection($downloads);
			}

		// No category, no results to display
		} else {
			$category = null;
			$category_path = null;

			$downloads = null;
			$comment_counts = null;
			$total = null;
			$pageinfo = null;
		}

		$category_counts = $structure->getDownloadCategoryCounts($this->person);
		$categories = $structure->getDownloadRootCategories();

		if ($category) {
			$category_children = $category->getChildren();
		} else {
			$category_children = $categories;
		}

		return $this->render('UserBundle:Downloads:browse.html.twig', array(
			'categories' => $categories,
			'category_children' => $category_children,
			'category' => $category,
			'category_counts' => $category_counts,
			'category_path' => $category_path,
			'downloads' => $downloads,
			'comment_counts' => $comment_counts,
			'num_results' => $total,
			'pageinfo' => $pageinfo,
			'section_counts' => $this->em->getRepository('DeskPRO:Download')->getSectionCounts($this->person),
		));
	}


	/**
	 * View a file
	 *
	 * @param  $article_id
	 */
	public function fileAction($slug)
	{
		$download = $this->em->getRepository('DeskPRO:Download')->getBySlug($slug);
		if (!$download) {
			return $this->renderStandardError('@user.downloads.file_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewDownload($download)) {
			return $this->renderLoginOrPermissionError();
		}

		// Auto-correct URL
		if ($slug != $download->getUrlSlug()) {
			return $this->redirectRoute('user_downloads_file', array('slug' => $download->getUrlSlug()), 301);
		}

		$category = $download->category;
		$category_path = $category->getTreeParents();

		$related_finder = new RelatedContentFinder($this->person, $download);
		$related_content = $related_finder->getRelatedEntities();

		$comments = null;
		$comments_widget = null;
		$comments_helper = Comments::create($download);
		if ($comments_helper) {
			$comments_widget = $comments_helper->getHtml();
		} else {
			$comments = $this->em->getRepository('DeskPRO:DownloadComment')->getDisplayComments($download, $this->person, $this->session->getVisitor());
		}

		$content_rating = new ContentRating($download, $this->person, $this->session->getVisitor());
		$content_rating->setRequest($this->request);
		$rating = $content_rating->getRating();

		if ($rating_log_search_id = $content_rating->getSearchLogId()) {
			$this->session->set('download.' . $download['id'], $rating_log_search_id);
		} elseif ($this->session->has('download.' . $download['id'])) {
			$rating_log_search_id = $this->session->get('download.' . $download['id']);
		} else {
			$rating_log_search_id = 0;
		}

		/** @var $structure \Application\DeskPRO\Publish\Structure */
		$structure = $this->container->getSystemService('publish_structure');
		$download->category->structure_helper = $structure;

		$tpl = 'UserBundle:Downloads:file.html.twig';
		if ($this->in->getString('_partial') == 'overlayWidget' || $this->in->getString('_partial') == 'overlaySuggest') {
			$tpl = 'UserBundle:Downloads:file-overlay.html.twig';
		}

		$this->container->getSystemService('view_log')->view($download);

		return $this->render($tpl, array(
			'rating' => $rating,
			'rating_log_search_id' => $rating_log_search_id,

			'comments_widget' => $comments_widget,
			'comments' => $comments,

			'download' => $download,
			'category' => $category,
			'category_path' => $category_path,

			'related_content' => $related_content
		));
	}


	/**
	 * @param $slug
	 */
	public function downloadFileAction($slug)
	{
		$download = $this->em->getRepository('DeskPRO:Download')->getBySlug($slug);
		if (!$download || (!$download->blob && !$download->fileurl)) {
			return $this->renderStandardError('@user.downloads.file_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewDownload($download)) {
			return $this->renderLoginOrPermissionError();
		}

		// Inc download count
		App::getDb()->executeUpdate("UPDATE downloads SET num_downloads = num_downloads + 1 WHERE id = ?", array($download->getId()));
		$this->container->getSystemService('view_log')->view($download, Entity\PageViewLog::ACTION_DOWNLOAD);

		if ($download->fileurl) {
			return $this->redirect($download->fileurl);
		}

		return $this->redirectRoute('serve_blob', array('blob_auth_id' => $download->blob->auth_id, 'filename' => $download->filename));
	}


	/**
	 * Submit a new comment
	 *
	 * @param  $download_id
	 */
	public function newCommentAction($download_id)
	{
		if ($this->container->getSetting('core.interact_require_login') && !$this->person->getId()) {
			return $this->forward('UserBundle:Login:index');
		}

		if (!$this->person->hasPerm('downloads.comment')) {
			return $this->renderLoginOrPermissionError();
		}

		$download = $this->em->getRepository('DeskPRO:Download')->find($download_id);
		if (!$download) {
			return $this->renderStandardError('@user.downloads.file_not_found', '@user.error.not-found', 404);
		}

		// Perm check
		if (!$this->person->PermissionsManager->UserPublishChecker->canViewDownload($download)) {
			return $this->renderLoginOrPermissionError();
		}

		$new_comment = new \Application\DeskPRO\Comments\NewComment(
			'Application\\DeskPRO\\Entity\\DownloadComment',
			$this->person,
			array('download' => $download)
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

			if (!$this->consumeRequest('newcomment_downloads') || $trap_fail) {
				return $this->redirectRoute('user_downloads_file', array(
					'slug' => $download->getUrlSlug(),
				));
			}

			$form->bindRequest($this->get('request'));

			if (!$validator->isValid($new_comment)) {
				$this->session->setFlash('comment_error', $validator->getErrors(true));
				return $this->redirectRoute('user_downloads_file', array(
					'slug' => $download->getUrlSlug(),
				));
			}

			if ($form->isValid()) {
				$comment = $new_comment->save();

				App::setSkipCache(true);

				if ($new_comment->require_login) {
					return $this->redirectRoute('user_newcomment_finishlogin', array(
						'comment_type' => 'article',
						'comment_id' => $comment->id,
					));
				}
			}
		}
		return $this->redirectRoute('user_downloads_file', array(
			'slug' => $download->getUrlSlug(),
		));
	}
}
