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

use Symfony\Component\HttpFoundation\Response;

use Application\DeskPRO\App;


class TwitterController extends AbstractController
{
	public function viewLongAction($long_id)
	{
		$long = $this->em->find('DeskPRO:TwitterStatusLong', $long_id);
		if (!$long) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		if (\Application\DeskPRO\Service\Twitter::getUserConsumerKey()) {
			if ($this->in->getBool('start')) {
				$api = \Application\DeskPRO\Service\Twitter::getUserTwitterApi();
				$api->setCallback($this->generateUrl('user_long_tweet_view', array('long_id' => $long->id), true));
				return $this->redirect($api->getAuthenticateUrl());
			} else if ($this->in->getString('oauth_token')) {
				$api = \Application\DeskPRO\Service\Twitter::getUserTwitterApi();
				$api->setToken($this->in->getString('oauth_token'));
				$access = $api->getAccessToken();

				$this->session->set('twitter_user_id', $access->user_id);
				$this->session->set('twitter_screen_name', $access->screen_name);
				$this->session->set('twitter_oauth_token', $access->oauth_token);
				$this->session->set('twitter_oauth_token_secret', $access->oauth_token_secret);

				return $this->redirectRoute('user_long_tweet_view', array('long_id' => $long->id));
			}
		}

		// todo: more than just session checking - may be stored with a user
		if ($long->is_public) {
			$can_view = true;
		} else {
			App::setUncachableResult();

			$can_view = (
				App::getSession()->get('twitter_user_id')
				&& App::getSession()->get('twitter_user_id') == $long->for_user->id
			);
		}

		$is_user = (
			$long->for_user
			&& App::getSession()->get('twitter_user_id')
			&& App::getSession()->get('twitter_user_id') == $long->for_user->id
		);
		if (!$long->is_read && $is_user) {
			$long->is_read = true;
			$long->date_read = new \DateTime();
			$this->em->persist($long);
			$this->em->flush();
		}

		return $this->render('UserBundle:Twitter:view-long.html.twig', array(
			'long' => $long,
			'can_view' => $can_view
		));
	}
}
