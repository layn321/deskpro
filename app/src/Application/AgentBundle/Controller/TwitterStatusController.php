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

use Application\DeskPRO\Entity\TwitterAccount;
use Application\DeskPRO\Entity\TwitterAccountStatus;
use Application\DeskPRO\Entity\TwitterAccountStatusNote;

/**
 * Handles creating/editing of Twitter Accounts
 */
class TwitterStatusController extends AbstractController
{
	public function listAllAction($account_id, $group, $group_value)
	{
		$account = $this->_getAccountOr404($account_id);
		return $this->_renderList('agent_twitter_all_list', $account, 'inbox', $group, $group_value);
	}

	public function listUnassignedAction($account_id, $group, $group_value)
	{
		$account = $this->_getAccountOr404($account_id);
		$conditions = array('assigned' => false);
		return $this->_renderList('agent_twitter_unassigned_list', $account, 'inbox', $group, $group_value, $conditions);
	}

	public function listTeamAction($account_id, $group, $group_value)
	{
		$this->person->loadHelper('AgentTeam');

		$account = $this->_getAccountOr404($account_id);
		$conditions = array('agent_team' => $this->person->getAgentTeamIds());
		return $this->_renderList('agent_twitter_team_list', $account, 'all', $group, $group_value, $conditions);
	}

	public function listMineAction($account_id, $group, $group_value)
	{
		$account = $this->_getAccountOr404($account_id);
		$conditions = array('agent' => $this->person->id);
		return $this->_renderList('agent_twitter_mine_list', $account, 'all', $group, $group_value, $conditions);
	}

	public function listSentAction($account_id, $group, $group_value)
	{
		$account = $this->_getAccountOr404($account_id);
		return $this->_renderList('agent_twitter_sent_list', $account, 'sent', $group, $group_value);
	}

	public function listTimelineAction($account_id, $group, $group_value)
	{
		$account = $this->_getAccountOr404($account_id);
		return $this->_renderList('agent_twitter_timeline_list', $account, 'timeline', $group, $group_value);
	}

	protected function _getGroupConditions($group, $group_value)
	{
		if ($group === null || $group_value === null || $group === '' || $group_value === '') {
			return array();
		}

		switch ($group) {
			case 'agent': return array('agent' => $group_value);
			case 'team': return array('agent_team' => $group_value);
			case 'type': return array('type' => $group_value);
			default: return array();
		}

	}

	protected function _adjustPage($count, $page = null, $per_page = null)
	{
		if (!$per_page) {
			$per_page = TwitterAccount::DEFAULT_LIMIT;
		}
		if ($page === null) {
			$page = $this->in->getUint('page');
		}
		if (!$page) {
			$page = 1;
		}

		$start = ($page - 1) * $per_page;
		if ($start >= $count) {
			$page = ($count ? ceil($count / $per_page) : 1);
		}

		return $page;
	}

	/**
	 * @param string $route
	 * @param \Application\DeskPRO\Entity\TwitterAccount $account
	 * @param string $type
	 * @param mixed $group
	 * @param mixed $group_value
	 * @param array $conditions
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function _renderList($route, TwitterAccount $account, $type, $group = null, $group_value = null, array $conditions = array())
	{
		$sort_by_date = 'desc';
		$conditions = array_merge(array(
			'include_archived' => $this->in->getBool('include.archived'),
			'include_self' => $this->in->getBool('include.account'),
			'type' => $type
		), $this->_getGroupConditions($group, $group_value), $conditions);

		$page = $this->in->getUint('page');
		if (!$page) $page = 1;
		$per_page = TwitterAccount::DEFAULT_LIMIT;

		/** @var $statusRepository \Application\DeskPRO\EntityRepository\TwitterAccountStatus */
		$statusRepository = $this->em->getRepository('DeskPRO:TwitterAccountStatus');

		$total_count = $statusRepository->countTimelineForAccount($account, $conditions);
		$page = $this->_adjustPage($total_count, $page, $per_page);

		$statuses = $statusRepository->getTimelineForAccount($account, $conditions, $sort_by_date, $page);

		$this->person->setPreference('agent.ui.last_twitter_account', $account->id);

