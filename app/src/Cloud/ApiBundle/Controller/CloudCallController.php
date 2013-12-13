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

namespace Cloud\ApiBundle\Controller;

use DeskPRO\Kernel\License;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\TmpData;
use Application\DeskPRO\App;

use Application\ApiBundle\Controller\AbstractController;

class CloudCallController extends AbstractController
{
	public function preAction($action, $arguments = null)
	{
		if (!isset($_REQUEST['DPC_CALL_KEY']) || !isset($GLOBALS['DP_CONFIG']['DPC_CALL_KEY']) || $GLOBALS['DP_CONFIG']['DPC_CALL_KEY'] != $_REQUEST['DPC_CALL_KEY']) {
			return $this->createApiErrorResponse('invalid_call_key', 'Invalid call key', 403);
		}
		return null;
	}

	public function pingAction()
	{
		return $this->createJsonResponse(array('time' => time()));
	}

	public function resetPasswordAction($person_id)
	{
		/** @var $person \Application\DeskPRO\Entity\Person */
		$person = $this->em->find('DeskPRO:Person', $person_id);

		if (!$person) {
			throw $this->createNotFoundException();
		}

		$email = $person->getPrimaryEmailAddress();

		#------------------------------
		# Send reset as a link
		#------------------------------

		if ($this->in->getBool('link')) {
			$interface = 'agent';
			if (License::getLicense()->isPastExpireDate() || DPC_BILL_FAILED) {
				$interface = 'billing';
			}

			$code_data = TmpData::create('reset-password', array('person_id' => $person['id'], 'interface' => $interface), '+3 days');
			$this->em->persist($code_data);
			$this->em->flush();

			$vars = array(
				'code'      => $code_data->getCode(),
				'person'    => $person,
				'email'     => $email,
				'interface' => $interface
			);

			$message = $this->container->getMailer()->createMessage();
			$message->setTemplate('DeskPRO:emails_user:reset-password.html.twig', $vars);
			$message->setTo($email, $person->getDisplayName());

			$this->container->getMailer()->send($message);

			return $this->createJsonResponse(array('sent_reset_link' => $email));

		#------------------------------
		# Reset password
		#------------------------------

		} else {
			$new_pass = $this->in->getString('password');

			if (!$new_pass) {
				return $this->createJsonResponse(array('error' => 'no_pass'));
			}

			$person->setPassword($new_pass);
			return $this->createJsonResponse(array('reset_password' => $email));
		}
	}
}