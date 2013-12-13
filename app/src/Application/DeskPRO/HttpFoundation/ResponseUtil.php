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

namespace Application\DeskPRO\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

class ResponseUtil
{
	private function __construct() {}

	/**
	 * Set headers on the response that indicate a response that doesnt expire.
	 *
	 * @param \Symfony\Component\HttpFoundation\Response $response
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public static function setNeverExpireHeaders(Response $response)
	{
		$future_ts = mktime(0, 0, 0, 1, 1, 2020);
		$past_ts   = mktime(0, 0, 0, 1, 1, 2010);
		$future    = new \DateTime('@' . $future_ts);
		$past      = new \DateTime('@' . $past_ts);

		$response->setExpires($future);
		$response->setLastModified($past);
	}
}