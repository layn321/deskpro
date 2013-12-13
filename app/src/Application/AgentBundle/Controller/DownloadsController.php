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
 * @subpackage AgentBundle
 */

namespace Application\AgentBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Download;
use Application\DeskPRO\Entity\DownloadComment;
use Application\DeskPRO\Searcher\DownloadSearch;
use Application\DeskPRO\UI\RuleBuilder;

use Application\DeskPRO\ContentRevision\Util as ContentRevisionUtil;
use Application\AgentBundle\Controller\Helper\DownloadResults;

use Application\DeskPRO\ContentSearch\RelatedContentFinder;
use Application\DeskPRO\Publish\RelatedContentUpdate;

use Orb\Util\Strings;
use Orb\Util\Arrays;
use Orb\Util\Util;
use Orb\Util\Numbers;

use FineDiff;

class DownloadsController extends AbstractController
{
	############################################################################
	# view
	############################################################################

	public function viewAction($download_id)
	{
		$download = $this->em->find('DeskPRO:Download', $download_id);

		if (!$download) {
			return $this->createNotFoundException();
		}

		$download_comments = $this->em->getRepository('DeskPRO:DownloadComment')->getComments($download);

		$related_finder = new RelatedContentFinder($this->person, $download);
		$related_content = $related_finder->getRelatedEntities();

		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.editdownload', $this->person->id);

		$sticky_search_words = $this->em->getRepository('DeskPRO:SearchStickyResult')->getWordsForObject($download);

		$rated_searches = $this->em->getRepository('DeskPRO:SearchLog')->getRatedSearchesFor('download', $download['id'], 'counted');

		$download_categories = $this->em->getRepository('DeskPRO:DownloadCategory')->getInHierarchy();

		$perms = array(
			'can_edit' => $this->person->PermissionsManager->PublishChecker->canEdit($download),
			'can_delete' => $this->person->PermissionsManager->PublishChecker->canDelete($download),
		);

		$user_view_count = $this->db->fetchColumn("
			SELECT COUNT(*)
			FROM page_view_log
			WHERE object_type = 2 AND object_id = ? AND view_action = 1 AND person_id IS NOT NULL
		", array($download->id));

		$user_download_count = $this->db->fetchColumn("
			SELECT COUNT(*)
			FROM page_view_log
			WHERE object_type = 2 AND object_id = ? AND view_action = 2 AND person_id IS NOT NULL
		", array($download->id));

		return $this->render('AgentBundle:Downloads:view.html.twig', array(
			'download'              => $download,
			'download_comments'     => $download_comments,
			'download_categories'   => $download_categories,
			'related_content'       => $related_content,
			'state'                 => $state,
			'sticky_search_words'   => $sticky_search_words,
			'rated_searches'        => $rated_searches,
			'perms'                 => $perms,
			'user_view_count'       => $user_view_count,
			'user_download_count'   => $user_download_count,
		));
	}

	public function infoAction($download_id)
	{
		$download = $this->em->find('DeskPRO:Download', $download_id);
		$blob = $download->blob;

		$data = array(
			'blob_id' => $blob['id'],
			'download_url' => $blob->getDownloadUrl(true),
			'filename' => $blob['filename'],
			'filesize_readable' => $blob->getReadableFilesize(),
			'permalink' => $download->getLink()
		);

		return $this->createJsonResponse($data);
	}

	public function viewRevisionsAction($download_id)
	{
		$download = $this->em->find('DeskPRO:Download', $download_id);

		return $this->render('AgentBundle:Downloads:view-revisions-tab.html.twig', array(
			'download' => $download,
		));
	}

	public function ajaxSaveLabelsAction($download_id)
	{
		$download = $this->em->find('DeskPRO:Download', $download_id);

		if (!$download || !$this->person->PermissionsManager->PublishChecker->canEdit($download)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$labels = $this->in->getCleanValueArray('labels', 'string', 'discard');

		$download->getLabelManager()->setLabelsArray($labels);

		$this->em->persist($download);
		$this->em->flush();

		return $this->createJsonResponse(array('success' => 1));
	}

	public function ajaxSaveCommentAction($download_id)
	{
		$download = $this->em->find('DeskPRO:Download', $download_id);

		$comment = new DownloadComment();
		$comment->download = $download;
		$comment->person = $this->person;
		$comment['content'] = $this->in->getString('content');
		$comment['status'] = 'visible';
		$comment['date_created']  = new \DateTime();

		if ($this->person->hasPerm('agent_publish.validate')) {
			$comment->is_reviewed = true;
		}

		$this->em->persist($comment);
		$this->em->flush();

		return $this->render('AgentBundle:Downloads:view-comment.html.twig', array(
			'comment' => $comment
		));
	}

	public function ajaxSaveAction($download_id)
	{
		$download = $this->em->find('DeskPRO:Download', $download_id);
		$rev = null;

		if (!$download) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$action = $this->in->getString('action');

		if ($action == 'delete') {
			if (!$this->person->PermissionsManager->PublishChecker->canDelete($download)) {
				return $this->createJsonResponse(array('success' => false));
			}
		} else {
			if (!$this->person->PermissionsManager->PublishChecker->canEdit($download)) {
				return $this->createJsonResponse(array('success' => false));
			}
		}

		$data = array('success' => 1);

		$this->em->beginTransaction();

		switch ($action) {

			case 'status':
				$download['status_code'] = $this->in->getString('status');
				if ($download['status_code'] == 'published' && !$this->person->hasPerm('agent_publish.validate')) {
					$download['status_code'] = 'hidden.validating';
				}
				break;

			case 'delete':
				$download['status_code'] = 'hidden.deleted';
				break;

			case 'title':
				$download['title'] = $this->in->getString('title');

				$rev = ContentRevisionUtil::findOrCreate($download, 'title', $this->person);
				$rev['title'] = $download['title'];

				break;

			case 'add-related':
				$updater = new RelatedContentUpdate($download);
				$updater->addRelated(
					$this->in->getString('content_type'),
					$this->in->getString('content_id')
				);
				break;

			case 'remove-related':
				$updater = new RelatedContentUpdate($download);
				$updater->removeRelated(
					$this->in->getString('content_type'),
					$this->in->getString('content_id')
				);
				break;

			case 'file':

				$rev = ContentRevisionUtil::findOrCreate($download, array('blob', 'title'), $this->person);

				if ($this->in->getUint('download.attach') && $blob = $this->em->getRepository('DeskPRO:Blob')->find($this->in->getUint('download.attach'))) {
        			$download->blob = $blob;

					$title = $this->in->getString('download.title');
					if (!$title) {
						$title = $blob->filename;
					}

					$download->title = $title;

					$blob->filename = $title;
					$this->em->persist($blob);

					$rev['title'] = $title;
					$rev->blob = $download->blob;
				} elseif ($this->in->getString('download.fileurl')) {
					$download->setFileUrl(
						$this->in->getString('download.fileurl'),
						$this->in->getString('download.filesize'),
						$this->in->getString('download.filename')
					);
				}

				$data['file_html'] = $this->renderView('AgentBundle:Downloads:view-fileinfo.html.twig', array(
					'download' => $download
				));

				break;

			case 'content':

				$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.editdownload', $this->person->id);

				$changed_content = false;
				if ($this->in->getString('content') != $download['content']) {
					$changed_content = true;
					$download['content'] = $this->in->getCleanValue('content', 'string', null, array('noclean' => true));
				}

				$data['content_html'] = $this->renderView('AgentBundle:Downloads:view-content-tab.html.twig', array(
					'download' => $download
				));

				$rev = ContentRevisionUtil::findOrCreate($download, array('content'), $this->person);

				if ($changed_content) {
					$rev['content'] = $download['content'];
				}

				break;

			case 'category':
				$cat = $this->em->find('DeskPRO:DownloadCategory', $this->in->getUint('category_id'));
				$download['category'] = $cat;
				$data['category_id'] = $cat['id'];
				break;
		}

		$this->em->persist($download);

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

	############################################################################
	# Compare revisions
	############################################################################

	public function compareRevisionsAction($rev_old_id, $rev_new_id)
	{
		$diff_info = ContentRevisionUtil::compareRevisions('DeskPRO:DownloadRevision', $rev_old_id, $rev_new_id);

		return $this->render('AgentBundle:Downloads:compare-revs.html.twig', array(
			'rendered_content_diff' => $diff_info['rendered_content_diff'],
			'rendered_title_diff'   => $diff_info['rendered_title_diff'],
			'new_blob'              => !empty($diff_info['new_blob']) ? $diff_info['new_blob'] : null,
			'old_blob'              => !empty($diff_info['old_blob']) ? $diff_info['old_blob'] : null,
		));
	}

	############################################################################
	# list
	############################################################################

	/**
	 * View a list of feedback
	 */
	public function listAction($category_id = 0)
	{
		$category = null;
		if ($category_id) {
			$category = $this->em->find('DeskPRO:DownloadCategory', $category_id);
		}

		$show_all = false;
		if (!$category) {
			$show_all = $this->in->getBool('all');
		}

		$result_helper = DownloadResults::newFromRequest($this, array(
			'category' => $category,
			'show_all' => $show_all
		));

		$page = $this->in->getUint('p');
		if (!$page) $page = 1;

		$results = $result_helper->getDownloadsForPage($page);
		$result_cache = $result_helper->getResultCache();

		$total_results = count($result_helper->getDownloadIds());
		$num_pages = ceil($total_results / 50);
		$showing_to = min(($page) * 50, $total_results);

		$display_fields = $this->person->getPref('agent.ui.download-filter-display-fields.0');
		if (!$display_fields) {
			$display_fields = array('author', 'date_created');
		}

		$tpl = 'AgentBundle:Downloads:filter.html.twig';
		if ($this->request->isPartialRequest()) {
			$tpl = 'AgentBundle:Downloads:filter-page.html.twig';
		}

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
				FROM download_category2usergroup
				WHERE category_id = ?
			", array($category->getId()));

			$cat_structure_data = $this->em->getRepository('DeskPRO:DownloadCategory')->getInHierarchy();;
			$cat_structure_data = Arrays::removeButKey($cat_structure_data, array('id' , 'title', 'children'), true, true);
			$cat_structure_data = Arrays::multiRenameKey($cat_structure_data, 'title', 'label');
			$cat_structure_data = Arrays::assocToNumericArary($cat_structure_data, 'children');
		}

		return $this->render($tpl, array(
			'results'            => $results,
			'comment_counts'     => $comment_counts,
			'result_id'          => $result_cache['id'],
			'cache'              => $result_cache,
			'display_fields'     => $display_fields,
			'category'           => $category,
			'cat_usergroups'     => $cat_usergroups,
			'cat_structure_data' => $cat_structure_data,
			'total_results'      => $total_results,
			'num_pages'          => $num_pages,
			'cur_page'           => $page,
			'showing_to'         => $showing_to,
		));
	}

	############################################################################
	# New download
	############################################################################

	public function newDownloadAction()
	{
		$download_categories = $this->em->getRepository('DeskPRO:DownloadCategory')->getFlatHierarchy();
		$state = $this->em->getRepository('DeskPRO:PersonPref')->getPrefForPersonId('agent.ui.state.newdownload', $this->person->id);

		return $this->render('AgentBundle:Downloads:newdownload.html.twig', array(
			'download_categories' => $download_categories,
			'state' => $state,
		));
	}

	public function newDownloadSaveAction()
	{
		$newdownload = new \Application\AgentBundle\Form\Model\NewDownload($this->person);

		$formType = new \Application\AgentBundle\Form\Type\NewDownload();
		$form = $this->get('form.factory')->create($formType, $newdownload);

		$this->db->executeUpdate("DELETE FROM people_prefs WHERE name = 'agent.ui.state.newdownload' AND person_id = ?", array($this->person->id));

		if ($this->get('request')->getMethod() == 'POST') {
			$form->bindRequest($this->get('request'));
			$form->isValid();

			$validator = new \Application\AgentBundle\Validator\NewDownloadValidator();
			if (!$validator->isValid($newdownload)) {
				return $this->createJsonResponse(array(
					'error' => true,
					'error_codes' => $validator->getErrorGroups()
				));
			}
			$newdownload->save();

			$download = $newdownload->getDownload();

			$this->em->getRepository('DeskPRO:PersonPref')->deletePrefForPersonId('agent.ui.state.newdownload', $this->person->id);

			return $this->createJsonResponse(array(
				'success' => true,
				'download_id' => $download['id']
			));
		} else {
			return $this->createJsonResponse(array(
				'success' => false,
			));
		}
	}
}
