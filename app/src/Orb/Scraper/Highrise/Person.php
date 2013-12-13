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
 * Orb
 *
 * @package Orb
 * @subpackage Scraper
 */

namespace Orb\Scraper\Highrise;

use Orb\Scraper\AbstractScraper;

/**
 * Scrapes person data
 */
class Person extends AbstractScraper
{
	/**
	 * @var Orb\Service\Highrise\Highrise
	 */
	protected $highrise;



	/**
	 * @param int $person_id
	 * @return ItemInterface
	 */
	function getData($person_id = null)
	{
		if ($this->highrise === null) {
			$this->highrise = new \Orb\Service\Highrise\Highrise(
				$this->getOption('highrise_url'),
				$this->getOption('highrise_auth_token')
			);
		}

		$data = $this->highrise->person->getPerson($person_id);

		$item = new \Orb\Scraper\Item(
			$person_id,
			trim($data['first-name'] . ' ' . $data['last-name']),
			$data
		);

		return $item;
	}
}
