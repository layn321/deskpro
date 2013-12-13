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
use Application\DeskPRO\Entity;

/**
 * Handles creating/editing of web hooks
 */
class WebHookController extends AbstractController
{
	############################################################################
	# index
	############################################################################

	public function indexAction()
	{
		$hookRepository = $this->_getWebHookRepository();

		return $this->render('AdminBundle:WebHooks:index.html.twig', array(
			'hooks' => $hookRepository->getAllHooks(),
		));
	}



	############################################################################
	# edit
	############################################################################

	public function editAction($webhook_id)
	{
		if ($webhook_id) {
			$hook = $this->_getWebHookOr404($webhook_id);
		} else {
			$hook = new Entity\WebHook();
		}

		$errors = array();

		if ($this->in->getString('process')) {
			$this->ensureRequestToken();

			$title = $this->in->getString('title');
			$url = $this->in->getString('url');

			if (!$title) {
				$errors['title'] = 'Please enter a title.';
			}

			if (!$url || !\Orb\Validator\StringUrl::isValueValid($url)) {
				$errors['url'] = 'Please enter a valid URL.';
			}

			$hook->title = $title;
			$hook->url = $url;
			$hook->username = $this->in->getString('username');
			$hook->password = $this->in->getString('password');

			if (!$errors) {
				$this->em->persist($hook);
				$this->em->flush();

				return $this->redirectRoute('admin_webhooks');
			}
		}

		return $this->render('AdminBundle:WebHooks:edit.html.twig', array(
			'hook' => $hook,
			'errors' => $errors
		));
	}



	############################################################################
	# test
	############################################################################

	public function testAction($webhook_id, $security_token)
	{
		$this->ensureAuthToken('webhook_test', $security_token);

		$hook = $this->_getWebHookOr404($webhook_id);

		$results = $hook->trigger(array('test' => 1));
		$response = $results['response'];
		$exception = $results['exception'];

		if ($response) {
			$success = $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
		} else {
			$success = null;
		}

		return $this->render('AdminBundle:WebHooks:test.html.twig', array(
			'hook' => $hook,
			'response' => $response,
			'exception' => $exception,
			'success' => $success
		));
	}



	############################################################################
	# delete
	############################################################################

	public function deleteAction($webhook_id, $security_token)
	{
		$this->ensureAuthToken('webhook_delete', $security_token);

		$hook = $this->_getWebHookOr404($webhook_id);

		$this->em->remove($hook);
		$this->em->flush();

		return $this->redirectRoute('admin_webhooks');
	}



	############################################################################

	/**
	 * @param integer $id
	 *
	 * @return \Application\DeskPRO\Entity\WebHook
	 */
	protected function _getWebHookOr404($id)
	{
		$data = $this->em->getRepository('DeskPRO:WebHook')->find($id);
		if (!$data) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("There is no web hook with ID $id");
		}

		return $data;
	}

	/**
	 * @return \Application\DeskPRO\EntityRepository\WebHook
	 */
	protected function _getWebHookRepository()
	{
		return $this->em->getRepository('DeskPRO:WebHook');
	}
}
