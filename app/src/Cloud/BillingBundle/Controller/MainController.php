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

class MainController extends AbstractController
{
    public function indexAction()
    {
		// We just insert this marker token here and then redirect the user off to the deskpro members area site
		$tmpdata = new \Application\DeskPRO\Entity\TmpData();
		$tmpdata->setType('dpc_billing_access');
		$tmpdata->setData('person_info', array(
			'helpdesk_url'     => rtrim($this->container->getSetting('core.deskpro_url'), '/'),
			'asset_url'        => str_replace('/index.php', '', rtrim($this->container->getSetting('core.deskpro_url'), '/')),
			'person_id'        => $this->person->getId(),
			'first_name'       => $this->person->first_name,
			'last_name'        => $this->person->last_name,
			'name'             => $this->person->getDisplayName(),
			'email'            => $this->person->getPrimaryEmailAddress(),
			'picture_url_24'   => $this->person->getPictureUrl(24),
			'can_admin'        => $this->person->can_admin,
			'can_agent'        => $this->person->can_agent,
			'can_billing'      => $this->person->can_billing,
			'can_reports'      => $this->person->can_reports,
			'can_portal'       => $this->container->getSetting('user.portal_enabled'),
		));
		$tmpdata->date_expire = new \DateTime('+1 hour');

		$this->em->persist($tmpdata);
		$this->em->flush();

		return $this->redirect(DP_MA_SERVER . '/cloud/start/'.DPC_SITE_ID.'/'. $tmpdata->getCode());
    }

	public function cancelAction($authcode)
	{
		// We just insert this marker token here and then redirect the user off to the deskpro members area site
		$tmpdata = new \Application\DeskPRO\Entity\TmpData();
		$tmpdata->setType('dpc_billing_access');
		$tmpdata->setData('person_info', array(
			'helpdesk_url'     => rtrim($this->container->getSetting('core.deskpro_url'), '/'),
			'asset_url'        => str_replace('/index.php', '', rtrim($this->container->getSetting('core.deskpro_url'), '/')),
			'person_id'        => $this->person->getId(),
			'first_name'       => $this->person->first_name,
			'last_name'        => $this->person->last_name,
			'name'             => $this->person->getDisplayName(),
			'email'            => $this->person->getPrimaryEmailAddress(),
			'picture_url_24'   => $this->person->getPictureUrl(24),
			'can_admin'        => $this->person->can_admin,
			'can_agent'        => $this->person->can_agent,
			'can_billing'      => $this->person->can_billing,
			'can_reports'      => $this->person->can_reports,
			'can_portal'       => $this->container->getSetting('user.portal_enabled'),
			'to_cancel'        => $authcode,
		));
		$tmpdata->date_expire = new \DateTime('+1 hour');

		$this->em->persist($tmpdata);
		$this->em->flush();

		return $this->redirect(DP_MA_SERVER . '/cloud/start/'.DPC_SITE_ID.'/'. $tmpdata->getCode());
	}
}
