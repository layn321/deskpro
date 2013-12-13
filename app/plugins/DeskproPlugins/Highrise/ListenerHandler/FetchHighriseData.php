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
 */

namespace DeskproPlugins\Highrise\ListenerHandler;

use Application\DeskPRO\App;

class FetchHighriseData
{
	protected $highrise;

	public function __construct()
	{
		$this->highrise = new  \Orb\Service\Highrise\Highrise(
			App::getSetting('dp_highrise.highrise_url'),
			App::getSetting('dp_highrise.api_auth_key')
		);
	}

	public function DeskPRO_onDisplayFieldRenderHtml($event)
	{
		$ticket = $event->ticket;
		$person = $ticket->person;

		$person_resource = \Orb\Service\Highrise\Resource\Person($this->highrise);
		$notes_resource = \Orb\Service\Highrise\Resource\Notes($this->highrise);
		$results = $person_resource->findPeopleWithCriteria(array('email' => $person->getPrimaryEmailAddress()));
		if (!$results OR empty($results[0])) {
			return '';
		}

		$result_person = $results[0];
		$result_notes = $notes_resource->getNotesForPerson($result_person['id']);

		$tpl = App::getTemplating();

		$event->html = $tpl->render('dp_highrise::highrise_results.html.twig', array(
			'highrise_person' => $result_person,
			'highrise_notes' => $result_notes
		));
	}
}