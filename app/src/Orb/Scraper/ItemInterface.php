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

namespace Orb\Scraper;

interface ItemInterface
{
	/**
	 * Gets the unique ID that identifies this remote item.
	 * The ID should NOT change. This means that integer ID's are good,
	 * but usernames are bad because most systems allow you to change usernames.
	 * 
	 * @return mixed
	 */
	public function getIdentity();


	/**
	 * This is a human-friendly ID. So the above is a computer friendly ID, such
	 * as a userid, and this is one that humans prefer, like a username.
	 *
	 * @return mixed
	 */
	public function getFriendlyIdentity();


	/**
	 * The actual data for the item. This must always be a plain PHP array. If
	 * the data returned from the resource is a single piece of information, by convention
	 * the array should contain a single key 'body',
	 */
	public function getData();
}
