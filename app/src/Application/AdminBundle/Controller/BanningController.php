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
 * Manages ip and email banning
 */
class BanningController extends AbstractController
{
	protected function getCounts()
	{
		$counts = array();
		$counts['emails'] = $this->db->fetchColumn("SELECT COUNT(*) FROM ban_emails");
		$counts['ips'] = $this->db->fetchColumn("SELECT COUNT(*) FROM ban_ips");

		return $counts;
	}

	public function listEmailsAction()
	{
		$banned_emails = $this->em->getRepository('DeskPRO:BanEmail')->getList();

		return $this->render('AdminBundle:Banning:list-emails.html.twig', array(
			'counts' => $this->getCounts(),
			'banned_emails' => $banned_emails
		));
	}

	public function listIpsAction()
	{
		$banned_ips    = $this->em->getRepository('DeskPRO:BanIp')->getList();

		return $this->render('AdminBundle:Banning:list-ips.html.twig', array(
			'counts' => $this->getCounts(),
			'banned_ips'    => $banned_ips,
		));
	}

	public function newIpBanAction()
	{
		$ip_address = $this->in->getString('ip');

		$ipban = new Entity\BanIp();
		$ipban['banned_ip'] = $ip_address;

		$this->em->persist($ipban);
		$this->em->flush();

		return $this->render('AdminBundle:Banning:ip-row.html.twig', array(
			'ip' => $ipban['banned_ip']
		));
	}

	public function newEmailBanAction()
	{
		$email_address = $this->in->getString('email');

		$emailban = new Entity\BanEmail();
		$emailban['banned_email'] = $email_address;

		$this->em->persist($emailban);
		$this->em->flush();

		return $this->render('AdminBundle:Banning:email-row.html.twig', array(
			'email' => $emailban['banned_email']
		));
	}

	public function removeIpBanAction()
	{
		$ip_address = $this->in->getString('ip');

		$ipban = $this->em->getRepository('DeskPRO:BanIp')->find($ip_address);

		if ($ipban) {
			$this->em->remove($ipban);
			$this->em->flush();
		}

		return $this->createJsonResponse(array(
			'success' => true,
			'ip' => $ip_address
		));
	}

	public function removeEmailBanAction()
	{
		$email_address = $this->in->getString('email');

		$emailban = $this->em->getRepository('DeskPRO:BanEmail')->find($email_address);

		if ($emailban) {
			$this->em->remove($emailban);
			$this->em->flush();
		}

		return $this->createJsonResponse(array(
			'success' => true,
			'email' => $email_address
		));
	}
}
