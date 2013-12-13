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
 * @category Tickets
 */

namespace Application\DeskPRO\People\PermissionChecker;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\Article;
use Application\DeskPRO\Entity\Feedback;
use Application\DeskPRO\Entity\News;
use Application\DeskPRO\Entity\Download;
use \Application\DeskPRO\HttpFoundation\Session as HttpSession;

use Orb\Util\Arrays;

class UserPublishChecker extends AbstractChecker
{
	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @param \Application\DeskPRO\Entity\Article $article
	 * @return bool
	 */
	public function canViewArticle(Article $article)
	{
		if (!$this->person->hasPerm('articles.use')) {
			return false;
		}

		// Only agents can view non-published
		if ($article->status != 'published' && $article->status != 'archived' && !$this->person->is_agent) {
			return false;
		}

		$perms = $this->person->PermissionsManager->ArticleCategories->getAllowedCategories();
		$perms = array_flip($perms);

		if (!count($article->categories)) {
			return true;
		}

		foreach ($article->categories as $cat) {
			if (isset($perms[$cat->id])) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @param \Application\DeskPRO\Entity\News $news
	 * @return bool
	 */
	public function canViewNews(News $news)
	{
		if (!$this->person->hasPerm('news.use')) {
			return false;
		}

		// Only agents can view non-published
		if ($news->status != 'published' && $news->status != 'archived' && !$this->person->is_agent) {
			return false;
		}

		if (!$news->category || $this->person->PermissionsManager->NewsCategories->isCategoryAllowed($news->category->getId())) {
			return true;
		}

		return false;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Download $download
	 * @return bool
	 */
	public function canViewDownload($download)
	{
		if (!$this->person->hasPerm('downloads.use')) {
			return false;
		}

		// Only agents can view non-published
		if ($download->status != 'published' && $download->status != 'archived' && !$this->person->is_agent) {
			return false;
		}

		if (!$download->category || $this->person->PermissionsManager->DownloadCategories->isCategoryAllowed($download->category->getId())) {
			return true;
		}

		return false;
	}


	/**
	 * @param \Application\DeskPRO\Entity\Feedback $feedback
	 * @return bool
	 */
	public function canViewFeedback(Feedback $feedback, HttpSession $user_session = null)
	{
		if (!$this->person->hasPerm('feedback.use')) {
			return false;
		}

		// Only agents can view non-published
		if ($feedback->status == 'hidden' && !$this->person->is_agent) {

			// But still show the user their own submitted feedback
			if ($feedback->person && $feedback->person->getId() == $this->person->getId()) {
				return true;
			}
			if ($user_session) {
				$submitted_feedback = $user_session->get('submitted_feedback');
				if (is_array($submitted_feedback) && in_array($feedback->getId(), $submitted_feedback)) {
					return true;
				}
			}

			return false;
		}

		return true;
	}
}
