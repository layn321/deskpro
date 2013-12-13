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
 * @subpackage BillingBundle
 */
namespace Cloud\BillingBundle\Controller;

use Application\DeskPRO\App;

class SendFeedbackController extends AbstractController
{
	public function preAction($action, $arguments = null)
	{

	}

    public function sendAction()
    {
		if ($this->person->getId()) {
			$admin = $this->person->getId();
		} else {
			$admin = $this->em->createQuery("
				SELECT p
				FROM DeskPRO:Person p
				WHERE p.is_agent = 1 AND p.is_deleted = 0 AND p.can_admin = 1
				ORDER BY p.can_billing DESC
			")->setMaxResults(1)->getOneOrNullResult();
		}

		if ($admin) {
			$from = $admin->getPrimaryEmailAddress();
			$name = preg_replace("#[^a-zA-Z_\-0-9 \.]#", '', $admin->getDisplayName());
		} else {
			$from = 'website-feedback@deskpro.com';
			$name = 'DeskPRO Demo';
		}

		$subject = 'Expired Demo Contact (' . $this->container->getSetting('core.deskpro_url') . ')';
		$body    = $this->in->getString('message');

		if (!$body) {
			return $this->createJsonResponse(array('error' => 'no_message'));
		}

		@mail(
			'sales@deskpro.com',
			$subject,
			$body,
			"From: $name <$from>\r\n"
		);

		return $this->createJsonResponse(array('success' => true));
    }
}
