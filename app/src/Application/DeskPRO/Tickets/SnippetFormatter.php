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

namespace Application\DeskPRO\Tickets;

use Application\DeskPRO\People\PersonContextInterface;
use Application\DeskPRO\Twig\Environment as Twig_Environment;
use Application\DeskPRO\Entity\Ticket;
use Application\DeskPRO\Entity\Person;

class SnippetFormatter implements PersonContextInterface
{
	/**
	 * @var \Application\DeskPRO\Twig\Environment
	 */
	protected $twig;

	/**
	 * @var \Application\DeskPRO\Entity\Person
	 */
	protected $person_context;

	public function __construct(Twig_Environment $twig)
	{
		$this->twig = $twig;
	}

	public function setPersonContext(Person $person)
	{
		$this->person_context = $person;
	}

	public function getVars(Ticket $ticket)
	{
		$data = array();
		$data['ticket'] = $ticket->toApiData();

		if (isset($data['ticket']['person'])) {
			$data['user'] = $data['ticket']['person'];
		}

		if (isset($data['ticket']['agent'])) {
			$data['agent'] = $data['ticket']['agent'];
		}

		if (isset($data['ticket']['agent_team'])) {
			$data['agent_team'] = $data['ticket']['agent_team'];
		}

		if ($this->person_context) {
			$data['me'] = $this->person_context->toApiData();
		}

		return $data;
	}

	public function formatSnippet($snippet, Ticket $ticket)
	{
		$data = $this->getVars($ticket);

		try {
			return $this->twig->renderStringTemplate($snippet->snippet, $data);
		} catch (\Exception $e) {
			return $snippet->snippet;
		}
	}

	public function formatText($text, Ticket $ticket)
	{
		$data = $this->getVars($ticket);

		try {
			return $this->twig->renderStringTemplate($text, $data);
		} catch (\Exception $e) {
			return $text;
		}
	}
}