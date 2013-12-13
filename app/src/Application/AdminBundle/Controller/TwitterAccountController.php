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
 * @subpackage AdminBundle
 */

namespace Application\AdminBundle\Controller;

use Application\DeskPRO\App;

use Application\DeskPRO\Entity\TwitterAccount;
use Application\DeskPRO\Entity\TwitterStatus;
use Application\DeskPRO\Entity\TwitterStatusMention;
use Application\DeskPRO\Entity\TwitterStatusTag;
use Application\DeskPRO\Entity\TwitterStatusUrl;
use Application\DeskPRO\Entity\TwitterUser;

use Application\AdminBundle\Form\EditTwitterAccountType;

/**
 * Handles creating/editing of Twitter Accounts
 */
class TwitterAccountController extends AbstractController
{
	/**
	 * List of Twitter accounts.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction()
	{
		$accounts = $this->em->getRepository('DeskPRO:TwitterAccount')->getAll();
		if (!$accounts && !App::getSetting('core.twitter_agent_consumer_key')) {
			return $this->redirectRoute('admin_twitter_apps');
		}

		$verified = array();
		$errors = array();
		foreach ($accounts as $account) {
			$ok = $account->verifyCredentials($message, $code);
			$verified[$account['id']] = $ok;
			if (!$ok) {
				$errors[$account['id']] = $message;
			}
		}

		return $this->render('AdminBundle:TwitterAccount:list.html.twig', array(
			'accounts' => $accounts,
			'verified' => $verified,
			'errors' => $errors
		));
	}

	public function appsAction()
	{
		if (App::getConfig('twitter.agent_consumer_key') || defined('DPC_IS_CLOUD')) {
			return $this->redirectRoute('admin_twitter_accounts');
		}

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken();

			$settings = App::getContainer()->getSettingsHandler();
			$settings->setSetting('core.twitter_agent_consumer_key', $this->in->getString('agent_consumer_key'));
			$settings->setSetting('core.twitter_agent_consumer_secret', $this->in->getString('agent_consumer_secret'));
			$settings->setSetting('core.twitter_user_consumer_key', $this->in->getString('user_consumer_key'));
			$settings->setSetting('core.twitter_user_consumer_secret', $this->in->getString('user_consumer_secret'));

			return $this->redirectRoute('admin_twitter_accounts');
		}

		return $this->render('AdminBundle:TwitterAccount:apps.html.twig', array(
			'accounts' => $this->em->getRepository('DeskPRO:TwitterAccount')->findAll()
		));
	}

	public function setCleanupAction()
	{
		$this->ensureRequestToken();

		$time = $this->in->getUint('time');
		App::getContainer()->getSettingsHandler()->setSetting('core.twitter_auto_remove_time', $time);

		return $this->redirectRoute('admin_twitter_accounts');
	}

	/**
	 * Request permission from Twitter for DeskPRO application.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function newAction()
	{
		$api = \Application\DeskPRO\Service\Twitter::getAgentTwitterApi();

		if ($this->in->getBool('start')) {
			$api->setCallback($this->generateUrl('admin_twitter_accounts_new', array(), true));
			try {
				$url = $api->getAuthorizationUrl();
			} catch (\EpiOAuthException $e) {
				return $this->renderStandardError($e->getMessage(), "Error " . $e->getCode());
			}
			return $this->redirect($url);
		}

		if ($this->in->getString('denied')) {
			return $this->renderStandardError("Access denied", "Error");
		}

		if (!$this->in->getString('oauth_token')) {
			return $this->renderStandardError("Missing oAuth token", "Error");
		}

		$api->setToken($this->in->getString('oauth_token'));
		$access = $api->getAccessToken();
		if ($access->oauth_token && $access->oauth_token_secret) {
			$api->setToken($access->oauth_token, $access->oauth_token_secret);

			// check if Twitter user already exists
			$twitter_user = $api->get_usersShow(array('screen_name' => $access->screen_name));
			$user = $this->em->getRepository('DeskPRO:TwitterUser')->find($twitter_user->id_str);
			if (!$user) {
				$user = TwitterUser::createFromJson($twitter_user);
				$this->em->persist($user);
			}

			$em = $this->em;
			$em->persist($user);
			$existed = true;

			// check if Twitter account already exists
			$account = $em->getRepository('DeskPRO:TwitterAccount')->findOneByUser($user['id']);
			if (!$account) {
				$account = new TwitterAccount();
				$account['user'] = $user;
				$existed = false;
			}

			// update OAuth credentials, regardless if its a new or an old account
			$account['oauth_token'] = $access->oauth_token;
			$account['oauth_token_secret'] = $access->oauth_token_secret;

			// add person to account
			if (!$account->hasPerson($this->person)) {
				$account['persons']->add($this->person);
			}

			$em->persist($account);
			$em->flush();

			$followers = $this->_loadUsers($account, 'followers');
			$em->flush();

			$this->_loadUsers($account, 'friends');
			$em->flush();

			if ($followers) {
				// look up the info for the most recent 100 in bulk.
				// most recent are last
				$recent_followers = array_slice($followers, -100, null, true);
				$recent_ids = array();
				foreach ($recent_followers AS $recent_follower) {
					if ($recent_follower->user->is_stub) {
						$recent_ids[] = $recent_follower->user->id;
					}
				}

				if ($recent_ids) {
					try {
						$response = $api->post_usersLookup(array(
							'user_id' => implode(',', $recent_ids)
						));
						foreach ($response AS $user) {
							if (isset($recent_followers[$user->id_str])) {
								$entity = $recent_followers[$user->id_str]->user;
								$entity->ensureDefaultPropertyChangedListener();
								$entity->updateFromJson($user);
								$em->persist($entity);
							}
						}
					} catch (\EpiTwitterException $e) {}

					$em->flush();
				}
			}

			$this->_accountCreate($account, $existed);
		}

		return $this->redirectRoute('admin_twitter_accounts');
	}

	protected function _accountCreate(TwitterAccount $account, $existed)
	{
	}

	protected function _loadUsers(TwitterAccount $account, $type)
	{
		$ids = array();
		$api = $account->getTwitterApi();
		$cursor = -1;

		if ($type == 'followers') {
			$api_call = 'get_followersIds';
			$repository_name = 'DeskPRO:TwitterAccountFollower';
			$entity_class = '\\Application\\DeskPRO\\Entity\\TwitterAccountFollower';
		} else {
			$api_call = 'get_friendsIds';
			$repository_name = 'DeskPRO:TwitterAccountFriend';
			$entity_class = '\\Application\\DeskPRO\\Entity\\TwitterAccountFriend';
		}

		do {
			try {
				$response = $api->$api_call(array(
					'user_id' => $account->user->id,
					'stringify_ids' => 'true',
					'cursor' => $cursor
				));
			} catch (\EpiTwitterException $e) {
				break;
			}
			if (!empty($response->ids)) {
				$ids = array_merge($ids, $response->ids);
			}
			if (!empty($response->next_cursor)) {
				$cursor = $response->next_cursor_str;
			}
		} while (!empty($response->next_cursor));

		if (!$ids) {
			return array();
		}

		if ($account->id) {
			$existing_for_type = $this->em->getRepository($repository_name)->getByAccountAndUsers(
				$account, $ids
			);
		} else {
			$existing_for_type = array();
		}

		$output = array();

		$existing_users = $this->em->getRepository('DeskPRO:TwitterUser')->getByIds($ids);
		foreach (array_reverse($ids) AS $key => $id) {
			if (!isset($existing_users[$id])) {
				$new_user = \Application\DeskPRO\Entity\TwitterUser::createStub($id);
				$this->em->persist($new_user);
			} else {
				$new_user = $existing_users[$id];
			}

			if (!isset($existing_for_type[$id])) {
				$new_for_type = new $entity_class();
				$new_for_type->user = $new_user;
				$new_for_type->account = $account;
				if ($type == 'followers') {
					$new_for_type->is_archived = true;
				}
			} else {
				$new_for_type = $existing_for_type[$id];
			}

			if ($type == 'followers') {
				$new_for_type->follow_order = $key;
			}

			$this->em->persist($new_for_type);

			$output[$id] = $new_for_type;
		}

		return $output;
	}

	public function editAction($account_id)
	{
		$account = $this->em->find('DeskPRO:TwitterAccount', $account_id);
		if (!$account) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if ($this->in->getBool('process')) {
			$this->ensureRequestToken();

			$db = App::getDb();
			$db->delete('twitter_accounts_person', array(
				'account_id' => $account->id
			));
			foreach ($this->in->getCleanValueArray('agents', 'uint') AS $agent_id) {
				$db->insert('twitter_accounts_person', array(
					'account_id' => $account->id,
					'person_id' => $agent_id
				));
			}

			return $this->redirectRoute('admin_twitter_accounts');
		}

		return $this->render('AdminBundle:TwitterAccount:edit.html.twig', array(
			'account' => $account,
			'agents' => $this->em->getRepository('DeskPRO:Person')->getAgents()
		));
	}

	public function deleteAction($account_id, $security_token)
	{
		$this->ensureAuthToken('delete_twitter', $security_token);

		$account = $this->em->find('DeskPRO:TwitterAccount', $account_id);
		if (!$account) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$this->em->remove($account);
		$this->em->flush();

		$this->_accountRemove($account);

		return $this->redirectRoute('admin_twitter_accounts');
	}

	protected function _accountRemove(TwitterAccount $account)
	{
	}
}
