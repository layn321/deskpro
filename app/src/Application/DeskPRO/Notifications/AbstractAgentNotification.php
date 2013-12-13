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
 * @subpackage
 */

namespace Application\DeskPRO\Notifications;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;
use Application\DeskPRO\Entity\ClientMessage;

abstract class AbstractAgentNotification
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var array
	 */
	protected $notify_list;

	/**
	 * @var string
	 */
	protected $client = 'sys';

	abstract public function shouldSendBrowserNotification(Person $person);
	abstract public function shouldSendEmailNotification(Person $person);
	abstract public function send();

	public function __construct()
	{
		$this->em = App::getOrm();
	}

	public function setCreatedClient($client)
	{
		$this->client = $client;
	}

	/**
	 * Build a list of agents to email and browser notify.
	 *
	 * Returns an array with two sub-arrays of agent_id=>agent:
	 * - (array) 'email': agent_id=>Person
	 * - (array) 'browser': agent_id=>Person
	 *
	 * @return array
	 */
	public function getNotifyList()
	{
		if ($this->notify_list) {
			return $this->notify_list;
		}
		$agents = $this->em->getRepository('DeskPRO:Person')->getAgents();

		$online_ids = $this->em->getRepository('DeskPRO:Session')->getAvailableAgentIds();

		$send_browser = array();
		$send_email   = array();

		foreach ($online_ids as $aid) {
			if (!isset($agents[$aid])) {
				continue;
			}
			$agent = $agents[$aid];
			if ($this->shouldSendBrowserNotification($agent)) {
				$send_browser[$aid] = $agent;
			}
		}

		foreach ($agents as $agent) {
			if ($this->shouldSendEmailNotification($agent)) {
				$send_email[$agent->getId()] = $agent;
			}
		}

		$this->notify_list = array(
			'email' => $send_email,
			'browser' => $send_browser
		);

		return $this->notify_list;
	}


	/**
	 * Send an email notification to agents from the build list
	 *
	 * @param string $tpl
	 * @param array $vars
	 */
	public function sendEmailNotifications($tpl, array $vars)
	{
		$notify_list = $this->getNotifyList();

		if (!$notify_list['email']) {
			return;
		}

		foreach	($notify_list['email'] as $agent) {
			$message = App::getMailer()->createMessage();
			$message->setTemplate($tpl, $vars);
			$message->setToPerson($agent);
			$message->setFrom(App::getSetting('core.default_from_email'), App::getSetting('core.deskpro_name'));
			App::getMailer()->send($message);
		}
	}


	/**
	 * Send a browser notification to agents from the built list.
	 *
	 * $vars should include a 'notify_data' array that'll be data in the client message. This should at least
	 * include a notify_type.
	 *
	 * @param string $tpl The template to display in the browser for the notification
	 * @param array $vars Vars used in the template, and a notify_data to be inserted into the client message data
	 */
	public function sendBrowserNotifications($tpl, array $vars)
	{
		$notify_list = $this->getNotifyList();

		if (!$notify_list['browser']) {
			return;
		}

		foreach	($notify_list['browser'] as $agent) {
			$tpl_line = App::getTemplating()->render($tpl, $vars);

			$data = $vars['notify_data'];
			$data['row'] = $tpl_line;

			$cm = new ClientMessage();
			$cm->fromArray(array(
				'channel' => 'agent-notify.' . $data['notify_type'],
				'data' => $data,
				'for_person'        => $agent,
				'created_by_client' => $this->client
			));
			$this->em->persist($cm);
		}

		$this->em->flush();
	}
}