		$parameters = array(
			'twitter_list_route' => $route,
			'group' => $group,
			'group_value' => $group_value,
			'account' => $account,
			'statuses' => $statuses,
			'person' => $this->getPerson(),
			'sort_by_date' => $sort_by_date,
			'total_count' => $total_count,
			'per_page' => $per_page,
			'page' => $page,
			'showing_to' => min($total_count, $page * $per_page)
		);

		if ($this->in->getBool('last')) {
			$parameters['account_status'] = end($statuses);
			return $this->render('AgentBundle:TwitterStatus:list-row.html.twig', $parameters);
		}

		// check if is partial
		if ($this->in->getBool('partial')) {
			return $this->render('AgentBundle:TwitterStatus:part-status.html.twig', $parameters);
		}

		// render html response
		return $this->render('AgentBundle:TwitterStatus:list.html.twig', $parameters);
	}

	/**
	 * @param integer $id
	 * @return \Application\DeskPRO\Entity\TwitterAccountStatus
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getAccountStatusOr404($id, $check_perm = '')
	{
		if (preg_match('/^status:(\d+)$/', $id, $match)) {
			$twitter_status = $this->em->getRepository('DeskPRO:TwitterStatus')->find($match[1]);
			if (!$twitter_status) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(sprintf('There is no status with ID "%d"', $match[1]));
			}

			$account_id = $this->person->getPref('agent.ui.last_twitter_account');
			if ($account_id) {
				$account = $this->em->getRepository('DeskPRO:TwitterAccount')->find($account_id);
			} else {
				$account = $this->person->getTwitterAccounts()->first();
			}

			if (!$account || !$account->hasPerson($this->person)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}

			$status = $this->em->getRepository('DeskPRO:TwitterAccountStatus')->getByTwitterStatusAndAccount($twitter_status->id, $account);
			if (!$status) {
				$status = new TwitterAccountStatus();
				$status->status = $twitter_status;
				$status->account = $account;
				$status->status_type = null; // this ensures it only appears where requested

				App::getOrm()->persist($status);
				App::getOrm()->flush();
			}
		} else {
			$status = $this->em->getRepository('DeskPRO:TwitterAccountStatus')->find($id);
			if (!$status) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(sprintf('There is no status with ID "%d"', $id));
			}

			$account = $status->account;
			if (!$account || !$account->hasPerson($this->person)) {
				throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(sprintf('There is no status with ID "%d"', $id));
			}
		}

		// todo: more fine grained permissions?

		return $status;
	}

	/**
	 * Check account security.
	 *
	 * @param integer $id The account id.
	 * @return \Application\DeskPRO\Entity\TwitterAccount
	 * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	protected function _getAccountOr404($id)
	{
		// check if account exists
		$account = $this->em->getRepository('DeskPRO:TwitterAccount')->find($id);
		if (!$account || !$account->hasPerson($this->person)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(sprintf('There is no account with ID "%d"', $id));
		}

		return $account;
	}

	public function tweetOverlayAction()
	{
		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'));

		$parents = array();
		$status = $account_status->status->in_reply_to_status;
		$i = 0;
		while ($status && $i < 10) {
			$parents[] = $status;
			$status = $status->in_reply_to_status;
			$i++;
		}

		return $this->render('AgentBundle:TwitterStatus:status-overlay.html.twig', array(
			'account_status' => $account_status,
			'parents' => array_reverse($parents)
		));
	}

	public function ajaxMassSaveAction()
	{
		$account_status_ids = $this->in->getCleanValueArray('result_ids', 'int', 'discard');
		$account_statuses = $this->em->getRepository('DeskPRO:TwitterAccountStatus')->getByIds($account_status_ids);
		$action = $this->in->getString('action');

		$twitter_service = new \Application\DeskPRO\Service\Twitter();

		foreach ($account_statuses AS $account_status) {
			/** @var $account_status TwitterAccountStatus */
			if (!$account_status->account->hasPerson($this->person)) {
				continue;
			}

			switch ($action) {
				case 'retweet':
					if (!$account_status->retweeted && $account_status->canRetweet()) {
						$twitter_service->sendRetweet($account_status->account, $account_status);
					}
					break;

				case 'unretweet':
					if ($account_status->retweeted) {
						$twitter_service->unsendRetweet($account_status->account, $account_status);
					}
					break;

				case 'reply':
					$text = $this->in->getString('text');
					$type = $this->in->getValue('type');
					$split = $this->in->getBool('split');
					if (strlen($text)) {
						if ($type == 'public' && strpos($text, '@'.$account_status->status->user->screen_name) === false) {
							$text = '@' . $account_status->status->user->screen_name . ' ' . $text;
						}

						$twitter_service = new \Application\DeskPRO\Service\Twitter();
						$response = $twitter_service->sendAccountMessage($type, $text, $split, $account_status->account, $account_status);

						if ($response['new_account_statuses']) {
							foreach ($response['new_account_statuses'] AS $new_account_status) {
								$reply_html = $this->renderView('AgentBundle:TwitterStatus:reply-li.html.twig', array(
									'account_status' => $account_status,
									'reply' => $new_account_status
								));
								$html[] = $reply_html;

								$this->_insertUpdatedTweetClientMessage($account_status,
									array('reply_added_html' => $reply_html, 'reply_added_id' => $new_account_status->id)
								);
							}

							$this->_updateArchiveStatus($account_status, true, false);
						}
					}
					break;

				case 'favorite':
					if (!$account_status->is_favorited) {
						$twitter_service->setFavorite($account_status->account, $account_status, true);
					}
					break;

				case 'unfavorite':
					if ($account_status->is_favorited) {
						$twitter_service->setFavorite($account_status->account, $account_status, false);
					}
					break;

				case 'archive':
					$this->_updateArchiveStatus($account_status, true, false);
					break;

				case 'unarchive':
					$this->_updateArchiveStatus($account_status, false, false);
					break;

				case 'assign':
					$this->_updateStatusAssignment($account_status, $this->in->getValue('assign'), false);
					break;
			}

			$this->em->persist($account_status);
		}

		$this->em->flush();

		return $this->createJsonResponse(array(
			'success' => true
		));
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxSaveNoteAction()
	{
		$success = false;
		$error = null;
		$html = null;

		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'), 'note');

		$text = $this->in->getValue('text');
		$text = \Orb\Util\Strings::prepareWysiwygHtml($text);

		$notify_agent_ids = array();
		preg_match_all('/<span[^>]+data-notify-agent-id="(\d+)"/i', $this->in->getString('text'), $matches, PREG_SET_ORDER);
		foreach ($matches AS $match) {
			$notify_agent_ids[] = $match[1];
		}

		$text = strip_tags($text);
		$text = str_replace('&nbsp;', ' ', $text);
		$text = html_entity_decode($text);

		$note = new TwitterAccountStatusNote();
		$note['account_status'] = $account_status;
		$note['person'] = $this->person;
		$note['text'] = $text;

		$em = App::getOrm();
		$em->persist($note);
		$em->flush();

		$success = true;

		$html = $this->renderView('AgentBundle:TwitterStatus:note-li.html.twig', array(
			'account_status' => $account_status,
			'note' => $note
		));

		$this->_insertUpdatedTweetClientMessage($account_status,
			array('note_added_html' => $html, 'note_added_id' => $note->id)
		);

		if ($notify_agent_ids) {
			$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();
			$agent_chat = new \Application\DeskPRO\Chat\AgentChat($this->person, $this->session->getEntity());

			$notify_agent_ids = array_unique($notify_agent_ids);
			foreach ($notify_agent_ids AS $k => $agent_id) {
				if ($agent_id == $this->person->id || !isset($agents[$agent_id])) {
					unset($notify_agent_ids[$k]);
				}
			}
			if ($notify_agent_ids) {
				$notify_text = $this->person->getDisplayName() . " alerted you in a note for {{tw-$account_status->id}}: " . $account_status->status->getClippedText(70);
				$agent_chat->sendAgentMessage($notify_text, $notify_agent_ids);
			}
		}

		return $this->createJsonResponse(array(
			'success' => $success,
			'error' => $error,
			'html' => $html
		));
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxSaveRetweetAction()
	{
		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'), 'retweet');
		$account = $account_status->account;

		$success = true;
		$error = null;
		$retweet = false;
		$html = array();
		$archived = false;

		if ($this->in->getBool('retweet')) {
			if (!$account_status->retweeted) {
				$twitter_service = new \Application\DeskPRO\Service\Twitter();
				$output = $twitter_service->sendRetweet($account, $account_status);
				$success = $output['success'];
				$error = $output['error'];
				if (!$error) {
					$retweet = true;
				}
			}
			$archived = true;
		} else {
			$text = $this->in->getString('text');
			if (strlen($text)) {
				$twitter_service = new \Application\DeskPRO\Service\Twitter();
				$output = $twitter_service->sendAccountMessage('public', $text, true, $account, $account_status);
				$success = $output['success'];
				$error = $output['error'];

				if ($output['new_account_statuses']) {
					foreach ($output['new_account_statuses'] AS $new_account_status) {
						$reply_html[] = $this->renderView('AgentBundle:TwitterStatus:reply-li.html.twig', array(
							'account_status' => $account_status,
							'reply' => $new_account_status
						));
						$html[] = $reply_html;

						$this->_insertUpdatedTweetClientMessage($account_status,
							array('reply_added_html' => $reply_html, 'reply_added_id' => $new_account_status->id)
						);
					}

					$archived = true;
					$this->_updateArchiveStatus($account_status);
				}
			}
		}

		return $this->createJsonResponse(array(
			'success' => $success,
			'error' => $error,
			'retweet' => $retweet,
			'html' => $html,
			'archived' => $archived
		));
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxSaveUnretweetAction()
	{
		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'), 'retweet');
		$account = $account_status->account;

		$twitter_service = new \Application\DeskPRO\Service\Twitter();
		$output = $twitter_service->unsendRetweet($account, $account_status);

		$success = $output['success'];
		$error = $output['error'];

		return $this->createJsonResponse(array('success' => $success, 'error' => $error));
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxSaveReplyAction()
	{
		$success = false;
		$error = null;
		$html = array();

		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'));
		$account = $account_status->account;

		$text = $this->in->getString('text');
		$type = $this->in->getValue('type');
		$split = $this->in->getBool('split');
		$archived = false;
		if (strlen($text)) {
			if ($type == 'public' && strpos($text, '@'.$account_status->status->user->screen_name) === false) {
				$text = '@' . $account_status->status->user->screen_name . ' ' . $text;
			}

			$twitter_service = new \Application\DeskPRO\Service\Twitter();
			$response = $twitter_service->sendAccountMessage($type, $text, $split, $account, $account_status);

			$success = $response['success'];
			$error = $response['error'];
			if ($response['new_account_statuses']) {
				foreach ($response['new_account_statuses'] AS $new_account_status) {
					$reply_html = $this->renderView('AgentBundle:TwitterStatus:reply-li.html.twig', array(
						'account_status' => $account_status,
						'reply' => $new_account_status
					));
					$html[] = $reply_html;

					$this->_insertUpdatedTweetClientMessage($account_status,
						array('reply_added_html' => $reply_html, 'reply_added_id' => $new_account_status->id)
					);
				}

				$archived = true;
				$this->_updateArchiveStatus($account_status);
			}
		} else {
			$error = 'No text specified.';
		}

		return $this->createJsonResponse(array(
			'success' => $success,
			'html' => $html,
			'error' => $error,
			'archived' => $archived
		));
	}

	public function ajaxSaveArchiveAction()
	{
		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'), 'archive');

		$old_archived = $account_status->is_archived;

		$account_status['is_archived'] = $this->in->getBool('archive');

		$this->em->persist($account_status);
		$this->em->flush();

		if ($old_archived != $account_status->is_archived) {
			$this->_insertUpdatedTweetClientMessage($account_status,
				array('change_archived' => $account_status->is_archived)
			);
		}

		return $this->createJsonResponse(array('success' => true));
	}

	public function ajaxSaveDeleteAction()
	{
		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'), 'delete');

		$twitter_service = new \Application\DeskPRO\Service\Twitter();
		$output = $twitter_service->deleteStatus($account_status->account, $account_status);

		$success = $output['success'];
		$error = $output['error'];

		return $this->createJsonResponse(array('success' => $success, 'error' => $error));
	}

	public function ajaxSaveEditAction()
	{
		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'), 'edit');

		if (!$account_status->status->long) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if ($this->in->getBool('process')) {
			$text = $this->in->getString('text');

			if (strlen($text)) {
				$account_status->status->long->text = $text;
				$this->em->persist($account_status->status->long);
				$this->em->flush();

				$this->_insertUpdatedTweetClientMessage($account_status,
					array('edited_html' => $account_status->status->long->parsed_text)
				);
			}

			return $this->createJsonResponse(array(
				'success' => true,
				'error' => null,
				'parsed_text' => $account_status->status->long->getParsedText())
			);
		} else {
			return $this->render('AgentBundle:TwitterStatus:edit-overlay.html.twig', array(
				'long' => $account_status->status->long
			));
		}
	}

	public function ajaxSaveFavoriteAction()
	{
		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'), 'favorite');
		$account = $account_status->account;

		$twitter_service = new \Application\DeskPRO\Service\Twitter();
		$output = $twitter_service->setFavorite($account, $account_status, $this->in->getBool('favorite'));

		$success = $output['success'];
		$error = $output['error'];

		return $this->createJsonResponse(array('success' => $success, 'error' => $error));
	}

	public function ajaxSaveAssignAction()
	{
		$account_status = $this->_getAccountStatusOr404($this->in->getValue('account_status_id'), 'assign');

		$this->_updateStatusAssignment($account_status, $this->in->getValue('assign'));

		return $this->createJsonResponse(array('success' => true));
	}

	protected function _insertUpdatedTweetClientMessage(TwitterAccountStatus $account_status, array $changes)
	{
		$twitter = new \Application\DeskPRO\Service\Twitter();
		return $twitter->insertUpdatedTweetClientMessage($account_status, $changes);
	}

	protected function _updateArchiveStatus(TwitterAccountStatus $account_status, $value = true, $flush = true)
	{
		if ($account_status->status_type === 'sent') {
			return;
		}

		$old_archived = $account_status->is_archived;

		$account_status->is_archived = $value;

		if ($flush) {
			$this->em->persist($account_status);
			$this->em->flush();
		}

		if ($old_archived != $account_status->is_archived) {
			$this->_insertUpdatedTweetClientMessage($account_status,
				array('change_archived' => $account_status->is_archived)
			);
		}
	}

	protected function _updateStatusAssignment(TwitterAccountStatus $account_status, $assign, $flush = true)
	{
		$old_agent_id = 0;
		$old_agent_team_id = 0;

		if ($account_status->agent) {
			$old_assign = 'agent:' . $account_status->agent->id;
			$old_agent_id = $account_status->agent->id;;
		} else if ($account_status->agent_team) {
			$old_assign = 'agent_team:' . $account_status->agent_team->id;
			$old_agent_team_id = $account_status->agent_team->id;;
		} else {
			$old_assign = '';
		}

		if ($assign) {
			list($type, $id) = explode(':', $this->in->getValue('assign'));
			if ($type == 'agent') {
				$account_status->setAgentId($id);
			} else {
				$account_status->setAgentTeamId($id);
			}
		} else {
			$account_status->agent = null;
			$account_status->agent_team = null;
		}

		if ($flush) {
			$this->em->persist($account_status);
			$this->em->flush();
		}

		if ($account_status->agent) {
			$new_assignment = 'agent:' . $account_status->agent->getId();
		} else if ($account_status->agent_team) {
			$new_assignment = 'agent_team:' . $account_status->agent_team->getId();
		} else {
			$new_assignment = '';
		}

		if ($new_assignment != $old_assign) {
			$this->_insertUpdatedTweetClientMessage($account_status,
				array(
					'change_assignment' => $new_assignment,
					'assignment_picture' => ($account_status->agent
						? $account_status->agent->getPictureUrl(16)
						: $this->generateUrl('serve_default_picture', array('s' => 16, 'size-fit' => 1), true)
					),
					'old_assignment' => $old_assign,
					'old_agent_id' => $old_agent_id,
					'old_agent_team_id' => $old_agent_team_id
				)
			);

			if ($new_assignment) {
				$notify = new \Application\DeskPRO\Notifications\TweetAssignNotification($account_status);
				$notify->send();
			}
		}
	}
}
