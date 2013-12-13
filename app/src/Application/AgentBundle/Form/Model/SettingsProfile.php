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

namespace Application\AgentBundle\Form\Model;

use Application\DeskPRO\App;
use Application\DeskPRO\Entity\Person;

class SettingsProfile
{
	public $name;
	public $override_display_name;
	public $email;
	public $timezone = 'UTC';
	public $language_id = 0;
	public $password = '';
	public $password2 = '';
	public $new_picture_blob_id = false;

	public $ticket_close_reply = false;
	public $ticket_close_note = false;
	public $hide_claimed_chat = false;
	public $ticket_go_next_reply = false;
	public $ticket_reverse_order = false;
	public $default_team_id = 0;
	public $reset_api_token = false;

	public $auto_dismiss_notifications = 60;

	public $new_emails;
	public $remove_emails;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(Person $person)
	{
		$this->em = App::getOrm();

		$this->person = $person;

		$this->name = $person->name;
		$this->override_display_name = $person->override_display_name;
		$this->email = $person->getPrimaryEmailAddress();
		$this->timezone = $person->timezone;
		$this->language_id = $person->getLanguage()->getId();

		$this->ticket_close_reply = (bool)$person->getPref('agent.ticket_close_reply', true);
		$this->ticket_close_note = (bool)$person->getPref('agent.ticket_close_note', false);
		$this->ticket_go_next_reply = (bool)$person->getPref('agent.ticket_go_next_reply', false);
		$this->hide_claimed_chat = (bool)$person->getPref('agent.hide_claimed_chat', false);
		$this->default_team_id = $person->getPref('agent.ticket_default_team_id');
		$this->ticket_reverse_order = (bool)$person->getPref('agent.ticket_reverse_order');
		if ($this->default_team_id === null) {
			$teams = $person->getAgent()->getTeams();
			$last_team = end($teams);
			$this->default_team_id = $last_team ? $last_team->id : 0;
		}
		$this->auto_dismiss_notifications = $person->getPref('agent.ui.auto_dismiss_notification', 60);
	}

	public function getPerson()
	{
		return $this->person;
	}

	public function requiresAuth()
	{
		if ($this->password || $this->email != $this->person->getPrimaryEmailAddress()) {
			return true;
		}

		return false;
	}

	public function save()
	{
		$person = $this->person;

		$person->name = $this->name;
		$person->override_display_name = $this->override_display_name;
		$person->timezone = $this->timezone;

		if ($this->new_picture_blob_id) {
			$blob = $this->em->getRepository('DeskPRO:Blob')->getByAuthId($this->new_picture_blob_id);
			if ($blob) {
				$person->picture_blob = $blob;
			}
		}

		$primary_email = $person->getPrimaryEmail();
		if ($primary_email->email != $this->email) {

			$found_email = $person->findEmailAddress($this->email);
			if ($found_email) {
				$new_primary_email = $found_email;
			} else {
				$new_primary_email = new \Application\DeskPRO\Entity\PersonEmail();
				$new_primary_email->email = $this->email;
				$new_primary_email->is_validated = true;
				$person->addEmailAddress($new_primary_email);
				$this->em->persist($new_primary_email);
			}

			$person->primary_email = $new_primary_email;

			$person->removeEmailAddressId($primary_email->id);
			$this->em->remove($primary_email);
		}

		if ($this->password) {
			$person->setPassword($this->password);
		}

		if ($this->language_id) {
			$person->setLanguageId($this->language_id);
		}

		$person->setPreference('agent.ticket_close_reply', $this->ticket_close_reply ? 1 : 0);
		$person->setPreference('agent.ticket_close_note', $this->ticket_close_note ? 1 : 0);
		$person->setPreference('agent.ticket_go_next_reply', $this->ticket_go_next_reply ? 1 : 0);
		$person->setPreference('agent.hide_claimed_chat', $this->hide_claimed_chat ? 1 : 0);
		$person->setPreference('agent.ticket_reverse_order', $this->ticket_reverse_order ? 1 : 0);

		$assign_team_setting = (
			App::getSetting('core_tickets.new_assignteam') == 'assign'
			|| App::getSetting('core_tickets.reply_assignteam_assigned') == 'assign'
			|| App::getSetting('core_tickets.reply_assignteam_unassigned') == 'assign'
		);
		if (count($person->getAgent()->getTeams()) && $assign_team_setting) {
			$person->setPreference('agent.ticket_default_team_id', intval($this->default_team_id));
		}

		$person->setPreference('agent.ui.auto_dismiss_notification', intval($this->auto_dismiss_notifications));

		if ($this->reset_api_token) {
			$token = App::getEntityRepository('DeskPRO:ApiToken')->getTokenForPerson($person);
			if ($token) {
				$token->regenerateToken();
				$this->em->persist($token);
			}
		}

		$this->em->persist($person);

		$this->em->beginTransaction();

		try {
			$this->em->flush();
			$this->em->commit();

		} catch (\Exception $e) {
			$this->em->rollback();
			throw $e;
		}

		// Additional email addresses
		foreach ($this->new_emails as $new_email) {
			if ($person->hasEmailAddress($new_email)) {
				continue;
			}

			$email_address = $person->addEmailAddressString($new_email);
			$this->em->persist($email_address);
			$this->em->flush();
		}

		// Removing email addresses
		foreach ($this->remove_emails as $remove_email_id) {
			$person->removeEmailAddressId($remove_email_id);
			$this->em->flush();
		}
	}
}
