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
 * @category Controllers
 */

namespace Application\UserBundle\Controller;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\PortalPageDisplay;
use Application\DeskPRO\PageDisplay\Page\PortalPage;

use Application\UserBundle\Controller\Helper\ContentRating;

class PortalController extends AbstractController
{
    public function portalAction()
    {
		$tpl_globals = $this->container->get('templating.globals');

		$tabs_order     = $tpl_globals->getVariable('portal_tabs_order');
		$admin_controls = $tpl_globals->getVariable('admin_portal_controls');

		if (!$admin_controls) {
			/** @var $portal_page \Application\DeskPRO\PageDisplay\Page\PortalPage */
			$portal_page = $this->container->get('deskpro.user_portal_page');

			// The user cant see anything on the page based on reg settings
			if (!$portal_page->getSectionDisplayItems('portal')) {
				if ($this->person->isGuest() && (!$this->person->hasPerm('tickets.use') || $this->container->getSetting('core.user_mode') == 'require_reg' || $this->container->getSetting('core.user_mode') == 'require_reg_agent_validation')) {
					return $this->redirectRoute('user_login');
				} else {
					return $this->redirectRoute('user_tickets_new');
				}
			}
		}

		do {
			$page = array_shift($tabs_order);
			$ctrl = null;
			switch ($page) {
				case 'news':
					if ($admin_controls || ($this->container->getSetting('user.portal_tab_news') && $this->person->hasPerm('news.use'))) {
						$ctrl = 'UserBundle:News:browse';
					}
					break;
				case 'articles':
					if ($admin_controls || ($this->container->getSetting('user.portal_tab_articles') && $this->person->hasPerm('articles.use'))) {
						$ctrl = 'UserBundle:Articles:browse';
					}
					break;
				case 'feedback':
					if ($admin_controls || ($this->container->getSetting('user.portal_tab_feedback') && $this->person->hasPerm('feedback.use'))) {
						$ctrl = 'UserBundle:Feedback:filter';
					}
					break;
				case 'downloads':
					if ($admin_controls || ($this->container->getSetting('user.portal_tab_downloads') && $this->person->hasPerm('downloads.use'))) {
						$ctrl = 'UserBundle:Downloads:browse';
					}
					break;
				case 'newticket':
					if ($admin_controls || ($this->container->getSetting('user.portal_tab_tickets') && $this->person->hasPerm('tickets.use'))) {
						$ctrl = 'UserBundle:NewTicket:new';
					}
					break;
			}
		} while (!$ctrl && $tabs_order);

		if (!$ctrl) {
			if ($this->session->getFlash('new_ticket')) {
				return $this->redirectRoute('user_tickets_new_thanks_simple', array('ticket_ref' => $this->session->getFlash('new_ticket')));
			}

			if ($this->person->isGuest() && (!$this->person->hasPerm('tickets.use') || $this->container->getSetting('core.user_mode') == 'require_reg' || $this->container->getSetting('core.user_mode') == 'require_reg_agent_validation')) {
				return $this->redirectRoute('user_login');
			} elseif ($this->person->hasPerm('tickets.use')) {
				return $this->redirectRoute('user_tickets_new');
			} else {
				return $this->redirectRoute('user_profile');
			}
		}

		$tpl_globals->setVariable('is_homepage', true);

		return $this->forward($ctrl);
    }

	public function saveRatingAction($object_type, $object_id)
	{
		$entity_name = 'DeskPRO:' . ucfirst($object_type);
		$content_object = $this->em->find($entity_name, $object_id);

		if (!$content_object) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$perm_name = false;
		switch ($entity_name) {
			case 'DeskPRO:Article':  $perm_name = 'articles.rate'; break;
			case 'DeskPRO:Download': $perm_name = 'downloads.rate'; break;
			case 'DeskPRO:News':     $perm_name = 'news.rate'; break;
			case 'DeskPRO:Feedback': $perm_name = 'feedback.rate'; break;
		}

		if ($perm_name) {
			if (!$this->person->hasPerm($perm_name)) {
				return $this->renderLoginOrPermissionError();
			}
		}

		if ($content_object instanceof \Application\DeskPRO\Entity\Feedback) {
			if ($content_object == 'closed') {
				return $this->renderStandardError('@user.feedback.voting_closed', '@user.feedback.voting_closed-explain');
			}
		}

		$content_rating = new ContentRating($content_object, $this->person, $this->session->getVisitor());
		$content_rating->setRequest($this->request);

		$rating = $this->in->getInt('rating');

		$this->em->beginTransaction();
		$content_rating->setRating(
			$rating,
			$this->in->getUint('log_search_id')
		);
		$this->em->flush();
		$this->em->commit();

		if ($this->session->has('preticket_id')) {
			$preticket = $this->em->find('DeskPRO:PreticketContent', $this->session->get('preticket_id'));
			$this->session->remove('preticket_id');

			if ($preticket) {
				if ($rating < 1) {
					$unsolved = $preticket->unsolved_content;
					$unsolved[] = array($object_type, $object_id);

					$preticket->unsolved_content = $unsolved;
				} else {
					$preticket->is_solved   = true;
					$preticket->object_type = $object_type;
					$preticket->object_id   = $object_id;
				}

				$this->em->beginTransaction();
				$this->em->persist($preticket);
				$this->em->flush();
				$this->em->commit();
			}
		}

		return $this->redirect($content_object->getLink());
	}

	public function newCommentFinishLoginAction($comment_type, $comment_id)
	{
		if ($this->person->isGuest()) {
			$return_url = $this->generateUrl('user_newcomment_finishlogin', array(
				'comment_type' => $comment_type,
				'comment_id' => $comment_id
			));
			return $this->redirectRoute('user_login', array('return' => $return_url));
		}

		switch ($comment_type) {
			case 'article': $entity = 'DeskPRO:ArticleComment'; break;
			case 'news': $entity = 'DeskPRO:NewsComment'; break;
			case 'download': $entity = 'DeskPRO:DownloadComment'; break;
			case 'feedback': $entity = 'DeskPRO:FeedbackComment'; break;
			default:
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
		}

		$comment = $this->em->find($entity, $comment_id);
		if (!$comment) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
		}

		$comment->status = 'validating';
		$this->em->getConnection()->beginTransaction();

		try {
			$this->em->persist($comment);
			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}

		return $this->redirect($comment->getObject()->getLink());
	}
}
