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
 * @subpackage ApiBundle
 */

namespace Application\ApiBundle\Controller;
use Application\DeskPRO\App;

/**
 * A Test API resource
 */
class TestController extends AbstractController
{
	public function preAction($action, $arguments = null)
	{
		if ($action == 'testAction') {
			return null;
		}

		return parent::preAction($action, $arguments);
	}

	public function aboutAction()
	{
		return $this->createApiResponse(array(
			'about'         => 'This is the DeskPRO API. Refer to the API Documentation for available endpoints.',
			'documentation' => 'https://support.deskpro.com/kb/articles/88-api-basics',
			'libraries'     => array('php' => 'https://support.deskpro.com/kb/articles/97-deskpro-api-wrapper-php')
		));
	}

	/**
	 * This action simply returns a message to indicate that the API is working
	 */
	public function testAction()
	{
		$api_url = App::getSetting('core.deskpro_url');
		$api_url .= 'index.php/';

		// If this call is secure, then we know https works and the client
		// requested it specifically, so return the same protocol
		if ($this->getRequest()->isSecure() && strpos($api_url, 'https://') !== 0 && !defined('DPC_IS_CLOUD')) {
			$api_url = preg_replace('#^http://#', 'https://', $api_url);
		}

		return $this->createApiResponse(array(
			'success'     => true,
			'api_version' => DP_BUILD_TIME,
			'api_url'     => $api_url
		));
	}


	/**
	 * Another test action to indicate the POST API is working
	 */
	public function postTestAction()
	{
		$message = isset($_POST['message']) ? $_POST['message'] : 'Post works!';
		return $this->createApiResponse(array('message' => $message));
	}
}
